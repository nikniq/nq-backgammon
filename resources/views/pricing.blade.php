@extends('layouts.app')
@section('title', 'Pricing · NQ Backgammon')
@section('content')
<div class="page-head">
    <h1>Simple pricing. Start free.</h1>
    <p class="lead">Everything you need to play is free, forever. Pro is coming soon for players who want more.</p>
</div>
<div class="plans">
    <div class="plan">
        <h3>Free</h3>
        <div class="price">$0<span>/forever</span></div>
        <ul>
            <li>Unlimited games vs computer</li>
            <li>Saved scores and stats</li>
            <li>Global leaderboard ranking</li>
            <li>Sign in with Google or nikniq</li>
        </ul>
        @auth
            <a href="{{ route('play') }}" class="btn btn-block">Play now</a>
        @else
            <a href="{{ route('register') }}" class="btn btn-block">Sign up free</a>
        @endauth
    </div>
    <div class="plan featured">
        <span class="badge">Coming soon</span>
        <h3>Pro</h3>
        <div class="price">$4<span>/month</span></div>
        <ul>
            <li>Everything in Free</li>
            <li>Harder AI difficulty levels</li>
            <li>Move hints and game replays</li>
            <li>Custom board and checker themes</li>
            <li>Pro badge on the leaderboard</li>
        </ul>
        @auth
            <a href="{{ route('play') }}" class="btn btn-block btn-ghost">You're on the early-access list</a>
        @else
            <a href="{{ route('register') }}" class="btn btn-block">Get early access</a>
        @endauth
        <p class="fine">Free accounts get first access and launch pricing.</p>
    </div>
</div>
<div class="faq">
    <h2>Questions</h2>
    <details><summary>Is it really free?</summary><p>Yes. Playing, saving scores and the leaderboard are free with no time limit.</p></details>
    <details><summary>Do I need to download anything?</summary><p>No. NQ Backgammon runs in any modern browser on desktop or mobile.</p></details>
    <details><summary>Can I play without an account?</summary><p>Yes, you can play as a guest. You only need an account to save your results and be ranked.</p></details>
</div>
@endsection
