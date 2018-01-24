@if(count($contactUrls)>0)

    <ul>
        @foreach($contactUrls as $contactUrl)
            <li>
                <strong><a href="{{ route('contact_urls.show',  [$contact->slug, $contactUrl->slug]) }}">{{ $contactUrl->name }}</a></strong>
            </li>
        @endforeach
    </ul>

@else
    <p>Keine Website verfügbar</p>
@endif