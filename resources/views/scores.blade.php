@extends('layouts.app')
@section('title', 'My Scores · Backgammon')
@section('content')
<h1>My Scores</h1>
<div class="stats">
    <div class="stat"><span>{{ $stats['points'] }}</span>Points</div>
    <div class="stat"><span>{{ $stats['played'] }}</span>Played</div>
    <div class="stat"><span>{{ $stats['wins'] }}</span>Wins</div>
    <div class="stat"><span>{{ $stats['losses'] }}</span>Losses</div>
    <div class="stat"><span>{{ $stats['win_rate'] }}%</span>Win rate</div>
</div>
<div class="card">
    <table>
        <thead><tr><th>Date</th><th>Result</th><th>Type</th><th>Points</th><th>Moves</th><th>Duration</th></tr></thead>
        <tbody>
        @forelse ($games as $g)
            <tr>
                <td>{{ $g->created_at->format('Y-m-d H:i') }}</td>
                <td class="{{ $g->result }}">{{ ucfirst($g->result) }}</td>
                <td>{{ ucfirst($g->win_type) }}</td>
                <td>{{ $g->points > 0 ? '+' : '' }}{{ $g->points }}</td>
                <td>{{ $g->moves }}</td>
                <td>{{ gmdate('i:s', $g->duration_seconds) }}</td>
            </tr>
        @empty
            <tr><td colspan="6" class="muted">No games yet. <a href="{{ route('play') }}">Play one!</a></td></tr>
        @endforelse
        </tbody>
    </table>
    {{ $games->links() }}
</div>
@endsection
