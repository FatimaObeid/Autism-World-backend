<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\VolunteeringOpportunity;

class HomeController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }

            if ($user->isSpecialist()) {
                return redirect()->route('specialist.dashboard');
            }

            if ($user->isParent()) {
                return redirect()->route('parentprofile.dashboard');
            }
        }

        return view('home.index');
    }
    public function storeVolunteer(Request $request)
    {

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'activity' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'phone'    => 'nullable|string|max:20',

        ]);


        VolunteeringOpportunity::create([
            'name'     => $validated['name'],
            'activity' => $validated['activity'],
            'location' => $validated['location'],
            'phone'    => $validated['phone'] ?? null,
        ]);

        return redirect()
            ->back()
            ->with('success', 'Thank you for volunteering!');
    }
}
