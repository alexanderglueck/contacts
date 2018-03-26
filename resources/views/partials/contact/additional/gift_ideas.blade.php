<a href="{{ route('gift_ideas.index', [$contact->slug])  }}">
    {{ trans('ui.manage_gift_ideas') }}
</a>

@if(count($giftIdeas)>0)
    <ul>
        @foreach($giftIdeas as $giftIdea)
            <li><strong>{{$giftIdea->name}}</strong>
                <a href="{{ $giftIdea->url }}" target="_blank">
                    {{ $giftIdea->url }}
                </a>
            </li>
        @endforeach
    </ul>
@else
    {{ trans('ui.no_gift_ideas') }}
@endif
