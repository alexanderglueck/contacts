<?php

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('contact', function ($user) {
    return Auth::check();
});

Broadcast::channel('p.contact.{tenantId}.{contactId}', function ($user, $tenantId, $contactId) {
    if (\App\Models\Team::findOrNew($tenantId)->users()->where('users.id', $user->id)->exists() &&
        \App\Models\Team::findOrNew($tenantId)->contacts()->where('contacts.id', $contactId)->exists()) {
        return [
            'id' => $user->id,
            'name' => $user->name
        ];
    }
});


Broadcast::channel('contact.{tenantId}.{contactId}', function ($user, $tenantId, $contactId) {
    return \App\Models\Team::findOrNew($tenantId)->users()->where('users.id', $user->id)->exists() &&
        \App\Models\Team::findOrNew($tenantId)->contacts()->where('contacts.id', $contactId)->exists();
});
