@extends('layouts.admin-master')
@section('title')Upload Work Records @endsection
@section('content')

<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Upload Employee Attendance Records</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active">Attendance Upload</li>
        </ol>
    </div>
</div>
<!-- Flass Session Message -->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        @if(Session::has('success'))
        <div class="alert alert-success alertsuccess" role="alert">
            <strong></strong>Employee Work Records Uploaded Successfully
        </div>
        @endif

        @if(Session::has('error'))
        <div class="alert alert-warning alerterror" role="alert">
            <strong>Opps!</strong> Access Denied.
        </div>
        @endif
    </div>
</div>


<!--Emp Work Hisotry Excell File Upload Start-->
<div class="row" id="emp_work_records_excel_file_import_section">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header mb-0">
                <div class="row">
                  <form method="POST" enctype="multipart/form-data" id="upload_emp_work_excell_records"  onsubmit="excel_upload_button.disabled = true;" >

                        <div class="col-md-12">
                            <div class="form-group row custom_form_group">
                                  <label class="col-md-1 control-label">Project:</label>
                                  <div class="col-md-4">
                                      <select class="form-select"  id="proj_name" name="proj_name" required >
                                            <option value="">Select Project</option>
                                          @foreach($projects as $proj)
                                            <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                                          @endforeach
                                      </select>
                                  </div>
                                  <label class="col-md-2 control-label"> Month</label>
                                  <div class="col-md-2">
                                      <select class="form-select" name="month" required>
                                      <option value="">Select Month</option>
                                        @foreach($month as $item)
                                          <option value="{{ $item->month_id }}">{{ $item->month_name }}</option>
                                        @endforeach
                                      </select>
                                  </div>
                                  <label class="col-md-1 control-label">Year</label>
                                  <div class="col-md-2">
                                      <select class="form-select" name="year" required>
                                        @foreach(range(date('Y'), date('Y')-1) as $y)
                                          <option value="{{$y}}" {{$y}}>{{$y}}</option>
                                        @endforeach
                                      </select>
                                  </div>
                            </div>

                            <div class="form-group row custom_form_group">
                                  <label class="col-md-1 control-label">File:</label>
                                  <div class="col-md-4">
                                    <input type="file" name="file" class="form-control" placeholder="Choose File" id="file">  <span class="text-danger">{{ $errors->first('file') }}</span>

                                  </div>
                                  <label class="col-md-2 control-label"> Operation Type</label>
                                  <div class="col-md-3">
                                     <select name="operation_type" class="form-select" id="operation_type" required>
                                        <option value="1">Preview </option>
                                        <option value="2">Preview & Submit</option>
                                    </select>
                                  </div>

                                  <div class="col-md-2">
                                    <button type="submit" id ="excel_upload_button" class="btn btn-primary">UPLOAD</button>
                                  </div>
                            </div>

                        </div>
                  </form>
                </div>
            </div>
            <div class="card-body mt-0 d-none" id="excell_file_upload_emp_list_table_section" >
                <hr>
                <div class="row mt-0" id="excell_file_preview_and_upload_section" >
                     <div class="col-12">
                        <div class="col-sm-6"></div>
                        <div class="col-sm-6" style="text-align:right; item-align:right;">
                            <form method="POST"  id="submit_imported_work_record"  onsubmit="excel_submit_button.disabled = true;" >
                            @csrf
                                <button type="submit" id ="excel_submit_button" disabled class="btn btn-primary waves-effect">SUBMIT </button>
                            </form>
                        </div>
                    </div>


                    <div class="col-12">
                        <h3>Records Summary</h3>
                        <div class="table-responsive">
                            <table class="table table-bordered custom_table mb-0">
                                <thead>
                                    <tr>
                                        <th>S.N</th>
                                        <th>Trade Name</th>
                                        <th>Hours</th>
                                        <th>Overtime</th>
                                        <th>Employees</th>
                                    </tr>
                                </thead>
                                <tbody id="excell_file_upload_summary_table_body">
                                </tbody>
                            </table>
                        </div>
                    </div>


                    <div class="col-12" id="valid_records_div_section" >
                         <br/>
                        <h3>Valid Data Records</h3>
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

                    <div class="col-12 d-none" id ="upload_error_div_section">
                        <br/>
                        <h3>Error Data Found</h3>
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
                                          <th>Days</th>
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


