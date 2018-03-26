@if(count($contactNumbers)>0)

    <div class="list-group">
        @foreach($contactNumbers as $contactNumber)
            <a class="list-group-item list-group-item-action" href="{{ route('contact_numbers.show', [$contact->slug, $contactNumber->slug]) }}">{{ $contactNumber->name }}</a>
        @endforeach
    </div>

@else
    <p>Keine Nummer verfügbar</p>
@endif