@csrf

{!! \App\Helpers\Form::text('name', trans('ui.name'), $giftIdea->name, true, true) !!}

{!! \App\Helpers\Form::textarea('description', trans('ui.description'), $giftIdea->description) !!}

{!! \App\Helpers\Form::text('url', trans('ui.link'), $giftIdea->url) !!}

{!! \App\Helpers\Form::text('due_at', trans('ui.due_at'), $giftIdea->formatted_due_at) !!}

<div class="form-group">
    <div class="col-md-8 col-md-offset-4">
        <button type="submit" class="btn btn-primary">
            {{ isset($createButtonText) ? $createButtonText : trans('ui.create_gift_idea') }}
        </button>
    </div>
</div>
