<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ParentProfile;
use App\Models\Child;


class ChildController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $parentProfile = $user->parentProfile;
        if ($parentProfile) {
            $child = $parentProfile->child;
        } else {
            $child = null;
        }
        return view('child.dashboard', [
            'parentProfile'  => $parentProfile,
            'child'          => $child,
        ]);
    }
    public function storeChild(Request $request)
    {
        $user = Auth::user();
        $parentProfile = $user->parentProfile;

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'dob'        => 'required|date',
            'gender'     => 'nullable|string',
            'autism_type' => 'nullable|string|max:255',
        ]);
        Child::create([
            'parent_profile_id'  => $parentProfile->id,
            'first_name'         => $validated['first_name'],
            'last_name'          => $validated['last_name'],
            'dob'                => $validated['dob'],
            'gender'             => $validated['gender'],
            'autism_type'        => $validated['autism_type'],
        ]);
        return redirect()
            ->route('parentprofile.dashboard')
            ->with('success', 'Child created successfully');
    }
    public function update(Request $request)
    {
        $user = Auth::user();
        $parentProfile = $user->parentProfile;


        $child = $parentProfile->child;

        $validated = $request->validate([
            'first_name'  => 'required|string|max:255',
            'last_name'   => 'required|string|max:255',
            'dob'         => 'required|date',
            'gender'      => 'nullable|string',
            'autism_type' => 'nullable|string|max:255',
        ]);


        $child->update([
            'first_name'  => $validated['first_name'],
            'last_name'   => $validated['last_name'],
            'dob'         => $validated['dob'],
            'gender'      => $validated['gender'],
            'autism_type' => $validated['autism_type'],
        ]);

        return redirect()
            ->route('parentprofile.dashboard')
            ->with('success', 'Child updated successfully');
    }
}
