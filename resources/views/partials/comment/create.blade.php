<div class="card card-default">
    <div class="card-header">
        {{ trans('ui.create_comment') }}
    </div>
    <div class="card-body">
        <form class="form-horizontal" role="form" method="POST" action="{{ route('comments.store', [$contact->slug]) }}">
            @csrf

            {!! \App\Helpers\Form::textarea('comment', trans('ui.comment'), $comment->comment, true, false) !!}

            <div class="form-group">
                <div class="col-md-8 col-md-offset-4">
                    <button type="submit" class="btn btn-primary">
                        {{ trans('ui.create_comment') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
