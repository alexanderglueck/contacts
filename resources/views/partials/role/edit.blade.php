@csrf

{!! \App\Helpers\Form::text('name', trans('ui.name'), $role->name) !!}

<div class="form-group">
    <div class="col-sm-offset-4 col-sm-8">
        {{ trans('ui.permissions') }}
        @foreach($permissions as $permission)
            <div class="form-check">
                <input {{ ($role->hasPermissionTo($permission->name) ? ' checked ' : ' ' ) }} name="permissions[{{$permission->id}}]" class="form-check-input" type="checkbox" value="{{ $permission->id }}" id="permission-{{$permission->id}}">
                <label class="form-check-label" for="permission-{{$permission->id}}">
                    {{ $permission->name }}
                </label>
            </div>
        @endforeach
    </div>
</div>

<div class="form-group">
    <div class="col-sm-offset-4 col-sm-8">
        {{ trans('ui.users') }}
        @foreach($users as $user)
            <div class="form-check">
                <input {{ (trim($role->name) != '' && $user->hasRole($role->name) ? ' checked ' : ' ' ) }} name="users[{{$user->id}}]" class="form-check-input" type="checkbox" value="{{ $user->id }}" id="user-{{$user->id}}">
                <label class="form-check-label" for="user-{{$user->id}}">
                    {{ $user->name }}
                </label>
            </div>
        @endforeach
    </div>
</div>

<div class="form-group">
    <div class="col-md-8 col-md-offset-4">
        <button type="submit" class="btn btn-primary">
            {{ isset($createButtonText) ? $createButtonText : trans('ui.create_role') }}
        </button>
    </div>
</div>
