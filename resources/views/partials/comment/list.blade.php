<ul class="list-unstyled">
    @foreach($collection as $comment)
        @include('partials.comment.comment')
    @endforeach
</ul>
