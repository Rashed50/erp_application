@extends('layouts.admin-master')
@section('title')Advance Setting @endsection
@section('content')

<div class="row bread_part">
  <div class="col-sm-12 bread_col">
    <h4 class="pull-left page-title bread_title">Employee Monthly Advance Deduction Setting</h4>
    <ol class="breadcrumb pull-right">
      <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
      <li class="active"> Advance Setting</li>
    </ol>
  </div>
</div>

<div class="row">
  <div class="col-md-2"></div>
  <div class="col-md-8">
    @if(Session::has('success'))
    <div class="alert alert-success alertsuccess" role="alert" style="margin-left: -20px">
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


<div class="row">
  <div class="col-md-1"></div>
  <div class="col-md-10">
 
    <div class="card">
      <div class="card-body card_form">
        <!-- SEARCH Employee -->
        <div class="form-group row custom_form_group" style="margin-top:10px">
          <div class="col-md-2">
           </div>
          <label class="col-sm-3 control-label">Employee Id:<span class="req_star">*</span></label>
          <div class="col-sm-3">
            <input type="text" class="form-control" name="employee_id" id="employee_id" required placeholder="Employee ID Type Here " autofocus>

            <span id="error_through" style="color:red"></span>
          </div>
          <div class="col-sm-1">
            <button onclick="findEmployeeForAdvanceSetting()" class="btn btn-primary btn-sm emp-sarch">SEARCH</button>
          </div>
        </div>
        <!-- form -->
        <div id="advance_setting_form_section" class="d-none card_footer_button text-center" style="padding-top: 20px;">

            <form id="projectInchargeForm" action="{{ route('update.advance-installAmount') }}" method="post">
              @csrf
                <div class="row">
                    <!-- <div class="form-group row custom_form_group" style="margin-top:10px;color:red">-->
                    <!--    <label class="col-sm-2 control-label" id ="emp_id">   </label>-->
                    <!--    <label class="col-sm-3 control-label" id ="emp_name">   </label>-->
                    <!--    <label class="col-sm-2 control-label" id ="emp_iqama">   </label> -->
                    <!--    <label class="col-sm-2 control-label" id ="emp_salary">   </label>               -->
                    <!--</div>-->
                    
              <div class="form-group row custom_form_group" style="margin:0px;padding:0px; color:red">
                <label class="col-sm-3 control-label" id ="emp_id">   </label>
                <label class="col-sm-3 control-label" id ="emp_name">   </label>
                <label class="col-sm-3 control-label" id ="emp_salary">   </label>
              </div>

              <div class="form-group row row custom_form_group" style="margin:0px;padding:0px;color:red">
                <label class="col-sm-3 control-label" id ="iqama_no">   </label>
                <label class="col-sm-3 control-label" id ="passport_no">   </label>
              </div>
              
              
                  <div class="col-md-6">
                    <div class="form-wrap">
                        <input type="hidden" name="id" id="adv_pay_id" value="">
                        <input type="hidden" name="operation_type" id="operation_type" value="1">
                        <div class="form-group row custom_form_group">
                          <label class="col-sm-3 control-label">Total Iqama:</label>
                          <div class="col-sm-7">
                            <input type="text" id="totalIqama" class="form-control" value="" disabled>
                          </div>
                        </div>
                        <div class="form-group row custom_form_group">
                          <label class="col-sm-3 control-label">Installes Amount:</label>
                          <div class="col-sm-7">
                            <input type="text" id="installIqama" class="form-control" value="" disabled>
                          </div>
                        </div>
                        <div class="form-group row custom_form_group">
                          <label class="col-sm-3 control-label">Total Paid:</label>
                          <div class="col-sm-7">
                            <input type="text" id="payIqama" class="form-control" value="" disabled>
                          </div>
                        </div>
                        <div class="form-group row custom_form_group">
                        <label class="col-sm-3 control-label">Next Pay:</label>
                        <div class="col-sm-7">
                          <input type="number" id="nextPayIqama" name="nextPayIqama" class="form-control" value="">
                        </div>
                      </div>
                    </div>
                  </div>



                  <div class="col-md-6">
                    <div class="form-wrap">
                        <div class="form-group row custom_form_group">
                          <label class="col-sm-3 control-label">Total Others:</label>
                          <div class="col-sm-7">
                            <input type="text" id="totalOthers" class="form-control" value="" disabled>
                          </div>
                        </div>
                        <div class="form-group row custom_form_group">
                          <label class="col-sm-3 control-label">Installes Amount:</label>
                          <div class="col-sm-7">
                            <input type="text" id="installOthers" class="form-control" value="" disabled>
                          </div>
                        </div>
                        <div class="form-group row custom_form_group">
                          <label class="col-sm-3 control-label">Total Paid:</label>
                          <div class="col-sm-7">
                            <input type="text" id="payOthers" class="form-control" value="" disabled>
                          </div>
                        </div>
                        <div class="form-group row custom_form_group">
                        <label class="col-sm-3 control-label">Next Pay:</label>
                        <div class="col-sm-7">
                          <input type="number" id="nextPayOthers" name="nextPayOthers" class="form-control" value="">
                        </div>
                      </div>
                    </div>
                  </div>
                        <button type="submit" class="btn btn-primary btn-sm waves-effect"  style="display:inline-block; margin: 0 auto;">SAVE</button>
                </div>
            </form>
        </div>

      </div>
    </div>
    <!-- </form> -->
  </div>
  <div class="col-md-1"></div>
