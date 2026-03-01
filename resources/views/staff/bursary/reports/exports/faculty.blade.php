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
        @foreach($data as $row)
            <tr>
                <td>{{ $row['faculty'] }}</td>
                <td>{{ number_format($row['expected'], 2) }}</td>
                <td>{{ number_format($row['received'], 2) }}</td>
                <td>{{ number_format($row['outstanding'], 2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>