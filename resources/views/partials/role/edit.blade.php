@csrf

{!! \App\Helpers\Form::text('name', trans('ui.name'), $role->name) !!}

<div class="form-group">
    <div class="col-md-8 col-md-offset-4">
        <button type="submit" class="btn btn-primary">
            {{ isset($createButtonText) ? $createButtonText : trans('ui.create_role') }}
        </button>
    </div>
</div>
