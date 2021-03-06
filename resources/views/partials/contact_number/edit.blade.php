
    @csrf

    <div class="form-group{{ $errors->has('name') ? ' has-danger' : '' }}">
        <label for="name" class="col-md-4 form-control-label">Name<span class="required">*</span></label>

        <div class="col-md-6">
            <input id="name" type="text" class="form-control" name="name" value="{{ old('name', $contactNumber->name) }}" autofocus required>

            @if ($errors->has('name'))
                <span class="form-text">
                    <strong>{{ $errors->first('name') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="form-group{{ $errors->has('number') ? ' has-danger' : '' }}">
        <label for="number" class="col-md-4 form-control-label">Nummer<span class="required">*</span></label>

        <div class="col-md-6">
            <input id="number" type="tel" class="form-control" name="number" value="{{ old('number', $contactNumber->number) }}" required>

            @if ($errors->has('number'))
                <span class="form-text">
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
