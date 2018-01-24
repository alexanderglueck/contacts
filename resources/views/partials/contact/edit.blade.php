{{ csrf_field() }}

<div class="form-group {{ $errors->has('is_company') ? ' has-danger' : '' }}">
    <div class="col-sm-offset-4 col-sm-8">
        <label class="">
            <input type="radio" name="is_company" id="is_company-on-radio" {{ (old('is_company', $contact->is_company) == 1) ? ' checked ' : " " }} value="1">
            {{ trans('ui.company') }}
        </label>
        <label class="">
            <input type="radio" name="is_company" id="is_company-off-radio" {{ (old('is_company', $contact->is_company) == 1) ? '  ' : " checked " }} value="0">
            {{ trans('ui.person') }}
        </label>

        @if ($errors->has('is_company'))
            <span class="form-text">
                <strong>{{ $errors->first('is_company') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('salutation') ? ' has-danger' : '' }}">
    <label for="salutation" class="col-md-4 form-control-label">
        {{ trans('ui.salutation') }}
        <span class="required">*</span>
    </label>

    <div class="col-md-6">
        <input id="salutation" type="text" class="form-control" name="salutation" value="{{ old('salutation', $contact->salutation) }}" autofocus required>

        @if ($errors->has('salutation'))
            <span class="form-text">
                <strong>{{ $errors->first('salutation') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('title') ? ' has-danger' : '' }}">
    <label for="title" class="col-md-4 form-control-label">{{ trans('ui.title') }}</label>

    <div class="col-md-6">
        <input id="title" type="text" class="form-control" name="title" value="{{ old('title', $contact->title) }}">

        @if ($errors->has('title'))
            <span class="form-text">
                <strong>{{ $errors->first('title') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('firstname') ? ' has-danger' : '' }}">
    <label for="firstname" class="col-md-4 form-control-label">{{ trans('ui.firstname') }}
        <span class="required">*</span></label>

    <div class="col-md-6">
        <input id="firstname" type="text" class="form-control" name="firstname" value="{{ old('firstname', $contact->firstname) }}" required>

        @if ($errors->has('firstname'))
            <span class="form-text">
                <strong>{{ $errors->first('firstname') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('lastname') ? ' has-danger' : '' }}">
    <label for="lastname" class="col-md-4 form-control-label">{{ trans('ui.lastname') }}
        <span class="required">*</span></label>

    <div class="col-md-6">
        <input id="lastname" type="text" class="form-control" name="lastname" value="{{ old('lastname', $contact->lastname) }}" required>

        @if ($errors->has('lastname'))
            <span class="form-text">
                    <strong>{{ $errors->first('lastname') }}</strong>
                </span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('title_after') ? ' has-danger' : '' }}">
    <label for="title_after" class="col-md-4 form-control-label">{{ trans('ui.title_after') }}</label>

    <div class="col-md-6">
        <input id="title_after" type="text" class="form-control" name="title_after" value="{{ old('title_after', $contact->title_after) }}">

        @if ($errors->has('title_after'))
            <span class="form-text">
                    <strong>{{ $errors->first('title_after') }}</strong>
                </span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('date_of_birth') ? ' has-danger' : '' }}">
    <label for="date_of_birth" class="col-md-4 form-control-label">{{ trans('ui.date_of_birth') }}</label>

    <div class="col-md-6">
        <input id="date_of_birth" type="text" class="form-control" name="date_of_birth" value="{{ old('date_of_birth', $contact->formatted_date_of_birth) }}">

        @if ($errors->has('date_of_birth'))
            <span class="form-text">
                <strong>{{ $errors->first('date_of_birth') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('company') ? ' has-danger' : '' }}">
    <label for="company" class="col-md-4 form-control-label">{{ trans('ui.company') }}</label>

    <div class="col-md-6">
        <input id="company" type="text" class="form-control" name="company" value="{{ old('company', $contact->company) }}">

        @if ($errors->has('company'))
            <span class="form-text">
                    <strong>{{ $errors->first('company') }}</strong>
                </span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('vatin') ? ' has-danger' : '' }}">
    <label for="vatin" class="col-md-4 form-control-label">{{ trans('ui.vatin') }}</label>

    <div class="col-md-6">
        <input id="vatin" type="text" class="form-control" name="vatin" value="{{ old('vatin', $contact->vatin) }}">

        @if ($errors->has('vatin'))
            <span class="form-text">
                <strong>{{ $errors->first('vatin') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('department') ? ' has-danger' : '' }}">
    <label for="department" class="col-md-4 form-control-label">{{ trans('ui.department') }}</label>

    <div class="col-md-6">
        <input id="department" type="text" class="form-control" name="department" value="{{ old('department', $contact->department) }}">

        @if ($errors->has('department'))
            <span class="form-text">
                    <strong>{{ $errors->first('department') }}</strong>
                </span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('job') ? ' has-danger' : '' }}">
    <label for="job" class="col-md-4 form-control-label">{{ trans('ui.job') }}</label>

    <div class="col-md-6">
        <input id="job" type="text" class="form-control" name="job" value="{{ old('job', $contact->job) }}">

        @if ($errors->has('job'))
            <span class="form-text">
                    <strong>{{ $errors->first('job') }}</strong>
                </span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('iban') ? ' has-danger' : '' }}">
    <label for="iban" class="col-md-4 form-control-label">{{ trans('ui.iban') }}</label>

    <div class="col-md-6">
        <input id="iban" type="text" class="form-control" name="iban" value="{{ old('iban', $contact->iban) }}">

        @if ($errors->has('iban'))
            <span class="form-text">
                <strong>{{ $errors->first('iban') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('gender') ? ' has-danger' : '' }}">
    <label for="gender_id" class="col-md-4 form-control-label">{{ trans('ui.gender') }}
        <span class="required">*</span></label>

    <div class="col-md-6">
        <select name="gender_id" id="gender_id" class="form-control">
            @foreach($genders as $gender)
                <option {{ old('gender_id', $contact->gender_id) === $gender->id ? 'selected' : '' }} value="{{$gender->id}}">{{ trans('ui.gender.' . $gender->gender) }}</option>
            @endforeach
        </select>

        @if ($errors->has('gender'))
            <span class="form-text">
                    <strong>{{ $errors->first('gender') }}</strong>
                </span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('nickname') ? ' has-danger' : '' }}">
    <label for="nickname" class="col-md-4 form-control-label">{{ trans('ui.nickname') }}</label>

    <div class="col-md-6">
        <input id="nickname" type="text" class="form-control" name="nickname" value="{{ old('nickname', $contact->nickname) }}">

        @if ($errors->has('nickname'))
            <span class="form-text">
                    <strong>{{ $errors->first('nickname') }}</strong>
                </span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('nickname') ? ' has-danger' : '' }}">
    <label for="custom_id" class="col-md-4 form-control-label">{{ trans('ui.custom_id') }}</label>

    <div class="col-md-6">
        <input id="custom_id" type="text" class="form-control" name="custom_id" value="{{ old('custom_id', $contact->custom_id) }}">

        @if ($errors->has('custom_id'))
            <span class="form-text">
                <strong>{{ $errors->first('custom_id') }}</strong>
            </span>
        @endif
    </div>
