@extends('layouts.app')
@section('title', 'Play · NQ Backgammon')
@section('content')
<div class="game-wrap">
    <div class="board-col">
        <div id="board" class="board" aria-label="Backgammon board"></div>
        <div id="result" class="result" hidden>
            <div class="result-card">
                <div class="r-title"></div>
                <div class="r-sub"></div>
                @guest
                    <p class="r-pitch">Sign up free to save results like this and get ranked on the leaderboard.</p>
                    <div class="cta-row center">
                        <a href="{{ route('register') }}" class="btn">Save my scores, free</a>
                        <button class="btn btn-ghost" data-again>Play again</button>
                    </div>
                @else
                    <div class="cta-row center">
                        <button class="btn" data-again>Play again</button>
                        <a href="{{ route('leaderboard') }}" class="btn btn-ghost">Leaderboard</a>
                    </div>
                @endguest
            </div>
        </div>
    </div>
    <aside class="panel">
        <div id="status" class="status">Setting up the board…</div>
        <div id="pips" class="pips muted"></div>
        <div id="tip" class="tip"></div>
        <div class="buttons">
            <button id="btn-roll" class="btn" disabled>🎲 Roll</button>
            <button id="btn-hint" class="btn btn-ghost" disabled title="Suggest the best move (H)">💡 Hint</button>
            <button id="btn-undo" class="btn btn-ghost" disabled>Undo</button>
            <button id="btn-new" class="btn btn-ghost">New game</button>
        </div>
        <div class="info">
            @auth
                <p>Playing as <strong>{{ auth()->user()->name }}</strong>. Results are saved automatically.</p>
                <p id="save-status" class="muted"></p>
            @else
                <div class="upsell">
                    <strong>Playing as a guest</strong>
                    <p>Your wins won't count yet. Create a free account in 10 seconds to save scores and join the leaderboard.</p>
                    @include('partials.social-login')
                    <a href="{{ route('register') }}" class="btn btn-block btn-sm">Sign up with email</a>
                </div>
                <p id="save-status" class="muted"></p>
            @endauth
        </div>
        <details class="help">
            <summary>How to play</summary>
            <p>You are <b>White</b> and move counter-clockwise toward your home board (bottom right, points 1–6). Roll, click a glowing checker, then click a highlighted point. Either die can be played first. Stuck? Press 💡 Hint (or H). Doubles give four moves. Bear off all 15 checkers to win.</p>
            <p>Gammon (opponent has borne off none) = 2 points. Backgammon (opponent also still has a checker on the bar or in your home) = 3 points.</p>
            <p><a href="{{ route('rules') }}">Read the full rules and strategy tips →</a></p>
        </details>
    </aside>
</div>
@endsection
@push('scripts')
<script>
    window.BG = {
        loggedIn: @json(auth()->check()),
        saveUrl: @json(route('games.store')),
    };
</script>
<script src="{{ asset('js/backgammon.js') }}?v=3"></script>
@endpush
