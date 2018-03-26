
    @csrf


    <div class="form-group{{ $errors->has('name') ? ' has-danger' : '' }}">
        <label for="name" class="col-md-4 form-control-label">Name<span class="required">*</span></label>

        <div class="col-md-6">
            <input id="name" type="text" class="form-control" name="name" value="{{ old('name', $contactGroup->name) }}" autofocus required>

            @if ($errors->has('name'))
                <span class="form-text">
                    <strong>{{ $errors->first('name') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="form-group{{ $errors->has('parent_id') ? ' has-danger' : '' }}">
        <label for="parent_id" class="col-md-4 form-control-label">Übergeordnete Kontaktgruppe<span class="required">*</span></label>

        <div class="col-md-6">
            <select name="parent_id" id="country_id" class="form-control" >
                <option value="">Keine Kontaktgruppe</option>
                @foreach($contactGroups as $group)
                    @if($group->id !== $contactGroup->id)
                        <option {{ old('parent_id', $contactGroup->parent_id) == $group->id ? 'selected' : '' }} value="{{$group->id}}">{{$group->name}}</option>
                    @endif
                @endforeach
            </select>

            @if ($errors->has('parent_id'))
                <span class="form-text">
                    <strong>{{ $errors->first('parent_id') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-8 col-md-offset-4">
            <button type="submit" class="btn btn-primary">
                {{ isset($createButtonText) ? $createButtonText : 'Kontaktgruppe erstellen' }}
            </button>
        </div>
    </div>
