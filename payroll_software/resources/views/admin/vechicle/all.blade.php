@extends('layouts.admin-master')
@section('title')New Vechicle @endsection
@section('content')
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">New Vechicle Information</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active"> Vechicle</li>
        </ol>
    </div>
</div>
<!-- add division -->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        @if(Session::has('success'))
        <div class="alert alert-success alertsuccess" role="alert">
            <strong> {{ Session::get('success')}} </strong>
        </div>
        @endif
        @if(Session::has('error'))
        <div class="alert alert-warning alerterror" role="alert">
            <strong> {{ Session::get('error') }} </strong>
        </div>
        @endif
    </div>
    <div class="col-md-2"></div>
</div>

<div class="row">
    <div class="col-md-12">
        <form class="form-horizontal" id="vechicleForm-validation" action="{{ route('insert-new.vechicle') }}"
            method="post" enctype="multipart/form-data">
            @csrf
            <div class="card">

                <div class="card-body card_form" style="padding-top: 0;">
                    <div class="row"> 
                        <div class="col-md-6">
                            <div class="form-group row custom_form_group">
                                <label class="col-sm-3 control-label">Owner Type  :<span
                                        class="req_star">*</span></label>
                                <div  class="col-sm-6">
                                    <select class="form-select" name="company_id" required>
                                          {{-- <option value="1">Asloob Internation Contracting Comany</option>
                                        <option value="2">Asloob Bedda Contracting Comany </option>
                                        <option value="3">Bedaa General Contracting Comany</option>
                                        <option value="4">Other </option> --}}
                                         @foreach ($veh_owners as $item)
                                        <option value="{{ $item->veh_own_id }}">{{ $item->veh_own_title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                     <button class="btn btn-sm btn-primary" data-toggle="modal"   data-target="#vehOwnerModal">Add Owner</button>

                                </div>
                            </div>
                            <div class="form-group row custom_form_group">
                                <label class="control-label col-md-3">Vehicle Name:<span
                                        class="req_star">*</span></label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="veh_name" value="{{ old('veh_name') }}"
                                        placeholder="Input Vechicle Name" required>
                                </div>
                                <div class="col-md-3">
                                </div>
                            </div>
                            <div class="form-group row custom_form_group">
                                <label class="col-sm-3 control-label">Vechicle Type:<span
                                        class="req_star">*</span></label>
                                <div  class="col-sm-6">
                                    <select class="form-select" name="veh_type_id" required>
                                        <option value="">Select Vechicle Type</option>
                                        {{-- <option value="1">Jeep</option>
                                        <option value="2">Pickup</option>
                                        <option value="3">Bus</option>
                                        <option value="4">Truck</option> --}}
                                         @foreach ($veh_types as $item)
                                        <option value="{{ $item->veh_typ_id }}">{{ $item->veh_type_title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                        <button class="btn btn-sm btn-primary"   data-toggle="modal" data-target="#vehTypeModal">Add Type</button>

                                </div>
                            </div>
                            <div class="form-group row custom_form_group">
                                <label class="control-label col-md-3">Plate Number:</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="veh_plate_number"
                                        value="{{ old('veh_plate_number') }}" placeholder="Input Vechicle Plate Number" required>
                                </div>
                                <div class="col-md-3"></div>
                            </div>
                            <div class="form-group row custom_form_group">
                                <label class="control-label col-md-3">Model Number:</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="veh_model_number"
                                        value="{{ old('veh_model_number') }}" placeholder="Input Vechicle Model Number">
                                </div>
                                <div class="col-md-3"></div>
                            </div>
                            <div class="form-group row custom_form_group">
                                <label class="control-label col-md-3">Brand Name:</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="veh_brand_name"
                                        value="{{ old('veh_brand_name') }}" placeholder="Input Vechicle Brand Name">
                                </div>
                                <div class="col-md-3"></div>
                            </div>
                            <div class="form-group row custom_form_group">
                                <label class="control-label col-md-3">Licence Number:<span
                                    class="req_star">*</span></label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="veh_licence_no"
                                        value="{{ old('veh_licence_no') }}" placeholder="Input Vechicle Licence Number">
                                </div>
                                <div class="col-md-3"></div>
                            </div>
                            <div class="form-group row custom_form_group">
                                <label class="control-label col-md-3">Insurrance Date:</label>
                                <div class="col-md-6">
                                    <input type="date" class="form-control" name="veh_insurrance_date"
                                        value="{{ date('Y-m-d') }}" placeholder="Input Purchase Date">
                                </div>
                                <div class="col-md-3"></div>
                            </div>
                            <div class="form-group row custom_form_group">
                                <label class="control-label col-md-3"> Price </label>
                                <div class="col-md-6">
                                    <input type="number" class="form-control" name="veh_price" value="0"
                                        placeholder="Input Vechicle Price">
                                </div>
                                <div class="col-md-3"></div>
                            </div>
                            <div class="form-group row custom_form_group">
                                <label class="control-label col-md-3">Meter Reading  </label>
                                <div class="col-md-6">
                                    <input type="number" class="form-control" name="veh_present_metar"
                                        value="0" placeholder="Input Vechicle Present Meter" required>
                                </div>
                                <div class="col-md-3"></div>
                            </div>
                            <div class="form-group row custom_form_group">
                                <label class="control-label col-md-3">Color </label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="veh_color" value="{{ old('veh_color') }}"
                                        placeholder="Input Vechicle Color">
                                </div>
                                <div class="col-md-3"></div>
                            </div>
                            
                        </div>
                        <div class="col-md-6">
                            
                            <div class="form-group row custom_form_group">
                                <label class="control-label col-md-2">Purchase </label>
                                <div class="col-md-6">
                                    <input type="date" class="form-control" name="veh_purchase_date"
                                        value="{{ date('Y-m-d') }}" placeholder="Input Purchase Date">
                                </div>
                                <div class="col-md-3"></div>
                            </div>
                            <div
                                class="form-group row custom_form_group{{ $errors->has('veh_ins_expire_date') ? ' has-error' : '' }}">
                                <label class="control-label col-md-2">Insurance Exp.  </label>
                                <div class="col-md-6">
                                    <input type="date" class="form-control" name="veh_ins_expire_date"
                                        value="{{ date('Y-m-d') }}" value="{{old('veh_ins_expire_date')}}">
                                    @if ($errors->has('veh_ins_expire_date'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('veh_ins_expire_date') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="col-md-3"></div>
                            </div>
                            <div class="form-group row custom_form_group">
                                <label class="control-label col-md-2">Fitness Date  </label>
                                <div class="col-md-6">
                                    <input type="date" class="form-control" name="veh_ins_renew_date"
                                        value="{{ date('Y-m-d') }}" >
                                </div>
                                <div class="col-md-3"></div>
                            </div>
                            <div class="form-group row custom_form_group">
                                <label class="control-label col-md-2">Reg. Exp:<span
                                        class="req_star">*</span></label>
                                <div class="col-md-6">
                                    <input type="date" class="form-control" name="veh_reg_expire_date"
                                        value="{{ date('Y-m-d') }}"  >
                                </div>
                                <div class="col-md-3"></div>
                            </div>
                            <div class="form-group row custom_form_group">
                                <label class="control-label col-md-2">Reg. Renewal
                                    Date:<span class="req_star">*</span></label>
                                <div class="col-md-6">
                                    <input type="date" class="form-control" name="veh_reg_renew_date"
                                        value="{{ date('Y-m-d') }}"  >
                                </div>
                                <div class="col-md-3"></div>
                            </div> 
                            <div class="form-group row custom_form_group">
                                <label class="control-label col-md-2">Remarks:<span
                                        class="req_star">*</span></label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="remarks" value="{{ old('remarks') }}"
                                        placeholder="Remarks Here">
                                </div>
                                <div class="col-md-3"></div>
                            </div>
                            <br>
                            <div class="form-group row custom_form_group">
                                <label class="col-sm-2 control-label">Insurance Cert.</label>
                                <div class="col-sm-3">
                                    <div class="input-group">
                                        <span class="input-group-btn">
                                            <span class="btn btn-default btn-file btnu_browse">
                                                Browse… <input type="file" name="veh_ins_certificate" id="imgInp4">
                                            </span>
                                        </span>
                                        <input type="text" class="form-control" readonly>
                                    </div>
                                </div>
                                <div class="col-sm-5">
                                    <img id='img-upload4' class="upload_image" />
                                </div>
                            </div>
                            <div class="form-group row custom_form_group">
                                <label class="col-sm-2 control-label">Reg. Cert.</label>
                                <div class="col-sm-3">
                                    <div class="input-group">
                                        <span class="input-group-btn">
                                            <span class="btn btn-default btn-file btnu_browse">
                                                Browse… <input type="file" name="veh_reg_certificate" id="imgInp2">
                                            </span>
                                        </span>
                                        <input type="text" class="form-control" readonly>
                                    </div>
                                </div>
                                <div class="col-sm-5">
                                    <img id='img-upload2' class="upload_image" />
                                </div>
                            </div>
                            <div class="form-group row custom_form_group">
                                <label class="col-sm-2 control-label">Veh. Photo:</label>
                                <div class="col-sm-3">
                                    <div class="input-group">
                                        <span class="input-group-btn">
                                            <span class="btn btn-default btn-file btnu_browse">
                                                Browse… <input type="file" name="veh_photo" id="imgInp3">
                                            </span>
                                        </span>
                                        <input type="text" class="form-control" readonly>
                                    </div>
                                </div>
                                <div class="col-sm-5">
                                    <img id='img-upload3' class="upload_image" />
                                </div>
                            </div>
                        </div>
                        <div class="card-footer card_footer_button text-center">
                            <button type="submit" class="btn btn-primary waves-effect">SAVE</button>
                        </div>
                    </div>
                </div>
        </form>
    </div>
</div>

<!-- Vehicle list -->
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <div class="row">

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
                                        <th>Company Name</th>
                                        <th>Name,Brand</th>                     
                                        <th>Plate No</th>
                                        <th>Reg. No</th> 
                                        <th>Insu. Exp.</th> 
                                        <th>Fitness</th>
                                        <th>Driver</th>
                                        <th>File</th>
                                        <th>Status</th>
                                        <th>Manage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($all as $item)
                                     @php
                                      
                                        $expire_date = $item->veh_ins_expire_date;
                                        $fitness = $item->veh_ins_renew_date;

                                        $current = Carbon\Carbon::now()->format('Y-m-d');
                                        $current = new DateTime($current);

                                        $expire_date = new DateTime($expire_date);
                                        $ins_diffDate = date_diff($current,$expire_date);
                                        $ins_diff =  $ins_diffDate->format("%R%a days");
                                        $ins_days =  $ins_diffDate->format("%R%a");

                                        $fitness = new DateTime($fitness);
                                        $fit_diffDate = date_diff($current,$fitness);
                                        $fit_diff =  $fit_diffDate->format("%R%a days");
                                        $fit_days =  $fit_diffDate->format("%R%a");
                                        
                                    @endphp
                                    <tr>
                                        <td>
                                            @if ($item->company_id == 1)
                                                Asloob Int. Co.
                                            @elseif ($item->company_id == 2)
                                                Asloob Bedda Co.
                                            @elseif ($item->company_id == 3)
                                                Bedaa General Co
                                            @elseif ($item->company_id == 4)
                                                Other Employee
                                            @endif
                                        </td>
                                        <td> {{ $item->veh_name }}, {{ $item->veh_brand_name }}  </td>                                        
                                        <td> {{ $item->veh_plate_number }} </td>
                                        <td> {{ $item->veh_licence_no }} </td> 
                                        
                                        
                                        @if($ins_days >= 1 && $ins_days <= 30)
                                            <td> <span class="days" style="background: red; color:black">{{ $ins_diff }}</span> <br> {{ $item->veh_ins_expire_date }}</td>
                                        @elseif($ins_days <= 0)
                                            <td> <span class="days" style="background: red; color:black">0 Day</span> <br> {{ $item->veh_ins_expire_date }} </td>
                                        @elseif($ins_days <= 60)
                                            <td> <span class="days" style="background: yellow; color: #222">{{ $ins_diff }}</span> <br> {{ $item->veh_ins_expire_date }} </td>
                                        @elseif($ins_days <= 90)
                                            <td> <span class="days" style="background: green; color:black">{{ $ins_diff }}</span> <br> {{ $item->veh_ins_expire_date }}</td>
                                        @else
                                            <td> <span>{{ $ins_diff }}</span> <br> {{ $item->veh_ins_expire_date }}</td>
                                        @endif

                                        @if($fit_days >= 1 && $fit_days <= 30)
                                            <td> <span class="days" style="background: red; color:black">{{ $fit_diff }}</span> <br> {{ $item->veh_ins_renew_date }}</td>
                                        @elseif($fit_days <= 0)
                                            <td> <span class="days" style="background: red; color:black">0 Day</span> <br> {{ $item->veh_ins_renew_date }} </td>
                                        @elseif($fit_days <= 60)
                                            <td> <span class="days" style="background: yellow; color: #222">{{ $fit_diff }}</span> <br> {{ $item->veh_ins_renew_date }} </td>
                                        @elseif($fit_days <= 90)
                                            <td> <span class="days" style="background: green; color:black">{{ $fit_diff }}</span> <br> {{ $item->veh_ins_renew_date }}</td>
                                        @else
                                            <td> <span>{{ $fit_diff }}</span> <br> {{ $item->veh_ins_renew_date }}</td>
                                        @endif
                                        
                                        
                                        <td>
                                            @if($item->driver_id == NULL)
                                            Not Assigned
                                            @else
                                            {{ $item->employee->employee_id ?? '' }} , {{ $item->employee->employee_name
                                            ?? '' }}
                                            @endif
                                        </td>
                                        <td>{{$item->veh_reg_certificate != null? 'Reg. Found' : 'Reg. Not Found' }} <br> {{$item->veh_ins_certificate != null? 'Insu. Found' : 'Insu. Not Found' }} </td>
                                        <td>
                                            @if ($item->status == 1)
                                                <span class="badge badge-pill badge-success">Active</span>
                                            @else
                                                <span class="badge badge-pill badge-danger">In Active</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('edit-vechicle',$item->veh_id) }}" title="edit"><i
                                                    class="fa fa-pencil-square fa-lg edit_icon"></i></a>

                                            <a href="{{ route('delete-vechicle',$item->veh_id) }}" title="delete"
                                                id="delete" title="delete data"><i class="fa fa-trash fa-lg delete_icon"></i></a>
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

<!-- Vehicle Owner Modal -->
<div class="modal fade" id="vehOwnerModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <form >
      {{-- @csrf method="POST" action="{{ route('vehicle.saveOwner') }}" --}}
      <input type="hidden" name="veh_own_id" id="veh_own_id">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Add Vehicle Owner</h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <label>Owner Title</label>
          <input type="text" name="veh_own_title"  id="veh_own_title" class="form-control" required>
        </div>
        <div class="modal-footer">
          <button type="button" id="saveOwnerBtn" class="btn btn-primary">Save Owner</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Vehicle Type Modal -->
<div class="modal fade" id="vehTypeModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <form >
      {{-- @csrf method="POST" action="{{ route('vehicle.saveType') }}" --}}
      <input type="hidden" name="veh_typ_id" id="veh_typ_id">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Add Vehicle Type</h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <label>Type Title</label>
          <input type="text" name="veh_type_title" id="veh_type_title" class="form-control" required>
        </div>
        <div class="modal-footer">
          <button type="button" id="saveTypeBtn" class="btn btn-primary">Save Type</button>
        </div>
      </div>
    </form>
  </div>
</div>


<!-- script area -->
<script type="text/javascript">
    /* form validation */
    $(document).ready(function () {
        
        
        
          $('#saveOwnerBtn').click(function () {
 
            const title = $('#veh_own_title').val();
            const id = $('#veh_own_id').val();
            
              if(title == null || title == "")
              {
                alert("Please Enter Information")
                return;
              }

            $.post("{{ route('vehicle.saveOwner') }}", {
            veh_own_title: title,
            veh_own_id: id
            }, function (response) {

                $('#vehOwnerModal').modal('hide');
                $('#veh_own_title').val('');
            location.reload();
            }).fail(function (xhr) {
                 alert('Error saving owner');
            });
        });

        $('#saveTypeBtn').click(function () {
            const title = $('#veh_type_title').val();
            const id = $('#veh_typ_id').val();
            
              if(title == null || title == "")
              {
                alert("Please Enter Information")
                return;
              }

            $.post("{{ route('vehicle.saveType') }}", {
            veh_type_title: title,
            veh_typ_id: id
            }, function (response) {

            $('#vehTypeModal').modal('hide');
            $('#veh_type_title').val('');
              location.reload();
            }).fail(function (xhr) {
             alert('Error saving type');
            });
        });



        $("#vechicleForm-validation").validate({

            rules: {
                veh_name: {
                    required: true,
                },
                veh_plate_number: {
                    required: true,
                },
                veh_type: {
                    required: true,
                },
                veh_brand_name:{
                    required:true,
                }
                // veh_licence_no: {
                //     required: true,
                // },
                /*
                veh_purchase_date: {
                    required: true,
                },
                veh_ins_expire_date: {
                    required: true,
                },
                veh_reg_expire_date: {
                    required: true,
                },
                veh_ins_certificate: {
                    required: true,
                },
                veh_reg_certificate: {
                    required: true,
                },
                veh_present_metar: {
                    required: true,
                    number: true,
                    maxlength: 9,
                },
                veh_price: {
                    required: true,
                    number: true,
                    maxlength: 15,
                },  */
            },

            messages: {
                veh_name: {
                    required: "You Must Be Input This Field!",
                },
                veh_plate_number:{
                    required: "You Must Be Input This Field!",
                },
                veh_type:{
                    required: "You Must Be Input This Field!",
                },
                veh_brand_name:{
                    required: "You Must Be Input This Field!",
                }
                // veh_licence_no:{
                //     required: "You Must Be Input This Field!",
                // }

                /*
                veh_color: {
                    required: "You Must Be Input This Field!",
                },
                veh_purchase_date: {
                    required: "You Must Be Select This Field!",
                },
                veh_ins_expire_date: {
                    required: "You Must Be Select This Field!",
                },
                veh_reg_expire_date: {
                    required: "You Must Be Select This Field!",
                },
                veh_ins_certificate: {
                    required: "You Must Be Provide Your Insurance Certificate Here!",
                },
                veh_reg_certificate: {
                    required: "You Must Be Provide Your Registration Certificate Here!",
                },
                veh_price: {
                    required: "Please Input This Field!",
                    number: "You Must Be Input Number!",
                    max: "You Must Be Input Maximum Length 15!",
                },
                veh_present_metar: {
                    required: "Please Input This Field!",
                    number: "You Must Be Input Number!",
                    max: "You Must Be Input Maximum Length 9!",
                },
                */
            },
        });
    });

</script>


@endsection
