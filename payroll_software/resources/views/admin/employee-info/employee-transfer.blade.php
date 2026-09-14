@extends('layouts.admin-master')
@section('title') Emp. Transfer @endsection
@section('content')

<style>
    .overlay {
        display: none;
        position: fixed;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        z-index: 999;
        background: rgba(255, 255, 255, 0.8) url('{{ asset("animation/Loading.gif")}}') center no-repeat;
    }

    /* Turn off scrollbar when body element has the loading class */
    body.loading {
        overflow: hidden;
    }

    /* Make spinner image visible when body element has the loading class */
    body.loading .overlay {
        display: block;
    }
</style>

<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Mutiple Employee Transfer</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active">Employee Transfer</li>
        </ol>
    </div>
</div>

{{-- <div class="row">
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
</div> --}}

<!-- Employee Search UI !-->
<div class="row"> 
    <div class="col-md-12">
        <div class="card">
            <div class="card-body card_form"> 
                <div class="form-group row custom_form_group">
                    <label class="col-sm-2 control-label">Project</label>
                    <div class="col-sm-3">
                        <select class="form-select" name="proj_name">
                            <option value="">Select Project</option>
                            @foreach($projects as $proj)
                            <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <label class="col-sm-2 control-label">OR Emp. ID</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control"  placeholder="Multiple Employee ID Here" id="multi_emp_id" name="multi_emp_id"  autofocus >
                    </div>
                    <div class="col-sm-1">
                        <button type="submit" onclick="searchProjectWiseEmployeeList()" class="btn btn-primary waves-effect">Search</button>
                    </div>
                </div>

            </div>

        </div>
    </div>
    
</div>

<!-- Employee List Table !-->
<div class="row d-none" id="empl_display_tranfer_section">
    <div class="col-lg-12">
        <div class="card">
            <form id="employee-entry-in-form" action="{{ route('employee.transfer-form-submit') }}" method="post">
                @csrf
                <div class="card-header">
                    <div class="row">
                      
                        <div class="form-group row custom_form_group">
                            <label class="col-sm-2 control-label">Assign Project</label>
                            <div class="col-sm-2">
                                <select class="form-select" name="assigned_project" required>
                                    <option value="">Select Project</option>
                                    @foreach($projects as $proj)
                                    <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <label class="col-sm-1 control-label">Date</label>
                            <div class="col-sm-2">
                                <input type="date" name="asign_date" value="<?= date(" Y-m-d") ?>" class="form-control" required>
                            </div>
                           
                            <label class="col-sm-1 control-label" >Remarks </label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control" id="remarks" name="remarks">
                             </div>
                            <div class="col-sm-1">
                                <button type="submit" class="btn btn-primary waves-effect">Transfer</button>
                            </div>
                            
                            <div class="col-sm-1"> 
                                <button type="button" id="select_button"  onclick="checkUnCheckAllEmployee()" class="btn btn-primary waves-effect" >Select All</button>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table id="alltableinfo" class="table table-bordered table-hover custom_table mb-0">
                                    <thead>
                                        <tr>
                                            <th>S.N</th>
                                            <th>ID</th>
                                            <th>Name</th>                                            
                                            <th>Iqama</th>
                                            <th>Trade</th>
                                            <th>Salary</th>
                                            <th></th>
                                            <th>Project</th> 
                                            <th>Status</th> 
                                            <th>Select</th>
                                        </tr>
                                    </thead>
                                    <tbody id="employee_list_table"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>

                <!--<div class="card-footer card_footer_expode">-->
                <!--</div>-->
            </form>
        </div>
    </div>
</div>

<div class="overlay"></div>
 
<!-- script area -->
<script type="text/javascript">
    

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
    
    $("#multi_emp_id").keyup(function(event) {
        if (event.keyCode === 13) {
            searchProjectWiseEmployeeList();
        }
    });
 
    // Get Employee List by Project Name   
    function searchProjectWiseEmployeeList() {
        
         $("#empl_display_tranfer_section").removeClass("d-block").addClass("d-none");

        var project_id = $('select[name="proj_name"]').val();
        var multi_emp_id = $('#multi_emp_id').val();
        if(project_id < 0 && multi_emp_id == '' ){
            showSweetAlertMessage('error','Please Select Project or Input Valid Employee ID');
            return;
        } 
        
        $.ajax({
                
                type: 'get',
                dataType: 'json',
                data: {
                    project_id: project_id,
                    multi_emp_id:multi_emp_id,
                },
                url: "{{ route('search.project.wise.employee.list.for.transfer') }}",
                success: function (response) {
                    
                     $('#employee_list_table').html('');
                    
                    if (response.status != 200) {
                        showSweetAlertMessage('error','Employee Not Found');
                        return;
                    }else if(response.data.length == 0){
                        showSweetAlertMessage('error','Employee Not Found');
                        return;
                    }
                    $("#empl_display_tranfer_section").removeClass("d-none").addClass("d-block");
                        var rows = "";
                        var counter = 1;
                        $.each(response.data, function (key, value) {
                            rows += `
                        <tr>
                            <td>${counter++}</td>
                            <td> ${value.employee_id}</td>
                            <td>${value.employee_name}</td>
                            <td>${value.akama_no} </td> 
                            <td>${value.catg_name}</td>
                            <td>${value.hourly_employee == 1 ? "Hourly" : "Basic Salary"}</td>
                            <td style="color:#fff">${value.emp_auto_id}</td>
                            <td>${value.proj_name}</td> 
                            <td>${value.job_status == 1 ? "Active" : "Inactive"}</td>
                            <td id="">
                                <div class="row align-items-center">

                                <input type="hidden" id="emp_auto_id${value.emp_auto_id}" name="emp_auto_id[]" value="${value.emp_auto_id}">
                                    <div class="col-md-5">
                                    <input type="checkbox" name="emp_transfer_checkbox-${value.emp_auto_id}" id="emp_transfer_checkbox-${value.emp_auto_id}" value="0">
                                    </div>
                                </div>
                            </td>
                        </tr>
                        `
                        });
                        $('#employee_list_table').html(rows);                  
                },
                error:function(response){
                    showSweetAlertMessage('error','Network Error, Please Try Again');
                    return; 
                }
        });
         
    }

    // Check uncheck 
    function checkUnCheckAllEmployee(operationType){
        let myTable = document.getElementById('employee_list_table');
            for (let row of myTable.rows) {
                allCell = row.cells;          
                var chkboxId = "emp_transfer_checkbox-" + allCell[6].innerText;         
                document.getElementById(chkboxId).checked = !document.getElementById(chkboxId).checked; 
            }      
    }

</script>


@endsection
