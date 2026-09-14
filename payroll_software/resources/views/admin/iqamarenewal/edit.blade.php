@extends('layouts.admin-master')
@section('title')Update Iqama Expense @endsection
@section('content')

<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title"> Update Iqama Renewal Expense</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-7">
        @if(Session::has('success'))
          <div class="alert alert-success alertsuccess" role="alert" style="margin-left: -20px">
             <strong>{{Session::get('success')}}</strong>
          </div>
        @endif
        @if(Session::has('error'))
          <div class="alert alert-warning alerterror" role="alert" style="margin-left: -20px">
          <strong>{{Session::get('error')}}</strong>
          </div>
        @endif
    </div>
    <div class="col-md-2"></div>
</div>
<div class="row">
    <div class="col-lg-12">
        <form class="form-horizontal" method="post" action="{{ route('update-iqamarenewal-fee') }}">
          @csrf
          <div class="card">

              <div class="card-body card_form">


                    <input type="hidden" name="id" value="{{ $data->IqamaRenewId }}">
                     <div class="form-group row custom_form_group">
                        <div class="form-group row col-md-6">

                                <label class="col-sm-4 control-label">Jawazat Fee:<span class="req_star">*</span></label>
                                <div class="col-sm-6">
                                        <input type="number"   class="form-control" id="jawazat_fee" name="jawazat_fee"  value="{{ $data->jawazat_fee }}"  onkeyup="calculateTotalAmount()" required>
                                        @if ($errors->has('jawazat_fee'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('Jawazat Fee Required') }}</strong>
                                            </span>
                                        @endif
                                </div>
                        </div>
                        <div class="form-group row col-md-6">

                                <label class="col-sm-4 control-label">Maktab Al Amal Fee:<span class="req_star">*</span></label>
                                <div class="col-sm-6">
                                        <input type="number" placeholder="Maktab Al Amal Fee" class="form-control" id="maktab_alamal_fee" name="maktab_alamal_fee" value="{{ $data->maktab_alamal_fee }}" onkeyup="calculateTotalAmount()"   required>
                                        @if ($errors->has('maktab_alamal_fee'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('Maktab Al Amal Fee Required') }}</strong>
                                            </span>
                                        @endif
                                </div>
                        </div>
                    </div>

                    <div class="form-group row custom_form_group">
                        <div class="form-group row col-md-6">
                                <label class="col-sm-4 control-label">VISA Amount:<span class="req_star">*</span></label>
                                <div class="col-sm-6">
                                        <input type="number" placeholder="BD Amount" class="form-control" id="bd_amount" name="bd_amount"  value="{{ $data->bd_amount }}" onkeyup="calculateTotalAmount()" required>
                                        @if ($errors->has('bd_amount'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('Jawazat Fee Required') }}</strong>
                                            </span>
                                        @endif
                                </div>
                        </div>
                        <div class="form-group row col-md-6">

                                <label class="col-sm-4 control-label">Medical Inssurance:<span class="req_star">*</span></label>
                                <div class="col-sm-6">
                                        <input type="number"  class="form-control" id="medical_insurance" name="medical_insurance"  value="{{ $data->medical_insurance }}" onkeyup="calculateTotalAmount()"   required>
                                        @if ($errors->has('medical_insurance'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('Medical Inssurance') }}</strong>
                                            </span>
                                        @endif
                                </div>
                        </div>
                    </div>

                    <div class="form-group row custom_form_group">
                        <div class="form-group row col-md-6">
                                <label class="col-sm-4 control-label">Jawazat Fee Penalty:<span class="req_star">*</span></label>
                                <div class="col-sm-6">
                                    <input type="number" placeholder="" class="form-control" id="jawazat_penalty" name="jawazat_penalty"  value="{{ $data->jawazat_penalty }}" onkeyup="calculateTotalAmount()"  required>
                                </div>
                        </div>
                        <div class="form-group row col-md-6">

                                <label class="col-sm-4 control-label">Others:<span class="req_star">*</span></label>
                                <div class="col-sm-6">
                                        <input type="number" placeholder="" class="form-control" id="others_fee" name="others_fee"  value="{{ $data->others_fee }}" onkeyup="calculateTotalAmount()" required>
                                        @if ($errors->has('others_fee'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('OThers') }}</strong>
                                            </span>
                                        @endif
                                </div>
                        </div>
                    </div>

                    <!--<div class="form-row">-->
                    <!--         <div class="col-sm-2"> <span class="req_star"><b> Total Expense</b> </span> </div>-->
                            <!-- <label class="col-sm-4 control-label">Iqama Renewal Total Expense:<span class="req_star">*</span></label> -->
                    <!--        <div class="col-sm-8">-->
                    <!--            <input type="text" class="form-control typeahead" placeholder="" name="total_amount" id="total_amount" value="{{ $data->total_amount }}"   required readonly>-->
                    <!--        </div>-->
                    <!--        <div class="col-sm-2"> </div>-->
                    <!--</div>-->
                    <div class="form-group row custom_form_group">
                          <label class="col-sm-2 control-label"> <span class="req_star"> <b>Total Amount</b></span></label>
                       <div class="col-sm-8">
                           <input type="text" class="form-control typeahead" placeholder="" name="total_amount" id="total_amount" value="{{ $data->total_amount }}"   required readonly>
                       </div>
                       <div class="col-sm-2"> </div>
                    </div>
                    <br>
                    <div class="form-group row custom_form_group">
                        <div class="form-group row col-md-6">
                            <label class="col-sm-4 control-label"> Renewal Duration:<span class="req_star">*</span></label>
                            <div class="col-sm-6">
                                <select class="form-select" name="duration" id="duration">
                                    {{-- <option value="{{ $data->duration }}" {{ $data->duration == $data->duration ? 'selected' : '' }} >{{ $data->duration }} Months</option> --}}
                                     @for ($i = 1; $i <= 72; $i++)
                                        <option value="{{ $i }}" {{ $i == $data->duration ? 'selected' : '' }}>
                                            {{ $i }} Month{{ $i > 1 ? 's' : '' }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="form-group row col-md-6">
                            <label class="col-sm-4 control-label">Renewal Date:</label>
                            <div class="col-sm-6">
                            <input type="date" name="renewal_date" class="form-control"  value="{{ $data->renewal_date }}">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row custom_form_group">
                        <div class="form-group row col-md-6">
                                <label class="col-sm-4 control-label">Payment Number:<span class="req_star">*</span></label>
                                <div class="col-sm-6">
                                    <input type="text" placeholder="Payment Number" class="form-control" id="payment_number"  name="payment_number" value="{{$data->payment_number}}" >
                                </div>
                        </div>
                        <div class="form-group row col-md-6">
                            <label class="col-sm-4 control-label">Payment Date:</label>
                            <div class="col-sm-6">
                                <input type="date" name="payment_date" class="form-control" max="{{ Carbon\Carbon::now()->format('Y-m-d') }}" value="<?= $data->payment_date != null ? $data->payment_date : date("Y-m-d") ?>">
                            </div>
                        </div>
                    </div>

                    <div class="form-group row custom_form_group">
                        <div class="form-group row col-md-6">
                            <label class="col-sm-4 control-label">Renewal Status:<span class="req_star">*</span></label>
                            <div class="col-sm-6">
                                <select class="form-control" name="renewal_status" id="renewal_status">
                                    <option value="{{ $data->renewal_status }}" selected>
                                        <?php
                                            if($data->renewal_status == 1)
                                             echo "Initial Step" ;
                                            elseif($data->renewal_status == 2)
                                           echo "Payment Initialize" ;
                                           elseif($data->renewal_status == 3)
                                           echo "Payment Pending" ;
                                           elseif($data->renewal_status == 4)
                                           echo "Payment Completed" ;
                                           elseif($data->renewal_status == 5)
                                           echo "Renewal Completed" ;

                                        ?> </option>
                                    <option value="1"> Initial Step </option>
                                    <option value="2"> Payment Initialize</option>
                                    <option value="3">Payment Completed</option>
                                    <option value="4">Renewal Pending</option>
                                    <option value="5">Renewal Completed</option>

                                </select>
                            </div>
                        </div>
                        <div class="form-group row col-md-6">
                            <label class="col-sm-4 control-label">Expense Paid By:</label>
                            <div class="col-sm-6">
                                <select class="form-control" name="expense_paid_by" id="expense_paid_by">
                                    <option value="{{ $data->expense_paid_by }}" selected > {{ $data->expense_paid_by == 1 ? "Self" : "Company"}}</option>
                                    <option value="1"> Self</option>
                                    <option value="2"> Company</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row custom_form_group">
                        <div class="form-group row col-md-6">
                                <label class="col-sm-4 control-label">Reference Employee:<span class="req_star">*</span></label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control typeahead" placeholder="Input Employee ID" name="reference_emp_id" id="emp_id_search" value="{{$data->reference_emp_id}}" onkeyup="empSearch()" onfocus="showResult()" onblur="hideResult()">
                                <div id="showEmpId"></div>
                                @if ($errors->has('emp_id'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('emp_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row col-md-6">

                                <label class="col-sm-4 control-label">Iqama Expire at:<span class="req_star">*</span></label>
                                <div class="col-sm-6">
                                    <input type="date" name="iqama_expire_date" class="form-control" value="<?= $data->iqama_expire_date != null ? $data->iqama_expire_date : date("Y-m-d") ?>">
                                </div>


                        </div>
                    </div>
                     <div class="form-group row custom_form_group">
                            <div class="form-group row col-md-6">
                                <label class="col-sm-4 control-label text-right">Purpose:<span
                                    class="req_star">*</span></label>
                                <div class="col-sm-6">
                                    <select class="form-select" name="payment_purpose_id" id="payment_purpose_id" required>
                                        <option value="{{ $data->payment_purpose_id }}" selected>
                                            <?php
                                                if($data->payment_purpose_id == 1)
                                                 echo "Iqama Renewal" ;
                                                elseif($data->payment_purpose_id == 2)
                                               echo "Medical Insurance" ;
                                               elseif($data->payment_purpose_id == 3)
                                               echo "Exit-Re-Entry" ;
                                               elseif($data->payment_purpose_id == 4)
                                               echo "Family Iqama" ;
                                               elseif($data->payment_purpose_id == 5)
                                               echo "Family Medical Insurance" ;
                                               elseif($data->payment_purpose_id == 6)
                                                echo "Traffic Violation" ;
                                            ?>
                                        </option>
                                        <option value="1"> Iqama Renewal</option>
                                        <option value="2"> Medical Insurance</option>
                                        <option value="3"> Exit-Re-Entry</option>
                                        <option value="4"> Family Iqama</option>
                                        <option value="5"> Family Medical Insurance</option>
                                         <option value="6">Traffic Violation</option>
                                    </select>
                                    @if ($errors->has('payment_purpose_id'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('payment_purpose_id') }}</strong>
                                    </span>
                                    @endif
                                </div>

                            </div>
                            <div class="form-group row col-md-6">
                                <label class="col-sm-4 control-label">Remarks</label>
                                <div class="col-sm-6">
                                    <input type="text" placeholder="Remarks Type Here" class="form-control" id="remarks" value="{{ $data->remarks}}" name="remarks">
                                </div>
                            </div>
                    </div>



                     <div class="form-group row custom_form_group">
                            <label class="col-sm-2 control-label"></label>
                            <div class="col-sm-4">
                             @can('iqama-renewal-expense-approval')
                                <input type="checkbox" id="update_approved_chk" name="update_approved_chk" value="1">
                                Update & Approved
                             @endcan

                            </div>
                    </div>


              </div>

              <div class="card-footer card_footer_button text-center">
                  <button type="submit" id="onSubmit" class="btn btn-primary waves-effect">Update</button>
              </div>
          </div>
        </form>
    </div>
</div>

<script type = "text/javascript">
         function calculateTotalAmount() {

            var value1 = document.getElementById('jawazat_fee').value != "" ? parseFloat(document.getElementById('jawazat_fee').value) : 0;
            var value2 = document.getElementById('maktab_alamal_fee').value != "" ? parseFloat(document.getElementById('maktab_alamal_fee').value) : 0;
            var value3 = document.getElementById('bd_amount').value != "" ? parseFloat(document.getElementById('bd_amount').value) : 0;
            var value4 = document.getElementById('medical_insurance').value != "" ? parseFloat(document.getElementById('medical_insurance').value) : 0;
            var value5 = document.getElementById('others_fee').value != "" ? parseFloat(document.getElementById('others_fee').value) : 0;
            var jawazat_penalty = document.getElementById('jawazat_penalty').value != "" ? parseFloat(document.getElementById('jawazat_penalty').value) : 0;
            document.getElementById("total_amount").value = (value1+value2+value3+value4+value5+jawazat_penalty);

         }
</script>
@endsection
