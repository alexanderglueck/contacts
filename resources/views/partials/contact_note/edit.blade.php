@csrf

{!! \App\Helpers\Form::text('name', 'Name', $contactNote->name, true, true) !!}

{!! \App\Helpers\Form::textarea('note', 'Note', $contactNote->note, true, false) !!}

<div class="form-group">
    <div class="col-md-8 col-md-offset-4">
        <button type="submit" class="btn btn-primary">
            {{ isset($createButtonText) ? $createButtonText : 'Add note' }}
        </button>

    </div>
</div>
