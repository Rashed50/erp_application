@extends('account::pages.layouts.masterPDF')
@section('title', 'Supplier Date Wise Report')

@section('company_name', $company->comp_name_en ?? "ASLOOB BEDAA CO.")
@section('company_address', $company->comp_address ?? "Riyadh, Kingdom of Saudi Arabia")
@section('company_email', $company->comp_email1 ?? "info@asloobb.com")
@section('company_phone', $company->comp_phone1 ?? "+966506685890")
@section('print', $current_datetime ?? Carbon\Carbon::now()->format('d M Y h:i A'))


@section('report_title')
    Supplier Date Wise Report
@endsection

@section('pdf-styles')
<style>
    /* Supplier Info Box */
    .supplier-info-box {
        width: 100%;
        border: 1px solid #6A7282;
        border-radius: 6px;
        background-color: #f8f9fa;
        padding: 10px 15px;
        margin-bottom: 15px;
        font-size: 11px;
    }

    .supplier-info-inline {
        display: flex;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
    }

    .supplier-info-item {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .info-label {
        font-weight: bold;
        color: #2c5aa0;
    }

    .info-value {
        color: #333;
    }

    /* Table Improvements */
    .summary-row {
        background-color: #e9ecef;
        font-weight: bold;
    }

    .amount-positive {
        color: #28a745;
    }

    .amount-negative {
        color: #dc3545;
    }

    .text-right {
        text-align: right;
    }

    .text-center {
        text-align: center;
    }
</style>
@endsection

@section('content')
    <!-- Supplier Information -->
    <div class="section supplier-info-box">
        <table style="width: 100%; border: none; border-collapse: collapse;">
            <tr>
                <td style="border: none; padding: 2px 5px; text-align: left; width: auto;">
                    <strong>Name:</strong> {{ $supplier->supplier_name ?? '-' }}
                </td>
                <td style="border: none; padding: 2px 5px; text-align: left; width: auto;">
                    <strong>Email:</strong> {{ $supplier->supplier_email ?? '-' }}
                </td>
                <td style="border: none; padding: 2px 5px; text-align: left; width: auto;">
                    <strong>Phone:</strong> {{ $supplier->supplier_phone ?? '-' }}
                </td>
            </tr>
        </table>
    </div>

    <!-- Transactions Table -->
    <div class="table-container">
        <div class="table-title">Purchase Transactions</div>
        <table>
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="15%">Transaction Type</th>
                    <th width="15%">Invoice No</th>
                    <th width="15%">Transaction Date</th>
                    <th width="15%">Debit (SAR)</th>
                    <th width="15%">Credit (SAR)</th>
                    <th width="35%">Notes</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalDebit = 0;
                    $totalCredit = 0;
                    $counter = 1;
                @endphp
                
                @foreach($purchases as $purchase)
                <tr>
                    <td>{{ $counter++ }}</td>
                    <td>{{ $purchase->transaction_type ?? '-' }}</td>
                    <td>{{ $purchase->invoice_no ?? '-' }}</td>
                    <td>{{ $purchase->transaction_date ? \Carbon\Carbon::parse($purchase->transaction_date)->format('d M, Y') : '-' }}</td>
                    <td class="text-right">{{ number_format($purchase->debit, 2) }}</td>
                    <td class="text-right">{{ number_format($purchase->credit, 2) }}</td>
                    <td class="text-left">{{ $purchase->notes ?? '-' }}</td>
                </tr>
                @php
                    $totalDebit += floatval($purchase->debit);
                    $totalCredit += floatval($purchase->credit);
                @endphp
                @endforeach
                
                @if(count($purchases) > 0)
                <!-- Summary Row -->
                <tr class="summary-row">
                    <td colspan="4" class="text-right"><strong>Total:</strong></td>
                    <td class="text-right"><strong>{{ number_format($totalDebit, 2) }}</strong></td>
                    <td class="text-right"><strong>{{ number_format($totalCredit, 2) }}</strong></td>
                    <td></td>
                </tr>
                
                <!-- Net Balance -->
                <tr class="summary-row">
                    <td colspan="4" class="text-right"><strong>Net Balance:</strong></td>
                    <td colspan="2" class="text-center">
                        <strong class="{{ ($totalCredit - $totalDebit) >= 0 ? 'amount-positive' : 'amount-negative' }}">
                            {{ number_format(abs($totalCredit - $totalDebit), 2) }} SAR
                            ({{ ($totalCredit - $totalDebit) >= 0 ? 'Credit' : 'Debit' }})
                        </strong>
                    </td>
                    <td></td>
                </tr>
                @else
                <tr>
                    <td colspan="7" class="text-center">Any records not found.</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
@endsection