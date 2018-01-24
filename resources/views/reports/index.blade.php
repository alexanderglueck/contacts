@extends('layouts.app')

@section('title', 'Berichte')

@section('content')
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-md-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Berichte</h5>
                    </div>

                    <div class="ibox-content">
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
    </div>
@endsection
