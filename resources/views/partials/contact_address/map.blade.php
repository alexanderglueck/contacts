@if(count($contactAddresses) && trim(config('contacts.googleMapsKey')) !== '')
    <div class="card-body">
        <div class="card-title">
            Map
        </div>
        <div class="card-body">
            <div id="map"></div>
        </div>
    </div>
@endif
