@extends('layouts.app')

@section('title', 'Adresse bearbeiten')

@section('content')
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-md-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Adresse bearbeiten</h5>
                    </div>

                    <div class="ibox-content">
                        <form class="form-horizontal" id="submitForm" role="form" method="POST" action="{{ route('contact_addresses.update', [$contact->slug, $contactAddress->slug]) }}">
                            {{ method_field('PUT') }}
                            @include('partials.contact_address.edit')
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('js-links')
    @if(strlen(env('GOOGLE_MAPS_GEOCODING_KEY'))>0)
        <script async defer src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_GEOCODING_KEY')  }}"></script>
    @endif
@endsection

@section('js')
    @if(strlen(env('GOOGLE_MAPS_GEOCODING_KEY'))>0)
        <script>
            $('#submitForm').submit(function (event) {
                var $form = this;
                event.preventDefault();
                $('#submitForm button').prop('disabled', true);

                var street = $('#street').val();
                var zip = $('#zip').val();
                var city = $('#city').val();
                var state = $('#state').val();
                var country = $('#country_id option:selected').text();
                var geocoder = new google.maps.Geocoder();
                geocoder.geocode({"address": street + ", " + zip + " " + city + ", " + state + ", " + country}, function (result, status) {
                    // Extract latitude and longitude
                    if (status == google.maps.GeocoderStatus.OK) {
                        $('#latitude').val(result[0].geometry.location.lat);
                        $('#longitude').val(result[0].geometry.location.lng);

                        $form.submit();
                    }else {
                        swal({
                            title: "Keine Geo-Daten",
                            text: "Es wurden keine Geo-Daten gefunden. Trotzdem peichern? ",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#DD6B55",
                            confirmButtonText: "Ja, trotzdem speichern!",
                            closeOnConfirm: false,
                            cancelButtonText: "Abbrechen!"
                        }, function () {
                            $form.submit();
                        });
                        $('#submitForm button').prop('disabled', false);
                    }
                });
            });
        </script>
    @endif
@endsection
