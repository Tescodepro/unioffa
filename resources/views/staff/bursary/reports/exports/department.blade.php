<h3 style="text-align:center;">Department Payment Report</h3>

@foreach($data as $campusName => $centers)
    <h3 style="margin-top: 20px; color: #333; border-bottom: 2px solid #333; padding-bottom: 5px;">{{ $campusName }} Section</h3>
    
    @foreach($centers as $centerLabel => $departments)
        <h4 style="margin-top: 15px; color: #555;">{{ $centerLabel }}</h4>
        <table width="100%" border="1" cellspacing="0" cellpadding="5" style="margin-bottom: 20px; border-collapse: collapse;">
            <thead style="background:#f2f2f2;">
                <tr>
                    <th>Department</th>
                    <th>Faculty</th>
                    <th>Total Students</th>
                    <th>Total Transactions</th>
                    <th>Expected (₦)</th>
                    <th>Received (₦)</th>
                    <th>Outstanding (₦)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($departments as $row)
                    <tr>
                        <td>{{ $row['department'] }}</td>
                        <td>{{ $row['faculty'] ?? 'N/A' }}</td>
                        <td style="text-align: center;">{{ $row['total_students'] }}</td>
                        <td style="text-align: center;">{{ $row['total_transactions'] }}</td>
                        <td style="text-align: right;">{{ number_format($row['expected'], 2) }}</td>
                        <td style="text-align: right;">{{ number_format($row['received'], 2) }}</td>
                        <td style="text-align: right; color: {{ $row['outstanding'] > 0 ? 'red' : 'green' }};">
                            {{ number_format($row['outstanding'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach
@endforeach

<p style="margin-top:20px; text-align:right;">
    <strong>Generated on:</strong> {{ now()->format('d M Y, h:i A') }}
</p>