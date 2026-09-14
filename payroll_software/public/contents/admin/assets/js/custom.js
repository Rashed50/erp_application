// JavaScript Document
//Image Upload Script
$(document).ready(function() {
    $(document).on('change', '.btn-file :file', function() {
        var input = $(this),
            label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
        input.trigger('fileselect', [label]);
    });

    $('.btn-file :file').on('fileselect', function(event, label) {

        var input = $(this).parents('.input-group').find(':text'),
            log = label;

        if (input.length) {
            input.val(log);
        } else {
            if (log)
                alert(log);
        }

    });

    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
                $('#img-upload').attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);
        }
    }

    $("#imgInp").change(function() {
        readURL(this);
    });

});



/* ajax request */
$(document).ready(function() {
    $('select[name="country_id"]').on('change', function() {
        var country_id = $(this).val();

        if (country_id) {
            $.ajax({
                url: "{{  url('/admin/division-get/ajax') }}/" + country_id,
                type: "GET",
                dataType: "json",
                success: function(data) {
                    // alert(country_id);
                    // $('select[name="state_id"]').empty();
                    // $('select[name="division_id"]').empty();
                    // $.each(data, function(key, value){
                    //
                    //     $('select[name="division_id"]').append('<option value="'+ value.division_id  +'">' + value.division_name + '</option>');
                    //
                    // });

                },

            });
        } else {
            alert('danger');
        }

    });
});
/* end ajax request */

// upload file 2
$(document).ready(function() {
    $(document).on('change', '.btn-file :file', function() {
        var input = $(this),
            label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
        input.trigger('fileselect', [label]);
    });

    $('.btn-file :file').on('fileselect', function(event, label) {

        var input = $(this).parents('.input-group').find(':text'),
            log = label;

        if (input.length) {
            input.val(log);
        } else {
            if (log)
                alert(log);
        }

    });

    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
                $('#img-upload2').attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);
        }
    }

    $("#imgInp2").change(function() {
        readURL(this);
    });

});

// upload file 3
$(document).ready(function() {
    $(document).on('change', '.btn-file :file', function() {
        var input = $(this),
            label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
        input.trigger('fileselect', [label]);
    });

    $('.btn-file :file').on('fileselect', function(event, label) {

        var input = $(this).parents('.input-group').find(':text'),
            log = label;

        if (input.length) {
            input.val(log);
        } else {
            if (log)
                alert(log);
        }

    });

    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
                $('#img-upload3').attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);
        }
    }

    // function readURL(input) {
    //     if (input.files && input.files[0]) {
    //         var file = input.files[0];
    //         var reader = new FileReader();

    //         reader.onload = function(e) {
    //             var preview = $('#img-upload3');
    //             var fileType = file.type;
    //             var fileName = file.name.toLowerCase();

    //             // If the file is an [Image]
    //             if (fileType.startsWith('image/')) {
    //                 preview.attr('src', e.target.result);
    //                 preview.show();
    //             }
    //             // If PDF
    //             else if (fileType === 'application/pdf' || fileName.endsWith('.pdf')) {
    //                 preview.hide();
    //                 $('#pdf-preview-container').remove();

    //                 var pdfHtml = `
    //                     <div id="pdf-preview-container" style="text-align:center; padding:15px; border:2px dashed #ccc; border-radius:10px; background:#f9f9f9;">
    //                         <!-- PDF Icon -->
    //                         <img src="https://img.icons8.com/color/96/000000/pdf.png" alt="PDF Icon" style="width:20px; height:20px;">
    //                         <span style="margin:10px 0 5px; font-weight:bold; color:#333;">${file.name}</span>
    //                         <p style="margin:0; color:#666; font-size:14px;">PDF File Selected</p>
    //                         <br>
    //                         <embed src="${e.target.result}" type="application/pdf" width="100%" height="300px" style="border:1px solid #ddd; border-radius:8px;">
    //                     </div>
    //                 `;

    //                 preview.after(pdfHtml);
    //             }
    //             else {
    //                 preview.hide();
    //                 $('#pdf-preview-container').remove();
    //                 preview.after(`
    //                     <div id="pdf-preview-container" style="text-align:center; padding:20px; background:#f8f9fa; border:1px dashed #ccc; border-radius:8px;">
    //                         <p>Selected File: <strong>${file.name}</strong></p>
    //                     </div>
    //                 `);
    //             }
    //         }

    //         reader.readAsDataURL(file);
    //     }
    // }

    $("#imgInp3").change(function() {
        readURL(this);
    });

});

