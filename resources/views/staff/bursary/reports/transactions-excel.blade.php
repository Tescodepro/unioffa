<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Reference</th>
            <th>Student</th>
            <th>Payment Type</th>
            <th>Amount (₦)</th>
            <th>Status</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        @foreach($transactions as $i => $txn)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $txn->refernce_number }}</td>
                <td>{{ optional($txn->user)->first_name }} {{ optional($txn->user)->last_name }}</td>
                <td>{{ ucfirst($txn->payment_type) }}</td>
                <td>{{ number_format($txn->amount, 2) }}</td>
                <td>
                    @if($txn->payment_status == 1)
                        Successful
                    @elseif($txn->payment_status == 2)
                        Failed
                    @else
                        Pending
                    @endif
                </td>
                <td>{{ $txn->created_at->format('d M, Y h:i A') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
