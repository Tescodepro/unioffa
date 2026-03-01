<h3>Report by Faculty</h3>

@foreach($data as $campus => $faculties)
    <h4 style="margin-top: 20px; color: #333; border-bottom: 2px solid #ccc; padding-bottom: 5px;">{{ $campus }}</h4>
    <table width="100%" border="1" cellspacing="0" cellpadding="4" style="margin-bottom: 20px; border-collapse: collapse;">
        <thead style="background-color: #f2f2f2;">
            <tr>
                <th>Faculty</th>
                <th>Total Students</th>
                <th>Total Transactions</th>
                <th>Expected (₦)</th>
                <th>Received (₦)</th>
                <th>Outstanding (₦)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($faculties as $row)
                <tr>
                    <td>{{ $row['faculty'] }}</td>
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