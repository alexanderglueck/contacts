@extends('layouts.public')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Resend password confirmation
                    </div>

                    <div class="panel-body">
                        <form method="post" class="form-horizontal" action="{{ route('activation.resend.store') }}">
                            @csrf

                            {!! \App\Helpers\Form::email('email', 'E-Mail', null, true, true) !!}

                            <div class="form-group">
                                <div class="col-md-8 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary">
                                        Resend
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
