@extends('account::pages.layouts.print')


@section('title', 'Trial Balance')

@section('content')
    <table>
        <thead>
        <tr>
            <th rowspan="2" class="text-center">SL</th>
            <th rowspan="2">Account Name</th>
            <th rowspan="2" class="text-center">Account No</th>
            <th colspan="2" class="text-center">Opening Balance</th>
            <th colspan="2" class="text-center">Transactions</th>
            <th colspan="2" class="text-center">Closing Balance</th>
        </tr>

        <tr>
            <th class="text-end">Debit</th>
            <th class="text-end">Credit</th>
            <th class="text-end">Debit</th>
            <th class="text-end">Credit</th>
            <th class="text-end">Debit</th>
            <th class="text-end">Credit</th>
        </tr>
        </thead>


        <tbody>
        @foreach($accounts as $account)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>{{ $account['account_name'] }}</td>
                <td class="text-center">{{ $account['account_no'] }}</td>
                <td class="text-end">{{ $account['opening_debit'] }}</td>
                <td class="text-end">{{ $account['opening_credit'] }}</td>
                <td class="text-end">{{ $account['debit'] }}</td>
                <td class="text-end">{{ $account['credit'] }}</td>
                <td class="text-end">{{ $account['closing_debit'] }}</td>
                <td class="text-end">{{ $account['closing_credit'] }}</td>
            </tr>
        @endforeach

        <tr>
            <td colspan="5" class="text-end text-bold">Total :</td>
            <td class="text-end text-bold">{{ $total_debit }}</td>
            <td class="text-end text-bold">{{ $total_credit }}</td>
            <td colspan="2" class="text-end text-bold"></td>
        </tr>
        </tbody>
    </table>
@endsection
