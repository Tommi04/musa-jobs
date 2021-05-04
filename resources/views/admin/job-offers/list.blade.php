@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <ul class="list-group">
                    @forelse($job_offers as $jb => $job_offer)
                        <li class="list-group-item">
                            {{ $job_offer->role }}
                            <br>
                        </li>
                    @empty
                        <li class="list-group-item">Non ci sono offerte di lavoro</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
@endsection