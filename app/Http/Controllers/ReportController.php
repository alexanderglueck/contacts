<?php

namespace App\Http\Controllers;

use App\Models\Contact;

class ReportController extends Controller
{
    protected $accessEntity = 'reports';

    public function index()
    {
        $this->can('view');

        return view('reports.index', [
            'reports' => [
                'inactive' => 'Inaktive Kontakte',
                'male' => 'Männliche Kontakte',
                'female' => 'Weibliche Kontakte',
                'wrong_male' => 'Falsche männliche Kontakte',
                'wrong_female' => 'Falsche weibliche Kontakte',
                'no_email' => 'Keine E-Mail Adresse',
                'no_date' => 'Keine Datumsangabe',
                'no_address' => 'Keine Adresse',
                'no_number' => 'Keine Nummer',
                'no_url' => 'Keine Website',
                'no_lat_lng' => 'Keine Koordinaten',
            ]
        ]);
    }

    /**
     * Display a listing of inactive contacts.
     *
     * @return \Illuminate\Http\Response
     */
    public function inactive()
    {
        $this->can('view');

        return view('reports.show', [
            'contacts' => Contact::sorted()->notActive()->paginate(10)
        ]);
    }

    public function maleGender()
    {
        $this->can('view');

        return view('reports.show', [
            'contacts' => Contact::sorted()->active()->where('gender_id', 1)->paginate(10)
        ]);
    }

    public function femaleGender()
    {
        $this->can('view');

        return view('reports.show', [
            'contacts' => Contact::sorted()->active()->where('gender_id', 2)->paginate(10)
        ]);
    }

    public function wrongMaleGender()
    {
        $this->can('view');

        return view('reports.show', [
            'contacts' => Contact::sorted()->active()->where('gender_id', 1)->where('salutation', 'Frau')->paginate(10)
        ]);
    }

    public function wrongFemaleGender()
    {
        $this->can('view');

        return view('reports.show', [
            'contacts' => Contact::sorted()->active()->where('gender_id', 2)->where('salutation', 'Herr')->paginate(10)
        ]);
    }

    public function noEmail()
    {
        $this->can('view');

        return view('reports.show', [
            'contacts' => Contact::select('contacts.*')->leftJoin('contact_emails', 'contacts.id', '=', 'contact_emails.contact_id')->whereNull('contact_emails.contact_id')->paginate(10)
        ]);
    }

    public function noDate()
    {
        $this->can('view');

        return view('reports.show', [
            'contacts' => Contact::select('contacts.*')->leftJoin('contact_dates', 'contacts.id', '=', 'contact_dates.contact_id')->whereNull('contact_dates.contact_id')->paginate(10)
        ]);
    }

    public function noAddress()
    {
        $this->can('view');

        return view('reports.show', [
            'contacts' => Contact::select('contacts.*')->leftJoin('contact_addresses', 'contacts.id', '=', 'contact_addresses.contact_id')->whereNull('contact_addresses.contact_id')->paginate(10)
        ]);
    }

    public function noNumber()
    {
        $this->can('view');

        return view('reports.show', [
            'contacts' => Contact::select('contacts.*')->leftJoin('contact_numbers', 'contacts.id', '=', 'contact_numbers.contact_id')->whereNull('contact_numbers.contact_id')->paginate(10)
        ]);
    }

    public function noUrl()
    {
        $this->can('view');

        return view('reports.show', [
            'contacts' => Contact::select('contacts.*')->leftJoin('contact_urls', 'contacts.id', '=', 'contact_urls.contact_id')->whereNull('contact_urls.contact_id')->paginate(10)
        ]);
    }

    public function noLatLng()
    {
        $this->can('view');

        return view('reports.show', [
            'contacts' => Contact::select('contacts.*')->whereNull('latitude')->orWhereNull('longitude')->orderBy('name')->join('contact_addresses', 'contacts.id', '=', 'contact_addresses.contact_id')->paginate(10)
        ]);
    }
}
