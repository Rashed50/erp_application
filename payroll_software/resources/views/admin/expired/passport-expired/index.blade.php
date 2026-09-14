@extends('layouts.admin-master')
@section('title') Employee Iqama Expired @endsection
@section('internal-css')
  <style media="screen">
      tr td span.days{
        display: inline-block;
        color: #fff;
        padding: 5px 10px;
      }
  </style>
@endsection

@section('content')
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Employee Passport Expired</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active"> Employee Passport Expired</li>
        </ol>
    </div>
</div>
<!-- division list -->
<div class="row">
    <div class="col-lg-12">
      <div class="card">
          <div class="card-header">
              <div class="row">
                  <div class="col-md-8">
                      <h3 class="card-title card_top_title"><i class="fab fa-gg-circle"></i> Employee List</h3>
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
                                      <th>ID</th>
                                      <th>Name</th>
                                      <th>Employee Type</th>
                                      <th>Designation</th>
                                      <th>Passport Expire Date</th>
                                      <th>Total Days</th>
                                  </tr>
                              </thead>
                              <tbody>
                                @foreach($emp as $item)
                                <!-- calculate -->
                                @php
                                  $expire = $item->passfort_expire_date;
                                  $current = Carbon\Carbon::now()->format('Y-m-d');

                                  $first = new DateTime($expire);
                                  $second = new DateTime($current);
                                  $diffDate = date_diff($second,$first);

                                  $diff =  $diffDate->format("%R%a days");
                                  $days =  $diffDate->format("%R%a");



                                @endphp

                                <tr>
                                  <td>{{ $item->employee_id }}</td>
                                  <td>{{ $item->employee_name }}</td>
                                  <td>{{ $item->employeeType->name }}</td>
                                  <td>{{ $item->category->catg_name }}</td>
                                  <td>{{ $item->passfort_expire_date }}</td>

                                  @if($days >= 1 && $days <= 30)
                                  <td> <span class="days" style="background: red">{{ $diff }}</span> </td>
                                  @elseif($days <= 0)
                                  <td> <span class="days" style="background: red">0 Day</span> </td>
                                  @elseif($days <= 60)
                                  <td> <span class="days" style="background: yellow; color: #222">{{ $diff }}</span> </td>
                                  @elseif($days <= 90)
                                  <td> <span class="days" style="background: green">{{ $diff }}</span> </td>
                                  @else
                                  <td> <span>{{ $diff }}</span> </td>
                                  @endif

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
