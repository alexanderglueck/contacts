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
 * than the avatar — upscaling would fabricate detail nobody uploaded.
 *
 * Callers supply either or both files:
 *
 *   $source only          the Android app: one photo, centre-cropped for the
 *                         avatar and used for both larger renditions.
 *   $source + $avatar     a new web upload: cropper.js sends the square the
 *                         user framed plus the untouched original, so the
 *                         avatar keeps their framing and the renditions keep
 *                         the full frame.
 *   $avatar only          re-cropping the photo already on file. The larger
 *                         renditions are LEFT ALONE — they are renditions of
 *                         the same photo, and regenerating them from a 400px
 *                         avatar would quietly destroy the high-res copy of a
 *                         contact whose photo came from the app.
 */
class StoreContactImageAction
{
    public const THUMB_SIZE = 400;

    public const MEDIUM_MAX_EDGE = 1600;

    public const FULL_MAX_EDGE = 4096;

    public function execute(Contact $contact, ?UploadedFile $source = null, ?UploadedFile $avatar = null): void
    {
        if (! $source && ! $avatar) {
            throw ValidationException::withMessages([
                'file' => __('The image could not be processed. Please try a different file.'),
            ]);
        }

        $disk = Storage::disk('public');
        $basename = (string) Str::ulid();
        $supersededPaths = [];

        [$thumb, $medium, $full] = $this->encode($source, $avatar);

        $supersededPaths[] = $contact->image;
        $contact->image = $this->put($disk, "contact_images/{$basename}.jpg", $thumb);

        // Only touched when a new source arrived. Without one there is nothing
        // better to build them from than what is already stored.
        if ($source) {
            $supersededPaths[] = $contact->image_medium;
            $supersededPaths[] = $contact->image_full;

            $contact->image_medium = $medium === null
                ? null
                : $this->put($disk, "contact_images/{$basename}_medium.jpg", $medium);
            $contact->image_full = $full === null
                ? null
                : $this->put($disk, "contact_images/{$basename}_full.jpg", $full);
        }

        $contact->save();

        // Only after the replacement is safely committed, so a failure above
        // can't leave the contact pointing at files that no longer exist.
        foreach (array_filter($supersededPaths) as $path) {
            if ($disk->exists($path)) {
                $disk->delete($path);
            }
        }
    }

    /**
     * @return array{0: string, 1: string|null, 2: string|null} thumb, medium, full
     */
    private function encode(?UploadedFile $source, ?UploadedFile $avatar): array
    {
        // Imagick: the only driver that decodes HEIC/HEIF/AVIF, and it handles
        // jpeg/png/webp uniformly so there's no branching on mime. A per-call
        // manager keeps this isolated; the rest of the app keeps the GD default.
        //
        // autoOrientation rotates pixels to match any EXIF Orientation tag on
        // decode; strip removes metadata on encode so viewers don't rotate a
        // second time off a stale tag.
        try {
            $manager = new ImageManager(new Driver(), autoOrientation: true, strip: true);

            $full = null;
            $medium = null;
            $thumb = null;

            if ($source) {
                $image = $manager->decode($source->getRealPath());

                if (max($image->width(), $image->height()) > self::THUMB_SIZE) {
                    // Derived largest-first from the one decode, each step
                    // shrinking the same instance: avoids decoding repeatedly
                    // and keeps peak memory at roughly one bitmap.
                    $full = (string) $image
                        ->scaleDown(self::FULL_MAX_EDGE, self::FULL_MAX_EDGE)
                        ->encode(new JpegEncoder(quality: 90));

                    $medium = (string) $image
                        ->scaleDown(self::MEDIUM_MAX_EDGE, self::MEDIUM_MAX_EDGE)
                        ->encode(new JpegEncoder(quality: 85));
                }

                // Reuse the same (now smaller) instance when no pre-cropped
                // avatar was sent, rather than decoding the source twice.
                if (! $avatar) {
                    $thumb = (string) $image
                        ->cover(self::THUMB_SIZE, self::THUMB_SIZE)
                        ->encode(new JpegEncoder(quality: 85));
                }
            }

            if ($avatar) {
                // Already the square the user framed; cover() is a safety net
                // against a client sending something off-size.
                $thumb = (string) $manager->decode($avatar->getRealPath())
                    ->cover(self::THUMB_SIZE, self::THUMB_SIZE)
                    ->encode(new JpegEncoder(quality: 85));
            }
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
