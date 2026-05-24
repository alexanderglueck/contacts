<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Database\Eloquent\Builder;
use Inertia\Inertia;
use Inertia\Response;

class ReportController extends Controller
{
    protected ?string $accessEntity = 'reports';

    public function index(): Response
    {
        $this->can('view');

        return Inertia::render('Reports/Index', [
            'reports' => [
                ['key' => 'inactive', 'name' => 'Inactive contacts'],
                ['key' => 'male', 'name' => 'Male contacts'],
                ['key' => 'female', 'name' => 'Female contacts'],
                ['key' => 'wrong_male', 'name' => 'Wrong male contacts'],
                ['key' => 'wrong_female', 'name' => 'Wrong female contacts'],
                ['key' => 'no_email', 'name' => 'No email address'],
                ['key' => 'no_date', 'name' => 'No date'],
                ['key' => 'no_address', 'name' => 'No address'],
                ['key' => 'no_number', 'name' => 'No phone number'],
                ['key' => 'no_url', 'name' => 'No website'],
                ['key' => 'no_lat_lng', 'name' => 'No coordinates'],
            ],
        ]);
    }

    public function inactive(): Response
    {
        $this->can('view');

        return $this->renderContactList(
            'Inactive contacts',
            Contact::sorted()->notActive()
        );
    }

    public function maleGender(): Response
    {
        $this->can('view');

        return $this->renderContactList(
            'Male contacts',
            Contact::sorted()->active()->where('gender_id', 1)
        );
    }

    public function femaleGender(): Response
    {
        $this->can('view');

        return $this->renderContactList(
            'Female contacts',
            Contact::sorted()->active()->where('gender_id', 2)
        );
    }

    public function wrongMaleGender(): Response
    {
        $this->can('view');

        return $this->renderContactList(
            'Wrong male contacts',
            Contact::sorted()->active()->where('gender_id', 1)->where('salutation', 'Frau')
        );
    }

    public function wrongFemaleGender(): Response
    {
        $this->can('view');

        return $this->renderContactList(
            'Wrong female contacts',
            Contact::sorted()->active()->where('gender_id', 2)->where('salutation', 'Herr')
        );
    }

    public function noEmail(): Response
    {
        $this->can('view');

        return $this->renderContactList(
            'Contacts without email address',
            Contact::select('contacts.*')->leftJoin('contact_emails', 'contacts.id', '=', 'contact_emails.contact_id')->whereNull('contact_emails.contact_id')
        );
    }

    public function noDate(): Response
    {
        $this->can('view');

        return $this->renderContactList(
            'Contacts without date',
            Contact::select('contacts.*')->leftJoin('contact_dates', 'contacts.id', '=', 'contact_dates.contact_id')->whereNull('contact_dates.contact_id')
        );
    }

    public function noAddress(): Response
    {
        $this->can('view');

        return $this->renderContactList(
            'Contacts without address',
            Contact::select('contacts.*')->leftJoin('contact_addresses', 'contacts.id', '=', 'contact_addresses.contact_id')->whereNull('contact_addresses.contact_id')
        );
    }

    public function noNumber(): Response
    {
        $this->can('view');

        return $this->renderContactList(
            'Contacts without phone number',
            Contact::select('contacts.*')->leftJoin('contact_numbers', 'contacts.id', '=', 'contact_numbers.contact_id')->whereNull('contact_numbers.contact_id')
        );
    }

    public function noUrl(): Response
    {
        $this->can('view');

        return $this->renderContactList(
            'Contacts without website',
            Contact::select('contacts.*')->leftJoin('contact_urls', 'contacts.id', '=', 'contact_urls.contact_id')->whereNull('contact_urls.contact_id')
        );
    }

    public function noLatLng(): Response
    {
        $this->can('view');

        return $this->renderContactList(
            'Contacts without coordinates',
            Contact::select('contacts.*')->whereNull('latitude')->orWhereNull('longitude')->orderBy('name')->join('contact_addresses', 'contacts.id', '=', 'contact_addresses.contact_id')
        );
    }

    private function renderContactList(string $title, Builder $query): Response
    {
        return Inertia::render('Reports/ContactList', [
            'title' => $title,
            'contacts' => $query->paginate(10)->through(fn ($contact) => [
                'id' => $contact->id,
                'ulid' => $contact->ulid,
                'fullname' => $contact->fullname,
            ]),
        ]);
    }
}
