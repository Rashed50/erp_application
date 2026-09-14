@extends('layouts.admin-master')
@section('title')Salary Paid @endsection
@section('content')



<style>
    .loader {
        border: 16px solid #f3f3f3;
        border-radius: 50%;
        border-top: 16px solid blue;
        border-right: 16px solid green;
        border-bottom: 16px solid red;
        border-left: 16px solid pink;
        width: 100px;
        height: 158px;
        -webkit-animation: spin 1s linear infinite;
        animation: spin 1s linear infinite;
    }

    @-webkit-keyframes spin {
        0% {
            -webkit-transform: rotate(0deg);
        }

        100% {
            -webkit-transform: rotate(360deg);
        }
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
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
        <h4 class="pull-left page-title bread_title">Salary Paid Employees</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active">Salary Paid</li>
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
                        <div class="form-group row custom_form_group">
                            <label class="col-md-1 control-label">Sponsor:</label>
                            <div class="col-md-3">
                                <select class="form-control" name="SponsId" id="SponsId">
                                    <option value="">Select Sponsor</option>
                                    @foreach($sponserList as $spons)
                                    <option value="{{ $spons->spons_id }}">{{ $spons->spons_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <label class="col-md-1 control-label">Project:</label>
                            <div class="col-md-3">
                                <select class="form-control" name="proj_id" id="proj_id">
                                    <option value="">Select Project</option>
                                    @foreach($projectlist as $proj)
                                        <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group row custom_form_group">
                            <label class="col-sm-1 control-label">From:<span class="req_star">*</span></label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control fromDate" id="datepickerFrom" autocomplete="off" name="fromDate" value="{{date('m/Y')}}" required>
                            </div>
                            <label class="col-sm-1 control-label">To:<span class="req_star">*</span></label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control toDate" id="datepickerTo" autocomplete="off" name="toDate" value="{{date('m/Y')}}" required>
                            </div>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" placeholder="Search By Employee ID" autofocus name="employee_id" id="employee_id">
                            </div>
                            <div class="col-md-1">
                                <button type="button" onclick="searchPaidSalaryRecords()" style="margin-top: 2px" class="btn btn-primary waves-effect">SEARCH</button>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>

        <!-- Searching Result Table -->

        <div class="row" id="salary_paid_records_section">
            <div class="card">
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
                                @can('monthly_salary_paid_to_unpaid_permission')
                                    <button type="submit" class="btn btn-primary waves-effect" id="update_button" onclick="updateMultipleEmpSalaryStatusAsPaidToUnpaid()" disabled >Update as Unpaid</button>
                                @endcan
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="col-lg-12">
                            <div class="table-responsive">
                                <table id="alltableinfo" class="table table-bordered custom_table mb-0">
                                    <thead>
                                        <tr>
                                            <th>SN</th>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Iqama</th>
                                            <th>Sponsor</th>
                                            <th>Trade</th>
                                            <th>Worked</th>
                                            <th>Salary Month</th>
                                            <th>Method</th>
                                            <th>Paid By</th>
                                            <th>Salary</th>
                                            <th colspan="2" class="text-center">Manage</th>
                                        </tr>
                                    </thead>
                                    <tbody id="employeePendingList"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </div>
</div>





<script>
    $(document).ready(function() {
       // searchPaidSalaryRecords();
    });

    $('.loader')
        .hide() // Hide it initially
        .ajaxStart(function() {
            $(this).show();
        })
        .ajaxStop(function() {
            $(this).hide();

        });

    var $loading = $('.loader').hide();
    $(document)
        .ajaxStart(function() {
            $loading.show();
          //  alert('loading start');
        })
        .ajaxStop(function() {
            $loading.hide();
           // alert('loading done');
        });


    $('#employee_id').keydown(function (e) {
        if (e.keyCode == 13) {
            searchPaidSalaryRecords();
        }

    })

    function searchPaidSalaryRecords() {

        var fromDate = $('.fromDate').val();
        var toDate = $('.toDate').val();
        var proj_id =   $('#proj_id').val();
        var SponsId =   $('#SponsId').val();
        var employee_id = $('#employee_id').val();


        if (proj_id == "" && SponsId == "" && employee_id == "") {
            showSweetAlert("Please Input Data Correctly ",'error');
            return;
        }

        $('#employeePendingList').html('');
        $.ajax({
                type: "POST",
                url: "{{ route('salary-paid.list') }}",
                data: {
                    fromDate: fromDate,
                    toDate: toDate,
                    SponsId: SponsId,
                    proj_id: proj_id,
                    employee_id:employee_id,
                },
                dataType: "json",
                success: function(response) {

                         if(response.status != 200){
                            showSweetAlert(' Records Not Found','error');
                            return;
                        }else if(response.pendingSalary.length == 0){
                            showSweetAlert('Salary Records Not Found','error');
                            return;
                        }
                        document.getElementById("select_button").disabled = false; // activate select/unselect all button if any record is found
                        var months = ["", "January", "February", "March", "April", "May", "June",   "July", "August", "September", "October", "November", "December" ];
                        var rows = "";
                        var counter = 0;
                        $.each(response.pendingSalary, function(key, value) {
                            counter++;
                            $month_name =months[value.slh_month];// Date("F", mktime(0, 0, 0, value.slh_month, 10));
                            var updated = new Date(value.updated_at);
                            updated ='';// updated.toISOString().slice(0, 10);
                            rows += `
                                    <tr>
                                        <td>${counter}</td>
                                        <td>${value.employee_id}</td>
                                        <td>${value.employee_name}</td>
                                        <td>${value.akama_no}</td>
                                        <td>${value.spons_name}</td>
                                        <td>${value.catg_name},  <br>${ value.hourly_employee == 1 ? 'Hourly':'Basic'} </td>
                                        <td>${value.proj_name}</td>
                                        <td>${$month_name},${value.slh_year}<br>Paid at: ${value.slh_salary_date}</td>
                                        <td>${ value.slh_paid_method == null ? 'Cash Paid':'Bank Paid'} </td>
                                        <td>${value.paid_by_name}<br>Updated at: ${updated}  </td>
                                        <td>${value.slh_total_salary}</td>

                                        <td style="background-color: #fff; color:#fff; padding: 0px; " >${value.slh_auto_id}</td>
                                        <td  style="width:100px; align-items:center;">
                                            <input type="hidden" id="slh_auto_id${value.slh_auto_id}" name="slh_auto_id[]" value="${value.slh_auto_id}">
                                            <input type="checkbox" onclick="countSelectedCheckBox(${value.slh_auto_id})"  name="emp_slh_paid_checkbox-${value.slh_auto_id}" id="emp_slh_paid_checkbox-${value.slh_auto_id}" value="0">

                                        </td>
                                    </tr>
                                `
                        });
                        $('#employeePendingList').html(rows);

                },
                error:function(response){
                    showSweetAlert('Operation Failed, Please try Again','error');
                }

        });

    }

    // pay salay function

    function updateMultipleEmpSalaryStatusAsPaidToUnpaid() {


            let myTable = document.getElementById('employeePendingList');
            var selected_slh_auto_ids = new Array();

            for (let row of myTable.rows) {
                allCell = row.cells;
                var chkboxId = "emp_slh_paid_checkbox-" + allCell[11].innerText;
                if(document.getElementById(chkboxId).checked){
                    selected_slh_auto_ids.push(parseInt(allCell[11].innerText));
                }
            }
            if(selected_slh_auto_ids.length == 0){
                showSweetAlert('Please select at least one record to update','error');
                return;
            }
            $.ajax({
                type: "POST",
                url: "{{route('payment.salary.undo')}}",
                dataType: "json",
                data: {
                        slh_auto_ids: selected_slh_auto_ids
                },
                success: function(response) {
                    if (response.status != 200) {
                        showSweetAlert('Update Failed','error');
                    } else {
                        showSweetAlert('Salary Status Updated Successfully','success');
                        searchPaidSalaryRecords();
                    }
                    //  end message
                }
            })
    }


    // show sweet alert message
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

    // Datepicker
    $('document').ready(function() {
        $('#datepickerFrom').datepicker({
            autoclose: true,
            toggleActive: true,
            // startView: "months",  // Ata 1st month select korle date asbe
            minViewMode: "months",
            // format: "mm/yyyy",
        });

        $('#datepickerTo').datepicker({
            autoclose: true,
            toggleActive: true,
            // viewMode: "months",
            minViewMode: "months",
            // format: "mm/yyyy",
        });
    });


        // Check uncheck
    function checkUnCheckAllEmployeeForUpdateSalaryPaid(){

        let myTable = document.getElementById('employeePendingList');
        var total_selected = 0;
        var  total_salary = 0;
            for (let row of myTable.rows) {
                allCell = row.cells;
                var chkboxId = "emp_slh_paid_checkbox-" + allCell[11].innerText;
                document.getElementById(chkboxId).checked = !(document.getElementById(chkboxId).checked) ;
                if(document.getElementById(chkboxId).checked){

                    total_salary += Math.round(parseFloat(allCell[10].innerText),2);
                    total_selected += 1;
                }
            }
        document.getElementById("lbl_total_selected_counter").innerText = "Total Selected: "+total_selected;
        document.getElementById("lbl_total_selected_salary").innerText = "Total Salary: "+total_salary+" SAR";
        if(total_selected > 0){
            document.getElementById("update_button").disabled = false; // activate update button if any checkbox is selected
        }else {
            document.getElementById("update_button").disabled = true; // disable update button if no checkbox is selected
        }
    }

    // checkbox click event
    function countSelectedCheckBox(value){

        let myTable = document.getElementById('employeePendingList');
        var total_selected = 0;
        var  total_salary = 0;
            for (let row of myTable.rows) {
                allCell = row.cells;
                var chkboxId = "emp_slh_paid_checkbox-" + allCell[11].innerText;
                if(document.getElementById(chkboxId).checked){
                   total_salary += Math.round(parseFloat(allCell[10].innerText),2);
                     total_selected += 1;
                }
            }
        document.getElementById("lbl_total_selected_counter").innerText = "Total Selected: "+total_selected;
        document.getElementById("lbl_total_selected_salary").innerText = "Total Salary: "+total_salary+" SAR";
        if(total_selected > 0){
            document.getElementById("update_button").disabled = false; // activate update button if any checkbox is selected
        }else {
            document.getElementById("update_button").disabled = true; // disable update button if no checkbox is selected
        }

    }
</script>
@endsection
