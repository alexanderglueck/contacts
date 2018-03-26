@if(count($breadcrumbs))
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
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
                        <li>
                            <a href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a>
                        </li>
                    @else
                        <li class="active">{{ $breadcrumb->title }}
                        </li>
                    @endif
                @endforeach
            </ol>
        </div>
    </div>
@endif