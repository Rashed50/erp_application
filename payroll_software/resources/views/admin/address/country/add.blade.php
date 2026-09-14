
@extends('layouts.admin-master')
@section('title') Add Country @endsection
@section('content')

<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title"> Country</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active"> Country</li>
        </ol>
    </div>
</div>

<!-- add division -->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        @if(Session::has('success_add'))
          <div class="alert alert-success alertsuccess" role="alert">
             <strong>Successfully!</strong> Added Country.
          </div>
        @endif
        @if(Session::has('success_update'))
          <div class="alert alert-success alertsuccess" role="alert">
             <strong>Successfully!</strong> Update Country.
          </div>
        @endif
        @if(Session::has('delete_update'))
          <div class="alert alert-success alertsuccess" role="alert">
             <strong>Successfully!</strong> Update Country.
          </div>
        @endif
        @if(Session::has('success_error'))
          <div class="alert alert-warning alerterror" role="alert">
             <strong>Opps!</strong> please try again.
          </div>
        @endif
    </div>
</div>

<div class="row">
    <div class="col-md-2">

    </div>
    <div class="col-md-6">
      <form class="form-horizontal" action="{{ route('insert-country') }}" method="post" onsubmit="return countryFormValidation()">
        @csrf
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-8">
                        <h3 class="card-title card_top_title"><i class="fab fa-gg-circle"></i> New ountry</h3>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="card-body card_form" style="padding-top: 0;">
              <div class="form-group custom_form_group{{ $errors->has('country_name') ? ' has-error' : '' }}">
                  <label class="control-label d-block" style="text-align: left;">Country Name:<span class="req_star">*</span></label>
                  <div>
                    <input type="text" placeholder="please enter country name" class="form-control keyup-characters" id="country_name" name="country_name" value="{{old('country_name')}}">
                    {{-- <span class="error"></span> --}}
                    @if ($errors->has('country_name'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('country_name') }}</strong>
                        </span>
                    @endif
                  </div>
              </div>


            </div>
            <div class="card-footer card_footer_button text-center">
                <button type="submit" class="btn btn-primary waves-effect">SAVE</button>
            </div>
        </div>
      </form>
    </div>
    <div class="col-md-2"></div>
</div>



<script type="text/javascript">

function countryFormValidation(){
    var country_name = $("#country_name").val();

    if(country_name == ""){
      $("span[class='error']").text('please enter country name!');
      return false;

    }

}
 </script>

@endsection
