@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <ul class="list-group">
                    @forelse($companies as $c => $company)
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    {{ $company->name }}
                                </div>
                                <div>
                                    <a href="{{ route('admin.companies.show', ['company' => $company->id]) }}" class="btn btn-primary">Details</a>
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="list-group-item">Non ci sono compagnie</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
@endsection