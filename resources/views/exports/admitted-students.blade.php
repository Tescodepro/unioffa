<table>
    <thead>
        <tr style="background-color: #f3f4f6; font-weight: bold;">
            <th>Student ID</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Matric Number</th>
            <th>Department</th>
            <th>Entry Mode</th>
            <th>Level</th>
            <th>Admission Session</th>
            <th>Centre/Campus</th>
            <th>Status</th>
            <th>Admission Date</th>
        </tr>
    </thead>
    <tbody>
        @forelse($students as $student)
            <tr>
                <td>{{ $student->id }}</td>
                <td>{{ $student->user?->full_name ?? 'N/A' }}</td>
                <td>{{ $student->user?->email ?? 'N/A' }}</td>
                <td>{{ $student->matric_no ?? 'Not Assigned' }}</td>
                <td>{{ $student->department?->department_name ?? 'N/A' }}</td>
                <td>{{ $student->entry_mode ?? 'N/A' }}</td>
                <td>{{ $student->level ?? 'N/A' }}</td>
                <td>{{ $student->admission_session ?? 'N/A' }}</td>
                <td>{{ $student->campus?->name ?? 'N/A' }}</td>
                <td>{{ $student->status ?? 'Active' }}</td>
                <td>{{ $student->admission_date ?? 'N/A' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="11" style="text-align: center; padding: 10px;">
                    No students found
                </td>
            </tr>
        @endforelse
    </tbody>
</table>