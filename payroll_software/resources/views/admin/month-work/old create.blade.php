@extends('layouts.admin-master')
@section('title')UploadWorkRecord @endsection
@section('content')

<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Upload Employee Attendance Records</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active">Work Record</li>
        </ol>
    </div>
</div>
<!-- Flass Session Message -->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        @if(Session::has('success'))
        <div class="alert alert-success alertsuccess" role="alert">
            <strong>Successfully!</strong>Employee Work Records Uploaded Successfully
        </div>
        @endif
        @if(Session::has('success_update'))
        <div class="alert alert-success alertsuccess" role="alert">
            <strong>Successfully!</strong> Update Work History.
        </div>
        @endif
        @if(Session::has('success_delete'))
        <div class="alert alert-success alertsuccess" role="alert">
            <strong>Successfully!</strong> {{ Session::get('success_delete')}}
        </div>
        @endif
        @if(Session::has('duplicate'))
        <div class="alert alert-warning alerterror" role="alert">
            <strong>Opps!</strong> This recode already exist.
        </div>
        @endif
        @if(Session::has('indirect_man'))
        <div class="alert alert-warning alerterror" role="alert">
            <strong>Opps!</strong> Invalid Indirect Manpower.
        </div>
        @endif
        @if(Session::has('error'))
        <div class="alert alert-warning alerterror" role="alert">
            <strong>Opps!</strong> please try again.
        </div>
        @endif
        @if(Session::has('duplicate_data_error'))
        <div class="alert alert-warning alerterror" role="alert">
            <strong>Opps!</strong> Duplicate Data Error.
        </div>
        @endif
        @if(Session::has('error_null_0'))
        <div class="alert alert-warning alerterror" role="alert">
            <strong>Opps!</strong> please valid value input.
        </div>
        @endif

        @if(Session::has('error_date'))
        <div class="alert alert-warning alerterror" role="alert">
            <strong>Opps!</strong> Access Denied.
        </div>
        @endif
    </div>
</div>


<!-- Work Record Insert Menu Section Start -->
<div class="row" id="">
    <div class="col-md-1"></div>
    <div class="col-md-10">
        <form class="form-horizontal">
            <div class="card">
                <div class="card-body card_form">
                    <div class="row">
                        <div class="col-sm-3"></div>
                        <div class="col-sm-2">
                            {{-- <button type="button" onclick="openSingleEmployeeWorkSection()"
                                class="btn btn-primary waves-effect">Single Employee Work</button>
                                this is closed it only insert monthly work record but also need to insert multiple project records
                                that why this is close --}}
                            <br> <br>
                        </div>
                        <div class="col-sm-2">
                            <button type="button" onclick="openMultipleEmployeeWorkSection()"
                                class="btn btn-primary waves-effect">Upload Excel File</button>
                            <br> <br>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="col-md-1"></div>
</div>
<!-- Work Record Insert Menu Section End -->


