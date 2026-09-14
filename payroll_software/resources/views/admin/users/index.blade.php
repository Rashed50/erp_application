@extends('layouts.admin-master')
@section('title') Users @endsection
@section('content')
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title"> Manage User</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active">User List</li>
        </ol>
    </div>
</div>
<!-- add division -->
<div class="row">
    <div class="col-md-1"></div>
    <div class="col-md-10">
        @if(Session::has('success'))
          <div class="alert alert-success alertsuccess" role="alert">
             <strong> {{ Session::get('success') }} </strong>
          </div>
        @endif
        @if(Session::has('error'))
          <div class="alert alert-warning alerterror" role="alert">
             <strong> {{ Session::get('error') }} </strong>
          </div>
        @endif
    </div>
    <div class="col-md-1"></div>
</div>

<!-- User list -->
<div class="row d-block" id="userRecordList">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header bg-white border-bottom-0">

                 <div class="row">
                  <div class="col-md-10">
                       @can('user_activities_history')
                        <a href="#" class="btn btn-md btn-primary waves-effect card_top_button" data-toggle="modal" data-target="#login_user_activity_history_modal"><i class="fa fa-search mr-1"></i> Login Activity</a>
                        &nbsp;&nbsp;
                      @endcan
                      @can('system_user_create')
                        <a href="{{ route('users.create') }}" class="btn btn-md btn-primary waves-effect card_top_button"><i class="fa fa-plus-circle mr-2"></i>New User</a>
                      @endcan

                      @can('supper_admin_super_access_login_user_activities_history')
                       @endcan
                   </div>
                  <div class="clearfix"></div>
              </div>

            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        {{-- DataTables requires the table to be within a container for responsive behavior --}}
                        <div class="table-responsive">
                            <table id="dt-vertical-scroll" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>S.N</th>
                                        <th>Emp. ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                        <th>Role</th>
                                        <th>Photo</th>
                                        <th>Manage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Initialize counter for the serial number, often passed via controller for pagination --}}
                                    @php
                                        // If $i is not defined (e.g., first page), start at 0
                                        if (!isset($i)) {
                                            $i = 0;
                                        }
                                    @endphp

                                    @foreach($data as $key => $user)
                                        <tr>
                                            {{-- Increment $i for the serial number, as per user's snippet --}}
                                            <td>{{ ++$i }}</td>
                                            <td>{{ $user->employee_id }}</td>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>
                                                @php
                                                    $statusText = $user->status == 1 ? "Active" : "Inactive";
                                                    $statusClass = $user->status == 1 ? "badge badge-success" : "badge badge-danger";
                                                @endphp
                                                <span class="{{ $statusClass }}">{{ $statusText }}</span>
                                            </td>
                                            {{-- Role column is commented out in the user's original snippet --}}
                                            <td>
                                                @if(!empty($user->getRoleNames()))
                                                    @foreach($user->getRoleNames() as $v)
                                                        <label class="badge badge-success">{{ $v }}</label>
                                                    @endforeach
                                                @endif
                                            </td>
                                            <td>
                                              {{-- <img class="thumb-lg rounded-circle" src="{{ asset($user->profile_image) }}"  width="200px" height="200px"  alt="Not Found" /> --}}
                                            </td>
                                            <td>
                                                @can('supper_admin_super_access_user_create_and_role_permission')
                                                    <a class="btn btn-primary btn-sm" href="{{ route('users.edit',$user->id) }}"> <i class="fa fa-edit"></i></a>
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


{{-- Login User Activity Modal --}}
<div class="modal fade" id="login_user_activity_history_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    {{-- <form action="{{ route('login.user.employee.activity.history') }}" method="post" target="_blank" >
        @csrf --}}
        <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Employee or User Activity History</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                    <div class="form-group row custom_form_group">
                        <label class="control-label col-md-2">Emp. ID</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" name="employee_id" id="employee_id">
                        </div>
                    </div>
                    <div class="form-group row custom_form_group">
                    <label class="col-sm-2 control-label"> From </label>
                    <div class="col-sm-5">
                        <input type="date" class="form-control" id="from_date" name="from_date" value="<?= date("Y-m-d") ?>" >
                    </div>
                    <label class="col-sm-1 control-label"> To </label>
                    <div class="col-sm-4">
                        <input type="date" class="form-control" name="to_date" id="to_date" value="<?= date("Y-m-d") ?>" >
                    </div>
                </div>
                <div class="form-group row custom_form_group">
                    <label class="control-label col-md-2">Report Type</label>
                    <div class="col-md-10">
                        <select class="form-select" id="report_type" name="report_type" required>
                            <option value="0">Login User Activities</option>
                            <option value="1">Employee Activities</option>
                            {{-- <option value="2">Multi Project Work History</option>
                            <option value="3">Salary Details History</option>
                            <option value="4">Activity Base History</option> --}}
                        </select>
                    </div>
                </div>
                <div class="form-group row custom_form_group">
                    <label class="control-label col-md-2">Activity Title</label>
                    <div class="col-md-10">
                        <select class="selectpicker"  id="activity_title_ids" name="activity_title_ids[]" multiple >
                            <option value="">Select Activity Title</option>
                            @foreach ($activity_form_names as $title)
                                <option value="{{ $title->uif_auto_id }}">{{ $title->uif_title }}</option>
                            @endforeach

                        </select>
                    </div>
                </div>


           </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" onclick="showReport()" class="btn btn-primary">Show Report</button>

              </div>
            </div>
          </div>
    {{-- </form> --}}
</div>



 <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta2/css/bootstrap-select.min.css"
        integrity="sha512-mR/b5Y7FRsKqrYZou7uysnOdCIJib/7r5QeJMFvLNHNhtye3xJp1TdJVPLtetkukFn227nKpXD9OjUc09lx97Q=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta2/js/bootstrap-select.min.js"
        integrity="sha512-FHZVRMUW9FsXobt+ONiix6Z0tIkxvQfxtCSirkKc5Sb4TKHmqq1dZa8DphF0XqKb3ldLu/wgMa8mT6uXiLlRlw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

 @push('scripts')
<script>
    $(document).ready(function() {
        if ($.fn.DataTable) {
            $('#dt-vertical-scroll').DataTable({
                // Required settings for vertical scrolling
                "scrollY": "400px",
                "scrollCollapse": true,
                "paging": true, // Using standard pagination for user lists is common
                "ordering": true, // Enables sorting
                "scrollX": true, // Enables horizontal scroll if needed
                "pageLength": 10 // Default page length
            });
        } else {
            console.error("DataTables library not found. Check your script loading order.");
        }
    });
</script>
@endpush

<script>
    function showReport(){
        var report_type = $('#report_type').val();
        const activity_title_ids = $('#activity_title_ids').val();
        const employeeId = $('#employee_id').val();
        const fromDate = $('#from_date').val();
        const toDate = $('#to_date').val();

        console.log('Report Type:', activity_title_ids);

        // For Login User and Employee History reports (types 0 and 1)
        if (report_type === "0" || report_type === "1") {
            // Create the query string with parameters
            const queryString = new URLSearchParams({
                employee_id: employeeId,
                from_date: $('#from_date').val(),
                to_date: $('#to_date').val(),
                report_type: report_type,
                activity_title_ids: activity_title_ids
            }).toString();

            var parameterValue = queryString;
            var url = "{{ route('login.user.employee.activity.history', ':parameter') }}";
            url = url.replace(':parameter', parameterValue);
            window.open(url, '_blank');
        }
        // For Multi Project Work History report (type 2)
        else if (report_type === "2") {
            const fromDate = $('#from_date').val();
            const toDate = $('#to_date').val();

            if (!fromDate || !toDate) {
                showSweetAlert('error', 'Please select both From Date and To Date');
                return;
            }

            if (new Date(fromDate) > new Date(toDate)) {
                showSweetAlert('error', 'From Date cannot be greater than To Date');
                return;
            }

            var baseUrl = '{{ route("employee.multi.project.work.history.report.create") }}';
            const params = new URLSearchParams({
                from_date: fromDate,
                to_date: toDate,
                employee_id: employeeId,
                operation_type: 2,
            });

            const finalUrl = `${baseUrl}?${params.toString()}`;
            window.open(finalUrl, '_blank');
        }
        // For Salary Details History report (type 3)
        else if (report_type === "3") {
            const fromDate = $('#from_date').val();
            const toDate = $('#to_date').val();

            if (!fromDate || !toDate) {
                showSweetAlert('error', 'Please select both From Date and To Date');
                return;
            }

            if (new Date(fromDate) > new Date(toDate)) {
                showSweetAlert('error', 'From Date cannot be greater than To Date');
                return;
            }

            var baseUrl = '{{ route("employee.salary.details.history.report.create") }}';
            const params = new URLSearchParams({
                from_date: fromDate,
                to_date: toDate,
                employee_id: employeeId,
            });

            const finalUrl = `${baseUrl}?${params.toString()}`;
            window.open(finalUrl, '_blank');
        }
        else if(report_type === "4") {
            // Create the query string with parameters
            const queryString = new URLSearchParams({
                employee_id: employeeId,
                from_date: $('#from_date').val(),
                to_date: $('#to_date').val(),
                report_type: report_type,
                activity_type: $('#activity_type').val()
            }).toString();

            var parameterValue = queryString;
            var url = "{{ route('login.user.employee.activity.history', ':parameter') }}";
            url = url.replace(':parameter', parameterValue);
            window.open(url, '_blank');
        }
    }
</script>

@endsection
