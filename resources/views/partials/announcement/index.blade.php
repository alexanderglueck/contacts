@if(count($announcements)>0)

    {{ $announcements->links() }}

    <div class="list-group">
        @foreach($announcements as $announcement)
            <a class="list-group-item  list-group-item-action" href="{{ route('announcements.show', [$announcement->slug]) }}">
                {{ $announcement->title }}
            </a>
        @endforeach
    </div>

    {{ $announcements->links() }}

@else
    <p>{{ trans('ui.no_announcements') }}</p>
@endif
