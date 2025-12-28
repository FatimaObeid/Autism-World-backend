<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Specialist;
use App\Models\ParentProfile;
use App\Models\User;
use Illuminate\Contracts\Support\ValidatedData;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function dashboard()
    {
        $specialists = Specialist::with('user')->get();
        $parents = ParentProfile::with('user')->get();
        return view('admin.dashboard', [
            'specialists' => $specialists,
            'parents'     => $parents,
        ]);
    }
    public function saveSpecialist(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'specialization' => 'required|string|max:255',
            'license'        => 'nullable|string|max:255',
        ]);
        $user = User::create([
            'name'   => $validated['name'],
            'email'  => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'    => 'doctor',
        ]);
        Specialist::create([
            'id'     => $user->id,
            'specialization'   => $validated['specialization'] ?? null,
            'license'          => $validated['license'] ?? null,
        ]);
        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Specialist has been successfully added');
    }
    public function updateSpecialist(Request $request, $id)
    {
        $specialist = Specialist::with('user')->findOrFail($id);
        $user = $specialist->user;
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|unique:users,email,'  . $user->id,
            'password'       => 'required|string|min:6',
            'specialization' => 'required|string|max:255',
            'license'        => 'nullable|string|max:255',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
        $user->save();
        $specialist->update([
            'specialization' => $validated['specialization'],
            'license'        => $validated['license'],
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Specialist updated successfully');
    }

    public function deleteSpecialist($id)
    {
        $specialist = Specialist::with('user')->findOrFail($id);
        $user = $specialist->user;
        $specialist->delete();
        $user->delete();
        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Specialist deleted successfully');
    }


    public function saveParent(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'dob'     => 'nullable|date',
            'phone'   => 'required|string|max:20',
            'address' => 'nullable|string|max:255',
            'gender'  => 'nullable|string|max:255',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => 'parent',
        ]);

        ParentProfile::create([
            'id'      => $user->id,
            'dob'     => $validated['dob'],
            'phone'   => $validated['phone'],
            'address' => $validated['address'] ?? null,
            'gender'  => $validated['gender'] ?? null,
        ]);
        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Parent has been added successfully');
    }

    public function updateParent(Request $request, $id)
    {
        $parentprofile = ParentProfile::with('user')->findOrFail($id);
        $user = $parentprofile->user;
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|unique:users,email,'  . $user->id,
            'password' => 'required|string|min:6',
            'dob'     => 'nullable|date',
            'phone'   => 'required|string|max:20',
            'address' => 'nullable|string|max:255',
            'gender'  => 'nullable|string|max:255',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
        $user->save();
        $parentprofile->update([
            'dob' => $validated['dob'],
            'phone'        => $validated['phone'],
            'address'        => $validated['address'],
            'gender'        => $validated['gender'],
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Parent updated successfully');
    }
    public function deleteParent($id)
    {
        $parentprofile = ParentProfile::with('user')->findOrFail($id);
        $user = $parentprofile->user;
        $parentprofile->delete();
        $user->delete();
        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Parent deleted successfully');
    }
}
