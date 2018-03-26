<a href="{{ route('contact_dates.index', [$contact->slug]) }}">Datumsangaben verwalten</a>

@if(count($dates)>0)
    <ul>
        @foreach($dates as $date)
            <li><strong>{{$date->name}}</strong> {{ $date->formatted_date }}</li>
        @endforeach
    </ul>
@else
    Keine Datumsangaben verfügbar
@endif