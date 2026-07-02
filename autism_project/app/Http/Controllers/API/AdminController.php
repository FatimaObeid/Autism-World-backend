<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ParentProfile;
use App\Models\Resource;
use App\Models\Specialist;
use App\Models\Volunteer;
use App\Models\Workshop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Log;

use function Pest\Laravel\json;

class AdminController extends Controller
{
    public function getDashboardData()
    {
        return response()->json([
            'specialists'      => Specialist::with('user')->get(),
            'parents'          => ParentProfile::with('user')->get(),
            'volunteers'       => Volunteer::with('user')->get(),
            'pendingWorkshops' => Workshop::with('volunteer.user')->where('status', 'pending')->get(),
            'resources'        => Resource::all(), // Assuming Resource model exists for bilingual assets
        ], 200);
    }
    public function saveVolunteer(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'phone'    => 'nullable|string|max:20',
            'activity' => 'nullable|string|max:255',
        ]);


        $volunteer = DB::transaction(function () use ($validated) {
            $user = User::create([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role'     => 'volunteer',
            ]);

            return Volunteer::create([
                'id'       => $user->id,
                'phone'    => $validated['phone'] ?? null,
                'activity' => $validated['activity'] ?? null,
            ])->load('user');
        });

        return response()->json([
            'message' => 'Volunteer registered successfully',
            'data'    => $volunteer
        ], 201);
    }
    public function updateVolunteer(Request $request, $id)
    {
        $volunteer = Volunteer::with('user')->findOrFail($id);
        $user = $volunteer->user;

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
            'phone'    => 'nullable|string|max:20',
            'activity' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($validated, $user, $volunteer) {
            $user->name = $validated['name'];
            $user->email = $validated['email'];
            if (!empty($validated['password'] ?? null)) {
                $user->password = Hash::make($validated['password']);
            }
            $user->save();

            $volunteer->update([
                'phone'    => $validated['phone'] ?? null,
                'activity' => $validated['activity'] ?? null,
            ]);
        });

        return response()->json([
            'message' => 'Volunteer updated successfully',
            'data'    => $volunteer->refresh()
        ], 200);
    }
    public function deleteVolunteer($id)
    {
        $volunteer = Volunteer::findOrFail($id);
        $user = User::find($volunteer->id);

        DB::transaction(function () use ($volunteer, $user) {
            $volunteer->delete();
            if ($user) {
                $user->delete();
            }
        });

        return response()->json(['message' => 'Volunteer profile deleted successfully'], 200);
    }
    public function approveWorkshop($id)
    {
        $workshop = Workshop::findOrFail($id);
        $workshop->update(['status' => 'approved']);

        return response()->json([
            'message' => "Workshop '{$workshop->title}' Approved to App Feed!"
        ], 200);
    }

    public function declineWorkshop($id)
    {
        $workshop = Workshop::findOrFail($id);
        $workshop->update(['status' => 'declined']);

        return response()->json([
            'message' => "Workshop request declined successfully"
        ], 200);
    }
    //specialist management
    public function approveSpecialist($id)
    {
        $specialist = Specialist::with('user')->findOrFail($id);
        $specialist->update(['status' => 'approved']);

        return response()->json([
            'success' => true,
            'message' => "Specialist '{$specialist->user->name}' has been approved successfully."
        ], 200);
    }

    public function declineSpecialist($id)
    {
        $specialist = Specialist::with('user')->findOrFail($id);
        $specialist->update(['status' => 'declined']);

        return response()->json([
            'success' => true,
            'message' => "Specialist request has been declined."
        ], 200);
    }
    public function saveSpecialist(Request $request)
    {
        $validated = $request->validate([
            'name'                => 'required|string|max:255',
            'email'               => 'required|email|unique:users,email',
            'password'            => 'required|string|min:6',
            'specialization'      => 'required|string|max:255',
            'license'             => 'nullable|string|max:255',
            'years_of_experience' => 'nullable|integer',
            'bio'                 => 'nullable|string',
            'location'            => 'nullable|string|max:255',
        ]);

        try {
            $specialist = DB::transaction(function () use ($validated) {
                // Create the base user account
                $user = User::create([
                    'name'     => $validated['name'],
                    'email'    => $validated['email'],
                    'password' => Hash::make($validated['password']),
                    'role'     => 'specialist',
                ]);

                // Create the specialist profile with status directly in create
                $profile = Specialist::create([
                    'id'                  => $user->id,
                    'specialization'      => $validated['specialization'] ?? null,
                    'license'             => $validated['license'] ?? null,
                    'years_of_experience' => $validated['years_of_experience'] ?? null,
                    'bio'                 => $validated['bio'] ?? null,
                    'location'            => $validated['location'] ?? null,
                    'status'              => 'approved', // Set status during creation
                ]);

                // Eager load the user relation
                return $profile->load('user');
            });

            return response()->json([
                'message' => 'Specialist registered successfully',
                'data'    => $specialist
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error saving specialist: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to save specialist',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function updateSpecialist(Request $request, $id)
    {
        $specialist = Specialist::with('user')->findOrFail($id);
        $user = $specialist->user;
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|unique:users,email,'  . $user->id,
            'password'       => 'nullable|string|min:6',
            'specialization' => 'required|string|max:255',
            'license'        => 'nullable|string|max:255',
            'years_of_experience' => 'nullable|integer',
            'bio' => 'nullable|string',
            'location' => 'nullable|string|max:255',
        ]);
        DB::transaction(function () use ($validated, $user, $specialist) {
            $user->name = $validated['name'];
            $user->email = $validated['email'];
            if (!empty($validated['password'] ?? null)) {
                $user->password = Hash::make($validated['password']);
            }
            $user->save();
            $specialist->update([
                'specialization' => $validated['specialization'],
                'license'        => $validated['license'] ?? null,
                'years_of_experience' => $validated['years_of_experience'] ?? null,
                'bio' => $validated['bio'] ?? null,
                'location' => $validated['location'] ?? null,
            ]);
        });
        return response()->json([
            'message' => 'Specialist updated successfully',
            'data' => $specialist->refresh()
        ], 200);
    }
    public function deleteSpecialist($id)
    {
        $specialist = Specialist::with('user')->findOrFail($id);
        $user = $specialist->user;

        DB::transaction(function () use ($specialist, $user) {
            $specialist->delete();
            if ($user) {
                $user->delete();
            }
        });

        return response()->json([
            'message' => 'Specialist profile deleted successfully'
        ], 200);
    }
    public function saveParent(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'dob'      => 'nullable|date' ?? null,
            'phone'    => 'required|string|max:20',
            'address'  => 'nullable|string|max:255',

        ]);
        $parentprofile = DB::transaction(function () use ($validated) {
            $user = User::create([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role'     => 'parent',
            ]);

            return ParentProfile::create([
                'id'      => $user->id,
                'dob'     => $validated['dob'] ?? null,
                'phone'   => $validated['phone'],
                'address' => $validated['address'] ?? null,

            ])->load('user');
        });
        return response()->json([
            'message' => 'Parent registered successfully',
            'data' => $parentprofile
        ], 201);
    }



    public function updateParent(Request $request, $id)
    {
        $parentprofile = ParentProfile::with('user')->findOrFail($id);
        $user = $parentprofile->user;
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|unique:users,email,'  . $user->id,
            'password' => 'nullable|string|min:6',
            'dob'     => 'nullable|date',
            'phone'   => 'required|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);
        DB::transaction(function () use ($validated, $user, $parentprofile) {
            $user->name = $validated['name'];
            $user->email = $validated['email'];
            if (!empty($validated['password'] ?? null)) {
                $user->password = Hash::make($validated['password']);
            }
            $user->save();
            $parentprofile->update([
                'dob' => $validated['dob'] ?? null,
                'phone'        => $validated['phone'],
                'address'        => $validated['address'] ?? null,
            ]);
        });

        return response()->json([
            'message' => 'Parent updated successfully',
            'data' => $parentprofile->refresh()
        ], 200);
    }

    public function deleteParent($id)
    {
        $parentprofile = ParentProfile::with('user')->findOrFail($id);
        $user = $parentprofile->user;

        DB::transaction(function () use ($parentprofile, $user) {
            $parentprofile->delete();
            if ($user) {
                $user->delete();
            }
        });

        return response()->json([
            'message' => 'Parent deleted successfully'
        ], 200);
    }
    public function saveResource(Request $request)
    {
        $validated = $request->validate([
            'title_en'       => 'nullable|string|max:255',
            'title_ar'       => 'nullable|string|max:255',
            'category_en'    => 'nullable|string|max:255',
            'category_ar'    => 'nullable|string|max:255',
            'description_en' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'icon'           => 'nullable|string|max:255',
        ]);

        $resource = Resource::create($validated);

        return response()->json([
            'message' => 'Resource shared successfully',
            'data'    => $resource
        ], 201);
    }
    public function updateResource(Request $request, $id)
    {
        $resource = Resource::find($id);

        if (!$resource) {
            return response()->json(['message' => 'Resource not found'], 404);
        }

        $validated = $request->validate([
            'title_en'       => 'nullable|string|max:255',
            'title_ar'       => 'nullable|string|max:255',
            'category_en'    => 'nullable|string|max:255',
            'category_ar'    => 'nullable|string|max:255',
            'description_en' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'icon'           => 'nullable|string|max:255',
        ]);

        $resource->update($validated);

        return response()->json([
            'message' => 'Resource updated successfully',
            'data'    => $resource
        ], 200);
    }


    public function deleteResource($id)
    {
        $resource = Resource::find($id);

        if (!$resource) {
            return response()->json(['message' => 'Resource not found'], 404);
        }
        $resource->delete();

        return response()->json([
            'success' => true,
            'message' => 'Resource deleted successfully'
        ], 200);
    }
}
