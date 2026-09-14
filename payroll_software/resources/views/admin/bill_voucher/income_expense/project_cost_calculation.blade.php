@extends('layouts.admin-master')
@section('title')Statement
@section('content')

<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active">Income/Expense Summary </li>
        </ol>
    </div>
</div>

 <!-- Session Flash Message -->
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

<div class="row">
    <div class="card-header">                
                <div class="form-group row custom_form_group{{ $errors->has('project_id') ? ' has-error' : '' }}">
                    <label class="col-sm-1 control-label">Year</label>
                    <div class="col-sm-2">
                        <select class="form-select" name="year" id="year" required>
                            <option value="2023">2023</option>  
                            <option value="2022">2022</option>                            
                        </select>
                    </div>
                    <label class="col-sm-1 control-label">Month</label>
                    <div class="col-sm-2">
                        <select class="form-select" name="month" id = "month" required>
                            <option value="1" {{ 1 == Carbon\Carbon::now()->format('m') ? 'selected' :'' }}>January</option>  
                            <option value="2" {{ 2 == Carbon\Carbon::now()->format('m') ? 'selected' :'' }}>February</option>  
                            <option value="3" {{ 3 == Carbon\Carbon::now()->format('m') ? 'selected' :'' }}>March</option>  
                            <option value="4" {{ 4 == Carbon\Carbon::now()->format('m') ? 'selected' :'' }}>April</option>  
                            <option value="5" {{ 5 == Carbon\Carbon::now()->format('m') ? 'selected' :'' }}>May</option>  
                            <option value="6" {{ 6 == Carbon\Carbon::now()->format('m') ? 'selected' :'' }}>June</option>  
                            <option value="7" {{ 7 == Carbon\Carbon::now()->format('m') ? 'selected' :'' }}>July</option>  
                            <option value="8" {{ 8 == Carbon\Carbon::now()->format('m') ? 'selected' :'' }}>August</option>  
                            <option value="9" {{ 9 == Carbon\Carbon::now()->format('m') ? 'selected' :'' }}>September</option>  
                            <option value="10" {{ 10 == Carbon\Carbon::now()->format('m') ? 'selected' :'' }}>October</option>  
                            <option value="11" {{ 11 == Carbon\Carbon::now()->format('m') ? 'selected' :'' }}>November</option>  
                            <option value="12" {{ 12 == Carbon\Carbon::now()->format('m') ? 'selected' :'' }}>December</option> 

                        </select>
                    </div> 
                    <label class="col-sm-1 control-label">Project</label>
                    <div class="col-sm-4">
                        <select class="form-select" name="project_id" id="project_id" required>
                            <option value="">Select Project</option>
                            @foreach($projects as $proj)
                            <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('project_id'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('project_id') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>             
       
    </div>

    <!-- Double column Bill voucher form part end -->
    <div class="card-body card_form d-none" id="income_expense_statement_form">   
        <form class="form-horizontal" id="work_and_invoice_form" target="blank" method="POST"  action="{{ route('aproject.salary.andinvoic.statement.report')}}"   >
            @csrf     
        <div class="row">
            
                <!-- Project Salary Info Colum -->
                <div class="col-md-6">

                    <input type="hidden"   name="select_project_id" id="select_project_id">
                    <input type="hidden" name="select_month" id="select_month">
                    <input type="hidden" name="select_year" id="select_year">    

                    <div class="form-group row custom_form_group{{ $errors->has('total_employee') ? ' has-error' : '' }}">
                        <div class="col-sm-4" style="text-align: right;">
                            <label class="control-label">Total Employees<span
                                    class="req_star">*</span></label>
                        </div>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="total_employee" id="total_employee"
                                placeholder="Total Employee"   required>
                            @if ($errors->has('total_employee'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('total_employee') }}</strong>
                            </span>
                            @endif
                        </div>
                        <div class="col-sm-2"></div>
                    </div>

                    <div class="form-group row custom_form_group{{ $errors->has('total_with_vat') ? ' has-error' : '' }}">
                        <div class="col-sm-4" style="text-align: right;">
                            <label class="control-label">Total Man Hours<span class="req_star">*</span></label>
                        </div>
                        <div class="col-sm-6">
                            <input type="number" class="form-control" name="total_man_hours"
                                id="total_man_hours" placeholder="Total Man Hours" value=""
                                required>
                            @if ($errors->has('total_man_hours'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('total_man_hours') }}</strong>
                            </span>
                            @endif
                        </div>
                        <div class="col-sm-2"></div>
                    </div>

                    <div class="form-group row custom_form_group{{ $errors->has('total_with_vat') ? ' has-error' : '' }}">
                        <div class="col-sm-4" style="text-align: right;">
                            <label class="control-label">Total Salary<span class="req_star">*</span></label>
                        </div>
                        <div class="col-sm-6">
                            <input type="number" class="form-control" name="total_salary"
                                id="total_salary" placeholder="Total Salary" value=""
                                required>
                            @if ($errors->has('total_salary'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('total_salary') }}</strong>
                            </span>
                            @endif
                        </div>
                        <div class="col-sm-2"></div>
                    </div>

                    <div class="form-group row custom_form_group{{ $errors->has('total_with_vat') ? ' has-error' : '' }}">
                        <div class="col-sm-4" style="text-align: right;">
                            <label class="control-label">Per Hours <span class="req_star">*</span></label>
                        </div>
                        <div class="col-sm-6">
                            <input type="number" class="form-control" name="hourly_rate"
                                id="hourly_rate" placeholder="Per Hour " value="" step= 0.01  required>
                        </div>
                        <div class="col-sm-2"></div>
                    </div>
                    

                    <div class="form-group row custom_form_group{{ $errors->has('invoice_status') ? ' has-error' : '' }}">
                        <label class="col-sm-4 control-label">Remarks</label>
                        <div class="col-sm-7">
                            <textarea style="height:100px; resize:none" name="remarks" class="form-control"
                                placeholder="Remarks">{{ old('remarks') }}</textarea>
                        </div>
                    </div>
    
                    
                </div>
                <!-- Invoice Information column  -->
                <div class="col-md-6">

                        <div class="form-group row custom_form_group{{ $errors->has('contract_no') ? ' has-error' : '' }}">
                                
                            <label class="col-sm-4 control-label">Contract No: </label>
                            <div class="col-sm-6">
                                <input type="text" class="custom-form-control form-control" id="contract_no" name="contract_no" placeholder="Contract No">                                 
                            </div>                           
                        </div>

                        <div class="form-group row custom_form_group{{ $errors->has('count_of_invoice') ? ' has-error' : '' }}">

                            <label class="col-sm-4 control-label">Invoice Count: </label>
                            <div class="col-sm-3">
                                <input type="text" class="custom-form-control form-control" id="count_of_invoice" name="count_of_invoice" placeholder="Number of Inovice">                                 
                            </div>                           
                        </div>
                        <div class="form-group row custom_form_group{{ $errors->has('invoice_number') ? ' has-error' : '' }}">
                            <label class="col-sm-4 control-label">Invoice Number:</label>
                            <div class="col-sm-6">
                                <input type="text" class="custom-form-control form-control" id="invoice_number" name="invoice_number" placeholder="Invoice Number">                                 

                            </div>
                        </div>

                        <div class="form-group row custom_form_group{{ $errors->has('grand_total_amount') ? ' has-error' : '' }}">
                            <label class="col-sm-4 control-label">Total Amount (Excl. VAT)<span
                                    class="req_star">*</span></label>
                            <div class="col-sm-6">
                                <input type="text" class="custom-form-control form-control" id="grand_total_amount" name="grand_total_amount"
                                    value="" placeholder="Total(Excluding VAT)">
                                @if ($errors->has('grand_total_amount'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('grand_total_amount') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="col-sm-2"></div>
                        </div>

                        <div class="form-group row custom_form_group{{ $errors->has('total_vat') ? ' has-error' : '' }}">
                            <label class="col-sm-4 control-label">VAT<span
                                class="req_star">*</span></label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control" name="total_vat" id="total_vat"
                                    value="0">
                                @if ($errors->has('total_vat'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('total_vat') }}</strong>
                                </span>
                                @endif
                            </div>
                            <label class="col-sm-2 control-label">% Of</label>
                            <div class="col-sm-2">
                                <input type="number" class="form-control" name="vat" id="vat" value="15">
                                @if ($errors->has('vat'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('vat') }}</strong>
                                </span>
                                @endif
                            </div>

                        </div>
                        <div class="form-group row custom_form_group{{ $errors->has('retention') ? ' has-error' : '' }}">

                            <label class="col-sm-4 control-label">Retention<span
                                    class="req_star">*</span></label>

                            <div class="col-sm-2">
                                <input type="text" class="form-control" name="total_retention"
                                    id="total_retention" value="0"  >
                                @if ($errors->has('total_retention'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('total_retention') }}</strong>
                                </span>
                                @endif
                            </div>
                            <label class="col-sm-2 control-label"> % Of:
                                    </label>
                            <div class="col-sm-2">
                                <input type="number" class="form-control" name="retention" id="retention"
                                    value="10">
                                @if ($errors->has('retention'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('retention') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="col-sm-2"></div>
                        </div>

                        <div class="form-group row custom_form_group{{ $errors->has('total_with_vat') ? ' has-error' : '' }}">
                            <div class="col-sm-4" style="text-align: right;">
                                <label class="control-label">Total(Inc. VAT)<span class="req_star">*</span></label>
                            </div>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="total_with_vat"
                                    id="total_with_vat" placeholder="Total Amount Included VAT" value=""
                                    required>
                                @if ($errors->has('total_with_vat'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('total_with_vat') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="col-sm-2"></div>
                        </div>

                        <div class="form-group row custom_form_group{{ $errors->has('total_with_vat_excl_ret') ? ' has-error' : '' }}">
                            <div class="col-sm-4" style="text-align: right;">
                                <label class="control-label">Total(Incl. VAT & Excl. Retension):<span
                                        class="req_star">*</span></label>
                            </div>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="total_with_vat_excl_ret" id="total_with_vat_excl_ret"
                                    placeholder="Total Amount Incl. VAT and Excl. Retention" value=""
                                    required>
                                @if ($errors->has('total_with_vat_excl_ret'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('total_with_vat_excl_ret') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="col-sm-2"></div>
                        </div>
                    
                        <div class="form-group row custom_form_group{{ $errors->has('invoice_status') ? ' has-error' : '' }}">
                            <label class="col-sm-4 control-label">Remarks</label>
                            <div class="col-sm-6">
                                <textarea style="height:100px; resize:none" name="remarks" class="form-control"
                                    placeholder="Remarks">{{ old('remarks') }}</textarea>
                            </div>
                        </div>
                        <!-- Process Button -->
                        <div class="form-group row custom_form_group">
                            <div class="col-sm-7">                        
                                <button type="submit" id ="process_button"   class="btn btn-primary waves-effect"    style="border-radius: 15px;
                                width: 150px; height: 40px; letter-spacing: 1px;">PROCESS</button>
                            </div>
                        </div>                
                </div>
        
        </div>    </form>
    </div>
    <!-- Double column Bill voucher form part end -->
    
</div>


<script>

    $(document).ready(function(){

       $('select[name="project_id"]').on('change',function(){
            var month = $("#month").val();
            var year = $("#year").val();
            var project_id = $("#project_id").val();
            getProjectWorkAndInvoiceSaummary(project_id,month,year);
        });

        $('select[name="month"]').on('change',function(){
            var month = $("#month").val();
            var year = $("#year").val();
            var project_id = $("#project_id").val();
            getProjectWorkAndInvoiceSaummary(project_id,month,year);
        });

        $('select[name="year"]').on('change',function(){
            var month = $("#month").val();
            var year = $("#year").val();
            var project_id = $("#project_id").val();
            getProjectWorkAndInvoiceSaummary(project_id,month,year);
        });

    });

    function showMessage(message,operationType){
         //  start message
         const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            })
           
                Toast.fire({
                    type: operationType,
                    title: message
                })
            
    }

    function getProjectWorkAndInvoiceSaummary(project_id,month,year){

        if(project_id == null || project_id == ''){
            showMessage('Please Select Project Name', 'error');
            return;
        }

        $.ajax({
            type:"POST",
            url:"{{route('aproject.total_salary.andinvoice.summary')}}",    
            dataType:"json",
            data:{
                month:month,
                year:year,
                project_id:project_id,
            },
            success:function(response){
                    $('#select_project_id').val(project_id);
                    $('#select_month').val(month);
                    $('#select_year').val(year);                     
                      
                    if(response.status == 200){
                     

                        var total_employee =  response.work_summary.total_emp == null ? 0 : parseFloat(response.work_summary.total_emp);
                        var total_hours =  response.work_summary.total_hour == null ? 0 : parseFloat(response.work_summary.total_hour);
                        var total_overtime =  response.work_summary.total_overtime == null ? 0 : parseFloat(response.work_summary.total_overtime);
                        var total_salary =  response.work_summary.total_salary == null ? 0 : parseFloat(response.work_summary.total_salary);
                        var total_hours = total_hours + total_overtime;
                        var hourly_rate = (total_salary / total_hours).toFixed(2);
                        
                        $('#total_employee').val(total_employee);
                        $('#total_man_hours').val((total_hours));
                        $('#total_salary').val((Math.round(total_salary)));
                        $('#hourly_rate').val(hourly_rate);
                        $("#income_expense_statement_form").removeClass("d-none").addClass("d-block");

                 //  alert(hourly_rate);
                        var count_of_invoice =  response.invoice_summary.count_of_invoice == null ? 0 : response.invoice_summary.count_of_invoice;
                        var grand_total_amount =  response.invoice_summary.grand_total_amount == null ? 0 : response.invoice_summary.grand_total_amount;
                        var total_vat =  response.invoice_summary.total_vat == null ? 0 : parseFloat(response.invoice_summary.total_vat).toFixed(2);
                        var vat = Math.round((total_vat*100) / grand_total_amount);
                        var total_retention =  response.invoice_summary.total_retention == null ? 0 : parseFloat(response.invoice_summary.total_retention).toFixed(2);  
                        var retention = Math.round((total_retention*100)/grand_total_amount);                 
                        var total_receivable_amount =  response.invoice_summary.total_receivable_amount == null ? 0 : response.invoice_summary.total_receivable_amount;
                        
                        var total_with_vat = (parseFloat(grand_total_amount)+parseFloat(total_vat)).toFixed(2);
                        var total_with_vat_excl_ret = (parseFloat(grand_total_amount) + parseFloat(total_vat) - parseFloat(total_retention)).toFixed(2);
                        

                        $('#count_of_invoice').val(count_of_invoice);
                        $('#grand_total_amount').val(grand_total_amount);
                        $('#total_vat').val(total_vat);
                        $('#vat').val(vat);
                        $('#total_retention').val(total_retention);
                        $('#retention').val(retention);
                        $('#total_with_vat').val((total_with_vat));
                        $('#total_with_vat_excl_ret').val(total_with_vat_excl_ret);
                        
                    }else {
                        showMessage(response.message,'error');
                    } 
                       
                },
            error:function(reponse){
                    showMessage("Operation Failed, Please Try Aggain",'error');
            }
        
            });

        
    }


    function processForPrintWorkAndInvoiceStatementSummary(){
 
    }


    

</script>
@endsection