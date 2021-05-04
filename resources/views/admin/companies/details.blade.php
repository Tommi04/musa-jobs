@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1>
                    {{ $company->name }}
                </h1>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                {{-- @jsonl($company) è l equivalente di {{ json_encode($company) }} --}}
                {{-- @json($company->jobOffers) --}}
                <ul class="list-group">

                  @forelse ($company->jobOffers as $jo => $job_offer)
                    <li class="list-group-item">
                        {{ $caompany->role }}
                        <hr>
                        @foreach ($job_offer->statusHistory as $history_item)
                            <div class="d-flex njustify-content-between">
                                <div>
                                    {{ $history_item->label }}
                                </div>
                                <div class="d-flex flex-column">
                                    <div>
                                        <strong>DA:</strong>
                                        <span>{{ $history_item->pivot->from ? $history_item->pivot->from : '------s' }}</span>
                                    </div>
                                    <div>
                                        <strong>A:</strong>
                                        <span>{{ $history_item->pivot->to ? $history_item->pivot->to : '------'}}</span>
                                    </div>
                                    {{-- {{ $history_item->label }} --}}
                                </div>
                            </div>
                        @endforeach
                        {{-- @json($job_offer->statusHistory) --}}
                    </li>
                  @empty
                    <li class="list-group-itam">
                        Nessuna offerta di lavoro
                    </li>
                  @endforelse
                </ul>

            </div>
        </div>
    </div>
@endsection