<script type="text/javascript">
    // Excel file upload
    $(document).ready(function (e) {

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

                            $('#excell_file_upload_emp_list_table_section').removeClass('d-none').addClass('d-block');
                            document.getElementById("excel_upload_button").disabled = false;
                            $operation_type = $('#operation_type').val();

                            if($operation_type == 1){
                                document.getElementById("excel_submit_button").disabled = true;
                            }else if($operation_type == 2){
                                document.getElementById("excel_submit_button").disabled = false; // Enable submit button for preview & submit option

                            }
                            previewExcelDataSummary(response.records);
                            previewExcelDataWithErrorAndUpload(response);
                            this.reset();



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

                            document.getElementById("excel_upload_button").disabled = false;
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


    function previewExcelDataSummary(records){

        const summary = records.reduce((acc, item) => {
            const id = item.catg_id;
            // If this category hasn't been added to the accumulator yet, initialize it
            if (!acc[id]) {
                acc[id] = {
                    catg_id: id,
                    catg_name: item.catg_name,
                    total_hours: 0,
                    total_overtime: 0,
                    employee_count: 0
                };
            }
            // Add the values (use parseFloat or Number to ensure they aren't treated as strings)
            acc[id].total_hours += parseFloat(item.total_hours || 0);
            acc[id].total_overtime += parseFloat(item.overtime || 0);
            acc[id].employee_count += 1;
            return acc;
        }, {});

        // Convert the object back into an array for use in a table or list
        const summary_result = Object.values(summary);
        console.log(summary_result);
        $('#excell_file_upload_summary_table_body').html("");
        var rows = "";
        var counter = 1;
        var total_basic_hours = 0;
        var total_overtime_hours = 0;
        var total_employees = 0;

        $.each(summary_result, function (key, value) {
                total_basic_hours += value.total_hours;
                total_overtime_hours += value.total_overtime;
                total_employees += value.employee_count;

            rows += `
                <tr>
                    <td>${counter++}</td>
                    <td>${value.catg_name}</td>
                    <td>${value.total_hours}</td>
                    <td> ${value.total_overtime}</td>
                    <td> ${value.employee_count}</td>
                </tr>
                `
        });
            rows += `
                <tr style="font-weight:bold;background-color:lightgreen;">
                    <td colspan="2" style="text-align:center;">Valid Total</td>
                    <td>${total_basic_hours}</td>
                    <td> ${total_overtime_hours}</td>
                    <td>${total_employees}</td>
                </tr>
                `
        $('#excell_file_upload_summary_table_body').html(rows);


    }

    function previewExcelDataWithErrorAndUpload(response){



                var rows = "";
                var counter = 1;
                var total_basic_hours_found = 0;
                var total_overtime_hours_found = 0;
                document.getElementById("excel_upload_button").disabled = false;
                var project_name = document.getElementById("proj_name").options[document.getElementById("proj_name").selectedIndex].text;

                $.each(response.records, function (key, value) {
                    var month_name = getMonthName(value.month_id);
                        total_basic_hours_found += value.total_hours;
                        total_overtime_hours_found += value.overtime;

                    rows += `
                        <tr>
                            <td>${counter++}</td>
                            <td>${value.emp_id}</td>
                            <td>${value.employee_name} </td>
                            <td>${value.hourly_employee == 1 ? 'Hourly' : 'Basic Salary'} </td>
                            <td>${project_name}</td>
                            <td>${month_name}, ${value.year_id}</td>
                            <td>${value.total_hours}</td>
                            <td> ${value.overtime}</td>
                            <td> ${value.total_work_day}+${value.paid_leave}</td>
                                <td> ${value.remarks}</td>
                            <td> ${value.upload_status}</td>
                        </tr>
                        `
                });

                rows += `
                        <tr style="font-weight:bold;background-color:lightgray;;">
                            <td colspan="6" style="font-weight:bold;">Total</td>
                            <td>${total_basic_hours_found}</td>
                            <td> ${total_overtime_hours_found}</td>
                            <td>  </td>
                            <td> </td>
                            <td> </td>
                        </tr>
                        `

                $('#excell_file_upload_emp_list_table_body').html(rows);

                var error_rows = "";
                var error_counter = 1;
                var total_basic_hours_notfound = 0;
                var total_overtime_hours_notfound = 0;

                $.each(response.records_not_found, function (key, value) {
                    var month_name = getMonthName(value.month_id);
                    total_basic_hours_notfound += value.total_hours;
                    total_overtime_hours_notfound += value.overtime;
                    error_rows += `
                        <tr>
                            <td>${error_counter++}</td>
                            <td>${value.emp_id}</td>
                            <td>${value.employee_name} </td>
                            <td>${value.hourly_employee == 1 ? 'Hourly' : 'Basic Salary'} </td>
                            <td>${month_name}, ${value.year_id}</td>
                            <td>${value.total_hours}</td>
                            <td> ${value.overtime}</td>
                            <td> ${value.total_work_day}+${value.paid_leave}</td>
                            <td> ${value.remarks}</td>
                            <td> ${value.upload_status}</td>
                        </tr>
                        `
                });

                    error_rows += `
                        <tr style="font-weight:bold;background-color:lightgray;">
                            <td colspan="5" style="text-align:center;">Total</td>
                            <td>${total_basic_hours_notfound}</td>
                            <td> ${total_overtime_hours_notfound}</td>
                            <td>  </td>
                                <td> </td>
                            <td> </td>
                        </tr>
                        `

                    $("#upload_error_div_section").removeClass("d-none").addClass("d-block");
                    $('#excell_file_upload_error_emp_list_table_body').html(error_rows);

                    let tableBody = document.querySelector("#excell_file_upload_summary_table_body");
                    let newrow = `
                    <tr style="font-weight:bold;background-color:#FFCCCB;">
                        <td colspan="2" style="text-align:center;">As per Excel Total</td>
                        <td>${total_basic_hours_found+total_basic_hours_notfound}</td>
                        <td> ${total_overtime_hours_found + total_overtime_hours_notfound}</td>
                        <td> Hours(Basic+OT) : ${total_basic_hours_found+total_basic_hours_notfound+total_overtime_hours_found + total_overtime_hours_notfound}, and Employees: ${error_counter-1 + counter-1}</td>
                    </tr>
                    `
                    // Append the HTML string to the end of the tbody
                    tableBody.insertAdjacentHTML('beforeend', newrow);



    }


    function getMonthName(monthNumber) {
            const date = new Date(2024, monthNumber - 1, 1);
            return date.toLocaleString('default', { month: 'short' }); // long, short, narrow
    }


</script>
@endsection
