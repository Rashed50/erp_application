@extends('layouts.admin-master')
@section('title')Iqama Expense @endsection
@section('internal-css')
@section('content')

<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title" style="font-weight:bold; color:red"> Total {{$no_of_records}} Records Waiting for Approval</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active">Iqama Renewal Approval</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-7">
        @if(Session::has('success'))
        <div class="alert alert-success alertsuccess" role="alert">
            <strong>{{Session::get('success')}}</strong>
        </div>
        @endif
        @if(Session::has('error'))
        <div class="alert alert-warning alerterror" role="alert">
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
                <div  class="form-group row custom_form_group{{ $errors->has('searchBy') ? ' has-error' : '' }}">
                    <label class="col-md-2 control-label d-block" style="text-align: right; margin-right:5px;">Employee
                    </label>
                    <div class="col-md-4">
                        <select class="form-select" name="searchBy" id="searchBy" required>
                            <option value="employee_id">Searching By Employee ID</option>
                            {{-- <option value="akama_no">Searching By Iqama </option> --}}
                         </select>
                    </div>

                    <div class="col-md-3">
                        <input type="text" placeholder="Enter ID/Iqama/Passport No" class="form-control"
                            id="empl_info" name="empl_info" value="" required autofocus>
                        <span id="employee_not_found_error_show" class="d-none"
                            style="color: red"></span>
                        @if ($errors->has('empl_info'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('empl_info') }}</strong>
                        </span>
                        @endif
                    </div>
                    <div class="col-md-2">
                        <button type="submit" onclick="searchingEmployeeIqamaRenewalExpenseApprovalPendingRecords()" class="btn btn-primary waves-effect">SEARCH</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
             <form id="employee-salary-payment-form" action="{{ route('pending-iqamarenewal-fee-multiemp.approved') }}" onsubmit="this.querySelector('button[type=submit]').disabled = true;" method="post">
                @csrf
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-2">
                        </div>
                        <div class="col-md-2">
                              <button type="button" id="select_button" onclick="checkUnCheckAllEmployees()" class="btn btn-primary waves-effect" disabled >Check/UnCheck</button>
                        </div>
                        <div class="col-md-2">
                            <label class="control-label" id="lbl_total_selected_counter"> </label>
                        </div>
                        <div class="col-md-2">
                            <label class="control-label" id="lbl_total_selected_salary"> </label>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary waves-effect" id="multi_update_button" disabled >Update </button>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table id="alltableinfo" class="table table-bordered custom_table mb-0">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>ID</th>
                                            <th>Employee</th>
                                            <th>Iqama</th>
                                            <th>Trade <br>Salary</th>
                                            <th>Inserted By</th>
                                            <th>Jawazat</th>
                                            <th>Mak.Aml</th>
                                            <th>VISA <br>Insur. </th>
                                            <th>J.Penalty <br>Others</th>
                                            <th>Total</th>
                                            <th>Duration</th>
                                            <th>Date</th>
                                            <th>Paid By</th>
                                            <th>Remarks</th>
                                            <th colspan="2">Action</th>


                                        </tr>
                                    </thead>
                                    <tbody id ="iqama_expense_approval_pending_records">
                                        {{-- @foreach($records as $item)
                                        <tr>
                                            <td>{{ $item->employee_id }}</td>
                                            <td>{{Str::words($item->employee_name,3)}}</td>
                                            <td>{{ $item->akama_no }}</td>
                                            <td>{{$item->hourly_employee == 1? 'Hourly':'Basic'}}</td>
                                            <td>{{$item->catg_name}}</td>
                                            <td>{{$item->inserted_by}}</td>
                                            <td>{{ $item->jawazat_fee }}</td>
                                            <td>{{ $item->maktab_alamal_fee }}</td>
                                            <td>{{ $item->bd_amount }}</td>
                                            <td>{{ $item->medical_insurance }}</td>
                                            <td>{{ $item->others_fee }}</td>
                                            <td>{{ $item->jawazat_penalty }}</td>
                                            <td>{{ $item->total_amount }}</td>
                                            <td>{{ $item->duration }} Months</td>
                                            <td>{{ $item->renewal_date}} , {{ $item->iqama_expire_date}}</td>
                                            <td>{{ $item->remarks }}</td>
                                            <td>
                                                <a href="{{ route('edit-iqamarenewal-fee',$item->IqamaRenewId ) }}" title="edit" class="edit_button"><i class="fa fa-edit"></i></a>
                                            </td>
                                            <td>
                                                <a href="#"
                                                onClick="approvalOfIqamaRenewalExpenceRecord('{{ $item->IqamaRenewId }}')"
                                                title="Approve Here"><i id="" class="fa fa-thumbs-up"></i></a> </td>
                                            <td>
                                                <a href="#"
                                                onClick="deleteIqamaExpenceRecord('{{ $item->IqamaRenewId }}')"
                                                title="edit"><i id="" class="fa fa-trash fa-lg delete_icon"></i></a>
                                                <a href="{{ route('pending-iqamarenewal-fee-view',$item->IqamaRenewId) }}" title="View" target="_blank" class="view_button"><i class="fa fa-eye"></i></a>

                                            </td>

                                        </tr>
                                        @endforeach --}}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


