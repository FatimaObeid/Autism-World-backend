<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Child;
use App\Models\CommunityEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class SpecialistController extends Controller
{

    public function dashboard()
    {
        $user = Auth::user();
        $specialist = $user->specialist;
        if (!$specialist) {
            return response()->json(['error' => 'Specialist profile not found.'], 404);
        }
        $specialistName = $specialist->user->name;
        $specialization = $specialist->specialization;
        $yearsOfExperience = $specialist->years_of_experience;
        $bio = $specialist->bio;
        $location = $specialist->location;
        $todayAppointments = $specialist->appointments()
            ->whereDate('appointment_time', today())
            ->count();

        $upcomingAppointments = $specialist->appointments()
            ->with('child')
            ->where('appointment_time', '>', now())
            ->orderBy('appointment_time', 'asc')
            ->get();
        $nextAppointment = $upcomingAppointments->first();
        $nextAppointmentData = null;
        if ($nextAppointment) {
            $nextAppointmentData = [
                'time' => $nextAppointment->appointment_time->format('h:i A'),
                'starts_in' => $this->getTimeUntil($nextAppointment->appointment_time),
                'type' => $nextAppointment->type,
                'child_name' => $nextAppointment->child->full_name,

            ];
        }


        return response()->json([
            'specialist_name' => $specialistName,
            'specialization' => $specialization,
            'years_of_experience' => $yearsOfExperience,
            'bio' => $bio,
            'location' => $location,
            'next_appointment' => $nextAppointmentData,
            'today_appointments' => $todayAppointments
        ]);
    }

    private function getTimeUntil($appointmentTime)
    {
        $diffInMinutes = now()->diffInMinutes($appointmentTime, false);

        if ($diffInMinutes <= 0) {
            return "Now";
        } elseif ($diffInMinutes < 60) {
            return "Starts in {$diffInMinutes} mins";
        } else {
            $hours = floor($diffInMinutes / 60);
            $minutes = $diffInMinutes % 60;
            if ($minutes > 0) {
                return "Starts in {$hours}h {$minutes}m";
            }
            return "Starts in {$hours} hours";
        }
    }
    public function getPendingRequests()
    {
        $specialist = Auth::user()->specialist;

        $pending = Appointment::where('specialist_id', $specialist->id)
            ->where('status', 'pending')
            ->with('child.parent.user')
            ->orderBy('appointment_time', 'asc')
            ->get();

        $formatted = $pending->map(function ($appt) {
            $apptTime = Carbon::parse($appt->appointment_time);

            // Determine human friendly label (Today, Tomorrow, or Specific Date)
            if ($apptTime->isToday()) {
                $dayLabel = 'Today';
            } elseif ($apptTime->isTomorrow()) {
                $dayLabel = 'Tomorrow';
            } else {
                $dayLabel = $apptTime->format('D, d M');
            }

            return [
                'id' => $appt->id,
                'parent_name' => $appt->child->parent->user->name ?? 'Unknown Parent',
                'session_type' => $appt->appointment_type,
                'day_label' => $dayLabel,
                'time' => $apptTime->format('h:i A'),
            ];
        });

        return response()->json([
            'success' => true,
            'pending_requests' => $formatted
        ]);
    }
    public function confirmAppointment($id)
    {
        $user = Auth::user();
        $specialist = $user->specialist;

        if (!$specialist) {
            return response()->json(['error' => 'Specialist profile not found.'], 403);
        }

        $appointment = Appointment::where('id', $id)
            ->where('specialist_id', $specialist->id)
            ->firstOrFail();

        $appointment->update(['status' => 'approved']);

        return response()->json([
            'success' => true,
            'message' => 'Appointment approved successfully.',
            'appointment' => $appointment
        ]);
    }
    public function declineAppointment($id, Request $request)
    {
        $user = Auth::user();
        $specialist = $user->specialist;

        if (!$specialist) {
            return response()->json(['error' => 'Specialist profile not found.'], 403);
        }

        $appointment = Appointment::where('id', $id)
            ->where('specialist_id', $specialist->id)
            ->firstOrFail();

        $appointment->update([
            'status' => 'declined'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Appointment declined successfully.',
            'appointment' => $appointment
        ]);
    }
    /**
     * Get all upcoming appointments for the specialist
     */
    public function upcomingAppointments()
    {
        $user = Auth::user();
        $specialist = $user->specialist;

        if (!$specialist) {
            return response()->json(['error' => 'Specialist profile not found.'], 404);
        }

        // Get all future appointments, ordered by date and time
        $upcomingAppointments = $specialist->appointments()
            ->where('appointment_time', '>', now())
            ->with('child')
            ->orderBy('appointment_time', 'asc')
            ->get();


        $formattedAppointments = $upcomingAppointments->map(function ($appointment) {
            return [
                'id' => $appointment->id,
                'child_name' => $appointment->child->full_name ?? 'Unknown Child',
                'date' => $appointment->appointment_time->format('D d M'), // "Mon 12 Feb"
                'time' => $appointment->appointment_time->format('h:i A'), // "09:00 AM"
                'session_type' => $appointment->appointment_type, // "Therapy", "Check-up", etc.
                'status' => $appointment->status,
            ];
        });

        return response()->json([
            'success' => true,
            'upcoming_appointments' => $formattedAppointments,
            'total_upcoming' => $upcomingAppointments->count()
        ]);
    }
    public function getMyClients()
    {
        $user = Auth::user();
        $specialist = $user->specialist;

        $children = Child::where('specialist_id', $specialist->id)
            ->with('parent.user')
            ->get();

        $formattedClients = $children->map(function ($child) {

            // Convert string to an explicit Carbon date instance safely to fix the math!
            $birthDate = $child->dob ? \Illuminate\Support\Carbon::parse($child->dob) : null;
            $age = $birthDate ? now()->diffInYears($birthDate) . ' years' : 'N/A';

            return [
                'id' => $child->id,
                'child_name' => $child->full_name,
                'age' => $age,
                'dob' => $child->dob, // Raw string form safely sent to Flutter
                'gender' => $child->gender ?? 'Not specified',
                'autism_level' => $child->autism_level ?? 'Not specified',
                'behavioral_description' => $child->description ?? 'No description provided',
                'parent_name' => $child->parent->user->name ?? 'N/A',
                'parent_email' => $child->parent->user->email ?? 'N/A',
                'parent_phone' => $child->parent->phone ?? 'N/A', // Pulled directly from the parent profile
                'last_session_summary' => $child->last_session ?? 'No session notes yet',
                'next_plan' => $child->next_plan ?? 'No plan set yet',
                'diagnosis' => $child->diagnosis ?? 'No diagnosis added yet.',
                'therapy_type' => $child->therapy_type ?? 'No therapy type selected yet.',
                'session_frequency' => $child->session_frequency ?? 'Session frequency not specified.',
                'goals' => $child->current_goals ?? 'No treatment goals added yet.',
                'progress' => $child->recent_progress ?? 'No progress reports yet.',
                'important_notes' => $child->important_notes ?? 'No important notes yet.',
            ];
        });

        return response()->json([
            'success' => true,
            'clients' => $formattedClients,
        ]);
    }
    public function getClientDetails($childId)
    {
        $specialist = Auth::user()->specialist;


        $child = Child::where('id', $childId)
            ->where('specialist_id', $specialist->id)
            ->with('parent.user')
            ->firstOrFail();


        $age = $child->dob ? now()->diffInYears($child->dob) . ' years' : 'N/A';

        $clientDetails = [
            'id' => $child->id,
            'child_name' => $child->full_name,
            'age' => $age,
            'dob' => $child->dob ? $child->dob->format('Y-m-d') : null,
            'gender' => $child->gender ?? 'Not specified',
            'parent_name' => $child->parent->user->name ?? 'N/A',
            'parent_email' => $child->parent->user->email ?? 'N/A',
            'parent_phone' => $child->parent->user->phone ?? 'N/A',
            'behavioral_description' => $child->description ?? 'No description provided',
            'autism_level' => $child->autism_level ?? 'Not specified',
            'has_severe_condition' => $child->has_other_disease ?? false,
            'severe_condition_details' => $child->medical_condition ?? '',
            'medical_details' => $child->medical_condition ?? 'No medical details',
            'diagnosis' => $child->diagnosis ?? 'No diagnosis added',
            'therapy_type' => $child->therapy_type ?? 'Not specified',
            'session_frequency' => $child->session_frequency ?? 'Not set',
            'last_session' => $child->last_session ?? 'No session notes yet',
            'next_plan' => $child->next_plan ?? 'No plan set yet',
            'goals' => $child->current_goals ?? 'No goals set',
            'progress' => $child->recent_progress ?? 'No progress yet',
            'important_notes' => $child->important_notes ?? 'No important notes',
        ];

        return response()->json([
            'success' => true,
            'client' => $clientDetails
        ]);
    }
    public function updateSpecialistNotes(Request $request, $childId)
    {
        $specialist = Auth::user()->specialist;

        $child = Child::where('id', $childId)
            ->where('specialist_id', $specialist->id)
            ->firstOrFail();

        $validated = $request->validate([
            'diagnosis' => 'nullable|string',
            'therapy_type' => 'nullable|string',
            'session_frequency' => 'nullable|string',
            'last_session_summary' => 'nullable|string',
            'next_plan' => 'nullable|string',
            'goals' => 'nullable|string',
            'progress' => 'nullable|string',
            'important_notes' => 'nullable|string',
        ]);

        $child->update([
            'diagnosis' => $validated['diagnosis'] ?? $child->diagnosis,
            'therapy_type' => $validated['therapy_type'] ?? $child->therapy_type,
            'session_frequency' => $validated['session_frequency'] ?? $child->session_frequency,
            'last_session' => $validated['last_session_summary'] ?? $child->last_session,
            'next_plan' => $validated['next_plan'] ?? $child->next_plan,
            'current_goals' => $validated['goals'] ?? $child->current_goals,
            'recent_progress' => $validated['progress'] ?? $child->recent_progress,
            'important_notes' => $validated['important_notes'] ?? $child->important_notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Specialist notes updated successfully.'
        ]);
    }
    public function getUpcomingEvents()
    {
        $specialist = Auth::user()->specialist;

        $events = CommunityEvent::where('event_date', '>=', now()->startOfDay())
            ->orderBy('event_date', 'asc')
            ->orderBy('event_time', 'asc')
            ->get();

        $formattedEvents = $events->map(function ($event) use ($specialist) {
            return [
                'id' => $event->id,
                'title' => $event->title,
                'category' => $event->category,
                'location' => $event->location,
                'date' => \Carbon\Carbon::parse($event->event_date)->format('M d'),
                'time' => \Carbon\Carbon::parse($event->event_time)->format('h:i A'),
                'description' => $event->description,
                'is_registered' => $event->isSpecialistRegistered($specialist->id),


            ];
        });

        return response()->json([
            'success' => true,
            'events' => $formattedEvents,
            'total_events' => $events->count()
        ]);
    }

    public function registerForEvent($eventId)
    {
        $specialist = Auth::user()->specialist;
        $event = CommunityEvent::findOrFail($eventId);
        if ($event->isSpecialistRegistered($specialist->id)) {
            return response()->json([
                'success' => false,
                'message' => 'You are already registered for this event.'
            ], 400);
        }

        // Register the specialist
        $event->specialists()->attach($specialist->id);

        return response()->json([
            'success' => true,
            'message' => 'Successfully registered for the event.'
        ]);
    }
    public function unregisterFromEvent($eventId)
    {
        $specialist = Auth::user()->specialist;

        $event = CommunityEvent::where('id', $eventId)->firstOrFail();

        if (!$event->isSpecialistRegistered($specialist->id)) {
            return response()->json([
                'success' => false,
                'message' => 'You are not registered for this event.'
            ], 400);
        }

        $event->specialists()->detach($specialist->id);

        return response()->json([
            'success' => true,
            'message' => 'Successfully unregistered from the event.'
        ]);
    }
}
