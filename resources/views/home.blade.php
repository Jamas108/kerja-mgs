@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <p>{{ __('You are logged in!') }}</p>
                    <p>Redirecting to your dashboard...</p>

                    <div class="d-flex justify-content-center mt-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>

                    <script>
                        setTimeout(function() {
                            window.location.href = "{{ route('home') }}";
                        }, 2000);
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection