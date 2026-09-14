@extends('account::pages.layouts.print')


@section('title', 'General Ledger')


@section('content')
    <div>
        <div class="row mb-3 bg-light">
            <div class="col-12 pt-2 pb-1 d-flex justify-content-between">
                <div><strong>Account No:</strong> {{ $account->chart_of_acct_number }}</div>
                <div><strong>Date Range:</strong> {{$from}} to {{ $to }}</div>
            </div>

            <div class="col-12 d-flex justify-content-between">
                <div><strong>Account Name : </strong> {{ $account->chart_of_acct_name }} </div>
                <div><strong>Opening Balance : </strong> {{ $opening_balance }} {{ $opening_balance_type }}</div>
            </div>
        </div>
    </div>

    <table>
        <thead>
        <tr>
            <th>SL</th>
            <th>Date</th>
            <th class="text-center text-nowrap">Invoice No</th>
            <th>Particulars</th>
            <th class="text-end text-nowrap">Debit</th>
            <th class="text-end text-nowrap">Credit</th>
            <th class="text-end text-nowrap">Balance</th>
        </tr>
        </thead>
        <tbody>
        @foreach($transactions as $transaction)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td class="text-nowrap">{{ $transaction['created_at'] }}</td>
                <td class="text-center">{{ $transaction['trd_id'] }}</td>
                <td>
                    <p class="text-justify m-0">{{ $transaction['particular'] }}</p>
                </td>
                <td class="text-end text-nowrap">{{ $transaction['debit'] }}</td>
                <td class="text-end text-nowrap">{{ $transaction['credit'] }}</td>
                <td class="text-end text-nowrap">{{ $transaction['balance'] }}</td>
            </tr>
        @endforeach

        <tr>
            <td colspan="4" class="text-end text-bold"> Total / Closing Balance :</td>
            <td class="text-end text-nowrap text-bold"></td>
            <td class="text-end text-nowrap text-bold"></td>
            <td class="text-end text-nowrap text-bold">{{ $closing_balance }} {{ $closing_balance_type }}</td>
        </tr>
        </tbody>
    </table>
@endsection
