@extends('layouts.app')

@section('title', 'Berichte')

@section('content')

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        Berichte
                    </div>
                    <div class="card-body">
                        @if(count($reports)>0)

                            <ul>
                                @foreach($reports as $key => $report)
                                    <li>
                                        <strong><a href="{{ route('reports.'.$key) }}">{{ $report }}</a></strong>
                                    </li>
                                @endforeach
                            </ul>

                        @else
                            <p>Keine Berichte verfügbar</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

@endsection
