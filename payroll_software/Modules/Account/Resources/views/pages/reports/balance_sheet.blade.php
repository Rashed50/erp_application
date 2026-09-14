@extends('layouts.admin-master')
@section('title', 'Sale Products')
@section('content')

<div class="card-head bg-light m-2 d-flex" style="justify-content: space-between;">
    <h4>Balance Sheet Report</h4>
    <a href="{{ route('admin.accounting.reports.balance.sheet.download') }}" class="btn btn-outline-info btn-sm">
        <i class="fa fa-download me-2" aria-hidden="true"></i> Download
    </a>
</div>

<div class="card">
    <div class="m-4">
        <h4>Assets</h4>
        <table>
            <tr>
                <td><strong>Total Assets: </strong></td>
                <td>{{ number_format($totalAssets, 2) }}</td>
            </tr>
        </table>
    
        <h4>Liabilities</h4>
        <table>
            <tr>
                <td><strong>Total Liabilities: </strong></td>
                <td>{{ number_format($totalLiabilities, 2) }}</td>
            </tr>
        </table>
    
        <h4>Owner's Equity</h4>
        <table>
            <tr>
                <td><strong>Total Owner's Equity: </strong></td>
                <td>{{ number_format($totalEquity, 2) }}</td>
            </tr>
        </table>
    
        <h4>Balance Check: </h4>
        <p>{{ $isBalanced ? 'The Balance Sheet is Balanced' : 'The Balance Sheet is Not Balanced' }}</p>
    </div>
</div>


@endsection