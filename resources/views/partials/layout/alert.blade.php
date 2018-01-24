@foreach (['danger', 'warning', 'success', 'info'] as $alertType)
    @if(Session::has('alert-' . $alertType))
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-{{ $alertType }} alert-dismissible fade show" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    {{ Session::get('alert-' . $alertType) }}
                </div>
            </div>
        </div>
    @endif
@endforeach