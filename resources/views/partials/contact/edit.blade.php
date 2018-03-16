@csrf

{!! \App\Helpers\Form::text('salutation', trans('ui.salutation'), $contact->salutation, true, true) !!}

{!! \App\Helpers\Form::text('title', trans('ui.title'), $contact->title) !!}

{!! \App\Helpers\Form::text('firstname', trans('ui.firstname'), $contact->firstname, true) !!}

{!! \App\Helpers\Form::text('lastname', trans('ui.lastname'), $contact->lastname, true) !!}

{!! \App\Helpers\Form::text('title_after', trans('ui.title_after'), $contact->title_after) !!}

{!! \App\Helpers\Form::text('date_of_birth', trans('ui.date_of_birth'), $contact->formatted_date_of_birth) !!}

{!! \App\Helpers\Form::text('company', trans('ui.company'), $contact->company, false, false) !!}

{!! \App\Helpers\Form::text('vatin', trans('ui.vatin'), $contact->vatin) !!}

{!! \App\Helpers\Form::text('department', trans('ui.department'), $contact->department) !!}

{!! \App\Helpers\Form::text('job', trans('ui.job'), $contact->job) !!}

{!! \App\Helpers\Form::text('iban', trans('ui.iban'), $contact->iban) !!}


<div class="form-group{{ $errors->has('gender') ? ' has-danger' : '' }}">
    <label for="gender_id" class="col-md-4 form-control-label">{{ trans('ui.gender') }}
        <span class="required">*</span></label>

    <div class="col-md-6">
        <select name="gender_id" id="gender_id" class="form-control">
            @foreach($genders as $gender)
                <option {{ old('gender_id', $contact->gender_id) == $gender->id ? 'selected' : '' }} value="{{$gender->id}}">{{ trans('ui.gender.' . $gender->gender) }}</option>
            @endforeach
        </select>

        @if ($errors->has('gender'))
            <span class="form-text">
                <strong>{{ $errors->first('gender') }}</strong>
            </span>
        @endif
    </div>
</div>

{!! \App\Helpers\Form::text('nickname', trans('ui.nickname'), $contact->nickname) !!}

{!! \App\Helpers\Form::text('custom_id', trans('ui.custom_id'), $contact->custom_id) !!}

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

{!! \App\Helpers\Form::textarea('first_met', trans('ui.first_met'), $contact->first_met) !!}

{!! \App\Helpers\Form::textarea('note', trans('ui.note'), $contact->note) !!}

{!! \App\Helpers\Form::text('died_at', trans('ui.died_at'), $contact->formatted_died_at) !!}

{!! \App\Helpers\Form::text('died_from', trans('ui.died_from'), $contact->died_from) !!}

<div class="form-group{{ $errors->has('nationality_id') ? ' has-danger' : '' }}">
    <label for="nationality_id" class="col-md-4 form-control-label">{{ trans('ui.nationality') }}</label>

    <div class="col-md-6">
        <select name="nationality_id" id="nationality_id" class="form-control">
            <option></option>
            @foreach($countries as $country)
                <option {{ old('nationality_id', $contact->nationality_id) == $country->id ? 'selected' : '' }} value="{{$country->id}}">{{$country->country}}</option>
            @endforeach
        </select>

        @if ($errors->has('nationality_id'))
            <span class="form-text">
                <strong>{{ $errors->first('nationality_id') }}</strong>
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
