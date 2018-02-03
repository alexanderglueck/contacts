    <ul>
            <li>
                <strong>Name: </strong> {{ $contact->fullname }}<br/>
                <strong>Geschlecht: </strong>{{ $contact->gender->gender }}<br/>
                <strong>Firma: </strong>   {{ $contact->company }}<br />
                <strong>Beruf: </strong> {{ $contact->job  }}<br />
                <strong>Abteilung: </strong> {{ $contact->department }}<br>
                <strong>Anrede: </strong> {{ $contact->salutation }}<br>
                <strong>Geburtstag: </strong> {{ $contact->formatted_date_of_birth }}<br>
                <strong>Adressen</strong>: @include('partials.contact.additional.addresses', ['addresses' => $contact->addresses])<br>
                <strong>Datumsangaben</strong>: @include('partials.contact.additional.dates', ['dates' => $contact->dates])<br>
                <strong>E-Mails</strong>: @include('partials.contact.additional.emails', ['emails' => $contact->emails])<br>
                <strong>Nummern</strong>: @include('partials.contact.additional.numbers', ['numbers' => $contact->numbers])<br>
                <strong>Websiten</strong>: @include('partials.contact.additional.urls', ['urls' => $contact->urls])<br>
            </li>
    </ul>
