@extends('layouts.admin-master')
@section('title') Emp Project Access @endsection
@section('content')

<div class="row bread_part">
  <div class="col-sm-12 bread_col">
    <h4 class="pull-left page-title bread_title">Project Access Permission</h4>
    <ol class="breadcrumb pull-right">
      <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
      <li class="active">Project Access</li>
    </ol>
  </div>
</div>

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

<!-- Total Form Part Start -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-footer card_footer_button text-center">

                 <!-- Employee Project Access Form Start -->
                <div class="col-md-4">
                    <div class="card"><br>
                        <h5 class="card-title">User Project Access Permission</h5> <br>
                        <div class="card-body card_form" style="padding-top: 0;">
                            <form class="form-horizontal" id="projectform" action="{{ route('insert-user-project-access-info') }}" method="post">
                                @csrf

                                <div class="form-group row custom_form_group">
                                    <label class="control-label col-md-3">User<span class="req_star">*</span></label>
                                    <div class="col-md-9">
                                        <select class="form-select" name="user_id">
                                           <option value="">Select User Name</option>
                                            @foreach ($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->employee_id}} - {{$user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row custom_form_group">
                                        <label class="col-md-3 control-label">Project<span class="req_star">*</span></label>
                                        <div class="col-md-9">
                                            <select class="selectpicker" name="proj_id[]" multiple>
                                                @foreach ($projects as $project)
                                                <option value="{{ $project->proj_id  }}">{{ $project->proj_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                </div>
                                <div class="card-footer card_footer_button text-center">
                                    <button type="submit" class="btn btn-primary waves-effect">SAVE</button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>


                <!-- Single Employee Project Details Part Start -->
                <div class="col-md-4">
                    <div class="card"><br>
                        <h5 class="card-title">Searching Employee Assigned Projects</h5>  <br>
                        <div class="card-body card_form" style="padding-top: 0;">
                            <div class="form-group row custom_form_group" id="searchEmployeeId">
                                <label class="col-md-3 control-label">Emp. ID:</label>

                              <div class="col-md-9">
                                  <input type="text" class="form-control typeahead"
                                      placeholder="Employee ID Here..." name="empId" id="emp_id_search"
                                       required>
                              </div>
                            </div>
                            <b style="text-align: center; font-size:15px;">OR</b>
                            <div class="form-group row custom_form_group" id="searchEmployeeId">
                                <label class="col-md-3 control-label">Project</label>
                                <div class="col-md-9">
                                            <select class="form-select" name="project_id"  id="project_id">
                                                <option value="">Select Project</option>
                                                @foreach ($projects as $project)
                                                <option value="{{ $project->proj_id  }}">{{ $project->proj_name }}</option>
                                                @endforeach
                                            </select>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="card-footer card_footer_button text-center">
                            <button type="submit" id="search_assign_project" onclick="singleEmoloyeeDetails()" style="margin-top: 2px"
                                class="btn btn-primary waves-effect">SEARCH</button>
                        </div>
                    </div>
                </div>

                 <!-- User Attendance IN OUT Permission -->
                <div class="col-md-4">
                  <div class="card"><br>
                      <h5 class="card-title">Timekeeper Attendance IN OUT Permission (Days)</h5> <br>
                      <div class="card-body card_form" style="padding-top: 0;">
                          <form class="form-horizontal" id="projectform" action="{{ route('attendance.inout.permission') }}" method="post">
                              @csrf
                              <div class="form-group row custom_form_group">
                                      <label class="col-md-3 control-label">Title</label>
                                      <div class="col-md-6">
                                          <select class="selectpicker" name="aiop_id[]" multiple>
                                              <option value="1"> Attendance IN </option>
                                              <option value="2"> Attendance OUT </option>
                                              <option value="3"> Attendance Update(Single Employee) </option>
                                              <option value="4"> Attendance Update(Multiple Employee) </option>
                                          </select>
                                      </div>
                              </div>
                              <div class="form-group row custom_form_group" >
                                <label class="control-label col-md-3">Days<span class="req_star">*</span></label>

                                <div class="col-md-6">
                                    <input type="text" class="form-control typeahead"
                                        placeholder="Input Number of Days" name="allow_days" id="allow_days"  required>
                                </div>
                              </div>

                              <div class="card-footer card_footer_button text-center">
                                <div class="btn-group" role="group" aria-label="Basic mixed styles example">
                                  <button type="submit" class="btn btn-primary waves-effect">Update</button>
                                 </div>

                              </div>
                          </form>

                      </div>
                  </div>
                </div>

            </div>
        </div>
    </div>
</div>
<!-- Total Form Part End -->




<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-header">

      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-12">
            <div class="table-responsive">
              <table id="alltableinfo" class="table table-bordered custom_table mb-0">
                <thead>
                  <tr>
                    <th>S.N</th>
                    <th>Emp Id</th>
                    <th>Emp Name</th>
                    <th>Project Name</th>
                    <th>Access Status</th>
                    <th>Insert By</th>
                    <th>Manage</th>
                  </tr>
                </thead>
                <tbody id="projAccessInfos">
                  @forelse($records as $item)
                  <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->employee_id }}</td>
                    <td>{{ $item->accessUser->name ?? ''}}</td>
                    <td>{{ $item->project->proj_name }}</td>
                    <td>
                        @if ($item->access_status == 1)
                            <span class="badge badge-pill badge-success">Access Granted</span>
                        @else
                            <span class="badge badge-pill badge-danger">Access Removed</span>
                        @endif
                    </td>
                    <td> </td>

                    <td>
                        @if ($item->access_status == 1)
                            <a href="{{ route('user-project-access-deActive',[$item->user_proj_acc_auto_id]) }}}"
                        title="Remove Access" id="confirm"><i class="fa fa-arrow-circle-down"
                            style="font-size: 20px; color:red;"></i></a>
                        @else
                            <a href="{{ route('user-project-access-active',[$item->user_proj_acc_auto_id]) }}"
                            title="Grant Access"><i class="fa fa-arrow-circle-up"
                                style="font-size: 20px;"></i></a>
                        @endif
                    </td>
                  </tr>
                  @empty
                  <p class="data_not_found"></p>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<!-- script area -->
<script type="text/javascript">
  /* form validation */
  $(document).ready(function() {
    $("#projectform").validate({
      /* form tag off  */
      // submitHandler: function(form) { return false; },
      /* form tag off  */
      rules: {
        empID: {
          required: true,
        },
        proj_id: {
          required: true,
        },
      },

      messages: {
        empID: {
          required: "Please Select Employee Name!",
        },
        proj_id: {
          required: "Please Select Project Name!",
        },
      },
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

    $('#emp_id_search').keydown(function (e) {
        if (e.keyCode == 13) { // Enter Key press
            singleEmoloyeeDetails();
        }
    })

    //   User Project Access Permission
    function singleEmoloyeeDetails() {
        var empId = $("#emp_id_search").val();
        var project_id = $("#project_id").val();
        debugger;
        if(empId == '' && project_id == ''){
            showSweetAlertMessage('error','Please input Employee ID or Select a Project');
            return;
        }

        $.ajax({
            type: 'POST',
            url: "{{ route('user.searching-for-project-access-details-info') }}",
            data: {
                empId: empId,
                project_id:project_id,
            },
            dataType: 'json',
            success: function (response) {

                if (response.success) {
                    var rows = "";

                    var counter = 1;
                    $('#projAccessInfos').html('');
                    $.each(response.data, function (key, value) {

                        var inActive = "{{ url('admin/user/project-access/information-not-allowed/')}}" + "/" + value.user_proj_acc_auto_id;
                        var active = "{{ url('admin/user/project-access/information-allowed/')}}" + "/" + value.user_proj_acc_auto_id;
                        rows += `
                    <tr>
                        <td>${counter++}</td>
                        <td>${value.employee_id}</td>
                        <td>${value.employee_name}</td>
                        <td>${value.proj_name} </td>
                        <td>
                            ${ (value.access_status == 1) ?
                                `<span class="badge badge-pill badge-success">Access Granted</span>`
                                :
                                `<span class="badge badge-pill badge-danger">Access Removed</span>`
                            }
                        </td>
                        <td>${value.name}</td>
                        <td id="">
                            ${ (value.access_status == 1) ?
                                ` <a href="${inActive}" title="Remove Access" id="confirm"><i class="fa fa-arrow-circle-down"
                            style="font-size: 20px; color:red;"></i></a>`
                                :
                                ` <a href="${active}" title="Grant Access"><i class="fa fa-arrow-circle-up"
                                style="font-size: 20px;"></i></a>`
                            }
                        </td>
                    </tr>
                    `
                    });
                    $('#projAccessInfos').html(rows);
                } else {
                    showSweetAlertMessage('error','Data Not found');
                }
             }
    });
}

</script>


<!-- added this for Multiple Selection dropdownlist  -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta2/css/bootstrap-select.min.css" integrity="sha512-mR/b5Y7FRsKqrYZou7uysnOdCIJib/7r5QeJMFvLNHNhtye3xJp1TdJVPLtetkukFn227nKpXD9OjUc09lx97Q==" crossorigin="anonymous"/>
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta2/js/bootstrap-select.min.js" integrity="sha512-FHZVRMUW9FsXobt+ONiix6Z0tIkxvQfxtCSirkKc5Sb4TKHmqq1dZa8DphF0XqKb3ldLu/wgMa8mT6uXiLlRlw==" crossorigin="anonymous"></script>


@endsection
