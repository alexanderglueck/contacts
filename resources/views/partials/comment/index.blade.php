@if(count($comments)>0)


    <div class="card">
        <div class="card-header">
            {{ trans('ui.comments') }}
        </div>
        <ul class="list-group list-group-flush">
            @foreach($comments as $comment)
                <li class="list-group-item">
                    {{ $comment->comment }}<br>
                    <a href="{{ route('comments.edit', [$contact->slug, $comment->id]) }}">
                        {{ trans('ui.edit_comment') }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>

@else
    <p>{{ trans('ui.no_comments') }}</p>
@endif
