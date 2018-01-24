<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\ContactAddress;
use App\Models\ContactDate;
use App\Models\ContactEmail;
use Illuminate\Http\Request;

class ReportController extends Controller
{

    public function index()
    {
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
        return view('reports.show', [
            'contacts' => Contact::sorted()->notActive()->get()
        ]);
    }

    public function maleGender() {
        return view('reports.show', [
            'contacts' => Contact::sorted()->active()->where('gender_id', 1)->get()
        ]);
    }

    public function femaleGender() {
        return view('reports.show', [
            'contacts' => Contact::sorted()->active()->where('gender_id', 2)->get()
        ]);
    }

    public function wrongMaleGender() {
        return view('reports.show', [
            'contacts' => Contact::sorted()->active()->where('gender_id', 1)->where('salutation', 'Frau')->get()
        ]);
    }

    public function wrongFemaleGender() {
        return view('reports.show', [
            'contacts' => Contact::sorted()->active()->where('gender_id', 2)->where('salutation', 'Herr')->get()
        ]);
    }


    public function noEmail() {
        return view('reports.show', [
            'contacts' => Contact::select('contacts.*')->leftJoin('contact_emails', 'contacts.id', '=', 'contact_emails.contact_id')->whereNull('contact_emails.contact_id')->get()
        ]);
    }

    public function noDate() {
        return view('reports.show', [
            'contacts' => Contact::select('contacts.*')->leftJoin('contact_dates', 'contacts.id', '=', 'contact_dates.contact_id')->whereNull('contact_dates.contact_id')->get()
        ]);
    }

    public function noAddress() {
        return view('reports.show', [
            'contacts' => Contact::select('contacts.*')->leftJoin('contact_addresses', 'contacts.id', '=', 'contact_addresses.contact_id')->whereNull('contact_addresses.contact_id')->get()
        ]);
    }

    public function noNumber() {
        return view('reports.show', [
            'contacts' => Contact::select('contacts.*')->leftJoin('contact_numbers', 'contacts.id', '=', 'contact_numbers.contact_id')->whereNull('contact_numbers.contact_id')->get()
        ]);
    }

    public function noUrl() {
        return view('reports.show', [
            'contacts' => Contact::select('contacts.*')->leftJoin('contact_urls', 'contacts.id', '=', 'contact_urls.contact_id')->whereNull('contact_urls.contact_id')->get()
        ]);
    }

    public function noLatLng() {
        return view('reports.show', [
            'contacts' => Contact::select('contacts.*')->whereNull('latitude')->orWhereNull('longitude')->orderBy('name')->join('contact_addresses', 'contacts.id', '=', 'contact_addresses.contact_id')->get()
        ]);
    }

}
