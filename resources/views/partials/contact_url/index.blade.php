@if(count($contactUrls)>0)

    <div class="list-group">
        @foreach($contactUrls as $contactUrl)
            <a class="list-group-item list-group-item-action" href="{{ route('contact_urls.show',  [$contact->slug, $contactUrl->slug]) }}">{{ $contactUrl->name }}</a>
        @endforeach
    </div>

@else
    <p>Keine Website verfügbar</p>
@endif