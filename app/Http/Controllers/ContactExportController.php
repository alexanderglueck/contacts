<?php

namespace App\Http\Controllers;

use App\Exports\ContactsExport;
use App\Http\Requests\ContactExport\ContactExportExportRequest;
use App\Models\ContactGroup;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ContactExportController extends Controller
{
    protected ?string $accessEntity = 'export';

    public function index(): Response
    {
        $this->can('create');

        return Inertia::render('ContactExport/Index', [
            'contactGroups' => ContactGroup::sorted()->get(['id', 'name']),
        ]);
    }

    public function export(ContactExportExportRequest $request): BinaryFileResponse
    {
        $contacts = ContactGroup::find($request->contact_group_id)
            ->contacts()
            ->active()
            ->sorted()
            ->get();

        return (new ContactsExport($contacts))->download('contacts.xlsx');
    }
}
