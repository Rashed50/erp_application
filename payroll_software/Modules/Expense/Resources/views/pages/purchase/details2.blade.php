@extends('layouts.admin-master')
@section('title', 'Show Purchase')
@section('content')
    <style>
        label {
            margin-bottom: 0;
        }
    </style>
    <div>
        <h2>
            Purchase: 
            Invoice No: {{ $purchase->invoice_number }}
        </h2>

        <div class="row">
            <div class="col-md-6">
                <div class="">
                    <label>Supplier:</label>
                    <div>
                        Name: {{ $purchase->supplier?->isupp_name }}
                        <br>
                        Phone: {{ $purchase->supplier?->isupp_phone }} <!-- Ensure you have this field in your supplier model -->
                        <br>
                        Email: {{ $purchase->supplier?->isupp_email }} <!-- Ensure you have this field in your supplier model -->
                    </div>
                </div>
                <div class="">
                    <label>Invoice No: </label>
                    <div>{{ $purchase->invoice_number }}</div>
                </div>
                <div class="">
                    <label>Invoice Description: </label>
                    <div>{{ $purchase->description }}</div>
                </div>
                <div class="">
                    <label>Payment Terms: </label>
                    <div>{{ $purchase->payment_terms ?? 'N/A' }}</div> <!-- Adjust as necessary -->
                </div>
            </div>
            <div class="col-md-6">
                <div class="">
                    <label>Issue Date:</label>
                    <div>{{ \Carbon\Carbon::parse($purchase->issue_date)->format('d M, Y') }}</div>
                </div>
                <div class="">
                    <label>Due Date:</label>
                    <div>{{ \Carbon\Carbon::parse($purchase->due_date)->format('d M, Y') }}</div> <!-- Ensure this field exists -->
                </div>
                <div class="">
                    <label>Purchase Date:</label>
                    <div>{{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d M, Y') }}</div>
                </div>
            </div>

            <div class="col-12 mt-3">
                <table id="" class="table table-bordered custom_table mb-0 no-footer" role="grid" aria-describedby="alltableinfo_info">
                    <thead>
                        <tr role="row">
                            <th class="sorting_disabled" rowspan="1" colspan="1">S.L</th>
                            <th class="sorting_disabled" rowspan="1" colspan="1">Name</th>
                            <th class="sorting_disabled" rowspan="1" colspan="1">Un.Rate</th>
                            <th class="sorting_disabled" rowspan="1" colspan="1">Qty</th>
                            <th class="sorting_disabled" rowspan="1" colspan="1">Vat</th>
                            <th class="sorting_disabled" rowspan="1" colspan="1">Price + Vat</th>
                        </tr>
                    </thead>
                    <tbody id="item_details_cart_table_content_view">
                        @foreach ($purchase->details as $item)
                            <tr>
                                <td style="width: 80px">
                                    {{ $loop->index + 1 }}
                                </td>

                                <td>
                                    @if ($item->item_id)
                                        {{ $item->product?->item_name ?? 'Product not found' }} 
                                    @else
                                        {{ $item->service_name ?? 'Service not found' }} <!-- Adjust accordingly -->
                                    @endif
                                </td>
                                <td style="width: 150px">
                                    {{ $item->unit_price }}
                                </td>
                                <td style="width: 100px">
                                    {{ $item->qty }}
                                </td>
                                <td style="width: 100px">
                                    {{ $item->vat }}%
                                </td>
                                <td style="width: 200px">
                                    {{ $item->total_amount }}
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
                        {{ $purchase->total_amount }}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        VAT Amount:
                    </div>
                    <div class="col-md-6 text-right">
                        {{ $purchase->vat_amount }}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        Total:
                    </div>
                    <div class="col-md-6 text-right">
                        {{ $purchase->net_total }}
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="mt-2">
                    @if($purchase->notes)
                        <div class="mb-2">
                            <label>Notes</label>
                            <div>{{ $purchase->notes }}</div>
                        </div>
                    @endif
                    @if($purchase->attachments && count($purchase->attachments) > 0)
                        <div class="mb-2">
                            <label for="attachments">Attachments: </label>
                            <div>
                                @foreach ($purchase->attachments as $attachment)
                                    <a href="{{ asset($attachment->file_path) }}" target="_blank" download="">
                                        {{ basename($attachment->file_path) }}
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