</div>


<!--  Multiple Employee Advance Setting Modal-->
<div class="modal fade" id="multi_emp_advance_setting_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document"> 
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Mutiple Employee Advance Setting <span class="text-danger" id="errorData"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
                <div class="modal-body">
                <form id="updaterecord"  method="post" action="{{route('update.advance-installAmount')}}"   onsubmit="updatebtn.disabled = true;" >            
                       @csrf                
                        <input type="hidden" name="operation_type" id="operation_type" value="2">           
                        <div class="form-group row custom_form_group">
                            <label class="control-label col-sm-3">Project Name:</label>
                            <div class="col-sm-7">
                            <select class="form-control" name="project_id" id="project_id" required >
                                <option value="">Select Project</option>
                                @foreach($projects as $aproject)
                                  <option value="{{$aproject->proj_id}}"> {{$aproject->proj_name}}</option>
                                @endforeach
                            </select>
                            <span class="error d-none" id="error_massage"></span>
                            </div>
                        </div> 
                        
                        {{-- Month List --}}
                        <div class="form-group row custom_form_group">
                          <label class="control-label col-sm-3" style="text-align: left;">Salary Month:</label>
                          <div class="col-sm-7">
                            <select class="form-control" name="month" required>
                                <option value="1">January</option>
                                <option value="2">Februry</option>
                                <option value="3">March</option>
                                <option value="4">April</option>
                                <option value="5">May</option>
                                <option value="6">June</option>
                                <option value="7">July</option>
                                <option value="8">August</option>
                                <option value="9">September</option>
                                <option value="10">October</option>
                                <option value="11">November</option>
                                <option value="12">December</option>                             
                            </select>
                          </div>
                        </div>
                        {{-- Year List --}}
                        <div class="form-group row custom_form_group{{ $errors->has('year') ? ' has-error' : '' }}">
                          <label class="control-label col-sm-3" style="text-align: left;">Salary Year:</label>
                          <div class="col-sm-7">
                            <select class="form-control" name="year">
                              @foreach(range(date('Y'), date('Y')-1) as $y)
                              <option value="{{$y}}" {{$y}}>{{$y}}</option>
                              @endforeach
                            </select>

                            @if ($errors->has('year'))
                            <span class="invalid-feedback" role="alert">
                              <strong>{{ $errors->first('year') }}</strong>
                            </span>
                            @endif
                          </div>
                        </div>
                        
                        <div class="form-group row custom_form_group">
                            <label class="control-label col-sm-3">Setting Type:</label>
                            <div class="col-sm-7">
                            <select class="form-control" name="adv_setting_type" id="adv_setting_type" required >
                                <option value="1">Iqama Deduction</option>
                                <option value="2">Other Deduction</option>
                            </select>
                            <span class="error d-none" id="error_massage"></span>
                            </div>
                        </div> 
        
                        <div class="form-group row custom_form_group">
                            <label class="col-sm-3 control-label">Amount :</label>
                            <div class="col-sm-7">
                                <input type="number" id="modal_total_overtime" class="form-control " name="amount" value="0" min="0"   required>
                            </div>
                        </div> 
         
                    <button type="submit" id="updatebtn" name="updatebtn"  class="btn btn-success">Update</button>
                  
                </div>
                
            </form>
        </div>
    </div>