// upload file 4
$(document).ready(function() {
    $(document).on('change', '.btn-file :file', function() {
        var input = $(this),
            label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
        input.trigger('fileselect', [label]);
    });

    $('.btn-file :file').on('fileselect', function(event, label) {

        var input = $(this).parents('.input-group').find(':text'),
            log = label;

        if (input.length) {
            input.val(log);
        } else {
            if (log)
                alert(log);
        }

    });

    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
                $('#img-upload4').attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);
        }
    }

    $("#imgInp4").change(function() {
        readURL(this);
    });

});

// upload file 5
$(document).ready(function() {
    $(document).on('change', '.btn-file :file', function() {
        var input = $(this),
            label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
        input.trigger('fileselect', [label]);
    });

    $('.btn-file :file').on('fileselect', function(event, label) {

        var input = $(this).parents('.input-group').find(':text'),
            log = label;

        if (input.length) {
            input.val(log);
        } else {
            if (log)
                alert(log);
        }

    });

    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
                $('#img-upload5').attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);
        }
    }

    $("#imgInp5").change(function() {
        readURL(this);
    });

});


// upload file 7
$(document).ready(function() {
    $(document).on('change', '.btn-file :file', function() {
        var input = $(this),
            label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
        input.trigger('fileselect', [label]);
    });

    $('.btn-file :file').on('fileselect', function(event, label) {

        var input = $(this).parents('.input-group').find(':text'),
            log = label;

        if (input.length) {
            input.val(log);
        } else {
            if (log)
                alert(log);
        }

    });

    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
                $('#img-upload7').attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);
        }
    }

    $("#imgInp7").change(function() {
        readURL(this);
    });

});

// upload file 8
$(document).ready(function() {
    $(document).on('change', '.btn-file :file', function() {
        var input = $(this),
            label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
        input.trigger('fileselect', [label]);
    });

    $('.btn-file :file').on('fileselect', function(event, label) {

        var input = $(this).parents('.input-group').find(':text'),
            log = label;

        if (input.length) {
            input.val(log);
        } else {
            if (log)
                alert(log);
        }

    });

    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
                $('#img-upload8').attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);
        }
    }

    $("#imgInp8").change(function() {
        readURL(this);
    });

});




    // file upload preview system
    $(document).ready(function() {
            $(document).on('change', '.btn-file :file', function() {
                var input = $(this),
                    label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
                input.trigger('fileselect', [label]);
            });

            $('.btn-file :file').on('fileselect', function(event, label) {

                var input = $(this).parents('.input-group').find(':text'),
                    log = label;

                if (input.length) {
                    input.val(log);
                } else {
                    if (log)
                        alert(log);
                }

            });



        function readURL(input) {
            if (input.files && input.files[0]) {
                var file = input.files[0];
                var reader = new FileReader();

                reader.onload = function(e) {
                    var preview = $('#daily_transaction_file_previewer');
                    var fileType = file.type;
                    var fileName = file.name.toLowerCase();

                    // If the file is an [Image]
                
                    if (fileType.startsWith('image/')) {
                        preview.attr('src', e.target.result);
                        preview.show();
                    }
                    // If PDF
                    else if (fileType === 'application/pdf' || fileName.endsWith('.pdf')) {
                        preview.hide();
                        $('#pdf-preview-container').remove();

                        var pdfHtml = `
                            <div id="pdf-preview-container" style="text-align:center; padding:15px; border:2px dashed #ccc; border-radius:10px; background:#f9f9f9;">
                                <!-- PDF Icon -->
                                <img src="https://img.icons8.com/color/96/000000/pdf.png" alt="PDF Icon" style="width:20px; height:20px;">
                                <span style="margin:10px 0 5px; font-weight:bold; color:#333;">${file.name}</span>
                                <p style="margin:0; color:#666; font-size:14px;">PDF File Selected</p>
                                <br>
                                <embed src="${e.target.result}" type="application/pdf" width="100%" height="300px" style="border:1px solid #ddd; border-radius:8px;">
                            </div>
                        `;

                        preview.after(pdfHtml);
                    }
                    else {
                        preview.hide();
                        $('#pdf-preview-container').remove();
                        preview.after(`
                            <div id="pdf-preview-container" style="text-align:center; padding:20px; background:#f8f9fa; border:1px dashed #ccc; border-radius:8px;">
                                <p>Selected File: <strong>${file.name}</strong></p>
                            </div>
                        `);
                    }
                }

                reader.readAsDataURL(file);
            }
        }

        $("#daily_transaction_file_selector").change(function() {
            readURL(this);
        });

    });


