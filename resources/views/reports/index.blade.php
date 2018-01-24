@extends('layouts.app')

@section('title', 'Berichte')

@section('content')

    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="card card-default">


                <div class="card-body">
                    <div class="card-title">
                        <h5>Berichte</h5>
                    </div>

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
