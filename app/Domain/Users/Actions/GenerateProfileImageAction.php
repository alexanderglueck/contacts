<?php

namespace App\Domain\Users\Actions;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\Typography\FontFactory;

class GenerateProfileImageAction
{
    public function execute(User $user): void
    {
        if ($user->image) {
            if (file_exists(storage_path('app/public/') . $user->image)) {
                unlink(storage_path('app/public/') . $user->image);
            }
        }

        $file = 'profile_images/' . Str::random(40) . '.jpeg';

        File::exists(storage_path('app/public/profile_images/')) or File::makeDirectory(storage_path('app/public/profile_images/'));

        // Initials avatar: solid background with the user's initials centered.
        // Replaces the former lasserafn/php-initial-avatar-generator package,
        // built directly on intervention/image (v4) instead.
        Image::createImage(50, 50)
            ->fill('#3867d6')
            ->text($this->initials($user->name), 25, 25, function (FontFactory $font) {
                $font->filename(resource_path('fonts/OpenSans-Semibold.ttf'));
                $font->size(24);
                $font->color('#EEEEEE');
                $font->align('center', 'center');
            })
            ->save(storage_path('app/public/') . $file);

        $user->image = $file;
        $user->save();
    }

    /**
     * Derive up to two uppercase initials from a name: the first letter of the
     * first and last words (or just the first letter for a single-word name).
     */
    private function initials(string $name): string
    {
        $words = preg_split('/\s+/', trim($name), -1, PREG_SPLIT_NO_EMPTY) ?: [];

        if ($words === []) {
            return '';
        }

        $first = Str::upper(Str::substr($words[0], 0, 1));

        if (count($words) === 1) {
            return $first;
        }

        $last = Str::upper(Str::substr($words[count($words) - 1], 0, 1));

        return $first . $last;
    }
}
