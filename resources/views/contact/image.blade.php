@extends('layouts.app')

@section('title', 'Bild bearbeiten')

@section('content')

    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="card card-default">
                <div class="card-body">
                    <div class="card-title">
                        <h5>Kontakt Detailansicht</h5>
                    </div>

                    @if(strlen($contact->image)>0)
                        <p>Aktuell: <img src="{{ url($contact->image) }}"/></p>
                    @endif


                    <form class="form-horizontal" role="form" method="POST" enctype="multipart/form-data"
                          action="{{ route('contacts.update_image', [$contact->slug]) }}">
                        <input type="hidden" name="_method" value="PUT">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('image') ? ' has-danger' : '' }}">
                            <label for="image" class="col-md-4 form-control-label">Bild</label>

                            <div class="col-md-6">
                                <div class="image-crop">
                                    <img src="@if(strlen($contact->image)>0){{ url($contact->image) }}@endif">
                                </div>
                                <div class="img-preview img-preview-sm"></div>
                                <label title="Upload image file" for="inputImage" class="btn btn-primary">
                                    <input type="file" accept="image/*" name="file" id="inputImage" class="hide">
                                    Upload new image
                                </label>

                                @if ($errors->has('image'))
                                    <span class="form-text">
                                            <strong>{{ $errors->first('image') }}</strong>
                                        </span>
                                @endif
                            </div>
                        </div>
                        <input type="hidden" id="image-x" name="image_x">
                        <input type="hidden" id="image-y" name="image_y">
                        <input type="hidden" id="image-width" name="image_width">
                        <input type="hidden" id="image-height" name="image_height">

                        <div class="form-group">
                            <div class="col-md-8 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Bild aktualisieren
                                </button>
                            </div>
                        </div>
                    </form>

                </div>

            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" href="{{ url('/css/plugins/cropper/cropper.min.css')  }}"/>
@endsection

@section('js-links')
    <script type="text/javascript" src="{{ url('/js/plugins/cropper/cropper.min.js') }}"></script>
@endsection

@section('js')

    <script>


        $(document).ready(function () {

            var $image = $(".image-crop > img")
            $($image).cropper({
                aspectRatio: 1,
                preview: ".img-preview",
                done: function (data) {
                    // Output the result data for cropping image.

                    $("#image-x").val(data.x);
                    $("#image-y").val(data.y);
                    $("#image-width").val(data.width);
                    $("#image-height").val(data.height);

                }
            });

            var $inputImage = $("#inputImage");
            if (window.FileReader) {
                $inputImage.change(function () {
                    var fileReader = new FileReader(),
                        files = this.files,
                        file;

                    if (!files.length) {
                        return;
                    }

                    file = files[0];

                    if (/^image\/\w+$/.test(file.type)) {
                        fileReader.readAsDataURL(file);
                        fileReader.onload = function () {

                            $image.cropper("reset", true).cropper("replace", this.result);
                        };
                    } else {
                        showMessage("Please choose an image file.");
                    }
                });
            } else {
                $inputImage.addClass("hide");
            }

        })

    </script>

@endsection