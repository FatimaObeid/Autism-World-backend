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
            'name'     => 'required| string |max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:parentprofile,specialist',

            'dob' => 'nullable|date',
            'phonenumber'    => 'nullable|string|max:255',
            'address'        => 'nullable|string|max:255',
            'specialization' => 'nullable|string|max:255',
            'license'        => 'nullable|string|max:255',


        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $validated['role'],

        ]);

        if ($validated['role'] === 'parentprofile') {
            ParentProfile::create([
                'id' => $user->id,
                'dob' => $validated['dob'] ?? null,
                'phonenumber' => $validated['phonenumber'] ?? null,
                'address' => $validated['address'] ?? null,

            ]);
        } else if ($validated['role'] === 'specialist') {
            Specialist::create([
                'id' => $user->id,
                'specialization' => $validated['specialization'] ?? null,
                'license' => $validated['license'] ?? null,
            ]);
        }

        Auth::login($user);

        return redirect()->route(
            $user->isSpecialist() ? 'specialist.dashboard' : 'parentprofile.dashboard'
        );
    }
}
