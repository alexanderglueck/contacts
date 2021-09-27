<?php

namespace App\Http\Controllers;

use App\Models\Admin\Announcement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\Announcement\StoreAnnouncement;
use App\Http\Requests\Announcement\DeleteAnnouncement;
use App\Http\Requests\Announcement\UpdateAnnouncement;

class AnnouncementController extends Controller
{
    protected ?string $accessEntity = 'announcements';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->can('view');

        return view('announcement.index', [
            'active' => Announcement::active()->paginate(10),
            'inactive' => Announcement::inactive()->paginate(10),
            'displayed' => Announcement::displayed(auth()->user())->paginate(10),
            'hidden' => Announcement::hidden(auth()->user())->paginate(10),
            'read' => Announcement::read(auth()->user())->paginate(10),
            'unread' => Announcement::unread(auth()->user())->paginate(10)
        ]);
    }

    public function markAsRead(Announcement $announcement)
    {
        $announcement->markAsRead(auth()->user());

        return redirect()->route('announcements.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->can('create');

        return view('announcement.create', [
            'announcement' => new Announcement
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreAnnouncement $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAnnouncement $request)
    {
        $announcement = new Announcement();
        $announcement->fill($request->all());
        $announcement->user_id = Auth::id();

        if ( ! $announcement->save()) {
            Session::flash('alert-danger', trans('flash_message.announcement.not_created'));

            return redirect()->route('announcements.create');
        }

        Session::flash('alert-success', trans('flash_message.announcement.created'));

        return redirect()->route('announcements.show', [$announcement->slug]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Admin\Announcement $announcement
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Announcement $announcement)
    {
        $this->can('view');

        return view('announcement.show', [
            'announcement' => $announcement
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Admin\Announcement $announcement
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Announcement $announcement)
    {
        $this->can('edit');

        return view('announcement.edit', [
            'announcement' => $announcement,
            'createButtonText' => trans('ui.edit_announcement'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateAnnouncement              $request
     * @param  \App\Models\Admin\Announcement $announcement
     *
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAnnouncement $request, Announcement $announcement)
    {
        $announcement->fill($request->all());

        if ( ! $announcement->save()) {
            Session::flash('alert-danger', trans('flash_message.announcement.not_updated'));

            return redirect()->route('announcements.edit', [$announcement->slug]);
        }

        Session::flash('alert-success', trans('flash_message.announcement.updated'));

        return redirect()->route('announcements.show', [$announcement->slug]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DeleteAnnouncement              $request
     * @param  \App\Models\Admin\Announcement $announcement
     *
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(DeleteAnnouncement $request, Announcement $announcement)
    {
        if ( ! $announcement->delete()) {
            Session::flash('alert-danger', trans('flash_message.announcement.not_deleted'));

            return redirect()->route('announcements.delete', [$announcement->slug]);
        }

        Session::flash('alert-success', trans('flash_message.announcement.deleted'));

        return redirect()->route('announcements.index');
    }

    /**
     * Show the form for deleting the specified resource.
     *
     * @param  \App\Models\Admin\Announcement $announcement
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Announcement $announcement)
    {
        $this->can('delete');

        return view('announcement.delete', [
            'announcement' => $announcement
        ]);
    }
}
