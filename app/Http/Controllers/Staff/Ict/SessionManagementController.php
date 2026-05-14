<?php

namespace App\Http\Controllers\Staff\Ict;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SessionManagementController extends Controller
{
    /**
     * Display session management dashboard.
     */
    public function index()
    {
        $sessions = AcademicSession::orderBy('name', 'desc')->get();
        $activeSession = AcademicSession::where('status', '1')->first();
        
        // Stats for progression readiness
        $studentStats = [
            'active_students' => Student::where('status', Student::STATUS_ACTIVE)->count(),
            'candidates_for_progression' => Student::where('status', Student::STATUS_ACTIVE)
                ->where('level', '<', 500) // Simple check, refined in bulk action
                ->count(),
            'candidates_for_graduation' => Student::where('status', Student::STATUS_ACTIVE)
                ->get()
                ->filter(function($student) {
                    return $student->level >= $student->getMaxLevel();
                })->count(),
        ];

        return view('staff.ict.sessions.index', compact('sessions', 'activeSession', 'studentStats'));
    }

    /**
     * Set the active academic session.
     */
    public function setActiveSession(Request $request)
    {
        $request->validate([
            'session_id' => 'required|exists:academic_sessions,id',
        ]);

        DB::transaction(function() use ($request) {
            // Deactivate all
            AcademicSession::query()->update(['status' => '0']);
            
            // Activate selected
            $session = AcademicSession::findOrFail($request->session_id);
            $session->update(['status' => '1']);
        });

        return back()->with('success', "Academic session updated successfully. Active session is now " . AcademicSession::where('status', '1')->first()->name);
    }

    /**
     * Perform bulk student progression (Level Up & Graduation).
     */
    public function processProgression(Request $request)
    {
        // This is a heavy operation, we should use a job if students > 1000
        // For now, we'll run it in a transaction
        
        return DB::transaction(function() {
            $students = Student::where('status', Student::STATUS_ACTIVE)->get();
            $progressed = 0;
            $graduated = 0;

            foreach ($students as $student) {
                $maxLevel = $student->getMaxLevel();

                if ($student->level >= $maxLevel) {
                    // Mark as Graduated
                    $student->update(['status' => Student::STATUS_GRADUATED]);
                    $graduated++;
                } else {
                    // Increment Level
                    $student->increment('level', 100);
                    $progressed++;
                }
            }

            return back()->with('success', "Progression completed: {$progressed} students leveled up, {$graduated} students marked as graduated.");
        });
    }

    /**
     * Create a new academic session.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:academic_sessions,name|regex:/^\d{4}\/\d{4}$/',
        ]);

        AcademicSession::create([
            'name' => $request->name,
            'status' => '0',
            'status_upload_result' => false,
        ]);

        return back()->with('success', "New session {$request->name} created.");
    }
}
