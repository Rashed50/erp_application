@extends('layouts.admin-master')
@section('title') Edit District @endsection
@section('content')

<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Edit District</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li><a href="{{ route('add-district') }}">Add District</a></li>
            <li class="active">Edit</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        @if(Session::has('error_update'))
          <div class="alert alert-warning alerterror" role="alert">
             <strong>Opps!</strong> please try again.
          </div>
        @endif
        @if(Session::has('already_exit'))
          <div class="alert alert-warning alerterror" role="alert">
             <strong>Opps!</strong> This District Already Exit!.
          </div>
        @endif
    </div>
</div>

<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-6">
      <form class="form-horizontal" id="registration" action="{{ route('update-district') }}" method="post">
        @csrf
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-8">
                        <h3 class="card-title card_top_title"><i class="fab fa-gg-circle mr-2"></i>Edit District</h3>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="card-body card_form" style="padding-top: 0;">
              <div class="form-group custom_form_group row{{ $errors->has('country_id') ? ' has-error' : '' }}">
                  <label class="control-label col-sm-3">Country Name:</label>
                  <div class="col-sm-9">
                    <select class="form-control" name="country_id" required>
                        <option value="">Select Country</option>
                        @foreach($allCountry as $country)
                        <option value="{{ $country->id }}" {{ $country->id == $edit->country->id ? 'selected':'' }}>{{ $country->country_name }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('country_id'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('country_id') }}</strong>
                        </span>
                    @endif
                  </div>
              </div>

              <div class="form-group row custom_form_group{{ $errors->has('division_id') ? ' has-error' : '' }}">
                  <label class="control-label col-md-3">Division Name:</label>
                  <input type="hidden" name="id" value="{{ $edit->district_id }}">
                  <div class="col-md-9">
                    <select class="form-control" name="division_id" required>
                        <option value="{{ $edit->division->division_id }}">{{ $edit->division->division_name }}</option>
                    </select>
                    @if ($errors->has('division_id'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('division_id') }}</strong>
                        </span>
                    @endif
                  </div>
              </div>

              <div class="form-group custom_form_group row{{ $errors->has('district_name') ? ' has-error' : '' }}">
                  <label class="control-label col-md-3">District Name:<span class="req_star">*</span></label>
                  <div class="col-md-9">
                    <input type="text" placeholder="please enter district name" class="form-control" id="district_name" name="district_name" value="{{ $edit->district_name }}" required>
                    @if ($errors->has('district_name'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('district_name') }}</strong>
                        </span>
                    @endif
                  </div>
              </div>
            </div>
            <div class="card-footer card_footer_button text-center">
                <button type="submit" class="btn btn-primary waves-effect">UPDATE</button>
            </div>
        </div>
      </form>
    </div>
    <div class="col-md-3"></div>
</div>


<script src="{{asset('contents/admin')}}/assets/js/ajax/jquery-ajax.min.js"></script>
<script type="text/javascript">
  $(document).ready(function() {
    $('select[name="country_id"]').on('change', function(){
        var country_id = $(this).val();
        if(country_id) {
            $.ajax({
                url: "{{  url('/admin/division/ajax') }}/"+country_id,
                type:"GET",
                dataType:"json",
                success:function(data) {
                   var d =$('select[name="division_id"]').empty();
                      $.each(data, function(key, value){

                          $('select[name="division_id"]').append('<option value="'+ value.division_id +'">' + value.division_name + '</option>');

                      });

                },

            });
        } else{

        }

    });

});

</script>

@endsection
