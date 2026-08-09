<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    protected ?string $accessEntity = 'contacts';

    /**
     * How many suggestions the nav typeahead shows. Kept small — it's a
     * jump-to-contact box, not a results page; "show all" hands the full
     * query over to contacts.index.
     */
    private const SUGGESTION_LIMIT = 8;

    public function search(Request $request): RedirectResponse
    {
        return redirect()->route('contacts.index', ['q' => $request->post('search')]);
    }

    /**
     * Typeahead behind the global nav search. Goes through the same Scout
     * index as contacts.index so the suggestions and the full result page
     * can't disagree about what matches.
     */
    public function suggest(Request $request): JsonResponse
    {
        $this->can('view');

        $term = trim((string) $request->query('q', ''));

        if ($term === '') {
            return response()->json(['data' => []]);
        }

        $contacts = Contact::search($term)->take(self::SUGGESTION_LIMIT)->get();

        return response()->json([
            'data' => $contacts->map(fn (Contact $contact) => [
                'ulid' => $contact->ulid,
                'fullname' => $contact->fullname,
                'firstname' => $contact->firstname,
                'lastname' => $contact->lastname,
                'image' => $contact->image,
            ])->values(),
        ]);
    }
}
