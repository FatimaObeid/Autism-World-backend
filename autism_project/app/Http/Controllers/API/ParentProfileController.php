<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Child;
use App\Models\CommunityEvent;
use App\Models\DailyProgress;
use App\Models\Workshop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ParentProfileController extends Controller
{
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

        $children = Child::where('parent_profile_id', $parentProfile->id)->get();

        $upcomingAppointment = Appointment::with(['specialist.user', 'child'])
            ->where('parent_profile_id', $parentProfile->id)
            ->where('appointment_time', '>=', Carbon::now())
            ->orderBy('appointment_time', 'asc')
            ->first();

        return response()->json([
            'success' => true,
            'parent_name' => $user->name,
            'upcoming_appointment' => $upcomingAppointment,
            'children' => $children
        ], 200);
    }

    public function bookAppointment(Request $request)
    {
        $user = Auth::user();
        // Always extract the actual database profile ID linked to this authenticated user account
        $parentProfile = $user->parentprofile ?? $user->parentProfile;

        if (!$parentProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Parent profile not found for this user account.'
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

        // Safety verification: Ensure the child actually belongs to this parent profile before booking
        $childExists = \App\Models\Child::where('id', $validated['child_id'])
            ->where('parent_profile_id', $parentProfile->id)
            ->exists();

        if (!$childExists) {
            return response()->json([
                'success' => false,
                'message' => "The selected child ID ({$validated['child_id']}) does not exist or belong to your profile."
            ], 422);
        }

        $appointment = Appointment::create([
            'parent_profile_id' => $parentProfile->id, // Uses the solid verified relationship ID
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

    public function specialists()
    {
        // 1. Fetch ALL approved specialists from the table
        $specialists = \App\Models\Specialist::where('status', 'approved')->with('user')->get();

        $formattedSpecialists = $specialists->map(function ($spec) {
            return [
                'id' => $spec->id,
                // Check multiple potential column names for therapy type or specialization
                'therapy_type' => $spec->therapy_type ?? $spec->specialization ?? 'General Therapy',
                'experience_years' => $spec->experience_years ?? $spec->years_of_experience ?? 0,
                'user' => [
                    'id' => $spec->user->id ?? 0,
                    'name' => $spec->user->name ?? 'Pending Name Assignment',
                    'email' => $spec->user->email ?? 'No email found'
                ]
            ];
        });

        return response()->json([
            'success' => true,
            'specialists' => $formattedSpecialists
        ], 200);
    }

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
            $resources = DB::table('resources')
                ->orderBy('created_at', 'desc')
                ->get();

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

    public function workshops()
    {
        $user = Auth::user();
        $parentProfile = $user->parentprofile ?? $user->parentProfile;

        if (!$parentProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Parent profile not found.'
            ], 404);
        }

        $workshops = DB::table('workshops')
            ->whereIn('target_audience', ['Parents', 'Both'])
            ->orderBy('date', 'asc')
            ->get();

        $formattedWorkshops = $workshops->map(function ($workshop) use ($parentProfile) {
            $hasApproved = DB::table('parent_workshop')
                ->where('parent_profile_id', $parentProfile->id)
                ->where('workshop_id', $workshop->id)
                ->exists();

            return [
                'id' => $workshop->id,
                'title_en' => $workshop->title_en,
                'title_ar' => $workshop->title_ar,
                'location_en' => $workshop->location_en,
                'location_ar' => $workshop->location_ar,
                'date' => $workshop->date . ' at ' . $workshop->time,
                'target_audience' => $workshop->target_audience,
                'is_parent_approved' => $hasApproved
            ];
        });

        return response()->json([
            'success' => true,
            'workshops' => $formattedWorkshops
        ], 200);
    }

    public function approveAttendance($id)
    {
        $user = Auth::user();
        $parentProfile = $user->parentprofile ?? $user->parentProfile;

        if (!$parentProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Parent profile not found.'
            ], 404);
        }

        $exists = DB::table('parent_workshop')
            ->where('parent_profile_id', $parentProfile->id)
            ->where('workshop_id', $id)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'You have already approved and registered for this event.'
            ], 400);
        }

        DB::table('parent_workshop')->insert([
            'parent_profile_id' => $parentProfile->id,
            'workshop_id' => $id,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Attendance approved and seat reserved successfully!'
        ], 200);
    }

    /**
     * Fetch community events filtered for parents.
     */
    public function events()
    {
        // Adjust column values to match exactly what you named them in your DB schema.
        // We look for 'approved' status and ensure it's meant for parents.
        $events = Workshop::where('status', 'approved')
            ->whereIn('target_audience', ['parent', 'Parents', 'Both'])
            ->orderBy('date', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'events' => $events
        ], 200);
    }
}
