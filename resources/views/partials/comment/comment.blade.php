<li class="media">
    <img class="mr-3" src="{{ asset('storage/' . $comment->owner->image) }}" width="50" height="50" alt="Image">
    <div class="media-body">
        <strong class="mt-0">{{ $comment->owner->name }}</strong>:

        <span class="float-right">
            <a href="{{ route('comments.edit', [$contact->slug, $comment->id]) }}">
                {{ trans('ui.edit_comment') }}
            </a>
        </span>
        <div class="clearfix"></div>

        {!! nl2br(e($comment->comment)) !!}

        @include ('partials.comment.create', ['parentId' => $comment->id])

        @if (isset($comments[$comment->id]))
            @include ('partials.comment.list', ['collection' => $comments[$comment->id]])
        @endif
    </div>
</li>
