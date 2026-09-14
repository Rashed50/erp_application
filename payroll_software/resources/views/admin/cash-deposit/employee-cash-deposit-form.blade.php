@extends('layouts.admin-master')
@section('title')Cash from Emp. @endsection
@section('content')


<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Cash Receive From Employee</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active">Cash Receive</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        @if(Session::has('success'))
        <div class="alert alert-success alertsuccess" role="alert" style="margin-left: -20px">
            <strong> {{ Session::get('success') }}</strong>
        </div>
        @endif
        @if(Session::has('error'))
        <div class="alert alert-warning alerterror" role="alert">
            <strong> {{ Session::get('error') }} </strong>
        </div>
        @endif
    </div>
    <div class="col-md-2"></div>
</div>


<!-- employee information searching with (id, passport, iqama) Start -->
<div class="row d-block">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header"></div>
            <div class="card-body card_form" style="padding: 0; padding-left:20px;">
                <div class="form-group row custom_form_group{{ $errors->has('searchBy') ? ' has-error' : '' }}">
                    <div class="col-md-3">
                        <select class="form-select" name="searchBy" id="searchBy" required>
                            <option value="employee_id">Searching By ID</option>
                            <option value="akama_no">Searching By Iqama </option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <input type="text" placeholder="Enter ID/Iqama/Passport No" class="form-control"
                            id="empl_info" name="empl_info" value="{{ old('empl_info') }}"
                             required autofocus>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" onclick="singleEmoloyeeDetails()" class="btn btn-primary waves-effect">SEARCH</button>
                    </div>
                    <div class="col-md-2">
                        {{-- <a href="{{ route('employee.cash.receive.allrecords.with.cash-payment') }}" style="background-color: #35424A;border: none;color: white;  padding: 8px;  text-align: center;  text-decoration: none;  display: inline-block;font-size: 14px;" id="allrecords"> All Records </a> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Employee Searching Data Deatils Show Start -->
<div class="row d-none" id="employee_searching_result_section">
    <div class="col-md-12">
        <table  class="table table-bordered table-striped table-hover custom_view_table show_employee_details_table">
            <tr>
                <td>
                    <span class="emp">Employee ID:</span> <span id="show_employee_id"
                        class="emp2"></span> </td>
                        <td> <span class="emp">Status:</span> <span
                            id="show_employee_job_status" class="emp2"  style="font-weight:bold;color:red;font-size:18px;"></span>
                    </td>

            </tr>
            <tr>
                <td> <span class="emp">Name:</span> <span id="show_employee_name"
                        class="emp2"></span> </td>
                <td> <span class="emp">Project:</span> <span id="show_employee_project_name" class="emp2"></span>
                    <span class="emp">, Working: </span> <span
                            id="show_employee_working_shift" class="emp2"></span>
                   <span class="emp">, Trade:</span> <span
                            id="show_employee_category" class="emp2"></span>

                </td>
            </tr>

            <tr>
                <td> <span class="emp">Passport No:</span> <span id="show_employee_passport_no"
                    class="emp2"></span>
                    <span class="emp">&nbsp;&nbsp; </span> <span id="show_employee_passport_file" class="emp2"></span>
                </td>
                <td> <span class="emp">Iqama No:</span> <span id="show_employee_akama_no"
                            class="emp2"></span>
                     <span class="emp">&nbsp;&nbsp; </span> <span id="show_employee_iqama_file" class="emp2"></span>
                </td>
            </tr>

            <tr>
                <td> <span class="emp">Emp. Type:</span> <span id="show_employee_type"
                        class="emp2"></span> </td>
                <td> <span class="emp">Agency:</span> <span id="show_employee_agency_name"
                            class="emp2"></span> </td>
             </tr>

            <tr>
                <td> <span class="emp">Sponsor: </span> <span
                        id="show_employee_working_sponsor_name" class="emp2"></span> </td>
                <td>
                        <span class="emp">Mobile Number:</span> <span id="show_employee_mobile_no"
                        class="emp2"></span>
                        <span class="emp">,Home Contact:</span> <span id="show_employee_phone_no"
                        class="emp2"></span>
                    </td>
            </tr>

            <tr>
                <td> <span class="emp">Accomodation:</span> <span
                    id="show_employee_accomodation_ofB_name" class="emp2"></span>
                </td>
                <td>
                    <span class="emp">Permanent Address:</span>
                    <span id="show_employee_address_Ds"></span>,
                    <span id="show_employee_address_D"></span> ,
                    <span id="show_employee_address_C" class="emp2"></span>
                </td>
            </tr>

        </table>
    </div>
</div>


<div class="row d-none" id="payment_receive_form_section">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header"></div>
            <div class="card-body card_form">
                <div class="row">
                  <!-- Cash payment Form -->
                    <div class="col-md-2"></div>
                    <div class="col-md-8">
                        <form class="form-horizontal" id="cash-payment-byemp" action="{{ route('employee-advance-payment-cash-receive') }}" method="post">
                            @csrf
                            <div class="card">

                                <div class="card-body card_form" style="padding-top: 0;">

                                    <div class="form-group row custom_form_group{{ $errors->has('emp_id') ? ' has-error' : '' }}">
                                        <label class="control-label col-md-3"  >Employee ID:</label>
                                        <div class="col-md-6">
                                            <input readonly type="text" class="form-control typeahead" placeholder="Input Employee ID" name="emp_id" id="emp_id">
                                            @if ($errors->has('emp_id'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('emp_id') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                        <div id="showEmpId"></div>
                                    </div>

                                    <div class="form-group row custom_form_group{{ $errors->has('pay_amount') ? ' has-error' : '' }}">
                                        <label class="control-label col-md-3">Received Amount:<span class="req_star">*</span></label>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control" placeholder="Input Amount" name="pay_amount" value="0" required>
                                            @if ($errors->has('pay_amount'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('pay_amount') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-group row custom_form_group">
                                        <label class="control-label col-md-3">Received Date</label>
                                        <div class="form-group col-md-6">
                                            <input type="date" class="form-control" name="payment_date" value="{{ Carbon\Carbon::now()->format('Y-m-d') }}">
                                        </div>
                                    </div>


                                    <div class="form-group row custom_form_group">
                                        <label class="control-label col-md-3">Remarks:</label>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control" placeholder="Type Here... " name="payment_remarks">
                                        </div>
                                    </div>

                                </div>
                                <div class="card-footer card_footer_button text-center">
                                    <button type="submit" class="btn btn-primary waves-effect">SAVE</button>
                                </div>


                            </div>
                        </form>
                    </div>
                    <div class="col-md-2"></div>
                </div>
           </div>
        </div>
    </div>
</div>
<!-- Cash Payemnt List  -->
<div class="row" id="payment_receive_records_section">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table id="alltableinfo" class="table table-bordered custom_table mb-0">
                                <thead>
                                    <tr>
                                        <th>Emp.ID</th>
                                        <th>Name</th>
                                        <th>Iqama No</th>
                                        <th>Project</th>
                                        <th>Date</th>
                                        <th>Month</th>
                                        <th>Amount</th>
                                        <th>Remarks</th>
                                        <th>Manage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $months = [
                                            'January', 'February', 'March', 'April', 'May', 'June',
                                            'July', 'August', 'September', 'October', 'November', 'December'
                                        ];
                                    @endphp

                                    @foreach($cashPaymentlist as $item)
                                    <tr>
                                        @php
                                            $month_name = $months[$item->month - 1];
                                        @endphp

                                        <td>{{ $item->employee_id }}</td>
                                        <td>{{ $item->employee_name }}</td>
                                        <td>{{ $item->akama_no }}</td>
                                        <td>{{ $item->proj_name }}</td>
                                        <td>{{ $item->date == NULL ? '--' : $item->date }}</td>
                                        <td>{{ $month_name }}</td>
                                        <td>{{ $item->adv_amount }}</td>
                                        <td>{{ $item->adv_remarks }}</td>
                                        <td>

                                            @can('employee_advance_edit')
                                                <a href="" id="edit_cashpaid_record" data-toggle="modal" data-target="#edit_modal" data-id="{{ $item->id }}"><i class="fa fa-edit fa-sm edit_icon"></i></a>
                                            @endcan
                                            @can('employee_advance_delete')
                                                <a href="{{ route('delete-employee-advance-payment-cash-receive',[$item->id]) }}" title="delete" id="delete"><i class="fa fa-trash fa-sm delete_icon"></i></a>
                                            @endcan

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

<!--  Edit Modal -->
<div class="modal fade" id="edit_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form class="form-horizontal" action="{{ route('employee-advance-payment-cash-receive') }}" enctype="multipart/form-data" method="post"
            onsubmit="this.querySelector('button[type=submit]').disabled = true" >
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle"> Cach Received From Employee Information Details<span class="text-danger" id="errorData"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="card-body card_form" style="padding-top: 0;">

                                    <div class="form-group row custom_form_group{{ $errors->has('emp_id') ? ' has-error' : '' }}">
                                        <label class="control-label col-md-3"  >Employee ID:</label>
                                        <div class="col-md-9">
                                            <input type="hidden" name="record_id" id="record_id" value="{{ $item->id }}" required>
                                            <input type="text"  readonly required class="form-control typeahead" placeholder="Input Employee ID"  name="emp_id" id="empoyee_id">
                                            @if ($errors->has('emp_id'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('emp_id') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                        <div id="showEmpId"></div>
                                    </div>
                                        <div class="form-group row custom_form_group{{ $errors->has('emp_id') ? ' has-error' : '' }}">
                                        <label class="control-label col-md-3"  >Employee Name:</label>
                                        <div class="col-md-9">
                                            <input readonly type="text" class="form-control typeahead"    name="employee_name" id="employee_name" >
                                            @if ($errors->has('emp_id'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('emp_id') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                        <div id="showEmpId"></div>
                                    </div>



                                    <div class="form-group row custom_form_group{{ $errors->has('pay_amount') ? ' has-error' : '' }}">
                                        <label class="control-label col-md-3">Amount:<span class="req_star">*</span></label>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control" placeholder="Input Amount" id="pay_amount" name="pay_amount" value="0" required>
                                            @if ($errors->has('pay_amount'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('pay_amount') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-group row custom_form_group">
                                        <label class="control-label col-md-3">Date</label>
                                        <div class="form-group col-md-9">
                                            <input type="date" class="form-control" id="payment_date" name="payment_date" value="" >
                                        </div>
                                    </div>


                                    <div class="form-group row custom_form_group">
                                        <label class="control-label col-md-3">Remarks:</label>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control" placeholder="Type Here... " value="" id="edit_payment_remarks" name="payment_remarks">
                                        </div>
                                    </div>
                                     <div class="form-group row custom_form_group">
                                        <label class="control-label col-md-3">Attachment:</label>
                                        <div class="col-md-9">
                                            <input type="file" class="form-control" value="" id="attached_file" name="attached_file">
                                         </div>
                                    </div>

                                </div>

                </div>

                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" id="update_button"   class="btn btn-primary waves-effect"  >Update</button>
                 </div>
            </form>

        </div>
    </div>
</div>


<!-- ========= Cash Payment By EMployee =========== -->
<script type="text/javascript">

    // Open Modal For Update cash paid by employee Record
    $(document).on("click", "#edit_cashpaid_record", function(){

        var myModal = new bootstrap.Modal(document.getElementById('edit_modal'));
        var auto_id = $(this).data('id');
        $.ajax({
            type: "GET",
            url:  "{{ url('admin/employee/cash-payment/record-search')}}" + "/" + auto_id, // "{{ route('search-employee-cash-payment-record','id') }}",
            success: function(response){
                debugger;
                 if(response.status == 200){
                    $('#record_id').val(response.data.id);
                    $('#empoyee_id').val(response.data.employee_id);
                    $('#employee_name').val(response.data.employee_name);
                    $('#pay_amount').val(response.data.adv_amount);
                    $('#payment_date').val(response.data.date);
                    $('#edit_payment_remarks').val(response.data.adv_remarks);


                }else{

                   document.getElementById("edit_modal").style.display = "none";
                   showSweetAlertMessage('error',"Data Not Found, Refresh the page and try again");
                }

            }
        })

    });


function closeModal() {
  document.getElementById('myModal').style.display = 'none';
}

    // Method For Reset All Loaded Data
    function resetEmpInfo() {
        $("#show_employee_address_C").html('');
        $("#show_employee_address_D").html('');
        $("#show_employee_address_Ds").html('');
        $("#show_employee_agency_name").html('');
        $("#show_employee_passport_no").html('');
        $("#show_employee_mobile_no").html('');
        $("#show_employee_phone_no").html('');
        $("#show_employee_name").html('');
        $("#show_employee_category").html('');
        $("#show_employee_id").html('');
        $("#show_employee_job_status").html('');
        $("#show_employee_akama_no").html('');
        $("#show_employee_type").html('');
        $("#show_employee_project_name").html('');
        $("#show_employee_working_shift").html('');
        $("#show_employee_accomodation_ofB_name").html('');
        $("#show_employee_working_sponsor_name").html('');
        $("#show_employee_passport_file").html('');
        $("#show_employee_iqama_file").html('');
    }
    $('#empl_info').keydown(function (e) {
        if (e.keyCode == 13) {
            $("#employee_searching_result_section").removeClass("d-block").addClass("d-none");
            $("#payment_receive_form_section").removeClass("d-block").addClass("d-none");
            singleEmoloyeeDetails();
        }
    })


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

    //   Single Employee Details Info
    function singleEmoloyeeDetails() {

        resetEmpInfo() // reset All Employe Info
        var searchType = $('#searchBy').find(":selected").val();
        var searchValue = $("#empl_info").val();
            if ($("#empl_info").val().length === 0) {
                showSweetAlertMessage('error',"Please Enter Employee ID/Iqama/Passport Number");
                return
            }
            $.ajax({
                type: 'POST',
                url: "{{ route('employee.searching.searching-with-multitype.parameter') }}",
                data: {
                    search_by: searchType,
                    employee_searching_value: searchValue
                },
                dataType: 'json',
                success: function (response) {

                    if (response.status != 200) {

                        $("#employee_searching_result_section").removeClass("d-block").addClass("d-none");
                        $("#payment_receive_form_section").removeClass("d-block").addClass("d-none");
                        showSweetAlertMessage("error","Employee Not Found");
                        return;
                    }

                    if (response.findEmployee.length > 1) {
                         showSweetAlertMessage("error","Critical Error Found, Please Contact with Software Support");
                    } else {
                        showSearchingEmployee(response.findEmployee[0], response.empOfficeBuilding, response.getAllProject, response.allEmployeeStatus, response.designation, response.agencies, response.sponsors);
                    }
                }, // end of success
                error:function(response){
                    showSweetAlertMessage('error',"Operation Failed, Please try Again");
                }
            }); // end of ajax calling

    }

    // End of Method for Router calling
    function showSearchingEmployee(findEmployee, empOfficeBuilding, getAllProject, allEmployeeStatus, designation, agencies, sponsors) {
        /* show employee information in employee table */

        $("#employee_searching_result_section").removeClass('d-none').addClass("d-block"); // show signle employee details
        $("#payment_receive_form_section").removeClass('d-none').addClass("d-block"); // show update form

        // Hidden feild employee id, emp auto id, current sponsor id
        $('#emp_id').val(findEmployee.employee_id);
        $('input[id="input_employee_id"]').val(findEmployee.employee_id);
        $('input[id="input_emp_sponsor_id"]').val(findEmployee.sponsor_id);
        $("span[id='show_employee_address_C']").text(findEmployee.country_name);
        $("span[id='show_employee_address_D']").text(findEmployee.division_name);
        $("span[id='show_employee_address_Ds']").text(findEmployee.district_name);
        $("span[id='show_employee_address_details']").text(findEmployee.details);
        $("span[id='show_employee_agency_name']").text(findEmployee.agc_title);
        $("span[id='show_employee_passport_no']").text(findEmployee.passfort_no);
        $("span[id='show_employee_mobile_no']").text(findEmployee.mobile_no+", Mobile No: "+ (findEmployee.phone_no != null ? findEmployee.phone_no :""));

        $("span[id='show_employee_phone_no']").text(findEmployee.phone_no);
        $("span[id='show_employee_akama_no']").text(findEmployee.akama_no);
        $("span[id='show_employee_name']").text(findEmployee.employee_name);
        $("span[id='show_employee_category']").text(findEmployee.catg_name);
        $("span[id='show_employee_id']").text(findEmployee.employee_id);

        var job_status = (findEmployee.job_status > 0 ? findEmployee.title : "Waiting for Approval") + (findEmployee.salary_status == 1 ? ', Salary: Active' : ", Salary: Hold");
        job_status += ", "+ (findEmployee.emp_act_remarks != null ? findEmployee.emp_act_remarks : "");

        $("span[id='show_employee_job_status']").text(job_status);
        $("span[id='show_employee_accomodation_ofB_name']").text(findEmployee.ofb_name);
        $("span[id='show_employee_working_sponsor_name']").text(findEmployee.spons_name);

        var passport = `
                        <span class="emp2" style="text-align: center; text-transform: capitalize;" colspan="3">
                            <a target="_blank" href="{{ url('${findEmployee.pasfort_photo}') }}" ><i class="fas fa-eye fa-lg view_icon"></i></a></span>
                    `
        var iqama =  `<span class="emp2" style="text-align: center; text-transform: capitalize;" colspan="3"><a target="_blank" href="{{ url('${findEmployee.akama_photo}') }}" class="fas fa-eye fa-lg view_icon"></a></span>
                    `
        findEmployee.pasfort_photo == null ?  $("#show_employee_passport_file").append('') :  $("#show_employee_passport_file").append(passport);
        findEmployee.akama_photo == null ?  $("#show_employee_iqama_file").append('') :  $("#show_employee_iqama_file").append(iqama);

        /* Employee Type  */
        if (findEmployee.emp_type_id == 1) {
            $("span[id='show_employee_type']").text("Direct Manpower");
        } else {
            $("span[id='show_employee_type']").text("Indirect Manpower");
        }
        /* show project name */
        if (findEmployee.project_id == null) {
            $("span[id='show_employee_project_name']").text("No Assigned Project!");
        } else {
            $("span[id='show_employee_project_name']").text(findEmployee.proj_name);
        }

        /* Employee Work Shift Status  */
         findEmployee.isNightShift == 0 ? $("span[id='show_employee_working_shift']").text("Day Shift"): $("span[id='show_employee_working_shift']").text("Night Shift")


    }


























</script>
<!-- ========= Cash Payment by Employee =========== -->

@endsection
