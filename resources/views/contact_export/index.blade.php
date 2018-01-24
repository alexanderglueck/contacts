@extends('layouts.app')

@section('title', 'Kontakte exportieren')

@section('content')
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-md-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Kontakte exportieren</h5>
                    </div>

                    <div class="ibox-content">
                        <form class="form-horizontal" role="form" method="POST"
                              action="{{ route('export.export') }}">
                            {{ csrf_field() }}

                            <div class="form-group{{ $errors->has('contact_group_id') ? ' has-error' : '' }}">
                                <label for="parent_id" class="col-md-4 control-label">
                                    Kontaktgruppe<span class="required">*</span>
                                </label>

                                <div class="col-md-6">
                                    <select name="contact_group_id" id="contact_group_id" class="form-control">
                                        @foreach($contactGroups as $group)
                                            <option {{ old('contact_group_id') == $group->id ? 'selected' : '' }} value="{{$group->id}}">{{$group->name}}</option>
                                        @endforeach
                                    </select>

                                    @if ($errors->has('contact_group_id'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('contact_group_id') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-8 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary">
                                        Kontakte exportieren
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