<!-- Single employee work records insert start-->
<div class="row d-none" id="singleEmployeeWorkRecordInsertUI">
    <div class="col-md-12">
        <div class="select_employee">
            <div class="card">
                <div class="card-body card_form row">
                    <div class="col-md-2"></div>
                    <div class="col-md-8">
                        <div class="form-group row custom_form_group">
                            <label class="col-md-3 col-sm-3 control-label">Employee ID:</label>
                            <div class="col-md-5 col-sm-5">
                                <input type="text" class="form-control typeahead" placeholder="Input Employee ID"
                                    name="emp_id" id="emp_id_search" required autofocus>
                                <div id="showEmpId"></div>
                                <span id="error_show" class="d-none" style="color: red"></span>
                            </div>
                            <div class="col-md-4 col-sm-4">
                                <button type="submit" onclick="searchEmployeeDetails()" style="margin-top: 2px"
                                    class="btn btn-primary waves-effect">SEARCH</button>
                            </div>
                        </div>
                        <br><br>
                    </div>
                    <div class="col-md-2"></div>
                    <!-- Show Employee Details with form ui start -->
                    <div class="col-md-12">
                        <div id="showEmployeeDetails" class="d-none">

                            <!-- Search employee details information show Start -->
                            <div class="row">
                                <div class="col-md-1"></div>
                                <!-- employee Deatils -->
                                <div class="col-md-5">
                                    <table
                                        class="table table-bordered table-striped table-hover custom_view_table show_employee_details_table"
                                        id="showEmployeeDetailsTable">
                                        <tr>
                                            <td> <span class="emp">Project:</span> <span id="show_employee_project_name"
                                                    class="emp2"></span> </td>
                                        </tr>
                                        <tr>
                                            <td> <span class="emp"> Name:</span> <span id="show_employee_name"
                                                    class="emp2"></span> </td>
                                        </tr>
                                        <tr>
                                            <td> <span class="emp">Iqama No:</span> <span id="show_employee_akama_no"
                                                    class="emp2"></span> </td>
                                        </tr>
                                        <tr>
                                            <td> <span class="emp">Sponsor:</span> <span id="show_employee_sponsor_name"
                                                    class="emp2"></span> </td>
                                        </tr>
                                        <tr>
                                            <td> <span class="emp">Type:</span> <span id="show_employee_type"
                                                    class="emp2"></span> </td>
                                        </tr>
                                    </table>
                                </div>
                                <!-- Salary Deatils -->
                                <div class="col-md-5">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <table
                                                class="table table-bordered table-striped table-hover custom_view_table show_employee_details_table"
                                                id="showEmployeeDetailsTable">

                                                <tr>
                                                    <td> <span class="emp">Trade:</span> <span
                                                            id="show_employee_category" class="emp2"></span> </td>
                                                </tr>
                                                <tr>
                                                    <td> <span class="emp">Basic Amount:</span> <span
                                                            id="show_employee_basic" class="emp2"></span> </td>
                                                </tr>
                                                <tr>
                                                    <td> <span class="emp">Hourly Rate:</span> <span
                                                            id="show_employee_hourly_rent" class="emp2"></span> </td>
                                                </tr>
                                                <tr>
                                                    <td> <span class="emp">Food Allowance:</span> <span
                                                            id="show_employee_food_allowance" class="emp2"></span> </td>
                                                </tr>

                                                <tr>
                                                    <td> <span class="emp">Saudi Tax:</span> <span
                                                            id="show_employee_saudi_tax" class="emp2"></span> </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-1"></div>
                            </div>
                            <!-- Search employee details information show End -->

                            <!-- employee work record insert form start -->
                            <div class="row">
                                <div class="col-md-2"></div>
                                <div class="col-md-8">
                                    <form style="margin-top:20px" class="form-horizontal" id="registration"
                                        action="{{ route('store.monthly-work-history') }}"  onsubmit="submit_button.disabled = true;"  method="post">
                                        @csrf
                                        <div class="card">
                                            <div class="card-body card_form" style="padding-top: 20;">
                                                <input type="hidden" id="emp_auto_id" name="emp_id" value="">

                                                <div class="form-group row custom_form_group">
                                                    <label class="col-sm-3 control-label"> Project Name:<span
                                                            class="req_star">*</span></label>
                                                    <div class="col-sm-7">
                                                        <select class="form-control" id="project_id" name="project_id">
                                                            @foreach($projects as $proj)
                                                            <option value="{{ $proj->proj_id }}"> {{ $proj->proj_name }}
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="form-group row custom_form_group">
                                                    <label class="col-sm-3 control-label"> Year:<span
                                                            class="req_star">*</span></label>
                                                    <div class="col-sm-4">
                                                        <select class="form-control" name="year">
                                                            @foreach(range(date('Y'), date('Y')-1) as $y)
                                                            <option value="{{$y}}" {{$y}}>{{$y}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                {{-- Show Input Field --}}
                                                <div class="form-group row custom_form_group">
                                                    <label class="control-label col-md-3">Month:</label>
                                                    <div class="col-md-7">
                                                        <select class="form-control" name="month">
                                                            @foreach($month as $data)
                                                            <option value="{{ $data->month_id }}" {{ $data->month_id ==
                                                                $currentMonth ? 'selected':'' }}>{{ $data->month_name }}
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div id="work_hours_field" class="">
                                                    <div  class="form-group row custom_form_group{{ $errors->has('work_hours') ? ' has-error' : '' }}">
                                                        <label class="control-label col-md-3">Total Hours:<span
                                                                class="req_star">*</span></label>
                                                        <div class="col-md-7">
                                                            <input type="text" class="form-control"
                                                                placeholder="Work Hours" id="work_hours_field_custom"
                                                                name="work_hours" value="" required
                                                                max="450" min="1">
                                                            @if ($errors->has('work_hours'))
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $errors->first('work_hours') }}</strong>
                                                            </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>

                                                <div
                                                    class="form-group row custom_form_group{{ $errors->has('overtime') ? ' has-error' : '' }}">
                                                    <label class="control-label col-md-3">Overtime Hours:<span
                                                            class="req_star">*</span></label>
                                                    <div class="col-md-7">
                                                        <input type="text" class="form-control"
                                                            placeholder="Overtime Hours" id="overtime" name="overtime"
                                                            value="0" min="0" required max="250">
                                                        @if ($errors->has('overtime'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('overtime') }}</strong>
                                                        </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="form-group row custom_form_group{{ $errors->has('total_work_day') ? ' has-error' : '' }}">
                                                    <label class="control-label col-md-3">Total Days:<span
                                                            class="req_star">*</span></label>
                                                    <div class="col-md-7">
                                                        <input type="text" class="form-control" placeholder="Work Days"
                                                            id="total_work_day" name="total_work_day"
                                                            value="{{old('total_work_day')}}" required max="31">
                                                        @if ($errors->has('total_work_day'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('total_work_day') }}</strong>
                                                        </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                {{-- Show Input Field --}}
                                            </div>
                                            <div class="card-footer card_footer_button text-center">
                                                <button type="submit" id="submit_button"
                                                    class="btn btn-primary waves-effect">SUBMIT</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-md-2">


                                </div>
                            </div>
                            <!-- employee work record insert form end -->

                        </div>
                    </div>
                    <!-- Show Employee Details with form ui end -->

                </div>
            </div>
        </div>
        <!-- Direct Man Power -->
    </div>
