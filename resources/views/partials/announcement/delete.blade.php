<form class="form-horizontal" role="form" method="POST" action="{{ route('announcements.destroy', [$announcement->slug]) }}">
    <input type="hidden" name="_method" value="DELETE">
    {{ csrf_field() }}
    
    <div class="form-group">
        <div class="col-md-8 col-md-offset-4">
            <button type="submit" class="btn btn-danger">
                {{ trans('ui.delete_announcement') }}
            </button>
        </div>
    </div>
</form>
