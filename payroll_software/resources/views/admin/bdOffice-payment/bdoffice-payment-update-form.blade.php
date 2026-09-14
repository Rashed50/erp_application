@extends('layouts.admin-master')
@section('title') Office Payment @endsection
@section('content')
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Employee Details  </h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        </ol>
    </div>
</div>
<!-- Session Message Section -->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        @if(Session::has('success'))
        <div class="alert alert-success alertsuccess" role="alert">
            <strong>{{Session::has('success')}}</strong>
        </div>
        @endif
        
        @if(Session::has('error'))
        <div class="alert alert-warning alerterror" role="alert">
            <strong>{{Session::get('error')}} </strong>
        </div>
        @endif
    </div>
</div>

<div class="row">
    <div class="col-md-1"></div>
        <div class="col-md-10">
            <div class="select_employee">
                <div class="card">
                    <form class="form-horizontal" id="bd-office-update-form" enctype="multipart/form-data" action="{{ route('employee.payment.from-bdoffice.update-request') }}" method="POST">
                    @csrf
                        <div class="card-body card_form"> 
                            <input type="hidden" class="form-control" id="bdofpay_auto_id" name="bdofpay_auto_id" value="{{$employee->bdofpay_auto_id}}" >
                            <input type="hidden" class="form-control" id="emp_auto_id" name="emp_auto_id" value="{{$employee->emp_auto_id}}" >

                                
                            <div class="row form-group custom_form_group{{ $errors->has('employee_id') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label d-block" style="text-align: left;">Employee ID : </label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" id="employee_id" name="employee_id" value="{{$employee->employee_id}}" readonly>
                            </div>

                           
                            <label class="col-sm-2 control-label d-block" style="text-align: left;">Passport : </label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" id="passport_no" name="passport_no" value="{{$employee->passfort_no}}, {{$employee->passfort_expire_date}}" readonly>
                            </div>

                            </div>
            

                            <div class="row form-group custom_form_group{{ $errors->has('employee_name') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label d-block" style="text-align: left;">Employee Name : </label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" id="employee_name" name="employee_name" value="{{$employee->employee_name}}" readonly>
                                
                            </div>
                            
                            <label class="col-sm-2 control-label d-block" style="text-align: left;">Address : </label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" id="details" name="details" value="{{$employee->details}}" readonly>
                            </div>

                            

                            </div>


                            <div class="row form-group custom_form_group{{ $errors->has('iqama_no') ? ' has-error' : '' }}">
                                <label class="col-sm-2 control-label d-block" style="text-align: left;">Iqama No : </label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" id="iqama_no" name="iqama_no" value="{{$employee->akama_no}}, {{$employee->akama_expire_date}}" readonly>
                                </div>

                                <label class="col-sm-2 control-label d-block" style="text-align: left;">Mobile No : </label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" id="mobile_no" name="mobile_no" value="{{$employee->mobile_no}}" readonly>
                                </div>

                            </div>

                            <div class="row form-group custom_form_group{{ $errors->has('approved_amount') ? ' has-error' : '' }}">
                                <label class="col-sm-4 control-label d-block" style="text-align: left;">Approved Amount(SAR) </label>
                                <div class="col-sm-2">
                                    <input type="text" class="form-control" id="approved_amount" name="approved_amount" value="{{$employee->approved_amount}}" readonly>
                                </div>

                                <label class="col-sm-2 control-label d-block" style="text-align: left;">Total Paid Amount </label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" id="total_paid_amount" name="total_paid_amount" value="{{$employee->sar_paid_total_amount}}" readonly>
                                </div>

                            </div>

 
                            <hr>
                            <br/>
                            <!-- Amount Receiver Details -->
                            <div class="row form-group custom_form_group{{ $errors->has('receiver_name') ? ' has-error' : '' }}">
                                <label class="col-sm-2 control-label d-block" style="text-align: left;">Receiver Name:<span
                                                            class="req_star">*</span></label></label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" id="receiver_name" name="receiver_name" value=""  >
                                </div>

                                <label class="col-sm-2 control-label d-block" style="text-align: left;"> Receiver Mobile:<span
                                                            class="req_star">*</span></label> </label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" id="receiver_mobile" name="receiver_mobile" value=""  >
                                    
                                </div>

                            </div>

                            <div class="row form-group custom_form_group{{ $errors->has('receiver_address') ? ' has-error' : '' }}">
                                <label class="col-sm-2 control-label d-block" style="text-align: left;">Receiver Address:</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" id="receiver_address" name="receiver_address" value=""  >
                                </div>

                                <label class="col-sm-2 control-label d-block" style="text-align: left;"> Relation with Emp<span
                                                            class="req_star">*</span></label></label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" id="relation_with_emp" name="relation_with_emp" value=""  >
                                    
                                </div>

                            </div>


                            <div class="row form-group custom_form_group{{ $errors->has('payment_method') ? ' has-error' : '' }}">
                                <label class="col-sm-2 control-label d-block" style="text-align: left;">Payment Method:<span
                                                            class="req_star">*</span></label> </label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" id="payment_method" name="payment_method" value=""  >
                                </div>

                                <label class="col-sm-2 control-label d-block" style="text-align: left;"> Payment Transaction Details </label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" id="transaction_details" name="transaction_details" value=" "  >
                                    
                                </div>

                            </div>

                           

                            <div class="row form-group custom_form_group{{ $errors->has('payment_received_date') ? ' has-error' : '' }}">
                                 

                                 <label class="col-sm-2 control-label d-block" style="text-align: left;"> Pay Date  </label>
                                 <div class="col-sm-4">
                                 <input type="date" name="payment_received_date" class="form-control"
                                 max="{{ Carbon\Carbon::now()->format('Y-m-d') }}" value="{{Carbon\Carbon::now()->format('Y-m-d')}}">
                                 </div>

                                 <label class="col-sm-2 control-label d-block" style="text-align: left;">Amount (SAR) : </label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" id="sar_amount" name="sar_amount" value="{{$employee->sar_unpaid_amount}}" onkeyup="calculateTotalAmount()" required> 
                                </div>
 
                             </div>

                            <div class="row form-group custom_form_group  }}">
                                

                                <label class="col-sm-2 control-label d-block" style="text-align: left;">Exchange Rate:<span class="req_star">*</span> </label>
                                <div class="col-sm-4">
                                    <input type="number" class="form-control" id="exchange_rate" name="exchange_rate" value="0" onkeyup="calculateTotalAmount()" placeholder="Enter Exchange Rate" >
                               
                                </div>

                                <label class="col-sm-2 control-label d-block" style="text-align: left;">Amount(BDT):<span class="req_star">*</span> </label>
                                <div class="col-sm-4">
                                    <input type="number" class="form-control" id="bdt_paid_amount" name="bdt_paid_amount" value="0" >
                                </div>

                            </div>

                            <div class="form-group row custom_form_group">
                        <label class="col-sm-2 control-label">Payment Slip:</label>
                        <div class="col-sm-4">
                            <div class="input-group">
                                <span class="input-group-btn">
                                    <span class="btn btn-default btn-file btnu_browse">
                                        Browse… <input type="file" name="payment_slip" id="imgInp3">
                                    </span>
                                </span>
                                <input type="text" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <img id='img-upload3' class="upload_image" />
                        </div>
                    </div>

                            <div class="card-footer card_footer_button text-center">
                                <div class="col-md-3"></div>
                                <div class="col-sm-3">
                                    <button type="submit" class="btn btn-primary waves-effect">Udpate</button>
                                </div>                                   
                                 <div class="col-sm-3">
                                   <button type="submit" class="btn btn-primary waves-effect" target="_blank" >Print</button>
                                </div>
                                <div class="col-md-3"> 
                                        <input class="form-check-input" type="checkbox" checked   name="print-checkbox"
                                            id="flexCheckDefault">
                                        <label >
                                           Click Here to Print
                                        </label>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>
     
    <div class="col-md-1"></div>
