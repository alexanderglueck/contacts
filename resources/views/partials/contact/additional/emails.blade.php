<a href="{{ route('contact_emails.index', [$contact->slug]) }}">E-Mail Adressen verwalten</a>

@if(count($emails)>0)
    <ul>
        @foreach($emails as $email)
            <li><strong>{{$email->name}}</strong> <a href="mailto:{{ $email->email }}" rel="noopener noreferrer">{{ $email->email }}</a></li>
        @endforeach
    </ul>
@else
    Keine E-Mail Adressen verfügbar
@endif
