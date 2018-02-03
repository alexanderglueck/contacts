@extends('layouts.app')

@section('title', 'Kontaktlandkarte')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-body">
                    <div class="card-title">
                        <h5>Kontaktlandkarte</h5>
                    </div>

                    @if(strlen(config('contacts.googleMapsKey'))>0)
                        <div id="map"></div>
                    @else
                        <p>Google Maps API Key ist nicht gesetzt. Karte kann
                            nicht geladen werden.</p>
                    @endif

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
    @if(strlen(config('contacts.googleMapsKey'))>0)
        <script src="https://maps.googleapis.com/maps/api/js?key={{ config('contacts.googleMapsKey') }}"></script>
        <script src="{{ url('/js/oms/oms.min.js') }}"></script>
    @endif
@endsection

@section('js')
    @if(strlen(config('contacts.googleMapsKey'))>0)
        <script>
            var map = null;
            var infoWindow = null;
            var oms = null;
            var xhr = null;
            var markers = [];

            $(document).ready(function () {
                map = new google.maps.Map(document.getElementById('map'), {
                    zoom: 7,
                    center: {lat: 47.516231, lng: 13.250072},
                    streetViewControl: false,

                });

                infoWindow = new google.maps.InfoWindow();

                oms = new OverlappingMarkerSpiderfier(map);

                oms.addListener('click', function (marker, event) {
                    infoWindow.setContent(marker.desc);
                    infoWindow.open(map, marker);
                });

                map.addListener('bounds_changed', function () {
                    getContacts();
                });
            });

            function getContacts() {
                xhr = $.ajax({
                    type: "POST",
                    url: "{{ route('map.contacts') }}",
                    data: {
                        "bounds": map.getBounds().toUrlValue()
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function () {
                        if (xhr !== null) {
                            xhr.abort();
                        }
                    },
                    success: function (data) {

                        hideOutOfBoundsMarkers();

                        $.each(data, function (key, entry) {

                            if (!showIfMarkerExist(entry)) {
                                // Already in list, but without map. Set map and proceed.

                                marker = new google.maps.Marker({
                                    position: new google.maps.LatLng(entry.latitude, entry.longitude),
                                    map: map,
                                    title: getTitle(entry),
                                    desc: entry.address
                                });

                                oms.addMarker(marker);

                                markers.push(marker);
                            }
                        });

                        xhr = null;
                    }
                });
            }

            function hideOutOfBoundsMarkers() {
                for (var i = 0; i < markers.length; i++) {
                    if (isOutOfBounds(markers[i])) {
                        hideMarker(markers[i]);
                    }
                }
            }

            function isOutOfBounds(marker) {
                return !map.getBounds().contains(marker.getPosition());
            }

            function hideMarker(marker) {
                marker.setMap(null);
            }

            function isVisible(marker) {
                return marker.getMap() != null;
            }

            function showIfMarkerExist(entry) {
                for (var i = 0; i < markers.length; i++) {
                    if (
                        isSamePosition(markers[i], entry) &&
                        isSameName(markers[i], entry)
                    ) {
                        showMarker(markers[i]);

                        return true;
                    }
                }
                return false;
            }

            function showMarker(marker) {
                if (!isVisible(marker)) {
                    marker.setMap(map);
                }
            }

            function isSamePosition(marker, entry) {
                return marker.getPosition().equals(
                    new google.maps.LatLng(entry.latitude, entry.longitude)
                );
            }

            function getTitle(entry) {
                return entry.title + "\r\n" + entry.name;
            }

            function isSameName(marker, entry) {
                return getTitle(entry) == marker.getTitle();
            }

        </script>
    @endif
@endsection