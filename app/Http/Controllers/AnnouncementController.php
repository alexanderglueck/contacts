<?php

namespace App\Http\Controllers;

use App\Models\Admin\Announcement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Inertia\Response;
use App\Http\Requests\Announcement\StoreAnnouncement;
use App\Http\Requests\Announcement\DeleteAnnouncement;
use App\Http\Requests\Announcement\UpdateAnnouncement;

class AnnouncementController extends Controller
{
    protected ?string $accessEntity = 'announcements';

    public function index(Request $request): Response
    {
        $this->can('view');

        $user = Auth::user();
        $mapper = fn ($announcement) => [
            'id' => $announcement->id,
            'ulid' => $announcement->ulid,
            'title' => $announcement->title,
            'is_pinned' => $announcement->isPinned(),
        ];

        return Inertia::render('Announcements/Index', [
            'active' => Announcement::active()->paginate(10)->through($mapper),
            'inactive' => Announcement::inactive()->paginate(10)->through($mapper),
            'displayed' => Announcement::displayed($request->user())->paginate(10)->through($mapper),
            'hidden' => Announcement::hidden($request->user())->paginate(10)->through($mapper),
            'read' => Announcement::read($request->user())->paginate(10)->through($mapper),
            'unread' => Announcement::unread($request->user())->paginate(10)->through($mapper),
            'canCreate' => $user->checkPermissionTo('create announcements'),
        ]);
    }

    public function markAsRead(Request $request, Announcement $announcement): RedirectResponse
    {
        $announcement->markAsRead($request->user());

        return redirect()->route('announcements.index');
    }

    public function create(): Response
    {
        $this->can('create');

        return Inertia::render('Announcements/Create', [
            //
        ]);
    }

    public function store(StoreAnnouncement $request): RedirectResponse
    {
        $announcement = new Announcement();
        $announcement->fill($request->all());
        $announcement->user_id = $request->user()->id;

        if ( ! $announcement->save()) {
            Session::flash('alert-danger', trans('flash_message.announcement.not_created'));

            return redirect()->route('announcements.create');
        }

        Session::flash('alert-success', trans('flash_message.announcement.created'));

        return redirect()->route('announcements.show', [$announcement->ulid]);
    }

    public function show(Announcement $announcement): Response
    {
        $this->can('view');

        $user = Auth::user();

        return Inertia::render('Announcements/Show', [
            'announcement' => [
                'id' => $announcement->id,
                'ulid' => $announcement->ulid,
                'title' => $announcement->title,
                'body' => $announcement->body,
                'parsed_body' => $announcement->parsedBody,
                'expired_at' => $announcement->expired_at,
                'pinned_at' => $announcement->pinned_at,
                'is_pinned' => $announcement->isPinned(),
            ],
            'can' => [
                'edit' => $user->checkPermissionTo('edit announcements'),
                'delete' => $user->checkPermissionTo('delete announcements'),
            ],
        ]);
    }

    public function edit(Announcement $announcement): Response
    {
        $this->can('edit');

        return Inertia::render('Announcements/Edit', [
            'announcement' => [
                'id' => $announcement->id,
                'ulid' => $announcement->ulid,
                'title' => $announcement->title,
                'body' => $announcement->body,
                'expired_at' => $announcement->expired_at,
                'pinned_at' => $announcement->pinned_at,
            ],
        ]);
    }

    public function update(UpdateAnnouncement $request, Announcement $announcement): RedirectResponse
    {
        $announcement->fill($request->all());

        if ( ! $announcement->save()) {
            Session::flash('alert-danger', trans('flash_message.announcement.not_updated'));

            return redirect()->route('announcements.edit', [$announcement->ulid]);
        }

        Session::flash('alert-success', trans('flash_message.announcement.updated'));

        return redirect()->route('announcements.show', [$announcement->ulid]);
    }

    public function destroy(DeleteAnnouncement $request, Announcement $announcement): RedirectResponse
    {
        if ( ! $announcement->delete()) {
            Session::flash('alert-danger', trans('flash_message.announcement.not_deleted'));

            return redirect()->route('announcements.delete', [$announcement->ulid]);
        }

        Session::flash('alert-success', trans('flash_message.announcement.deleted'));

        return redirect()->route('announcements.index');
    }

    public function delete(Announcement $announcement): Response
    {
        $this->can('delete');

        return Inertia::render('Announcements/Delete', [
            'announcement' => [
                'id' => $announcement->id,
                'ulid' => $announcement->ulid,
                'title' => $announcement->title,
            ],
        ]);
    }
}
