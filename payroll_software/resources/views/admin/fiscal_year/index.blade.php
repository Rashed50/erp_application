@extends('layouts.admin-master')
@section('title') Fiscal-Year @endsection
@section('content')

@section('internal-css')
@endsection

<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Salary Closing</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active">Fiscal Year</li>
        </ol>
    </div>
</div>

<!-- Session Message Flash -->
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
            <strong>{{Session::get('error')}} </strong>
        </div>
        @endif
    </div>
    <div class="col-md-2"></div>
</div>

<!-- Employee Searching Section  -->
<div class="row">
    <div class="col-md-12">
        <div class="card"><br>
            <div class="card-body card_form" style="padding-top: 0;">
                <div  class="row form-group custom_form_group{{ $errors->has('searchBy') ? ' has-error' : '' }}">


                                        <label class="col-md-2 control-label d-block" > Search By</label>
                                        <div class="col-md-2">

                                            <select class="form-select" name="searchBy" id="searchBy" required>
                                                <option value="employee_id">  Employee ID</option>
                                                <option value="akama_no">  Iqama </option>
                                            </select>
                                        </div>

                                        <div class="col-md-3">
                                            <input type="text" placeholder="Enter ID/Iqama/Passport No"
                                                class="form-control" id="empl_info" name="empl_info"
                                                value=""  required autofocus>
                                            @if ($errors->has('empl_info'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('empl_info') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                        <div class="col-md-1">
                                            @can('employee_fiscal_year_search')
                                            <button type="submit"  onclick="searchAnEmployeeOpenCloseFiscalYearAllRecords()"    class="btn btn-primary waves-effect">Search </button>

                                            @endcan
                                        </div>
                                        <div class="col-md-1">
                                            @can('employee_fiscal_year_add')
                                            <button type="submit"  onclick="addNewFiscalYear()"    class="btn btn-primary waves-effect">Add New</button>

                                            @endcan
                                        </div>

                                        <div class="col-md-2">
                                            @can('employee_fiscal_year_edit')
                                            <button type="submit"  onclick="searchAnEmployeeWithFiscalYearClosingRecord()"   class="btn btn-primary waves-effect">Salary Closing</button>

                                            @endcan
                                        </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!--  Employee Salary Closing Modal-->
<div class="modal fade" id="employee_salary_closing_modal" tabindex="-1" role="dialog"   aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Employee Salary Fiscal Year Update  <span class="text-danger" id="errorData"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
                <div class="modal-body">
                        <input type="hidden" id="salary_closing_emp_auto_id" name="emp_auto_id" value="" required>
                        <input type="hidden" id="fiscal_year_auto_id" name="salary_closing_auto_id" value="" required>

                        <div class="form-group row custom_form_group">
                            <label class="col-sm-4 control-label">ID, Name & Iqama:</label>
                            <div class="col-sm-8">
                                <span id ="salary_closing_employee_info" style="color:red"> </span>
                            </div>
                        </div>

                        <div class="form-group row custom_form_group">
                            <label class="col-sm-4 control-label">Fiscal Details</label>
                            <div class="col-sm-8">
                                <span id ="salary_closing_fiscal_info" style="color:red"> </span>
                            </div>
                        </div>

                        <div class="form-group row custom_form_group">
                            <label class="col-sm-4 control-label">Operation Type</label>
                            <div class="col-md-8">
                                            <select class="form-select" name="operation_type" id="operation_type" required>
                                                <option value="0">Fiscal Year Update</option>
                                                <option value="1">Update & Fiscal Year Close</option>
                                                <option value="2">New Fiscal Year Open</option>
                                                <option value="3">Fiscal Closing File Upload</option>
                                                <option value="4">Reopening Closed Fiscal Year</option>

                                            </select>
                                    @if ($errors->has('operation_type'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('operation_type') }}</strong>
                                    </span>
                                    @endif
                            </div>
                        </div>

                        <div class="form-group row custom_form_group">
                            <label class="col-sm-4 control-label"> Opening Date</label>
                            <div class="col-md-8">
                                <input type="date" class="form-control" id="fyc_open_date"  name="fyc_open_date" value="<?= date("Y-m-d") ?>" required>
                                @if ($errors->has('fyc_open_date'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('fyc_open_date') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row custom_form_group">
                            <label class="col-sm-4 control-label"> Closing Date</label>
                            <div class="col-md-8">
                                <input type="date" class="form-control" id="fyc_end_date"  name="fyc_end_date" value="<?= date("Y-m-d") ?>" required>
                                @if ($errors->has('fyc_end_date'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('fyc_end_date') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row custom_form_group">
                            <label class="col-sm-4 control-label"> Balance</label>
                            <div class="col-sm-8">
                                <input type="number" id="salary_closing_balance_amount" class="form-control " name="salary_closing_balance_amount"   min="0" step="1"  autofocus required>
                                    @if ($errors->has('salary_closing_balance_amount'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('salary_closing_balance_amount') }}</strong>
                                    </span>
                                    @endif
                            </div>

                        </div>

                        <div class="form-group row custom_form_group">
                            <label class="col-sm-4 control-label">Remarks</label>
                            <div class="col-sm-8">
                            <input type="text" id="salary_closing_remarks" class="form-control" name="remarks" >
                            </div>
                        </div>

                        <div class="form-group row custom_form_group">
                             <label class="col-sm-4 control-label">Closing File</label>
                            <div class="col-sm-8">
                             <input type="file" class="form-control ignore" name="fiscal_closing_file" id="fiscal_closing_file" >
                            </div>
                        </div>

                        <br><br>
                        <button type="submit" id="submitbutton" name="submitbutton" onclick="submitEmployeeSalaryClosingFormData()"  class="btn btn-success">UPDATE</button>
                </div>

            <!-- </form> -->
        </div>
    </div>
</div>


<!-- Employee Fiscal Year Records -->
<div class="row d-block" id= "emp_bonus_records_section">
    <div class="col-12">
        <div class="table-responsive">

            <table id="alltableinfo" class="table table-bordered custom_table mb-0">
                <thead>
                    <tr>
                        <th>S.N</th>
                        <th>ID</th>
                        <th>Emp. Name</th>
                        <th>Iqama</th>
                        <th>Salary</th>
                        <th>Start Date</th>
                        <th>Month,Year</th>
                        <th>Closing Date</th>
                        <th>Month,Year</th>
                        <th>Status</th>
                        <th>Update By</th>
                        <th>Balance</th>
                        <th>File</th>
                        <th>Manage</th>
                    </tr>
                </thead>
                <tbody id="emp_fiscal_records_table"></tbody>
            </table>
        </div>
    </div>
</div>


<script type="text/javascript">

    /* form validation */
    $(document).ready(function() {


        var emp_auto_id = $('#salary_closing_emp_auto_id').val();
        var start_date = $('#fyc_open_date').val();
        var closing_date = $('#fyc_end_date').val();
        var balance_amount = $('#salary_closing_balance_amount').val();
        var remarks = $('#salary_closing_remarks').val();
        var operation_type = $('#operation_type').val();


        $("#employee_salary_closing_modal").validate({
            submitHandler: function(form) {
                return false;
            },
            rules: {
                fyc_open_date: {
                    required: true,
                },

                fyc_end_date: {
                    required: true,
                },
                salary_closing_balance_amount: {
                    required: true,
                },
                operation_type:{required:true,},

            },

            messages: {
                fyc_open_date: {
                    required: "You Must Be Input This Field!",
                },
                fyc_end_date: {
                    required: "You Must Be Input This Field!",
                },
                salary_closing_balance_amount: {
                    required: "You Must Be Input This Field!",
                },
                operation_type:{required:"You Must Be Input This Field!"},
            },
        });

    });

    // Search by Enter Key Event
    $('#empl_info').keydown(function (e) {
        if (e.keyCode == 13) {
          searchAnEmployeeOpenCloseFiscalYearAllRecords();
        }
    })


    function searchAnEmployeeOpenCloseFiscalYearAllRecords() {

            var searchType = $('#searchBy').find(":selected").val();
            var searchValue = $("#empl_info").val();
            if ($("#empl_info").val().length === 0) {
                showMessage('error',"Pleaase Enter Employee ID, Iqama or Passport Number");
                return;
            }
            $.ajax({
                type: 'GET',
                url: "{{ route('employee.open.close.fiscal.year.search') }}",
                data: {
                    search_by: searchType,
                    employee_searching_value: searchValue
                },
                dataType: 'json',
                success: function (response) {

                    if(response.status == 200){

                        if(response.data.length == 0){
                            showMessage('error','Records Not Found');
                            return;
                        }
                        var counter = 0;
                        var rows = '';

                        $.each(response.data, function(key, value) {
                                counter++;

                                rows += `
                                        <tr>
                                            <td>${counter}</td>
                                            <td>${value.employee_id}</td>
                                            <td>${value.employee_name}</td>
                                            <td>${value.akama_no}</td>
                                            <td>${value.hourly_employee == 1 ? 'Hourly' : 'Basic'}</td>
                                            <td>${value.start_date}</td>
                                            <td>${value.start_month}, ${value.start_year}</td>
                                            <td>${value.end_date }</td>
                                            <td>${value.end_month}, ${value.end_year}</td>
                                            <td>${value.closing_status == 1 ? 'Closed' :'Open' }</td>
                                            <td>${value.updated_by_name != null ? value.updated_by_name : '-'}</td>
                                            <td>${value.balance_amount }</td>
                                            <td>
                                                 ${value.closing_file ? `<a target="_blank" href="${getAttachmentUrl(value.closing_file)}"><i class="fas fa-eye view_icon"></i></a>` : ''}
                                            </td>
                                            <td>
                                                @can('employee_fiscal_year_edit')
                                                    <a href="" id="record_edit_button" data-toggle="modal" data-target="#employee_salary_closing_modal" title ="Edit" data-id="${value.efcr_auto_id}">
                                                    <i class="fa fa-edit edit_icon"></i></a>||
                                                @endcan
                                                @can('employee_fiscal_year_file_upload')
                                                    <a href="" id="file_upload_button" data-toggle="modal" data-target="#employee_salary_closing_modal" title ="File Upload" data-id="${value.efcr_auto_id}">
                                                    <i class="fa fa-upload upload_icon"></i></a>||
                                                @endcan

                                                @can('employee_fiscal_year_delete')
                                                    <a href="#" onClick="deleteEmployeeFiscalYear(${value.efcr_auto_id})" title="Delete"><i class="fa fa-trash delete_icon"></i></a>
                                                @endcan

                                            </td>
                                        </tr>
                                    `
                            });
                        $('#emp_fiscal_records_table').html(rows);
                    }
                } // end of success
            }); // end of ajax calling
    }

    // Open Modal For Fiscal Year Closing
    function searchAnEmployeeWithFiscalYearClosingRecord() {

        var searchType = $('#searchBy').find(":selected").val();
        var searchValue = $("#empl_info").val();
        if ($("#empl_info").val().length === 0) {
            showMessage('error',"Pleaase Enter Employee ID/Iqama  Number");
            return;
        }
        $.ajax({
            type: 'POST',
            url: "{{ route('employee.salary.fiscal.year.search') }}",
            data: {
                search_by: searchType,
                employee_searching_value: searchValue
            },
            dataType: 'json',
            success: function (response) {
                if (response.success == false) {
                    $('#emp_auto_id').val(null);
                    $('#fiscal_year_auto_id').val(null);
                    showMessage('error',response.message);
                    return ;
                }
                else if (response.total_emp > 1) {
                    $('#emp_auto_id').val(null);
                    $('#fiscal_year_auto_id').val(null);
                    showMessage('error','Operation Failed');
                    return;
                } else {
                        anemp =  response.employee;
                        var fiscal_record = response.fiscal_record;
                        var status = fiscal_record.closing_status == 0 ? "Running": "Closed";
                        $('#salary_closing_emp_auto_id').val(anemp.emp_auto_id);
                        $('#fiscal_year_auto_id').val(fiscal_record.efcr_auto_id);
                        $('#salary_closing_balance_amount').val(fiscal_record.balance_amount);
                        $('#salary_closing_employee_info').text(anemp.employee_id+", "+anemp.employee_name+", "+anemp.akama_no+",Basic/Hourly: "+anemp.basic_amount+"/"+anemp.hourly_rent);
                        $('#salary_closing_fiscal_info').text("Start : "+fiscal_record.start_date+", End : "+ (fiscal_record.end_date == null ? "Not Found":fiscal_record.end_date) +", Status: "+status+", Closing Balance: "+fiscal_record.balance_amount); //

                        $('#operation_type').val(1);
                        $('#fyc_open_date').val(fiscal_record.start_date);
                        $('#fyc_end_date').val(fiscal_record.end_date);
                        document.getElementById('operation_type').disabled = true;

                        var start_date =new Date(fiscal_record.start_date);
                        var end_date =new Date(fiscal_record.end_date);
                        start_date = start_date.toISOString().slice(0,10);
                        end_date = end_date.toISOString().slice(0,10);
                        document.getElementById('fyc_end_date').setAttribute("min", (end_date));
                        document.getElementById('fyc_open_date').setAttribute("value", (start_date));
                        document.getElementById('fyc_open_date').setAttribute("min", (start_date));

                        $('#employee_salary_closing_modal').modal('show');
                }
            } // end of success
        }); // end of ajax calling

    }

    function submitEmployeeSalaryClosingFormData(){


        // 1. Grab the file from the input field
        var file_data = $('#fiscal_closing_file').prop('files')[0];

        // 2. Use FormData instead of a standard object
        var formData = new FormData();

        // 3. Append all your existing fields
        formData.append('emp_auto_id', $('#salary_closing_emp_auto_id').val());
        formData.append('start_date', $('#fyc_open_date').val());
        formData.append('closing_date', $('#fyc_end_date').val());
        formData.append('balance_amount', $('#salary_closing_balance_amount').val());
        formData.append('remarks', $('#salary_closing_remarks').val());
        formData.append('operation_type', $('#operation_type').val());
        formData.append('fiscal_year_auto_id', $('#fiscal_year_auto_id').val());

        // 4. Append the file (matching the key your controller expects)
        if(file_data) {
            formData.append('fiscal_closing_file', file_data);
        }

        // 5. Validation (Keep your existing logic)
        if(operation_type == 2 && (start_date == '' || closing_date == null)){
            showMessage('error','Please Input Valid Data');
            return;
        }

        $.ajax({
            type: "POST",
            url: "{{ route('employee.salary.fiscal.year.update') }}",
            data: formData, // Send the FormData object
            processData: false, // REQUIRED: Tell jQuery not to convert the data to a string
            contentType: false, // REQUIRED: Tell jQuery not to set a Content-Type header
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Ensure CSRF is sent
            },
            success: function(response){
                if(response.status == 200){
                    showMessage('success', response.message);
                    $('#employee_salary_closing_modal').modal('hide');
                    searchAnEmployeeOpenCloseFiscalYearAllRecords();
                } else {
                    showMessage('error', response.message);
                }
            },
            error: function(response){
                showMessage('error', "Something went wrong during upload.");
            }
        });



    }

    function getAttachmentUrl(path)
    {
       // const s3_bucket_url = "{{ config('app.aws_s3_bucket_url') }}";
        return "{{ config('app.aws_s3_bucket_url') }}" + path;
    }

    function addNewFiscalYear(){

        var searchValue = $("#empl_info").val();
        if ($("#empl_info").val().length === 0) {
            showMessage('error',"Pleaase Enter Employee ID, Iqama or Passport Number");
            return;
        }
        $.ajax({
            type:"GET",
            url:"{{ route('get.last.close.fiscal.year')}}",
            data:{
                employee_id:searchValue,
            },
            dataType:"json",
            success:function(response){
                if(response.status == 200){

                    anemp =  response.employee;
                    var fiscal_record = response.data;


                    $('#salary_closing_fiscal_info').text('');
                    $('#salary_closing_balance_amount').val(0);
                    $('#salary_closing_employee_info').text(anemp.employee_id+", "+anemp.employee_name+", "+anemp.akama_no);
                    $('#salary_closing_emp_auto_id').val(anemp.emp_auto_id);
                    $('#fiscal_year_auto_id').val("-1");

                  //  $('#fyc_open_date').val(fiscal_record.end_date);
                    $('#fyc_end_date').val(new Date());
                    $('#operation_type').val(2);
                    document.getElementById('operation_type').disabled = true;
                    var end_date =new Date(fiscal_record.end_date);
                    end_date.setDate(end_date.getDate()+1);
                    end_date = end_date.toISOString().slice(0,10);//.toLocaleDateString();
                    document.getElementById('fyc_open_date').setAttribute("min", (end_date));
                    $('#fyc_open_date').val(end_date);  // previous closing date +1 day
                    document.getElementById('submitbutton').textContent = 'Add';
                    $('#employee_salary_closing_modal').modal('show');


                }else{
                    showMessage('error',response.message);
                }
            }
        });
    }
    $(document).on("click", "#file_upload_button", function(){

         var efcr_auto_id = $(this).data('id');
        openEditModalForEditFiscalYEarForRecord(efcr_auto_id,2)

    });

    // Open Modal For Update Employee Fiscal Year  Information
    $(document).on("click", "#record_edit_button", function(){

        var efcr_auto_id = $(this).data('id');
        openEditModalForEditFiscalYEarForRecord(efcr_auto_id,1)

    });

    function openEditModalForEditFiscalYEarForRecord(efcr_auto_id,action_type){
            // 1 =edit, 2 = only file upload

        $.ajax({
            type: "GET",
            url: "{{ route('employee.salary.fiscal.year.arecord') }}",
            data: {efcr_auto_id: efcr_auto_id},
            datatype:"json",
            success: function(response){
                    if (response.success == false) {
                        $('#emp_auto_id').val(null);
                        $('#fiscal_year_auto_id').val(null);
                        showMessage('error',response.message);
                        return ;
                    }
                    anemp =  response.employee;
                    var fiscal_record = response.fiscal_record;
                    var status = fiscal_record.closing_status == 0 ? "Running": "Closed";
                    $('#salary_closing_emp_auto_id').val(anemp.emp_auto_id);
                    $('#fiscal_year_auto_id').val(fiscal_record.efcr_auto_id);
                    $('#salary_closing_balance_amount').val( parseFloat(fiscal_record.balance_amount).toFixed(2));
                    $('#salary_closing_employee_info').text(anemp.employee_id+", "+anemp.employee_name+", "+anemp.akama_no+",Basic/Hourly: "+anemp.basic_amount+"/"+anemp.hourly_rent);
                    $('#salary_closing_fiscal_info').text("Start : "+fiscal_record.start_date+", End : "+ (fiscal_record.end_date == null ? "Not Found":fiscal_record.end_date) +", Status: "+status+", Closing Balance: "+fiscal_record.balance_amount); //
                    $('#operation_type').val(fiscal_record.closing_status == 0 ? 2:1 );
                    $('#fyc_open_date').val(fiscal_record.start_date);
                    $('#fyc_end_date').val(fiscal_record.end_date);
                    document.getElementById('operation_type').disabled = false;

                    var start_date =new Date(fiscal_record.start_date);
                    var end_date =new Date(fiscal_record.end_date);
                    start_date = start_date.toISOString().slice(0,10);
                    end_date = end_date.toISOString().slice(0,10);
                   // document.getElementById('fyc_end_date').setAttribute("min", (end_date));
                    document.getElementById('fyc_open_date').setAttribute("value", (start_date));
                    document.getElementById('fyc_open_date').setAttribute("min", (start_date));
                    document.getElementById('submitbutton').textContent = 'Update';

                    if(action_type == 2){
                            // only file upload permission
                        document.getElementById('fyc_open_date').disabled = true;
                        document.getElementById('fyc_end_date').disabled = true;
                        document.getElementById('salary_closing_balance_amount').disabled = true;
                        $('#operation_type').val(3); // closing file file upload
                        document.getElementById('operation_type').disabled = true;
                    }else{
                        document.getElementById('fyc_open_date').disabled = false;
                        document.getElementById('fyc_end_date').disabled = false;
                        document.getElementById('salary_closing_balance_amount').disabled = false;
                        $('#operation_type').val(1); // update and close
                        document.getElementById('operation_type').disabled = false;
                    }

            },
            error:function(response){
              //  showSweetAlert('Operation Failed ','error');
            }
        })

    }


    function deleteEmployeeFiscalYear(efcr_auto_id){
       // //
      // url: "{{  url('admin/employee/salary/fiscal-year/delete') }}/"+efcr_auto_id,
       swal({
           title: "Are you sure?",
           text: "Once deleted, You will not be able to recover this Record!",
           icon: "warning",
           buttons: true,
           dangerMode: true,
       })
       .then((willDelete) => {
           if (willDelete) {
               $.ajax({
                   type: 'delete',
                   url: "{{  route('employee.salary.fiscal.year.delete') }}/",
                   dataType: 'json',
                   data:{
                    efcr_auto_id:efcr_auto_id,
                   },
                   success: function (response) {
                       if(response.status == 200){
                        showMessage('success',response.message);
                          searchAnEmployeeOpenCloseFiscalYearAllRecords();
                       }else {
                        showMessage('error',response.message);
                       }
                   },
                   error:function(response){
                    showMessage('error',"Operation Failed");
                   }
               });
           }
       });
       //  window.location.reload();
    }


    // Show Sweet Alert Message
    function showMessage(type , message){

        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        })

            Toast.fire({
                type: type,
                title: message
            })
    }

    // Reset Modal Previous Data
    $('#employee_salary_closing_modal').on('hidden.bs.modal', function (e) {
      $(this)
      .find("input,textarea,select,hidden").val('').end()
      .find("input[type=checkbox], input[type=radio]").prop("checked", "").end();
    })


</script>

@endsection
