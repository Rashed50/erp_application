function deleteEmployeeRecievedItemRecord(item_received_auto_id){
        
    // alert(item_received_auto_id);
    swal({
        title: "Are you sure?",
        text: "Once deleted, You will not be able to recover this Record!",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    })
    .then((willDelete) => {
        if (willDelete) {
            $.ajax({
                type: 'delete',
                url: "{{url('admin/inventory/item/received-item-delete')}}/" + item_received_auto_id,
                dataType: 'json',
                success: function (response) {
                    if(response.status == 200){    
                        showSweetAlertMessage("success",response.message);
                        searchingEmployeeReceivedItems();
                    }else {
                     showSweetAlertMessage("error",response.message);
                    }                     
                },
                error:function(response){
                     showSweetAlertMessage("error",response.message);
                }
            });
        }
    });
    //  window.location.reload();
}