<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\User;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function home()
    {
        return view('home', [
            'playerCount' => User::has('games')->count(),
            'gameCount' => Game::count(),
            'topPlayers' => User::query()
                ->withSum('games as total_points', 'points')
                ->has('games')
                ->orderByDesc('total_points')
                ->limit(3)
                ->get(),
        ]);
    }

    public function play()
    {
        return view('game');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'result' => ['required', 'in:win,loss'],
            'win_type' => ['required', 'in:single,gammon,backgammon'],
            'moves' => ['nullable', 'integer', 'min:0', 'max:10000'],
            'duration_seconds' => ['nullable', 'integer', 'min:0', 'max:86400'],
        ]);

        $multiplier = ['single' => 1, 'gammon' => 2, 'backgammon' => 3][$data['win_type']];
        $data['points'] = $data['result'] === 'win' ? $multiplier : -$multiplier;

        $game = $request->user()->games()->create($data);

        return response()->json(['saved' => true, 'game' => $game, 'stats' => $this->stats($request->user())]);
    }

    public function scores(Request $request)
    {
        $user = $request->user();

        return view('scores', [
            'stats' => $this->stats($user),
            'games' => $user->games()->latest()->paginate(20),
        ]);
    }

    public function leaderboard()
    {
        $leaders = User::query()
            ->withCount(['games', 'games as wins_count' => fn ($q) => $q->where('result', 'win')])
            ->withSum('games as total_points', 'points')
            ->has('games')
            ->orderByDesc('total_points')
            ->orderByDesc('wins_count')
            ->limit(50)
            ->get();

        return view('leaderboard', compact('leaders'));
    }

    private function stats(User $user): array
    {
        $games = $user->games();
        $played = (clone $games)->count();
        $wins = (clone $games)->where('result', 'win')->count();

        return [
            'played' => $played,
            'wins' => $wins,
            'losses' => $played - $wins,
            'points' => (int) (clone $games)->sum('points'),
            'win_rate' => $played ? round($wins / $played * 100) : 0,
        ];
    }
}