</div>


<script type="text/javascript">

   // Enter Key Press Event Fire
    $('#employee_id').keydown(function(e) {
        if (e.keyCode == 13) {
          findEmployeeForAdvanceSetting();
        }
    })
    
    
  function findEmployeeForAdvanceSetting() {
      
    var emp_id = $("#employee_id").val();
    $("#advance_setting_form_section").addClass('d-none').removeClass('d-block');
    
    if(emp_id == null || emp_id == ''){
        showSweetAlertMessage('error', 'Invalid Employee ID');
        return;
    }
    
    $.ajax({
      type: 'POST',
      dataType: 'json',
      data: {
        emp_id: emp_id
      },
      url: "{{ route('findEmployeeForLoan') }}",
      success: function(response) {

         if (response.status != 200) {

            showSweetAlertMessage('error', 'employee not found');
            $("#advance_setting_form_section").addClass('d-none').removeClass('d-block');
            return;
        }

           $("#employee_id").val("");
           $("#emp_id").html("Employee ID: "+response.employee.employee_id);
           $("#emp_name").html('Name: '+response.employee.employee_name);
           $("#emp_salary").html('Salary Type: '+ ( response.employee.hourly_employee == null ? 'Basic' : 'Hourly'));
           $("#iqama_no").html('Iqama No: '+response.employee.akama_no);
           $("#passport_no").html('Passport No: '+response.employee.passfort_no);

           $('input[id="adv_pay_id"]').val(response.empAutoId);
           $('input[id="totalIqama"]').val(response.totalIqama);
           $('input[id="totalOthers"]').val(response.totalOthers);
           $('input[id="payIqama"]').val(response.totalPaidIqama);
           $('input[id="payOthers"]').val(response.totalPaidOthers);
            $('input[id="installIqama"]').val(response.salaryDetatils.iqama_adv_inst_amount);
            $('input[id="installOthers"]').val(response.salaryDetatils.other_adv_inst_amount);
            $('input[id="nextPayIqama"]').val(response.salaryDetatils.iqama_adv_inst_amount);
            $('input[id="nextPayOthers"]').val(response.salaryDetatils.other_adv_inst_amount);
            $("#error_through").text('');
           $("#advance_setting_form_section").removeClass('d-none').addClass('d-block');

        
        
        
      }
    });
  }

  function inchargeValidation() {
    var emp_id = $("#employee_id").val();
    $.ajax({
      type: 'POST',
      dataType: 'json',
      data: {
        emp_id: emp_id
      },
      url: "{{ route('check.valid-emp-id') }}",
      success: function(response) {
        if (response.status == 'error') {
          // $("form[id='registration']").submit(false);
        }
      }
    });

  }
</script>
<!-- validation -->
<script type="text/javascript">
  $(document).ready(function() {
      $("#projectInchargeForm").validate({
            rules: {
           
          },
          messages: {
              proj_name: {
                required: "You Must Be Select This Field!",
              },
            }
        });
   });
</script>
@endsection