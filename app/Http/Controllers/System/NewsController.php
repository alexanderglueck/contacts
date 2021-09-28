<?php

namespace App\Http\Controllers\System;

use App\Models\System\News;
use App\Http\Controllers\Controller;
use App\Http\Requests\News\EditNews;
use App\Http\Requests\News\DeleteNews;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class NewsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request): View
    {
        if (auth()->check()) {
            return view('news.index', [
                'active' => News::active()->paginate(10),
                'inactive' => News::inactive()->paginate(10),
                'displayed' => News::displayed($request->user())->paginate(10),
                'hidden' => News::hidden($request->user())->paginate(10),
                'read' => News::read($request->user())->paginate(10),
                'unread' => News::unread($request->user())->paginate(10)
            ]);
        }

        return view('news.index', [
            'active' => News::active()->paginate(10),
            'inactive' => News::inactive()->paginate(10)
        ]);
    }

    public function markAsRead(Request $request, News $news): RedirectResponse
    {
        $news->markAsRead($request->user());

        return redirect()->route('news.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('news.create', [
            'news' => new News
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EditNews $request): RedirectResponse
    {
        $announcement = new News();
        $announcement->fill($request->all());

        if ( ! $announcement->save()) {
            Session::flash('alert-danger', trans('flash_message.announcement.not_created'));

            return redirect()->route('news.create');
        }

        Session::flash('alert-success', trans('flash_message.announcement.created'));

        return redirect()->route('news.show', [$announcement->slug]);
    }

    /**
     * Display the specified resource.
     */
    public function show(News $news): View
    {
        return view('news.show', [
            'news' => $news
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(News $news): View
    {
        return view('news.edit', [
            'news' => $news,
            'createButtonText' => trans('ui.edit_news'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeleteNews $request, News $news): RedirectResponse
    {
        if ( ! $news->delete()) {
            Session::flash('alert-danger', trans('flash_message.announcement.not_deleted'));

            return redirect()->route('news.delete', [$news->slug]);
        }

        Session::flash('alert-success', trans('flash_message.announcement.deleted'));

        return redirect()->route('news.index');
    }

    /**
     * Show the form for deleting the specified resource.
     */
    public function delete(News $news): View
    {
        return view('news.delete', [
            'news' => $news
        ]);
    }
}
