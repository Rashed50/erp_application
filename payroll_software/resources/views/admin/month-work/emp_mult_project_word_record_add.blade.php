@extends('layouts.admin-master')
@section('title') Add Work Record @endsection
@section('content')

<style>
    body {
        font-family: "Open Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", Helvetica, Arial, sans-serif;
    }
   
  .overlay {
    display: none;
    position: fixed;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    z-index: 999;
    background: rgba(255, 255, 255, 0.8) url('{{ asset("Loading.gif")}}') center no-repeat;
  }

  /* Turn off scrollbar when body element has the loading class */
  body.loading {
    overflow: hidden;
  }

  /* Make spinner image visible when body element has the loading class */
  body.loading .overlay {
    display: block;
  }
</style>

<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Multiple Project Working Record Insertion</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active">Multiple Project Working Section</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        @if(Session::has('success'))
        <div class="alert alert-success alertsuccess" role="alert">
            <strong>{{ Session::get('success') }}</strong>
        </div>
        @endif
        @if(Session::has('error'))
        <div class="alert alert-warning alerterror" role="alert">
            <strong>{{ Session::get('error') }}</strong>
        </div>
        @endif
    </div>
    <div class="col-md-2"></div>
</div>

<!-- employee information searching with (id, passport, iqama) Start -->
<div class="row d-block">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body card_form" style="padding: 0;">
                <div
                    class="form-group row custom_form_group{{ $errors->has('searchBy') ? ' has-error' : '' }}">
                    <label class="col-md-2 control-label d-block" style="text-align: right; margin-right:5px;">Employee Searching By
                    </label>
                    <div class="col-md-4">
                        <select class="form-select" name="searchBy" id="searchBy" required>
                            <option value="employee_id">Employee ID</option>
                            <option value="akama_no">Iqama </option>
                            <option value="passfort_no">Passport</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <input type="text" placeholder="Enter ID/Iqama/Passport No" class="form-control"
                            id="empl_info" name="empl_info" required autofocus>
                        <span id="employee_not_found_error_show" class="d-none"
                            style="color: red"></span>
                        @if ($errors->has('empl_info'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('empl_info') }}</strong>
                        </span>
                        @endif
                    </div>
                    <div class="col-md-2">
                        <button type="submit" onclick="searchingEmployee()" class="btn btn-primary waves-effect">SEARCH</button>
                    </div>
                </div>
            </div>            
        </div>
    </div>
</div>

