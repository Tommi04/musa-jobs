@extends('layouts.app')

@section('content')
    <div class="container">
        @include('admin.components.messages')
        <div class="row">
            <div class="col-12">
                <ul class="list-group">
                    @forelse($categories as $c => $category)
                        <li class="list-group-item">
                            {{ $category->label }}
                        </li>
                    @empty
                        <li class="list-group-item">Non ci sono ancora categorie</li>
                    @endforelse
                </ul>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">Nuova categoria</a>
            </div>
        </div>
    </div>
@endsection