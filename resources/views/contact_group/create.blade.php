@extends('layouts.app')

@section('title', 'Kontaktgruppe hinzufügen')

@section('content')
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-md-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Kontaktgruppe hinzufügen</h5>
                    </div>

                    <div class="ibox-content">
                        <form class="form-horizontal" role="form" method="POST" action="{{ route('contact_groups.store') }}">
                            @include('partials.contact_group.edit')
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
