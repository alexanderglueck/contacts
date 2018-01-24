@foreach (['danger', 'warning', 'success', 'info'] as $msg)
    @if(Session::has('alert-' . $msg))
        <div class="wrapper wrapper-content" style="padding-bottom: 0;">
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-{{ $msg }}" role="alert" style="margin-bottom: 0;">
                        {{ Session::get('alert-' . $msg) }}
                    </div>
                </div>
            </div>
        </div>
    @endif
@endforeach