
    @csrf


    <div class="form-group{{ $errors->has('name') ? ' has-danger' : '' }}">
        <label for="name" class="col-md-4 form-control-label">Name<span class="required">*</span></label>

        <div class="col-md-6">
            <input id="name" type="text" class="form-control" name="name" value="{{ old('name', $contactEmail->name) }}" autofocus required>

            @if ($errors->has('name'))
                <span class="form-text">
                    <strong>{{ $errors->first('name') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="form-group{{ $errors->has('email') ? ' has-danger' : '' }}">
        <label for="email" class="col-md-4 form-control-label">E-Mail Adresse<span class="required">*</span></label>

        <div class="col-md-6">
            <input id="email" type="email" class="form-control" name="email" value="{{ old('email', $contactEmail->email) }}" required>

            @if ($errors->has('email'))
                <span class="form-text">
                    <strong>{{ $errors->first('email') }}</strong>
                </span>
            @endif
        </div>
    </div>


    <div class="form-group">
        <div class="col-md-8 col-md-offset-4">
            <button type="submit" class="btn btn-primary">
               {{ isset($createButtonText) ? $createButtonText : 'E-Mail Adresse erstellen' }}
            </button>

        </div>
    </div>
