<ul>
    <li>
        <strong>{{ trans('ui.name') }}: </strong>
        {{ $giftIdea->name }}<br/>

        <strong>{{ trans('ui.description') }}: </strong>
        {{ $giftIdea->description }}<br/>

        <strong>{{ trans('ui.link') }}: </strong>
        @if (trim($giftIdea->url) !== '')
            <a href="{{ $giftIdea->url }}" target="_blank" rel="noopener noreferrer">
                {{ $giftIdea->url }}
            </a>
        @endif
        <br/>

        <strong>{{ trans('ui.due_at') }}: </strong>
        {{ $giftIdea->formatted_due_at }}
    </li>
</ul>
