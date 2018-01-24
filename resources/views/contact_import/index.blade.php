@extends('layouts.app')

@section('title', 'Kontakte importieren')

@section('content')
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-md-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Kontakte importieren</h5>
                    </div>

                    <div class="ibox-content">
                        <form class="form-horizontal" role="form" method="POST" enctype="multipart/form-data"
                              action="{{ route('import.import') }}">
                            {{ csrf_field() }}

                            <div class="form-group{{ $errors->has('contact_group_id') ? ' has-error' : '' }}">
                                <label for="parent_id"
                                       class="col-md-4 control-label">
                                    Ziel Kontaktgruppe<span
                                            class="required">*</span>
                                </label>

                                <div class="col-md-6">
                                    <select name="contact_group_id"
                                            id="contact_group_id"
                                            class="form-control">
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

                            <div class="form-group{{ $errors->has('import_file') ? ' has-error' : '' }}">
                                <label for="salutation"
                                       class="col-md-4 control-label">
                                    Import Datei<span class="required">*</span>
                                </label>

                                <div class="col-md-6">
                                    <input id="import_file"
                                           type="file"
                                           class="form-control"
                                           name="import_file"
                                           value="{{ old('import_file') }}">

                                    @if ($errors->has('import_file'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('import_file') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-8 col-md-offset-4">
                                    <button type="submit"
                                            class="btn btn-primary">
                                        Kontakte importieren
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
