@extends('layouts.admin-master')
@section('title') Add Multiple Employee Monthly Record @endsection
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

<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Multiple Employee Monthly Records Insertion </h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        </ol>
    </div>
</div>

<div class="row">
  
    <div class="col-md-12">
        <form class="form-horizontal" id="employeeListForm" action="" method="">
            <div class="card">
                <div class="card-body card_form">
                    <div class="row">
                        <div class="form-group row custom_form_group">
                        <label class="col-sm-1 control-label">Project:</label>
                        <div class="col-sm-3">
                            <select class="form-control" name="proj_name">
                                <option value="">Select Project</option>
                                @foreach($projects as $proj)
                                <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                                @endforeach
                            </select>
                        </div>

                            <label class="col-sm-1 control-label">Year:</label>
                            <div class="col-sm-2">
                                <select class="form-control" name="year">
                                    @foreach(range(date('Y'), date('Y')-5) as $y)
                                    <option value="{{$y}}" {{$y}}>{{$y}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <label class="col-sm-1 control-label">Month:</label>
                            <div class="col-sm-2">
                                <select class="form-control" name="month">
                                    @foreach($months as $m)
                                    <option value="{{ $m->month_id }}" {{ $m->month_id == Carbon\Carbon::now()->format('m') ? 'selected' :'' }}>{{ $m->month_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-sm-2">
                            <button type="submit" onclick="searchProjectWiseEmployeeList()" class="btn btn-primary waves-effect">Search Employee</button>

                             </div>

                        </div>
                    </div>

                </div>

            </div>
        </form>
    </div>
    
</div>

<div class="row">
    <div class="col-lg-12">
        <form id="multi-employee-work-record-insert" action="{{ route('multi-employe-month-record-insert-form-submit') }}" onsubmit="submit_button.disabled = true;" method="post">
            @csrf
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="form-group row custom_form_group">


                            <label class="col-sm-2 control-label">Salary Year:</label>
                            <div class="col-sm-2">
                                <select class="form-control" name="year">
                                    @foreach(range(date('Y'), date('Y')-5) as $y)
                                    <option value="{{$y}}" {{$y}}>{{$y}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <label class="col-sm-2 control-label">Salary Month:</label>
                            <div class="col-sm-2">
                                <select class="form-control" name="month">
                                    @foreach($months as $m)
                                    <option value="{{ $m->month_id }}" {{ $m->month_id == Carbon\Carbon::now()->format('m') ? 'selected' :'' }}>{{ $m->month_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-sm-2">
                                <button type="submit" id="submit_button" name="submit_button" class="btn btn-primary waves-effect">Submit</button>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <span id="data_not_found" class="d-none">Data Not Found!</span>
                                <table id="employeeinfo">
                                    <thead>
                                        <tr>
                                            <th>S.N</th>
                                            <th>Emp.ID</th>
                                            <th>Name</th>
                                            <th>Basic</th>
                                            <th>Rate</th>
                                            <th>Total Hours</th>
                                            <th>Total OT</th>
                                            <th>Days</th>
                                            <th>Salary Project</th>
                                            <th>Select</th>
                                        </tr>
                                    </thead>
                                    <tbody id="employee_list_table"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
</div>
</div>
<!-- FORM VALIDATION -->



<script type="text/javascript">
    $(document).ready(function() {

        $("#employeeListForm").validate({

            submitHandler: function(form) {
                return false;
            },

            rules: {
                proj_name: {
                    required: true,
                },

            },

            messages: {
                proj_name: {
                    required: "You Must Be Select This Field!",
                },

            },


        });
    });

    function searchProjectWiseEmployeeList() {
        var project_id = $('select[name="proj_name"]').val();
  var year = $('select[name="year"]').val();
        var month = $('select[name="month"]').val();
        if (project_id > 0) {
            $.ajax({

                type: 'POST',
                dataType: 'json',
                data: {
                    project_id: project_id,
                    month: month,
                    year: year
                },
                url: "{{ route('employee.month.work-project-wise-employees-request') }}",
                success: function(response) {

                    if (response.entryList) {
                        var rows = "";
                        var counter = 1;
                        $.each(response.entryList, function(key, value) {
                            rows += `
                    <tr>
                        <td>${counter++}</td>
                        <td> ${value.employee_id}</td>
                        <td>${value.employee_name}</td>
                        <td>${value.basic_amount}</td>
                        <td>${value.hourly_rent}</td>
                        
                          <td>
                          <input type="hidden" id="emp_auto_id${value.emp_auto_id}" name="emp_auto_id[]" value="${value.emp_auto_id}">
                          <input type="number" name="total_hours_${value.emp_auto_id}"  id="total_hours${value.emp_auto_id}"  value=""  step=".01"  max="400" min="0">
                          </td>
                          <td>
                          <input type="number" name="ot_hours_${value.emp_auto_id}" id="ot_hours${value.emp_auto_id}"   placeholder=""   max="150" min="0">
                         </td>
                         <td>
                          <input type="number" name="total_day_${value.emp_auto_id}" id="total_day${value.emp_auto_id}"  value=""  placeholder=""  max="30" min="0">
                          </td>
                          <td> 
                          <input type="number" name="project_id_${value.emp_auto_id}" id="project_id${value.emp_auto_id}" value="${value.project_id}"  placeholder=""  max="100" min="0">
                         </td>

                          <td><input type="checkbox" name="checkbox_${value.emp_auto_id}" id="entry_in_checkbox-${value.emp_auto_id}" value="0"></td>
                        
                    </tr>
                    `
                        });
                        $('#employee_list_table').html(rows);
                    } else {

                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000
                        })

                        if ($.isEmptyObject(response.error)) {
                            Toast.fire({
                                type: 'success',
                                title: response.success
                            })
                        } else {
                            Toast.fire({
                                type: 'error',
                                title: response.error
                            })
                        }
                        //  end message
                    }

                }
            });
        } else {
            // alert(project_id);
            const toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });

        }
    }
    //  <td><a title="Add" id="${value.emp_auto_id}" onclick="addOutEmployeeTime(this.id)"><i class="fas fa-thumbs-up fa-lg edit_icon"></i></a>
    //  </td>
</script>




@endsection