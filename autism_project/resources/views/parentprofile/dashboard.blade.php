@extends('layouts.app')
@section('content')
<header>
    <h1>Welcome, {{ $user->name }}</h1>
    <nav>
        <ul>
            <li><a href="{{ route('home') }}">Home</a></li>
            <li><a href="#book">Book Appointment</a></li>
            <li><a href="#manage">Manage Appointments</a></li>
            <li>
                <form method="POST" action="{{route('logout')}}">
                    @csrf
                    <button type="submit">Logout</button>
                </form>
            </li>

        </ul>
    </nav>
</header>

<section id="book">
    <h2>Book an Appointment</h2>
    <form method="POST" action="{{ route('parentprofile.appointments.store') }}">
        @csrf
        <label for="specialist">Choose a Specialist:</label>
        <select id="specialist" name="specialist_id" required>

            <option value="" disabled selected>Select Specialist</option>
            @foreach ($specialists as $specialist)
            <option value="{{ $specialist->id }}">
                {{ $specialist->user->name }} ({{ $specialist->specialization }})
            </option>
            @endforeach

        </select>
        <label for="date">Select Date:</label>
        <input type="datetime-local" id="date" name="appointment_time" onchange="validateAppointmentDate(this)" required>

        <label for="phone">Phone number:</label>
        <input type="text" id="phone" name="phone" required>
        <label for="address">Home Address:</label>
        <input type="text" id="address" name="address" required>

        <button type="submit" onclick="showBookConfirmation()">Book</button>
    </form>
</section>

<section id="manage">
    <h2>Your Appointments</h2>
    <table>
        <tr>
            <th>Specialist</th>
            <th>Date</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        @foreach ($appointments as $appointment)
        <tr>
            <td>{{ $appointment->specialist->user->name ?? 'Specialist deleted' }}</td>
            <td>{{ $appointment->appointment_time }}</td>
            <td>{{ ucfirst($appointment->status) }}</td>
            <td>
                <button onclick="openUpdateModal(
                  '{{ $appointment->id }}',
                  '{{ $appointment->specialist_id }}',
                  '{{ $appointment->appointment_time }}'
            )">
                    Update
                </button>
                <form method="POST"
                    action="{{ route('parentprofile.appointments.delete',$appointment->id) }}"



                    style="display:inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        onclick="showDeleteConfirmation()">Delete</button>

                </form>
            </td>
        </tr>
        @endforeach
    </table>
</section>

<div id="update-modal" style="display: none;" class="modal-content">
    <h2>Update Appointment</h2>
    <form method="POST" id="update-form" data-base-url="/parent/appointments/">
        @csrf
        @method('PUT')
        <label>Appointment ID:</label>
        <input type="hidden" id="appointment_id_update">
        <label>Choose a New Specialist:</label>
        <select name="specialist_id" id="specialist_update">
            @foreach ($specialists as $specialist)
            <option value="{{ $specialist->id }}"> {{ $specialist->user->name }} </option>
            @endforeach
        </select>

        <label>Select New Date:</label>
        <input type="datetime-local" name="appointment_time" id="date_update"

            required>

        <button type="submit" onclick="showUpdateConfirmation()">Update

            Appointment</button>

        <button type="button" onclick="closeUpdateModal()">Close</button>
    </form>
</div>

<a href="{{ route('child.dashboard') }}" class="btn">
    Your Child's Profile
</a>


<section id="tips">
    <h2>Helpful Tips for Parents</h2>
    <ul>
        <li>💙 Keep a consistent daily routine</li>
        <li>🗣️ Encourage simple communication</li>
        <li>📅 Attend therapy sessions regularly</li>
        <li>🌟 Celebrate small progress</li>
    </ul>
</section>
@endsection