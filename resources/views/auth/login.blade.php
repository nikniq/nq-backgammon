@extends('layouts.app')
@section('title', 'Log in · Backgammon')
@section('content')
<div class="card narrow">
    <h1>Log in</h1>
    @include('partials.social-login')
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <label>Email <input type="email" name="email" value="{{ old('email') }}" required autofocus></label>
        <label>Password <input type="password" name="password" required></label>
        <label class="check"><input type="checkbox" name="remember"> Remember me</label>
        @error('email')<p class="error">{{ $message }}</p>@enderror
        <button type="submit" class="btn">Log in</button>
    </form>
    <p class="muted">No account? <a href="{{ route('register') }}">Register</a> — or just <a href="{{ route('play') }}">play as a guest</a>.</p>
</div>
@endsection
