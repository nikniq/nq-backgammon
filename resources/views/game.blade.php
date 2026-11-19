@extends('layouts.app')
@section('content')
<div class="game-wrap">
    <div class="board-col">
        <div id="board" class="board" aria-label="Backgammon board"></div>
    </div>
    <aside class="panel">
        <div id="status" class="status">Click “New game” to start.</div>
        <div id="dice" class="dice"></div>
        <div class="buttons">
            <button id="btn-roll" class="btn" disabled>Roll</button>
            <button id="btn-undo" class="btn btn-ghost" disabled>Undo</button>
            <button id="btn-new" class="btn btn-ghost">New game</button>
        </div>
        <div class="info">
            @auth
                <p>Playing as <strong>{{ auth()->user()->name }}</strong>. Results are saved automatically.</p>
                <p id="save-status" class="muted"></p>
            @else
                <p class="muted">You're playing as a guest. <a href="{{ route('login') }}">Log in</a> or <a href="{{ route('register') }}">register</a> to save your scores.</p>
            @endauth
        </div>
        <details class="help">
            <summary>How to play</summary>
            <p>You are <b>White</b> and move counter-clockwise toward your home board (bottom right). Roll, then click a checker and click a highlighted point to move it. Doubles give four moves. Bear off all 15 checkers to win.</p>
            <p>Gammon (opponent has borne off none) = 2 points, Backgammon (opponent also has a checker on the bar or in your home) = 3 points.</p>
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
<script src="{{ asset('js/backgammon.js') }}"></script>
@endpush
