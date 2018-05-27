@extends('user_settings.layout.default')

@section('user_settings.content')
    <div class="card">
        <div class="card-header">
            Profile image
        </div>
        <div class="card-body">
            <form class="form-horizontal" role="form" method="POST"
                  enctype="multipart/form-data"
                  action="{{ route('user_settings.image.update', [$user->slug]) }}">
                @method('PUT')
                @csrf


                <div class="form-group{{ $errors->has('image') ? ' has-error' : '' }}">
                    <label for="image"
                           class="col-md-4 control-label">Bild</label>

                    <div class="col-md-6">
                        <input id="image" type="file"
                               class="form-control" name="image"
                               value="">

                        @if ($errors->has('image'))
                            <span class="help-block">
                            <strong>{{ $errors->first('image') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-8 col-md-offset-4">
                        <button type="submit"
                                class="btn btn-primary">
                            Bild aktualisieren
                        </button>

                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
