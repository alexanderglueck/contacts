<form class="form-horizontal" role="form" method="POST" action="{{ route('contact_numbers.destroy', [$contact->slug, $contactNumber->slug]) }}">
    @method('DELETE')
    @csrf

    <div class="form-group">
        <div class="col-md-8 col-md-offset-4">
            <button type="submit" class="btn btn-danger">
                Nummer löschen
            </button>

        </div>
    </div>

</form>
