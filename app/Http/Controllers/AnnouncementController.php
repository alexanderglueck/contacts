<?php

namespace App\Http\Controllers;

use App\Admin\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AnnouncementController extends Controller
{
    private $validationRules = [
        'title' => 'required',
        'body' => 'required'
    ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('announcement.index', [
            'announcements' => Announcement::paginate(10)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('announcement.create', [
            'announcement' => new Announcement
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, $this->validationRules);

        $announcement = new Announcement();
        $announcement->fill($request->all());
        $announcement->user_id = Auth::id();

        if ($announcement->save()) {
            Session::flash('alert-success', trans('flash_message.announcement.created'));

            return redirect()->route('announcements.show', [$announcement->slug]);
        } else {
            Session::flash('alert-danger', trans('flash_message.announcement.not_created'));

            return redirect()->route('announcements.create');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Admin\Announcement $announcement
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Announcement $announcement)
    {
        return view('announcement.show', [
            'announcement' => $announcement
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Admin\Announcement $announcement
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Announcement $announcement)
    {
        return view('announcement.edit', [
            'announcement' => $announcement,
            'createButtonText' => trans('ui.edit_announcement'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Admin\Announcement  $announcement
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Announcement $announcement)
    {
        $this->validate($request, $this->validationRules);

        $announcement->fill($request->all());

        if ($announcement->save()) {
            Session::flash('alert-success', trans('flash_message.announcement.updated'));

            return redirect()->route('announcements.show', [$announcement->slug]);
        } else {
            Session::flash('alert-danger', trans('flash_message.announcement.not_updated'));

            return redirect()->route('announcements.edit', [$announcement->slug]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Admin\Announcement $announcement
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Announcement $announcement)
    {
        if ($announcement->delete()) {
            Session::flash('alert-success', trans('flash_message.announcement.deleted'));

            return redirect()->route('announcements.index');
        } else {
            Session::flash('alert-danger', trans('flash_message.announcement.not_deleted'));

            return redirect()->route('announcements.delete', [$announcement->slug]);
        }
    }

    /**
     * Show the form for deleting the specified resource.
     *
     * @param  \App\Admin\Announcement $announcement
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Announcement $announcement)
    {
        return view('announcement.delete', [
            'announcement' => $announcement
        ]);
    }
}
