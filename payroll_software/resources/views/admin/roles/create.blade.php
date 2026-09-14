@extends('layouts.admin-master')
@section('title') New Role @endsection

@section('internal-css')
    <link href="{{ asset('contents/admin') }}/datatables/css/jquery.dataTables.min.css" rel="stylesheet">
@endsection

@section('content')
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title"> Role Create</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active"> Role Create</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-7">
        @if(Session::has('success'))
          <div class="alert alert-success alertsuccess" role="alert" style="margin-left: -20px">
              <strong>Successfully!</strong> Added New Role.
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
        <form class="form-horizontal" id="registration" method="post" action="{{ route('roles.store') }}">
            @csrf
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-6">
                            <h3 class="card-title card_top_title"><i class="fab fa-gg-circle"></i> Role List</h3>
                        </div>
                        <div class="col-md-6 text-right">
                            <a href="{{ route('roles.index') }}" class="btn btn-md btn-primary waves-effect card_top_button">
                                <i class="fa fa-th mr-2"></i>All Roles
                            </a>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>

                <div class="card-body card_form">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group custom_form_group{{ $errors->has('name') ? ' has-error' : '' }}">
                                <label class="control-label">Name:<span class="req_star">*</span></label>
                                <div>
                                    <input type="text" placeholder="Name" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                                    @if ($errors->has('name'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('name') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    {{-- Simplified buttons to work with the jQuery script below --}}
                                    <button type="button" id="Check_All" class="btn btn-sm btn-secondary">Check All</button>
                                    <button type="button" id="Un_CheckAll" class="btn btn-sm btn-light border">Uncheck All</button>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="example2" class="display" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>Permission</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($permission as $value)
                                                <tr>
                                                    <td>
                                                        <label>
                                                            <input type="checkbox" name="permission[]" value="{{ $value->id }}" class="permission-checkbox">
                                                            {{ $value->name }}
                                                        </label>
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
                <div class="card-footer card_footer_button text-center">
                    <button type="submit" id="onSubmit" class="btn btn-primary waves-effect">SAVE</button>
                </div>
            </div>
        </form>
    </div>
</div>

@section('script')
    <script src="{{ asset('contents/admin') }}/datatables/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('contents/admin') }}/datatables/js/datatables.init.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            // Check All
            $(document).on('click', '#Check_All', function(){
                $(".permission-checkbox").prop('checked', true);
            });

            // Uncheck All
            $(document).on('click', '#Un_CheckAll', function(){
                $(".permission-checkbox").prop('checked', false);
            });
        });
    </script>
@endsection

@endsection
