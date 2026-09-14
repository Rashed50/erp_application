@extends('layouts.admin-master')
@section('title') Daily Cost List @endsection
@section('content')
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title"> Daily Expenses List For Approval</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
           
        </ol>
    </div>
</div>
<!-- Session Flash Message -->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        @if(Session::has('success'))
          <div class="alert alert-success alertsuccess" role="alert">
             <strong> {{Session::get('success')}} </strong>
          </div>
        @endif
        @if(Session::has('error'))
          <div class="alert alert-warning alerterror" role="alert">
             <strong> {{Session::get('error')}} </strong>
          </div>
        @endif
    </div>
</div>

<!-- Expense Approval Pending list -->
<div class="row">
    <div class="col-lg-12">
      <div class="card">
           
          <div class="card-body">
              <div class="row">
                  <div class="col-12">
                      <div class="table-responsive">
                          <table id="alltableinfo" class="table table-bordered custom_table mb-0">
                              <thead>
                                  <tr>
                                      <th>Date</th>
                                      <th>Project Name</th>
                                      <th>Cost By</th>
                                      <th>Amount</th>
                                      <th>Vouchar</th>
                                      <th>Approve</th>
                                  </tr>
                              </thead>
                              <tbody>
                                @foreach($all as $item)
                                  <tr>
                                    <td>{{ Carbon\Carbon::parse($item->expire_date)->format('D, d F Y') }}</td>
                                    <td>{{ $item->project->proj_name }}</td>
                                    <td>{{ $item->employee->employee_name }}</td>
                                    <td>{{ $item->total_amount }}</td>
                                    <td>
                                      <!-- <a href="{{ asset('uploads/vouchar/'.$item->vouchar) }}" class="project-popup">
                                          <img src="{{ asset('uploads/vouchar/'.$item->vouchar) }}" alt="Vouchar" style="width: 80px">
                                      </a> -->
                                    </td>
                                    <td>
                                      @if($item->status == 'pending')
                                      <a href="{{ route('company.daily.new.expesne.approval.request',$item->cost_id) }}" title="Approve"><i class="fas fa-thumbs-up fa-lg edit_icon"></i></a>
                                      @else
                                       --
                                      @endif
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
