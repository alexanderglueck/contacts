@if(count($contactEmails)>0)

    <div class="list-group">
        @foreach($contactEmails as $contactEmail)
            <a class="list-group-item list-group-item-action" href="{{ route('contact_emails.show', [$contact->slug, $contactEmail->slug]) }}">{{ $contactEmail->name }}</a>
        @endforeach
    </div>

@else
    <p>Kein E-Mail Adresse verfügbar</p>
@endif