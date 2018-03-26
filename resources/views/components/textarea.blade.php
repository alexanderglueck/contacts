<div class="form-group{{ $errors->has($fieldName) ? ' has-error' : '' }}">
    <label for="{!! $fieldName !!}" class="col-md-4 control-label {{ $required }}">
        {{ $label }}
    </label>

    <div class="col-md-6">
        <textarea id="{!! $fieldName !!}"
                  class="form-control"
                  name="{!! $fieldName !!}"
                {{ $required }} {{ $autofocus }}
        >{{ old($fieldName, $value) }}</textarea>

        @if ($errors->has($fieldName))
            <span class="help-block">
                <strong>{{ $errors->first($fieldName) }}</strong>
            </span>
        @endif
    </div>
</div>
