<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Skill Swap Platform' }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=space-grotesk:400,500,700|instrument-sans:400,500,600" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('app.css') }}">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite('resources/js/app.js')
    @endif
</head>
<body>
    @php
        $isHome = request()->routeIs('home');
        $isBrowse = request()->routeIs('browse', 'users.show');
        $isDashboard = request()->routeIs('dashboard', 'swaps.*', 'onboarding');
        $isSettings = request()->routeIs('settings');
        $isLogin = request()->routeIs('login');
        $isSignup = request()->routeIs('signup');
    @endphp
    <div class="page-shell">
        <header class="site-header">
            <a class="brand {{ $isHome ? 'brand-active' : '' }}" href="{{ route('home') }}">
                <span class="brand-mark">SS</span>
                <span>
                    <strong>Skill Swap</strong>
                    <small>Teach what you know. Learn what you want.</small>
                </span>
            </a>

            <nav class="site-nav">
                <a href="{{ route('browse') }}" class="nav-link {{ $isBrowse ? 'is-active' : '' }}" @if($isBrowse) aria-current="page" @endif>Browse</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="nav-link {{ $isDashboard ? 'is-active' : '' }}" @if($isDashboard) aria-current="page" @endif>Dashboard</a>
                    <a href="{{ route('settings') }}" class="nav-link {{ $isSettings ? 'is-active' : '' }}" @if($isSettings) aria-current="page" @endif>Settings</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="ghost-button">Log out</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="nav-link {{ $isLogin ? 'is-active' : '' }}" @if($isLogin) aria-current="page" @endif>Log in</a>
                    <a href="{{ route('signup') }}" class="nav-link signup-link {{ $isSignup ? 'is-active' : 'is-primary' }}" @if($isSignup) aria-current="page" @endif>Sign up</a>
                @endauth
            </nav>
        </header>

        @if (session('status'))
            <div class="flash">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="flash flash-error">
                {{ $errors->first() }}
            </div>
        @endif

        <main class="main-content">
            {{ $slot }}
        </main>
    </div>
</body>
</html>
