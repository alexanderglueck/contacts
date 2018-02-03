@extends('layouts.app')

@section('title', 'Adressen verwalten')

@section('content')
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="card card-default">


                <div class="card-body">
                    <div class="card-title">
                        <h5>Adressen verwalten</h5>
                    </div>

                    <p>
                        <strong>Kontakt Adressen: </strong><br>
                        <a href="{{ route('contact_addresses.create', [$contact->slug]) }}">Adresse
                            hinzufügen</a>
                    </p>

                    @include('partials.contact_address.index')
                </div>

                @include('partials.contact_address.map')

            </div>
        </div>
    </div>
@endsection

@section('css')
    <style>
        #map {
            height: 650px;
        }
    </style>
@endsection

@section('js-links')
    @if(count($contactAddresses) && trim(config('contacts.googleMapsKey')) !== '')
        <script src="https://maps.googleapis.com/maps/api/js?key={{ config('contacts.googleMapsKey') }}"></script>
        <script src="{{ url('/js/oms/oms.min.js') }}"></script>
    @endif
@endsection

@section('js')
    @if(count($contactAddresses) && trim(config('contacts.googleMapsKey')) !== '')
        <script>
            var map = null;
            var oms = null;
            var markers = [];

            $(document).ready(function () {
                map = new google.maps.Map(document.getElementById('map'), {
                    zoom: 7,
                    center: {lat: 47.516231, lng: 13.250072},
                    streetViewControl: false,
                });

                infoWindow = new google.maps.InfoWindow();

                oms = new OverlappingMarkerSpiderfier(map);

                var bounds = new google.maps.LatLngBounds();

                @foreach($contactAddresses as $contactAddress)
                    @if (trim($contactAddress->latitude) !== '' && trim($contactAddress->longitude) !== '')
                        marker = new google.maps.Marker({
                            position: new google.maps.LatLng('{{ $contactAddress->latitude }}', '{{ $contactAddress->longitude }}'),
                            map: map,
                            title: '{{ $contactAddress->name }}'
                        });

                        oms.addMarker(marker);
                        markers.push(marker);
                        bounds.extend(marker.getPosition());
                    @endif
                @endforeach

                map.fitBounds(bounds);
            });
        </script>
    @endif
@endsection
