@extends('account::pages.layouts.print')


@section('title', 'Sale Details')


@section('content')
    <style>
        label {
            margin-bottom: 0;
        }
        .fs-1{
            font-size: 12px !important;
        }
        .border-n{
            border: none;
        }
    </style>
    <div>
        <h2>
            Sale: {{ $sale->sr_invoice_no }}
        </h2>

        <table width="100%" cellpadding="10" cellspacing="0">
            <tr class="fs-1">
                <!-- Left Column -->
                <td class="border-n" width="50%" valign="top">
                    <div>
                        <label class="fs-1"><strong>Customer:</strong></label>
                        <div>
                            <div><strong>Name:</strong> {{ $sale->customer?->cus_name }}</div>
                            <div><strong>Phone:</strong> {{ $sale->customer?->cus_phone }}</div>
                            <div><strong>Email:</strong> {{ $sale->customer?->cus_email }}</div><br>
                        </div>
                    </div>
                    <div>
                        <div><strong class="fs-1">Invoice No:</strong></div>
                         {{ $sale->sr_invoice_no }}
                    </div>
                    <div>
                        <div><strong class="fs-1">Invoice Description:</strong></div>
                        {{ $sale->sr_invoice_description }}
                    </div>
                    <div>
                        <div><strong class="fs-1">Payment Terms:</strong></div>
                        {{ $sale->sr_payment_terms }}
                    </div>
                </td>

                <!-- Right Column -->
                <td width="50%" valign="top" class="border-n">
                    <div>
                        <div><strong>Account (Credit):</strong> {{ $sale->credit?->chart_of_acct_name }}</div><br>
                    </div>
                    <div>
                        <div><strong>Journal (Debit):</strong> {{ $sale->debit?->jour_name }}</div><br>
                    </div>
                    <div>
                        <div><strong>Issue Date:</strong> {{ \Carbon\Carbon::parse($sale->sr_issue_date)->format('d M, Y') }}</div><br>
                    </div>
                    <div>
                        <div><strong>Due Date:</strong> {{ \Carbon\Carbon::parse($sale->sr_due_date)->format('d M, Y') }}</div><br>
                    </div>
                    <div>
                        <div><strong>Supply Date:</strong> {{ \Carbon\Carbon::parse($sale->sr_supply_date)->format('d M, Y') }}</div><br>
                    </div>
                </td>
            </tr>
        </table>




        <table width="100%" cellpadding="10" cellspacing="0" style="border-collapse: collapse;">
            <thead>
                <tr class="fs-1">
                    <th width="5%" style="border: 1px solid #000; padding: 5px;">S.L</th>
                    <th width="50%" style="border: 1px solid #000; padding: 5px;">Product Name</th>
                    <th width="15%" style="border: 1px solid #000; padding: 5px;">Un. Rate</th>
                    <th width="10%" style="border: 1px solid #000; padding: 5px;">Qty</th>
                    <th width="10%" style="border: 1px solid #000; padding: 5px;">Vat</th>
                    <th width="20%" style="border: 1px solid #000; padding: 5px;">Price + Vat</th>
                </tr>
            </thead>
            <tbody class="fs-1">
                @foreach ($sale->items as $item)
                    <tr>
                        <td style="border: 1px solid #000; padding: 5px;">
                            {{ $loop->index + 1 }}
                        </td>
                        <td style="border: 1px solid #000; padding: 5px;">
                            @if ($item->srd_product_id)
                                {{ $item->product?->name ?? 'Product not found' }}
                            @else
                                {{ $item->product_name }}
                            @endif
                        </td>
                        <td style="border: 1px solid #000; padding: 5px; text-align: right;">
                            {{ $item->srd_unit_price }}
                        </td>
                        <td style="border: 1px solid #000; padding: 5px; text-align: right;">
                            {{ $item->srd_qty }}
                        </td>
                        <td style="border: 1px solid #000; padding: 5px; text-align: right;">
                            {{ $item->srd_vat_percent }}%
                        </td>
                        <td style="border: 1px solid #000; padding: 5px; text-align: right;">
                            {{ $item->srd_total_amount }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>


        

        <table width="100%" cellpadding="10" cellspacing="0" class="border-n">
            <tr>
                <!-- Left Column (50%) -->
                <td width="60%" valign="top" class="border-n">
                    <!-- Notes Section -->
                    <div class="fs-1" style="margin-top: 10px;">
                        @if($sale->notes)
                            <div style="margin-bottom: 10px;">
                                <strong>Notes:</strong>
                                <div>{{ $sale->notes }}</div>
                            </div>
                        @endif
                        @if($sale->attachments && count($sale->attachments) > 0)
                            <div style="margin-bottom: 10px;">
                                <strong>Attachments:</strong>
                                <div>
                                    @foreach ($sale->attachments as $item)
                                        {{ $loop->index + 1 }}.
                                        <a href="{{ $item }}" target="_blank" download="">
                                            {{ basename($item) }}
                                            &nbsp;
                                            <i class="fa fa-download"></i>
                                        </a>
                                        <br>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </td>

                <!-- Right Column (50%) -->
                <td width="40%" valign="top" class="border-n">
                    <!-- Total and Notes Section -->
                    <div class="fs-1" style="font-size: 14px;">
                        <!-- Totals Section -->
                        <table width="100%" cellpadding="5" cellspacing="0" class="border-n">
                            <tr>
                                <td width="50%" class="border-n">Total Before VAT:</td>
                                <td width="50%" align="right" class="border-n">{{ $sale->sr_total_amount }}</td>
                            </tr>
                            <tr>
                                <td width="50%" class="border-n">VAT Amount:</td>
                                <td width="50%" align="right" class="border-n">{{ $sale->sr_vat_amount }}</td>
                            </tr>
                            <tr>
                                <td width="50%" class="border-n">Retention Amount:</td>
                                <td width="50%" align="right" class="border-n">{{ $sale->retention_amount }}</td>
                            </tr>
                            <tr>
                                <td width="50%" class="border-n">Total:</td>
                                <td width="50%" align="right" class="border-n">{{ $sale->sr_grand_total_amount }}</td>
                            </tr>
                        </table>

                    </div>
                </td>
            </tr>
        </table>


    </div>
@endsection
