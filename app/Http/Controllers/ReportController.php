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

        $entries = [
            'inactive', 'male', 'female', 'wrong_male', 'wrong_female',
            'no_email', 'no_date', 'no_address', 'no_number', 'no_url', 'no_lat_lng',
        ];

        return Inertia::render('Reports/Index', [
            'reports' => array_map(
                fn ($key) => ['key' => $key, 'name' => __("reports.{$key}")],
                $entries,
            ),
        ]);
    }

    public function inactive(): Response
    {
        $this->can('view');

        return $this->renderContactList(
            __('reports.titles.inactive'),
            Contact::sorted()->notActive()
        );
    }

    public function maleGender(): Response
    {
        $this->can('view');

        return $this->renderContactList(
            __('reports.titles.male'),
            Contact::sorted()->active()->where('gender_id', 1)
        );
    }

    public function femaleGender(): Response
    {
        $this->can('view');

        return $this->renderContactList(
            __('reports.titles.female'),
            Contact::sorted()->active()->where('gender_id', 2)
        );
    }

    public function wrongMaleGender(): Response
    {
        $this->can('view');

        return $this->renderContactList(
            __('reports.titles.wrong_male'),
            Contact::sorted()->active()->where('gender_id', 1)->where('salutation', 'Frau')
        );
    }

    public function wrongFemaleGender(): Response
    {
        $this->can('view');

        return $this->renderContactList(
            __('reports.titles.wrong_female'),
            Contact::sorted()->active()->where('gender_id', 2)->where('salutation', 'Herr')
        );
    }

    public function noEmail(): Response
    {
        $this->can('view');

        return $this->renderContactList(
            __('reports.titles.no_email'),
            Contact::select('contacts.*')->leftJoin('contact_emails', 'contacts.id', '=', 'contact_emails.contact_id')->whereNull('contact_emails.contact_id')
        );
    }

    public function noDate(): Response
    {
        $this->can('view');

        return $this->renderContactList(
            __('reports.titles.no_date'),
            Contact::select('contacts.*')->leftJoin('contact_dates', 'contacts.id', '=', 'contact_dates.contact_id')->whereNull('contact_dates.contact_id')
        );
    }

    public function noAddress(): Response
    {
        $this->can('view');

        return $this->renderContactList(
            __('reports.titles.no_address'),
            Contact::select('contacts.*')->leftJoin('contact_addresses', 'contacts.id', '=', 'contact_addresses.contact_id')->whereNull('contact_addresses.contact_id')
        );
    }

    public function noNumber(): Response
    {
        $this->can('view');

        return $this->renderContactList(
            __('reports.titles.no_number'),
            Contact::select('contacts.*')->leftJoin('contact_numbers', 'contacts.id', '=', 'contact_numbers.contact_id')->whereNull('contact_numbers.contact_id')
        );
    }

    public function noUrl(): Response
    {
        $this->can('view');

        return $this->renderContactList(
            __('reports.titles.no_url'),
            Contact::select('contacts.*')->leftJoin('contact_urls', 'contacts.id', '=', 'contact_urls.contact_id')->whereNull('contact_urls.contact_id')
        );
    }

    public function noLatLng(): Response
    {
        $this->can('view');

        return $this->renderContactList(
            __('reports.titles.no_lat_lng'),
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
