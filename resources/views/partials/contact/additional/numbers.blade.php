<a href="{{ route('contact_numbers.index', [$contact->slug]) }}">Nummern verwalten</a>

@if(count($numbers)>0)
    <ul>
        @foreach($numbers as $number)
            <li><strong>{{$number->name}}</strong> <a href="tel:{{$number->formatted_number }}">{{ $number->number }}</a></li>
        @endforeach
    </ul>
@else
    Keine Nummern verfügbar
@endif