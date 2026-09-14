@extends('layouts.admin-master')
@section('title', 'Sale Products')
@section('content')


<div class="card-head bg-light m-2 d-flex" style="justify-content: space-between;">
    <h4>Profit and Loss Report</h4>
    <a href="{{ route('admin.accounting.reports.profit.loss.download') }}" class="btn btn-outline-info btn-sm">
        <i class="fa fa-download me-2" aria-hidden="true"></i> Download
    </a>
</div>
<div class="card">
    <div class="m-4">
        <p><strong>Total Revenue:</strong> {{ number_format($totalRevenue, 2) }}</p>
        <p><strong>Total Expense:</strong> {{ number_format($totalExpense, 2) }}</p>

        <h5>{{ $result }}</h5>
    </div>
</div>

@endsection