</div>


<div class="form-group{{ $errors->has('contact_groups') ? ' has-danger' : '' }}">
    <label for="contact_groups" class="col-md-4 form-control-label">{{ trans('ui.contact_groups') }}</label>

    <div class="col-md-6">
        <select multiple id="contact_groups" name="contact_groups[]" class="form-control">
            @foreach($contactGroups as $contactGroup)
                <option {{ in_array($contactGroup->id, old("contact_groups", $contact->contactGroups->pluck("id")->toArray())) ? "selected":"" }} value="{{$contactGroup->id}}">{{$contactGroup->name}}</option>
            @endforeach
        </select>

        @if ($errors->has('contact_groups'))
            <span class="form-text">
                    <strong>{{ $errors->first('contact_groups') }}</strong>
                </span>
        @endif
    </div>
</div>

<div class="form-group {{ $errors->has('active') ? ' has-danger' : '' }}">
    <div class="col-sm-offset-4 col-sm-8">

        <label class="">
            <input type="radio" name="active" id="active-on-radio" {{ (old('active', $contact->active) == 1) ? ' checked ' : " " }} value="1">
            {{ trans('ui.active') }}
        </label>
        <label class="">
            <input type="radio" name="active" id="active-off-radio" {{ (old('active', $contact->active) == 1) ? '  ' : " checked " }} value="0">
            {{ trans('ui.inactive') }}
        </label>

        @if ($errors->has('active'))
            <span class="form-text">
                    <strong>{{ $errors->first('active') }}</strong>
                </span>
        @endif
    </div>
</div>

<div class="form-group">
    <div class="col-md-8 col-md-offset-4">
        <button type="submit" class="btn btn-primary">
            {{ isset($createButtonText) ? $createButtonText : trans('ui.create_contact') }}
        </button>

    </div>
</div>
