@extends('layouts.app')
@section('title', 'Register · Backgammon')
@section('content')
<div class="card narrow">
    <h1>Create an account</h1>
    <p class="muted">Registered players have their results saved and appear on the leaderboard.</p>
    <form method="POST" action="{{ route('register') }}">
        @csrf
        <label>Display name <input type="text" name="name" value="{{ old('name') }}" required maxlength="50" autofocus></label>
        @error('name')<p class="error">{{ $message }}</p>@enderror
        <label>Email <input type="email" name="email" value="{{ old('email') }}" required></label>
        @error('email')<p class="error">{{ $message }}</p>@enderror
        <label>Password <input type="password" name="password" required minlength="8"></label>
        @error('password')<p class="error">{{ $message }}</p>@enderror
        <label>Confirm password <input type="password" name="password_confirmation" required></label>
        <button type="submit" class="btn">Register</button>
    </form>
    <p class="muted">Already registered? <a href="{{ route('login') }}">Log in</a></p>
</div>
@endsection
