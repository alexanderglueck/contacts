@if(count($contactDates)>0)

    <div class="list-group">
        @foreach($contactDates as $contactDate)
            <a class="list-group-item list-group-item-action" href="{{ route('contact_dates.show', [$contact->slug, $contactDate->slug]) }}">{{ $contactDate->name }}</a>
        @endforeach
    </div>

@else
    <p>Kein Datum verfügbar</p>
@endif