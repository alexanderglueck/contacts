
    @csrf


    <div class="form-group{{ $errors->has('name') ? ' has-danger' : '' }}">
        <label for="name" class="col-md-4 form-control-label">Name<span class="required">*</span></label>

        <div class="col-md-6">
            <input id="name" type="text" class="form-control" name="name" value="{{ old('name', $contactUrl->name) }}" autofocus required>

            @if ($errors->has('name'))
                <span class="form-text">
                    <strong>{{ $errors->first('name') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="form-group{{ $errors->has('url') ? ' has-danger' : '' }}">
        <label for="url" class="col-md-4 form-control-label">Website<span class="required">*</span></label>

        <div class="col-md-6">
            <input id="url" type="url" class="form-control" name="url" value="{{ old('url', $contactUrl->url) }}" required>

            @if ($errors->has('url'))
                <span class="form-text">
                    <strong>{{ $errors->first('url') }}</strong>
                </span>
            @endif
        </div>
    </div>


    <div class="form-group">
        <div class="col-md-8 col-md-offset-4">
            <button type="submit" class="btn btn-primary">
                {{ isset($createButtonText) ? $createButtonText : 'Website erstellen' }}
            </button>

        </div>
    </div>
