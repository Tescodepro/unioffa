<h3 style="text-align:center;">Student Transaction Report</h3>
<table width="100%" border="1" cellspacing="0" cellpadding="5">
    <thead style="background:#f2f2f2;">
        <tr>
            <th>Student</th>
            <th>Matric No</th>
            <th>Level</th>
            <th>Payment Type</th>
            <th>Amount (₦)</th>
            <th>Status</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        @foreach($transactions as $t)
            <tr>
                <td>{{ $t->student->full_name ?? 'Unknown' }}</td>
                <td>{{ $t->student->matric_number ?? 'N/A' }}</td>
                <td>{{ $t->student->level ?? 'N/A' }}</td>
                <td>{{ ucfirst($t->payment_type) }}</td>
                <td>{{ number_format($t->amount, 2) }}</td>
                <td>{{ ucfirst($t->status) }}</td>
                <td>{{ $t->created_at->format('d M Y, h:i A') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<p style="margin-top:20px; text-align:right;">
    <strong>Generated on:</strong> {{ now()->format('d M Y, h:i A') }}
</p>
