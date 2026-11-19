@extends('layouts.app')
@section('title', 'How to Play Backgammon · NQ Backgammon')
@section('description', 'Learn backgammon in minutes: setup, moving, hitting, the bar, bearing off and scoring, with tips for beginners.')
@section('content')
<div class="page-head">
    <h1>How to play backgammon</h1>
    <p class="lead">Learn the whole game in about five minutes. Then play a practice game against the computer, with no sign-up needed.</p>
    <div class="cta-row center" style="margin-top:20px"><a href="{{ route('play') }}" class="btn btn-lg">Start a practice game</a></div>
</div>

<div class="rules">
    <nav class="toc card">
        <strong>On this page</strong>
        <a href="#goal">1. The goal</a>
        <a href="#board">2. The board and setup</a>
        <a href="#start">3. Starting the game</a>
        <a href="#moving">4. Moving checkers</a>
        <a href="#doubles">5. Doubles</a>
        <a href="#hitting">6. Hitting and the bar</a>
        <a href="#bearing">7. Bearing off</a>
        <a href="#scoring">8. Winning and scoring</a>
        <a href="#controls">9. Playing on this site</a>
        <a href="#tips">10. Beginner strategy</a>
    </nav>

    <article class="card prose-wide">
        <section id="goal">
            <h2>1. The goal</h2>
            <p>Each player has <b>15 checkers</b>. You race them all the way around the board into your <b>home board</b>, then take them off the board ("bear off"). <b>The first player to bear off all 15 checkers wins.</b></p>
            <p>Here you play <b>White</b> and the computer plays <b>Black</b>. The two of you move in opposite directions, so your checkers keep running into each other. That's where the strategy comes in.</p>
        </section>

        <section id="board">
            <h2>2. The board and setup</h2>
            <div class="rules-figure">@include('partials.board-art')</div>
            <ul>
                <li>The board has <b>24 narrow triangles called points</b>, numbered 1–24 from your point of view.</li>
                <li>The board is split into four quarters of six points. Your <b>home board</b> is points <b>1–6</b>, in the bottom right.</li>
                <li>The wooden strip down the middle is the <b>bar</b>.</li>
                <li>You move <b>from point 24 down toward point 1</b>: counter-clockwise, top-right → top-left → bottom-left → bottom-right.</li>
                <li>Your starting position: 2 checkers on point 24, 5 on point 13, 3 on point 8 and 5 on point 6. Black's setup mirrors yours.</li>
            </ul>
        </section>

        <section id="start">
            <h2>3. Starting the game</h2>
            <p>Each player rolls one die. The higher roll goes first and moves using <b>both</b> numbers rolled. If the dice tie, they roll again. After that, players take turns rolling two dice.</p>
        </section>

        <section id="moving">
            <h2>4. Moving checkers</h2>
            <ul>
                <li><b>You can play the dice in either order.</b> Each die is a separate move. A roll of <b>5 and 3</b> means one checker moves 5 points and one moves 3. You can use them on two different checkers, or move one checker 5 and then 3 more (8 in total).</li>
                <li>You can move to any point that is <b>empty</b>, holds <b>your own checkers</b>, or holds <b>exactly one</b> opposing checker.</li>
                <li>You <b>cannot</b> land on a point that holds two or more opposing checkers. That point is <b>blocked</b>.</li>
                <li><b>You must use as much of your roll as you legally can.</b> If you can only use one die, you must use the higher one when possible. If you have no legal move at all, your turn is skipped.</li>
            </ul>
        </section>

        <section id="doubles">
            <h2>5. Doubles</h2>
            <p>Rolling the same number on both dice (for example 4-4) lets you move that number <b>four times</b> instead of twice. Doubles are the most powerful rolls in the game.</p>
        </section>

        <section id="hitting">
            <h2>6. Hitting and the bar</h2>
            <ul>
                <li>A single checker alone on a point is a <b>blot</b>. If your opponent lands on it, it's <b>hit</b> and sent to the <b>bar</b>.</li>
                <li>A hit checker must <b>re-enter</b> the game in the opponent's home board before any other move. Your checkers enter on points 24–19: a roll of 1 enters on 24, a 6 enters on 19.</li>
                <li>You can't enter on a blocked point. If both dice are blocked, you lose your turn.</li>
                <li>Two or more of your checkers on one point <b>make</b> that point. They're safe, and they block your opponent.</li>
            </ul>
        </section>

        <section id="bearing">
            <h2>7. Bearing off</h2>
            <ul>
                <li>Once <b>all 15</b> of your checkers are in your home board (points 1–6), you can start removing them.</li>
                <li>A roll of 4 can remove a checker from point 4, a roll of 6 from point 6, and so on.</li>
                <li>If no checker is on the rolled point, you must move a checker from a higher point if you can. If none are higher, you remove a checker from the highest point you have.</li>
                <li>If one of your checkers gets hit while you're bearing off, you have to bring it all the way home again before you can continue.</li>
            </ul>
        </section>

        <section id="scoring">
            <h2>8. Winning and scoring</h2>
            <table class="score-table">
                <tr><td><b>Single game</b> — your opponent has borne off at least one checker</td><td>1</td></tr>
                <tr><td><b>Gammon</b> — your opponent hasn't borne off any checkers</td><td>2</td></tr>
                <tr><td><b>Backgammon</b> — a gammon <i>and</i> your opponent still has a checker on the bar or in your home board</td><td>3</td></tr>
            </table>
            <p class="muted">On NQ Backgammon, wins add these points to your leaderboard total and losses subtract them.</p>
        </section>

        <section id="controls">
            <h2>9. Playing on this site</h2>
            <ol>
                <li>Press <b>Roll</b>, or the <kbd>Space</kbd> bar.</li>
                <li>Checkers you can move <b>glow gold</b>. Click one to select it.</li>
                <li>The points you can move it to light up. Click one to move.</li>
                <li>To move off the board, click the <b>tray</b> on the right edge when it lights up.</li>
                <li>Not sure what to do? Press <b>💡 Hint</b> (or <kbd>H</kbd>). The suggested move lights up green, with a short explanation of why it's good.</li>
                <li>Changed your mind? Press <b>Undo</b> before your last die is used.</li>
            </ol>
            <p>Your turn ends automatically when your dice are used up. The game also enforces all the rules for you, so you only ever get offered legal moves.</p>
        </section>

        <section id="tips">
            <h2>10. Beginner strategy</h2>
            <ul>
                <li><b>Make points, especially in your home board.</b> Points 5, 4 and 7 are the most valuable early on.</li>
                <li><b>Avoid leaving blots</b> within 6 points of an enemy checker. Those are the easiest shots for your opponent.</li>
                <li><b>Build a prime.</b> Six made points in a row form a wall your opponent can't get past.</li>
                <li><b>Hit when it's safe.</b> Sending a checker to the bar costs your opponent a lot of distance.</li>
                <li><b>Know when it's a race.</b> Once your checkers have all passed each other, just move as efficiently as possible. Watch the pip counts in the game panel to see who's ahead.</li>
                <li><b>Good opening moves:</b> 3-1 → make your 5 point (8/5, 6/5). 6-1 → make your 7 point (13/7, 8/7). 4-2 → make your 4 point (8/4, 6/4).</li>
            </ul>
        </section>

        <div class="final-cta" style="margin:40px 0 0">
            <h2>Ready to roll?</h2>
            <p>The best way to learn is to play, and the game won't let you make an illegal move.</p>
            <div class="cta-row center">
                <a href="{{ route('play') }}" class="btn btn-lg btn-light">Play now</a>
                @guest<a href="{{ route('register') }}" class="btn btn-lg btn-outline-light">Sign up free</a>@endguest
            </div>
        </div>
    </article>
</div>
@endsection
