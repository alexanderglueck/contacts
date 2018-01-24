@extends('layouts.app')

@section('title', 'Kontaktgruppen verwalten')

@section('content')
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-md-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Kontaktgruppen verwalten</h5>
                    </div>

                    <div class="ibox-content">
                        <p><strong>Kontaktgruppen: </strong><br>
                            <a href="{{ route('contact_groups.create') }}">Kontaktgruppe hinzufügen</a>
                        </p>


                        @include('partials.contact_group.index')


                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
