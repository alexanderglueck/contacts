@extends('layouts.app')

@section('title', trans('ui.logs'))

@section('content')

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card ">

                    <div class="card-header">
                        {{ trans('ui.logs') }}
                    </div>
                    <div class="card-body">

                        @if( ! empty($logs))

                            {{ $logs->links() }}

                            <table class="table">
                                <thead>
                                <tr>
                                    <th scope="col">Event</th>
                                    <th scope="col">IP</th>
                                    <th scope="col">Recorded at</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($logs as $log)
                                    <tr>
                                        <th scope="row">{{ trans('event.' . $log->event) }}</th>
                                        <td>{{ $log->ip_address }}</td>
                                        <td>{{ $log->created_at }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>

                            {{ $logs->links() }}
                        @else
                            <p>No logs available</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
