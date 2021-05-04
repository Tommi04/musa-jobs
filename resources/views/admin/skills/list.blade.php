@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <ul class="list-group">
                    @forelse($skills as $s => $skill)
                        <li class="list-group-item">
                            {{ $skill->label }}
                            <br>
                        </li>
                    @empty
                        <li class="list-group-item">Non ci sono skills</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
@endsection