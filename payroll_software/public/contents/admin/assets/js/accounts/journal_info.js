
$('#jour_type_id').on('change', function() {
    var jour_type_id = $(this).val();
    if(jour_type_id) {
        $.ajax({
            url: "/admin/account/chart-of-account/records-by/journal-type/" + jour_type_id,
            type:"GET",
            dataType:"json",
            success:function(data) {
                console.log(data);
                var d =$('select[name="chart_of_acct_id"]').empty();
                if (data.length > 0) {
                    $.each(data, function(key, value){
                        $('select[name="chart_of_acct_id"]').append('<option value="'+ value.chart_of_acct_id +'">' + value.chart_of_acct_name + '-' + value.chart_of_acct_number + '</option>');
                    });
                } else {
                    $('select[name="chart_of_acct_id"]').append('<option value="">' + 'Not Found Any' + '</option>');
                }
            },
        });
    }
});


$("form#account_journal_info_form").validate({
    rules: {
        jour_type_id: {
            required: true,
        },
        chart_of_acct_id: {
            required: true,
        },
        jour_name: {
            required: true,
        },
        jour_code: {
            required: true,
        },
    },
    messages: {
        jour_type_id    : {
            required: "Please Select Any Journal Type Name.",
        },
        chart_of_acct_id    : {
            required: "Please Select Any Chart Of Account Name.",
        },
        jour_name    : {
            required: "Enter Journal Name.",
        },
        jour_code    : {
            required: "Enter Journal Code.",
        },
    }
});

$(document).ready(function() {
    loadAccountJournalInfo();

    // Account Journal Information Store Form submit Start
    $('#account_journal_info_form').submit(function(event) {
        event.preventDefault();
        accountJournalInfoStore();
    });

    function accountJournalInfoStore() {
        const purchaseButton = document.getElementById('account-journal-info-submit-button');

        // Clear previous errors
        $('.form-error').remove();

        // Validate the form
        if (!$('#account_journal_info_form').valid()) {
            return;
        }

        var formData = new FormData($('#account_journal_info_form')[0]);

        // Disable button to prevent multiple submissions
        purchaseButton.disabled = true;
        purchaseButton.innerText = 'Submitting...';

        // Call ajax
        $.ajax({
            type: 'POST',
            url: '/admin/account/journal-information/store',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                console.log(res);
                if (res.success) {
                    $('#account_journal_info_form').trigger("reset");
                    showSweetAlertMessage('success', res.message);
                    loadAccountJournalInfo();
                } else {
                    showSweetAlertMessage('error', res.message);
                }
                purchaseButton.disabled = false;
                purchaseButton.innerText = 'SAVE INFO';
            },
            error: function(xhr) {
                purchaseButton.disabled = false;
                purchaseButton.innerText = 'SAVE INFO';

                // Handling error from controller validation
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    // Clear previous errors
                    $('.form-error').remove();

                    // Display validation errors
                    $.each(xhr.responseJSON.errors, function(key, messages) {
                        var inputElement = $('[name="' + key + '"]');
                        var errorMessage = messages.join(', ');

                        // Append the error message
                        inputElement.after('<span class="form-error" style="color: red;">' + errorMessage + '</span>');
                    });
                } else {
                    showSweetAlertMessage('error', 'Operation Failed, Please try Again');
                }
            }
        });
    }

    // Account Journal Information Store Form submit End


    $('#journalInfoEditForm').submit(function(event) {
        event.preventDefault();
        accountJournalInfoUpdate();
    });

    function accountJournalInfoUpdate() {
        const purchaseButton = document.getElementById('account-journal-info-update-button');

        // Clear previous errors
        $('.form-error').remove();

        // Validate the form
        if (!$('#journalInfoEditForm').valid()) {
            return;
        }

        var formData = new FormData($('#journalInfoEditForm')[0]);

        // Disable button to prevent multiple submissions
        purchaseButton.disabled = true;
        purchaseButton.innerText = 'Submitting...';

        // Call ajax
        $.ajax({
            type: 'POST',
            url: '/admin/account/journal-information/update',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                console.log(res)
                if (res.success) {
                    showSweetAlertMessage('success', res.message);
                    loadAccountJournalInfo();
                } else {
                    showSweetAlertMessage('error', res.message);
                }
                $('#journal_info_update_modal').modal('hide');

                purchaseButton.disabled = false;
                purchaseButton.innerText = 'UPDATE INFO';
            },
            error: function(xhr) {
                purchaseButton.disabled = false;
                purchaseButton.innerText = 'SAVE INFO';

                // Handling error from controller validation
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    // Clear previous errors
                    $('.form-error').remove();

                    // Display validation errors
                    $.each(xhr.responseJSON.errors, function(key, messages) {
                        var inputElement = $('[name="' + key + '"]');
                        var errorMessage = messages.join(', ');

                        // Append the error message
                        inputElement.after('<span class="form-error" style="color: red;">' + errorMessage + '</span>');
                    });
                } else {
                    showSweetAlertMessage('error', 'Operation Failed, Please try Again');
                }
            }
        });
    }
});


