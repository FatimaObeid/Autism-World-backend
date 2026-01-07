<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use App\Models\ParentProfile;
use App\Models\Specialist;


class RegisteredUserController extends Controller
{

    public function showRegisterForm(): View
    {
        return view('auth.register');
    }


    public function register(Request $request)
    {

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role'     => 'required|in:parent,specialist',

            'dob'            => 'nullable|date',
            'phone'          => 'nullable|string|max:255',
            'specialization' => 'nullable|string|max:255',
            'license'        => 'nullable|string|max:255',


        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => $validated['role'],

        ]);

        if ($validated['role'] === 'parent') {
            ParentProfile::create([
                'id'     => $user->id,
                'dob'    => $validated['dob'] ?? null,
                'phone'  => $validated['phone'] ?? null,

            ]);
        } else if ($validated['role'] === 'specialist') {
            Specialist::create([
                'id'             => $user->id,
                'specialization' => $validated['specialization'] ?? null,
                'license'        => $validated['license'] ?? null,
            ]);
        }

        Auth::login($user); //login the user after registration

        return redirect()->route(
            $user->isSpecialist() ? 'specialist.dashboard' : 'parentprofile.dashboard'
        );
    }
}
