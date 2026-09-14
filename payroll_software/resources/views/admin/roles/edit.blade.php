@extends('layouts.admin-master')
@section('title') Update Permissions @endsection
@section('content')

{{-- <div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title"> Update Role Permissions</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active">Update Role Permissions</li>
        </ol>
    </div>
</div> --}}

<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-7">
        @if(Session::has('success'))
          <div class="alert alert-success alertsuccess" role="alert" style="margin-left: -20px">
              <strong>Successfully!</strong>Updated Permissions .
          </div>
        @endif
        @if(Session::has('error'))
          <div class="alert alert-warning alerterror" role="alert" style="margin-left: -20px">
              <strong>Opps!</strong> please try again.
          </div>
        @endif
    </div>
    <div class="col-md-2"></div>
</div>

<div class="row">
    <div class="col-lg-12">
        <form action="{{ route('roles.update', $role->id) }}" method="POST">
            @csrf
            @method('PATCH') {{-- This replaces 'method' => 'PATCH' --}}
            <div class="card">
                <div class="card-body card_form">
                    <div class="row">

                        <div class="col-md-4">
                            <div class="form-group row custom_form_group {{ $errors->has('name') ? ' has-error' : '' }}">
                                <label class="control-label col-md-5">Role Name:<span class="req_star">*</span></label>
                                <div class="col-md-7">
                                    <input type="text" name="name" value="{{ old('name', $role->name) }}" placeholder="Role Name" class="form-control">
                                    @if ($errors->has('name'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('name') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                            <div class="col-md-4 text-right">
                                @can('role-edit')
                                    <button type="button" id="Check_All" class="btn btn-md btn-primary waves-effect card_top_button">Check All</button>
                                    <button type="button" id="Un_CheckAll" class="btn btn-md btn-primary waves-effect card_top_button">Uncheck All</button>
                                @endcan
                            </div>

                        <div class="col-md-4 text-left">
                            @can('system_user_list')
                                <a href="{{ route('users.index') }}" class="btn btn-md btn-primary waves-effect card_top_button"><i class="fa fa-th"></i> Users list</a>
                            @endcan
                             @can('role-list')
                                <a href="{{ route('roles.index') }}" class="btn btn-md btn-primary waves-effect card_top_button"><i class="fa fa-th"></i> Roles</a>
                             @endcan
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table id="dt-vertical-scroll" class="table table-striped table-bordered" data-toggle="table" data-search="true" cellspacing="0" width="100%">

                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>ID</th>
                                            <th>Permission</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($permission as $value)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $value->id }}</td>
                                            <td>
                                                <input  type="checkbox"   name="permission[]"  value="{{ $value->id }}"  class="permission-checkbox"  {{ in_array($value->id, $rolePermissions) ? 'checked' : '' }}>
                                                    {{ $value->name }}
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer card_footer_button text-center">
                    @can('role-edit')
                        <button type="submit" id="onSubmit" class="btn btn-primary waves-effect">Update</button>
                    @endcan
                </div>
            </div>
        </form>
    </div>
</div>



<script>
    $(document).ready(function () {

        // Store all selected permissions
        let selectedPermissions = new Set();

        // Load initially selected permissions
        $('.permission-checkbox:checked').each(function () {
            selectedPermissions.add(String($(this).val()));
        });


        // Checkbox changed
        $(document).on('change', '.permission-checkbox', function () {

            const permissionId = String($(this).val());

            if ($(this).is(':checked')) {
                selectedPermissions.add(permissionId);
            } else {
                selectedPermissions.delete(permissionId);
            }
        });


        // Bootstrap Table redraw/search
        $('#dt-vertical-scroll').on('post-body.bs.table', function () {

            $('.permission-checkbox').each(function () {

                const permissionId = String($(this).val());

                $(this).prop(
                    'checked',
                    selectedPermissions.has(permissionId)
                );

            });

        });


        // Check All - visible/search result only
        $('#Check_All').on('click', function () {

            $('.permission-checkbox:visible').each(function () {

                const permissionId = String($(this).val());

                selectedPermissions.add(permissionId);

                $(this).prop('checked', true);
            });

        });


        // Uncheck All - visible/search result only
        $('#Un_CheckAll').on('click', function () {

            $('.permission-checkbox:visible').each(function () {

                const permissionId = String($(this).val());

                selectedPermissions.delete(permissionId);

                $(this).prop('checked', false);
            });

        });


        // IMPORTANT:
        // Before submitting, create hidden inputs for ALL selected permissions
        $('#onSubmit').closest('form').on('submit', function () {

            const form = this;

            // Remove previously created hidden permission inputs
            $(form).find('.selected-permission-hidden').remove();

            // Add all selected permissions
            selectedPermissions.forEach(function (permissionId) {

                $('<input>')
                    .attr('type', 'hidden')
                    .attr('name', 'permission[]')
                    .attr('value', permissionId)
                    .addClass('selected-permission-hidden')
                    .appendTo(form);

            });

        });

    });
</script>



@endsection
