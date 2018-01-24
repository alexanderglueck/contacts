@if ($breadcrumbs)
    <h2>
        @foreach ($breadcrumbs as $breadcrumb)
            @if ($loop->last)
                {{ $breadcrumb->title }}
            @endif
        @endforeach
    </h2>
    <ol class="breadcrumb">
        @foreach ($breadcrumbs as $breadcrumb)
            @if (!$loop->last)
                <li><a href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a></li>
            @else
                <li class="active"><strong>{{ $breadcrumb->title }}</strong></li>
            @endif
        @endforeach
    </ol>
@endif