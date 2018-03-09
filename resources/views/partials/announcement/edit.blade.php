{{ csrf_field() }}

{!! \App\Helpers\Form::text('title', trans('ui.title'), $announcement->title) !!}

{!! \App\Helpers\Form::textarea('body', trans('ui.body'), $announcement->body) !!}

<div class="form-group">
    <div class="col-md-8 col-md-offset-4">
        <button type="submit" class="btn btn-primary">
            {{ isset($createButtonText) ? $createButtonText : trans('ui.create_announcement') }}
        </button>
    </div>
</div>
