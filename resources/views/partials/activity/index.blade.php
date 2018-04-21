@if(count($activities) > 0)

    {{ $activities->links() }}

    <div class="list-group">
        @foreach($activities as $activity)
            <span class="list-group-item ">
                {{ $activity->user->name }} {{ trans('activity.' . $activity->action) }} {{ (new \Carbon\Carbon($activity->created_at))->diffForHumans() }}
                <br>
                <pre><code>{{ $activity->body }}</code></pre>
            </span>
        @endforeach
    </div>

    {{ $activities->links() }}

@else
    <p>{{ trans('ui.no_activities') }}</p>
@endif
