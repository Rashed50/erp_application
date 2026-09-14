@extends('layouts.admin-master')
@section('title')Emp. Bank @endsection
@section('content')



<!-- Session Flash Message -->
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Employee Bank Details</h4>
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
            <div class="card-body card_form" style="padding-top:10px;">
               
                <div class="form-group row custom_form_group">
                     
                    <div class="col-md-3">
                        <input type="number" placeholder="Employee ID or Iqama" class="form-control"
                            id="empl_info" name="empl_info" value="{{ old('empl_info') }}"
                             required autofocus >
                       
                        @if ($errors->has('empl_info'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('empl_info') }}</strong>
                        </span>
                        @endif
                    </div>                    
                                     
                    <div class="col-md-9">
                        <div class="btn-toolbar" role="toolbar" aria-label="Toolbar with button groups">

                            <div class="btn-group" role="group" aria-label="First group">
                                <button type="submit" onclick="searchEmployeeBasicInformation()" class="btn btn-primary waves-effect">Emp. Search</button>
                            </div>
                            <div class="btn-group mr-2" role="group" aria-label="Second group">
                                <button type="submit" onclick="searchEmployeeBankInformation()" class="btn btn-primary waves-effect">Search Bank</button> 
                            </div>
                            <div class="btn-group mr-2" role="group" aria-label="Third group">                                
                                <button type="submit" onclick="showBankInfoExistEmployeeList()" class="btn btn-primary waves-effect">Emp. Bank Report</button> 
                            </div> 

                            <div class="btn-group mr-2" role="group" aria-label="Fourth group">
                                @can('update_emp_salary_payment_method')
                                 <button type="button" class="btn btn-primary waves-effect" data-toggle="modal" data-target="#employee_salary_payment_method_modal">Salary Method</button>
                                @endcan
                            </div>
                            <div class="btn-group mr-2" role="group" aria-label="Fourth group">
                                <button type="button" class="btn btn-primary waves-effect" data-toggle="modal" data-target="#new_bank_modal">Bank List</button>
                            </div>
                            

                        </div>         
                    </div>
                     
                </div>
            </div>            
        </div>
    </div>
</div>


{{-- Searching  Employee Information Details--}}
<div id="showEmployeeDetails" class="d-none" >
    <div class="row" id="showEmployeeDetails" class="d-none">
        <div class="col-md-12">
            <table   class="table table-bordered table-striped table-hover custom_view_table"
                id="showEmployeeDetailsTable">

                <tr>
                    <td> <span class="emp"> Name:</span> <span id="show_employee_name"
                            class="emp2"></span> </td>
                    <td> <span class="emp">Sponsor:</span> <span id="show_employee_sponsor_name"
                                class="emp2"></span> </td>
                </tr>
                <tr>
                    <td> <span class="emp">Iqama :</span> <span id="show_employee_akama_no"
                            class="emp2"></span> </td>
                    <td> <span class="emp">Status:</span>
                        <span  id="show_employee_status" class="emp2"  style="font-weight:bold;color:red;font-size:18px;"></span>
                    </td>
                </tr>
                <tr>
                    <td> <span class="emp">Project:</span> <span id="show_employee_project_name"
                            class="emp2"></span> </td>
                    <td> <span class="emp">Trade:</span> <span id="show_employee_trade"
                                class="emp2"></span> </td>
                </tr>
            </table>
        </div>       
    </div>
</div>
                    
 