</div>



<!-- All employee work history list start-->
<div class="row d-none" id="emp_work_history_table_section">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-8">
                        <h3 class="card-title card_top_title"><i class="fab fa-gg-circle"></i> Month Work History List
                        </h3>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table id="alltableinfo" class="table table-bordered custom_table mb-0">
                                <thead>
                                    <tr>
                                        <th>Emp ID</th>
                                        <th>Name</th>
                                        <th> Project</th>
                                        <th>Emp Type</th>
                                        <th>Hours</th>
                                        <th>Overtime</th>
                                        <th>Days</th>
                                        <th>Month,Year</th>
                                        <th>Manage</th>
                                    </tr>
                                </thead>
                                <tbody id="workrecords">
                                    @foreach($all as $item)
                                    <tr>
                                        <td>{{ $item->employee->employee_id }}</td>
                                        <td>{{ $item->employee->employee_name }}</td>
                                        <td>{{ $item->employee->project->proj_name }}</td>
                                        <td>{{ $item->employee->employeeType->name }}</td>
                                        <td>{{ $item->total_hours == NULL ? '--' : $item->total_hours }}</td>
                                        <td>{{ $item->overtime == NULL ? '--' : $item->overtime }}</td>
                                        <td>{{ $item->total_work_day }}</td>
                                        <td>{{ $item->month->month_name }},{{$item->year_id}}</td>
                                        <td>
                                            <a href="{{ route('edit.month-work',$item->month_work_id ) }}"
                                                title="edit"><i class="fa fa-pencil-square fa-lg edit_icon"></i></a>
                                            <a href="#"
                                                onClick="deleteEmpMothlyWorkRecord('{{ $item->month_work_id }}')"
                                                title="edit"><i id="" class="fa fa-trash fa-lg delete_icon"></i></a>
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
<!-- All employee work history list end-->

