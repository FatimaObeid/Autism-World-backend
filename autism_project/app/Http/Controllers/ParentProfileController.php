<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Specialist;
use App\Models\ParentProfile;
use App\Models\Appointment;

class ParentProfileController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $parentprofile = $user->parentprofile;
        $specialists = Specialist::with('user')->get();
        $appointments = Appointment::with(['specialist.user'])
            ->where('parent_id', $parentprofile->id)
            ->orderBy('appointment_time', 'asc')
            ->get();

        return view('parentprofile.dashboard', [
            'user' => $user,
            'parentprofile' => $parentprofile,
            'specialists' => $specialists,
            'appointments' => $appointments
        ]);
    }

    public function storeAppointment(Request $request)
    {
        $user = Auth::user();
        $parentprofile = $user->parentprofile;

        $validated = $request->validate([
            'specialist_id' => 'required|exists:specialists,id',
            'appointment_time' => 'required|date|after:now',
            'phone' => 'required|string|max:255',
            'address' => 'required|string|max:255',
        ]);
        $parentprofile->update([
            'phone' => $validated['phone'],
            'address' => $validated['address'],
        ]);

        Appointment::create([
            'parent_id' => $parentprofile->id,
            'specialist_id' => $validated['specialist_id'],
            'appointment_time' => $validated['appointment_time'],
            'status' => 'pending',

        ]);

        return redirect()
            ->route('parentprofile.dashboard')
            ->with('success', 'Appointment booked successfully and is pending approval.');
    }

    public function updateAppointment(Request $request, $id)
    {
        $user = Auth::user();
        $parentProfile = $user->parentProfile;
        $appointment = Appointment::where('id', $id)
            ->where('parent_id', $parentProfile->id)
            ->firstOrFail();

        $validated = $request->validate([
            'specialist_id' => 'required|exists:specialists,id',
            'appointment_time' => 'required|date|after:now',
            'phone' => 'required|string|max:255',
            'address' => 'required|string|max:255',
        ]);
        $parentProfile->update([
            'phone' => $validated['phone'],
            'address' => $validated['address'],
        ]);

        $appointment->update([
            'specialist_id' => $validated['specialist_id'],
            'appointment_time' => $validated['appointment_time'],
            'status' => 'pending',

        ]);
        return redirect()
            ->route('parentprofile.dashboard')
            ->with('success', 'Appointment updated successfully.');
    }

    public function deleteAppointment($id)
    {
        $user = Auth::user();
        $parentProfile = $user->parentProfile;
        $appointment = Appointment::where('id', $id)
            ->where('parent_id', $parentProfile->id)
            ->firstOrFail();

        $appointment->delete();

        return redirect()
            ->route('parentprofile.dashboard')
            ->with('success', 'Appointment cancelled successfully.');
    }
}