{{-- Bank Information Form --}}
<div class="row">
    <div class="col-lg-12" >
    <div id="emp_bank_info_section" class="d-none">
            <form class="form-horizontal" id="bank_info_form" method="post" action="{{ route('employee.bank.details.insert.request') }}">
                @csrf
                <div class="card">
                    <div class="card-body card_form">
                        <div class="form-group row custom_form_group{{ $errors->has('emp_auto_id') ? ' has-error' : '' }}">
                            <div class="col-sm-5"></div>
                            <div class="col-sm-5">
                                <span class="req_star">Employee ID <span id="show_employee_id"
                                        class="req_star">Required</span>
                                </span>
                                <input type="hidden" class="form-control" id="emp_auto_id" name="emp_auto_id" value="" required>
                                <input type="hidden" class="form-control" id="ebd_auto_id" name="ebd_auto_id" value="">

                                @if ($errors->has('emp_auto_id'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('emp_auto_id') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="col-sm-2"></div>
                        </div> 
 
                            <div class="form-group row custom_form_group">
                                <label class="col-sm-4 control-label"> Bank Name:<span
                                        class="req_star">*</span></label>
                                <div class="col-sm-6">
                                    <select class="form-select" name="bank_name" id="bank_name" required>
                                        <option value="">Select Bank Name</option>
                                        @foreach($bank_names as $bn)
                                         <option value="{{$bn->bn_auto_id }}"> {{$bn->bn_name}}</option>
                                        @endforeach                                   
                                    </select>
                                </div>
                                 
                            </div>
                              
                             
                            <div class="form-group row custom_form_group">
                                <label class="col-sm-4 control-label">A/C Holder Name: <span class="start">*</span> </label>
                                <div class="col-sm-6">
                                    <input type="text" name="acc_holder_name" id="acc_holder_name" class="form-control" placeholder="Enter Account Holder Name" >
                                </div>
                            </div>

                            <div class="form-group row custom_form_group">
                                <label class="col-sm-4 control-label">Account Number:<span class="req_star">*</span></label>
                                <div class="col-sm-6">
                                    <input type="number" placeholder="Enter Account Number" class="form-control" id="account_number" step="1" required  name="account_number">
                                </div>
                            </div>
                       
                            <div class="form-group row custom_form_group">
                                <label class="col-sm-4 control-label">IBAN:<span class="req_star">*</span></label>
                                <div class="col-sm-6">
                                    <input type="text" placeholder="Enter IBAN Number" class="form-control" id="acc_iban" required
                                        name="acc_iban">
                                </div>
                            </div> 
                        <div class="form-group row custom_form_group{{ $errors->has('emp_id') ? ' has-error' : '' }}">
                            <label class="col-sm-4 control-label">Remarks:</label>
                            <div class="col-sm-6">
                                <input type="text" placeholder="Remarks Here" class="form-control" id="acc_remarks"
                                    name="acc_remarks">
                            </div>
                        </div>
                    </div>

                    <div class="card-footer card_footer_button text-center">
                         @can('emp_bank_info_update')
                            <button type="submit" id="save_button" name="save_button"   class="btn btn-primary waves-effect">SAVE </button>
                        @endcan
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- New Bank Name Add Modal-->
<div class="modal fade" id="new_bank_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Add and Update Bank Name</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
         <div class="modal-body">

            <div class="form-group row custom_form_group">
                <label class="col-md-3 control-label">Bank</label>
                <div class="col-md-9">
                    <select class="form-select" name="modal_bank_name" id="modal_bank_name" >
                        <option value="">Add New Bank</option>
                        @foreach($bank_names as $bn)
                         <option value="{{$bn->bn_auto_id }}"> {{$bn->bn_name}}</option>
                        @endforeach
                    </select>
                </div>

            </div>

            <div class="form-group row custom_form_group">
                <label class="col-md-3 control-label">New Name</label>
                <div class="col-md-9">
                    <input type="text" class="form-control" name="bn_name" id="bn_name" placeholder="Enter Bank Name" required>
                </div>
            </div>


        </div>
        
        
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
             @can('add_new_bank_name')
                        <button type="button" id="btn_bank_save" onclick="storeNewBankInformation()" class="btn btn-primary waves-effect">Save</button>
            @endcan
        </div>
      </div>
    </div>
</div>


<!--  Employee Salary Payment Method Update Modal-->
<div class="modal fade" id="employee_salary_payment_method_modal" tabindex="-1" role="dialog"   aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Salary Payment Method Update <span class="text-danger" id="errorData"></span></h5>               
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>   
            <form class="form-horizontal" id="salary_payment_method_form" method="post" action="{{ route('employee.salary.payment.method.update') }}">
                @csrf         
                <div class="modal-body">                
                         
                        <input type="hidden" id="operation_type" name="operation_type" value="2" required>
                          
                        <div class="form-group row custom_form_group">
                            <label class="col-md-4 control-label">Employee ID &nbsp;&nbsp;</label>
                             
                            <input type="text" class="form-control col-md-8" name="employee_id" id="employee_id" autofocus required>
                                @if ($errors->has('employee_id'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('employee_id') }}</strong>
                                </span>
                                @endif
                             
                        </div> 

                        <div class="form-group row custom_form_group">
                            <label class="col-md-4 control-label">Payment Method</label>
                            <div class="col-md-8">
                                <select class="form-select" name="payment_method" id="payment_method" required>                                    
                                    <option value="">Select One</option> 
                                    <option value="Bank">Bank</option> 
                                    <option value="Cash">Cash</option>                                               
                                </select>
                                @if ($errors->has('payment_method'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('payment_method') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div> 

                        </div>                   
                        <button type="submit" id="submit_button" name="submit_button"    class="btn btn-success">UPDATE</button>
                </div>                
            </form>
        </div>
    </div>
</div>
 

<script type="text/javascript">

    function showBankInfoExistEmployeeList(){
        var report_date = $('#report_date').val();               
        var url = "{{ route('employee.bank.list.of.employees.report', ':parameter') }}";
        url = url.replace(':parameter', report_date);
        window.open(url, '_blank');
    }
     
     
    // Enter Key Press Event Fire
    $('#empl_info').keydown(function(e) {
        if (e.keyCode == 13) {
            searchEmployeeBasicInformation();
        }
    })

    //   Single Employee Details Info
    function searchEmployeeBasicInformation() {
        
            $('#bank_info_form')[0].reset();
 
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
              // url:"{{route('employee.searching.searching-with-multitype.parameter') }}",

                data: {
                    search_by: searchType,
                    employee_searching_value: searchValue
                },
                dataType: 'json',
                success: function (response) {
                                     
                    if (response.success == false) {
                        $('input[id="emp_auto_id"]').val('');
                        $("span[id='show_employee_id']").text("is Required");
                        $("input[id='empl_info']").val(''); 
                         $("#showEmployeeDetails").addClass("d-none").removeClass("d-block");
                        // hide insert ui
                        $("#emp_bank_info_section").addClass("d-none").removeClass("d-block")
                        showSweetAlertMessage('error',"Employee Not Found");  
                        return ;
                    } 
                   
                    if (response.total_emp > 1) {                         
                         alert('Mone Than One Employee Found,Please Inform this issue to Software Engineer');
                    } else {
                        showSearchingEmployee(response.findEmployee[0]);
                    }
                }, // end of success
                error:function(response){                 
                    showSweetAlertMessage('error',"Operation Failed, Please try Again");  
                }
            }); // end of ajax calling
       
    }

    // End of Method for Router calling
    function showSearchingEmployee(findEmployee) {

                $("input[id='empl_info']").val('');
                 $("#showEmployeeDetails").removeClass("d-none").addClass("d-block");
                // display insert UI
                $("#emp_bank_info_section").addClass("d-block").removeClass("d-none")

               /* show employee information in employee table */
                $("span[id='show_employee_id']").text(findEmployee.employee_id);
                $("span[id='show_employee_name']").text(findEmployee.employee_name);
                $("#acc_holder_name").val(findEmployee.employee_name);
                $("span[id='show_employee_trade']").text(findEmployee.catg_name);            
                               
                $("span[id='show_employee_akama_no']").text((findEmployee.akama_no+", "+findEmployee.akama_expire_date+", Passport No: "+findEmployee.passfort_no));
                $('input[id="emp_auto_id"]').val(findEmployee.emp_auto_id);
                var job_status = findEmployee.title + (findEmployee.salary_status == 1 ? ', Salary: Active' : ", Salary: Hold");
                $("span[id='show_employee_status']").text(job_status);
                 
                $("span[id='show_employee_project_name']").text(findEmployee.proj_name);
                $("span[id='show_employee_sponsor_name']").text(findEmployee.spons_name);
               

    }

    $(document).ready(function () {
        
            $('#modal_bank_name').change(function()
            {
                var selectedValue = parseInt(jQuery(this).val());
                var ddl = document.getElementById("modal_bank_name");
                $('#bn_name').val(ddl.options[selectedValue].text);
            });
        
        
            //  bank info form validation
            $("#bank_info_form").validate({
                submitHandler: function (form) {
                   // alert(10);
                    return false;
                },
                rules: {
                    acc_holder_name: {
                        required: true,
                    },
                    account_number: {
                        required: true,
                    },
                    acc_iban: {
                        required: true,
                    },
                    bank_name: {
                        required: true,
                    },
                },
                messages: {
                    acc_holder_name: {
                        required: 'Account Holder Name is Required',
                    },
                    account_number: {
                        required:'Account Number is Required',
                    },
                    acc_iban: {
                        required: 'IBAN is Required',
                    },
                    bank_name: {
                        required: 'Account Holder Bank Name is Required',
                    },
                },
            }); 
            
            // new advance insertion form submit
            $("#bank_info_form").submit(function (e) {
        
                e.preventDefault();
                var form = $("#bank_info_form");
                var data =  new FormData($(this)[0]);  // if same name two form then o index
                var action = form.attr("action");
                if(form.valid() == false){
                    return;
                }
                document.getElementById("save_button").disabled = true;
                $.ajax({
                        url: action,
                        method: form.attr("method"),
                        data: data,
                        processData: false,
                        contentType: false,
                        beforeSend:function(){
                                
                        },
                })
                .done(function(response) { 
                           
                        document.getElementById("save_button").disabled = false;
                        if(response.status == 200){   
                             resetBanInformationForm();
                            $("#emp_bank_info_section").removeClass("d-block").addClass("d-none");  
                            $("#showEmployeeDetails").removeClass("d-block").addClass("d-none");                                                              
                            showSweetAlertMessage('success',response.message);
                        }else {
                            showSweetAlertMessage('error', response.message);
                        }                                             
                })
                .fail(function(xhr) {
                    showMessage('error',"Operation Failed, Please Try Aggain");
                    document.getElementById("save_button").disabled = false;
                });
            }); 
            
            //update employee salary payment method form submit
            $("#salary_payment_method_form").submit(function (e) {
        
                e.preventDefault();
                var form = $("#salary_payment_method_form");
                var data =  new FormData($(this)[0]);  // if same name two form then o index
                var action = form.attr("action");
                if(form.valid() == false){
                    return;
                }
                document.getElementById("submit_button").disabled = true;
                $.ajax({
                        url: action,
                        method: form.attr("method"),
                        data: data,
                        processData: false,
                        contentType: false,
                        beforeSend:function(){
                                
                        },
                })
                .done(function(response) {        
                        document.getElementById("submit_button").disabled = false;
                        
                        if(response.status == 200){   
                            $('#salary_payment_method_form')[0].reset();  
                            $('#employee_salary_payment_method_modal').modal('hide');                                                          
                            showSweetAlertMessage('success',response.message);
                        }else {
                            showSweetAlertMessage('error', response.message);
                        }                                             
                })
                .fail(function(xhr) {
                    showSweetAlertMessage('error',"Operation Failed, Please Try Aggain");
                    document.getElementById("submit_button").disabled = false;
                });
            });
    });

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
    
    

    // store new bank name
    function storeNewBankInformation(){
         
            var bn_name = $('#bn_name').val(); 
             var selected_bank = $('#modal_bank_name').val();
             
            if(bn_name == null || bn_name == ""){
                showSweetAlertMessage("error","Please Input Bank Name");
                return;
            } 
                             
            document.getElementById("btn_bank_save").disabled = true;
            $.ajax({
                type: 'POST',
                url: "{{ route('employee.bank.insert.new.bank') }}",
                data: {
                    bn_name:bn_name,
                    bank_auto_id:selected_bank,
                },
                dataType: 'json',
                success: function (response) {
                    
                    document.getElementById("btn_bank_save").disabled = false;
                    if(response.status == 200){   
                        $('#new_bank_modal').modal('hide'); 
                        
                        // showSweetAlertMessage('success',response.message);
                        // $('#bank_name').empty();
                        // $('#bank_name').append('<option value="">Select Bank Name</option>');
                        // $.each(response.data,function(i,obj)
                        // {                               
                        //     var div_data = "<option value="+obj.bn_auto_id +">"+obj.bn_name+"</option>";                            
                        //     $(div_data).appendTo('#bank_name'); 
                        // });  
                        
                        showSweetAlertMessage('success',response.message);
                        $('#bank_name').empty();
                        $('#bank_name').append('<option value="">Select Bank Name</option>');

                        $('#modal_bank_name').empty();
                        $('#modal_bank_name').append('<option value="">Add New Bank</option>');

                        $.each(response.data,function(i,obj)
                        {
                            var div_data = "<option value="+obj.bn_auto_id +">"+obj.bn_name+"</option>";
                            $(div_data).appendTo('#bank_name');
                            $(div_data).appendTo('#modal_bank_name');
                        });


                    }else {
                        showSweetAlertMessage('error', response.message);
                    }      
                   
                }, // end of success
                error:function(response){                 
                    showSweetAlertMessage('error',"Operation Failed, Please try Again");  
                    document.getElementById("btn_bank_save").disabled = false;
                }
            }); // end of ajax calling

    }

    function searchEmployeeBankInformation(){
        
             $('#bank_info_form')[0].reset();

            var employee_id = $("#empl_info").val();                    
            if ($("#empl_info").val().length == 0 || $("#empl_info").val().length == 10) {
                showSweetAlertMessage('error',"Please Enter Employee ID"); 
                return ;
            } 
             resetBanInformationForm();
            $.ajax({
                type: 'GET',
                url: "{{ route('employee.bank.details.searching') }}",
                data: {
                   employee_id: employee_id, 
                },
                dataType: 'json',
                success: function (response) {
                    if (response.status != 200) {  
                        $("#showEmployeeDetails").addClass("d-none").removeClass("d-block");
                        $("#emp_bank_info_section").addClass("d-none").removeClass("d-block")
                        showSweetAlertMessage('error',"Employee Not Found");  
                        return;
                    }                   
                    var record = response.data;
                    $('#ebd_auto_id').val(record.ebd_auto_id);
                    $('#emp_auto_id').val(record.emp_auto_id);
                    $('#acc_holder_name').val(record.acc_holder_name);
                    $('#account_number').val(record.acc_number);
                    $('#acc_iban').val(record.acc_iban);
                    $('#bank_name').val(record.bank_id);
                    $('#acc_remarks').val(record.remarks);                   

                  $("#showEmployeeDetails").addClass("d-none").removeClass("d-block"); 
                  $("#emp_bank_info_section").addClass("d-block").removeClass("d-none")


                }, // end of success
                error:function(response){                 
                    showSweetAlertMessage('error',"Operation Failed, Please try Again");  
                }
            }); // end of ajax calling
    }

    $('#employee_salary_payment_method_modal').on('shown.bs.modal', function () {
        $('#employee_id').focus();
    })
    
    
    
    // reset employee bank information form
    function resetBanInformationForm(){

            $('#ebd_auto_id').val('');
            $('#emp_auto_id').val('');
            $('#acc_holder_name').val('');
            $('#account_number').val('');
            $('#acc_iban').val('');
            $('#bank_name').val('');
            $('#acc_remarks').val('');

    }



</script>
@endsection
