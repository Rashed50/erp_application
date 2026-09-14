@extends('account::pages.layouts.print')


@section('title', 'Trial Balance')

@section('content')
    <table>
        <thead>
        <tr>
            <th>SL</th>
            <th>Account Name</th>
            <th>Account No</th>
            <th class="text-end">Debit</th>
            <th class="text-end">Credit</th>
        </tr>
        </thead>

        <tbody>
        @foreach($accounts as $account)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $account['account_name'] }}</td>
                <td>{{ $account['account_no'] }}</td>
                <td class="text-end">{{ $account['debit'] }}</td>
                <td class="text-end">{{ $account['credit'] }}</td>
            </tr>
        @endforeach

        <tr>
            <td colspan="3" class="text-end text-bold"> Total :</td>
            <td class="text-end text-bold">{{ $total_debit }}</td>
            <td class="text-end text-bold">{{ $total_credit }}</td>
        </tr>

        </tbody>
    </table>
@endsection
