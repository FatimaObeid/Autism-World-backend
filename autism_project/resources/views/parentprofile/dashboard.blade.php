@extends('layouts.app')
@section('content')
<header>
<h1>Welcome, {{ $user->name }}</h1>
<nav>
<ul>
<li><a href="{{ route('home') }}">Home</a></li>
<li><a href="#book">Book Appointment</a></li>
<li><a href="#manage">Manage Appointments</a></li>
<li><form method="POST" action="{{route('logout')}}">
    @csrf
    <button type="submit">Logout</button>
    </form>
</li>

</ul>
</nav>
</header>

<section id="book">
<h2>Book an Appointment</h2>
<form method="POST" action="{{ route('patient.appointments.store') }}">
@csrf
<label for="doctor">Choose a Doctor:</label>
<select id="doctor" name="doctor_id"
onchange="showDoctorInfo(this.value)" required>

<option value="" disabled selected>Select Doctor</option>
@foreach ($doctors as $doctor) <option value="{{ $doctor->id }}">
{{ $doctor->user->name }} ({{ $doctor->specialization }}) </option> @endforeach

</select>
<label for = "date">Select Date:</label>
<input type="datetime-local" id="date" name="appointment_time"

onchange="validateAppointmentDate(this)" required>

<button type="submit" onclick="showBookConfirmation()">Book</button>
</form>
</section>

<section id="manage">
<h2>Your Appointments</h2>
<table>
<tr>
<th>Doctor</th>
<th>Date</th>
<th>Status</th>
<th>Actions</th>
</tr>
@foreach ($appointments as $appointment)
<tr>
<td>{{ $appointment->doctor->user->name }}</td>
<td>{{ $appointment->appointment_time }}</td>
<td>{{ ucfirst($appointment->status) }}</td>
<td>
<button onclick="openUpdateModal(
'{{ $appointment->id }}',
'{{ $appointment->doctor_id }}',
'{{ $appointment->appointment_time }}'
)">
Update
</button>
<form method="POST"
action="{{ route('patient.appointments.cancel',

$appointment->id) }}"

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
<form method="POST" id="update-form">
@csrf
@method('PUT')
<label>Appointment ID:</label>
<input type="hidden" id="appointment_id_update">
<label>Choose a New Doctor:</label>
<select name="doctor_id" id="doctor_update"> @foreach ($doctors as
$doctor) <option value="{{ $doctor->id }}"> {{ $doctor->user->name }} </option>
@endforeach </select>

<label>Select New Date:</label>
<input type="datetime-local" name="appointment_time" id="date_update"

required>

<button type="submit" onclick = "showUpdateConfirmation()">Update

Appointment</button>

<button type="button" onclick="closeUpdateModal()">Close</button>
</form>
</div>
@endsection
