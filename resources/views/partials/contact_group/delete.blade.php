<form class="form-horizontal" role="form" method="POST" action="{{ route('contact_groups.destroy', $contactGroup->slug) }}">
    @method('DELETE')
    @csrf

    <div class="form-group">
        <div class="col-md-8 col-md-offset-4">
            <button type="submit" class="btn btn-danger">
                Kontaktgruppe löschen
            </button>

        </div>
    </div>

</form>
