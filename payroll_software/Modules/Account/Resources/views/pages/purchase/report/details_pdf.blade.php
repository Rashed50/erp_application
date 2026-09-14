@extends('account::pages.layouts.print')

@section('title', 'Purchase Details')

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
            Purchase: {{ $purchase->invoice_number }}
        </h2>

        <table width="100%" cellpadding="10" cellspacing="0">
            <tr class="fs-1">
                <!-- Left Column -->
                <td class="border-n" width="50%" valign="top">
                    <div>
                        <label class="fs-1"><strong>Supplier:</strong></label>
                        <div>
                            <div><strong>Name:</strong> {{ $purchase->supplier?->isupp_name }}</div>
                            <div><strong>Phone:</strong> {{ $purchase->supplier?->isupp_phone }}</div>
                            <div><strong>Email:</strong> {{ $purchase->supplier?->isupp_email }}</div><br>
                        </div>
                    </div>
                    <div>
                        <div><strong class="fs-1">Invoice No:</strong></div>
                         {{ $purchase->invoice_number }}
                    </div>
                    <div>
                        <div><strong class="fs-1">Invoice Description:</strong></div>
                        {{ $purchase->description }}
                    </div>
                    <div>
                        <div><strong class="fs-1">Purchase Type:</strong></div>
                        {{ ucfirst($purchase->purchase_type) }}
                    </div>
                </td>

                <!-- Right Column -->
                <td width="50%" valign="top" class="border-n">
                    <div>
                        <div><strong>Issue Date:</strong> {{ \Carbon\Carbon::parse($purchase->issue_date)->format('d M, Y') }}</div><br>
                    </div>
                    <div>
                        <div><strong>Purchase Date:</strong> {{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d M, Y') }}</div><br>
                    </div>
                    <div>
                        <div><strong>Total Before VAT:</strong> {{ $purchase->total_amount }}</div><br>
                    </div>
                    <div>
                        <div><strong>VAT Amount:</strong> {{ $purchase->vat_amount }}</div><br>
                    </div>
                    <div>
                        <div><strong>Net Total:</strong> {{ $purchase->net_total }}</div><br>
                    </div>
                </td>
            </tr>
        </table>

        <table width="100%" cellpadding="10" cellspacing="0" style="border-collapse: collapse;">
            <thead>
                <tr class="fs-1">
                    <th width="5%" style="border: 1px solid #000; padding: 5px;">S.L</th>
                    <th width="50%" style="border: 1px solid #000; padding: 5px;">Name</th>
                    <th width="15%" style="border: 1px solid #000; padding: 5px;">Un. Rate</th>
                    <th width="10%" style="border: 1px solid #000; padding: 5px;">Qty</th>
                    <th width="10%" style="border: 1px solid #000; padding: 5px;">Discount</th>
                    <th width="10%" style="border: 1px solid #000; padding: 5px;">Vat</th>
                    <th width="20%" style="border: 1px solid #000; padding: 5px;">Total</th>
                </tr>
            </thead>
            <tbody class="fs-1">
                @foreach ($purchase->details as $item)
                    <tr>
                        <td style="border: 1px solid #000; padding: 5px;">
                            {{ $loop->index + 1 }}
                        </td>
                        <td style="border: 1px solid #000; padding: 5px;">
                            @if ($item->item_id)
                                {{ $item->product?->item_deta_name ?? 'Product not found' }} - 
                                {{ $item->product?->item_deta_code}}
                            @else
                                {{ $item->service_name }}
                            @endif
                        </td>
                        <td style="border: 1px solid #000; padding: 5px; text-align: right;">
                            {{ $item->unit_price }}
                        </td>
                        <td style="border: 1px solid #000; padding: 5px; text-align: right;">
                            {{ $item->qty }}
                        </td>
                        <td style="border: 1px solid #000; padding: 5px; text-align: right;">
                            {{ $item->discount }}
                        </td>
                        <td style="border: 1px solid #000; padding: 5px; text-align: right;">
                            {{ $item->vat }}%
                        </td>
                        <td style="border: 1px solid #000; padding: 5px; text-align: right;">
                            {{ $item->total_amount }}
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
                        @if($purchase->notes)
                            <div style="margin-bottom: 10px;">
                                <strong>Notes:</strong>
                                <div>{{ $purchase->notes }}</div>
                            </div>
                        @endif
                        {{-- @if($purchase->attachments && count($purchase->attachments) > 0)
                            <div style="margin-bottom: 10px;">
                                <strong>Attachments:</strong>
                                <div>
                                    @foreach ($purchase->attachments as $attachment)
                                        {{ $loop->index + 1 }}.
                                        <a href="{{ $attachment }}" target="_blank" download="">
                                            {{ basename($attachment) }}
                                            &nbsp;
                                            <i class="fa fa-download"></i>
                                        </a>
                                        <br>
                                    @endforeach
                                </div>
                            </div>
                        @endif --}}
                    </div>
                </td>

                <!-- Right Column (50%) -->
                <td width="40%" valign="top" class="border-n">
                    <!-- Total Section -->
                    <div class="fs-1" style="font-size: 14px;">
                        <!-- Totals Section -->
                        <table width="100%" cellpadding="5" cellspacing="0" class="border-n">
                            <tr>
                                <td width="50%" class="border-n">Total Before VAT:</td>
                                <td width="50%" align="right" class="border-n">{{ $purchase->total_amount }}</td>
                            </tr>
                            <tr>
                                <td width="50%" class="border-n">VAT Amount:</td>
                                <td width="50%" align="right" class="border-n">{{ $purchase->vat_amount }}</td>
                            </tr>
                            <tr>
                                <td width="50%" class="border-n">Discount Amount:</td>
                                <td width="50%" align="right" class="border-n">{{ $purchase->discount_amount }}</td>
                            </tr>
                            <tr>
                                <td width="50%" class="border-n">Net Total:</td>
                                <td width="50%" align="right" class="border-n">{{ $purchase->net_total }}/-</td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>
    </div>
@endsection
