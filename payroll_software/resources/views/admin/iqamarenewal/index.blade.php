@extends('layouts.admin-master')
@section('title')Iqama Renewal @endsection
@section('content')



<!-- Session Flash Message -->
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title"> Iqama Renewal Expense Insertion</h4>
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

<!-- employee information searching with (id, passport, iqama) Start -->
<div class="row d-block">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body card_form" style="padding: 0;">

                <div  class="form-group row custom_form_group">
                    <div class="col-sm-3">
                        @can('multi_emp_iqama_expiration_date_update_by_excel_upload')
                            <button type="button" onclick="openIqamaRenewalExpenseSection()"  class="btn btn-primary waves-effect">Upload Excel</button>
                        @endcan
                    </div>
                    <div class="col-md-4">
                        @can('employee-anualsfee')
                            <input type="text" placeholder="Enter Employee ID or Iqama Number" class="form-control"
                                id="empl_info" name="empl_info" value="{{ old('empl_info') }}"
                                required autofocus>
                            <span id="employee_not_found_error_show" class="d-none"
                                style="color: red"></span>
                            @if ($errors->has('empl_info'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('empl_info') }}</strong>
                            </span>
                            @endif
                        @endcan
                    </div>
                    <div class="col-md-2">
                        @can('employee-anualsfee')
                            <button type="submit" onclick="searchEmployeeBasicInformation()" class="btn btn-primary waves-effect">SEARCH</button>
                        @endcan
                    </div>
                    <div class="col-sm-3">
                        {{-- @can('multi_emp_iqama_expiration_date_update_by_excel_upload')
                            <button type="button" onclick="openRenewalExpenseImportSection()"  class="btn btn-primary waves-effect">Upload Insurance Data </button>
                        @endcan --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


{{-- Searching  Employee Information Details--}}
<div id="iqama_expense_renewal_section" class="d-none" >
    <div class="row"  >
        <!-- employee Deatils -->
        <div class="col-md-6">
            <table
                class="table table-bordered table-striped table-hover custom_view_table show_employee_details_table"
                id="showEmployeeDetailsTable">

                <tr>
                    <td> <span class="emp"> Name:</span> <span id="show_employee_name"
                            class="emp2"></span> </td>
                </tr>
                <tr>
                    <td> <span class="emp">Iqama No:</span> <span id="show_employee_akama_no"
                            class="emp2"></span> </td>
                </tr>
                <tr>
                    <td> <span class="emp">Project:</span> <span id="show_employee_project_name"
                            class="emp2"></span> </td>
                </tr>

            </table>
        </div>
        <div class="col-md-6">

            <table
                class="table table-bordered table-striped table-hover custom_view_table show_employee_details_table"
                id="showEmployeeDetailsTable">
                <tr>
                    <td> <span class="emp">Sponsor:</span> <span id="show_employee_sponsor_name"
                            class="emp2"></span> </td>
                </tr>
                <tr>
                    <td> <span class="emp">Employee Status:</span>
                        <span  id="show_employee_status" class="emp2"  style="font-weight:bold;color:red;font-size:18px;"></span>
                        </td>
                </tr>
                <tr>
                    <td> <span class="emp"><b>Salary Type:</b></span> <span id="show_employee_saudi_tax"  class="emp2" style="font-weight:bold;color:red;font-size:18px;"></span> </td>
                </tr>

            </table>


        </div>
    </div>
    {{-- Iqama Renewal Expense Insert User Information --}}
    <div id="IqamaExpenseInsertForm">
        <form class="form-horizontal" id="registration" method="post" action="{{ route('insert-iqamarenewal-fee') }}">
            @csrf
            <div class="card">
                <div class="card-body card_form">
                    <div class="form-group row custom_form_group{{ $errors->has('emp_auto_id') ? ' has-error' : '' }}">
                        <div class="col-sm-5"></div>
                        <div class="col-sm-5">
                            <span class="req_star">Employee ID <span id="show_employee_id"
                                    class="req_star">Required</span>
                            </span>
                            <input type="hidden" class="form-control" id="emp_auto_id" name="emp_auto_id" value=""
                                required>
                            @if ($errors->has('emp_auto_id'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('emp_auto_id') }}</strong>
                            </span>
                            @endif
                        </div>
                        <div class="col-sm-2"></div>

                    </div>
                    <div class="form-row">
                        <div class="form-group row col-md-6">
                            <label class="col-sm-4 control-label  text-right">Jawazat Fee:<span class="req_star">*</span></label>
                            <div class="col-sm-6">
                                <input type="number" placeholder="Jawazat Fee" class="form-control" id="jawazat_fee"
                                    name="jawazat_fee" onkeyup="calculateTotalAmount()" value="0" required>
                                @if ($errors->has('jawazat_fee'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('Jawazat Fee Required') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row col-md-6">

                            <label class="col-sm-4 control-label text-right">Maktab Al Amal Fee:<span
                                    class="req_star">*</span></label>
                            <div class="col-sm-6">
                                <input type="number" placeholder="Maktab Al Amal Fee" class="form-control" id="maktab_alamal_fee"
                                    name="maktab_alamal_fee" onkeyup="calculateTotalAmount()" value="0" required>
                                @if ($errors->has('maktab_alamal_fee'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('Maktab Al Amal Fee Required') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group row col-md-6">
                            <label class="col-sm-4 control-label text-right">VISA Amount:<span class="req_star">*</span></label>
                            <div class="col-sm-6">
                                <input type="number" placeholder="BD Amount" class="form-control" id="bd_amount"
                                    name="bd_amount" value="0" onkeyup="calculateTotalAmount()" required>
                                @if ($errors->has('bd_amount'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('Jawazat Fee Required') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row col-md-6">

                            <label class="col-sm-4 control-label text-right">Medical Inssurance:<span
                                    class="req_star">*</span></label>
                            <div class="col-sm-6">
                                <input type="number" placeholder="Maktab Al Amal Fee" class="form-control" id="medical_insurance"
                                    name="medical_insurance" value="0" onkeyup="calculateTotalAmount()" required>
                                @if ($errors->has('medical_insurance'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('Medical Inssurance') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group row col-md-6">
                            <label class="col-sm-4 control-label text-right">Jawazat Fee Penalty:<span
                                    class="req_star">*</span></label>
                            <div class="col-sm-6">
                                <input type="number" placeholder="" class="form-control" id="jawazat_penalty"
                                    name="jawazat_penalty" value="0" onkeyup="calculateTotalAmount()" required>
                            </div>
                        </div>
                        <div class="form-group row col-md-6">

                            <label class="col-sm-4 control-label text-right text-right">Others:<span class="req_star">*</span></label>
                            <div class="col-sm-6">
                                <input type="number" placeholder="" class="form-control" id="others_fee" name="others_fee"
                                    value="0" onkeyup="calculateTotalAmount()" required>
                                @if ($errors->has('others_fee'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('OThers') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group row col-md-6">
                            <label class="col-sm-4 control-label text-right">Emp Tranfer Fee<span
                                    class="req_star">*</span></label>
                            <div class="col-sm-6">
                                <input type="number" placeholder="" class="form-control" id="transfer_fee"
                                    name="transfer_fee" value="0" onkeyup="calculateTotalAmount()" required>
                            </div>
                        </div>
                        <div class="form-group row col-md-6">
                        </div>
                    </div>

                    <div class="form-row" >

                        <div class="col-sm-2  text-right"> <span class="req_star" style="font-size: 18px; font-weight:bold; color:red"><b> Total Expense</b> </span> </div>
                        <div class="col-sm-8">
                            <input type="text" class="form-control typeahead"  placeholder="" name="total_amount"
                                id="total_amount" value="0" required readonly style="font-size: 18px; font-weight:bold; color:red">
                        </div>
                        <div class="col-sm-2"> </div>
                    </div>
                    <br>
                    <div class="form-row">
                        <div class="form-group row col-md-6">
                            <label class="col-sm-4 control-label text-right"> Renewal Duration:<span
                                    class="req_star">*</span></label>
                            <div class="col-sm-6">
                                <select class="form-select" name="duration" id="duration">
                                    @for ($i = 1; $i <= 72; $i++)
                                        <option value="{{ $i }}" {{ $i == 3 ? 'selected' : '' }}>
                                            {{ $i }} Month{{ $i > 1 ? 's' : '' }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="form-group row col-md-6 text-right">
                            <label class="col-sm-4 control-label">Renewal Date:</label>
                            <div class="col-sm-6">
                                <input type="date" name="renewal_date" class="form-control"
                                    max="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group row col-md-6">
                            <label class="col-sm-4 control-label text-right">Payment Number:<span class="req_star">*</span></label>
                            <div class="col-sm-6">
                                <input type="text" placeholder="Payment Number" class="form-control" id="payment_number"
                                    name="payment_number">
                            </div>
                        </div>
                        <div class="form-group row col-md-6">
                            <label class="col-sm-4 control-label text-right">Payment Date:</label>
                            <div class="col-sm-6">
                                <input type="date" name="payment_date" class="form-control"
                                    max="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group row col-md-6">
                            <label class="col-sm-4 control-label text-right">Renewal Status:<span class="req_star">*</span></label>
                            <div class="col-sm-6">
                                <select class="form-control" name="renewal_status" id="renewal_status">
                                    <option value="1"> Initial Step </option>
                                    <option value="2"> Payment Initialize</option>
                                    <option value="3">Payment Completed</option>
                                    <option value="4">Renewal Pending</option>
                                    <option value="5">Renewal Completed</option>

                                </select>
                            </div>
                        </div>
                        <div class="form-group row col-md-6">
                            <label class="col-sm-4 control-label text-right">Expense Paid By:</label>
                            <div class="col-sm-6">
                                <select class="form-control" name="expense_paid_by" id="expense_paid_by">
                                    <option value="1"> Self</option>
                                    <option value="2"> Company</option>

                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group row col-md-6">
                            <label class="col-sm-4 control-label text-right">Reference Employee:<span
                                    class="req_star">*</span></label>
                            <div class="col-sm-7">
                                <input type="number" class="form-control typeahead" placeholder="Input Employee ID"
                                    name="reference_emp_id" id="emp_id_search" required onkeyup="empSearch()"
                                    onfocus="showResult()" onblur="hideResult()">
                                <div id="showEmpId"></div>
                                @if ($errors->has('emp_id'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('emp_id') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row col-md-6">

                            <label class="col-sm-4 control-label text-right">Iqama Expire:<span
                                    class="req_star">*</span></label>
                            <div class="col-sm-6">
                                <input type="date" name="iqama_expire_date" class="form-control" id="iqama_expire_date"
                                value="{{ date('Y-m-d') }}" min= "{{ date('Y-m-d') }}" required >
                            </div>


                        </div>
                    </div>


                     <div class="form-row">
                            <div class="form-group row col-md-6">
                                <label class="col-sm-4 control-label text-right">Purpose:<span
                                    class="req_star">*</span></label>
                                <div class="col-sm-6">
                                    <select class="form-select" name="payment_purpose_id" id="payment_purpose_id" required>
                                        <option value="">Select Purpose</option>
                                        <option value="1"> Iqama Renewal</option>
                                        <option value="2"> Medical Insurance</option>
                                        <option value="3"> Exit-Re-Entry</option>
                                        <option value="4"> Family Iqama</option>
                                        <option value="5"> Family Medical Insurance</option>
                                        <!--<option value="6">Traffic Violation</option>-->

                                    </select>
                                    @if ($errors->has('payment_purpose_id'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('payment_purpose_id') }}</strong>
                                    </span>
                                    @endif
                                </div>

                            </div>
                            <div class="form-group row col-md-6">
                                <label class="col-sm-4 control-label text-right">Remarks:</label>
                                <div class="col-sm-6">
                                    <input type="text" placeholder="Remarks Here" class="form-control" id="remarks"
                                        name="remarks">
                                </div>

                            </div>
                    </div>

                </div>

                <div class="card-footer card_footer_button text-center">
                    <button type="submit" id="onSubmit" class="btn btn-primary waves-effect">SAVE</button>
                </div>
            </div>
        </form>
    </div>
</div>


<!-- Iqama Expiration Date Excell File Upload Start-->
<div class="row d-none" id="iqama_renewal_expense_excel_import_section">
    <div class="col-lg-12">
        <div class="card">
            {{--excel upload section        --}}
                  <form method="POST" enctype="multipart/form-data" id="upload_iqama_expense_form"  onsubmit="excel_upload_button.disabled = true;" >

                        <div class="form-group row custom_form_group" style="padding-top:10px;">
                            <label class="col-sm-2 control-label">Upload File:<span class="req_star">*</span></label>
                            <div class="col-sm-3">
                                <select name="upload_type_ddl" id="upload_type_ddl" class="form-select" required>
                                    <option value="1">Iqama Expiration Date</option>
                                    <option value="2">Insurace Expense </option>
                                    <!--<option value="3">Employee Sponsor Update </option>-->

                                </select>
                            </div>
                            <div class="col-sm-4">
                                <input type="file" name="file" placeholder="Choose File" id="file" required>
                                          <span class="text-danger">{{ $errors->first('file') }}</span>
                            </div>
                            <div class="col-sm-2">
                                    <button type="submit" id ="excel_upload_button" class="btn btn-primary">UPLOAD</button>
                            </div>
                            <div class="col-sm-1">  </div>
                        </div>
                  </form>
            {{-- form submit section --}}
            <div class="card-body" id="excell_file_upload_emp_list_table_section" >

                <form method="GET" action="{{route('iqama.renewal.expense.upload.update.request')}}" id="imported_records_preview_form"   onsubmit="excel_submit_button.disabled = true;" >

                    <div class="col-12 d-block" id="valid_records_div_section" >
                        <div class="form-group row custom_form_group">
                            <div class="col-sm-9">
                            <input type="text" id="upload_file_type" class="form-control col-sm-3" name="upload_file_type" value="1" hidden >
                            </div>
                            <div class="col-sm-3">
                                <button type="submit" id ="excel_submit_button"  class="btn btn-primary waves-effect">Update </button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table id="excell_file_upload_emp_list_table" class="table table-bordered custom_table mb-0">
                                <thead id="excell_file_upload_emp_list_table_head">
                                </thead>
                                <tbody id="excell_file_upload_emp_list_table_body">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>
                <div class="col-12 d-none" id ="upload_error_div_section">
                    <br/>
                    <h3> <span id="data_not_found_title"> </span> </h3>
                        <div class="table-responsive">
                            <table id="excell_file_upload_error_emp_list_table" class="table table-bordered custom_table mb-0">
                                <thead id="excell_file_upload_error_emp_list_table_head">
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



<!-- Excell File Upload End -->
<script type="text/javascript">

    function openIqamaRenewalExpenseSection(){
        $('#iqama_expense_renewal_section').addClass('d-none').removeClass('d-block');
        $('#iqama_renewal_expense_excel_import_section').addClass('d-block').removeClass('d-none');
    }

    function calculateTotalAmount() {
            var value1 = document.getElementById('jawazat_fee').value != "" ? parseFloat(document.getElementById('jawazat_fee').value) : 0;
            var value2 = document.getElementById('maktab_alamal_fee').value != "" ? parseFloat(document.getElementById('maktab_alamal_fee').value) : 0;
            var value3 = document.getElementById('bd_amount').value != "" ? parseFloat(document.getElementById('bd_amount').value) : 0;
            var value4 = document.getElementById('medical_insurance').value != "" ? parseFloat(document.getElementById('medical_insurance').value) : 0;
            var value5 = document.getElementById('others_fee').value != "" ? parseFloat(document.getElementById('others_fee').value) : 0;
            var jawazat_penalty = document.getElementById('jawazat_penalty').value != "" ? parseFloat(document.getElementById('jawazat_penalty').value) : 0;
            var transfer_fee = document.getElementById('transfer_fee').value != "" ? parseFloat(document.getElementById('transfer_fee').value) : 0;

            document.getElementById("total_amount").value = (value1+value2+value3+value4+value5+jawazat_penalty+transfer_fee);

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

    // Enter Key Press Event Fire
    $('#empl_info').keydown(function(e) {
        if (e.keyCode == 13) {
            searchEmployeeBasicInformation();
        }
    })

    //   Single Employee Details Info
    function searchEmployeeBasicInformation() {

            $('#iqama_renewal_expense_excel_import_section').addClass('d-none').removeClass('d-block');

            var searchType = "employee_id";
            var searchValue = $("#empl_info").val();

            if ($("#empl_info").val().length == 10) {
                searchType = "akama_no";
            }
            if ($("#empl_info").val().length == 0) {
                showSweetAlertMessage('error',"Please Enter Employee ID or Iqama Number");
                return ;
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
                        $('input[id="emp_auto_id"]').val('');
                        $("span[id='show_employee_id']").text("is Required");
                        $("input[id='emp_id_search']").val('');
                        $("span[id='error_show']").text('This Id Dosn,t Match!');
                        $("span[id='error_show']").addClass('d-block').removeClass('d-none');
                        $("#iqama_expense_renewal_section").addClass("d-none").removeClass("d-block");
                        showSweetAlertMessage('error',"Employee Not Found");
                        return ;
                    }
                    $("#empl_info").val('');

                    if (response.total_emp > 1) {
                         alert('Mone Than One Employee Found,Please Inform this issue to Software Engineer');
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

                $("input[id='emp_id_search']").val('');
                $("span[id='error_show']").removeClass('d-block').addClass('d-none');
                $("#iqama_expense_renewal_section").removeClass("d-none").addClass("d-block");


               /* show employee information in employee table */
                $("span[id='show_employee_id']").text(findEmployee.employee_id);
                $("span[id='show_employee_name']").text(findEmployee.employee_name);


                $("span[id='show_employee_akama_no']").text((findEmployee.akama_no+", "+findEmployee.akama_expire_date+", Passport No: "+findEmployee.passfort_no));
                $('input[id="emp_auto_id"]').val(findEmployee.emp_auto_id);
                var job_status = findEmployee.title + (findEmployee.salary_status == 1 ? ', Salary: Active' : ", Salary: Hold");
                    $("span[id='show_employee_status']").text(job_status);

                if (findEmployee.project_id == null) {
                $("span[id='show_employee_project_name']").text("No Assigned Project!");
                } else {
                $("span[id='show_employee_project_name']").text(findEmployee.proj_name);
                }

                if (findEmployee.sponsor_id == null) {
                $("span[id='show_employee_sponsor_name']").text("No Assigned Sponsor!");
                } else {
                $("span[id='show_employee_sponsor_name']").text(findEmployee.spons_name);
                }

                if(findEmployee.hourly_employee){
                    $('#expense_paid_by').val(1);
                    $("span[id='show_employee_saudi_tax']").text("Hourly");
                }else{
                    $('#expense_paid_by').val(2);
                    $("span[id='show_employee_saudi_tax']").text("Basic");
                }

    }


    $(document).ready(function (e) {
        // excell data uploaded button event
        $('#upload_iqama_expense_form').submit(function(e) {

             // reset previous records
            $('#excell_file_upload_emp_list_table_body').html('');
            $('#excell_file_upload_error_emp_list_table_body').html('');

            var upload_file_type = $('#upload_type_ddl').val(); // 1 iqama expire date update, 2= expense file upload
            $('#upload_file_type').val(upload_file_type);
           // alert(upload_file_type);

            e.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                    type:'POST',
                    url: "{{ route('iqama.renewal.expense.upload.preview.request')}}",
                    data: formData,
                    cache:false,
                    contentType: false,
                    processData: false,
                    success: (response) => {


                        document.getElementById("excel_upload_button").disabled = false;
                        if(response.status != 200){
                            showSweetAlertMessage('error',response.message);
                            return;
                        }
                        $('#upload_file_type').val(upload_file_type);
                        $("#valid_records_div_section").removeClass("d-none").addClass("d-block");
                        bindValidRecordsTableData(upload_file_type,response.records);

                        $("#upload_error_div_section").removeClass("d-none").addClass("d-block");
                        bindRecordNotFoundTableData(upload_file_type,response.records_not_found);

                    },
                    error: function(data){
                        document.getElementById("excel_upload_button").disabled = false;
                        showSweetAlertMessage('error',"Operation Failed, Please Check Format and Tryp Again");
                    }
            });
        });

        // Submit Imported Excell Data
        // $('#imported_records_preview_form').submit(function(e) {

        //    // alert(200);
        //     e.preventDefault();
        //     var formData = new FormData(this);
        //     $.ajax({
        //         type:'GET',
        //         url: "{{ route('iqama.renewal.expense.upload.update.request')}}",
        //         data: {
        //             upload__type:2,
        //         },
        //         // cache:false,
        //         // contentType: false,
        //         // processData: false,
        //         success: (response) => {
        //             if(response.status == 200){
        //                 showSweetAlertMessage("success", response.message)
        //             }else {
        //                 showSweetAlertMessage('error', response.message)
        //             }
        //           //  this.reset();
        //           //  location.reload();

        //         },
        //         error: function(data){
        //             debugger;
        //             document.getElementById("excel_submit_button").disabled = false;
        //             showSweetAlertMessage('error', "Operation Failed Try Again");

        //         }
        //     });

        // });

    });

    function bindValidRecordsTableData(upload_file_type, records){

        var rows = "";
        var counter = 1;
        if(upload_file_type == 1){
            // iqama expire date update
            rows = `<tr>
                        <th>S.N</th>
                        <th>Emp Id</th>
                        <th>Name</th>
                        <th>Iqama No</th>
                        <th>Salary Type</th>
                        <th>Expire Date</th>
                        <th>Status</th>
                    </tr>`;

            $('#excell_file_upload_emp_list_table_head').html(rows);

            rows = "";

            $.each(records, function (key, value) {
                rows += `
                    <tr>
                        <td>${counter++}</td>
                        <td>${value.employee_id}</td>
                        <td>${value.employee_name} </td>
                        <td>${value.akama_no}</td>
                        <td>${value.hourly_employee == 1 ? 'Hourly' : 'Basic Salary'} </td>
                        <td>${value.iqama_expire_date}</td>
                        <td>${value.upload_status}</td>
                    </tr>
                    `
            });
            $('#excell_file_upload_emp_list_table_body').html(rows);

        }else if(upload_file_type == 2){

            // insurance expense insert
            rows = `<tr>
                        <th>S.N</th>
                        <th>Emp Id</th>
                        <th>Name</th>
                        <th>Iqama No</th>
                        <th>Salary Type</th>
                        <th>Total Expense</th>
                        <th>Duration</th>
                        <th>Expiration Date</th>
                        <th>Status</th>
                    </tr>` ;

            $('#excell_file_upload_emp_list_table_head').html(rows);

            rows = "";
            $.each(records, function (key, value) {
                rows += `
                    <tr>
                        <td>${counter++}</td>
                        <td>${value.employee_id}</td>
                        <td>${value.employee_name} </td>
                        <td>${value.akama_no}</td>
                        <td>${value.hourly_employee == 1 ? 'Hourly' : 'Basic Salary'} </td>
                        <td>${value.total_amount}</td>
                        <td>${value.duration} Months</td>
                        <td>${value.expire_date}</td>
                        <td>${value.upload_status}</td>
                    </tr> `;
            });
            $('#excell_file_upload_emp_list_table_body').html(rows);
        }
        else if(upload_file_type == 3){

            // sposnor udpate
            rows = `<tr>
                        <th>S.N</th>
                        <th>Emp Id</th>
                        <th>Name</th>
                        <th>Iqama No</th>
                        <th>Salary Type</th>
                        <th>Sponsor</th>
                        <th>Status</th>
                    </tr>`;

            $('#excell_file_upload_emp_list_table_head').html(rows);

            rows = "";

            $.each(records, function (key, value) {
                rows += `
                    <tr>
                        <td>${counter++}</td>
                        <td>${value.employee_id}</td>
                        <td>${value.employee_name} </td>
                        <td>${value.akama_no}</td>
                        <td>${value.hourly_employee == 1 ? 'Hourly' : 'Basic Salary'} </td>
                        <td>${value.sponsor_id}</td>
                        <td>${value.upload_status}</td>
                    </tr>
                    `
            });
            $('#excell_file_upload_emp_list_table_body').html(rows);

        }

    }
    function bindRecordNotFoundTableData(upload_file_type, records){

         var error_header_rows = "";
         var error_body_rows = "";
         var error_counter = 1;
        if(upload_file_type == 1){

            // iqama expiration date updat
            error_header_rows = `<tr>
                        <th>S.N</th>
                        <th>Emp Id</th>
                        <th>Name</th>
                        <th>Iqama No</th>
                        <th>Salary Type</th>
                        <th>Expire Date</th>
                        <th>Status</th>
                    </tr>` ;

                error_body_rows = "";
                $.each(records, function (key, value) {
                    error_body_rows += `
                        <tr>
                            <td>${error_counter++}</td>
                            <td>${value.employee_id}</td>
                            <td>${value.employee_name} </td>
                            <td>${value.akama_no}</td>
                            <td>${value.hourly_employee == 1 ? 'Hourly' : 'Basic Salary'} </td>
                            <td> ${value.iqama_expire_date}</td>
                            <td> ${value.upload_status}</td>
                        </tr>`;
                });
            if(error_counter >1){
                $('#data_not_found_title').text("Error Records");
                $('#excell_file_upload_error_emp_list_table_head').html(error_header_rows);
                $('#excell_file_upload_error_emp_list_table_body').html(error_body_rows);
            }


        }else if(upload_file_type == 2){
            // insurance expense insert
            error_header_rows =  `<tr>
                        <th>S.N</th>
                        <th>Emp Id</th>
                        <th>Name</th>
                        <th>Iqama No</th>
                        <th>Salary Type</th>
                        <th>Total Expense</th>
                        <th>Duration</th>
                        <th>Expiration Date</th>
                        <th>Status</th>
                    </tr>`

                error_body_rows = "";
                $.each(records, function (key, value) {
                    error_body_rows += `<tr>
                                        <td>${error_counter++}</td>
                                        <td>${value.employee_id}</td>
                                        <td>${value.employee_name} </td>
                                        <td>${value.akama_no}</td>
                                        <td>${value.hourly_employee == 1 ? 'Hourly' : 'Basic Salary'} </td>
                                        <td>${value.total_amount}</td>
                                        <td>${value.duration} Months</td>
                                        <td>${value.expire_date}</td>
                                        <td>${value.upload_status}</td>
                                    </tr>`
                });


            if(error_counter >1){
                $('#data_not_found_title').text("Error Records");
                $('#excell_file_upload_error_emp_list_table_head').html(error_header_rows);
                $('#excell_file_upload_error_emp_list_table_body').html(error_body_rows);
            }

        }
        else  if(upload_file_type == 3){
            // sponsor update
            error_header_rows = `<tr>
                        <th>S.N</th>
                        <th>Emp Id</th>
                        <th>Name</th>
                        <th>Iqama No</th>
                        <th>Salary Type</th>
                        <th>Sponsor</th>
                        <th>Status</th>
                    </tr>` ;

                error_body_rows = "";
                $.each(records, function (key, value) {
                    error_body_rows += `
                        <tr>
                            <td>${error_counter++}</td>
                            <td>${value.employee_id}</td>
                            <td>${value.employee_name} </td>
                            <td>${value.akama_no}</td>
                            <td>${value.hourly_employee == 1 ? 'Hourly' : 'Basic Salary'} </td>
                            <td> ${value.sponsor_id}</td>
                            <td> ${value.upload_status}</td>
                        </tr>`;
                });
            if(error_counter >1){
                $('#data_not_found_title').text("Error Records");
                $('#excell_file_upload_error_emp_list_table_head').html(error_header_rows);
                $('#excell_file_upload_error_emp_list_table_body').html(error_body_rows);
            }


        }

    }

</script>
@endsection
