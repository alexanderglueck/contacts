@extends('layouts.app')

@section('title', 'Kontaktlandkarte')

@section('content')
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-md-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Kontaktlandkarte</h5>
                    </div>
                    <div class="ibox-content">
                        @if(strlen(env('GOOGLE_MAPS_GEOCODING_KEY'))>0)
                            <div id="map"></div>
                        @else
                            <p>Google Maps API Key ist nicht gesetzt. Karte kann nicht geladen werden.</p>
                        @endif

                    </div>
                </div>
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
    @if(strlen(env('GOOGLE_MAPS_GEOCODING_KEY'))>0)
        <script src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js"></script>
        <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_GEOCODING_KEY') }}"></script>
        <script src="{{ url('/js/oms/oms.min.js') }}"></script>
    @endif
@endsection

@section('js')
    @if(strlen(env('GOOGLE_MAPS_GEOCODING_KEY'))>0)
        <script>
            var mapReady = false;
            var documentReady = false;
            var map = null;
            var infoWindow = null;
            var oms = null;

            $(document).ready(function () {
                documentReady = true;

                map = new google.maps.Map(document.getElementById('map'), {
                    zoom: 7,
                    center: {lat: 47.516231, lng: 13.250072},
                    streetViewControl: false,

                });

                mapReady = true;

                infoWindow = new google.maps.InfoWindow();

                oms = new OverlappingMarkerSpiderfier(map);

                oms.addListener('click', function (marker, event) {
                    infoWindow.setContent(marker.desc);
                    infoWindow.open(map, marker);
                });

                $.ajax({
                    type: "GET",
                    url: "{{ route('map.contacts') }}",
                    success: function (data) {

                        var markers = [];

                        $.each(data, function (key, entry) {
                            marker = new google.maps.Marker({
                                position: new google.maps.LatLng(entry.latitude, entry.longitude),
                                map: map,
                                title: entry.title+"\r\n"+entry.name,
                                desc: entry.address
                            });

                            oms.addMarker(marker);

                            markers.push(marker);
                        });


                        // Add a marker clusterer to manage the markers.
                       /* var markerCluster = new MarkerClusterer(map, markers, {
                                    imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'
                                }
                        );

                        markerCluster.setMaxZoom(14);
*/

                    }
                });

            });


        </script>
    @endif
@endsection