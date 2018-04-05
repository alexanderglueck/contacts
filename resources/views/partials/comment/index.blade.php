<div class="card">
    <div class="card-header">
        {{ trans('ui.comments') }}
    </div>
    <div class="card-body">
        @if (count($comments)>0)
            @include('partials.comment.list', ['collection' => $comments['root']])
        @else
            <p>{{ trans('ui.no_comments') }}</p>
        @endif

        @if (Auth::user()->hasPermissionTo('create comments'))
            <hr>

            @include('partials.comment.create')
        @endif
    </div>
</div>
