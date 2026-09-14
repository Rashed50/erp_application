@extends('layouts.admin-master')
@section('title') UnPaid Salary @endsection
@section('content')

<style>
    /* Employee Salary Information Table */

    #employeeinfo {
        font-family: Arial, Helvetica, sans-serif;
        border-collapse: collapse;
        width: 100%;

    }

    #employeeinfo td,
    #employeeinfo th {
        border: 1px solid #ddd;
        padding: 8px;
    }

    #employeeinfo tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    #employeeinfo tr:hover {
        background-color: #ddd;
    }

    #employeeinfo th {
        padding-top: 5px;
        padding-bottom: 5px;
        text-align: left;
        background-color: #EAEDED;
        color: black;
    }
</style>
<!-- Session Flash Message -->
<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-7">
        @if(Session::has('success'))
        <div class="alert alert-success alertsuccess" role="alert">
            <strong> {{Session::get('success')}}</strong>
        </div>
        @endif

        @if(Session::has('error'))
        <div class="alert alert-warning alerterror" role="alert">
        <strong> {{Session::get('error')}}</strong>
        </div>
        @endif
    </div>
    <div class="col-md-2"></div>
</div>

<!-- Top Bar   -->
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Salary Unpaid Employees</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active">Salary Unpaid</li>
        </ol>
    </div>
</div>


