<?php

namespace App\Http\Controllers;

use App\Http\Requests\GiftIdea\GiftIdeaStoreRequest;
use App\Http\Requests\GiftIdea\GiftIdeaUpdateRequest;
use App\Models\Contact;
use App\Models\GiftIdea;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;

class GiftIdeaController extends Controller
{
    protected ?string $accessEntity = 'giftIdeas';

    public function store(GiftIdeaStoreRequest $request, Contact $contact): RedirectResponse
    {
        if ($contact->giftIdeas()->create($request->all())) {
            Session::flash('alert-success', trans('flash_message.gift_idea.created'));
        } else {
            Session::flash('alert-danger', trans('flash_message.gift_idea.not_created'));
        }

        return redirect()->route('contacts.show', $contact->ulid);
    }

    public function update(GiftIdeaUpdateRequest $request, Contact $contact, GiftIdea $giftIdea): RedirectResponse
    {
        if ($giftIdea->update($request->all())) {
            Session::flash('alert-success', trans('flash_message.gift_idea.updated'));
        } else {
            Session::flash('alert-danger', trans('flash_message.gift_idea.not_updated'));
        }

        return redirect()->route('contacts.show', $contact->ulid);
    }

    public function destroy(Contact $contact, GiftIdea $giftIdea): RedirectResponse
    {
        $this->can('delete');

        if ($giftIdea->delete()) {
            Session::flash('alert-success', trans('flash_message.gift_idea.deleted'));
        } else {
            Session::flash('alert-danger', trans('flash_message.gift_idea.not_deleted'));
        }

        return redirect()->route('contacts.show', $contact->ulid);
    }
}
