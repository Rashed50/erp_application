@extends('layouts.admin-master')
@section('title') Sponsor @endsection
@section('content')
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Sponsor Information</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active"> Sponsor</li>
        </ol>
    </div>
</div>
<!-- add Sponsor -->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        @if(Session::has('success'))
          <div class="alert alert-success alertsuccess" role="alert">
          <strong> {{ Session::get('success') }} </strong>
          </div>
        @endif
        @if(Session::has('error'))
          <div class="alert alert-warning alerterror" role="alert">
          <strong> {{ Session::get('error') }} </strong>
          </div>
        @endif
    </div>
</div>

<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-6">
      <form class="form-horizontal" id="vechicleForm-validation" action="{{ route('insert-new.sponser') }}" method="post">
        @csrf
        <div class="card">
            
            <div class="card-body card_form" style="padding-top: 0;">

              <div class="form-group custom_form_group">
                  <label class="control-label d-block" style="text-align: left;">Sponsor Name:<span class="req_star">*</span></label>
                  <div>
                      <input type="text" class="form-control" name="spons_name" value="{{ old('spons_name') }}"
                      placeholder="Input New Sponsor Name">
                  </div>
              </div>

            </div>
            <div class="card-footer card_footer_button text-center">
                @can('sponser-create')
                <button type="submit" class="btn btn-primary waves-effect">SAVE</button>
                @endcan
            </div>
        </div>
      </form>
    </div>
    <div class="col-md-3"></div>
</div>

<!-- Sponsors list -->
<div class="row">
    <div class="col-lg-12">
      <div class="card">
          <div class="card-header">
              <div class="row">
                  <div class="col-md-8">
                     
                  </div>
                  <div class="clearfix"></div>
              </div>
          </div>
          <div class="card-body">
              <div class="row">
                  <div class="col-12">
                      <div class="table-responsive">
                          <table id="" class="table table-bordered custom_table mb-0">
                              <thead>
                                  <tr>
                                   <th>S.N</th>
                                    <th>Sponsor Name</th>
                                    <th>Sponsor Type</th>
                                    <th>Status</th>
                                     <th>Manage</th>
                                  </tr>
                              </thead>
                              <tbody>
                                @foreach($all as $item)
                                  <tr>
                                    <td> {{ $loop->iteration }} </td>
                                    <td> {{ $item->spons_name }} </td>
                                    <td> {{ $item->owner_name }} </td>

                                    <td> {{ $item->status == 1 ? "Active" : "Inactive" }} </td>

                                    <td>
                                    @can('sponser-edit')
                                      <a href="{{ route('edit.sponser',$item->spons_id) }}" title="edit"><i class="fa fa-pencil-square fa edit_icon"></i></a> ||
                                    @endcan
                                    @can('sponser-delete')
                                      <a href="{{ route('delete.sponser',$item->spons_id) }}" title="delete" id="delete" title="delete data"><i class="fa fa-trash fa delete_icon"></i></a>
                                    @endcan
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
<!-- script area -->
<script type="text/javascript">
/* form validation */
$(document).ready(function(){
  $("#vechicleForm-validation").validate({
    /* form tag off  */
    // submitHandler: function(form) { return false; },
    /* form tag off  */
    rules: {
      spons_name: {
        required : true,
      },
    },

    messages: {
      spons_name: {
        required : "You Must Be Input This Field!",
      },
    },
  });
});
</script>


@endsection