</div>

<!-- Payment Employee list -->

 

<script type="text/javascript">

    $(document).ready(function () {

        $("#bd-office-update-form").validate({
 
            rules: {
                
                receiver_name: {
                    required: true,
                },
                paid_amount: {
                    required: true,
                },
                receiver_mobile: {
                    required: true,
                },
                relation_with_emp: {
                    required: true,
                },
                payment_method: {
                    required: true,
                },
                // payment_slip: {
                //     required: true,
                // },
                
            },

            messages: {
                receiver_name: {
                    required: "You Must Be Input This Field!",
                },
                paid_amount: {
                    required: "You Must Be Input This Field!",
                },
                empreceiver_mobile_name: {
                    required: "You Must Be Input This Field!",
                },
                relation_with_emp: {
                    required: "You Must Be Input This Field!",
                },
                payment_method: {
                    required: "You Must Be Input This Field!",
                },
                // payment_slip:{"You Must be Upload Payment Slip"}
                
            },
        });
    });

    function calculateTotalAmount(){

             var rate = document.getElementById('exchange_rate').value != "" ? parseFloat(document.getElementById('exchange_rate').value) : 0;
             var sar_amount = document.getElementById('sar_amount').value != "" ? parseFloat(document.getElementById('sar_amount').value) : 0;
             document.getElementById("bdt_paid_amount").value = (rate*sar_amount).toFixed(2);

        }
       



</script>
@endsection
