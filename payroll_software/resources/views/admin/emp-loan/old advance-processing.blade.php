@extends('layouts.admin-master')
@section('title') Advance Processing @endsection
@section('content')


<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Employee Advance Processing </h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        </ol>
    </div>
</div>
<!-- Session flash Message -->
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


{{-- Advace Processing --}}
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-lg-8">
         
        <div class="card">
            <div class="card-header"></div>
            <div class="card-body card_form">

                 <div class="form-group row custom_form_group">
                    <label class="col-sm-3 control-label"> Porject:<span class="req_star">*</span></label>
                    <div class="col-sm-7">
                        <select class="selectpicker" id="project_ids" multiple="multiple" >

                            @foreach($projects as $aproject)
                            <option value="{{$aproject->proj_id}}" {{$aproject->proj_id}}>{{$aproject->proj_name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                 <div class="form-group row custom_form_group">
                        <label class="col-sm-3 control-label">Sponsor:</label>
                        <div class="col-sm-7">
                                <select class="selectpicker" id="sponsor_ids" multiple="multiple" >
                                @foreach($sponsors as $spons)
                                    <option value="{{ $spons->spons_id }}">{{ $spons->spons_name }}</option>
                                @endforeach
                                </select>
                        </div>
                    </div>

                
                
                    <?php  $current_month = date('m'); 
                        $months = array(12);
                        for ($m=1; $m<=12; $m++) {
                        $months[$m-1] = date('F', mktime(0,0,0,$m, 1, date('Y')));
                         }         
                    ?>


                <div class="form-group row custom_form_group{{ $errors->has('year') ? ' has-error' : '' }}">
                            <label class="control-label col-sm-3"  >Salary Month:</label>
                          <div class="col-sm-3">
                            <select class="form-select" id ="month" name="month" required>
                                @foreach($months as $mm)
                                <option value="{{$loop->iteration}}">{{$mm}}</option>
                                @endforeach
                                
                            </select>
                          </div>
                          
                          <label class="control-label col-sm-1" >Year:</label>
                          <div class="col-sm-3">
                            <select class="form-select" id="year" name="year">
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
                            <label class="col-sm-3 control-label">Iqama Deduction Amount :</label>
                            <div class="col-sm-7">
                                <input type="number"  class="form-control"  id="iqama_amount"  name="iqama_amount" value="1000" min="0" required>
                            </div>
                        </div> 
        
                        <div class="form-group row custom_form_group">
                            <label class="col-sm-3 control-label">Other Deduction Amount :</label>
                            <div class="col-sm-4">
                                <input type="number"  class="form-control" id="other_amount" name="other_amount" value="300" min="0"   required>
                            </div>
                             &nbsp;&nbsp;&nbsp;
                            <div class="col-sm-4">
                                <input type="checkbox" class="form-check-input" id="only_hourly_emp_direct_process" name="only_hourly_emp_direct_process" value="1" >
                                <label class="form-check-label" for="check1">Direct Setup on Asloob Hourly Employees</label>
                            </div>
                        </div> 
                 

                 </div>
            <div class="card-footer card_footer_button text-center">
                <button onclick="AdvanceProcessingByProjectWise()" class="btn btn-primary btn-sm emp-sarch">Advance Process</button>
            </div>
        </div>

    </div>
    <div class="col-md-2"></div>
</div>


<!-- Multiple Employee ID Base Advace Processing -->
<div class="row">
    <div class="col-md-2">    </div>
    <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-12">
                            <h3 class="card-title card_top_title salary-generat-heading"> Multiple Emplyee ID</h3>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
                <div class="card-body card_form" style="padding-top: 0;">
                    <div class="form-group row custom_form_group{{ $errors->has('emp_id') ? ' has-error' : '' }}">
                        <label class="control-label col-sm-2" style="text-align: left;">Employee ID:</label>
                        <div class="col-sm-6">                            
                            <input type="text" class="form-control typeahead" placeholder="Enter Multiple Employee ID" name="multiple_emp_Id" id="multiple_empId" value="{{ old('multiple_emp_Id') }}">
                            @if ($errors->has('emp_id'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('emp_id') }}</strong>
                            </span>
                            @endif
                        </div>
                        <div class ="col-sm-4">
                                <button onclick="AdvanceProcessingWithMultipleEmployeeID()" class="btn btn-primary btn-sm emp-sarch">ADVANCE PROCESS</button>
                        </div>                         
                    </div>
                </div>

            </div>
       
    </div>
    <div class="col-md-2">
            
    </div>
</div>

<!-- Employee Food Amount Setting -->
<div class="row">
    <div class="col-md-2">    </div>
    <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-12">
                            <h3 class="card-title card_top_title salary-generat-heading">Employees Food Amount Setting</h3>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
                <div class="card-body card_form" style="padding-top: 0;">                  
                    <div class="card-body card_form" style="padding-top: 0;">
                        <div class="form-group row custom_form_group{{ $errors->has('emp_id') ? ' has-error' : '' }}">                       
                            <div class="col-sm-8">   </div>
                            <div class ="col-sm-4">
                                <button   data-toggle="modal" data-target="#multi_emp_food_setting_modal" class="btn btn-primary btn-sm emp-sarch">Food Amount Setting</button>
                            </div>                         
                        </div>
                    </div>
                </div>

            </div>
       
    </div>
    <div class="col-md-2">
            
    </div>
</div>

<!--  Multiple Employee Food Setting Modal-->
<div class="modal fade" id="multi_emp_food_setting_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Mutiple Employee Food Setting <span class="text-danger" id="errorData"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
                <div class="modal-body">
                <form id="updaterecord"  method="post" action="{{route('update.advance-installAmount')}}"   onsubmit="updatebtn.disabled = true;" >            
                       @csrf                
                        <input type="hidden" name="operation_type" id="operation_type" value="2">           
                        <div class="form-group row custom_form_group">
                            <label class="control-label col-sm-3">Wrking Project:</label>
                            <div class="col-sm-7">
                            <select class="form-select" name="project_id" id="project_id" required >
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
                            <select class="form-select" name="month" required>
                                @foreach($months as $mm)
                                <option value="{{$loop->iteration}}">{{$mm}}</option>
                                @endforeach
                                <!--<option value="1">January</option>-->
                                <!--<option value="2">Februry</option>-->
                                <!--<option value="3">March</option>-->
                                <!--<option value="4">April</option>-->
                                <!--<option value="5">May</option>-->
                                <!--<option value="6">June</option>-->
                                <!--<option value="7">July</option>-->
                                <!--<option value="8">August</option>-->
                                <!--<option value="9">September</option>-->
                                <!--<option value="10">October</option>-->
                                <!--<option value="11">November</option>-->
                                <!--<option value="12">December</option>                             -->
                            </select>
                          </div>
                        </div>
                        {{-- Year List --}}
                        <div class="form-group row custom_form_group{{ $errors->has('year') ? ' has-error' : '' }}">
                          <label class="control-label col-sm-3" style="text-align: left;">Salary Year:</label>
                          <div class="col-sm-7">
                            <select class="form-select" name="year">
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


{{-- end section --}}

<div class="overlay"></div>



<!-- added this for Multiple Selection dropdownlist  -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta2/css/bootstrap-select.min.css" integrity="sha512-mR/b5Y7FRsKqrYZou7uysnOdCIJib/7r5QeJMFvLNHNhtye3xJp1TdJVPLtetkukFn227nKpXD9OjUc09lx97Q==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta2/js/bootstrap-select.min.js" integrity="sha512-FHZVRMUW9FsXobt+ONiix6Z0tIkxvQfxtCSirkKc5Sb4TKHmqq1dZa8DphF0XqKb3ldLu/wgMa8mT6uXiLlRlw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
 

<script>
    $(document).on({
        ajaxStart: function() {
            $("body").addClass("loading");
        },
        ajaxStop: function() {
            $("body").removeClass("loading");
        }
    });
    function AdvanceProcessingByProjectWise() {
        var project_ids = $('#project_ids').val();
        EmployeeAdvanceProcessing(project_ids,"");
    }

    function AdvanceProcessingWithMultipleEmployeeID() {
        var multiple_emp_id = $('#multiple_empId').val();
        EmployeeAdvanceProcessing([],multiple_emp_id);
    }

    function EmployeeAdvanceProcessing(project_ids,multiple_emp_id) {
        
        var iqama_amount = document.getElementById('iqama_amount').value != "" ? parseFloat(document.getElementById('iqama_amount').value) : 0 ;
        var other_amount = document.getElementById('other_amount').value != "" ? parseFloat(document.getElementById('other_amount').value) : 0 ;
        var sponsor_ids = $('#sponsor_ids').val();
        var hourly_emp_checkBox = document.getElementById("only_hourly_emp_direct_process");
        var isChecked = hourly_emp_checkBox.checked == true ? 1:0;
         
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: {
                project_ids: project_ids,
                sponsor_ids:sponsor_ids,
                mul_emp_id: multiple_emp_id,
                month:$('#month').val(),
                year:$('#year').val(),
                iqama_amount: iqama_amount,
                other_amount:other_amount,
                hourly_emp_direct_process:isChecked,
            },
            url: "{{ route('addvance.processing.request') }}",
            success: function(response) {
                if (response.status == 200) {
                
                   showSweetAlertMessage('success', response.message+'');
                } else {
                    showSweetAlertMessage('error', 'Operation Failed, '+ response.error);
                }
            }
        });

    }
    
     function showSweetAlertMessage(type,message){
        const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            })
                Toast.fire({
                    type: type,
                    title: message,
                })
    }
</script>
@endsection