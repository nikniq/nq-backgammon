<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'NQ Backgammon · Play free online backgammon')</title>
    <meta name="description" content="@yield('description', 'Play beautiful, free backgammon in your browser. No download. Track your scores and climb the leaderboard.')">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,800&family=Inter:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v=2">
</head>
<body>
<header class="topbar">
    <div class="topbar-inner">
        <a href="{{ route('home') }}" class="brand"><span class="brand-mark" aria-hidden="true"></span>NQ Backgammon</a>
        <button class="menu-toggle" aria-label="Menu" aria-expanded="false" onclick="this.setAttribute('aria-expanded', document.body.classList.toggle('nav-open'))">
            <span></span><span></span><span></span>
        </button>
        <nav class="mainnav">
            <a href="{{ route('play') }}" @class(['active' => request()->routeIs('play')])>Play</a>
            <a href="{{ route('leaderboard') }}" @class(['active' => request()->routeIs('leaderboard')])>Leaderboard</a>
            <a href="{{ route('pricing') }}" @class(['active' => request()->routeIs('pricing')])>Pricing</a>
            @auth
                <a href="{{ route('scores') }}" @class(['active' => request()->routeIs('scores')])>My Scores</a>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="linkish">
                        @if (auth()->user()->avatar)<img src="{{ auth()->user()->avatar }}" alt="" class="avatar" referrerpolicy="no-referrer">@endif
                        Log out
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}">Log in</a>
                <a href="{{ route('register') }}" class="btn btn-sm">Sign up free</a>
            @endauth
        </nav>
    </div>
</header>
<main class="@yield('main_class', 'container')">
    @yield('content')
</main>
<footer class="footer">
    <div class="footer-inner">
        <div class="footer-brand">
            <a href="{{ route('home') }}" class="brand"><span class="brand-mark" aria-hidden="true"></span>NQ Backgammon</a>
            <p class="muted">The classic game of strategy and luck, beautifully made for your browser.</p>
            <a href="{{ route('play') }}" class="btn btn-sm">Play now</a>
        </div>
        <div>
            <h4>Play</h4>
            <a href="{{ route('play') }}">Play vs computer</a>
            <a href="{{ route('leaderboard') }}">Leaderboard</a>
            <a href="{{ route('home') }}#how">How to play</a>
        </div>
        <div>
            <h4>Account</h4>
            @auth
                <a href="{{ route('scores') }}">My scores</a>
            @else
                <a href="{{ route('register') }}">Sign up free</a>
                <a href="{{ route('login') }}">Log in</a>
            @endauth
            <a href="{{ route('pricing') }}">Pricing</a>
        </div>
        <div>
            <h4>Company</h4>
            <a href="https://nikniq.com" rel="noopener">nikniq</a>
            <a href="{{ route('privacy') }}">Privacy</a>
            <a href="{{ route('terms') }}">Terms</a>
        </div>
    </div>
    <div class="footer-bottom">© {{ date('Y') }} nikniq. All rights reserved.</div>
</footer>
@stack('scripts')
</body>
</html>
