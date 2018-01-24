@if(count($contactAddresses)>0)
    <div class="list-group">
        @foreach($contactAddresses as $contactAddresse)
            <a class="list-group-item list-group-item-action" href="{{ route('contact_addresses.show', [$contact->slug, $contactAddresse->slug]) }}">{{ $contactAddresse->name }}</a>
        @endforeach
    </div>

@else
    <p>Keine Adresse verfügbar</p>
@endif