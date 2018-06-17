@if(count($notes)>0)
    <div class="list-group">
        @foreach($notes as $note)
            <a class="list-group-item list-group-item-action" href="{{ route('contact_notes.show',  [$contact->slug, $note->slug]) }}">
                {{ $note->name }}
            </a>
        @endforeach
    </div>
@else
    <p>No notes available</p>
@endif
