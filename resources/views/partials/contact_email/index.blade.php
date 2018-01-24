@if(count($contactEmails)>0)

    <ul>
        @foreach($contactEmails as $contactEmail)
            <li>
                <strong><a href="{{ route('contact_emails.show', [$contact->slug, $contactEmail->slug]) }}">{{ $contactEmail->name }}</a></strong>
            </li>
        @endforeach
    </ul>

@else
    <p>Kein E-Mail Adresse verfügbar</p>
@endif