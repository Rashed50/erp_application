@extends('account::pages.layouts.masterPDF')
@section('title', 'Purchase Report')

@section('company_name', $company->comp_name_en ?? "ASLOOB BEDAA CO.")
@section('company_address', $company->comp_address ?? "Riyadh, Kingdom of Saudi Arabia")
@section('company_email', $company->comp_email1 ?? "info@asloobb.com")
@section('company_phone', $company->comp_phone1 ?? "+966506685890")
@section('print', $current_datetime ?? Carbon\Carbon::now()->format('d M Y h:i A'))

@section('report_title', 'Purchase Report')

@section('pdf-styles')
<style>
    /* Supplier Info Box */
    .supplier-info-box {
        width: 100%;
        border: 1px solid #999;
        border-radius: 4px;
        background-color: #f8f9fa;
        padding: 10px 15px;
        margin-bottom: 15px;
        font-size: 10px;
        page-break-inside: avoid;
        page-break-after: avoid;
    }

    .supplier-info-box .info-row {
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
        margin-bottom: 6px;
    }

    .supplier-info-box .info-label {
        width: 20%;
        font-weight: bold;
        color: #000;
    }

    .supplier-info-box .info-value {
        width: 30%;
        border-bottom: 1px dashed #ccc;
        padding-bottom: 2px;
        color: #333;
    }

    .supplier-info-box .info-value.address {
        width: 70%;
        white-space: normal;
    }

    .supplier-info-box .info-row:last-child {
        margin-bottom: 0;
    }

    /* Table page break handling */
    .table-container table {
        page-break-inside: auto;
    }

    .table-container tr {
        page-break-inside: avoid;
        page-break-after: auto;
    }

    .table-container thead {
        display: table-header-group;
    }

    .table-container tfoot {
        display: table-footer-group;
    }
</style>
@endsection

@section('content')
    <!-- Supplier Information -->
    {{-- <div class="section supplier-info-box no-break">
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
    </div> --}}

    <!-- Table 1 -->
    <div class="table-container">
        <div class="table-title">Purchase Details</div>
        <table>
            <thead>
                <tr>
                    <th width="5%">S.N</th>
                    <th width="10%">Invoice Number</th>
                    <th width="15%">Supplier Name</th>
                    <th width="15%">Project Name</th>
                    {{-- <th width="10%">Issue Date</th> --}}
                    <th width="10%">Purchase Date</th>
                    <th width="10%">Total</th>
                    <th width="10%">VAT</th>
                    {{-- <th width="10%">Discount</th> --}}
                    <th width="10%">Net Total</th>
                </tr>
            </thead>
            <tbody>
                @if(count($purchases) > 0)
                @foreach($purchases as $index => $purchase)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $purchase->invoice_number }}</td>
                        <td>{{ $purchase->supplier->supplier_name ?? 'N/A' }}</td>
                        <td>{{ $purchase->project->proj_name ?? ' ' }}</td>
                        {{-- <td>{{ \Carbon\Carbon::parse($purchase->issue_date)->format('Y M d') }}</td> --}}
                        <td>{{ \Carbon\Carbon::parse($purchase->purchase_date)->format('Y M d') }}</td>
                        <td class="text-right">{{ number_format($purchase->total_amount, 2) }}</td>
                        <td class="text-right">{{ number_format($purchase->vat_amount, 2) }}</td>
                        {{-- <td class="text-right">{{ number_format($purchase->discount_amount, 2) }}</td> --}}
                        <td class="text-right">{{ number_format($purchase->net_total, 2) }}</td>
                    </tr>
                @endforeach

                <tr class="total-row">
                    <td colspan="5" class="text-right">Total Purchase:</td>
                    <td class="text-right">{{ number_format($totals['total_amount'], 2) }}</td>
                    <td class="text-right">{{ number_format($totals['vat_amount'], 2) }}</td>
                    {{-- <td class="text-right">{{ number_format($totals['discount_amount'], 2) }}</td> --}}
                    <td class="text-right">{{ number_format($totals['net_total'], 2) }}</td>
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