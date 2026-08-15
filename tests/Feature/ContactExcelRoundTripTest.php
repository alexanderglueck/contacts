<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\ContactGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Tests\TestCase;

/**
 * The export and import controllers are a matched pair: the export writes six
 * sheets of positional columns and the import reads them back by index. Both
 * sides run through maatwebsite/excel and phpoffice/phpspreadsheet, and neither
 * had coverage beyond "the page renders" -- so a spreadsheet library upgrade
 * could break the whole feature with a green suite. This drives a real workbook
 * out through the export route and back in through the import route.
 */
class ContactExcelRoundTripTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function a_contact_survives_an_export_and_import_round_trip()
    {
        $this->createUser('create export');

        $source = create(ContactGroup::class);
        $target = create(ContactGroup::class);

        $contact = create(Contact::class, [
            'lastname' => 'Kowalski',
            'firstname' => 'Marta',
            'company' => 'Beispiel GmbH',
            'nickname' => 'mk',
            'active' => 1,
        ]);
        $contact->contactGroups()->attach($source->id);

        $workbook = $this->export($source);

        $this->post(route('import.import'), [
            'contact_group_id' => $target->id,
            'import_file' => new UploadedFile(
                $workbook,
                'contacts.xlsx',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                null,
                true
            ),
        ])->assertRedirect(route('import.index'));

        $imported = Contact::where('id', '!=', $contact->id)->first();

        $this->assertNotNull($imported, 'The exported contact was not imported back.');
        $this->assertSame('Kowalski', $imported->lastname);
        $this->assertSame('Marta', $imported->firstname);
        $this->assertSame('Beispiel GmbH', $imported->company);
        $this->assertSame('mk', $imported->nickname);
        $this->assertSame($contact->gender_id, $imported->gender_id);
    }

    /**
     * Runs the export route and hands back a copy of the generated workbook.
     * The copy matters: the response marks the temporary file for deletion once
     * it is sent, and it lives in the framework's own temp directory either way.
     */
    protected function export(ContactGroup $group): string
    {
        $response = $this->post(route('export.export'), [
            'contact_group_id' => $group->id,
        ]);

        $response->assertStatus(200);

        $file = $response->baseResponse;

        $this->assertInstanceOf(BinaryFileResponse::class, $file);

        $copy = tempnam(sys_get_temp_dir(), 'roundtrip').'.xlsx';
        copy($file->getFile()->getPathname(), $copy);

        return $copy;
    }
}
