@extends('account::pages.layouts.print')


@section('title', 'Purchase List')


@section('content')
    <style>
        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 12px;
            color: #777;
        }
        .text-right{
            text-align: right;
        }
    </style>
    <div>
        <div class="text-center">
            <h3><strong>Purchase List</strong></h3>
        </div>
    </div>

    <table cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>#</th>
                <th>Invoice Number</th>
                <th>Supplier Name</th>
                <th>Issue Date</th>
                <th>Purchase Date</th>
                <th>Total</th>
                <th>Vat</th>
                <th>Discount</th>
                <th>Net Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($purchases as $index => $purchase)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $purchase->invoice_number }}</td>
                    <td>{{ $purchase->supplier->isupp_name ?? 'N/A' }}</td>
                    <td>{{ $purchase->issue_date }}</td>
                    <td>{{ $purchase->purchase_date }}</td>
                    <td class="total text-right">{{ number_format($purchase->total_amount, 2) }}</td>
                    <td class="total text-right">{{ number_format($purchase->vat_amount, 2) }}</td>
                    <td class="total text-right">{{ number_format($purchase->discount_amount, 2) }}</td>
                    <td class="total text-right">{{ number_format($purchase->net_total, 2) }}</td>
                </tr>
            @endforeach
                <tr>
                    <td colspan="5" class="text-end text-bold">Total Purchase:</td>
                    <td class="text-end text-bold">{{ number_format($totals['total_amount'], 2) }}</td>
                    <td class="text-end text-bold">{{ number_format($totals['vat_amount'], 2) }}</td>
                    <td class="text-end text-bold">{{ number_format($totals['discount_amount'], 2) }}</td>
                    <td class="text-end text-nowrap text-bold">{{ number_format($totals['net_total'], 2) }}</td>
                </tr>
        </tbody>
    </table>

    <div class="footer">
        Generated on: {{ date('Y-m-d H:i:s') }}
    </div>
@endsection
