@extends('layouts.admin-master')
@section('title') Income Source @endsection
@section('content')
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Income Source</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active">Income Source</li>
        </ol>
    </div>
</div>
<!-- add division -->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        @if(Session::has('success'))
          <div class="alert alert-success alertsuccess" role="alert">
             <strong>Successfully!</strong> Approve New Income Source Information.
          </div>
        @endif
        @if(Session::has('success_update'))
          <div class="alert alert-success alertsuccess" role="alert">
             <strong>Successfully!</strong> Update New Income Source Information.
          </div>
        @endif
        @if(Session::has('delete'))
          <div class="alert alert-success alertsuccess" role="alert">
             <strong>Successfully!</strong> Delete This Information.
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
    <div class="col-lg-12">
      <div class="card">
          <div class="card-header">
              <div class="row">
                  <div class="col-md-8">
                      <h3 class="card-title card_top_title"><i class="fab fa-gg-circle"></i> Founding Source </h3>
                  </div>
                  <div class="clearfix"></div>
              </div>
          </div>
          <div class="card-body">
              <div class="row">
                  <div class="col-12">
                      <div class="table-responsive">
                          <table id="alltableinfo" class="table table-bordered custom_table mb-0">
                              <thead>
                                  <tr>
                                      <th>Date</th>
                                      <th>Project Name</th>
                                      <th>Employee Name</th>
                                      <th>Amount</th>
                                      <th>Status</th>
                                      <th>Manage</th>
                                  </tr>
                              </thead>
                              <tbody>
                                @foreach($all as $item)
                                  <tr>
                                    <td>{{ $item->submitted_date }}</td>
                                    <td>{{ $item->project->proj_name }}</td>
                                    <td>{{ $item->employee->employee_name }}</td>
                                    <td>{{ $item->net_amount }}</td>
                                    <td>
                                        <span class="badge bg-primary">{{ $item->invoice_status == 1 ? 'Released' : 'Pending' }}</span>
                                    </td>
                                    <td>
                                      <a href="{{ route('income-approve',$item->inc_id ) }}" title="Approve"><i class="fas fa-thumbs-up fa-lg edit_icon"></i></a>
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
@endsection
