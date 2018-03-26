@if(count($contactGroups)>0)
    {{ $contactGroups->links() }}

    <div class="list-group">
        @foreach($contactGroups as $contactGroup)
            <a class="list-group-item list-group-item-action" href="{{ route('contact_groups.show', $contactGroup->slug) }}">{{ $contactGroup->name }}</a>
        @endforeach
    </div>

    {{ $contactGroups->links() }}
@else
    <p>Keine Kontaktgruppen verfügbar</p>
@endif