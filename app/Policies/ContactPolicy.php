<?php

namespace App\Policies;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ContactPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the contact.
     *
     * @param  User    $user
     * @param  Contact $contact
     *
     * @return mixed
     */
    public function view(User $user, Contact $contact)
    {
        return $user->id == $contact->created_by;
    }

    /**
     * Determine whether the user can create contacts.
     *
     * @param  User $user
     *
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the contact.
     *
     * @param  User    $user
     * @param  Contact $contact
     *
     * @return mixed
     */
    public function update(User $user, Contact $contact)
    {
        //
    }

    /**
     * Determine whether the user can delete the contact.
     *
     * @param  User    $user
     * @param  Contact $contact
     *
     * @return mixed
     */
    public function delete(User $user, Contact $contact)
    {
        //
    }
}
