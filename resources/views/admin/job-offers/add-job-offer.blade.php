@extends('layouts.app')

@section('content')
    @if($errors->any() )
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-danger" role="alert">
                        <ul class="list-unstyled">
                            @foreach ($errors->all() as $err => $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <form action="{{ route('admin.job-offers.store') }}" method="POST">
        @csrf
        <div class="container">
            @include('admin.components.messages')
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label for="label">Nome</label>
                        {{-- il name="label" serve per la request CreateCategoryRequest --}}
                        <input type="text" name="label" class="form-control" id="category-label">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        @lang('labels.save')
                    </button>
                    <a href="{{ route('admin.job-offers.index') }}" class="btn btn-danger">
                        @lang('labels.back')
                    </a>
                </div>
            </div>
        </div>
    </form>
@endsection