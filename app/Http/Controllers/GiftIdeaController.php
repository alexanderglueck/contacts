<?php

namespace App\Http\Controllers;

use App\Http\Requests\GiftIdea\GiftIdeaStoreRequest;
use App\Http\Requests\GiftIdea\GiftIdeaUpdateRequest;
use App\Models\Contact;
use App\Models\GiftIdea;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Inertia\Response;

class GiftIdeaController extends Controller
{
    protected ?string $accessEntity = 'giftIdeas';

    public function index(Contact $contact): Response
    {
        $this->can('view');

        return Inertia::render('GiftIdeas/Index', [
            'contact' => ['ulid' => $contact->ulid, 'fullname' => $contact->fullname],
            'items' => $contact->giftIdeas->map(fn ($g) => [
                'ulid' => $g->ulid,
                'name' => $g->name,
                'description' => $g->description,
                'url' => $g->url,
                'formatted_due_at' => $g->formatted_due_at,
            ]),
            'canCreate' => Auth::user()->checkPermissionTo('create giftIdeas'),
        ]);
    }

    public function create(Contact $contact): Response
    {
        $this->can('create');

        return Inertia::render('GiftIdeas/Create', [
            'contact' => ['ulid' => $contact->ulid, 'fullname' => $contact->fullname],
        ]);
    }

    public function store(GiftIdeaStoreRequest $request, Contact $contact): RedirectResponse
    {
        if ($contact->giftIdeas()->create($request->all())) {
            Session::flash('alert-success', trans('flash_message.gift_idea.created'));

            return redirect()->route('gift_ideas.index', [$contact->ulid]);
        } else {
            Session::flash('alert-danger', trans('flash_message.gift_idea.not_created'));

            return redirect()->route('gift_ideas.create', [$contact->ulid]);
        }
    }

    public function show(Contact $contact, GiftIdea $giftIdea): Response
    {
        $this->can('view');

        $user = Auth::user();

        return Inertia::render('GiftIdeas/Show', [
            'contact' => ['ulid' => $contact->ulid, 'fullname' => $contact->fullname],
            'item' => [
                'ulid' => $giftIdea->ulid,
                'name' => $giftIdea->name,
                'description' => $giftIdea->description,
                'url' => $giftIdea->url,
                'formatted_due_at' => $giftIdea->formatted_due_at,
            ],
            'can' => [
                'edit' => $user->checkPermissionTo('edit giftIdeas'),
                'delete' => $user->checkPermissionTo('delete giftIdeas'),
            ],
        ]);
    }

    public function edit(Contact $contact, GiftIdea $giftIdea): Response
    {
        $this->can('edit');

        return Inertia::render('GiftIdeas/Edit', [
            'contact' => ['ulid' => $contact->ulid, 'fullname' => $contact->fullname],
            'item' => [
                'ulid' => $giftIdea->ulid,
                'name' => $giftIdea->name,
                'description' => $giftIdea->description,
                'url' => $giftIdea->url,
                'due_at' => $giftIdea->formatted_due_at,
            ],
        ]);
    }

    public function update(GiftIdeaUpdateRequest $request, Contact $contact, GiftIdea $giftIdea): RedirectResponse
    {
        if ($giftIdea->update($request->all())) {
            Session::flash('alert-success', trans('flash_message.gift_idea.updated'));

            return redirect()->route('gift_ideas.show', [$contact->ulid, $giftIdea->ulid]);
        } else {
            Session::flash('alert-danger', trans('flash_message.gift_idea.not_updated'));

            return redirect()->route('gift_ideas.edit', [$contact->ulid, $giftIdea->ulid]);
        }
    }

    public function destroy(Contact $contact, GiftIdea $giftIdea): RedirectResponse
    {
        $this->can('delete');

        if ($giftIdea->delete()) {
            Session::flash('alert-success', trans('flash_message.gift_idea.deleted'));

            return redirect()->route('gift_ideas.index', [$contact->ulid]);
        } else {
            Session::flash('alert-danger', trans('flash_message.gift_idea.not_deleted'));

            return redirect()->route('gift_ideas.delete', [$contact->ulid, $giftIdea->ulid]);
        }
    }

    public function delete(Contact $contact, GiftIdea $giftIdea): Response
    {
        $this->can('delete');

        return Inertia::render('GiftIdeas/Delete', [
            'contact' => ['ulid' => $contact->ulid, 'fullname' => $contact->fullname],
            'item' => [
                'ulid' => $giftIdea->ulid,
                'name' => $giftIdea->name,
                'description' => $giftIdea->description,
                'url' => $giftIdea->url,
                'formatted_due_at' => $giftIdea->formatted_due_at,
            ],
        ]);
    }
}
