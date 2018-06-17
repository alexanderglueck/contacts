<form class="form-horizontal" role="form" method="POST" action="{{ route('contact_calls.destroy', [$contact->slug, $contactCall->id]) }}">
    @method('DELETE')
    @csrf

    <div class="form-group">
        <div class="col-md-8 col-md-offset-4">
            <button type="submit" class="btn btn-danger">
                Delete call
            </button>
        </div>
    </div>
</form>
