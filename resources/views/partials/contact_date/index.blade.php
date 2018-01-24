@if(count($contactDates)>0)

    <ul>
        @foreach($contactDates as $contactDate)
            <li>
                <strong><a href="{{ route('contact_dates.show', [$contact->slug, $contactDate->slug]) }}">{{ $contactDate->name }}</a></strong>
            </li>
        @endforeach
    </ul>

@else
    <p>Kein Datum verfügbar</p>
@endif