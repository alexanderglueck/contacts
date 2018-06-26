@csrf

{!! \App\Helpers\Form::text('title', trans('ui.title'), $news->title) !!}

{!! \App\Helpers\Form::textarea('body', trans('ui.body'), $news->body) !!}

{!! \App\Helpers\Form::text('expired_at', trans('ui.expires_at'), $news->expired_at) !!}

<div class="form-check {{ $errors->has('pinned_at') ? ' has-error' : '' }}">
    <input class="form-check-input"
           type="checkbox"
           value="{{ date('Y-m-d H:i:s') }}"
           id="pinned_at"
           name="pinned_at"
           {{ trim(old('pinned_at', $news->pinned_at)) !== '' ? ' checked ' : '' }}
    >
    <label class="form-check-label" for="pinned_at">
        {{ trans('ui.pinned') }}
    </label>
    @if ($errors->has('pinned_at'))
        <span class="help-block">
            <strong>{{ $errors->first('pinned_at') }}</strong>
        </span>
    @endif
</div>


<div class="form-group">
    <div class="col-md-8 col-md-offset-4">
        <button type="submit" class="btn btn-primary">
            {{ isset($createButtonText) ? $createButtonText : trans('ui.create_news') }}
        </button>
    </div>
</div>
