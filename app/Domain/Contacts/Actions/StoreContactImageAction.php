<?php

namespace App\Domain\Contacts\Actions;

use App\Models\Contact;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\ImageManager;

/**
 * Turns one uploaded photo into the renditions a contact needs, replacing
 * whatever was stored before.
 *
 * Shared by the API and the web controller so both paths validate, decode and
 * store identically — before this they differed in driver, accepted formats
 * and size cap.
 *
 * Three renditions, all JPEG regardless of input so browsers and the Android
 * dialer can render them without extra decoding:
 *
 *   image        400x400 square, cropped. The avatar. Meaning unchanged.
 *   image_medium longest edge 1600, uncropped. For an in-app/web viewer, so
 *                tapping an avatar doesn't pull several MB over mobile data.
 *   image_full   longest edge 4096, uncropped, higher quality. The download.
 *
 * The two larger ones are only produced when the source is actually bigger
 * than the avatar. A web upload arrives already cropped to 400x400 by
 * cropper.js, and upscaling that would fabricate detail that was never
 * uploaded — so those uploads legitimately end up with null renditions and
 * clients fall back to the avatar. If the web flow ever starts sending the
 * uncropped source, it gets the full set with no change here.
 */
class StoreContactImageAction
{
    public const THUMB_SIZE = 400;

    public const MEDIUM_MAX_EDGE = 1600;

    public const FULL_MAX_EDGE = 4096;

    public function execute(Contact $contact, UploadedFile $file): void
    {
        $previous = $contact->storedImagePaths();

        [$thumb, $medium, $full] = $this->encodeRenditions($file);

        $disk = Storage::disk('public');
        $basename = (string) Str::ulid();

        $contact->image = $this->put($disk, "contact_images/{$basename}.jpg", $thumb);
        $contact->image_medium = $medium === null
            ? null
            : $this->put($disk, "contact_images/{$basename}_medium.jpg", $medium);
        $contact->image_full = $full === null
            ? null
            : $this->put($disk, "contact_images/{$basename}_full.jpg", $full);

        $contact->save();

        // Only after the replacement is safely committed, so a failure above
        // can't leave the contact pointing at files that no longer exist.
        foreach ($previous as $path) {
            if ($disk->exists($path)) {
                $disk->delete($path);
            }
        }
    }

    /**
     * @return array{0: string, 1: string|null, 2: string|null} thumb, medium, full
     */
    private function encodeRenditions(UploadedFile $file): array
    {
        // Imagick: the only driver that decodes HEIC/HEIF/AVIF, and it handles
        // jpeg/png/webp uniformly so there's no branching on mime. A per-call
        // manager keeps this isolated; the rest of the app keeps the GD default.
        //
        // autoOrientation rotates pixels to match any EXIF Orientation tag on
        // decode; strip removes metadata on encode so viewers don't rotate a
        // second time off a stale tag.
        try {
            $image = (new ImageManager(new Driver(), autoOrientation: true, strip: true))
                ->decode($file->getRealPath());

            $sourceEdge = max($image->width(), $image->height());
            $hasDetailToKeep = $sourceEdge > self::THUMB_SIZE;

            // Derived largest-first from the one decode, each step shrinking the
            // same instance: avoids decoding three times, and keeps peak memory
            // at roughly one bitmap rather than three.
            $full = null;
            $medium = null;

            if ($hasDetailToKeep) {
                $full = (string) $image
                    ->scaleDown(self::FULL_MAX_EDGE, self::FULL_MAX_EDGE)
                    ->encode(new JpegEncoder(quality: 90));

                $medium = (string) $image
                    ->scaleDown(self::MEDIUM_MAX_EDGE, self::MEDIUM_MAX_EDGE)
                    ->encode(new JpegEncoder(quality: 85));
            }

            $thumb = (string) $image
                ->cover(self::THUMB_SIZE, self::THUMB_SIZE)
                ->encode(new JpegEncoder(quality: 85));
        } catch (\Throwable $e) {
            // Corrupt file, or a variant this Imagick build can't decode.
            // Surface as a validation error rather than a 500 so clients can
            // show something meaningful.
            throw ValidationException::withMessages([
                'file' => __('The image could not be processed. Please try a different file.'),
            ]);
        }

        return [$thumb, $medium, $full];
    }

    private function put(\Illuminate\Contracts\Filesystem\Filesystem $disk, string $path, string $contents): string
    {
        // visibility public → 0755 dirs, so nginx can serve the file through
        // the public/storage mount.
        $disk->put($path, $contents, 'public');

        return $path;
    }
}
