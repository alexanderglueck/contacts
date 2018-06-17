<ul>
    <li>
        <strong>Name: </strong> {{ $contact->fullname }}<br/>
        <strong>Geschlecht: </strong>{{ $contact->gender->gender }}<br/>
        <strong>Firma: </strong> {{ $contact->company }}<br/>
        <strong>Beruf: </strong> {{ $contact->job  }}<br/>
        <strong>Abteilung: </strong> {{ $contact->department }}<br>
        <strong>Anrede: </strong> {{ $contact->salutation }}<br>
        <strong>Geburtstag: </strong> {{ $contact->formatted_date_of_birth }}
        <br>
        @if (Auth::user()->hasPermissionTo('view addresses'))
            <strong>Adressen</strong>:
            @include('partials.contact.additional.addresses', ['addresses' => $contact->addresses])
            <br>
        @endif

        @if (Auth::user()->hasPermissionTo('view addresses'))
            <strong>Datumsangaben</strong>:
            @include('partials.contact.additional.dates', ['dates' => $contact->dates])
            <br>
        @endif

        @if (Auth::user()->hasPermissionTo('view emails'))
            <strong>E-Mails</strong>:
            @include('partials.contact.additional.emails', ['emails' => $contact->emails])
            <br>
        @endif

        @if (Auth::user()->hasPermissionTo('view numbers'))
            <strong>Nummern</strong>:
            @include('partials.contact.additional.numbers', ['numbers' => $contact->numbers])
            <br>
        @endif

        @if (Auth::user()->hasPermissionTo('view urls'))
            <strong>Websiten</strong>:
            @include('partials.contact.additional.urls', ['urls' => $contact->urls])
            <br>
        @endif

        @if (Auth::user()->hasPermissionTo('view notes'))
            <strong>Notes</strong>:
            @include('partials.contact.additional.notes', ['notes' => $contact->notes])
            <br>
        @endif

        @if (Auth::user()->hasPermissionTo('view giftIdeas'))
            <strong>{{ trans('ui.gift_ideas') }}</strong>:
            @include('partials.contact.additional.gift_ideas', ['giftIdeas' => $contact->giftIdeas])
            <br>
        @endif
    </li>
</ul>