@endsection

@section('script')
<script type="text/javascript">

    $('#empl_info').keydown(function (e) {
        if (e.keyCode == 13) {
            searchingEmployeeIqamaRenewalExpenseApprovalPendingRecords();
        }

    })

    function searchingEmployeeIqamaRenewalExpenseApprovalPendingRecords(){

        var searchType = $('#searchBy').find(":selected").val();
        var searchValue = $("#empl_info").val();

        $("#iqama_expense_approval_pending_records").html('');
        $.ajax({
                type: 'GET',
                url: "{{ route('anemp.iqama.renewal.expense.aproval.pending.records') }}",
                data: {
                    searchType: searchType,
                    searchValue: searchValue
                },
                dataType: 'json',
                success: function(response) {

                $("#iqama_expense_approval_pending_records").html("");

                        if(response.status != 200 || response.data.length == 0){
                            showSweetAlertMessage('error', 'Data Not Found');
                            return;
                        }
                       // $("#multi_update_button").removeAttr("disabled");
                      //  document.getElementById("multi_update_button").disabled = false;
                        document.getElementById("select_button").disabled = false;


                        var records = "";
                        var counter = 0;

                        $.each(response.data, function(key, value) {
                        counter++;
                        records +=
                                '<tr>'+
                                    '<td style="color:white">'+value.IqamaRenewId+'</td>'+
                                    '<td>'+value.employee_id+'</td>'+
                                    '<td>'+value.employee_name+'</td>'+
                                    '<td>'+value.akama_no + ', '+ value.iqama_expire_date+ '</td>'+
                                    '<td>'+value.catg_name+',' + (value.hourly_employee == 1? 'Hourly':'Basic')+'</td>'+
                                //  '<td>'+value.catg_name+'</td>'+
                                    '<td>'+value.inserted_by+'</td>'+
                                    '<td>'+value.jawazat_fee+'</td>'+
                                    '<td>'+value.maktab_alamal_fee+'</td>' +
                                    '<td>'+value.bd_amount+','+ value.medical_insurance +'</td>' +
                                    //'<td>'+value.medical_insurance+'</td>' +
                                    '<td>'+value.jawazat_penalty+','+value.others_fee+'</td>' +
                                // '<td>'+value.jawazat_penalty+'</td>' +
                                    '<td>'+value.total_amount+'</td>' +
                                    '<td>'+value.duration+" Months"+'</td>' +
                                    '<td>'+value.renewal_date+'</td>' +
                                    '<td>'+ (value.expense_paid_by == 1? 'Self':'Company')+'</td>'+
                                    '<td>'+value.remarks+'</td>';

                                    records += '<td>' + '<input type="hidden" id="iqama_renewal_auto_id'+value.IqamaRenewId+'" name="iqama_renewal_auto_id[]" value="'+value.IqamaRenewId+'">';
                                    records +=  '<input type="checkbox" onClick="countSelectedCheckBox('+value.IqamaRenewId+')"     id="iqama_renewal_checkbox-'+value.IqamaRenewId+'"  name="iqama_renewal_checkbox-'+value.IqamaRenewId+'" value="'+value.IqamaRenewId+'">';
                                    records +=  '&nbsp;||<a href="#" onClick="approvalOfIqamaRenewalExpenceRecord('+value.IqamaRenewId+')" ><i class="fas fa-thumbs-up fa-sm"></i></a></td>';

                                    var edit_url = "{{  url('admin/edit/iqama-anual/fee') }}/" + value.IqamaRenewId;
                                    records += '<td>' + '<a  href=' + edit_url+'><i class="fa fa-pencil-square fa-sm edit_icon"></i></a>';
                                    records += '&nbsp;||<a href="#"  onClick = "deleteIqamaExpenceRecord('+value.IqamaRenewId+')" ><i class="fa fa-trash fa-sm delete_icon"></i></a></td>';


                                records += '</tr>';
                        });

                        $("#iqama_expense_approval_pending_records").html(records);
                }
        });

    }

    function approvalOfIqamaRenewalExpenceRecord(IqamaRenewRecordAutoId){


    swal({
        title: "Do you want to Approve?",
        text: "",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    })
    .then((willDelete) => {
        if (willDelete) {

                $.ajax({
                    type: 'GET',
                    url: "{{  url('admin/iqama-anual/fee-approved/for-employee_id') }}/" + IqamaRenewRecordAutoId,
                    dataType: 'json',
                    success: function (response) {
                        if(response.status == 200){
                            showSweetAlertMessage('success', response.message);
                            searchingEmployeeIqamaRenewalExpenseApprovalPendingRecords();

                        }else {
                            showSweetAlertMessage('error', response.message);
                        }
                    },
                    error:function(response){
                        showSweetAlertMessage('error', "Network Failed, Please Try Again");
                    }
                });


        }
    });
    }

    function deleteIqamaExpenceRecord(IqamaRenewRecordAutoId){


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
                                    searchingEmployeeIqamaRenewalExpenseApprovalPendingRecords();

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

            // Check uncheck
    function checkUnCheckAllEmployees(){
        debugger;

        let myTable = document.getElementById('iqama_expense_approval_pending_records');
        var total_selected = 0;
            for (let row of myTable.rows) {
                allCell = row.cells;
                var chkboxId = "iqama_renewal_checkbox-" + allCell[0].innerText;
                document.getElementById(chkboxId).checked = !(document.getElementById(chkboxId).checked) ;
                if(document.getElementById(chkboxId).checked){
                    total_selected += 1;
                }
            }
        document.getElementById("lbl_total_selected_counter").innerText = "Total Selected: "+total_selected;
        if(total_selected > 0){
            document.getElementById("multi_update_button").disabled = false; // activate update button if any checkbox is selected
        }else {
            document.getElementById("multi_update_button").disabled = true; // disable update button if no checkbox is selected
        }
    }

    // checkbox click event
    function countSelectedCheckBox(value){

        let myTable = document.getElementById('iqama_expense_approval_pending_records');
        var total_selected = 0;
            for (let row of myTable.rows) {
                allCell = row.cells;
                var chkboxId = "iqama_renewal_checkbox-" + allCell[0].innerText;
                if(document.getElementById(chkboxId).checked){
                     total_selected += 1;
                }
            }

        document.getElementById("lbl_total_selected_counter").innerText = "Total Selected: "+total_selected;
        if(total_selected > 0){
            document.getElementById("multi_update_button").disabled = false; // activate update button if any checkbox is selected
        }else {
            document.getElementById("multi_update_button").disabled = true; // disable update button if no checkbox is selected
        }

    }


</script>
@endsection
