@extends('layouts.admin-master')
@section('title')Search Iqama Expense @endsection
@section('content')

<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title"> Searching Iqama Renewal Expense </h4>
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
                    <!--<label class="col-md-3 control-label d-block" style="text-align: right; margin-right:5px;">Employee Searching By </label> -->
                    <div class="col-md-2">
                        <!--<select class="form-select" name="searchBy" id="searchBy" required>-->
                        <!--     <option value="akama_no">Iqama </option>-->
                        <!--    <option value="employee_id">Employee ID</option>-->
                        <!-- </select>-->
                    </div>

                    <div class="col-md-4">
                        <input type="text" placeholder="Enter Employee ID or Iqama Number" class="form-control"
                            id="empl_info" name="empl_info" 
                             required autofocus>
                        <span id="employee_not_found_error_show" class="d-none"
                            style="color: red"></span>
                        @if ($errors->has('empl_info'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('empl_info') }}</strong>
                        </span>
                        @endif
                    </div>
                    <div class="col-md-2">
                        <button type="submit" onclick="searchingEmployeeIqamaRenewalExpenseRecords()" class="btn btn-primary waves-effect">SEARCH</button>
                    </div>
                </div>
            </div>            
        </div>
    </div>
</div>
 

{{-- Iqama Renewal Expense Records --}}
<div class="row">
    <div class="col-lg-12">
        <div class="card">            
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <!-- <div class="col-1"></div> -->
                        <div class="table-responsive ">
                            <table id="alltableinfo" class="table table-bordered custom_table mb-0">
                                <thead>
                                    <tr>
                                        <th>S.N</th>
                                        <th>Emp.ID</th>
                                        <th>Name</th>
                                        <th>Jawazat Fee </th>
                                        <th>Mak.Amal Fee </th>
                                        <th>VISA Amount </th>
                                        <th>Medical Ins. </th>
                                        <th>Others </th>
                                        <th>Jaw. Penalty</th>
                                        <th>Total Exp.</th>
                                        <th>Duration</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Paid By</th>
                                        <th>Remarks</th>
                                        <th colspan="2">Manage</th>
                                    </tr>
                                </thead>
                                <tbody id="iqama_renewal_records">
                                </tbody>
                            </table>
                        </div>
                        <!-- <div class="col-1"></div> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">

    // Enter Key Press Event
    $('#empl_info').keydown(function(e) {
        if (e.keyCode == 13) {
            searchingEmployeeIqamaRenewalExpenseRecords();
        }
    })

    function searchingEmployeeIqamaRenewalExpenseRecords(){
    
        var searchType = "employee_id"; 
        var searchValue = $("#empl_info").val();

        if (searchValue.length == 10) {
            searchType = "akama_no";
        }
    
        if (searchValue.length == 0) {
            showSweetAlertMessage('error',"Please Enter Employee ID or Iqama Number"); 
            return ;
        }
    
       $.ajax({
            type: 'POST',
           url: "{{ route('iqama.renewal.expense.record.search.ajax') }}",
           data: {
               searchType: searchType,
               searchValue: searchValue
           },
           dataType: 'json',
           success: function(response) { 

              $("#iqama_renewal_records").html("");
                if(response.status != 200){
                    showSweetAlertMessage('error', response.message);
                    return;
                }
              var employee_id =   response.employee.employee_id;
              var employee_name =    response.employee.employee_name;
               var records = "";
               var counter = 0;
                
  
                 
                      $.each(response.data, function(key, value) {  
                           counter++;
                           var edit_url = "{{  url('admin/edit/iqama-anual/fee') }}/" + value.IqamaRenewId;
                           var renewal_status = value.renewal_status;                                               
                           records +=
                                   '<tr>'+
                                        '<td>'+counter+'</td>'+
                                       '<td>'+employee_id+'</td>'+
                                       '<td>'+employee_name+'</td>'+
                                       '<td>'+value.jawazat_fee+'</td>'+
                                       '<td>'+value.maktab_alamal_fee+'</td>' +
                                       '<td>'+value.bd_amount+'</td>' +
                                       '<td>'+value.medical_insurance+'</td>' +
                                       '<td>'+value.others_fee+'</td>' +
                                       '<td>'+value.jawazat_penalty+'</td>' +
                                       '<td>'+value.total_amount+'</td>' +
                                       '<td>'+value.duration+" Months"+'</td>' +
                                       '<td>'+value.renewal_date+'</td>' +
                                       '<td>'+ (value.approved_status == 0 ? 'Pending': 'Approved') +'</td>' +
                                       '<td>'+ (value.expense_paid_by == 1? 'Self':'Company')+'</td>'+
                                       '<td>'+value.remarks+'</td>';
   
                                       var edit_url = "{{  url('admin/edit/iqama-anual/fee') }}/" + value.IqamaRenewId;
                                       var delete_url = "#"; // "{{  url('admin/delete/iqama-anual/fee') }}/" + value.IqamaRenewId;
                                       // records += '<td>' + '<a href= "">Edit</a></td>';

                                       if(value.approved_status == 0) {
                                           @can('iqama-renewal-expense-edit')
                                           records += '<td>' + '<a  href=' + edit_url+'><i class="fa fa-pencil-square fa-lg edit_icon"></i></a></td>';
                                           @endcan

                                            @can('iqama_renewal_expense_delete')
                                              records += '<td>' + '<button type="button" class="btn btn-danger" onClick = "deleteIqamaExpenceRecord('+value.IqamaRenewId+')">Delete</button></td>';
                                            @endcan
                                           
                                       } else  if(value.approved_status == 1) {
                                           
                                           
                                           @can('iqama-renewal-expense-approval')
                                           records += '<td>' + '<a href=' + edit_url+'><i class="fa fa-pencil-square fa-lg edit_icon"></i></a></td>';
                                           @endcan
                                           
                                            @can('iqama_renewal_expense_delete')
                                              records += '<td>' + '<button type="button" class="btn btn-danger" onClick = "deleteIqamaExpenceRecord('+value.IqamaRenewId+')">Delete</button></td>';
                                            @endcan

                                        
                                       }
                                       
                               records += '</tr>';

                       });                     
                                         
                    $("#iqama_renewal_records").html(records);

                

               }
       });
   
    }


    function deleteIqamaExpenceRecord(IqamaRenewRecordAutoId){
        //  alert(IqamaRenewRecordAutoId);
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
                            url: "{{  url('admin/delete/iqama-anual/fee') }}/" +IqamaRenewRecordAutoId,
                        
                            dataType: 'json',
                            success: function (response) {
                                if(response.status == 200){
                                    showSweetAlertMessage('success', response.message);  
                                    window.location.reload();                              
                                }else {
                                    showSweetAlertMessage('error', response.message);
                                }                           
                            },
                            error:function(response){
                                showSweetAlertMessage('error', "Operation Failed, Please Try Again");
                            }
                        });


                    }
                });
    }
    
     // show message 
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

</script>
@endsection

 