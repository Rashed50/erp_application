@extends('layouts.admin-master')
@section('title') Add Project @endsection
@section('content')


<style media="screen">
    .approve_button {
        background: #2B4049;
        color: #fff;
        font-size: 12px;
        padding: 3px 6px;
        border-radius: 5px;
    }

    .approve_button:hover {
        color: #fff;
    }

    .switch {
        margin-left: 10px;
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 5px;
        right: -5px;
        bottom: 0;
        background-color: #ccc;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    input:checked+.slider {
        background-color: #2196F3;
    }

    input:focus+.slider {
        box-shadow: 0 0 1px #2196F3;
    }

    input:checked+.slider:before {
        -webkit-transform: translateX(26px);
        -ms-transform: translateX(26px);
        transform: translateX(26px);
    }

    /* Rounded sliders */
    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }
</style>


<div class="row bread_part">
  <div class="col-sm-12 bread_col">
    <h4 class="pull-left page-title bread_title">Projects Information</h4>
    <ol class="breadcrumb pull-right">
      <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
      <li class="active">Project Information</li>
    </ol>
  </div>
</div>

<div class="row">
  <div class="col-md-2"></div>
  <div class="col-md-8">
    @if(Session::has('success'))
    <div class="alert alert-success alertsuccess" role="alert">
      <strong>{{Session::get('success')}}</strong>
    </div>
    @endif

    @if(Session::has('error'))
    <div class="alert alert-warning alerterror" role="alert">
    <strong>{{Session::get('error')}}</strong>
    </div>
    @endif
  </div>
  <div class="col-md-2"></div>
</div>

