
    {{ csrf_field() }}

    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
        <label for="name" class="col-md-4 control-label">Name<span class="required">*</span></label>

        <div class="col-md-6">
            <input id="name" type="text" class="form-control" name="name" value="{{ old('name', $contactNumber->name) }}" autofocus required>

            @if ($errors->has('name'))
                <span class="help-block">
                    <strong>{{ $errors->first('name') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="form-group{{ $errors->has('number') ? ' has-error' : '' }}">
        <label for="number" class="col-md-4 control-label">Nummer<span class="required">*</span></label>

        <div class="col-md-6">
            <input id="number" type="text" class="form-control" name="number" value="{{ old('number', $contactNumber->number) }}" required>

            @if ($errors->has('number'))
                <span class="help-block">
                    <strong>{{ $errors->first('number') }}</strong>
                </span>
            @endif
        </div>
    </div>


    <div class="form-group">
        <div class="col-md-8 col-md-offset-4">
            <button type="submit" class="btn btn-primary">
                {{ isset($createButtonText) ? $createButtonText : 'Nummer erstellen' }}
            </button>

        </div>
    </div>