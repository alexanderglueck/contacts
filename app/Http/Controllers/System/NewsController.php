<?php

namespace App\Http\Controllers\System;

use App\Models\System\News;
use App\Http\Controllers\Controller;
use App\Http\Requests\News\EditNews;
use App\Http\Requests\News\DeleteNews;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Inertia\Response;

class NewsController extends Controller
{
    public function index(Request $request): Response
    {
        $mapper = fn ($news) => [
            'id' => $news->id,
            'slug' => $news->slug,
            'title' => $news->title,
            'is_pinned' => $news->isPinned(),
        ];

        $data = [
            'active' => News::active()->paginate(10)->through($mapper),
            'inactive' => News::inactive()->paginate(10)->through($mapper),
        ];

        if (auth()->check()) {
            $data['displayed'] = News::displayed($request->user())->paginate(10)->through($mapper);
            $data['hidden'] = News::hidden($request->user())->paginate(10)->through($mapper);
            $data['read'] = News::read($request->user())->paginate(10)->through($mapper);
            $data['unread'] = News::unread($request->user())->paginate(10)->through($mapper);
            $data['canCreate'] = (bool) ($request->user()->admin ?? false);
        } else {
            $data['canCreate'] = false;
        }

        return Inertia::render('News/Index', $data);
    }

    public function markAsRead(Request $request, News $news): RedirectResponse
    {
        $news->markAsRead($request->user());

        return redirect()->route('news.index');
    }

    public function create(): Response
    {
        return Inertia::render('News/Create', [
            //
        ]);
    }

    public function store(EditNews $request): RedirectResponse
    {
        $news = new News();
        $news->fill($request->all());

        if ( ! $news->save()) {
            Session::flash('alert-danger', trans('flash_message.announcement.not_created'));

            return redirect()->route('news.create');
        }

        Session::flash('alert-success', trans('flash_message.announcement.created'));

        return redirect()->route('news.show', [$news->slug]);
    }

    public function show(News $news): Response
    {
        $user = auth()->user();

        return Inertia::render('News/Show', [
            'news' => [
                'id' => $news->id,
                'slug' => $news->slug,
                'title' => $news->title,
                'body' => $news->body,
                'parsed_body' => $news->parsedBody,
                'expired_at' => $news->expired_at,
                'pinned_at' => $news->pinned_at,
                'is_pinned' => $news->isPinned(),
            ],
            'can' => [
                'edit' => (bool) ($user->admin ?? false),
                'delete' => (bool) ($user->admin ?? false),
            ],
            'isAuthenticated' => auth()->check(),
        ]);
    }

    public function edit(News $news): Response
    {
        return Inertia::render('News/Edit', [
            'news' => [
                'id' => $news->id,
                'slug' => $news->slug,
                'title' => $news->title,
                'body' => $news->body,
                'expired_at' => $news->expired_at,
                'pinned_at' => $news->pinned_at,
            ],
        ]);
    }

    public function update(EditNews $request, News $news): RedirectResponse
    {
        $news->fill($request->all());

        if ( ! $news->save()) {
            Session::flash('alert-danger', trans('flash_message.announcement.not_updated'));

            return redirect()->route('news.edit', [$news->slug]);
        }

        Session::flash('alert-success', trans('flash_message.announcement.updated'));

        return redirect()->route('news.show', [$news->slug]);
    }

    public function destroy(DeleteNews $request, News $news): RedirectResponse
    {
        if ( ! $news->delete()) {
            Session::flash('alert-danger', trans('flash_message.announcement.not_deleted'));

            return redirect()->route('news.delete', [$news->slug]);
        }

        Session::flash('alert-success', trans('flash_message.announcement.deleted'));

        return redirect()->route('news.index');
    }

    public function delete(News $news): Response
    {
        return Inertia::render('News/Delete', [
            'news' => [
                'id' => $news->id,
                'slug' => $news->slug,
                'title' => $news->title,
            ],
        ]);
    }
}
