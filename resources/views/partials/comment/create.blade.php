<form class="form-horizontal" role="form" method="POST" action="{{ route('comments.store', [$contact->slug]) }}">
    @csrf

    @if (isset($parentId))
        <input name="parent_id" type="hidden" value="{{ $parentId }}"/>
    @endif

    {!! \App\Helpers\Form::textarea('comment', trans('ui.comment'), $newComment->comment, true, false) !!}

    <div class="form-group">
        <div class="col-md-8 col-md-offset-4">
            <button type="submit" class="btn btn-primary">
                {{ trans('ui.create_comment') }}
            </button>
        </div>
    </div>
</form>
