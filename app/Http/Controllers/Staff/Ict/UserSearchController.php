<?php

namespace App\Http\Controllers\Staff\Ict;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserSearchController extends Controller
{
    public function searchStudents(Request $request)
    {
        $search = $request->get('q');

        $query = User::whereHas('userType', function ($q) {
            $q->where('name', 'student');
        });

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('other_names', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('student', function ($sq) use ($search) {
                        $sq->where('matric_no', 'like', "%{$search}%");
                    });
            });
        }

        $students = $query->limit(20)->get();

        $results = [];
        foreach ($students as $student) {
            $matric = $student->student ? $student->student->matric_no : 'N/A';
            $results[] = [
                'id' => $student->id,
                'text' => "{$student->first_name} {$student->last_name} ({$matric})",
            ];
        }

        return response()->json(['results' => $results]);
    }

    public function searchLecturers(Request $request)
    {
        $search = $request->get('q');

        $query = User::whereHas('userType', function ($q) {
            $q->where('name', 'like', '%lecturer%'); // Can also be 'staff' depending on setup, but looking for lecturer
        });

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('other_names', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('staff', function ($sq) use ($search) { // Adjusted from lecturer to staff based on User model
                        $sq->where('staff_no', 'like', "%{$search}%");
                    });
            });
        }

        $lecturers = $query->limit(20)->get();

        $results = [];
        foreach ($lecturers as $lecturer) {
            $staffNo = $lecturer->staff ? $lecturer->staff->staff_no : 'N/A'; // Adjusted to staff
            $results[] = [
                'id' => $lecturer->id,
                'text' => "{$lecturer->first_name} {$lecturer->last_name} ({$staffNo})",
            ];
        }

        return response()->json(['results' => $results]);
    }
}
