@extends('layout')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Dashboard') }}</div>

                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success" role="alert">
                                {{ session('success') }}
                            </div>
                        @endif
                            <p>You are logged in as:</p>
                            <p>Name: {{ auth()->user()->name }} </p>
                            <p>Email: {{ auth()->user()->email }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
