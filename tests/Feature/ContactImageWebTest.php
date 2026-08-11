<?php

namespace Tests\Feature;

use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * The web image editor: cropper.js sends the 400x400 square the user framed as
 * `file`, plus the untouched original as `source` when there is a new one.
 */
class ContactImageWebTest extends TestCase
{
    use RefreshDatabase;

    private function aContact(): Contact
    {
        $user = $this->createUser('edit contacts');

        return create(Contact::class, [
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);
    }

    #[Test]
    public function uploading_a_crop_with_its_original_produces_every_rendition()
    {
        Storage::fake('public');

        $contact = $this->aContact();

        $this->put(route('contacts.update_image', $contact->ulid), [
            'file' => UploadedFile::fake()->image('crop.png', 400, 400),
            'source' => UploadedFile::fake()->image('original.jpg', 2000, 1000),
        ])->assertRedirect(route('contacts.show', $contact->ulid));

        $contact->refresh();

        $this->assertNotNull($contact->image);
        $this->assertNotNull($contact->image_medium);
        $this->assertNotNull($contact->image_full);

        // Avatar comes from the crop, the renditions from the original — so
        // they keep the source's aspect ratio rather than being square.
        $medium = getimagesizefromstring(Storage::disk('public')->get($contact->image_medium));
        $this->assertSame([1600, 800], [$medium[0], $medium[1]]);
    }

    #[Test]
    public function re_cropping_the_stored_photo_keeps_the_existing_high_res_renditions()
    {
        Storage::fake('public');

        $contact = $this->aContact();

        // A photo that arrived from the app: one file, all three renditions.
        $this->put(route('contacts.update_image', $contact->ulid), [
            'file' => UploadedFile::fake()->image('crop.png', 400, 400),
            'source' => UploadedFile::fake()->image('original.jpg', 2000, 1000),
        ])->assertRedirect();

        $contact->refresh();
        $originalAvatar = $contact->image;
        $originalMedium = $contact->image_medium;
        $originalFull = $contact->image_full;

        // Now re-frame the avatar with no new original — the web "crop current"
        // action. Regenerating renditions from a 400px avatar would destroy the
        // high-res copy, so they must survive untouched.
        $this->put(route('contacts.update_image', $contact->ulid), [
            'file' => UploadedFile::fake()->image('recrop.png', 400, 400),
        ])->assertRedirect();

        $contact->refresh();

        $this->assertNotSame($originalAvatar, $contact->image, 'the avatar should have been replaced');
        $this->assertSame($originalMedium, $contact->image_medium);
        $this->assertSame($originalFull, $contact->image_full);

        Storage::disk('public')->assertMissing($originalAvatar);
        Storage::disk('public')->assertExists($contact->image_medium);
        Storage::disk('public')->assertExists($contact->image_full);
    }

    #[Test]
    public function the_web_upload_enforces_the_same_8mb_cap_as_the_api()
    {
        Storage::fake('public');

        $contact = $this->aContact();

        $this->put(route('contacts.update_image', $contact->ulid), [
            'file' => UploadedFile::fake()->image('crop.png', 400, 400),
            'source' => UploadedFile::fake()->create('huge.jpg', 9000, 'image/jpeg'),
        ])->assertSessionHasErrors('source');
    }
}
