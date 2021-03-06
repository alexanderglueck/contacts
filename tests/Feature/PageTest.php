<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function map_page_works()
    {
        $this->createUser('view map');

        $response = $this->get(route('map.index'));

        $response->assertStatus(200);
        $response->assertSee(trans('ui.map'));
    }

    /** @test */
    public function calendar_page_works()
    {
        $this->createUser('view calendar');

        $response = $this->get(route('calendar.index'));

        $response->assertStatus(200);
        $response->assertSee(trans('ui.calendar'));
    }

    /** @test */
    public function contact_groups_page_works()
    {
        $this->createUser('view contactGroups');

        $response = $this->get(route('contact_groups.index'));

        $response->assertStatus(200);
        $response->assertSee('Kontaktgruppen verwalten');
    }

    /** @test */
    public function contact_groups_create_page_works()
    {
        $this->createUser('create contactGroups');

        $response = $this->get(route('contact_groups.create'));

        $response->assertStatus(200);
        $response->assertSee('Kontaktgruppe hinzufügen');
    }

    /** @test */
    public function no_lat_lng_report_page_works()
    {
        $this->createUser('view reports');

        $response = $this->get(route('reports.no_lat_lng'));

        $response->assertStatus(200);
        $response->assertSee('Ergebnis');
    }

    /** @test */
    public function no_url_report_page_works()
    {
        $this->createUser('view reports');

        $response = $this->get(route('reports.no_url'));

        $response->assertStatus(200);
        $response->assertSee('Ergebnis');
    }

    /** @test */
    public function no_number_report_page_works()
    {
        $this->createUser('view reports');

        $response = $this->get(route('reports.no_number'));

        $response->assertStatus(200);
        $response->assertSee('Ergebnis');
    }

    /** @test */
    public function no_address_report_page_works()
    {
        $this->createUser('view reports');

        $response = $this->get(route('reports.no_address'));

        $response->assertStatus(200);
        $response->assertSee('Ergebnis');
    }

    /** @test */
    public function no_date_report_page_works()
    {
        $this->createUser('view reports');

        $response = $this->get(route('reports.no_date'));

        $response->assertStatus(200);
        $response->assertSee('Ergebnis');
    }

    /** @test */
    public function no_email_report_page_works()
    {
        $this->createUser('view reports');

        $response = $this->get(route('reports.no_email'));

        $response->assertStatus(200);
        $response->assertSee('Ergebnis');
    }

    /** @test */
    public function wrong_female_report_page_works()
    {
        $this->createUser('view reports');

        $response = $this->get(route('reports.wrong_female'));

        $response->assertStatus(200);
        $response->assertSee('Ergebnis');
    }

    /** @test */
    public function wrong_male_report_page_works()
    {
        $this->createUser('view reports');

        $response = $this->get(route('reports.wrong_male'));

        $response->assertStatus(200);
        $response->assertSee('Ergebnis');
    }

    /** @test */
    public function female_report_page_works()
    {
        $this->createUser('view reports');

        $response = $this->get(route('reports.female'));

        $response->assertStatus(200);
        $response->assertSee('Ergebnis');
    }

    /** @test */
    public function male_report_page_works()
    {
        $this->createUser('view reports');

        $response = $this->get(route('reports.male'));

        $response->assertStatus(200);
        $response->assertSee('Ergebnis');
    }

    /** @test */
    public function inactive_report_page_works()
    {
        $this->createUser('view reports');

        $response = $this->get(route('reports.inactive'));

        $response->assertStatus(200);
        $response->assertSee('Ergebnis');
    }

    /** @test */
    public function reports_page_works()
    {
        $this->createUser('view reports');

        $response = $this->get(route('reports.index'));

        $response->assertStatus(200);
        $response->assertSee('Berichte');
    }

    /** @test */
    public function export_page_works()
    {
        $this->createUser('create export');

        $response = $this->get(route('export.index'));

        $response->assertStatus(200);
        $response->assertSee('Kontakte exportieren');
    }

    /** @test */
    public function import_page_works()
    {
        $this->createUser('create import');

        $response = $this->get(route('import.index'));

        $response->assertStatus(200);
        $response->assertSee('Kontakte importieren');
    }

    /** @test */
    public function create_contacts_page_works()
    {
        $this->createUser('create contacts');

        $response = $this->get(route('contacts.create'));

        $response->assertStatus(200);
        $response->assertSee('Kontakt hinzufügen');
    }

    /** @test */
    public function contacts_page_works()
    {
        $this->createUser('view contacts');

        $response = $this->get(route('contacts.index'));

        $response->assertStatus(200);
        $response->assertSee(trans('ui.manage_contacts'));
    }
}
