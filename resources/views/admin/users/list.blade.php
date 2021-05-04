@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <ul class="list-group">
                    @forelse($users as $u => $user)
                        <li class="list-group-item">
                            {{ $user->full_name }}
                        </li>
                    @empty
                        <li class="list-group-item">Non ci sono utenti</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
@endsection