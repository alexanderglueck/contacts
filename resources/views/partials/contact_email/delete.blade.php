<form class="form-horizontal" role="form" method="POST" action="{{ route('contact_emails.destroy', [$contact->slug, $contactEmail->slug]) }}">
    <input type="hidden" name="_method" value="DELETE">
    {{ csrf_field() }}

    <div class="form-group">
        <div class="col-md-8 col-md-offset-4">
            <button type="submit" class="btn btn-danger">
                E-Mail Adresse löschen
            </button>

        </div>
    </div>

</form>