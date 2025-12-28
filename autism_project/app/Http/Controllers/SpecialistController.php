<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SpecialistController extends Controller
{
    public function dashboard(){
        $user=Auth::user();
        $specialist=$user->specialist;
        $appointments=Appointment::with(['parentprofile.user'])
        ->where('specialist_id',$specialist->id)
        ->orderBy('appointment_time','asc')
        ->get();

        return view('specialist.dashboard',[
            'user'=>$user,
            'specialist'=>$specialist,
            'appointments'=>$appointments,
        ]);
    }

    public function confirmAppointment($id){
        $user=Auth::user();
        $specialist=$user->doctor;

        $appointment=Appointment::where('id',$id)
        ->where('specialist_id',$specialist_id)
        ->firstOrFail();

        $appointment->update(['status'=>'approved']);

        return redirect()
        ->route('specialist.dashboard')
        ->with('success','Appointment approved successfully.');
    }

    public function declineAppointment($id){
        $user=Auth::user();
        $specialist=$user->specialist;

        $appointment=Appointment::where('id',$id)
        ->where('specialist_id',$specialist_id)
        ->firstOrFail();

        $appointment->update(['status'=>'declined']);

        return redirect()
        ->route('specialist.dashboard')
        ->with('success','Appointment declined.');
    }
}
