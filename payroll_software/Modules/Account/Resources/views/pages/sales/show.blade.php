@extends('layouts.admin-master')
@section('title', 'Show Sale')
@section('content')
    <style>
        label {
            margin-bottom: 0;
        }
    </style>
    <div>
        <h2>
            Sale: {{ $sale->sr_invoice_no }}
        </h2>
        <div class="row">
            <div class="col-md-6">
                <div class="">
                    <label>Customer:</label>
                    <div>
                        Name: {{ $sale->customer?->cus_name }}
                        <br>
                        Phone: {{ $sale->customer?->cus_phone }}
                        <br>
                        Email: {{ $sale->customer?->cus_email }}
                        {{-- <br>
                        Tax: {{ $sale->customer->cus_tax_number }} --}}
                    </div>
                </div>
                <div class="">
                    <label>Invoice No: </label>
                    <div>{{ $sale->sr_invoice_no }}</div>
                </div>
                <div class="">
                    <label>Invoice Description: </label>
                    <div>{{ $sale->sr_invoice_description }}</div>
                </div>
                <div class="">
                    <label>Payment Terms: </label>
                    {{ $sale->sr_payment_terms }}
                </div>
            </div>
            <div class="col-md-6">
                <div class="">
                    <label>Account (Credit):

                        {{ $sale->credit?->chart_of_acct_name }}
                    </label>
                </div>
                <div class="">
                    <label>Journal (Debit):
                        {{ $sale->debit?->jour_name }}
                    </label>
                </div>
                <div class="">
                    <label>Issue Date:</label>
                    {{ \Carbon\Carbon::parse($sale->sr_issue_date)->format('d M, Y') }}
                </div>
                <div class="">
                    <label>Due Date:</label>
                    {{ \Carbon\Carbon::parse($sale->sr_due_date)->format('d M, Y') }}
                </div>
                <div class="">
                    <label>Supply Date:</label>
                    {{ \Carbon\Carbon::parse($sale->sr_supply_date)->format('d M, Y') }}
                </div>
            </div>

            <div class="col-12 mt-3">
                <a class="btn btn-primary btn-sm" target="_blank" href="{{route('admin.accounting.sale.pdf', [$sale['sr_auto_id']])}}">Download</a>
            </div>


            <div class="col-12 mt-3">
                <table id="" class="table table-bordered custom_table mb-0 no-footer" role="grid" aria-describedby="alltableinfo_info">
                    <thead>
                        <tr role="row">
                            <th class="sorting_disabled" rowspan="1" colspan="1">S.L</th>
                            <th class="sorting_disabled" rowspan="1" colspan="1">Product Name</th>
                            <th class="sorting_disabled" rowspan="1" colspan="1">Un.Rate</th>
                            <th class="sorting_disabled" rowspan="1" colspan="1">Qty</th>
                            <th class="sorting_disabled" rowspan="1" colspan="1">Vat</th>
                            <th class="sorting_disabled" rowspan="1" colspan="1">Price + Vat</th>
                        </tr>
                    </thead>
                    <tbody id="item_details_cart_table_content_view">
                        @foreach ($sale->items as $item)
                            <tr>
                                <td style="width: 80px">
                                    {{ $loop->index + 1 }}
                                </td>

                                <td>
                                    @if ($item->srd_product_id)
                                        {{ $item->product?->name ?? 'Product not found' }} 
                                        {{-- (ID: {{ $item->srd_product_id }}) --}}
                                    @else
                                        {{ $item->product_name }}
                                    @endif
                                </td>
                                <td style="width: 150px">
                                    {{ $item->srd_unit_price }}
                                </td>
                                <td style="width: 100px">
                                    {{ $item->srd_qty }}
                                </td>
                                <td style="width: 100px">
                                    {{ $item->srd_vat_percent }}%
                                </td>
                                <td style="width: 200px" >
                                    {{ $item->srd_total_amount }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="col-sm-4"></div>
            <div class="col-sm-4"></div>
            <div class="col-sm-4" style="font-size: 18px">
                <div class="row mt-2">
                    <div class="col-md-6">
                        Total Before VAT:
                    </div>
                    <div class="col-md-6 text-right">
                        {{ $sale->sr_total_amount }}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        VAT Amount:
                    </div>
                    <div class="col-md-6 text-right">
                        {{ $sale->sr_vat_amount }}
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        Retention Amount:
                    </div>
                    <div class="col-md-6 text-right">
                        {{ $sale->retention_amount }}
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        Total:
                    </div>
                    <div class="col-md-6 text-right">
                        {{ $sale->sr_grand_total_amount }}
                    </div>
                </div>
            </div>
            <div class="col-12 ">
                <div class="mt-2">
                    @if($sale->notes)
                        <div class="mb-2">
                            <label>Notes</label>
                            <div>{{ $sale->notes }}</div>
                        </div>
                    @endif
                    @if($sale->attachments && count($sale->attachments) > 0)
                        <div class="mb-2">
                            <label for="attachments">Attachments: </label>
                            <div>
                                @foreach ($sale->attachments as $item)
                                    {{ $loop->index + 1 }}.
                                    <a href="{{ $item }}"  target="_blank" download="">
                                        {{ @end(explode('/', $item)) }}
                                        &nbsp;
                                        <i class="fa fa-download"></i>
                                    </a>
                                    <br>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