<!-- Searching UI Form -->
<div class="row">
    <div class="col-lg-12">
        <div class="card">

            <div class="card-body">

                <form method="post" action="#">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                             <div class="form-group row custom_form_group">
                                <label class="col-sm-3 control-label">Project:</label>
                                <div class="col-sm-9">
                                    <select class="selectpicker" name="proj_id[]" id="proj_id" multiple multiple data-container="body" data-live-search="true">
                                         @foreach($projectlist as $proj)
                                            <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4"><div class="form-group row custom_form_group">
                            <label class="col-sm-4 control-label">Sponsor:</label>
                            <div class="col-sm-8">
                                 <select class="selectpicker" name="SponsId[]" id="SponsId" multiple>
                                     <!--<option value="">Select Sponsor</option>-->
                                    @foreach($sponserList as $spons)
                                    <option value="{{ $spons->spons_id }}">{{ $spons->spons_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            </div></div>
                        <div class="col-md-3">
                            <div class="form-group row custom_form_group">
                            <label class="col-sm-4 control-label">Emp. Type:</label>
                            <div class="col-sm-6">
                                <select class="form-select" name="emp_type" id="emp_type" >
                                    <option value="-1">Select One </option>
                                    <option value="0">Direct Basic Employee</option>
                                    <option value="1">Hourly Employee</option>
                                    <option value="2">Indirect Employee</option>

                                </select>
                            </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group row custom_form_group">

                                <label class="col-md-1 control-label">Month:<span class="req_star">*</span></label>
                                <div class="col-md-3">
                                    <input type="text" class="form-control fromDate" id="datepickerFrom"   name="fromDate" value="{{date('m/d/Y')}}" required>
                                </div>
                                <label class="col-md-1 control-label"></label>
                                 <div class="col-md-5">
                                    <input type="text" class="form-control" placeholder="Search By Employee ID" autofocus name="employee_id" id="employee_id">
                                </div>
                                <div class="col-md-2">
                                    <button type="button" onclick="searchEmpPendingSalaryList()"  id="search_button" style="margin-top: 2px" class="btn btn-primary waves-effect">SEARCH</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Salary Pending Emp Searching Result Table -->
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <form id="employee-salary-payment-form" action="{{ route('payment.salary') }}"  onsubmit="this.querySelector('button[type=submit]').disabled = true;" method="post">
                @csrf
                <div class="card-header">
                    <div class="row">
                         <div class="col-md-3">
                         </div>
                        <div class="col-md-3">
                            <button type="button" id="select_button" disabled onclick="checkUnCheckAllEmployeeForUpdateSalaryPaid()" class="btn btn-primary waves-effect" >Check/UnCheck</button>
                        </div>
                        <div class="col-md-2">
                            <label class="control-label" id="lbl_total_selected_counter"> </label>
                        </div>
                         <div class="col-md-2">
                            <label class="control-label" id="lbl_total_selected_salary"> </label>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary waves-effect" id="update_button" disabled >Update as unpaid </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!--<div class="table-responsive">-->
                    <!--    <div style="max-height: 500px !important;overflow-y: auto;">-->
                    <!--            <table id="employeeinfo" class="table table-bordered table-hover custom_table mb-0">-->
                    <!--                <thead>-->
                    <!--                    <tr>-->
                    <!--                        <th>S.N</th>-->
                    <!--                        <th>Emp.ID</th>-->
                    <!--                        <th>Name</th>-->
                    <!--                        <th>Iqama</th>-->
                    <!--                        <th>Salary</th>-->
                    <!--                        <th>Sponsor</th>-->
                    <!--                        <th>Trade</th>-->
                    <!--                        <th>Project</th>-->
                    <!--                        <th>Month,Year</th>-->
                    <!--                        <th>Salary</th>-->
                    <!--                        <th class="text-center" colspan="2">Manage</th>-->
                    <!--                    </tr>-->
                    <!--                </thead>-->
                    <!--                <tbody id="employeePendingList"></tbody>-->
                    <!--            </table>-->
                    <!--    </div>-->
                    <!--</div>-->
                    <div class="table-responsive">
                        <table id="employeeinfo">
                            <thead>
                                <tr>
                                    <th>S.N</th>
                                    <th>Emp.ID</th>
                                    <th>Name</th>
                                    <th>Iqama</th>
                                    <th>Salary</th>
                                    <th>Sponsor</th>
                                    <th>Trade</th>
                                    <th>Project</th>
                                    <th>Month,Year</th>
                                    <th>Salary</th>
                                    <th class="text-center" colspan="2">Manage</th>
                                </tr>
                            </thead>
                            <tbody id="employeePendingList"></tbody>
                        </table>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>



<!--  Employee Salary Update Modal -->
<div class="modal fade" id="emp_salary_edit_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Udpate An Employee Salary <span class="text-danger" id="errorData"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

                <div class="modal-body">

                        <input type="hidden" id="modal_emp_auto_id" name="modal_emp_auto_id" value="" required>
                        <input type="hidden" id="modal_slh_auto_id" name="modal_slh_auto_id" value="" required>

                        <div class="form-group row custom_form_group">
                            <label class="col-sm-4 control-label">ID, Name & Iqama:</label>
                            <div class="col-sm-8">
                                <span id ="employee_details" style="color:red"> </span>
                            </div>
                        </div>

                        <div class="form-group row custom_form_group">
                            <label class="col-sm-3 control-label">Month</label>
                            <div class="col-sm-3">
                                <input type="text" id="modal_slh_month" class="form-control " name="modal_slh_month"   readonly>
                            </div>

                            <label class="col-sm-3 control-label">Year</label>
                            <div class="col-sm-3">
                                <input type="text" id="modal_slh_year" class="form-control " name="modal_slh_year"   readonly>
                            </div>
                        </div>

                        <div class="form-group row custom_form_group">
                            <label class="col-sm-3 control-label">Working Days</label>
                            <div class="col-sm-3">
                                <input type="text" id="working_days" class="form-control " name="working_days"   readonly>
                            </div>

                            <label class="col-sm-3 control-label">Total Hours</label>
                            <div class="col-sm-3">
                                <input type="text" id="total_hours" class="form-control " name="total_hours"   readonly>
                            </div>
                        </div>

                        <div class="form-group row custom_form_group">
                            <label class="col-sm-3 control-label">Total(Excl. Food)</label>
                            <div class="col-sm-3">
                                <input type="number" id="total_amount" class="form-control" name="total_amount"   readonly>
                            </div>
                            <label class="col-sm-3 control-label">Food</label>
                            <div class="col-sm-3">
                                <input type="number" id="food_amount" class="form-control " name="food_amount" readonly>
                            </div>
                        </div>

                        <div class="form-group row custom_form_group">
                            <label class="col-sm-3 control-label">Mobile </label>
                            <div class="col-sm-3">
                                <input type="number" id="mobile_allowance" class="form-control" name="mobile_allowance"   readonly>
                            </div>
                            <label class="col-sm-3 control-label">Medical</label>
                            <div class="col-sm-3">
                                <input type="number" id="medical_allowance" class="form-control " name="medical_allowance" readonly>
                            </div>
                        </div>



                        <div class="form-group row custom_form_group">
                            <label class="col-sm-3 control-label">Total(All Inc.)</label>
                            <div class="col-sm-8">
                            <input type="number" id="grand_total_salary" class="form-control " name="grand_total_salary" readonly>
                            </div>
                        </div>



                         <div class="form-group row custom_form_group">

                            <label class="col-sm-3 control-label">Saudi TAX</label>
                            <div class="col-sm-3">
                                <input type="number" id="saudi_tax" class="form-control " name="saudi_tax" value="0" min="0" max="300" required>
                            </div>
                            <label class="col-sm-3 control-label">Iqama Deduc.</label>
                            <div class="col-sm-3">
                            <input type="number" id="new_iqama_advance" class="form-control " name="new_iqama_advance" value="0" min="0" required>
                            </div>
                        </div>

                        <div class="form-group row custom_form_group">
                            <label class="col-sm-3 control-label">Catering</label>
                            <div class="col-sm-3">
                                <input type="number" id="catering_service_amount" class="form-control " name="catering_service_amount" value="0" min="0" autofocus>
                            </div>

                            <label class="col-sm-3 control-label">Food </label>
                            <div class="col-sm-3">
                                <input type="number" id="new_food_amount" class="form-control " name="new_food_amount" value="0" min="0" max="2000"  required>
                                <span id ="new_grand_total_salary" style="font-weight:bold; color:red" > </span>
                            </div>

                        </div>

                         <div class="form-group row custom_form_group">
                            <label class="col-sm-3 control-label">Other Deduc.</label>
                            <div class="col-sm-3">
                                <input type="number" id="new_other_advance" class="form-control " name="new_other_advance" value="0" min="0"  required>
                            </div>

                            <label class="col-sm-3 control-label">Partial Paid</label>
                            <div class="col-sm-3">
                                 <input type="number" id="partial_paid" class="form-control " name="partial_paid" value="0" min="0" disabled>
                            </div>
                        </div>

                        <div class="form-group row custom_form_group">

                                <label class="col-sm-3 control-label"><b style="color:red">Receivable</b></label>
                                <div class="col-sm-4">
                                    <input type="number" id="new_receivable_total_salary" class="form-control " style="font-weight:bold; color:red" name="new_receivable_total_salary"  min="0" readonly required>
                                </div>
                                <div class="col-sm-3">
                                    @can('monthly_salary_unpaid_to_paid_permission')
                                        <input name="salary_paid__status" id="salary_paid__status" type="checkbox" >&nbsp;Salary Paid
                                        <select name="payment_method" id="payment_method" class="form-select" required>
                                            <option value="1">Cash</option>
                                            <option value="2">Bank</option>
                                        </select>
                                    @endcan

                                </div>

                        </div>

                        <br>
                    <!-- <div class="modal-footer"> -->
                    <button type="submit" id="updatebtn" name="updatebtn" onclick="sumbitSalaryCorrectionData()"  class="btn btn-success"  >Salary Update</button>

                </div>

            <!-- </form> -->
        </div>
    </div>
</div>


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta2/css/bootstrap-select.min.css" integrity="sha512-mR/b5Y7FRsKqrYZou7uysnOdCIJib/7r5QeJMFvLNHNhtye3xJp1TdJVPLtetkukFn227nKpXD9OjUc09lx97Q==" crossorigin="anonymous" referrerpolicy="no-referrer" />
{{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script> --}}
{{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4" crossorigin="anonymous"></script> --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta2/js/bootstrap-select.min.js" integrity="sha512-FHZVRMUW9FsXobt+ONiix6Z0tIkxvQfxtCSirkKc5Sb4TKHmqq1dZa8DphF0XqKb3ldLu/wgMa8mT6uXiLlRlw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>


<script>

    // Datepicker Selection Event
    $('document').ready(function() {
        $('#datepickerFrom').datepicker({
            autoclose: true,
            toggleActive: true,
            // startView: "months",  // Ata 1st month select korle date asbe
            minViewMode: "months",
            // format: "mm/yyyy",
        });

        // $('#datepickerTo').datepicker({
        //     autoclose: true,
        //     toggleActive: true,
        //     minViewMode: "months",
        //     // format: "mm/yyyy",
        // });
    });

    $('#employee_id').keydown(function (e) {
        if (e.keyCode == 13) {
            searchEmpPendingSalaryList();
        }

    })

    function showSweetAlert(message,opeation_status){
        const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000
                            })
                            Toast.fire({
                                type: opeation_status,// 'error',
                                title: message
                            })

    }

    function deletePendingSalary(salary_auto_id){

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
                    url: "{{  url('admin/employee/salary/delete') }}/" + salary_auto_id,
                    dataType: 'json',
                    success: function (response) {
                        if(response.status == 200){
                            showSweetAlert("Successfully Deleted",'success');
                            searchEmpPendingSalaryList();
                        }else {
                            showSweetAlert("Operation Failed",'error');
                        }

                    },
                    error:function(response){
                        showSweetAlert("Operation Failed",'error');
                    }
                });
            }
        });
        //  window.location.reload();
    }

    function searchEmpPendingSalaryList() {

        var fromDate = $('.fromDate').val();
        var emp_type = $('#emp_type').val();
        var proj_id = $('#proj_id').val();
        var SponsId = $('#SponsId').val();
        var employee_id = $('#employee_id').val();
        if (fromDate == '') {
            showSweetAlert("Please Select Month and Year",'error');
            return;
        }

        document.getElementById("lbl_total_selected_counter").innerText = "";
        document.getElementById("lbl_total_selected_salary").innerText = "";
        document.getElementById("search_button").disabled = true;
        $('#employeePendingList').html('');

            $.ajax({
                type: "POST",
                url: "{{ route('salary-pending.list') }}",
                data: {
                    fromDate: fromDate,
                    emp_type: emp_type,
                    SponsId: SponsId,
                    proj_id: proj_id,
                    employee_id: employee_id
                },
                dataType: "json",
                success: function(response) {

                    var rows = "";
                    var counter = 0;
                    var total_salary =0;
                    document.getElementById("search_button").disabled = false;
                    if(response.status != 200){
                        showSweetAlert(response.message,'error');
                        return;
                    }
                     document.getElementById("update_button").disabled = false;
                     document.getElementById("select_button").disabled = false;

                    $.each(response.pendingSalary, function(key, value) {
                            total_salary +=parseFloat(value.slh_total_salary);

                            counter++;
                            rows += `
                                    <tr>
                                        <td>${counter}</td>
                                        <td>${value.employee.employee_id}</td>
                                        <td>${value.employee.employee_name}</td>
                                        <td>${value.employee.akama_no}</td>
                                        <td>${value.employee.hourly_employee == 1 ? 'Hourly':'Basic'}</td>
                                        <td>${value.employee.sponsor.spons_name}</td>
                                        <td>${value.employee.category.catg_name}</td>
                                        <td>${value.proj_name}</td>
                                        <td>${value.month.month_name},${value.slh_year}</td>
                                        <td>${Math.round(value.slh_total_salary)}</td>
                                        <td style="background-color: #fff; color:#fff; padding: 0px; " >${value.slh_auto_id}</td>
                                        <td  style="width:100px; align-items:center;">

                                            <input type="hidden" id="slh_auto_id${value.slh_auto_id}" name="slh_auto_id[]" value="${value.slh_auto_id}">
                                            <input type="checkbox" onclick="countSelectedCheckBox(${value.slh_auto_id})"  name="emp_slh_paid_checkbox-${value.slh_auto_id}" id="emp_slh_paid_checkbox-${value.slh_auto_id}" value="0">
                                            @can('employee_salary_record_edit')
                                            ||
                                            <a href="" id="salary_edit_button" data-toggle="modal" data-target="#emp_salary_edit_modal" data-id="${value.slh_auto_id}"><i id="" class="fa fa-edit edit_icon"></i></a>

                                            @endcan
                                             ||
                                            @can('employee_salary_record_delete')
                                                <a href="#" onClick="deletePendingSalary(${value.slh_auto_id})" title="Delete"><i id="" class="fa fa-trash delete_icon"></i></a>
                                            @endcan
                                        </td>
                                    </tr>
                                `
                    });

                    $('#employeePendingList').html(rows);

                    document.getElementById("lbl_total_selected_salary").innerText ="Total Salary: "+ Math.round(total_salary);
                    document.getElementById("lbl_total_selected_counter").innerText = "Total Employees: "+ counter;
                },
                error:function(response){
                    showSweetAlert('Operation Failed ','error');
                }
            });

          //  $('#employee_id').val("");
          document.getElementById("employee_id").focus();
          $('#employee_id').select();
    }

        // Open Modal For Update Employee Salary Information
    $(document).on("click", "#salary_edit_button", function(){

        var slh_auto_id = $(this).data('id');

        $.ajax({
            type: "post",
            url: "{{ route('get.amemployee.unpaid.salary.record.byslh.autoid') }}",
            data: {slh_auto_id: slh_auto_id},
            datatype:"json",
            success: function(response){

                if(response.status == 200){

                    var arecord = response.arecord;
                    $('#modal_emp_auto_id').val(arecord.emp_auto_id);
                    $('#modal_slh_auto_id').val(arecord.slh_auto_id);
                    $('#employee_details').text(arecord.employee_id+", "+arecord.employee_name+", "+arecord.akama_no+",Basic/Hourly: "+arecord.basic_amount+"/"+arecord.hourly_rent);
                    $('#employee_iqama').val(arecord.akama_no);
                    $('#working_days').val(arecord.slh_total_working_days);
                    $('#total_hours').val(arecord.slh_total_hours);
                    $('#modal_slh_month').val(arecord.slh_month);
                    $('#modal_slh_year').val(arecord.slh_year);

                   var slh_all_include_amount = parseFloat(arecord.slh_all_include_amount);

                    if(slh_all_include_amount <= 0){
                        slh_all_include_amount = parseFloat( arecord.slh_total_salary) + parseFloat( arecord.slh_iqama_advance) + parseFloat(arecord.slh_other_advance) + parseFloat(arecord.slh_saudi_tax) + parseFloat(arecord.slh_food_deduction);
                    }
                    var total_working_amount = slh_all_include_amount - (parseFloat( arecord.food_allowance) + parseFloat( arecord.mobile_allowance) + parseFloat( arecord.medical_allowance) );

                    $('#catering_service_amount').val(arecord.slh_food_deduction);
                    $('#total_amount').val(total_working_amount);
                    $('#food_amount').val(arecord.food_allowance);
                    $('#mobile_allowance').val(arecord.mobile_allowance);
                    $('#medical_allowance').val(arecord.medical_allowance);
                    $('#grand_total_salary').val(Math.round(slh_all_include_amount));

                    $('#new_food_amount').val(arecord.food_allowance);
                    $('#saudi_tax').val(arecord.slh_saudi_tax);
                    $('#new_other_advance').val(arecord.slh_other_advance);
                    $('#new_iqama_advance').val(arecord.slh_iqama_advance);
                    $('#partial_paid').val(arecord.partial_paid_amount);

                    $('#new_receivable_total_salary').val(Math.round(arecord.slh_total_salary));
                    // set default checked
                   // document.getElementById('salary_paid__status').checked = true;

                }else{
                    $('#errorData').text(response.error);
                }
            },
            error:function(response){
                showSweetAlert('Operation Failed ','error');
            }
        })

    });

    // all number field on focus value selection code
    $("input[type='number']").on("click", function () {
        $(this).select();
    });

    $("#new_food_amount").on("input", function() {
        calculateTotalReceivableSalary();
    });
    $("#saudi_tax").on("input", function() {
        calculateTotalReceivableSalary();
    });
    $("#new_other_advance").on("input", function() {
        calculateTotalReceivableSalary();
    });
    $("#new_iqama_advance").on("input", function() {
        calculateTotalReceivableSalary();
    });

    $("#catering_service_amount").on("input", function() {
        calculateTotalReceivableSalary();
    });



    function calculateTotalReceivableSalary(){

            if ($('#new_food_amount').val() == "" || $('#new_food_amount').val() == null) {
                $('#new_food_amount').val('0');

            }
            if ($('#saudi_tax').val() == "" || $('#saudi_tax').val() == null) {
                $('#saudi_tax').val('0');

            }
            if ($('#new_other_advance').val() == "" || $('#new_other_advance').val() == null) {
                $('#new_other_advance').val('0');

            }
            if ($('#new_iqama_advance').val() == "" || $('#new_iqama_advance').val() == null) {
                $('#new_iqama_advance').val('0');

            }
            if ($('#catering_service_amount').val() == "" || $('#catering_service_amount').val() == null) {
                $('#catering_service_amount').val('0');
            }

             if ($('#partial_paid').val() == "" || $('#partial_paid').val() == null) {
                $('#partial_paid').val('0');
            }


       var grand_total_salary = parseFloat($('#total_amount').val());
       var new_food_amount = parseFloat($('#new_food_amount').val());



       var new_grand_total_salary = grand_total_salary   + new_food_amount; // from grand_total amount previous food amnt is deducted already
       $('#new_grand_total_salary').text("New Total: "+ new_grand_total_salary);

        var saudi_tax = parseFloat($('#saudi_tax').val());
        var catering_amount = parseFloat($('#catering_service_amount').val());
         var partial_paid = parseFloat($('#partial_paid').val());
        var new_other_advance = parseFloat($('#new_other_advance').val());
        var new_iqama_advance = parseFloat($('#new_iqama_advance').val());
        var total = (grand_total_salary + new_food_amount) - (new_other_advance + saudi_tax + new_iqama_advance + catering_amount+partial_paid);

        $('#new_receivable_total_salary').val(Math.round(total));

       // alert(food_amount);
    }

    function sumbitSalaryCorrectionData(){
        // UI reset
        $("#emp_salary_edit_modal").modal('hide');
        $('#employee_details').text('');
        $('#new_grand_total_salary').text("");

        var total_amount = $('#total_amount').val();
        var new_food_amount = $('#new_food_amount').val();
        var saudi_tax = $('#saudi_tax').val();
        var new_other_advance = $('#new_other_advance').val();
        var new_iqama_advance = $('#new_iqama_advance').val();
        var catering_amount = $('#catering_service_amount').val();


        var new_receivable_total_salary = $('#new_receivable_total_salary').val();

        var emp_auto_id = $('#modal_emp_auto_id').val();
        var slh_auto_id = $('#modal_slh_auto_id').val();
        var salary_month = $('#modal_slh_month').val();
        var salary_year = $('#modal_slh_year').val();
        var working_days = $('#working_days').val();
        var salary_paid__status = (document.getElementById('salary_paid__status').checked) == true ? 1:0;

        $.ajax({
            type:"POST",
            url:"{{route('employee.salary.update.request')}}",
            data:{
                working_days:working_days,
                total_amount:total_amount,
                new_food_amount:new_food_amount,
                saudi_tax:saudi_tax,
                new_other_advance:new_other_advance,
                new_iqama_advance:new_iqama_advance,
                new_receivable_total_salary:new_receivable_total_salary,
                salary_month:salary_month,
                salary_year:salary_year,
                emp_auto_id:emp_auto_id,
                slh_auto_id:slh_auto_id  ,
                salary_paid__status:salary_paid__status,
                catering_amount:catering_amount,
                paytment_method: $('#payment_method').val() // 1=Cash,2=Bank
            },
            datatype:"json",
            success:function(response){
               if(response.status == 200){
                showSweetAlert('Update Operation Successfully Completed','success');
                searchEmpPendingSalaryList();
               }else {
                showSweetAlert('Update Operation Failed','error');
               }

            },
            error:function(response){
                showSweetAlert('Update Operation Failed','error');
            }

        })


    }

    // Modal View Showing Event
    $('#emp_salary_edit_modal').on('shown.bs.modal', function() {
        $(this).find('[autofocus]').focus();
        // $('#new_food_amount').select();
        $('#catering_service_amount').select(); // auto focus with selected

    });

    //  Modal View Hidden Event, Reset Modal Previous Data
    $('#emp_salary_edit_modal').on('hidden.bs.modal', function (e) {
      $(this)
      .find("input,textarea,select").val('').end()
      .find("input[type=checkbox], input[type=radio]").prop("checked", "").end();

    })

    // Check uncheck
    function checkUnCheckAllEmployeeForUpdateSalaryPaid(){
         let myTable = document.getElementById('employeePendingList');
        var total_selected = 0;
         var  total_salary = 0;
            for (let row of myTable.rows) {
                allCell = row.cells;
                var chkboxId = "emp_slh_paid_checkbox-" + allCell[10].innerText;
                document.getElementById(chkboxId).checked = !(document.getElementById(chkboxId).checked) ;
                if(document.getElementById(chkboxId).checked){

                    total_salary += Math.round(parseFloat(allCell[9].innerText),2);
                    total_selected += 1;
                 }
            }
        document.getElementById("lbl_total_selected_counter").innerText = "Total Selected: "+total_selected;
        document.getElementById("lbl_total_selected_salary").innerText = "Total Salary: "+total_salary;


    }

    // checkbox click event
    function countSelectedCheckBox(value){
         let myTable = document.getElementById('employeePendingList');
        var total_selected = 0;
        var  total_salary = 0;
            for (let row of myTable.rows) {
                allCell = row.cells;
                var chkboxId = "emp_slh_paid_checkbox-" + allCell[10].innerText;
                if(document.getElementById(chkboxId).checked){

                   total_salary += Math.round(parseFloat(allCell[9].innerText),2);
                     total_selected += 1;
                }
            }
        document.getElementById("lbl_total_selected_counter").innerText = "Total Selected: "+total_selected;
        document.getElementById("lbl_total_selected_salary").innerText = "Total Salary: "+total_salary;

    }

</script>
@endsection