//Footer Logo Upload Script
$(document).ready(function() {
    $(document).on('change', '.btn-file :file', function() {
        var input = $(this),
            label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
        input.trigger('fileselect', [label]);
    });

    $('.btn-file :file').on('fileselect', function(event, label) {

        var input = $(this).parents('.input-group').find(':text'),
            log = label;

        if (input.length) {
            input.val(log);
        } else {
            if (log)
                alert(log);
        }

    });

    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
                $('#img-upload-flogo').attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);
        }
    }

    $("#imgInpFlogo").change(function() {
        readURL(this);
    });

});

//Favicon Upload Script
$(document).ready(function() {
    $(document).on('change', '.btn-file :file', function() {
        var input = $(this),
            label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
        input.trigger('fileselect', [label]);
    });

    $('.btn-file :file').on('fileselect', function(event, label) {

        var input = $(this).parents('.input-group').find(':text'),
            log = label;

        if (input.length) {
            input.val(log);
        } else {
            if (log)
                alert(log);
        }

    });

    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
                $('#img-upload-favicon').attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);
        }
    }

    $("#imgInpFavicon").change(function() {
        readURL(this);
    });

});

//Modal code start
$(document).ready(function() {
    $(document).on("click", "#softDelete", function() {
        var deleteID = $(this).data('id');
        $(".modal_card #modal_id").val(deleteID);
    });

    $(document).on("click", "#publish", function() {
        var publishID = $(this).data('id');
        $(".modal_card #modal_id").val(publishID);
    });

    $(document).on("click", "#unpublish", function() {
        var unPublishID = $(this).data('id');
        $(".modal_card #modal_id").val(unPublishID);
    });

    $(document).on("click", "#restore", function() {
        var restoreID = $(this).data('id');
        $(".modal_card #modal_id").val(restoreID);
    });

});

//Success and Error Message Timeout Code Start
setTimeout(function() {
    $('.alertsuccess').slideUp(1000);
}, 5000);

setTimeout(function() {
    $('.alerterror').slideUp(1000);
}, 10000);

//print code start
$(document).ready(function() {
    $('.btnPrint').printPage();
});

//datatable code start
$(document).ready(function() {
    $('#myTable').DataTable();

    $('#employeeTable').DataTable({
        "paging": false,
        "lengthChange": false,
        "searching": true,
        "ordering": false,
        "info": false,
        "autoWidth": false,
    });

    $('#alltableinfo').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": false,
        "info": true,
        "autoWidth": false
    });

    $('#allTableDesc').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "order": [
            [0, "desc"]
        ],
        "info": true,
        "autoWidth": false
    });
});

$(document).ready(function () {

    $('#dt-vertical-scroll').dataTable({
      "paging": false,
      "searching": true,
      "fnInitComplete": function () {
        var myCustomScrollbar = document.querySelector('#dt-vertical-scroll_wrapper .dataTables_scrollBody');
        var ps = new PerfectScrollbar(myCustomScrollbar);
      },
      "scrollY": 450,
    });
  });

//counter code start
jQuery(document).ready(function($) {
    $('.counter').counterUp({
        delay: 100,
        time: 1200
    });
});

//summernote code editor code start
jQuery(document).ready(function() {
    $('.summernote').summernote({
        height: 200, // set editor height
        minHeight: null, // set minimum height of editor
        maxHeight: null, // set maximum height of editor
        focus: true // set focus to editable area after initializing summernote
    });
});

/* laravel file upload && validation rule */


$(document).ready(function() {
    // magnific js
    $(".project-popup").magnificPopup({
        type: "image",
        gallery: {
            enabled: true,
        },
    });
});