<!--Emp Work Hisotry Excell File Upload Start-->
<div class="row d-none" id="emp_work_records_excel_file_import_section">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                  <form method="POST" enctype="multipart/form-data" id="upload_emp_work_excell_records"  onsubmit="excel_upload_button.disabled = true;" >

                        <div class="row">
                              <div class="form-group row custom_form_group">
                                  <label class="col-sm-1 control-label">Project:</label>
                                  <div class="col-sm-4">
                                      <select class="form-control"  id="proj_name" name="proj_name" required >
                                            <option value="">Select Project</option>
                                          @foreach($projects as $proj)
                                            <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                                          @endforeach
                                      </select>
                                  </div>
                                  <label class="col-sm-1 control-label"> Month</label>
                                  <div class="col-sm-2">
                                      <select class="form-control" name="month" required>
                                      <option value="">Select Month</option>
                                        @foreach($month as $item)
                                          <option value="{{ $item->month_id }}">{{ $item->month_name }}</option>
                                        @endforeach
                                      </select>
                                  </div>
                                  <label class="col-sm-1 control-label">Year</label>
                                  <div class="col-sm-2">
                                      <select class="form-control" name="year" required>
                                        @foreach(range(date('Y'), date('Y')-1) as $y)
                                          <option value="{{$y}}" {{$y}}>{{$y}}</option>
                                        @endforeach
                                      </select>
                                  </div>
                              </div>

                            <div class="form-group row custom_form_group">
                            <div class="col-sm-3">  </div>
                            <div class="col-sm-3">
                                <input type="file" name="file" placeholder="Choose File" id="file">
                                          <span class="text-danger">{{ $errors->first('file') }}</span>
                                </div>
                              <div class="col-sm-2">
                                    <button type="submit" id ="excel_upload_button" class="btn btn-primary">UPLOAD</button>
                              </div>
                            <div class="col-sm-2">  </div>
                          </div>
                        </div>
                  </form>
                </div>
            </div>
            <div class="card-body" id="excell_file_upload_emp_list_table_section" >
                <div class="row">
                    <form method="POST"  id="submit_imported_work_record"  onsubmit="excel_submit_button.disabled = false;" >
                        <div class="row">
                              <div class="form-group row custom_form_group">

                                  <div class="col-sm-9">

                                  </div>
                                  <div class="col-sm-3">
                                      <button type="submit" id ="excel_submit_button"  class="btn btn-primary waves-effect">SUBMIT </button>
                                  </div>
                              </div>
                        </div>
                        <div class="col-12" id="valid_records_div_section" >

                          <div class="table-responsive">
                              <table id="excell_file_upload_emp_list_table" class="table table-bordered custom_table mb-0">
                                  <thead>
                                      <tr>
                                          <th>S.N</th>
                                          <th>ID</th>
                                          <th>Name</th>
                                          <th>S.Type</th>
                                          <th>Project</th>
                                          <th>Month,Year</th>
                                          <th>Hours</th>
                                          <th>Overtime</th>
                                          <th>Total Days</th>
                                          <th>Remarks</th>
                                          <th>Status</th>
                                      </tr>
                                  </thead>
                                  <tbody id="excell_file_upload_emp_list_table_body">
                                  </tbody>
                              </table>
                          </div>
                        </div>
                    </form>
                    <div class="col-12 d-none" id ="upload_error_div_section">
                        <br/>
                        <h3>Data Error Found</h3>
                          <div class="table-responsive">
                              <table id="excell_file_upload_error_emp_list_table" class="table table-bordered custom_table mb-0">
                                  <thead>
                                      <tr>
                                          <th>S.N</th>
                                          <th>ID</th>
                                          <th>Name</th>
                                          <th>S.Type</th>
                                          <th>Month,Year</th>
                                          <th>Hours</th>
                                          <th>Overtime</th>
                                          <th>Total Days</th>
                                          <th>Remarks</th>
                                          <th>Status</th>
                                      </tr>
                                  </thead>
                                  <tbody id="excell_file_upload_error_emp_list_table_body">

                                  </tbody>
                              </table>
                          </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Excell File Upload End -->



