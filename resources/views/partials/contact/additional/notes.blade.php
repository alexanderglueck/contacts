<a href="{{ route('contact_notes.index', [$contact->slug])  }}">
    Manage notes
</a>

@if(count($notes)>0)
    <ul>
        @foreach($notes as $note)
            <li><strong>{{$note->name}}</strong> {{ $note->note }}</li>
        @endforeach
    </ul>
@else
    No notes available
@endif
