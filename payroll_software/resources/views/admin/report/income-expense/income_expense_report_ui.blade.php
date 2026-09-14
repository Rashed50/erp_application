@extends('layouts.admin-master')
@section('title') Report Income Expense @endsection
@section('content')
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Report Income Expense</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active"> Report Income Expense</li>
        </ol>
    </div>
</div>
<!-- add division -->

<div class="row">
  <div class="col-md-2"></div>
  <div class="col-md-8">
      <form class="form-horizontal" id="registration" target="_blank" action="{{ route('income-expense-report-process-form-submit') }}" method="post">
        @csrf
        <div class="card mt-4">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-8">
                        <h3 class="card-title card_top_title"><i class="fab fa-gg-circle"></i> Report Income Expense</h3>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="card-body card_form" style="padding-top: 0;">
              <div class="row">
                  <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-control-label">Select Month</label>
                            <select class="form-control" name="month_id">
                                <option value="0">Select Month</option>
                                @foreach($months as $month)
                                    <option value="{{ $month->month_id }}">{{ $month->month_name }}</option>
                                @endforeach
                            </select>
                        </div>
                  </div>
                  <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-control-label">Select Project</label>
                            <select class="form-control" name="proj_id">
                                <option value="0">Select Project</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->proj_id }}">{{ $project->proj_name }}</option>
                                @endforeach
                            </select>
                        </div>
                  </div>
              </div>
            </div>
            <div class="card-footer card_footer_button text-center">
                <button type="submit" class="btn btn-primary waves-effect">PROCESS</button>
            </div>
        </div>
      </form>
    </div>
  <div class="col-md-2"></div>
</div>
@endsection
