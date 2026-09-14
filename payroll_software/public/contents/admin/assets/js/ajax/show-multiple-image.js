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
      success:function(data){
        console.log(data);
      }
    });

});
