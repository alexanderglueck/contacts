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
    public function it_accepts_a_webp_upload_and_stores_it_as_jpg()
    {
        Storage::fake('public');

        $contact = $this->aContact();

        // Imagick can synthesize a real WebP without a fixture.
        $im = new \Imagick();
        $im->newImage(800, 800, new \ImagickPixel('blue'));
        $im->setImageFormat('webp');
        $tmp = tempnam(sys_get_temp_dir(), 'webp_').'.webp';
        $im->writeImage($tmp);
        $im->clear();

        $upload = new UploadedFile($tmp, 'avatar.webp', 'image/webp', null, true);

        $response = $this->postJson(route('api.v1.contacts.image.upload', $contact->ulid), [
            'file' => $upload,
        ]);

        $response->assertOk();
        $contact->refresh();
        // Output is always normalised to .jpg, regardless of input format.
        $this->assertStringEndsWith('.jpg', $contact->image);
        Storage::disk('public')->assertExists($contact->image);
    }

    #[Test]
    public function it_accepts_an_avif_upload_and_stores_it_as_jpg()
    {
        // AVIF stands in for the HEIF family in tests: same Imagick +
        // libheif decode path as HEIC, but unlike HEIC the libheif
        // encoder is bundled, so we can synthesise a fixture here.
        // HEIC at runtime follows the identical decode-then-encode-to-
        // JPEG path; the heic-specific test below covers it when an
        // encoder is available.
        Storage::fake('public');

        $contact = $this->aContact();

        $im = new \Imagick();
        $im->newImage(800, 800, new \ImagickPixel('green'));
        $im->setImageFormat('avif');
        $tmp = tempnam(sys_get_temp_dir(), 'avif_').'.avif';
        $im->writeImage($tmp);
        $im->clear();

        $upload = new UploadedFile($tmp, 'avatar.avif', 'image/avif', null, true);

        $response = $this->postJson(route('api.v1.contacts.image.upload', $contact->ulid), [
            'file' => $upload,
        ]);

        $response->assertOk();
        $contact->refresh();
        $this->assertStringEndsWith('.jpg', $contact->image);
        Storage::disk('public')->assertExists($contact->image);
    }

    #[Test]
    public function it_accepts_a_heic_upload_when_imagick_can_encode_one_for_the_fixture()
    {
        // Most Imagick + libheif builds ship decode-only (the HEIC
        // encoder is x265-based and excluded for licensing reasons).
        // In that situation we can't generate a fixture, but the
        // runtime decode path is exercised by the AVIF test above.
        $canEncode = true;
        try {
            $probe = new \Imagick();
            $probe->newImage(2, 2, new \ImagickPixel('black'));
            $probe->setImageFormat('heic');
            $probe->getImageBlob();
            $probe->clear();
        } catch (\Throwable) {
            $canEncode = false;
        }

        if (! $canEncode) {
            $this->markTestSkipped('Imagick build lacks a HEIC encoder — fixture cannot be synthesised here. Runtime HEIC decode covered by AVIF test (same code path).');
        }

        Storage::fake('public');

        $contact = $this->aContact();

        $im = new \Imagick();
        $im->newImage(800, 800, new \ImagickPixel('red'));
        $im->setImageFormat('heic');
        $tmp = tempnam(sys_get_temp_dir(), 'heic_').'.heic';
        $im->writeImage($tmp);
        $im->clear();

        $upload = new UploadedFile($tmp, 'avatar.heic', 'image/heic', null, true);

        $this->postJson(route('api.v1.contacts.image.upload', $contact->ulid), [
            'file' => $upload,
        ])->assertOk();

        $contact->refresh();
        $this->assertStringEndsWith('.jpg', $contact->image);
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

    /**
     * @return array{0: int, 1: int} width, height of a stored rendition
     */
    private function dimensionsOf(string $path): array
    {
        $info = getimagesizefromstring(Storage::disk('public')->get($path));

        return [$info[0], $info[1]];
    }

    #[Test]
    public function a_large_upload_produces_an_avatar_a_medium_and_a_full_rendition()
    {
        Storage::fake('public');

        $contact = $this->aContact();

        // Deliberately non-square so the aspect-preserving renditions are
        // distinguishable from the square avatar crop.
        $this->postJson(route('api.v1.contacts.image.upload', $contact->ulid), [
            'file' => UploadedFile::fake()->image('big.jpg', 2000, 1000),
        ])->assertOk();

        $contact->refresh();

        $this->assertNotNull($contact->image_medium);
        $this->assertNotNull($contact->image_full);

        Storage::disk('public')->assertExists($contact->image);
        Storage::disk('public')->assertExists($contact->image_medium);
        Storage::disk('public')->assertExists($contact->image_full);

        // Avatar is a square crop; the others keep the 2:1 aspect ratio.
        $this->assertSame([400, 400], $this->dimensionsOf($contact->image));
        $this->assertSame([1600, 800], $this->dimensionsOf($contact->image_medium));
        // Below the 4096 cap, so the full rendition keeps its source size.
        $this->assertSame([2000, 1000], $this->dimensionsOf($contact->image_full));
    }

    #[Test]
    public function a_source_no_bigger_than_the_avatar_produces_no_extra_renditions()
    {
        Storage::fake('public');

        $contact = $this->aContact();

        // What the web cropper uploads. Upscaling this would invent detail
        // that was never uploaded, so the larger renditions stay null and
        // clients fall back to the avatar.
        $this->postJson(route('api.v1.contacts.image.upload', $contact->ulid), [
            'file' => UploadedFile::fake()->image('cropped.jpg', 400, 400),
        ])->assertOk();

        $contact->refresh();

        $this->assertNotNull($contact->image);
        $this->assertNull($contact->image_medium);
        $this->assertNull($contact->image_full);
    }

    #[Test]
    public function a_replacement_deletes_every_previous_rendition()
    {
        Storage::fake('public');

        $contact = $this->aContact();

        $this->postJson(route('api.v1.contacts.image.upload', $contact->ulid), [
            'file' => UploadedFile::fake()->image('first.jpg', 2000, 1000),
        ])->assertOk();

        $old = $contact->fresh()->storedImagePaths();
        $this->assertCount(3, $old);

        $this->postJson(route('api.v1.contacts.image.upload', $contact->ulid), [
            'file' => UploadedFile::fake()->image('second.jpg', 2000, 1000),
        ])->assertOk();

        foreach ($old as $path) {
            Storage::disk('public')->assertMissing($path);
        }

        foreach ($contact->fresh()->storedImagePaths() as $path) {
            Storage::disk('public')->assertExists($path);
        }
    }

    #[Test]
    public function destroying_the_image_removes_every_rendition_and_nulls_all_columns()
    {
        Storage::fake('public');

        $contact = $this->aContact();

        $this->postJson(route('api.v1.contacts.image.upload', $contact->ulid), [
            'file' => UploadedFile::fake()->image('x.jpg', 2000, 1000),
        ])->assertOk();

        $paths = $contact->fresh()->storedImagePaths();
        $this->assertCount(3, $paths);

        $this->deleteJson(route('api.v1.contacts.image.destroy', $contact->ulid))
            ->assertNoContent();

        $contact->refresh();
        $this->assertNull($contact->image);
        $this->assertNull($contact->image_medium);
        $this->assertNull($contact->image_full);

        foreach ($paths as $path) {
            Storage::disk('public')->assertMissing($path);
        }
    }

    #[Test]
    public function the_contact_payload_exposes_a_url_for_every_rendition()
    {
        Storage::fake('public');

        $contact = $this->aContact();

        $response = $this->postJson(route('api.v1.contacts.image.upload', $contact->ulid), [
            'file' => UploadedFile::fake()->image('big.jpg', 2000, 1000),
        ])->assertOk();

        $contact->refresh();

        $response
            ->assertJsonPath('data.image_url', url('storage/'.$contact->image))
            ->assertJsonPath('data.image_medium_url', url('storage/'.$contact->image_medium))
            ->assertJsonPath('data.image_full_url', url('storage/'.$contact->image_full));
    }

    #[Test]
    public function the_rendition_urls_are_null_when_a_contact_has_no_photo()
    {
        $contact = $this->aContact();

        $this->getJson(route('api.v1.contacts.show', $contact->ulid))
            ->assertOk()
            ->assertJsonPath('data.image_url', null)
            ->assertJsonPath('data.image_medium_url', null)
            ->assertJsonPath('data.image_full_url', null);
    }
}
