@extends('layouts.admin-master')
@section('title') Edit Banner Information @endsection
@section('content')

<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Edit Banner Information</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li><a href="{{ route('project-info') }}">Banner Information</a></li>
            <li class="active">Edit Banner</li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <form class="form-horizontal" id="registration" method="post" action="{{ route('update-banner-info') }}" enctype="multipart/form-data">
          @csrf
          <div class="card">
              <div class="card-header">
                  <div class="row">
                      <div class="col-md-8">
                          <h3 class="card-title card_top_title"><i class="fab fa-gg-circle"></i> Update Banner</h3>
                      </div>
                      <div class="col-md-4 text-right">
                          <a href="{{ route('banner-info') }}" class="btn btn-md btn-primary waves-effect card_top_button"><i class="fa fa-th"></i> Banner List</a>
                      </div>
                      <div class="clearfix"></div>
                  </div>
              </div>
              <div class="card-body card_form">
                <div class="row">
                    <div class="col-md-3"></div>
                    <div class="col-md-7">
                        @if(Session::has('error'))
                          <div class="alert alert-warning alerterror" role="alert" style="margin-left: -20px">
                             <strong>Opps!</strong> please try again.
                          </div>
                        @endif
                    </div>
                    <div class="col-md-2"></div>
                </div>

                <input type="hidden" name="id" value="{{ $edit->ban_id }}">
                <input type="hidden" name="old_img" value="{{ $edit->ban_image }}">
                <input type="hidden" name="company_id" value="{{ $comp->comp_id }}">

                <div class="form-group row custom_form_group{{ $errors->has('company_id') ? ' has-error' : '' }}">
                    <label class="col-sm-3 control-label">Company name:<span class="req_star">*</span></label>
                    <div class="col-sm-7">

                      <input type="text" placeholder="banner title" class="form-control" id="ban_title" name="ban_title" value="{{ $comp->comp_name_en }}" disabled>

                    </div>
                </div>



                <div class="form-group row custom_form_group{{ $errors->has('ban_title') ? ' has-error' : '' }}">
                    <label class="col-sm-3 control-label">Title:<span class="req_star">*</span></label>
                    <div class="col-sm-7">
                      <input type="text" placeholder="banner title" class="form-control" id="ban_title" name="ban_title" value="{{ $edit->ban_title }}" required>
                      @if ($errors->has('ban_title'))
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $errors->first('ban_title') }}</strong>
                          </span>
                      @endif
                    </div>
                </div>

                <div class="form-group row custom_form_group">
                    <label class="col-sm-3 control-label">Subtitle:</label>
                    <div class="col-sm-7">
                      <input type="text" placeholder="banner subtitle" class="form-control" id="ban_subtitle" name="ban_subtitle" value="{{ $edit->ban_subtitle }}">
                    </div>
                </div>
                <div class="form-group row custom_form_group">
                    <label class="col-sm-3 control-label">Caption:</label>
                    <div class="col-sm-7">
                      <input type="text" placeholder="banner caption" class="form-control" id="ban_caption" name="ban_caption" value="{{ $edit->ban_caption }}">
                    </div>
                </div>
                <div class="form-group row custom_form_group">
                    <label class="col-sm-3 control-label">Description:</label>
                    <div class="col-sm-7">
                      <textarea name="ban_description" class="form-control" placeholder="banner description">{{ $edit->ban_description }}</textarea>
                    </div>
                </div>
                <!-- <div class="form-group row custom_form_group{{ $errors->has('company_id') ? ' has-error' : '' }}">
                    <label class="col-sm-3 control-label">Company Name:</label>
                    <div class="col-sm-7">
                      <select class="form-control" name="company_id" required>
                        <option value="">select your company</option>
                        <option value="1">Creative Shaper</option>
                        <option value="2">Creative It</option>
                      </select>
                      @if ($errors->has('company_id'))
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $errors->first('company_id') }}</strong>
                          </span>
                      @endif
                    </div>
                </div> -->
                <div class="form-group row custom_form_group{{ $errors->has('ban_image') ? ' has-error' : '' }}">
                    <label class="col-sm-3 control-label">Banner Image:</label>
                    <div class="col-sm-4">
                      <div class="input-group">
                          <span class="input-group-btn">
                              <span class="btn btn-default btn-file btnu_browse">
                                  Browse… <input type="file" name="ban_image" id="imgInp3" accept="image/x-png,image/gif,image/jpeg">
                              </span>
                          </span>
                          <input type="text" class="form-control" readonly>
                      </div>
                      @if ($errors->has('ban_image'))
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $errors->first('ban_image') }}</strong>
                          </span>
                      @endif
                      <img id='img-upload3'/>
                    </div>
                    <!-- show image -->
                    <div class="col-md-3">
                        <img src="{{ asset('uploads/banner/'.$edit->ban_image) }}" alt="banner image" class="list_image">
                    </div>
                </div>


              </div>
              <div class="card-footer card_footer_button text-center">
                  <button type="submit" id="onSubmit" onclick="formValidation();" class="btn btn-primary waves-effect">UPDATE</button>
              </div>
          </div>
        </form>
    </div>
</div>
@endsection
