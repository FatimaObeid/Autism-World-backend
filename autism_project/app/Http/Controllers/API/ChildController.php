<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Specialist;
use App\Models\User;
use App\Models\ParentProfile; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Child;

class ChildController extends Controller
{
    /**
     * Fetch child list data for mobile selection lists
     */
    public function dashboard()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 401);
        }

        // Handle structural casing mismatch safe tracking fallback
        $parentProfile = $user->parentprofile ?? $user->parentProfile;
        if (!$parentProfile) {
            return response()->json(['success' => false, 'message' => 'Parent profile not found.'], 44);
        }

        $children = Child::where('parent_profile_id', $parentProfile->id)->get();

        return response()->json([
            'success' => true,
            'data' => [
                'parent_profile' => $parentProfile,
                'children' => $children,
            ]
        ], 200);
    }

    /**
     * Securely record a new child row
     */
    public function storeChild(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 401);
        }

        $parentProfile = $user->parentprofile ?? $user->parentProfile;
        if (!$parentProfile) {
            return response()->json(['success' => false, 'message' => 'Parent Profile missing.'], 403);
        }

        if ($request->has('has_severe_condition')) {
            $request->merge([
                'has_severe_condition' => filter_var($request->input('has_severe_condition'), FILTER_VALIDATE_BOOLEAN)
            ]);
        }

        $validated = $request->validate([
            'full_name'              => 'required|string|max:255',
            'dob'                    => 'required|date_format:Y-m-d',
            'gender'                 => 'required|string',
            'autism_level'           => 'required|string|max:255',
            'behavioral_description' => 'nullable|string',
            'has_severe_condition'   => 'required|boolean',
            'medical_details'        => 'nullable|string',
            'specialist_id'          => 'nullable|integer'
        ]);

        // Fallback context: auto-assign database placeholder specialist row if missing
        $specialistId = $validated['specialist_id'] ?? null;
        if (!$specialistId) {
            $fallback = \App\Models\User::where('id', '!=', $user->id)->first() ?? $user;
            $specialistId = $fallback->id;
        }

        $child = Child::create([
            'parent_profile_id' => $parentProfile->id,
            'specialist_id'     => $specialistId,
            'full_name'         => $validated['full_name'],
            'dob'               => $validated['dob'],
            'gender'            => strtolower($validated['gender']),
            'autism_level'      => $validated['autism_level'],
            'description'       => $validated['behavioral_description'],
            'has_other_disease' => $validated['has_severe_condition'] ? 'yes' : 'no',
            'medical_condition' => $validated['has_severe_condition'] ? $validated['medical_details'] : null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Child profile securely recorded.',
            'child'   => $child
        ], 201);
    }
}