@extends('layouts.auth')

@section('content')
    <div class="container">
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        <div class="row">

            <div class="col-md-6">
                @isset($url)
                    <form class="box" method="POST" action='{{ url("login/$url") }}' aria-label="{{ __('Login') }}">
                @else
                    <form class="box" method="POST" action="" aria-label="{{ __('Login') }}">
                @endisset
                        @csrf
                        <h1>{{ isset($url) ? $url : '' }} Login</h1>
                        <input type="email" name="email" placeholder="{{ __('E-Mail Address') }}" value="{{ old('email') }}" required autofocus>
                        <input type="password" name="password" required placeholder="Password">
                        <input type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}><label for="remember"> {{ __('Remember Me') }}</label>
                        <input type="submit" name="" value="Login" href="#">

                        @if (isset($url) && $url == 'vendor')
                            <a href="{{ route('reset.vendor') }}">Lost your password?</a> <br /><br />
                            <a href="{{ route('register.vendor') }}">Create an account</a>
                        @endif
                    </form>
            </div>
        </div>
    </div>
@endsection
