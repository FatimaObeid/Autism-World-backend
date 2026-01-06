@extends('layouts.app')
@section('content')
<header>
    <h1>Welcome,
        @if($specialist->specialization==='psychologist')
        Psychologist {{$user->name}}
        @elseif($specialist->specialization==='speech therapist')
        Speech Therapist {{$user->name}}
        @elseif($specialist->specialization==='Doctor')
        Dr. {{$user->name}}
        @endif
    </h1>
    <nav>
        <ul>
            <li><a href="{{route('home')}}">Home</a></li>
            <li> <a href="#appointments">View Appointments</a></li>
            <li><a href="#manage-app">Manage Appointments</a></li>

            <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit">Logout</button>
                </form>
            </li>
        </ul>
    </nav>

    <section id="appointments">
        <h2>Upcoming Appointments</h2>
        <table>
            <tr>
                <th>Parent</th>
                <th>Date</th>
            </tr>
            @foreach($appointments->where('status','approved') as $appointment)
            <tr>
                <td>{{$appointment->parentprofile->user->name}}</td>
                <td>{{$appointment->appointment_time}}</td>
            </tr>
            @endforeach
        </table>
    </section>
    <section id="manage-app">
        <h2>Manage Appointments</h2>
        <table>
            <tr>
                <th>Patient</th>
                <th>Specialist</th>
                <th>Date</th>
                <th>Status</th>
                <th>Actions</th>

            </tr>
            @foreach($appointments as $appointment)
            <tr>
                <td>{{$appointment->parentprofile->user->name}}</td>
                <td>{{$appointment->specialist->user->name}}</td>
                <td>{{$appointment->appointment_time}}</td>
                <td>{{ucfirst($appointment->status)}}</td>
                <td>
                    @if($appointment->status==='pending')
                    <form method="POST" action="{{ route('specialist.appointments.confirm',$appointment->id) }}" style="display:inline;">
                        @csrf
                        <button type="submit" onclick="showApproveMessage()">Approve</button>
                    </form>
                    <form method="POST" action="{{ route('specialist.appointments.decline', $appointment->id) }}" style="display:inline;">
                        @csrf
                        <button type="submit" onclick="showDeclineMessage()">Decline</button>
                    </form>
                    @else

                    @endif
                </td>
            </tr>
            @endforeach
        </table>
    </section>

</header>
@endsection