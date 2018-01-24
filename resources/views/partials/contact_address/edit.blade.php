
    {{ csrf_field() }}


    <div class="form-group{{ $errors->has('name') ? ' has-danger' : '' }}">
        <label for="name" class="col-md-4 form-control-label">Name<span class="required">*</span></label>

        <div class="col-md-6">
            <input id="name" type="text" class="form-control" name="name" value="{{ old('name', $contactAddress->name) }}" autofocus required>

            @if ($errors->has('name'))
                <span class="form-text">
                    <strong>{{ $errors->first('name') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="form-group{{ $errors->has('street') ? ' has-danger' : '' }}">
        <label for="street" class="col-md-4 form-control-label">Straße<span class="required">*</span></label>

        <div class="col-md-6">
            <input id="street" type="text" class="form-control" name="street" value="{{ old('street', $contactAddress->street) }}" required>

            @if ($errors->has('street'))
                <span class="form-text">
                    <strong>{{ $errors->first('street') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="form-group{{ $errors->has('zip') ? ' has-danger' : '' }}">
        <label for="zip" class="col-md-4 form-control-label">PLZ<span class="required">*</span></label>

        <div class="col-md-6">
            <input id="zip" type="text" class="form-control" name="zip" value="{{ old('zip', $contactAddress->zip) }}" required>

            @if ($errors->has('zip'))
                <span class="form-text">
                    <strong>{{ $errors->first('zip') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="form-group{{ $errors->has('city') ? ' has-danger' : '' }}">
        <label for="city" class="col-md-4 form-control-label">Ort<span class="required">*</span></label>

        <div class="col-md-6">
            <input id="city" type="text" class="form-control" name="city" value="{{ old('city', $contactAddress->city) }}" required>

            @if ($errors->has('city'))
                <span class="form-text">
                    <strong>{{ $errors->first('city') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="form-group{{ $errors->has('state') ? ' has-danger' : '' }}">
        <label for="state" class="col-md-4 form-control-label">Bundesland<span class="required">*</span></label>

        <div class="col-md-6">
            <input id="state" type="text" class="form-control" name="state" value="{{ old('state', $contactAddress->state) }}" required>

            @if ($errors->has('state'))
                <span class="form-text">
                    <strong>{{ $errors->first('state') }}</strong>
                </span>
            @endif
        </div>
    </div>


    <div class="form-group{{ $errors->has('country_id') ? ' has-danger' : '' }}">
        <label for="country_id" class="col-md-4 form-control-label">Land<span class="required">*</span></label>

        <div class="col-md-6">
            <select name="country_id" id="country_id" class="form-control" required>
                @foreach($countries as $country)
                    <option {{ old('country_id', $contactAddress->country_id) === $country->id ? 'selected' : '' }} value="{{$country->id}}">{{$country->country}}</option>
                @endforeach
            </select>

            @if ($errors->has('country_id'))
                <span class="form-text">
                    <strong>{{ $errors->first('country_id') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <input type="hidden" name="latitude" value="{{ $contactAddress->latitude }}" id="latitude" />
    <input type="hidden" name="longitude" value="{{ $contactAddress->longitude }}" id="longitude" />

    <div class="form-group">
        <div class="col-md-8 col-md-offset-4">
            <button type="submit" class="btn btn-primary">
                {{ isset($createButtonText) ? $createButtonText : "Adresse erstellen" }}
            </button>

        </div>
    </div>