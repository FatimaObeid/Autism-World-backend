<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Specialist;
use App\Models\ParentProfile; 
use App\Models\Volunteer;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required|in:Parent,Specialist,Volunteer',

            'date_of_birth' => 'nullable|date',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',

            'specialization' => 'nullable|string',
            'license_number' => 'nullable|string',

            'activity' => 'nullable|string',
            'location' => 'nullable|string',
        ]);

        // 1. Create the base User (Only save fields that actually exist on the users table!)
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            // FIXED: Removed date_of_birth, phone, and address from here to stop the users table crash!
        ]);

        // 2. Create the specialized profile based on Role
        if ($user->role === 'Specialist') {
            Specialist::create([
                'id' => $user->id, 
                'specialization' => $request->specialization ?? 'General',
                'license' => $request->license_number ?? 'N/A' 
            ]);
        }

        if ($user->role === 'Parent') {
            ParentProfile::create([ 
                'id' => $user->id, 
                'dob' => $request->date_of_birth, // Saves here correctly!
                'phone' => $request->phone ?? 'N/A', 
                'address' => $request->address ?? 'N/A' 
            ]);
        }

        if ($user->role === 'Volunteer') {
            // FIXED: Explicitly creating the volunteer sub-record with proper columns
            Volunteer::create([
                'id' => $user->id, 
                'name' => $user->name,
                'activity' => $request->activity ?? 'General Support', 
                'location' => $request->location ?? 'Remote', 
            ]); 
        }

        // 3. Issue Sanctum Token
        $token = $user->createToken('auth_token')->plainTextToken;
        
        return response()->json([
            'message' => 'Account created successfully',
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }
}