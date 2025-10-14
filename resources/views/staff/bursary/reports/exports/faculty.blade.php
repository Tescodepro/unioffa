<h3>Report by Faculty</h3>
<table width="100%" border="1" cellspacing="0" cellpadding="4">
    <thead>
        <tr>
            <th>Faculty</th>
            <th>Expected (₦)</th>
            <th>Received (₦)</th>
            <th>Outstanding (₦)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($faculties as $faculty)
            @php
                $expected = $faculty->departments->flatMap->paymentSettings->sum('amount');
                $received = $faculty->departments->flatMap->transactions->where('status', 'success')->sum('amount');
            @endphp
            <tr>
                <td>{{ $faculty->faculty_code }}</td>
                <td>{{ number_format($expected, 2) }}</td>
                <td>{{ number_format($received, 2) }}</td>
                <td>{{ number_format($expected - $received, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
