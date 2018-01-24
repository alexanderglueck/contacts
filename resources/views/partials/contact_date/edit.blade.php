
    {{ csrf_field() }}


    <div class="form-group{{ $errors->has('name') ? ' has-danger' : '' }}">
        <label for="name" class="col-md-4 form-control-label">Name<span class="required">*</span></label>

        <div class="col-md-6">
            <input id="name" type="text" class="form-control" name="name" value="{{ old('name', $contactDate->name) }}" autofocus required>

            @if ($errors->has('name'))
                <span class="form-text">
                    <strong>{{ $errors->first('name') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="form-group{{ $errors->has('date') ? ' has-danger' : '' }}">
        <label for="date" class="col-md-4 form-control-label">Datum<span class="required">*</span></label>

        <div class="col-md-6">
            <input id="date" type="text" class="form-control" name="date" value="{{ old('date', $contactDate->formatted_date) }}" required>

            @if ($errors->has('date'))
                <span class="form-text">
                    <strong>{{ $errors->first('date') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="form-group {{ $errors->has('skip_year') ? ' has-danger' : '' }}">
        <div class="col-sm-offset-4 col-sm-8">

            <label class="">
                <input type="radio" name="skip_year" id="skip-year-radio" {{ (old('skip_year', $contactDate->skip_year) == 0) ? '  ' : " checked " }} value="1"> Jahr
                ignorieren
            </label>
            <label class="">
                <input type="radio" name="skip_year" id="dont-skip-year-radio" {{ (old('skip_year', $contactDate->skip_year) == 0) ? ' checked ' : "  " }} value="0"> Jahr
                nicht ignorieren
            </label>

            @if ($errors->has('skip_year'))
                <span class="form-text">
                    <strong>{{ $errors->first('skip_year') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-8 col-md-offset-4">
            <button type="submit" class="btn btn-primary">
                {{ isset($createButtonText) ? $createButtonText : 'Datum erstellen' }}
            </button>

        </div>
    </div>