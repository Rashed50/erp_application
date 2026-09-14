function showSweetAlertMessage(type,message){
    const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        })
            Toast.fire({
                type: type,
                title: message,
            })
}

$('#emp_information').keydown(function (e) {
    if (e.keyCode == 13) {
        driverInformationSearch();
    }
})


function driverInformationSearch() {
    var searchValue = $("#emp_information").val();

    if (searchValue.length === 0) {
        showSweetAlertMessage('error', "Please Enter Employee Number.");
        return;
    }

    $.ajax({
        type: 'POST',
        url: "/admin/driver-details/info-search",
        data: {
            employee_id: searchValue
        },
        dataType: 'json',
        success: function (response) {

            if (response.status == false) {
                $("span[id='driver_not_found_error_show']").text('Please Enter Valid Employee Id');
                $("span[id='driver_not_found_error_show']").addClass('d-block').removeClass('d-none');
                showSweetAlertMessage('error', response.message);
                return ;
            }

                $("#driver_searching_result_section").addClass('d-block').removeClass('d-none');

                var adriver =  response.driver_info;
                var driver_vehicle = response.driver_vehicle_info;
                if (adriver) {
                    // driver Information
                    $("input[name='driv_id']").val(adriver.dri_auto_id);
                    $("span[id='show_driver_employee_id']").text(adriver.dri_emp_id);
                    $("span[id='show_driver_iqama_no']").text(adriver.dri_iqama_no);
                    $("span[id='show_driver_name']").text(adriver.dri_name);
                    $("span[id='show_driver_address']").text(adriver.dri_address);
                } else {
                  //  showSweetAlertMessage('error', "Driver information is not available.");
                }


                if (driver_vehicle) {
                    // Vehicle Information
                    $("span[id='show_driver_vehicle_name']").text(driver_vehicle.veh_name+", "+driver_vehicle.veh_brand_name);
                    $("span[id='show_driver_vehicle_plate_no']").text(driver_vehicle.veh_plate_number);
                    $("span[id='show_driver_vehicle_model']").text(driver_vehicle.veh_model_number);
                    $("span[id='show_driver_vehicle_color']").text(driver_vehicle.veh_color);
                    $("span[id='show_driver_vehicle_license']").text(driver_vehicle.veh_licence_no);
                    $("span[id='show_driver_vehicle_assign_date']").text(driver_vehicle.assign_date);
                    $("span[id='show_driver_vehicle_project_name']").text(driver_vehicle.proj_name);
                } else {
                    showSweetAlertMessage('error', "Vehicle Information Not Found");
                }

        }, // end of success
        error: function (response) {
            showSweetAlertMessage('error', "Operation Failed, Please try Again");
        }
    }); // end of ajax calling
}



 /* form validation */
$("#driverForm-validation").validate({
    /* form tag off  */
    // submitHandler: function(form) { return false; },
    /* form tag off  */
    rules: {
        dri_empId: {
            required: true,
        },
        dri_iqamaNo: {
            required: true,
        },
        dri_name: {
            required: true,
        },
        dri_license_certificate: {
            required: true,
        },
        dri_ins_certificate: {
            required: true,
        },
    },

    messages: {
        dri_empId: {
            required: "You Must Be Input This Field!",
        },
        dri_iqamaNo: {
            required: "You Must Be Input This Field!",
        },
        dri_name: {
            required: "You Must Be Input This Field!",
        },
        dri_license_certificate: {
            required: "You Must Be Provide Your Driving License Here!",
        },
        dri_ins_certificate: {
            required: "You Must Be Provide Your Insurance Certificate Here!",
        },
    },
});

$("#company_driver_details_information_update_form").submit(function(event) {
    event.preventDefault();
    updateDriverInformationDetails();
});
function updateDriverInformationDetails()  {
    const informationUpdateButton = document.getElementById('company_driver_information_update_button');

    // Validate the form
    if (!$('#company_driver_details_information_update_form').valid()) {
        return;
    }

    var formData = new FormData($('#company_driver_details_information_update_form')[0]);

    // Disable button to prevent multiple submissions
    informationUpdateButton.disabled = true;
    informationUpdateButton.innerText = 'Submitting...';

    // Call ajax
    $.ajax({
        type: 'POST',
        url: '/admin/driver/info-update',
        data: formData,
        processData: false,
        contentType: false,
        success: function(res) {
            console.log(res)
            if (res.status == 200) {
                showSweetAlertMessage('success', res.message);
            } else {
                showSweetAlertMessage('error', res.message);
            }
            informationUpdateButton.disabled = false;
            informationUpdateButton.innerText = 'UPDATE';
        },
        error: function(status, error) {
            showSweetAlertMessage('error', 'An error occurred');
            informationUpdateButton.disabled = false;
            informationUpdateButton.innerText = 'UPDATE';
        }
    });
}




// Attach the submit event handler to the form
$('form[id^="company_driver_photo_update_form"]').submit(function(event) {
    event.preventDefault();
    updateDriverDocumentsDetails(this);
});
function updateDriverDocumentsDetails(form)  {
    const informationUpdateButton = document.getElementById('company_driver_photo_update_button');

    var formData = new FormData($('#company_driver_photo_update_form')[0]);

    // Disable button to prevent multiple submissions
    informationUpdateButton.disabled = true;
    informationUpdateButton.innerText = 'Submitting...';

    // Call ajax
    $.ajax({
        type: 'POST',
        url: '/admin/driver/image-update',
        data: formData,
        processData: false,
        contentType: false,
        success: function(res) {
            console.log(res)
            if (res.status == 200) {
                showSweetAlertMessage('success', res.message);
                $(form).closest('.modal').modal('hide');
                location.reload();
            } else {
                showSweetAlertMessage('error', res.message);
            }
            informationUpdateButton.disabled = false;
            informationUpdateButton.innerText = 'UPDATE';
        },
        error: function(status, error) {
            showSweetAlertMessage('error', 'An error occurred');
            informationUpdateButton.disabled = false;
            informationUpdateButton.innerText = 'UPDATE';
        }
    });
}
