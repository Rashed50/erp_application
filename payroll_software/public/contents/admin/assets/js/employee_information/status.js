// $("form#employee_activity_remarks_add_form").validate({
//     rules: {
//         emp_act_remarks: {
//             required: true,
//         },
//     },
//     messages: {
//         emp_act_remarks: {
//             required: "Please Insert Employee Activity Details",
//         },
//     }
// });

$(document).ready(function() {
    // Attach the submit event handler to the form
    $('#employee_activity_remarks_add_form').submit(function(event) {
        event.preventDefault();
        updateEmployeeActivityRemarks();
    });
    function updateEmployeeActivityRemarks() {
        const remarksBtn = document.getElementById('emp_act_remarks_add_btn');

        // Validate the form
        if (!$('#employee_activity_remarks_add_form').valid()) {
            return;
        }

        var formData = new FormData($('#employee_activity_remarks_add_form')[0]);

        // Disable button to prevent multiple submissions
        remarksBtn.disabled = true;
        remarksBtn.innerText = 'Submitting...';

        // Call ajax
        $.ajax({
            type: 'POST',
            url: '/admin/search/employee/activity-remarks/update',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {

                if (res.status == 200) {
                    $('#employee_activity_remarks_modal').modal('hide');
                    $('#employee_activity_remarks_add_form').trigger("reset");
                    showSweetAlertMessage('success', res.message);
                } else {
                    showSweetAlertMessage('error', res.message);
                }

                remarksBtn.disabled = false;
                remarksBtn.innerText = 'SUBMIT';

            },
            error: function(status, error) {
                showSweetAlertMessage('error', 'An error occurred');
                remarksBtn.disabled = false;
                remarksBtn.innerText = 'SUBMIT';
            }
        });
    }
});
