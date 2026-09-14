@extends('layouts.admin-master')
@section('title') Monthly Salary @endsection
@section('content')
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Monthly Salary Generat</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="active"> Monthly Salary</li>
        </ol>
    </div>
</div>
<!-- add division -->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        @if(Session::has('success'))
          <div class="alert alert-success alertsuccess" role="alert">
             <strong>Successfully!</strong> Salary Generat.
          </div>
        @endif
        @if(Session::has('success_update'))
          <div class="alert alert-success alertsuccess" role="alert">
             <strong>Successfully!</strong> Update User Information.
          </div>
        @endif
        @if(Session::has('delete'))
          <div class="alert alert-success alertsuccess" role="alert">
             <strong>Successfully!</strong> Delete User Information.
          </div>
        @endif
        @if(Session::has('error'))
          <div class="alert alert-warning alerterror" role="alert">
             <strong>Opps!</strong> please try again.
          </div>
        @endif
    </div>
</div>

<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
      <form class="form-horizontal" id="registration" action="{{ route('salary-generat-apply') }}" method="post">
        @csrf
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-8">
                        <h3 class="card-title card_top_title"><i class="fab fa-gg-circle"></i> Salary Generat</h3>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="card-body card_form" style="padding-top: 0;">

              <div class="form-group custom_form_group row{{ $errors->has('emp_id') ? ' has-error' : '' }}">
                  <label class="control-label col-sm-3">Employee ID:</label>
                  <div class="col-sm-9">
                    <input type="text" class="form-control typeahead" placeholder="Type Employee ID" name="emp_id" id="emp_id_search" onkeyup="empSearch()" onfocus="showResult()" onblur="hideResult()">
                    @if ($errors->has('emp_id'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('emp_id') }}</strong>
                        </span>
                    @endif
                    <div id="showEmpId"></div>
                  </div>
              </div>

              <div class="form-group custom_form_group row{{ $errors->has('month') ? ' has-error' : '' }}">
                  <label class="control-label col-sm-3">Select Month:</label>
                  <div class="col-sm-9">
                    <select class="form-control" name="month" required>
                      <option value="">Select Month</option>
                      @foreach($month as $item)
                      <option value="{{ $item->month_id }}">{{ $item->month_name }}</option>
                      @endforeach
                    </select>

                    @if ($errors->has('month'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('month') }}</strong>
                        </span>
                    @endif
                  </div>
              </div>



            </div>
            <div class="card-footer card_footer_button text-center">
                <button type="submit" class="btn btn-primary waves-effect">Generat</button>
            </div>
        </div>
      </form>
    </div>
    <div class="col-md-2"></div>
</div>

<!-- division list -->
<div class="row">
    <div class="col-lg-12">
      <div class="card">
          <div class="card-header">
              <div class="row">
                  <div class="col-md-8">
                      <h3 class="card-title card_top_title"><i class="fab fa-gg-circle"></i> All User List</h3>
                  </div>
                  <div class="clearfix"></div>
              </div>
          </div>
          <div class="card-body">
              <div class="row">
                  <div class="col-12">
                      <div class="table-responsive">
                          <table id="alltableinfo" class="responsive table table-bordered custom_table mb-0">
                              <thead>
                                  <tr>
                                      <th>Photo</th>
                                      <th>Name</th>
                                      <th>Email</th>
                                      <th>Role</th>
                                      <th>Manage</th>
                                  </tr>
                              </thead>
                              <tbody>


                                  <tr>
                                    <td>
                                      #
                                    </td>
                                    <td>#</td>
                                    <td>**</td>
                                    <td>**</td>
                                    <td>
                                      <a href="#" title="edit"><i class="fa fa-pencil-square fa-lg edit_icon"></i></a>
                                      <a href="#" title="delete"  id="delete" title="delete data"><i class="fa fa-trash fa-lg delete_icon"></i></a>

                                    </td>
                                  </tr>

                              </tbody>
                          </table>
                      </div>
                  </div>
              </div>
          </div>
      </div>
    </div>
</div>
@endsection
