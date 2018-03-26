<div class="form-group{{ $errors->has($fieldName) ? ' has-error' : '' }}">
    <label for="{!! $fieldName !!}" class="col-md-4 control-label {{ $required }}">
        {{ $label }}
    </label>

    <div class="col-md-6">
        <input id="{!! $fieldName !!}"
               type="{{ $type }}"
               class="form-control"
               name="{!! $fieldName !!}"
               value="{{ $type != 'password' ? old($fieldName, $value) : '' }}" {{ $required }} {{ $autofocus }}
        >

        @if ($errors->has($fieldName))
            <span class="help-block">
                <strong>{{ $errors->first($fieldName) }}</strong>
            </span>
        @endif
    </div>
</div>