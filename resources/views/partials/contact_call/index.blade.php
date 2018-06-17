@if(count($contactCalls)>0)

    <div class="list-group">
        @foreach($contactCalls as $contactCall)
            <a class="list-group-item list-group-item-action" href="{{ route('contact_calls.show',  [$contact->slug, $contactCall->id]) }}">{{ $contactCall->called_at }}</a>
        @endforeach
    </div>

@else
    <p>No calls available</p>
@endif
