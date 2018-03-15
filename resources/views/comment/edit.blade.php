@extends('layouts.app')

@section('title', trans('ui.edit_comment'))

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">

                <div class="card card-default">
                    <div class="card-header">
                        {{ trans('ui.edit_comment') }}
                    </div>
                    <div class="card-body">
                        <form class="form-horizontal" role="form" method="POST" action="{{ route('comments.update', [$contact->slug, $comment->id]) }}">
                            {{ csrf_field() }}
                            {{ method_field('PUT') }}

                            {!! \App\Helpers\Form::textarea('comment', trans('ui.comment'), $comment->comment) !!}

                            <div class="form-group">
                                <div class="col-md-8 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ trans('ui.edit_comment') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card card-default">
                    <div class="card-header">
                        {{ trans('ui.delete_comment') }}
                    </div>
                    <div class="card-body">
                        <form class="form-horizontal" role="form" method="POST" action="{{ route('comments.destroy', [$contact->slug, $comment->id]) }}">
                            {{ csrf_field() }}
                            {{ method_field('DELETE') }}

                            <div class="form-group">
                                <div class="col-md-8 col-md-offset-4">
                                    <button type="submit" class="btn btn-danger">
                                        {{ trans('ui.delete_comment') }}
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
