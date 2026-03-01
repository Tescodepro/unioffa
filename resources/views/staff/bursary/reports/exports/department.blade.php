<h3 style="text-align:center;">Department Payment Report</h3>
<table width="100%" border="1" cellspacing="0" cellpadding="5">
    <thead style="background:#f2f2f2;">
        <tr>
            <th>Department</th>
            <th>Faculty</th>
            <th>Expected (₦)</th>
            <th>Received (₦)</th>
            <th>Outstanding (₦)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $row)
            <tr>
                <td>{{ $row['department'] }}</td>
                <td>{{ $row['faculty'] ?? 'N/A' }}</td>
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