<?php

namespace App\Http\Controllers\Account;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Inertia\Response;
use App\Domain\Users\Actions\GenerateProfileImageAction;
use App\Http\Requests\Account\ProfileImageUpdateRequest;

class ProfileImageController extends Controller
{
    public function show(Request $request): Response
    {
        $user = $request->user();

        return Inertia::render('UserSettings/Image', [
            'user' => [
                'name' => $user->name,
                'image' => $user->image,
                'has_image' => $user->hasImage(),
            ],
        ]);
    }

    public function update(ProfileImageUpdateRequest $request): RedirectResponse
    {
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            // Route through the 'public' disk (visibility: public → 0755 dirs
            // so nginx can serve the file via the public/storage symlink).
            $file = $request->file('image')->storePublicly('profile_images', 'public');

            // Cap server-side to 400x400 even if the client uploaded larger.
            // The Vue cropper already produces 400x400 PNG, this is a safety net.
            // orientate() applies the EXIF Orientation tag to the pixels so
            // phone-camera shots don't end up sideways after EXIF is stripped.
            Image::make(storage_path('app/public/') . $file)
                ->orientate()
                ->fit(400, 400)
                ->save();

            if ($request->user()->image) {
                if (file_exists(storage_path('app/public/') . $request->user()->image)) {
                    unlink(storage_path('app/public/') . $request->user()->image);
                }
            }

            $request->user()->image = str_replace('public/', '', $file);

            if ($request->user()->save()) {
                Session::flash('alert-success', trans('flash_message.user_setting.updated'));

                return redirect()->route('user_settings.image.show');
            }
        }

        Session::flash('alert-danger', trans('flash_message.user_setting.not_updated'));

        return redirect()->route('user_settings.image.show');
    }

    public function destroy(Request $request, GenerateProfileImageAction $generateProfileImageAction): RedirectResponse
    {
        $generateProfileImageAction->execute($request->user());

        flashSuccess(trans('flash_message.user_setting.updated'));

        return redirect()->route('user_settings.image.show');
    }
}
