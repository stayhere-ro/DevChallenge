@extends('layouts.guest_booking')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Login') }}</div>

                <div class="card-body">
                    <div class="alert alert-secondary d-flex align-items-center justify-content-between" role="alert">
                        <div>
                            <strong>Demo credentials:</strong>
                            <div>Email: <code>testuser@example.com</code></div>
                            <div>Password: <code>password</code></div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary" data-auto-login>Auto login</button>
                    </div>
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6 offset-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Login') }}
                                </button>

                                @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            var btn = document.querySelector('[data-auto-login]');
                            if (!btn) return;
                            btn.addEventListener('click', function () {
                                var email = document.getElementById('email');
                                var password = document.getElementById('password');
                                var remember = document.getElementById('remember');
                                if (email) email.value = 'testuser@example.com';
                                if (password) password.value = 'password';
                                if (remember) remember.checked = true;
                                var form = btn.closest('.card-body')?.querySelector('form') || document.querySelector('form[action="{{ route('login') }}"]');
                                if (form) form.submit();
                            });
                        });
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
