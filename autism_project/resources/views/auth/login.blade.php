@extends('layouts.app')
@section('content')
<header>
    <div>
        @if($errors->any())
        <p style="color:red;">
            {{ $errors->first() }}
        </p>
        @endif
        <form method="POST" action="{{ route('login.submit') }}">
            @csrf
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="Enter your email">
            <br>
            <label for="password">Passsword:</label>
            <input type="password" id="password" name="password" placeholder="Enter your password">
            <br>
            <button type="submit" name="submit">Login</button>
            <p>Don't have an account <a href="{{ route('register') }}" target="_blank">Register</a></p>
        </form>
    </div>
</header>
@endsection