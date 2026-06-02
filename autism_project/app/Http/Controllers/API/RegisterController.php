<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
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

            'volunteer_type' => 'nullable|string',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],

            'date_of_birth' => $request->date_of_birth,
            'phone' => $request->phone,
            'address' => $request->address,

            'specialization' => $request->specialization,
            'license_number' => $request->license_number,

            'volunteer_type' => $request->volunteer_type,
        ]);
        if ($user->role === 'Specialist') {
            \App\Models\Specialist::create([
                'id' => $user->id, // Assign the foreign key/primary key relationship
                'specialization' => $request->specialization ?? 'General',
                'license' => $request->license_number ?? 'N/A' // Matches your $fillable in Specialist.php
            ]);
        }
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'message' => 'Account created successfully',
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }
}
