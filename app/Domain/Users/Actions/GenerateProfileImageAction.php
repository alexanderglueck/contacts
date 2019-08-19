<?php

namespace App\Domain\Users\Actions;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use LasseRafn\InitialAvatarGenerator\InitialAvatar;

class GenerateProfileImageAction
{
    public function execute(User $user)
    {
        if ($user->image) {
            if (file_exists(storage_path('app/public/') . $user->image)) {
                unlink(storage_path('app/public/') . $user->image);
            }
        }

        $file = 'profile_images/' . Str::random(40) . '.jpeg';

        $avatar = new InitialAvatar();

        File::exists(storage_path('app/public/profile_images/')) or File::makeDirectory(storage_path('app/public/profile_images/'));

        $avatar->name($user->name)
            ->length(2)
            ->fontSize(0.5)
            ->size(50)
            ->background('#3867d6')
            ->color('#EEE')
            ->generate()
            ->save(storage_path('app/public/') . $file);

        $user->image = $file;
        $user->save();
    }
}