function loadAccountJournalInfo(){
    $.ajax({
        type: 'GET',
        url: '/admin/account/journal-information/details',
        dataType: 'json',
        success: function(res) {
            if (res.success) {
                var rows = "";
                $.each(res.journal_infos, function(key, value) {
                    rows += `
                        <tr>
                            <td>${value.jour_type_name}</td>
                            <td>${value.chart_of_acct_name}-${value.chart_of_acct_number} </td>
                            <td>${value.jour_name}</td>
                            <td>${value.jour_code}</td>
                            <td>${value.jour_status == 1 ? "Active" : "De Active"}</td>
                            <td style="text-align: center;">
                                <button style="border:none;padding: 1px 3px 0px 3px;margin-left: 3px;background: #3232a3;color: white;text-transform: uppercase;" id="${value.jour_id}" onclick="editAccountJournalInfo(this.id)">
                                    <i class="fa fa-pencil-square fa-lg"></i>
                                </button>
                                <button style="border:none;padding: 1px 3px 1px 3px;margin-left: 3px;background: #b92b31;color: white;text-transform: uppercase;" class="journal-deActive" data-id="${value.jour_id}">
                                    <i class="fa fa-times-circle" aria-hidden="true"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                });
                $('#account_journal_info_table_content_view').html(rows);
            }
        }
    });
}


function editAccountJournalInfo(jourInfoId) {
    console.log(jourInfoId);
    $.ajax({
        type: 'GET',
        url: '/admin/account/journal-information/record-edit/' + jourInfoId,
        dataType: 'json',
        success: function(res) {
            if (res.success) {
                console.log(res.jour_Info);

                // Populate the modal fields with the fetched data
                $('#journal_info_update_modal').find('#edit_jour_id').val(res.jour_Info.jour_id );

                $('#journal_info_update_modal').find('#edit_jour_type_id').val(res.jour_Info.jour_type_id);
                $('#journal_info_update_modal').find('#edit_chart_of_acct_id').val(res.jour_Info.chart_of_acct_id);
                $('#journal_info_update_modal').find('#edit_jour_name').val(res.jour_Info.jour_name);
                $('#journal_info_update_modal').find('#edit_jour_code').val(res.jour_Info.jour_code);

                // Show the modal
                $('#journal_info_update_modal').modal('show');
                // Trigger the onchange event for the chart_of_acct_id
                $('#edit_jour_type_id').change();

            } else {
                showSweetAlertMessage('error', res.message);
            }
        }
    });
}

//onchange function for chart_of_acct_id
$('#edit_jour_type_id').on('change', function() {
    var editJourTypeId = $(this).val();
    if(editJourTypeId) {
        $.ajax({
            url: "/admin/account/chart-of-account/records-by/journal-type/" + editJourTypeId,
            type:"GET",
            dataType:"json",
            success:function(data) {
                console.log(data);
                $('#edit_chart_of_acct_id').empty();
                if (data.length > 0) {
                    $.each(data, function(key, value){
                        $('#edit_chart_of_acct_id').append('<option value="'+ value.chart_of_acct_id +'">' + value.chart_of_acct_name + '-' + value.chart_of_acct_number + '</option>');
                    });
                } else {
                    $('#edit_chart_of_acct_id').append('<option value="">' + 'Not Found Any' + '</option>');
                }
            },
        });
    }
});


$(document).on("click", ".journal-deActive", function(e){
    e.preventDefault();
    var button = $(this); // Store the button that was clicked
    var jourInfoID = button.data("id");

    swal({
        title: "Are you sure To Confirm?",
        text: "Once Confirm, you will not go Back Step Again!",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    })
    .then((willDelete) => {
        if (willDelete) {
            // Run the onclick event handler after confirmation
            chartOfAccountClosed(jourInfoID);
        } else {
            // Optional: Handle the case where the user cancels
            // swal("Not Confirm!");
        }
    });
});


function chartOfAccountClosed(jourInfoID) {
    $.ajax({
        type: 'GET',
        url: '/admin/account/journal-information/de-active/' + jourInfoID, // Replace with your actual URL
        success: function(res) {
            if (res.success) {
                showSweetAlertMessage('success', res.message);
                loadAccountJournalInfo();
            } else {
                showSweetAlertMessage('error', res.message);
            }
            // window.location.reload();
        },
        error: function(status, error) {
            showSweetAlertMessage('error', 'An error occurred');
        }
    });
}
