@if(count($contactGroups)>0)
    <ul>
        @foreach($contactGroups as $contactGroup)
            <li>
                <strong><a href="{{ route('contact_groups.show', $contactGroup->slug) }}">{{ $contactGroup->name }}</a></strong>
            </li>
        @endforeach
    </ul>

@else
    <p>Keine Kontaktgruppen verfügbar</p>
@endif