<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class SettingsController extends Controller
{
    /**
     * Fetch user profile data combined with their role-specific details.
     */
    public function getProfile(Request $request)
    {
        $user = $request->user();
        $profileData = [];

        // Dynamic checking based on your concrete database schemas
        if ($user->role === 'parent') {
            $parent = DB::table('parent_profiles')->where('id', $user->id)->first(['phone', 'address']);
            $profileData['phone'] = $parent->phone ?? '';
            $profileData['address'] = $parent->address ?? '';
        } elseif ($user->role === 'volunteer') {
            $volunteer = DB::table('volunteers')->where('id', $user->id)->first(['phone', 'activity']);
            $profileData['phone'] = $volunteer->phone ?? '';
            $profileData['activity'] = $volunteer->activity ?? '';
        } elseif ($user->role === 'specialist') {
            $specialist = DB::table('specialists')->where('id', $user->id)->first(['specialization', 'license']);
            $profileData['specialization'] = $specialist->specialization ?? '';
            $profileData['license'] = $specialist->license ?? '';
        }

        return response()->json([
            'status' => 'success',
            'user' => array_merge([
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role, // frontend checks role down to lowercase anyway
            ], $profileData)
        ], 200);
    }

    /**
     * Validate and update data dynamically inside users and role-specific tables.
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        // 1. Core validation rules
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        ];

        // 2. Add sub-table specific validation matching your migrations
        if ($user->role === 'parent') {
            $rules['phone'] = 'nullable|string|max:255';
            $rules['address'] = 'nullable|string|max:255';
        } elseif ($user->role === 'volunteer') {
            $rules['phone'] = 'nullable|string|max:255';
            $rules['activity'] = 'nullable|string|max:255';
        } elseif ($user->role === 'specialist') {
            $rules['specialization'] = 'nullable|string|max:255';
            $rules['license'] = 'nullable|string|max:255';
        }

        $request->validate($rules);

        // 3. Update core user table
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        // 4. Safely update sub-tables using DB transactions or direct upserts
        if ($user->role === 'parent') {
            DB::table('parent_profiles')->updateOrInsert(
                ['id' => $user->id],
                ['phone' => $request->phone, 'address' => $request->address, 'updated_at' => now()]
            );
        } elseif ($user->role === 'volunteer') {
            DB::table('volunteers')->updateOrInsert(
                ['id' => $user->id],
                ['phone' => $request->phone, 'activity' => $request->activity, 'updated_at' => now()]
            );
        } elseif ($user->role === 'specialist') {
            DB::table('specialists')->updateOrInsert(
                ['id' => $user->id],
                ['specialization' => $request->specialization, 'license' => $request->license, 'updated_at' => now()]
            );
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Profile updated successfully'
        ], 200);
    }

    /**
     * Change Password Logic
     */
    public function changePassword(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'current_password' => 'required',
            'new_password' => ['required', 'confirmed', Password::defaults()],
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'The provided password does not match your current password.'
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Password changed successfully'
        ], 200);
    }
}
