<form class="form-horizontal" role="form" method="POST" action="{{ route('contacts.destroy', [$contact->slug]) }}">
    <input type="hidden" name="_method" value="DELETE">
    {{ csrf_field() }}

    <div class="form-group">
        <div class="col-md-8 col-md-offset-4">
            <button type="submit" class="btn btn-danger">
                Kontakt löschen
            </button>

        </div>
    </div>

</form>