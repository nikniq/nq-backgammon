@extends('layouts.app')
@section('main_class', 'landing')
@section('content')
<section class="hero">
    <div class="hero-inner">
        <div class="hero-copy">
            <span class="eyebrow">Free · No download · Plays in your browser</span>
            <h1>The world's oldest strategy game, <em>beautifully</em> reborn.</h1>
            <p class="lead">Roll the dice, outthink the computer and climb the global leaderboard. A game takes about ten minutes, and you can start right now without signing up.</p>
            <div class="cta-row">
                <a href="{{ route('play') }}" class="btn btn-lg">Play free now</a>
                @guest
                    <a href="{{ route('register') }}" class="btn btn-lg btn-ghost">Create free account</a>
                @endguest
            </div>
            <ul class="trust">
                <li>✓ No credit card</li>
                <li>✓ Sign in with Google or nikniq</li>
                <li>✓ Works on phone, tablet and desktop</li>
            </ul>
        </div>
        <div class="hero-art">@include('partials.board-art')</div>
    </div>
</section>

@if ($gameCount > 0)
<section class="proof">
    <div class="proof-inner">
        <div><strong>{{ number_format($gameCount) }}</strong><span>games played</span></div>
        <div><strong>{{ number_format($playerCount) }}</strong><span>ranked players</span></div>
        @if ($topPlayers->isNotEmpty())
            <div><strong>{{ $topPlayers->first()->name }}</strong><span>is #1 right now. Can you beat them?</span></div>
        @endif
    </div>
</section>
@endif

<section class="features">
    <h2>Why players stay for "one more game"</h2>
    <div class="feature-grid">
        <div class="feature"><div class="ficon">🎲</div><h3>Instant play</h3><p>Open the page and roll. No installs, no ads between moves, no waiting for an opponent.</p></div>
        <div class="feature"><div class="ficon">🧠</div><h3>A worthy opponent</h3><p>The computer plays a sound, steady game that rewards real strategy: priming, hitting and safe bear-offs.</p></div>
        <div class="feature"><div class="ficon">🏆</div><h3>Global leaderboard</h3><p>Every win counts, and gammons and backgammons score double and triple. Watch your name climb.</p></div>
        <div class="feature"><div class="ficon">📈</div><h3>Track your progress</h3><p>Your record, win rate and points are saved to your account, on any device.</p></div>
    </div>
</section>

<section class="how" id="how">
    <div class="how-inner">
        <div>
            <h2>Learn in two minutes</h2>
            <ol class="steps">
                <li><b>Roll</b> the dice. Each die is one move.</li>
                <li><b>Click a checker</b>, then a glowing point to move it.</li>
                <li><b>Hit</b> lone enemy checkers to send them to the bar.</li>
                <li><b>Bear off</b> all 15 checkers first to win.</li>
            </ol>
            <div class="cta-row"><a href="{{ route('play') }}" class="btn">Try it now</a><a href="{{ route('rules') }}" class="btn btn-ghost">Full rules</a></div>
        </div>
        <div class="how-card">
            <h3>Scoring</h3>
            <table class="score-table">
                <tr><td>Single win</td><td>+1</td></tr>
                <tr><td>Gammon <small>(opponent bore off none)</small></td><td>+2</td></tr>
                <tr><td>Backgammon <small>(and still stuck in your home)</small></td><td>+3</td></tr>
            </table>
        </div>
    </div>
</section>

<section class="final-cta">
    <h2>Your first game is one click away</h2>
    <p>Join free and get on the leaderboard today.</p>
    <div class="cta-row center">
        <a href="{{ route('play') }}" class="btn btn-lg btn-light">Play now</a>
        @guest<a href="{{ route('register') }}" class="btn btn-lg btn-outline-light">Sign up free</a>@endguest
    </div>
</section>
@endsection
