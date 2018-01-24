<form class="form-horizontal" role="form" method="POST" action="{{ route('contact_dates.destroy', [$contact->slug, $contactDate->slug]) }}">
    <input type="hidden" name="_method" value="DELETE">
    {{ csrf_field() }}

    <div class="form-group">
        <div class="col-md-8 col-md-offset-4">
            <button type="submit" class="btn btn-danger">
                Datum löschen
            </button>

        </div>
    </div>

</form>