<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class VolunteerController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $volunteer = $user->volunteer;
        $volunteer = $user->$volunteer()->with('workshops')->first();

        if (!$volunteer) {
            abort(403, 'Volunteer profile not found.');
        }


        return response()->json([
            'dashboard_summary' => [
                'total_workshops'   => $volunteer->workshops->count(),
                'approved_count'    => $volunteer->workshops->where('status', 'approved')->count(),
                'pending_count'     => $volunteer->workshops->where('status', 'pending')->count(),
            ],
            'profile'   => $volunteer,
            'workshops' => $volunteer->workshops
        ], 200);
    }
    public function addWorkshop(Request $request, $id)
    {
        $user = Auth::user();
        $volunteer = $user->volunteer;
        if (!$volunteer) {
            return response()->json(['message' => 'Volunteer not found'], 404);
        }


        $validated = $request->validate([
            'title'     => 'required|string|max:255',
            'age_group' => 'required|string|max:100', // e.g., "Kids 8-12", "Teens"
            'location'  => 'required|string|max:255',
            'workshop_time' => 'required|date_format:H:i', // Expecting time in HH:MM format
            'date' => 'required|date|after:today', // Ensure the date is in the future
        ]);


        $workshop = $volunteer->workshops()->create($validated);

        return response()->json([
            'message' => 'Workshop submitted successfully and is now pending approval.',
            'data'    => $workshop
        ], 201);
    }
}
