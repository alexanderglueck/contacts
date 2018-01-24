@if(count($contactAddresses)>0)
    <ul>
        @foreach($contactAddresses as $contactAddresse)
            <li>
                <strong><a href="{{ route('contact_addresses.show', [$contact->slug, $contactAddresse->slug]) }}">{{ $contactAddresse->name }}</a></strong>
            </li>
        @endforeach
    </ul>

@else
    <p>Keine Adresse verfügbar</p>
@endif