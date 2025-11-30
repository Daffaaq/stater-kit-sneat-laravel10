<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Register - Sistem Informasi RW 003</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
</head>

<body>
    <div class="login-wrapper">
        <!-- Dark Mode Toggle -->
        <button class="theme-toggle" id="theme-toggle-btn" onclick="toggleTheme()" title="Toggle dark mode">
            <span id="theme-icon">🌙</span>
        </button>

        <h1>Create Account</h1>
        <p>Register to access the dashboard</p>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="form-group">
                <label for="name" class="sr-only">Name</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                    placeholder="Name">
                @error('name')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="email" class="sr-only">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required
                    placeholder="Email">
                @error('email')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password" class="sr-only">Password</label>
                <input id="password" type="password" name="password" required placeholder="Password">
                <span id="toggleIcon" class="toggle-password" onclick="togglePassword()">👁️</span>
                @error('password')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation" class="sr-only">Confirm Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required
                    placeholder="Confirm Password">
            </div>

            <div class="form-group button-group">
                <button type="submit" class="btn-login">Register</button>
            </div>
        </form>

        <div class="form-footer">
            <a href="{{ url('/') }}">Already have an account? Login</a>
            <small>&copy; {{ date('Y') }} Starter Kit Sneat | Created by ACHE</small>
        </div>
    </div>

    <script src="{{ asset('auth/js/script.js') }}"></script>
</body>

</html>