<div class="row d-none" id="emp_work_record_insert_ui">
    <div class="col-md-1"></div>
    <div class="col-md-10">
        <form class="form-horizontal project-details-form" id="multipleprojectworkform" action="" method="post" >
            @csrf
            <div class="card">

                <div class="card-body card_form">
                    <div class="form-group row custom_form_group">
                        <table  class="table table-bordered table-striped table-hover custom_view_table show_employee_details_table">
                            <tr>
                                <td>
                                    <span class="emp">Emp. ID:</span> <span id="show_employee_id"
                                        class="emp2" style="font-weight:bold;color:red;font-size:18px;"></span> </td>
                                        <td> <span class="emp">Name:</span> <span id="show_employee_name"
                                            class="emp2"></span> </td> 
                                
                            </tr>
                            <tr>
                               
                                <td> <span class="emp">Sponsor: </span> <span
                                            id="show_employee_working_sponsor_name" class="emp2"></span> </td>    
                                <td> <span class="emp">Status:</span> <span
                                                id="show_employee_job_status" class="emp2"  style="font-weight:bold;color:red;font-size:18px;"></span>
                                                <span class="emp">, Trade:</span> <span
                                                id="show_employee_category" class="emp2"></span>
                                    </td>            
                                 
                            </tr>
                
                            <tr>
                                <td> <span class="emp">Passport:</span> <span id="show_employee_passport_no"
                                    class="emp2"></span>
                                    <span class="emp">&nbsp;&nbsp; </span> <span id="show_employee_passport_file" class="emp2"></span>
                                </td>
                                <td> <span class="emp">Iqama:</span> <span id="show_employee_akama_no"
                                            class="emp2"></span>
                                     <span class="emp">&nbsp;&nbsp; </span> <span id="show_employee_iqama_file" class="emp2"></span>
                                </td>                 
                            </tr>  
                        </table>
                    </div>

                    <input type="number"  class="form-control typeahead" placeholder="Input Employee ID"
                    name="emp_id" id="emp_id"  value="{{ old('emp_id') }}" required readonly hidden>
                    <div class="form-group row custom_form_group ">
                        <label class="control-label col-md-3">Project Name:</label>
                        <div class="col-md-7">
                            <select class="form-select" name="proj_name" required>
                                <option value="">Select Project</option>
                                @foreach($project as $proj)
                                <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                                @endforeach
                            </select>                           
                        </div>
                    </div>
  
                    {{-- Show Input Field --}}
                    <div class="form-group row custom_form_group">                       
                        <label class="control-label col-md-3">Month</label>
                        <div class="col-md-3">
                            <select class="form-select" name="month" required>                             
                                <option value="">Select Month</option>   
                                <option value="1">January</option>
                                <option value="2">February</option>
                                <option value="3">March</option>
                                <option value="4">April</option>
                                <option value="5">May</option>
                                <option value="6">June</option>
                                <option value="7">July</option>
                                <option value="8">Auguest</option>
                                <option value="9">September</option>
                                <option value="10">October</option>
                                <option value="11">November</option>
                                <option value="12">December</option>                                
                            </select>
                         </div>
                        <label class="col-sm-1 control-label"> Year </label>
                        <div class="col-sm-3">
                            <select class="form-select" name="year">
                                @foreach(range(date('Y'), date('Y')-1) as $y)
                                <option value="{{$y}}" {{$y}}>{{$y}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row custom_form_group{{ $errors->has('total_work_day') ? ' has-error' : '' }}">
                        <label class="control-label col-md-3">Total Days:<span
                                class="req_star">*</span></label>
                        <div class="col-md-7">
                            <input type="number" class="form-control" step="1" placeholder="Total Working Days"
                                id="total_work_day" name="total_work_day"
                                value="{{old('total_work_day')}}"  required max="31">                           
                                @if ($errors->has('total_work_day'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('total_work_day') }}</strong>
                                </span>
                                @endif
                        </div>
                    </div>
 

                    <div class="form-group row custom_form_group">
                        <label class="col-sm-3 control-label">Total Basic Hours:</label>
                        <div class="col-sm-7">
                            <input type="number" class="form-control"  id="total_basic_hours"  name="total_basic_hours" placeholder="Total Basic Hours" min="0" max="350" step="0.5" required>
                         </div>
                    </div>
                    <div class="form-group row custom_form_group">
                        <label class="col-sm-3 control-label">Total Overtime:</label>
                        <div class="col-sm-7">
                            <input type="number" class="form-control " name="total_overtime" id="total_overtime"  value="0"  step="0.5" required>
                        </div>
                    </div>
                    <div class="form-group row custom_form_group">
                        <label class="col-sm-3 control-label">Work From:</label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control datepicker" id="datepicker" name="startDate"
                                value="{{ Carbon\Carbon::now()->format('m/d/Y') }}" required>
                         </div>
                         <label class="col-sm-1 control-label">To:</label>
                         <div class="col-sm-3">
                             <input type="text" class="form-control datepicker" id="workDatepicker" name="endDate"
                                 value="{{ Carbon\Carbon::now()->format('m/d/Y') }}" required>
                          </div>
                    </div>
                    

                </div>
                <div class="card-footer card_footer_button text-center">
                    <button type="submit" class="btn btn-primary waves-effect"
                        onclick="saveAnEmployeeMultipleProjectWorkingInformation()">SAVE</button>
                </div>
            </div>
        </form>
    </div>
    <div class="col-md-1"></div>
</div>


<div class="row">
    <div class="col-lg-12">
        <div class="card">
             
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table id="alltableinfo" class="table table-bordered custom_table mb-0">
                                <thead>
                                    <tr>
                                        <th>Emp. ID</th>
                                        <th>Name</th>                                        
                                        <th>Emp. Type</th>
                                        <th>Sponsor Name</th>
                                        <th>Project</th>
                                        <th>Hours</th>
                                        <th>Overtime</th>
                                        <th>Days</th>
                                        <th>Month</th>
                                        <th>Manage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($records as $mulProject)
                                    <tr>
                                        <td>{{ $mulProject->employee->employee_id}}</td>
                                        <td>{{ $mulProject->employee->employee_name}}</td>                                         
                                        <td>{{ $mulProject->employee->employeeType->name }}</td>
                                        <td>{{ $mulProject->employee->sponser->spons_name }}</td>
                                        <td>{{ $mulProject->projectName->proj_name}}</td> 
                                        <td>{{ $mulProject->total_hour == NULL ? '--' : $mulProject->total_hour }}</td>
                                        <td>{{ $mulProject->total_overtime == NULL ? '--' : $mulProject->total_overtime
                                            }}</td>
                                        <td>{{ $mulProject->total_day }}</td>
                                        <td>{{ $mulProject->month }}</td>
                                        <td>
                                            <!-- <a href="{{ route('edit.employee.multiple.project.in-out',$mulProject->empwh_auto_id) }}" title="edit"><i class="fa fa-pencil-square fa-lg edit_icon"></i></a> -->
                                            <a href="#"
                                                onClick="deleteMultiProjectBtnClick('{{ $mulProject->empwh_auto_id}}')"
                                                vallue="{{$mulProject->empwh_auto_id}}" title="Delete"><i  class="fa fa-trash fa-lg delete_icon"></i></a>
                                        </td>
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




<!-- script area -->
<script type="text/javascript">
    /* form validation */
    $(document).ready(function () {
        $("#multipleprojectworkform").validate({          
            submitHandler: function (form) {
                return false;
            },
            /* form tag off  */
            rules: {
                emp_id: {
                    required: true,
                },
                proj_name: {
                    required: true,
                }, 
                month: {
                    required: true,
                },               
                totalHourTime: {
                    required: true,
                    number: true,
                },
                total_overtime: {
                    number: true,
                },
                total_work_day:{
                    required:true,
                    number:true,
                },
                 startDate: {
                    required: true,
                },
                endDate: {
                    required: true,
                }

            },
            messages: {
                emp_id: {
                    required: "You Must Be Input This Field!",
                },
                proj_name: {
                    required: "Please Select Working Project",
                },
                month:{
                    required:"Please Select Working Month"
                }   ,             
                totalHourTime: {
                    required: "Please Input This Field!",
                    number: "You Must Be Input Number!",
                },
                total_overtime: {
                    required: "Please Input This Field!",
                    number: "You Must Be Input Number!",
                },
                total_work_day:{
                    required: "Please Input This Field!",
                    number: "You Must Be Input Number!",
                },
                startDate: {
                    required: "You Must Be Select This Field!",
                },
                endDate: {
                    required: "You Must Be Select This Field!",
                }
            },
        });
    }); 

    // Show Alert Message
    function showAlertMessage(msgType,message){
 
         const Toast = Swal.mixin({
              toast: true,
              position: 'top-end',
              showConfirmButton: false,
              timer: 3000
            })
            
              Toast.fire({
                type: msgType,
                title: message
              })
            
    }

    // Data Store Request
    function saveAnEmployeeMultipleProjectWorkingInformation() {
    
      var emp_id = $('input[name="emp_id"]').val();
      var proj_name = $('select[name="proj_name"]').val();
      var month = $('select[name="month"]').val();
      var year = $('select[name="year"]').val();
      var startDate = $('input[name="startDate"]').val();
      var endDate = $('input[name="endDate"]').val();
      var totalHourTime = $('input[name="total_basic_hours"]').val();
      var totalOverTime = $('input[name="total_overtime"]').val();
      var total_days =   $('input[name="total_work_day"]').val();
       
      //  alert(200);
        if (emp_id == "" && proj_name == "" && startDate == "" && endDate == "" && totalHourTime == "") {
            showAlertMessage('error',"Please Inputer All Require Data");
            return;
        }
        $.ajax({
          type: 'POST',
          dataType: 'json',
          data: {
            emp_id: emp_id,
            proj_name: proj_name,
            month:month,
            year:year,
            startDate: startDate,
            endDate: endDate,
            total_days:total_days,
            totalHourTime: totalHourTime,
            totalOverTime: totalOverTime
          },
          url: "{{ route('employee-multiple-time-insert') }}",
          beforeSend:()=>{
              $("body").addClass("loading");
            },
            complete:()=>{
              $("body").removeClass("loading");
            },
          success: function(data) {

            // error_massage
            if (data.status != 200) {
              $("span[id='error_massage']").text("Employee Not Found!");
              $("span[id='error_massage']").removeClass('d-none').addClass('d-block');
            } else {
              
              var emp_id = $('input[name="emp_id"]').val("");
              $("span[id='error_massage']").addClass('d-none').removeClass('d-block');
              showAlertMessage('success',data.message); 
              $('#multipleprojectworkform')[0].reset();               
              const emp_sear = document.getElementById('empl_info');
              emp_sear.focus();
            }
                       
          },
          error:function(response){
            showAlertMessage('error','Operation Failed!, Pleage Try Again ');
          }
        });
      

    }


    function deleteMultiProjectBtnClick(id) {
        swal({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover this imaginary file!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })

            .then((willDelete) => {
                if (willDelete) {

                    $.ajax({
                        type: 'GET',
                         url: "{{  url('admin/delete/employee/multi-project/work/record') }}/" + id,
                        dataType: 'json',
                        success: function (response) {
                            if(response.status == 200){
                                showAlertMessage('success', response.message);
                                
                            }else {
                                showAlertMessage('error', response.message);
                            }
                           // window.location.reload();
                            
                        },
                        error:function(response){
                            showAlertMessage('error', "Operation Failed, Please Try Again");
                        }
                    });


                }
            });

    }

    function resetWorkingRecordInsertForm(){

        $("#emp_work_record_insert_ui").removeClass("d-block").addClass("d-none");  
        $('input[id="emp_id"]').val('');                       
        $("span[id='show_employee_id']").text('');                       
        $("span[id='show_employee_name']").text('');
        $("span[id='show_employee_passport_no']").text('');          
        $("span[id='show_employee_akama_no']").text('');
        $("span[id='show_employee_category']").text('');
        $("span[id='show_employee_working_sponsor_name']").text('');
        $("span[id='show_employee_job_status']").text('');

    }


    $('#empl_info').keydown(function (e) {
        if (e.keyCode == 13) {           
            searchingEmployee();
        }
    })

   //   Single Employee Details Info
   function searchingEmployee() {

        //   resetEmpInfo() // reset All Employe Info
        $("#emp_work_record_insert_ui").removeClass("d-block").addClass("d-none");
        var searchType = $('#searchBy').find(":selected").val();
        var searchValue = $("#empl_info").val();

            if ($("#empl_info").val().length === 0) {
                showAlertMessage('error',"Please Enter Employee ID/Iqama/Passport Number");           
            }  
            $.ajax({
                type: 'POST',
                url: "{{ route('active.employee.searching.searching-with-multitype.parameter') }}",
                data: {
                    search_by: searchType,
                    employee_searching_value: searchValue
                },
                dataType: 'json',
                success: function (response) {
                                       
                    if (response.success == false) {
                        $('input[id="emp_auto_id"]').val(null);
                        $("span[id='employee_not_found_error_show']").text('Please Enter An Active Employee Id');
                        $("span[id='employee_not_found_error_show']").addClass('d-block').removeClass('d-none');
                        $("#employee_searching_result_section").removeClass("d-block").addClass("d-none"); 
                        $("#emp_work_record_insert_ui").removeClass("d-block").addClass("d-none");  
                        $("#update_form_section").removeClass("d-block").addClass("d-none");
                        return ;
                    }               
                    if (response.total_emp > 1) {
                        showSearchingResultAsMultipleRecords(response.findEmployee);
                        alert('Mone Than One Employee Found,Please Inform this issue to Software Engineer');
                    } else {

                        $("#emp_work_record_insert_ui").removeClass("d-none").addClass("d-block");
                        findEmployee = response.findEmployee[0];
                        $('input[id="emp_id"]').val(findEmployee.employee_id);                       
                        $("span[id='show_employee_id']").text(findEmployee.employee_id);                       
                        $("span[id='show_employee_name']").text(findEmployee.employee_name);
                        $("span[id='show_employee_passport_no']").text(findEmployee.passfort_no);          
                        $("span[id='show_employee_akama_no']").text(findEmployee.akama_no);
                        $("span[id='show_employee_category']").text(findEmployee.catg_name);
                        $("span[id='show_employee_working_sponsor_name']").text(findEmployee.spons_name);
                        var job_status = (findEmployee.job_status > 0 ? findEmployee.title : "Waiting for Approval") + (findEmployee.salary_status == 1 ? ', Salary: Active' : ", Salary: Hold");
                        $("span[id='show_employee_job_status']").text(job_status);
                                             
                     }
                }, // end of success
                error:function(response){                 
                    showAlertMessage('error',"Operation Failed, Please try Again");  
                }
            }); // end of ajax calling
    }
  
</script>



<!-- employee out time -->



@endsection
