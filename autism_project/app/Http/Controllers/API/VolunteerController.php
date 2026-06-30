<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Volunteer;

class VolunteerController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $volunteer = Volunteer::where('id', $user->id)->with('workshops')->first();

        if (!$volunteer) {
            return response()->json(['message' => 'Volunteer profile not found.'], 403);
        }

        return response()->json([
            'dashboard_summary' => [
                'total_workshops'   => $volunteer->workshops->count(),
                'approved_count'    => $volunteer->workshops->where('status', 'approved')->count(),
                'pending_count'     => $volunteer->workshops->where('status', 'pending')->count(),
            ],
            'profile'   => [
                'id'       => $volunteer->id,
                'name'     => $volunteer->user ? $volunteer->user->name : 'Volunteer',
                'activity' => $volunteer->activity,
                'phone'    => $volunteer->phone ?? 'Not specified',
            ],
            'workshops' => $volunteer->workshops
        ], 200);
    }

    public function addWorkshop(Request $request)
    {
        $user = Auth::user();

        $volunteer = Volunteer::find($user->id);

        if (!$volunteer) {
            return response()->json(['message' => 'Volunteer profile record not found'], 404);
        }
        $volunteer->name = $volunteer->user ? $volunteer->user->name : 'Volunteer';

        $validated = $request->validate([
            'title'           => 'required|string|max:255',
            'age_group'       => 'nullable|string|max:100',
            'location'        => 'required|string|max:255',
            'workshop_time'   => 'required|date_format:H:i',
            'date'            => 'required|date|after_or_equal:today',
            'target_audience' => 'required|string|max:255',
        ]);


        $workshop = $volunteer->workshops()->create($validated);

        return response()->json([
            'message' => 'Workshop submitted successfully and is now pending approval.',
            'data'    => $workshop
        ], 201);
    }
}
