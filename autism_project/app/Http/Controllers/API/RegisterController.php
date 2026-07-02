<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required|in:Parent,Specialist,Volunteer,parent,specialist,volunteer',

            'date_of_birth' => 'nullable|date',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',

            'specialization' => 'nullable|string',
            'license_number' => 'nullable|string',

            // Specialist-specific fields sent by the Flutter registration form
            'experience_years' => 'nullable|integer',
            'clinic_location'  => 'nullable|string',
            'description'      => 'nullable|string',

            'volunteer_type' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $roleLowercase = strtolower($validated['role']);
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $roleLowercase,
            ]);

            if ($roleLowercase === 'specialist') {
                \App\Models\Specialist::create([
                    'id'                  => $user->id,
                    'specialization'      => $request->specialization ?? 'General',
                    'license'             => $request->license_number ?? 'N/A',
                    'years_of_experience' => $request->experience_years ?? 0,
                    'location'            => $request->clinic_location ?? null,
                    'bio'                 => $request->description ?? null,
                    'status'              => 'pending',
                ]);
            } elseif ($roleLowercase === 'volunteer') {
                \App\Models\Volunteer::create([
                    'id' => $user->id,
                    'activity' => $request->volunteer_type ?? 'General',
                    'phone' => $request->phone ?? 'Not Specified',
                ]);
            } elseif ($roleLowercase === 'parent') {
                \App\Models\ParentProfile::create([
                    'id' => $user->id,
                    'dob' => $request->date_of_birth ?? null,
                    'phone' => $request->phone ?? 'Not Specified',
                    'address' => $request->address ?? null,
                ]);
            }

            DB::commit();

            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'message' => 'Account created successfully',
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer',
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Registration failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