<script type="text/javascript">
    // Excel file upload
    $(document).ready(function (e) {

            //   $.ajaxSetup({
            //     headers: {
            //       'X-CSRF-TOKEN': document.getElementsByName("_token")[0].value // $('meta[name="csrf-token"]').attr('content')
            //       }
            //   });

               // excell data uploaded button event
              $('#upload_emp_work_excell_records').submit(function(e) {

                 var project_name = document.getElementById("proj_name").options[document.getElementById("proj_name").selectedIndex].text;
                  e.preventDefault();
                    var formData = new FormData(this);

                  $.ajax({
                        type:'POST',
                        url: "{{ route('import.monthly.work.history.excell')}}",
                        data: formData,
                        cache:false,
                        contentType: false,
                        processData: false,
                        success: (response) => {
                            if(response.status != 200){
                                alert(response.error);
                                document.getElementById("excel_upload_button").disabled = false;
                                return;
                            }
                            this.reset();
                            var rows = "";
                            var counter = 1;
                            document.getElementById("excel_upload_button").disabled = false;
                            $.each(response.records, function (key, value) {

                                rows += `
                                    <tr>
                                        <td>${counter++}</td>
                                        <td>${value.emp_id}</td>
                                        <td>${value.employee_name} </td>
                                        <td>${value.hourly_employee == 1 ? 'Hourly' : 'Basic Salary'} </td>
                                        <td>${project_name}</td>
                                        <td>${value.month_id}, ${value.year_id}</td>
                                        <td>${value.total_hours}</td>
                                        <td> ${value.overtime}</td>
                                        <td> ${value.total_work_day}+${value.paid_leave}</td>
                                         <td> ${value.remarks}</td>
                                        <td> ${value.upload_status}</td>
                                    </tr>
                                    `
                            });
                            $('#excell_file_upload_emp_list_table_body').html(rows);


                        var error_rows = "";
                        var error_counter = 1;
                        $.each(response.records_not_found, function (key, value) {

                            error_rows += `
                                <tr>
                                    <td>${error_counter++}</td>
                                    <td>${value.emp_id}</td>
                                    <td>${value.employee_name} </td>
                                    <td>${value.hourly_employee == 1 ? 'Hourly' : 'Basic Salary'} </td>
                                    <td>${value.month_id}, ${value.year_id}</td>
                                    <td>${value.total_hours}</td>
                                    <td> ${value.overtime}</td>
                                    <td> ${value.total_work_day}+${value.paid_leave}</td>
                                    <td> ${value.remarks}</td>
                                    <td> ${value.upload_status}</td>
                                </tr>
                                `
                        });

                         $("#upload_error_div_section").removeClass("d-none").addClass("d-block");
                         $('#excell_file_upload_error_emp_list_table_body').html(error_rows);
                        },
                        error: function(data){

 
                            document.getElementById("excel_upload_button").disabled = false;
                           
                            const Toast = Swal.mixin({
                                  toast: true,
                                  position: 'top-end',
                                  showConfirmButton: false,
                                  timer: 3000
                              })
                            Toast.fire({
                                      type: 'error',
                                      title: data.status
                                  })
                        }
                    });
              });

              // Submit Imported Excell Data
              $('#submit_imported_work_record').submit(function(e) {
                  e.preventDefault();
                    var formData = new FormData(this);
                    $.ajax({
                        type:'POST',
                        url: "{{ route('submit.monthly.work.history.imported.excell')}}",
                        data: formData,
                        cache:false,
                        contentType: false,
                        processData: false,
                        success: (response) => {
                            this.reset();
                            const Toast = Swal.mixin({
                                  toast: true,
                                  position: 'top-end',
                                  showConfirmButton: false,
                                  timer: 3000
                              })

                            if(response.status == 200){
                                Toast.fire({
                                      type: 'success',
                                      title: response.message
                                  })
                             } else {
                                    Toast.fire({
                                      type: 'error',
                                      title: response.error
                                  })
                              }
                            location.reload();

                        },
                        error: function(data){
                          
                          //  alert("Please Check Excel Header Name & Data Format");
                           const Toast = Swal.mixin({
                                  toast: true,
                                  position: 'top-end',
                                  showConfirmButton: false,
                                  timer: 3000
                              })
                              
                            Toast.fire({
                                      type: 'error',
                                      title: 'Please Check Excel Header Name & Data Format'
                                  })
                            
                        }
                    });

              });

       });


    function openSingleEmployeeWorkSection() {
        $('#singleEmployeeWorkRecordInsertUI').removeClass('d-none').addClass('d-block');
        $('#emp_work_history_table_section').removeClass('d-none').addClass('d-block');
        $('#emp_work_records_excel_file_import_section').removeClass('d-block').addClass('d-none');
        document.getElementById("emp_id_search").focus();
    }

    function openMultipleEmployeeWorkSection() {
        $('#emp_work_records_excel_file_import_section').removeClass('d-none').addClass('d-block');
        $('#singleEmployeeWorkRecordInsertUI').removeClass('d-block').addClass('d-none');
        $('#emp_work_history_table_section').removeClass('d-block').addClass('d-none');
    }


    function deleteEmpMothlyWorkRecord(id) {
        swal({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover this file!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
        .then((willDelete) => {
            if (willDelete) {

                $.ajax({
                    type: 'GET',
                    url: "{{  url('admin/delete/work/history') }}/" + id,
                    dataType: 'json',
                    success: function (response) {
                        window.location.reload();
                    }
                });
            }
        });
    }


     // Enter Key Press Event Fire
     $('#emp_id_search').keydown(function(e) {
        if (e.keyCode == 13) {

           searchEmployeeDetails();
        }
    })

    /* ================= search Employee Details ================= */
    function searchEmployeeDetails() {

      var emp_id = $("#emp_id_search").val();
      var iqamaNo = $("input[id='iqamaNoSearch']").val();
      $.ajax({
        type: 'POST',
        url: "{{ route('search.employee-details') }}",
        data: {
          emp_id: emp_id,
          iqamaNo: iqamaNo
        },
        dataType: 'json',
        success: function(response) {


          if (response.status == "error") {
            $('input[id="emp_auto_id"]').val(null);
            $("span[id='show_employee_id']").text("ID is Required");
            $("input[id='emp_id_search']").val('');
            $("span[id='error_show']").text('This Id Dosn,t Match!');
            $("span[id='error_show']").addClass('d-block').removeClass('d-none');
            $("#showEmployeeDetails").addClass("d-none").removeClass("d-block");
          } else {
            $("input[id='emp_id_search']").val('');
            $("span[id='error_show']").removeClass('d-block').addClass('d-none');
            $("#showEmployeeDetails").removeClass("d-none").addClass("d-block");
          }
          $("span[id='show_employee_id']").text(response.findEmployee.employee_id);
          $("span[id='show_employee_name']").text(response.findEmployee.employee_name);
          $("span[id='show_employee_akama_no']").text(response.findEmployee.akama_no);
          $("span[id='show_employee_akama_expire_date']").text(response.findEmployee.akama_expire_date);


          $("span[id='show_employee_passport_no']").text(response.findEmployee.passfort_no);
          $("span[id='show_employee_passport_expire_date']").text(response.findEmployee.passfort_expire_date);

          $("span[id='show_employee_job_status']").text(response.findEmployee.status.title);
          /* conditionaly show project name */
          if (response.findEmployee.project_id == null) {
            $("span[id='show_employee_project_name']").text("No Assigned Project!");
          } else {
            $("span[id='show_employee_project_name']").text(response.findEmployee.project.proj_name);
          }


          /* conditionaly show sponsor name */
          if (response.findEmployee.sponsor_id == null) {
            $("span[id='show_employee_sponsor_name']").text("No Assigned Sponsor!");
          } else {
            $("span[id='show_employee_sponsor_name']").text(response.findEmployee.sponsor.spons_name);
          }


          /* conditionaly show project name */
          /* conditionaly show Department name */
          if (response.findEmployee.department_id == null) {
            $("span[id='show_employee_department']").text("No Assigned Department");
          } else {
            $("span[id='show_employee_department']").text(response.findEmployee.department.dep_name);
          }
          /* conditionaly show project name */
          /* show Relationaly data */
          if (response.findEmployee.emp_type_id == 1) {
            $("span[id='show_employee_type']").text("Direct Manpower");
          } else {
            $("span[id='show_employee_type']").text("Indirect Manpower");
          }
          $("span[id='show_employee_category']").text(response.findEmployee.category.catg_name);
          $("span[id='show_employee_address_C']").text(response.findEmployee.country.country_name);
          $("span[id='show_employee_address_D']").text(response.findEmployee.division.division_name);
          $("span[id='show_employee_address_Ds']").text(response.findEmployee.district.district_name);

          $("span[id='show_employee_confirmation_date']").text(response.findEmployee.confirmation_date);
          $("span[id='show_employee_appointment_date']").text(response.findEmployee.appointment_date);
          $("span[id='show_employee_date_of_birth']").text(response.findEmployee.date_of_birth);
          $("span[id='show_employee_mobile_no']").text(response.findEmployee.mobile_no);
          $("span[id='show_employee_email']").text(response.findEmployee.email);
          $("span[id='show_employee_joining_date']").text(response.findEmployee.joining_date);
          if (response.findEmployee.maritus_status == 1) {
            $("span[id='show_employee_metarials']").text('Unmarid');
          } else {
            $("span[id='show_employee_metarials']").text('Marid');
          }
          /* show Relationaly data */
          /* show employee Salary */
          $("span[id='show_employee_basic']").text(response.salary.basic_amount);
          $("span[id='show_employee_house_rent']").text(response.salary.house_rent);
          $("span[id='show_employee_hourly_rent']").text(response.salary.hourly_rent);
          $("span[id='show_employee_mobile_allow']").text(response.salary.mobile_allowance);
          $("span[id='show_employee_food_allow']").text(response.salary.food_allowance);
          $("span[id='show_employee_medical_allow']").text(response.salary.medical_allowance);
          $("span[id='show_employee_local_travel_allow']").text(response.salary.local_travel_allowance);
          $("span[id='show_employee_conveyance_allow']").text(response.salary.conveyance_allowance);
          $("span[id='show_employee_increment_no']").text(response.salary.increment_no);
          $("span[id='show_employee_increment_amount']").text(response.salary.increment_amount);
          $("span[id='show_employee_food_allowance']").text(response.salary.food_allowance);
          $("span[id='show_employee_saudi_tax']").text(response.salary.saudi_tax);

          $("span[id='show_employee_address_details']").text(response.findEmployee.details);
          $("span[id='show_employee_present_address']").text(response.findEmployee.present_address);
          /* show salary details in input form */
          $('input[id="emp_status"]').val(response.findEmployee.job_status);
          $('input[id="emp_auto_id"]').val(response.findEmployee.emp_auto_id);
          $('input[id="input_basic_amount"]').val(response.salary.basic_amount);
          $('input[id="input_hourly_rate"]').val(response.salary.hourly_rent);
          $('input[id="input_house_rate"]').val(response.salary.house_rent);
          $('input[id="input_mobile_allowance"]').val(response.salary.mobile_allowance);
          $('input[id="input_medical_allowance"]').val(response.salary.medical_allowance);
          $('input[id="input_local_travel_allowance"]').val(response.salary.local_travel_allowance);
          $('input[id="input_conveyance_allowance"]').val(response.salary.conveyance_allowance);
          $('input[id="input_others1"]').val(response.salary.others1);
          /*hidden field*/
          $('input[id="input_emp_id"]').val(response.findEmployee.employee_id);
          $('input[id="input_emp_id_in_desig"]').val(response.findEmployee.emp_auto_id);
          $('input[id="hidden_input_emp_name_in_user"]').val(response.findEmployee.employee_name);
          $('input[id="hidden_input_emp_mobile_in_user"]').val(response.findEmployee.mobile_no);
          $('input[id="hidden_input_emp_email_in_user"]').val(response.findEmployee.email);
          // user module
          $('input[id="show_input_emp_name_in_user"]').val(response.findEmployee.employee_name);
          $('input[id="show_input_emp_category_in_user"]').val(response.findEmployee.category.catg_name);
          $('input[id="show_input_emp_mobile_in_user"]').val(response.findEmployee.mobile_no);
          // user module

          $('input[id="input_emp_desig_id_in_desig"]').val(response.findEmployee.designation_id);




        }

      });
    }




</script>
@endsection
