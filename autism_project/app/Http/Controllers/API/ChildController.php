<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Middleware\Authenticate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Child;

class ChildController extends Controller
{
    public function dashboard()
    {

        $user = Auth::user();

        $parentProfile = $user->parentProfile;

        if (!$parentProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Parent profile record not found.'
            ], 44);
        }

        $children = $parentProfile->children()->get();


        $totalCount = $children->count();
        $severeCount = $children->where('has_severe_condition', true)->count();
        $stableCount = $totalCount - $severeCount;

        return response()->json([
            'success' => true,
            'data' => [
                'parent_profile' => $parentProfile,
                'children' => $children,
                'stats' => [
                    'total_count' => $totalCount,
                    'severe_count' => $severeCount,
                    'stable_count' => $stableCount,
                ]
            ]
        ], 200);
    }
    public function storeChild(Request $request)
    {
        $user = Auth::user();
        $parentProfile = $user->parentProfile;

        if (!$parentProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized processing. Parent profile context missing.'
            ], 403);
        }

        // Validate incoming payloads sent by the Flutter client application
        $validated = $request->validate([
            'full_name'              => 'required|string|max:255',
            'dob'                    => 'required|date_format:Y-m-d',
            'gender'                 => 'required|string|in:Male,Female',
            'autism_level'           => 'required|string|max:255',
            'behavioral_description' => 'nullable|string',
            'has_severe_condition'   => 'required|boolean',
            'medical_details'        => 'nullable|required_if:has_severe_condition,true|string',
        ]);


        // Write database tuple linked directly to active parent ID context
        $child = Child::create([
            'parent_profile_id'      => $parentProfile->id,
            'full_name'              => $validated['full_name'],
            'dob'                    => $validated['dob'],
            'gender'                 => $validated['gender'],
            'autism_level'           => $validated['autism_level'],
            'behavioral_description' => $validated['behavioral_description'] ?? null,
            'has_severe_condition'   => $validated['has_severe_condition'],
            'medical_details'        => $validated['has_severe_condition'] ? $validated['medical_details'] : null,
        ]);


        return response()->json([
            'success' => true,
            'message' => 'Child profile securely recorded.',
            'child'   => $child
        ], 201);
    }
    public function updateChild(Request $request, $id)
    {
        $user = Auth::user();
        $parentProfile = $user->parentProfile;

        if (!$parentProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized processing. Parent profile context missing.'
            ], 403);
        }


        $child = Child::find($id);

        if (!$child) {
            return response()->json([
                'success' => false,
                'message' => 'Child profile record not found.'
            ], 404);
        }

        if ($child->parent_profile_id !== $parentProfile->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. You do not have permission to modify this profile.'
            ], 403);
        }


        $validated = $request->validate([
            'full_name'              => 'required|string|max:255',
            'dob'                    => 'required|date_format:Y-m-d',
            'gender'                 => 'required|string|in:Male,Female',
            'autism_level'           => 'required|string|max:255',
            'behavioral_description' => 'nullable|string',
            'has_severe_condition'   => 'sometimes|required|boolean',
            'medical_details'        => 'nullable|required_if:has_severe_condition,true|string',
        ]);


        // If the parent switches severe condition to false, automatically wipe out older text data
        if (isset($validated['has_severe_condition']) && !$validated['has_severe_condition']) {
            $validated['medical_details'] = null;
        }

        // Update database attributes safely
        $child->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Child profile updated successfully.',
            'child'   => $child
        ], 200);
    }
}
