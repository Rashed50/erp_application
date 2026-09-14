@extends('layouts.admin-master')
@section('title') Report Expenditure @endsection
@section('content')
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Expenditure Report </h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
           
        </ol>
    </div>
</div>
<!-- add division -->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
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
      <form class="form-horizontal" id="registration" target="_blank" action="{{ route('report-expenditure-process') }}" method="post">
        @csrf
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-8">
                     </div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="card-body card_form" style="padding-top: 0;">
              <div class="row">

                    <div class="row form-group custom_form_group{{ $errors->has('subcompany_id') ? ' has-error' : '' }}">
                        <label class="col-md-3 control-label d-block" style="text-align: left;">Sub Company Name:</label>
                        <div class="col-md-7">
                            <select class="form-control" name="subcompany_id">
                                @foreach($subCompanies as $subComp)
                                <option value="{{ $subComp->sb_comp_id  }}">{{ $subComp->sb_comp_name }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('subcompany_id'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('subcompany_id') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="row form-group custom_form_group{{ $errors->has('project_id') ? ' has-error' : '' }}">
                        <label class="col-md-3 control-label d-block" style="text-align: left;">Project Name:</label>
                        <div class="col-md-7">
                            <select class="form-control" name="project_id">
                                <option value="">Select Here</option>
                                @foreach($allProjects as $project)
                                <option value="{{ $project->proj_id }}">{{ $project->proj_name }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('project_id'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('project_id') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="row form-group custom_form_group{{ $errors->has('expense_head') ? ' has-error' : '' }}">
                        <label class="col-md-3 control-label d-block" style="text-align: left;">Expense Head:</label>
                        <div class="col-md-7">
                            <select class="form-control" name="expense_head">
                                <option value="">Select Here</option>
                                @foreach($expenseHeads as $expenseHead)
                                <option value="{{ $expenseHead->cost_type_id }}">{{ $expenseHead->cost_type_name }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('expense_head'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('expense_head') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div> 
                    <div class="row form-group custom_form_group">
                        <div class="col-md-3">
                            <label class="control-label d-block" style="text-align: left;">Date From:</label>
                        </div>

                        <div class="col-md-3">
                        <input type="date" class="form-control" name="from_date" value="{{ Date('Y-m-d')}}" required>
                        </div>
                        <div class="col-md-1">
                            <label class="control-label d-block" style="text-align: left;">To:</label>
                        </div>

                        <div class="col-md-3">
                        <input type="date" class="form-control" name="to_date" value="{{ Date('Y-m-d')}}" required>
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
