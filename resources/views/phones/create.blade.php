@extends('layouts.app')
@section('title', 'Register a phone')

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('phones.index') }}">Phones</a></li>
            <li class="breadcrumb-item active">Register</li>
        </ol>
    </nav>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <h1 class="h4 mb-4">Register a phone</h1>
            <form method="POST" action="{{ route('phones.store') }}">
                @csrf
                @include('phones._form')
                <div class="mt-4">
                    <button class="btn btn-primary"><i class="bi bi-check-lg"></i> Register phone</button>
                    <a href="{{ route('phones.index') }}" class="btn btn-link">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
