@extends('layouts.admin-master')
@section('title') Project Information @endsection
@section('content')

<div class="row bread_part">
  <div class="col-sm-12 bread_col">
    <h4 class="pull-left page-title bread_title">Cost Control Information</h4>
    <ol class="breadcrumb pull-right">
      <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
      <li class="active">Cost Control</li>
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


<div class="row">
  <div class="col-md-3">
        <a data-toggle="modal" data-target="#activity_element_ui_modal" class="btn btn-md btn-primary waves-effect card_top_button">New Activity Element</a><hr/>
  </div>
  <div class="col-md-3">
        <a data-toggle="modal" data-target="#activity_name_ui_modal" class="btn btn-md btn-primary waves-effect card_top_button">New Activity</a><hr/>
  </div>
  <div class="col-md-3"> 
   </div>
</div>
 
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
                   
                   
                </thead>
                <tbody>
                  
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Activity Element Insert Mdal -->
<div class="modal fade" id="activity_element_ui_modal" tabindex="-1" role="dialog"   aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"> Create New Activity Element </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('costcontrol.activity.element.insert.request') }}">
                    @csrf 
                    <div class="form-group row custom_form_group{{ $errors->has('activity_element_name') ? ' has-error' : '' }}" >
                        <label class="control-label col-md-4">Activity Element:<span class="req_star">*</span></label>
                        <div class="col-md-8">
                            <input type="text" placeholder="Input Activity Element Name" class="form-control keyup-characters" id="activity_element_name" name="activity_element_name"   required>
                        </div>
                    </div> 

                    <!-- <div class="form-group row custom_form_group{{ $errors->has('role_name') ? ' has-error' : '' }}" >
                        <label class="control-label col-md-4">Activity Element Code:<span class="req_star">*</span></label>
                        <div class="col-md-8">
                            <input type="text" placeholder="Input Activity Element Code" class="form-control keyup-characters" id="activity_element_code" name="activity_element_code"   required>
                        </div>
                    </div>                     -->
                    <button type="submit"   class="btn btn-primary waves-effect">SAVE</button>
                </form>
            </div>
        </div>
    </div>
</div>
 
<!-- Activity Name Insert M0dal -->
<div class="modal fade" id="activity_name_ui_modal" tabindex="-1" role="dialog"   aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"> Create New Activity </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('costcontrol.activity.name.insert.request') }}">
                    @csrf 
                    <div class="form-group row custom_form_group{{ $errors->has('activity_element_name') ? ' has-error' : '' }}" >
                        <label class="control-label col-md-4">Activity Name:<span class="req_star">*</span></label>
                        <div class="col-md-8">
                            <input type="text" placeholder="Input Activity Name" class="form-control keyup-characters" id="activity_name" name="activity_name"   required>
                        </div>
                    </div>                    
                    <button type="submit"   class="btn btn-primary waves-effect">SAVE</button>
                </form>
            </div>
        </div>
    </div>
</div>

  
<script type="text/javascript">
  
  $(document).ready(function() {
     
  });
</script>

@endsection