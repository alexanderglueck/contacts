<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\GiftIdea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class GiftIdeaController extends Controller
{
    protected $accessEntity = 'giftIdeas';

    private $validationRules = [
        'name' => 'required',
        'due_at' => 'nullable|sometimes|date|date_format:d.m.Y',
        'description' => 'nullable',
        'url' => 'nullable|sometimes|url',
    ];

    /**
     * Display a listing of the resource.
     *
     * @param Contact $contact
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Contact $contact)
    {
        $this->can('view');

        return view('gift_idea.index', [
            'contact' => $contact,
            'giftIdeas' => $contact->giftIdeas
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Contact $contact
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Contact $contact)
    {
        $this->can('create');

        return view('gift_idea.create', [
            'contact' => $contact,
            'giftIdea' => new GiftIdea()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param Contact                   $contact
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Contact $contact)
    {
        $this->can('create');

        $this->validate($request, $this->validationRules);

        $giftIdea = new GiftIdea();
        $giftIdea->fill($request->all());
        $giftIdea->contact_id = $contact->id;
        $giftIdea->created_by = Auth::id();
        $giftIdea->updated_by = Auth::id();

        if ($giftIdea->save()) {
            Session::flash('alert-success', trans('flash_message.gift_idea.created'));

            return redirect()->route('gift_ideas.index', [$contact->slug]);
        } else {
            Session::flash('alert-danger', trans('flash_message.gift_idea.not_created'));

            return redirect()->route('gift_ideas.create', [$contact->slug]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Contact               $contact
     * @param  \App\Models\GiftIdea $giftIdea
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Contact $contact, GiftIdea $giftIdea)
    {
        $this->can('view');

        return view('gift_idea.show', [
            'contact' => $contact,
            'giftIdea' => $giftIdea
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Contact               $contact
     * @param  \App\Models\GiftIdea $giftIdea
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Contact $contact, GiftIdea $giftIdea)
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
     *
     * @param  \Illuminate\Http\Request $request
     * @param Contact                   $contact
     * @param  \App\Models\GiftIdea     $giftIdea
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Contact $contact, GiftIdea $giftIdea)
    {
        $this->can('edit');

        $this->validate($request, $this->validationRules);
        $giftIdea->fill($request->all());
        $giftIdea->updated_by = Auth::id();

        if ($giftIdea->save()) {
            Session::flash('alert-success', trans('flash_message.gift_idea.updated'));

            return redirect()->route('gift_ideas.show', [$contact->slug, $giftIdea->id]);
        } else {
            Session::flash('alert-danger', trans('flash_message.gift_idea.not_updated'));

            return redirect()->route('gift_ideas.edit', [$contact->slug, $giftIdea->id]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Contact  $contact
     * @param GiftIdea $giftIdea
     *
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Contact $contact, GiftIdea $giftIdea)
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
     *
     * @param  \App\Models\Contact  $contact
     * @param  \App\Models\GiftIdea $giftIdea
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Contact $contact, GiftIdea $giftIdea)
    {
        $this->can('delete');

        return view('gift_idea.delete', [
            'contact' => $contact,
            'giftIdea' => $giftIdea
        ]);
    }
}
