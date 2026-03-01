<h3 style="text-align:center;">Student Transaction Report</h3>
<table width="100%" border="1" cellspacing="0" cellpadding="5">
    <thead style="background:#f2f2f2;">
        <tr>
            <th>Student Name</th>
            <th>Matric No</th>
            <th>Level</th>
            <th>Entry Mode</th>
            <th>Center</th>
            <th>Faculty</th>
            <th>Department</th>
            <th>Expected (₦)</th>
            <th>Received (₦)</th>
            <th>Outstanding (₦)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $row)
            <tr>
                <td>{{ $row['student_name'] }}</td>
                <td>{{ $row['matric_number'] }}</td>
                <td>{{ $row['level'] }}</td>
                <td>{{ $row['entry_mode'] }}</td>
                <td>{{ $row['center'] }}</td>
                <td>{{ $row['faculty'] }}</td>
                <td>{{ $row['department'] }}</td>
                <td>{{ number_format($row['expected'], 2) }}</td>
                <td>{{ number_format($row['received'], 2) }}</td>
                <td>{{ number_format($row['outstanding'], 2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<p style="margin-top:20px; text-align:right;">
    <strong>Generated on:</strong> {{ now()->format('d M Y, h:i A') }}
</p>