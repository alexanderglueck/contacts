@if(count($news)>0)

    {{ $news->links() }}

    <div class="list-group">
        @foreach($news as $newsItem)
            <a class="list-group-item  list-group-item-action" href="{{ route('news.show', [$newsItem->slug]) }}">
                {{ $newsItem->title }}
                @if ($newsItem->isPinned())
                    <span class="float-right">
                        Pinned!
                    </span>
                @endif
            </a>
        @endforeach
    </div>

    {{ $news->links() }}

@else
    <p>{{ trans('ui.no_news') }}</p>
@endif
