function getAllDesignation(){
  
    axios.get('/admin/getAllDesignation')
    .then(function (response){
      if(response.status==200){

        alert('Calling from Desgination JS File');
        $('.dataTables_empty').css('display','none');

        var jsonData = response.data;

        $.each(jsonData, function(i,item){
          $('<tr>').html(
            "<td>"+ jsonData[i].id +"</td>" +
            "<td>"+ jsonData[i].desig_name +"</td>" +
            "<td>"+ jsonData[i].desig_code +"</td>" +
            "<td> <a href='#' title='edit'><i class='fa fa-pencil-square fa-lg edit_icon'></i></a> <a href='#' class='deleteDesignation' title='delete' id='softDelete' data-id="+jsonData[i].id+"><i class='fa fa-trash fa-lg delete_icon'></i></a> </td>"

          ).appendTo('#allDesignation_table');
        });

        /* detect id    data-toggle='modal'  data-target=''*/
        $('.deleteDesignation').click(function(){
          var id = $(this).data('id');

          $('#desigDeleteConfirmationBtn').attr('data-id',id);
          $('#softDelModal').modal('show');
        });

        /* delete action */
        $('#desigDeleteConfirmationBtn').click(function(){
          var id = $(this).data('id');
          getDesignationDelete(id);

        });

      }
      else{
        $('.dataTables_empty').css('display','block');
      }

    })
    .catch(function (error){

    });
}



/* delete method */
function getDesignationDelete(deleteId){
    axios.post('/admin/getDeleteDesignation',{id:deleteId})
    .then(function(response){
      if(response.data == 1){
        alert("Success");
      }else{
        alert("Fail");
      }
    })
    .catch(function(error){

    });
}

function callMeFromPage(){
  alert(123);
}