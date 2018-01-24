
    {{ csrf_field() }}

    <div class="form-group{{ $errors->has('salutation') ? ' has-error' : '' }}">
        <label for="salutation" class="col-md-4 control-label">Anrede</label>

        <div class="col-md-6">
            <input id="salutation" type="text" class="form-control" name="salutation" value="{{ old('salutation', $contact->salutation) }}" autofocus>

            @if ($errors->has('salutation'))
                <span class="help-block">
                    <strong>{{ $errors->first('salutation') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
        <label for="title" class="col-md-4 control-label">Titel</label>

        <div class="col-md-6">
            <input id="title" type="text" class="form-control" name="title" value="{{ old('title', $contact->title) }}">

            @if ($errors->has('title'))
                <span class="help-block">
                    <strong>{{ $errors->first('title') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="form-group{{ $errors->has('firstname') ? ' has-error' : '' }}">
        <label for="firstname" class="col-md-4 control-label">Vorname<span class="required">*</span></label>

        <div class="col-md-6">
            <input id="firstname" type="text" class="form-control" name="firstname" value="{{ old('firstname', $contact->firstname) }}" required>

            @if ($errors->has('firstname'))
                <span class="help-block">
                    <strong>{{ $errors->first('firstname') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="form-group{{ $errors->has('lastname') ? ' has-error' : '' }}">
        <label for="lastname" class="col-md-4 control-label">Nachname<span class="required">*</span></label>

        <div class="col-md-6">
            <input id="lastname" type="text" class="form-control" name="lastname" value="{{ old('lastname', $contact->lastname) }}" required>

            @if ($errors->has('lastname'))
                <span class="help-block">
                    <strong>{{ $errors->first('lastname') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="form-group{{ $errors->has('title_after') ? ' has-error' : '' }}">
        <label for="title_after" class="col-md-4 control-label">Titel (nachgestellt)</label>

        <div class="col-md-6">
            <input id="title_after" type="text" class="form-control" name="title_after" value="{{ old('title_after', $contact->title_after) }}">

            @if ($errors->has('title_after'))
                <span class="help-block">
                    <strong>{{ $errors->first('title_after') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="form-group{{ $errors->has('company') ? ' has-error' : '' }}">
        <label for="company" class="col-md-4 control-label">Firma</label>

        <div class="col-md-6">
            <input id="company" type="text" class="form-control" name="company" value="{{ old('company', $contact->company) }}">

            @if ($errors->has('company'))
                <span class="help-block">
                    <strong>{{ $errors->first('company') }}</strong>
                </span>
            @endif
        </div>
    </div>
    <div class="form-group{{ $errors->has('department') ? ' has-error' : '' }}">
        <label for="department" class="col-md-4 control-label">Abteilung</label>

        <div class="col-md-6">
            <input id="department" type="text" class="form-control" name="department" value="{{ old('department', $contact->department) }}">

            @if ($errors->has('department'))
                <span class="help-block">
                    <strong>{{ $errors->first('department') }}</strong>
                </span>
            @endif
        </div>
    </div>
    <div class="form-group{{ $errors->has('job') ? ' has-error' : '' }}">
        <label for="job" class="col-md-4 control-label">Beruf</label>

        <div class="col-md-6">
            <input id="job" type="text" class="form-control" name="job" value="{{ old('job', $contact->job) }}">

            @if ($errors->has('job'))
                <span class="help-block">
                    <strong>{{ $errors->first('job') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="form-group{{ $errors->has('gender') ? ' has-error' : '' }}">
        <label for="gender_id" class="col-md-4 control-label">Geschlecht<span class="required">*</span></label>

        <div class="col-md-6">
            <select name="gender_id" id="gender_id" class="form-control">
                @foreach($genders as $gender)
                    <option {{ old('gender_id', $contact->gender_id) === $gender->id ? 'selected' : '' }} value="{{$gender->id}}">{{$gender->gender}}</option>
                @endforeach
            </select>

            @if ($errors->has('gender'))
                <span class="help-block">
                    <strong>{{ $errors->first('gender') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="form-group{{ $errors->has('nickname') ? ' has-error' : '' }}">
        <label for="nickname" class="col-md-4 control-label">Spitzname</label>

        <div class="col-md-6">
            <input id="nickname" type="text" class="form-control" name="nickname" value="{{ old('nickname', $contact->nickname) }}">

            @if ($errors->has('nickname'))
                <span class="help-block">
                    <strong>{{ $errors->first('nickname') }}</strong>
                </span>
            @endif
        </div>
    </div>


    <div class="form-group{{ $errors->has('contact_group_id') ? ' has-error' : '' }}">
        <label for="contact_group_id" class="col-md-4 control-label">Kontaktgruppe</label>

        <div class="col-md-6">
            <select multiple id="contact_group_id" name="contact_group_id[]" class="form-control">
                @foreach($contactGroups as $contactGroup)
                  <option {{ in_array($contactGroup->id, old("contact_group_id", $contact->contactGroups->pluck("id")->toArray())) ? "selected":"" }} value="{{$contactGroup->id}}">{{$contactGroup->name}}</option>
                @endforeach
            </select>

            @if ($errors->has('contact_group_id'))
                <span class="help-block">
                    <strong>{{ $errors->first('contact_group_id') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="form-group {{ $errors->has('active') ? ' has-error' : '' }}">
        <div class="col-sm-offset-4 col-sm-8">

            <label class="radio-inline">
                <input type="radio" name="active" id="active-on-radio" {{ (old('active', $contact->active) == 1) ? ' checked ' : " " }} value="1"> Aktiv
            </label>
            <label class="radio-inline">
                <input type="radio" name="active" id="active-off-radio" {{ (old('active', $contact->active) == 1) ? '  ' : " checked " }} value="0"> Inaktiv
            </label>

            @if ($errors->has('active'))
                <span class="help-block">
                    <strong>{{ $errors->first('active') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-8 col-md-offset-4">
            <button type="submit" class="btn btn-primary">
                {{ isset($createButtonText) ? $createButtonText : "Kontakt erstellen" }}
            </button>

        </div>
    </div>
