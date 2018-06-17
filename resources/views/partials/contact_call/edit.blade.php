@csrf

{!! \App\Helpers\Form::text('called_at', 'Called at', $contactCall->formatted_called_at, true, true) !!}

{!! \App\Helpers\Form::textarea('note', 'Note', $contactCall->note) !!}


<div class="form-group">
    <div class="col-md-8 col-md-offset-4">
        <button type="submit" class="btn btn-primary">
            {{ isset($createButtonText) ? $createButtonText : 'Create call' }}
        </button>
    </div>
</div>
