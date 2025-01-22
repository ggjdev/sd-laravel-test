@extends('layouts.app')

@section('content')
<div class="container">

    <div class="row justify-content-center">

        <div class="col-md-8">

            <div class="card">

                <div class="card-header">{{ __(isset($note) ? 'Edit Note' : 'Create Note') }}</div>

                <div class="card-body">

                    <form action="{{ isset($note) ? route('note.store', $note->id) : route('note.store') }}" method="post">
                        @csrf

                        <div class="form-group">
                            <label for="title">{{ __('Title') }}*</label>
                            <input type="text" name="title" id="title" placeholder="Title" class="form-control @error('title') is-invalid @enderror" value="{{ isset($note) ? $note->title : '' }}">

                            @error('title')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>


                        <div class="form-group">
                            <label for="content">{{ __('Content') }}</label>
                            <textarea name="content" id="content" rows="10" class="form-control @error('content') is-invalid @enderror" id="content">{{ isset($note) ? $note->content : '' }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection
