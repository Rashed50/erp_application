@extends('layouts.admin-master')
@section('title') Project Image Upload @endsection
@section('content')

<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Project Image Upload</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li><a href="{{ route('project-info') }}">Project Info</a></li>
            <li class="active">Project Image Upload</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        @if(Session::has('success'))
          <div class="alert alert-success alertsuccess" role="alert">
             <strong>Successfully!</strong> Added New Multiple Project Image.
          </div>
        @endif
        @if(Session::has('success_soft'))
          <div class="alert alert-success alertsuccess" role="alert">
             <strong>Successfully!</strong> delete project information.
          </div>
        @endif

        @if(Session::has('success_update'))
          <div class="alert alert-success alertsuccess" role="alert">
             <strong>Successfully!</strong> update project information.
          </div>
        @endif

        @if(Session::has('error'))
          <div class="alert alert-warning alerterror" role="alert">
             <strong>Opps!</strong> please try again.
          </div>
        @endif
    </div>
    <div class="col-md-2"></div>
</div>
<!-- add of image -->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        <form class="form-horizontal" id="project_image_upload" method="post" action="{{ route('upload-project-muliple-image') }}" enctype="multipart/form-data">
          @csrf
          <div class="card">
              <div class="card-header">
                  <div class="row">
                      <div class="col-md-8">
                          <h3 class="card-title card_top_title"><i class="fab fa-gg-circle"></i> Project Image</h3>
                      </div>
                      <div class="clearfix"></div>
                  </div>
              </div>
              <div class="card-body card_form">

                <div class="form-group row custom_form_group">
                    <label class="col-sm-4 control-label"> Project Name:<span class="req_star">*</span></label>
                    <div class="col-sm-7">
                        <select class="form-control" name="project_id" required>
                            <option value="">Select Project Name</option>
                            @foreach($getProject as $proj)
                            <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group row custom_form_group">
                    <label class="col-sm-4 control-label"> Upload Multiple Image:<span class="req_star">*</span></label>
                    <div class="col-sm-7 project_image">
                      <div class="input-group ">
                          <span class="input-group-btn ">
                              <span class="btn btn-default btn-file btnu_browse ">
                                  Browse… <input type="file" name="project_image[]" value="{{ old('multi_img') }}" multiple id="multiImg" required>
                              </span>
                          </span>
                          <input type="text" class="form-control" readonly>
                      </div>
                    </div>
                </div>
                <!-- show image -->
                <div class="row show_multiple_image">
                  <div class="col-md-4"></div>
                  <div class="col-md-8">
                    <div class="show_multiple_image" id="preview_img"></div>
                  </div>
                </div>
                <!-- end show image -->
              </div>
              <div class="card-footer card_footer_button text-center">
                  <button type="submit" class="btn btn-primary waves-effect">UPLOAD</button>
              </div>
          </div>
        </form>
    </div>
    <div class="col-md-2"></div>
</div>
<!-- list of image -->
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-8">
                        <h3 class="card-title card_top_title"><i class="fab fa-gg-circle mr-2"></i> Project Image List </h3>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <!-- form  -->
                <form class="form-horizontal" id="ShowImageProjectWise" >
                  @csrf
                  <div class="form-group row custom_form_group">
                      <div class="col-md-1"></div>
                      <label class="col-sm-3 control-label"> Project Name:<span class="req_star">*</span></label>
                      <div class="col-sm-5">
                          <select class="form-control" name="project_id" id="getProjectWiseImage" required>
                              <option value="">Select Project Name</option>
                              @foreach($getProject as $proj)
                              <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                              @endforeach
                          </select>
                      </div>
                      <div class="col-md-3">
                        <button type="submit" onclick="showImage()" class="btn btn-primary waves-effect">SEARCH</button>
                      </div>
                  </div>
                </form>
            </div>
            <div class="card-body">
                <div id="show_multiple_image_list" class="row">

                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

function showImage(){
  $("#ShowImageProjectWise").submit(function(e){
      e.preventDefault();
      let project_id = $('select[id="getProjectWiseImage"]').val();
      let _token = $("input[name=_token]").val();

      $.ajax({
        url: "{{ route('search-project-image') }}",
        type: "POST",
        data: {
          project_id:project_id,
          _token:_token,
        },
        success:function(response){
          /* Show Project Image List */
          if(response.photo_path != ''){
            var show_image = "";
            $.each(response.findImage, function(key,value){

              show_image += `
                <div class="col-md-3 project_image">
                  <img src="/${value.photo_path}" alt="">
                  <input name="old_image" value="${value.photo_path}" hidden>
                  <button id="${value.proj_img_id}" onclick="removeProjectImage(this.id)" class="btn btn-primary waves-effect"><i class="fas fa-trash-alt"></i></button>
                </div>
              `
            });
            $("#show_multiple_image_list").html(show_image);
          }else{
            alert('not-image');
          }



          /* ====================================================================*/
        }
      });

  });
}

showImage();



function removeProjectImage(id){
    $.ajax({
        type:'GET',
        url: '/admin/project/image/remove/'+id,
        dataType:'json',
        success: function(response){
          showImage();
          //  start message
          const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
          })
          if($.isEmptyObject(data.error)){
              Toast.fire({
                type: 'success',
                title: data.success
              })
          }else{
            Toast.fire({
              type: 'error',
              title: data.error
            })
          }
          //  end message
        }
    });
}

</script>


<!-- this script for show multiple images -->
<script>

  $(document).ready(function(){
   $('#multiImg').on('change', function(){ //on file input change
      if (window.File && window.FileReader && window.FileList && window.Blob) //check File API supported browser
      {
          var data = $(this)[0].files; //this file data

          $.each(data, function(index, file){ //loop though each file
              if(/(\.|\/)(gif|jpe?g|png)$/i.test(file.type)){ //check supported file type
                  var fRead = new FileReader(); //new filereader
                  fRead.onload = (function(file){ //trigger function on successful read
                  return function(e) {
                      var img = $('<img/>').addClass('thumb').attr('src', e.target.result) .width(80)
                  .height(80); //create image element
                      $('#preview_img').append(img); //append image to output element
                  };
                  })(file);
                  fRead.readAsDataURL(file); //URL representing the file's data.
              }
          });

      }else{
          alert("Your browser doesn't support File API!"); //if File API is absent
      }
   });
  });

  </script>
<!-- ===================================== -->
@endsection
