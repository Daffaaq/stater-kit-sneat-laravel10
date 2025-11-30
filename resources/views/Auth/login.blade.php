<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Stater-Kit Laravel Sneat</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="robots" content="noindex, nofollow">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Login - Sistem Informasi RW 003 Kelurahan Begadung">
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('auth/css/style.css') }}?v=1.0.0">
    <script>
        if (
            localStorage.getItem('theme') === 'dark' ||
            (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)
        ) {
            document.documentElement.classList.add('dark');
        }
    </script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>

<body>
    <div class="login-wrapper">
        <!-- Dark Mode Toggle -->
        <button class="theme-toggle" id="theme-toggle-btn" onclick="toggleTheme()" title="Toggle dark mode">
            <span id="theme-icon">🌙</span>
        </button>

        <h1>Welcome Back!</h1>
        <p>Sign in to your dashboard and manage your workspace</p>


        <form action="{{ route('login') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="email" class="sr-only">Email address</label>
                <input type="email" id="email" name="email" placeholder="Email address"
                    value="{{ old('email') }}" required autofocus autocomplete="email">
                @error('email')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password" class="sr-only">Password</label>
                <input type="password" id="password" name="password" placeholder="Password" required
                    autocomplete="current-password">
                <span id="toggleIcon" class="toggle-password" onclick="togglePassword()">👁️</span>
                @error('password')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <input type="hidden" name="longitude" id="longitude" value="">
            <input type="hidden" name="latitude" id="latitude" value="">

            <div class="form-group button-group">
                <button type="submit" class="btn-login">Login</button>
            </div>

        </form>

        <div class="form-footer">
            <small style="margin: 0; padding: 0;">
                Don't have an account? <a href="{{ route('register') }}"
                    style="color: var(--primary-color); margin: 0; padding: 0;">Sign up, it's
                    free!</a>
            </small>
            <small>&copy; {{ date('Y') }} Starter Kit Sneat | Created by ACHE</small>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('auth/js/script.js') }}"></script>



</body>

</html>
