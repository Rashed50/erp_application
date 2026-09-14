@extends('layouts.admin-master')
@section('title')Expense Head @endsection
@section('content')
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Create Expense Head</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active"> Expense Head</li>
        </ol>
    </div>
</div>
<!-- Session Flass Message -->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        @if(Session::has('success'))
          <div class="alert alert-success alertsuccess" role="alert">
             <strong> {{ Session::get('success')}}</strong>  
          </div>
        @endif        
        @if(Session::has('error'))
          <div class="alert alert-warning alerterror" role="alert">
            <strong> {{ Session::get('error')}}</strong> 
          </div>
        @endif
    </div>
</div>

<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
      <form class="form-horizontal" id="registration" action="{{ route('insert-cost-type') }}" method="post">
        @csrf
        <div class="card">            
            <div class="card-body card_form" >
              <div class="form-group row custom_form_group">
                  <label class="control-label col-sm-3"  >Expense Head Name:<span class="req_star">*</span></label>
                  <div class="col-sm-6">
                    <input type="text" class="form-control" name="cost_type_name" autofocus value="{{ old('cost_type_name') }}" placeholder="Expense Head Name" required>
                  </div>
                  <div class="col-sm-3">
                    <button type="submit" class="btn btn-primary waves-effect">SAVE</button>
                  </div>
              </div>
            </div>            
        </div>
      </form>
    </div>
    <div class="col-md-2"></div>
</div>

<!-- Expense Head list -->
<div class="row">
  <div class="col-md-2"></div>
  <div class="col-lg-8">
      <div class="card">           
          <div class="card-body">
              <div class="row">
                
                <div class="col-12">
                      <div class="table-responsive">
                          <table id="alltableinfo" class="table table-bordered custom_table mb-0">
                              <thead>
                                  <tr>
                                      <th>Srl No</th>
                                      <th>Expense Head Name</th>
                                      <th>Manage</th>
                                  </tr>
                              </thead>
                              <tbody>
                                @foreach($all as $item)
                                  <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->cost_type_name }}</td>
                                    <td> 
                                      <a href="{{ route('edit-cost-type',$item->cost_type_id) }}" title="edit" ><i class="fa fa-pencil-square fa-lg edit_icon"></i></a>
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
  <div class="col-md-2"></div>
</div>


<!-- Expense Head Update Modal -->
{{-- <div class="modal fade" id="expense_head_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Update Expense Head</h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-2"></div>
          <div class="col-md-8">
            <form class="form-horizontal" id="registration" action="{{ route('insert-cost-type') }}" method="post">
              @csrf
              <div class="card">            
                  <div class="card-body card_form" >
                    <div class="form-group row custom_form_group">
                        <label class="control-label col-sm-3"  >Expense Head Name:<span class="req_star">*</span></label>
                        <div class="col-sm-6">
                          <input type="text" class="form-control" name="cost_type_name" autofocus value="{{ old('cost_type_name') }}" placeholder="Expense Head Name" required>
                        </div>
                        <div class="col-sm-3">
                          <button type="submit" class="btn btn-primary waves-effect">SAVE</button>
                        </div>
                    </div>
                  </div>            
              </div>
            </form>
          </div>
          <div class="col-md-2"></div>
      </div>
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Update</button>
      </div>
    </div>
  </div>
</div> --}}


 

@endsection
