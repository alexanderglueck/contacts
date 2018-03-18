@if(count($giftIdeas)>0)
    <div class="list-group">
        @foreach($giftIdeas as $giftIdea)
            <a class="list-group-item list-group-item-action" href="{{ route('gift_ideas.show', [$contact->slug, $giftIdea->id]) }}">{{ $giftIdea->name }}</a>
        @endforeach
    </div>
@else
    <p>{{ trans('ui.no_gift_ideas') }}</p>
@endif
