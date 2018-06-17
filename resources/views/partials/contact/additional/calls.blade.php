<a href="{{ route('contact_calls.index', [$contact->slug])  }}">
    Manage calls
</a>

@if(count($calls)>0)
    <ul>
        @foreach($calls as $call)
            <li><strong>{{$call->called_at}}</strong> {{ $call->note }}</li>
        @endforeach
    </ul>
@else
    No calls available
@endif
