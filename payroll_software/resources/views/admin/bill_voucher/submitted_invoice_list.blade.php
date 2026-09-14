@extends('layouts.admin-master')
@section('title') submited bill voucher @endsection
@section('content')
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Submitted Invoice Records</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active">Submitted Invoice Records</li>
        </ol>
    </div>
</div>

<!-- Session Flash MEssage -->
<div class="row">
  <div class="col-md-2"></div>
  <div class="col-md-8">
    @if(Session::has('success'))
    <div class="alert alert-success alertsuccess" role="alert">
      <strong>{{Session::get('success')}}</strong>  
    </div>
    @endif

    @if(Session::has('error'))
    <div class="alert alert-warning alerterror" role="alert">
    <strong>{{Session::get('error')}}</strong>  
    </div>
    @endif
  </div>
  <div class="col-md-2"></div>
</div>


<!-- Already Submitted Inovice Record -->
<div class="row d-block" id="invoiceRecordList">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <div class="row">

                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table id="dt-vertical-scroll" class="table" cellspacing="0" width="100%">
                                <thead>
                                    <tr> 
                                        <th>S.N</th>
                                        <th>Invoice No</th>
                                        <th>Main Contractor</th>
                                        <th>Sub Contractor</th>
                                         <th>Project</th>
                                        <th>Date</th>
                                        <th>Total Amount</th>
                                        <th>VAT</th>
                                        <th>Total(VAT Inc.)</th>
                                        <th>Retention</th>
                                        <th>Bill Amount</th>
                                        <th>Remarks</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($invoice_records as $item)
                                    <tr>
                                        <td> {{ $loop->iteration }} </td>
                                        <td> {{ $item->invoice_no }} </td>
                                        <td> {{ $item->mainContractor->en_name }} </td>
                                        <td> {{ $item->subContractor->sb_comp_name }} </td>
                                        <td> {{ $item->proj_name }} </td>
                                        <td> {{ Carbon\Carbon::parse($item->submitted_date)->format('D, d F Y') }} </td>
                                        <td> {{ $item->items_grand_total_amount }} </td>
                                        <td> {{ $item->total_vat }} </td>
                                        <td> {{ $item->total_amount }} </td>
                                        <td> {{ $item->total_retention }} </td>
                                        <td> {{ $item->total_amount -  $item->total_retention }} </td>
                                        <td> {{ $item->remarks }} </td>                                   
                                        <td>
                                        <a href="{{ route('submited-bill-voucher-regenerate',$item->invoice_record_auto_id) }}" target="_blank"><i class="fas fa-eye fa-lg view_icon"></i></a>
                                            <span class="badge badge-pill badge-danger"></span>
                                            <a href="#" title="Edit" class="approve_button" data-toggle="modal"
                                                data-target="#invoiceStatsuModal-{{ $item->invoice_record_auto_id}}">
                                                @if ($item->invoice_status_id == 1)
                                                 Pending @else  Released @endif
                                            </a>
                                            
                                        </td>
                                           <!-- Invoice Update Modal -->
                                           <div class="modal fade" id="invoiceStatsuModal-{{ $item->invoice_record_auto_id }}"
                                            value="{{ $item->invoice_record_auto_id }}" tabindex="-1" role="dialog" aria-
                                            labelledby="demoModalLabel" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                     
                                                    <div class="modal-body">
                                                        <div class="row">
                                                        <form class="form-horizontal" id="employee-info-form"
                                                                method="post"
                                                                action="{{ route('updated.e.voucher.invoice.status') }}">
                                                                @csrf
                                                            <div class="col-sm-12">
                                                                <div class="card">    
                                                                    <input type="hidden" name = "invoice_record_auto_id" value = "{{ $item->invoice_record_auto_id }}" />                                                           
                                                                    <div class="form-group row custom_form_group{{ $errors->has('invoice_status') ? ' has-error' : '' }}">
                                                                        <label class="col-sm-5 control-label">Invoice Status:<span
                                                                                class="req_star">*</span></label>
                                                                        <div class="col-sm-7">
                                                                            <select class="form-control" id="invoice_status" name="invoice_status" required>
                                                                                <option value="">Select Invoice Status</option>
                                                                                <option value="1">Pending</option>
                                                                                <option value="5">Released</option>
                                                                            </select>
                                                                            
                                                                        </div>
                                                                    </div>                                                                  
                                                                    <button type="submit" id="onSubmit" onclick="formValidation();" class="btn btn-primary waves-effect"  style="border-radius: 15px;width: 100px; height: 40px; letter-spacing: 1px;">Update</button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            </div>
                                           <!-- Invoice Update Modal End -->
                                      
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection