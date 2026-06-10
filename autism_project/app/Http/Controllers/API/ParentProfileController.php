<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Child;
use App\Models\CommunityEvent;
use App\Models\DailyProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ParentProfileController extends Controller
{
    /**
     * Parent Dashboard
     */
    /**
     * Parent Dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();
        $parentProfile = $user->parentprofile ?? $user->parentProfile;

        if (!$parentProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Parent profile not found.'
            ], 404);
        }

        // Get children linked to this parent profile
        $children = Child::where('parent_profile_id', $parentProfile->id)->get();

        // Find the single closest upcoming appointment that is scheduled for today or later
        $upcomingAppointment = Appointment::with(['specialist.user', 'child'])
            ->where('parent_profile_id', $parentProfile->id)
            ->where('appointment_time', '>=', Carbon::now())
            ->orderBy('appointment_time', 'asc')
            ->first();

        return response()->json([
            'success' => true,
            'parent_name' => $user->name, // Explicitly pass the registered parent name
            'upcoming_appointment' => $upcomingAppointment, // Real booked appointment metadata
            'children' => $children
        ], 200);
    }
    /**
     * Book Appointment Profile Request Handler
     * Customized to accept text fields (child_name & child_age) from the original Flutter view.
     */
   public function bookAppointment(Request $request)
{
    $user = Auth::user();
    $parentProfile = $user->parentprofile ?? $user->parentProfile;

    if (!$parentProfile) {
        return response()->json([
            'success' => false,
            'message' => 'Parent profile not found.'
        ], 404);
    }

    $validated = $request->validate([
        'child_id'         => 'required|integer',
        'specialist_id'    => 'required|integer',
        'appointment_time' => 'required|date_format:Y-m-d H:i:s',
        'therapy_type'     => 'required|string',
        'phone'            => 'required|string',
        'notes'            => 'nullable|string',
    ]);

    $appointment = Appointment::create([
        'parent_profile_id' => $parentProfile->id,
        'specialist_id'     => $validated['specialist_id'],
        'child_id'          => $validated['child_id'],
        'appointment_time'  => $validated['appointment_time'],
        'type'              => $validated['therapy_type'],
        'status'            => 'pending',
        'notes'             => $validated['notes'] ?? null,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Appointment successfully requested and pending confirmation.',
        'appointment' => $appointment
    ], 201);
} 
    /**
     * Fetch list of all registered specialists
     */
    public function specialists()
    {
        // Fetch specialists with eager-loaded user accounts
        $specialists = \App\Models\Specialist::with('user')->get();

        // Transform results to ensure fallbacks exist if relations are empty during local testing
        $formattedSpecialists = $specialists->map(function ($spec) {
            return [
                'id' => $spec->id,
                'therapy_type' => $spec->therapy_type ?? 'General Therapy',
                'experience_years' => $spec->experience_years ?? 0,
                'user' => [
                    'id' => $spec->user ? $spec->user->id : $spec->id,
                    'name' => $spec->user ? $spec->user->name : 'Specialist Session',
                    'email' => $spec->user ? $spec->user->email : 'specialist@example.com'
                ]
            ];
        });

        return response()->json([
            'success' => true,
            'specialists' => $formattedSpecialists
        ], 200);
    }

    /**
     * Daily Progress Summary Endpoint
     */
    public function dailyProgress(Request $request)
{
    $validated = $request->validate([
        'child_id'           => 'required|integer|exists:children,id',
        'date'               => 'required|date',
        'mood_level'         => 'required|integer|min:1|max:5',
        'sensory_play'       => 'required|boolean',
        'social_interaction' => 'required|boolean',
        'notes'              => 'nullable|string',
    ]);

    $progress = \App\Models\DailyProgress::create($validated);

    return response()->json([
        'success' => true,
        'message' => 'Daily metrics recorded successfully.',
        'data'    => $progress
    ], 201);
}

public function resources()
{
    try {
        // Query rows directly from the resources table
        $resources = \DB::table('resources')
            ->orderBy('created_at', 'desc')
            ->get();

        // CRITICAL FIX: Pass the collection directly into the json response array
        return response()->json([
            'success' => true,
            'message' => 'Resources loaded successfully.',
            'data'    => $resources
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to retrieve resources.',
            'error'   => $e->getMessage()
        ], 500);
    }
}
}