<!--   New Project Insertion FORM -->
<div class="row">
  <div class="col-md-2">
    @can('project_new_plot_add')
        <a data-toggle="modal" data-target="#addPlotModalUI" class="btn btn-md btn-primary waves-effect card_top_button">Add Project Plot</a>
    @endcan()  </div>
  <div class="col-md-8">
    <form class="form-horizontal project-details-form" id="projectform" method="post" action="{{ route('insert-project-info') }}" enctype="multipart/form-data">
      @csrf
      <div class="card">
        <div class="card-header">
          <div class="row">
            <div class="col-md-8">
             </div>
            <div class="clearfix"></div>
          </div>
        </div>
        <div class="card-body card_form">

          <div class="form-group row custom_form_group{{ $errors->has('proj_name') ? ' has-error' : '' }}">
            <label class="col-sm-3 control-label">Project Name:<span class="req_star">*</span></label>
            <div class="col-sm-7">
              <input type="text" placeholder="project name" class="form-control" id="proj_name" name="proj_name" value="{{old('proj_name')}}">
              @if ($errors->has('proj_name'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('proj_name') }}</strong>
              </span>
              @endif
            </div>
          </div>
          <div class="form-group row custom_form_group{{ $errors->has('starting_date') ? ' has-error' : '' }}">
            <label class="col-sm-3 control-label">Starting Date:<span class="req_star">*</span></label>
            <div class="col-sm-7">
              <input type="date" placeholder="starting date" class="form-control" id="starting_date" name="starting_date"  >
              @if ($errors->has('starting_date'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('starting_date') }}</strong>
              </span>
              @endif
            </div>
          </div>
          <div class="form-group row custom_form_group{{ $errors->has('address') ? ' has-error' : '' }}">
            <label class="col-sm-3 control-label">Project Location:<span class="req_star">*</span></label>
            <div class="col-sm-7">
            <input type="text" placeholder="Enter Project Location" class="form-control" id="address" name="address" value="{{old('address')}}">
              @if ($errors->has('address'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('address') }}</strong>
              </span>
              @endif
            </div>
          </div>
          <div class="form-group row custom_form_group{{ $errors->has('proj_code') ? ' has-error' : '' }}">
            <label class="col-sm-3 control-label">Project Code:</label>
            <div class="col-sm-7">
              <input type="text" placeholder="project code" class="form-control" id="proj_code" name="proj_code" value="{{$project_code}}">
              @if ($errors->has('proj_code'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('proj_code') }}</strong>
              </span>
              @endif
            </div>
          </div>

          <div class="form-group row custom_form_group{{ $errors->has('proj_budget') ? ' has-error' : '' }}">
            <label class="col-sm-3 control-label">Contract Value:<span class="req_star">*</span></label>
            <div class="col-sm-7">
              <input type="number" placeholder="Project Contract Amount" class="form-control" id="proj_budget" name="proj_budget" value="{{old('proj_budget')}}">
              @if ($errors->has('proj_budget'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('proj_budget') }}</strong>
              </span>
              @endif
            </div>
          </div>

          <div class="form-group row custom_form_group{{ $errors->has('boq_clearance_duration') ? ' has-error' : '' }}">
            <label class="col-sm-3 control-label">BOQ Clearance(In Day):<span class="req_star">*</span></label>
            <div class="col-sm-7">
              <input type="number" placeholder="BOQ Clearnace Duration" value="20" class="form-control" id="boq_clearance_duration" min="1" name="boq_clearance_duration" >
              @if ($errors->has('boq_clearance_duration'))
              <span class="invalid-feedback" role="alert">
                  <strong>{{ $errors->first('boq_clearance_duration') }}</strong>
              </span>
              @endif
            </div>
          </div>

          <div class="form-group row custom_form_group">
                        <label class="control-label col-sm-3">Working Status </label>
                        <label class="switch">
                            <input type="checkbox" name="working_status" id="working_status" checked >
                            <span class="slider round"></span>
                        </label>
          </div>

          <div class="form-group row custom_form_group{{ $errors->has('proj_deadling') ? ' has-error' : '' }}">
            <label class="col-sm-3 control-label">Working Deadline:<span class="req_star">*</span></label>
            <div class="col-sm-7">
              <input type="date" class="form-control datepicker" id="starting_date" name="proj_deadling"
              value="{{ Carbon\Carbon::now()->format('m/d/Y') }}" required>

              @if ($errors->has('proj_deadling'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('proj_deadling') }}</strong>
              </span>
              @endif
            </div>
          </div>

          <div class="form-group row custom_form_group">
            <label class="col-sm-3 control-label">Color Code </label>
            <div class="col-sm-4">
              <input type="text" class="form-control" name="color_code" id="color_code" onkeyup="getColorFromHexaColorCode()" minlength="3" maxlength="6"  value="AA0231" >

            </div>
            <label class="col-sm-3 control-label"  id="color_code_label" style="background-color:#AA0231"> </label>
          </div>

          <div class="form-group row custom_form_group">
            <label class="col-sm-3 control-label">Description </label>
            <div class="col-sm-7">
              <textarea name="proj_description" class="form-control" placeholder="Project Description"></textarea>
            </div>
          </div>

          <div class="form-group row custom_form_group{{ $errors->has('proj_main_thumb') ? ' has-error' : '' }}">
            <label class="col-sm-3 control-label">Agreement File:<span class="req_star">*</span></label>
            <div class="col-sm-4">
              <div class="input-group">
                <span class="input-group-btn">
                  <span class="btn btn-default btn-file btnu_browse">
                    Browse… <input type="file" name="agreement_file" id="imgInp8">
                  </span>
                </span>
                <input type="text" class="form-control" readonly>
              </div>
              @if ($errors->has('agreement_file'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('agreement_file') }}</strong>
              </span>
              @endif
            </div>
          </div>


        </div>
        <div class="card-footer card_footer_button text-center">
          <button type="submit"  class="btn btn-primary waves-effect">SAVE</button>
        </div>
      </div>
    </form>
  </div>
  <div class="col-md-2"> <a data-toggle="modal" data-target="#project_report_modal" class="btn btn-md btn-primary waves-effect card_top_button">Project Report</a> </div>
</div>

