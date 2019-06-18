@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    You are logged in!
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
{{-- 800031950878-8jl8m1ddh7b1iqpkms4hafi3tjagv834.apps.googleusercontent.com --}}
{{-- Secreto del Cliente:         eov3Or6rP2YYIyywqPLL7eVe --}}