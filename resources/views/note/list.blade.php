@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Notes') }}<a href="{{ route('note.create') }}" class="float-right btn btn-primary btn-sm">{{ __('Create Note') }}</a></div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <ul class="list-group list-group-flush">
                        @foreach ($notes as $note)
                            <li class="list-group-item"><a href="{{ route('note.view', $note->id) }}">{{ $note->title }}</a></li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
