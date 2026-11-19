@extends('layouts.app')
@section('title', 'Leaderboard · Backgammon')
@section('content')
<h1>Leaderboard</h1>
<div class="card">
    <table>
        <thead><tr><th>#</th><th>Player</th><th>Points</th><th>Wins</th><th>Played</th></tr></thead>
        <tbody>
        @forelse ($leaders as $i => $u)
            <tr @if(auth()->id() === $u->id) class="me" @endif>
                <td>{{ $i + 1 }}</td>
                <td>{{ $u->name }}</td>
                <td>{{ (int) $u->total_points }}</td>
                <td>{{ $u->wins_count }}</td>
                <td>{{ $u->games_count }}</td>
            </tr>
        @empty
            <tr><td colspan="5" class="muted">No scores yet.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
