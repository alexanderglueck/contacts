<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\GiftIdea;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\GiftIdea\GiftIdeaStoreRequest;
use App\Http\Requests\GiftIdea\GiftIdeaUpdateRequest;

class GiftIdeaController extends Controller
{
    protected ?string $accessEntity = 'giftIdeas';

    /**
     * Display a listing of the resource.
     */
    public function index(Contact $contact): View
    {
        $this->can('view');

        return view('gift_idea.index', [
            'contact' => $contact,
            'giftIdeas' => $contact->giftIdeas
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Contact $contact): View
    {
        $this->can('create');

        return view('gift_idea.create', [
            'contact' => $contact,
            'giftIdea' => new GiftIdea()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(GiftIdeaStoreRequest $request, Contact $contact): RedirectResponse
    {
        if ($contact->giftIdeas()->create($request->all())) {
            Session::flash('alert-success', trans('flash_message.gift_idea.created'));

            return redirect()->route('gift_ideas.index', [$contact->slug]);
        } else {
            Session::flash('alert-danger', trans('flash_message.gift_idea.not_created'));

            return redirect()->route('gift_ideas.create', [$contact->slug]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Contact $contact, GiftIdea $giftIdea): View
    {
        $this->can('view');

        return view('gift_idea.show', [
            'contact' => $contact,
            'giftIdea' => $giftIdea
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Contact $contact, GiftIdea $giftIdea): View
    {
        $this->can('edit');

        return view('gift_idea.edit', [
            'contact' => $contact,
            'giftIdea' => $giftIdea,
            'createButtonText' => trans('ui.edit_gift_idea')
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(GiftIdeaUpdateRequest $request, Contact $contact, GiftIdea $giftIdea): RedirectResponse
    {
        if ($giftIdea->update($request->all())) {
            Session::flash('alert-success', trans('flash_message.gift_idea.updated'));

            return redirect()->route('gift_ideas.show', [$contact->slug, $giftIdea->id]);
        } else {
            Session::flash('alert-danger', trans('flash_message.gift_idea.not_updated'));

            return redirect()->route('gift_ideas.edit', [$contact->slug, $giftIdea->id]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contact $contact, GiftIdea $giftIdea): RedirectResponse
    {
        $this->can('delete');

        if ($giftIdea->delete()) {
            Session::flash('alert-success', trans('flash_message.gift_idea.deleted'));

            return redirect()->route('gift_ideas.index', [$contact->slug]);
        } else {
            Session::flash('alert-danger', trans('flash_message.gift_idea.not_deleted'));

            return redirect()->route('gift_ideas.delete', [$contact->slug, $giftIdea->id]);
        }
    }

    /**
     * Show the form for deleting the specified resource.
     */
    public function delete(Contact $contact, GiftIdea $giftIdea): View
    {
        $this->can('delete');

        return view('gift_idea.delete', [
            'contact' => $contact,
            'giftIdea' => $giftIdea
        ]);
    }
}
