@extends('layouts.app')
@section('title', 'Edit phone')

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('phones.index') }}">Phones</a></li>
            <li class="breadcrumb-item"><a href="{{ route('phones.show', $phone) }}">{{ $phone->label }}</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>
    </nav>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <h1 class="h4 mb-4">Edit phone</h1>
            <form method="POST" action="{{ route('phones.update', $phone) }}">
                @csrf
                @method('PUT')
                @include('phones._form')
                <div class="mt-4 d-flex justify-content-between">
                    <div>
                        <button class="btn btn-primary"><i class="bi bi-check-lg"></i> Save changes</button>
                        <a href="{{ route('phones.show', $phone) }}" class="btn btn-link">Cancel</a>
                    </div>
                    <button form="delete-phone" class="btn btn-outline-danger"
                            onclick="return confirm('Delete this phone and all its location history?')">
                        <i class="bi bi-trash"></i> Delete
                    </button>
                </div>
            </form>
            <form id="delete-phone" method="POST" action="{{ route('phones.destroy', $phone) }}" class="d-none">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>
@endsection
