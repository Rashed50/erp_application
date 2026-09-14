@extends('layouts.admin-master')
@section('title', 'Show Purchase')
@section('content')
    <style>
        label {
            margin-bottom: 0;
        }
        .card-header{
            background-color: #F5F5F5;
            color: black;
            font-weight: bold;
        }
        .file_icon{
            color: rgb(49, 49, 209);
        }
        .td-name {
            width: 40%;
        }
        .td-value {
            width: 60%;
        }
        .downlode_btn{
            background: rgb(84, 81, 81);
            color: #F5F5F5
        }
        .downlode_btn:hover{
            background: rgb(61, 61, 210);
            color: #F5F5F5
        }

    </style>
    <div class=" mt-4">
        <h2 class="mb-4">Purchase 
            {{ $purchase->purchase_type === 'product' ? 'Product' : 'Service' }} 
            Invoice Details
            <div class="col-12 mt-3">
                <a class="btn btn-sm downlode_btn" target="_blank" href="{{route('admin.accounting.purchase.pdf', [$purchase['pur_id']])}}">
                    <i class="fa fa-download me-2" aria-hidden="true"></i>
                    Download
                </a>
            </div>
        </h2>


        <div class="row">
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-header">
                        Supplier Information
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <td class="td-name"><strong>Name:</strong></td>
                                    <td  class="td-name">{{ $purchase->supplier?->isupp_name }}</td>
                                </tr>
                                <tr>
                                    <td class="td-name"><strong>Phone:</strong></td>
                                    <td class="td-value">{{ $purchase->supplier?->isupp_phone }}</td>
                                </tr>
                                <tr>
                                    <td class="td-name"><strong>Email:</strong></td>
                                    <td class="td-value">{{ $purchase->supplier?->isupp_email }}</td>
                                </tr>
                                <tr>
                                    <td class="td-name"><strong>Address:</strong></td>
                                    <td class="td-value">{{ $purchase->supplier?->isupp_contact_address }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-header">
                        Financial Information
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <td class="td-name"><strong>Total Before VAT:</strong></td>
                                    <td class="td-value text-right">{{ $purchase->total_amount }}/-</td>
                                </tr>
                                <tr>
                                    <td class="td-name"><strong>VAT Amount:</strong></td>
                                    <td class="td-value text-right">{{ $purchase->vat_amount }}/-</td>
                                </tr>
                                <tr>
                                    <td class="td-name"><strong>Discount Amount:</strong></td>
                                    <td class="td-value text-right">{{ $purchase->discount_amount }}/-</td>
                                </tr>
                                <tr>
                                    <td class="td-name"><strong>Net Total:</strong></td>
                                    <td class="td-value text-right">{{ $purchase->net_total }}/-</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>


            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-header">
                        Admin Information
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <td><strong>Created Admin </strong></td>
                                    <td>
                                        Name: {{ $purchase->createdAdmin->name }} <br>
                                        Email: {{ $purchase->createdAdmin->email }} <br>
                                        Date: {{ $purchase->created_at }}
                                    </td>
                                </tr>

                                <tr>
                                    <td><strong>Updated Admin</strong></td>
                                    <td>
                                        Name: {{ $purchase->updatedAdmin->name ?? 'N/A' }} <br>
                                        Email: {{ $purchase->updatedAdmin->email ?? 'N/A' }} <br>
                                        Date: {{ $purchase->updated_at }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>


            <div class="col-md-12">
                <div class="card mb-3">
                    <div class="card-header">
                        Invoice Details
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <td style="width: 20%"><strong>Invoice No:</strong></td>
                                    <td style="width: 80%">{{ $purchase->invoice_number }}</td>
                                </tr>
                                <tr>
                                    <td style="width: 20%"><strong>Issue Date:</strong></td>
                                    <td style="width: 80%">{{ \Carbon\Carbon::parse($purchase->issue_date)->format('d M, Y') }}</td>
                                </tr>
                                <tr>
                                    <td style="width: 20%"><strong>Purchase Date:</strong></td>
                                    <td style="width: 80%">{{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d M, Y') }}</td>
                                </tr>
                                <tr>
                                    <td style="width: 20%"><strong>Description:</strong></td>
                                    <td style="width: 80%">{{ $purchase->description }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

        <div class="card mb-4">
            <div class="card-header">
                Item Details
            </div>
            <div class="card-body">
                <table class="table table-bordered custom_table mb-0 no-footer">
                    <thead>
                        <tr>
                            <th class="text-center" width="5%">S.L</th>
                            <th class="text-center" width="45%">{{ $purchase->purchase_type === 'product' ? 'Product' : 'Service' }}  Name</th>
                            <th class="text-center" width="10%">Unit Price</th>
                            <th class="text-center" width="5%">Quantity</th>
                            <th class="text-center" width="10%">Price</th>
                            <th class="text-center" width="5%">VAT(%)</th>
                            <th class="text-center" width="10%">VAT Amount</th>
                            <th class="text-center" width="15%">Total (Price + VAT)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($purchase->details as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    @if ($item->item_id)
                                        {{ $item->product?->item_deta_name ?? 'Product not found' }} - 
                                        {{ $item->product?->item_deta_code}}
                                    @else
                                        {{ $item->service_name ?? 'Service not found' }}
                                    @endif
                                </td>
                                <td class="text-right">{{ $item->unit_price }}</td>

                                <td class="text-right">{{ $item->qty }}</td>

                                <td class="text-right">
                                    {{ number_format(($item->unit_price * $item->qty)) }}
                                </td>

                                <td class="text-right">
                                    {{ $item->vat }}%
                                </td>

                                <td class="text-right">
                                    {{ number_format(($item->unit_price * $item->qty * $item->vat / 100), 2) }}
                                </td>

                                <td class="text-right">
                                    {{ number_format(($item->unit_price * $item->qty) + ($item->unit_price * $item->qty * $item->vat / 100), 2) }}/-
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
    
                <div class="offset-md-8 col-md-4" style="font-size: 18px">
                    <div class="row mt-2">
                        <div class="col-md-6">
                            Total Before VAT:
                        </div>
                        <div class="col-md-6 text-right">
                            {{ $purchase->total_amount }}/-
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            VAT Amount:
                        </div>
                        <div class="col-md-6 text-right">
                            {{ $purchase->vat_amount }}/-
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            Discount Amount:
                        </div>
                        <div class="col-md-6 text-right">
                            {{ $purchase->discount_amount }}/-
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            Net Total:
                        </div>
                        <div class="col-md-6 text-right">
                            {{ $purchase->net_total }}/-
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Notes and Attachments
            </div>
            <div class="card-body">
                @if($purchase->notes)
                    <div class="mb-3">
                        <label><strong>Notes:</strong></label>
                        <p>{{ $purchase->notes }}</p>
                    </div>
                @endif

                @if($purchase->attachments && count($purchase->attachments) > 0)
                    <div class="mb-3">
                        <label><strong>Attachments:</strong></label>
                        <ul class="list-unstyled">
                            @foreach ($purchase->attachments as $attachment)
                                <li>
                                    {{-- <a class="ms-2" href="{{ url('storage/' . $attachment->file_path) }}" target="_blank" download=""> --}}
                                    <a class="ms-2" href="{{ url('storage/' . $attachment->file_path) }}" target="_blank">
                                        <i class="fa fa-file me-2 fs-1 mb-2 file_icon" aria-hidden="true"></i> 
                                        {{ basename($attachment->file_path) }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

            </div>
        </div>
    </div>
@endsection
