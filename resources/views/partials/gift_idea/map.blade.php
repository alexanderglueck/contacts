@if(count($contactAddresses) && trim(config('contacts.googleMapsKey')) !== '')
    <div class="card">
        <div class="card-header">
            Map
        </div>
        <div class="card-body">
            <div id="map"></div>
        </div>
    </div>
@endif
