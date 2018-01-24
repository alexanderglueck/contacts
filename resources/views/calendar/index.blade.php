@extends('layouts.app')

@section('title', 'Kalender')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-body">
                    <div class="card-title">
                        <h5>Kalender</h5>
                    </div>
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link type="text/css" rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.2.0/fullcalendar.min.css"/>
    <link type="text/css" rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.2.0/fullcalendar.print.css" media="print"/>
@endsection

@section('js-links')
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.17.1/moment.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.2.0/fullcalendar.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.2.0/locale/de-at.js"></script>
@endsection

@section('js')
    <script>
        $(document).ready(function () {
            $('#calendar').fullCalendar({
                header: {
                    left: 'month,listMonth',
                    center: 'title',
                    right: 'today prev,next'
                },
                firstDay: 1,
                height: 650,
                events: '{{ route('calendar.events') }}'
            });
        });
    </script>

@endsection