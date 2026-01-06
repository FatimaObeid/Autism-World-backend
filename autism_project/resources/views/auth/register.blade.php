@extends('layouts.app')
@section('content')

<header>
    <h1>Register</h1>
</header>
@if ($errors->any())
<div style="color: red;">
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
<div>

    <form action="{{ route('register.submit') }}" method="POST" onsubmit="return ValidateRegisterform()">
        @csrf
        <label for="name">Full Name:</label>
        <input type="text" name="name" value="{{ old('name') }}" id="name" placeholder="Please enter your name:">
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="Enter your Email:">
        <label for="password">Password:</label>
        <input type="password" name="password" id="password" placeholder=" Enter a password:">
        <label for="role">Register As</label>
        <select name="role" id="role" onchange="updateRoleFields(this)">
            <option value="" disabled selected>Select your role</option>
            <option value="parent">Parent</option>
            <option value="specialist">Specialist</option>
        </select>
        <div id="role-specific-fields"></div>
        <button type="submit">Register</button>
    </form>
    <p>Already have an account?<a href="{{ route('login') }}">Login here</a></p>
</div>
@endsection