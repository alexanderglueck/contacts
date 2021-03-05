<a href="{{ route('contact_urls.index', [$contact->slug])  }}">Websiten verwalten</a>

@if(count($urls)>0)
    <ul>
        @foreach($urls as $url)
            <li><strong>{{$url->name}}</strong>
                <a href="{{ $url->url }}" target="_blank" rel="noopener noreferrer">{{ $url->url }}</a>
            </li>
        @endforeach
    </ul>
@else
    Keine Websiten verfügbar
@endif