<!--   List of Project -->
<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col-md-8">
            <h3 class="card-title card_top_title"><i class="fab fa-gg-circle mr-2"></i>Project List</h3>
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
                    <th>S.N</th>
                    <th>Project Name</th>
                    <th>ID</th>
                    <th>Starting Date</th>
                    <th>Deadline</th>
                    <th>Pro.Code</th>
                    <th>Location</th>
                    <th>Working Status</th>
                    <th>Status</th>
                    <th>Color Code</th>
                    <th>Project Value</th>
                    <th>Manage</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($all as $item)
                  <tr>
                    <td>{{ $loop->iteration }}</td>
                     <td>{{ $item->proj_name }}</td>
                    <td>{{ $item->proj_id }}</td>
                    <td>{{ Carbon\Carbon::parse($item->starting_date)->format('d-m-Y') }}</td>
                    <td>{{ Carbon\Carbon::parse($item->proj_deadling)->format('d-m-Y') }}</td>
                    <td>{{ $item->proj_code }}</td>
                    <td>{{ $item->address }}</td>
                    <td>{{ $item->working_status == 1 ? 'Running' :'Completed' }}</td>
                    <td>{{ $item->status == 1 ? 'Active' :'Inactive' }}</td>
                    <td class="td__project_code" style="background-color: #{{$item->color_code}}"> {{$item->color_code  }} </td>
                    <td>{{ $item->proj_budget }}</td>
                    <td>
                      @can('project-edit')
                      <a href="{{ route('project-info-edit',[$item->proj_id]) }}" title="edit"><i class="fa fa-pencil-square fa-lg edit_icon"></i></a>
                      @endcan
                      <a href="{{ route('project-info-view',[$item->proj_id]) }}" title="view"><i class="fas fa-eye fa-lg view_icon"></i></a>
                    </td>
                  </tr>
                  @empty
                  <p class="data_not_found">Data Not Found</p>
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

<!--   ADD New Plot Under Project -->
<div class="modal fade" id="addPlotModalUI" tabindex="-1" role="dialog"   aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"> Create New Plot Under Project </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('project.new.plot.insert.request') }}">
                    @csrf
                    <div class="form-group row custom_form_group">
                        <label class="col-sm-4 control-label">Project Name:</label>
                        <div class="col-sm-8">
                            <select class="form-control" name="project_auto_id" required>
                                <option value="">Select Project</option>
                                @foreach($all as $proj)
                                <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row custom_form_group{{ $errors->has('role_name') ? ' has-error' : '' }}" >
                        <label class="control-label col-md-4">Plot Name:<span class="req_star">*</span></label>
                        <div class="col-md-8">
                            <input type="text" placeholder="Input Plot Name" class="form-control keyup-characters" id="plot_name" name="plot_name"   required>
                        </div>
                    </div>
                    <button type="submit"   class="btn btn-primary waves-effect">SAVE PLOT</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!--   Project Report -->
<div class="modal fade" id="project_report_modal" tabindex="-1" role="dialog"   aria-hidden="true">
  <div class="modal-dialog" role="document">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title"> Project List Report</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span>
              </button>
          </div>
          <div class="modal-body">
                  <div class="form-group row custom_form_group">
                      <label class="col-sm-4 control-label">Working Status</label>
                      <div class="col-sm-8">
                          <select class="select-control" name="project_working_status" id="project_working_status">
                              <option value="">All</option>
                              <option value="1">Running</option>
                              <option value="0">Completed</option>
                          </select>
                      </div>
                  </div>
                  <div class="form-group row custom_form_group">
                    <label class="col-sm-4 control-label">Status:</label>
                    <div class="col-sm-8">
                        <select class="select-control" name="project_status" id="project_status">
                            <option value="">All</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>


                        </select>
                    </div>
                </div>
                  <button type="submit" onclick="showProjectListReport()"   class="btn btn-primary waves-effect">Show</button>
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
        proj_name: {
          required: true,

        },
        starting_date: {
          required: true,
        },
        address: {
          required: true,
        },
        proj_code: {
          required: true,
        },
        proj_budget: {
          required: true,
          number: true,
          maxlength: 20,
        },
      },

      messages: {
        proj_name: {
          required: "You Must Be Input This Field!",
        },
        starting_date: {
          required: "You Must Be Select This Field!",
        },
        address: {
          required: "You Must Be Input This Field!",
        },
        proj_code: {
          required: "You Must Be Input This Field!",
        },
        proj_budget: {
          required: "Please Input This Field!",
          number: "You Must Be Input Number!",
          max: "You Must Be Input Maximum Length 11!",
        },
      },
    });
  });

  function getColorFromHexaColorCode(){

    var color_lbl = document.getElementById('color_code_label');
    var color_code = $('#color_code').val();
    color_lbl.style.backgroundColor = '#'+color_code;
  }

  function showProjectListReport(){
    var project_working_status = $('#project_working_status').val();
    var project_status = $('#project_status').val();

              // Create the query string with parameters
              const queryString = new URLSearchParams({
                  project_working_status: project_working_status,
                  project_status: project_status,
              }).toString();

              var parameterValue = queryString; // Set parameter value
              var url = "{{ route('project.information.report', ':parameter') }}";
              url = url.replace(':parameter', parameterValue);
              window.open(url, '_blank');
  }

</script>

@endsection
