<?php

namespace Tests\Feature\Api\V1;

use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * /api/v1/contacts/{contact}/image — upload + delete the profile image.
 * Uses Storage::fake('public') so the test doesn't actually write to
 * storage/app/public, and the assertions hit the in-memory disk.
 */
class ContactImageTest extends TestCase
{
    use RefreshDatabase;

    private function aContact(): Contact
    {
        $user = $this->createUser();
        Sanctum::actingAs($user);

        return create(Contact::class, [
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);
    }

    #[Test]
    public function it_uploads_a_jpeg_and_returns_the_storage_path_on_the_contact()
    {
        Storage::fake('public');

        $contact = $this->aContact();
        $file = UploadedFile::fake()->image('avatar.jpg', 800, 800);

        $response = $this->postJson(route('api.v1.contacts.image.upload', $contact->ulid), [
            'file' => $file,
        ]);

        $response->assertOk();
        $contact->refresh();
        $this->assertNotNull($contact->image);
        Storage::disk('public')->assertExists($contact->image);
    }

    #[Test]
    public function it_rejects_non_image_uploads()
    {
        Storage::fake('public');

        $contact = $this->aContact();
        $file = UploadedFile::fake()->create('not-an-image.pdf', 100, 'application/pdf');

        $this->postJson(route('api.v1.contacts.image.upload', $contact->ulid), [
            'file' => $file,
        ])->assertStatus(422)
          ->assertJsonValidationErrors('file');
    }

    #[Test]
    public function it_rejects_files_above_the_8mb_cap()
    {
        Storage::fake('public');

        $contact = $this->aContact();
        // UploadedFile::fake()->create accepts a size in kilobytes.
        $oversized = UploadedFile::fake()->create('huge.png', 9000, 'image/png');

        $this->postJson(route('api.v1.contacts.image.upload', $contact->ulid), [
            'file' => $oversized,
        ])->assertStatus(422)
          ->assertJsonValidationErrors('file');
    }

    #[Test]
    public function uploading_a_replacement_deletes_the_previous_file()
    {
        Storage::fake('public');

        $contact = $this->aContact();

        $this->postJson(route('api.v1.contacts.image.upload', $contact->ulid), [
            'file' => UploadedFile::fake()->image('first.jpg'),
        ])->assertOk();

        $firstPath = $contact->fresh()->image;

        $this->postJson(route('api.v1.contacts.image.upload', $contact->ulid), [
            'file' => UploadedFile::fake()->image('second.jpg'),
        ])->assertOk();

        $secondPath = $contact->fresh()->image;

        $this->assertNotSame($firstPath, $secondPath);
        Storage::disk('public')->assertMissing($firstPath);
        Storage::disk('public')->assertExists($secondPath);
    }

    #[Test]
    public function it_destroys_the_stored_image_and_nulls_the_column()
    {
        Storage::fake('public');

        $contact = $this->aContact();
        $this->postJson(route('api.v1.contacts.image.upload', $contact->ulid), [
            'file' => UploadedFile::fake()->image('x.jpg'),
        ])->assertOk();

        $path = $contact->fresh()->image;
        Storage::disk('public')->assertExists($path);

        $this->deleteJson(route('api.v1.contacts.image.destroy', $contact->ulid))
            ->assertNoContent();

        $this->assertNull($contact->fresh()->image);
        Storage::disk('public')->assertMissing($path);
    }

    #[Test]
    public function destroying_with_no_image_set_is_still_a_204()
    {
        $contact = $this->aContact();

        $this->deleteJson(route('api.v1.contacts.image.destroy', $contact->ulid))
            ->assertNoContent();

        $this->assertNull($contact->fresh()->image);
    }
}
