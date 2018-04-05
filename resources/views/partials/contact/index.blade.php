@if(count($contacts)>0)

    {{ $contacts->links() }}

    <div class="list-group">
        @foreach($contacts as $contact)
            <a class="list-group-item  list-group-item-action" href="{{ route('contacts.show', [$contact->slug]) }}">

                {{ $contact->fullname }}

            </a>
        @endforeach
    </div>

    {{ $contacts->links() }}

@else
    <p>{{ trans('ui.no_contacts') }}</p>
@endif
