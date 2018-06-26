<form class="form-horizontal" role="form" method="POST" action="{{ route('news.destroy', [$news->slug]) }}">
    @method('DELETE')
    @csrf

    <div class="form-group">
        <div class="col-md-8 col-md-offset-4">
            <button type="submit" class="btn btn-danger">
                {{ trans('ui.delete_news') }}
            </button>
        </div>
    </div>
</form>
