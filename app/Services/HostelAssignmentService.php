<?php

namespace App\Services;

use App\Models\Hostel;
use App\Models\Room;
use App\Models\Student;
use App\Models\StudentHostelAssignment;
use App\Models\Transaction;

class HostelAssignmentService
{
    public function autoAssign(Student $student): array
    {
        // 1. Already assigned?
        if (StudentHostelAssignment::where('student_id', $student->id)->exists()) {
            return ['status' => false, 'message' => 'You are already assigned to a hostel.'];
        }

        $currentSession = activeSession()?->name;

        // 2. Total hostel fee paid this session
        $hostelFeePaid = Transaction::whereIn('payment_type', ['accommodation', 'hostel'])
            ->where('session', $currentSession)
            ->where('payment_status', 1)
            ->where('user_id', $student->user_id)
            ->sum('amount');

        if ($hostelFeePaid <= 0) {
            return ['status' => false, 'message' => 'You have not paid any hostel fee.'];
        }

        // 3. Maintenance fee check
        $maintenancePaid = Transaction::where('payment_type', 'maintenance')
            ->where('session', $currentSession)
            ->where('payment_status', 1)
            ->where('user_id', $student->user_id)
            ->sum('amount');

        if ($maintenancePaid <= 0) {
            return ['status' => false, 'message' => 'You must pay the maintenance fee before hostel allocation.'];
        }

        // 4. Get all eligible hostels by gender + within budget
        $hostels = Hostel::where('category', $student->sex)
            ->where('price', '<=', $hostelFeePaid)
            ->orderByDesc('price') // prefer most expensive within budget
            ->get();

        if ($hostels->isEmpty()) {
            return ['status' => false, 'message' => 'No hostel found matching your payment and gender.'];
        }

        // 5. Loop through hostels to find available room
        foreach ($hostels as $hostel) {
            $room = Room::where('hostel_id', $hostel->id)
                ->withCount('assignments')
                ->orderBy('room_number', 'asc') // fill sequentially
                ->get()
                ->first(function ($r) use ($hostel) {
                    return $r->assignments_count < $hostel->capacity_per_room;
                });

            if ($room) {
                // 6. Assign student
                StudentHostelAssignment::create([
                    'student_id' => $student->id,
                    'room_id' => $room->id,
                ]);

                return [
                    'status' => true,
                    'message' => 'You have been assigned to '.$hostel->name.' (Room '.$room->room_number.').',
                ];
            }
        }

        // 7. No available room in any hostel
        return ['status' => false, 'message' => 'No available rooms in any hostel within your budget.'];
    }
}
