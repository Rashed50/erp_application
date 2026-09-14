// $.validator.addMethod( "regex", function(value, element, regexp) {
//     var re = new RegExp(regexp);
//     return this.optional(element) || re.test(value);
//   },
//   "Please check your input."
// );






$(document).ready(function(){
  $("#employee-info-form").validate({ //employee-info-form
    rules: {
      emp_name : {
        required: true,
        lettersonly: true,
      },

      passfort_expire_date: {
        required: true,
      },

      passfort_no: {
        required: true,
        unique: true,
      },

      akama_no : {
        required: true,
        maxlength: 20,
        number: true,
      },

      akama_expire : {
        required: true,
      },

      present_address : {
        required: true,
      },

      country_id : {
        required: true,
      },

      division_id : {
        required: true,
      },

      district_id : {
        required: true,
      },

      post_code : {
        required: true,
      },

      details : {
        required: true,
      },

      emp_type_id : {
        required: true,
      },

      emp_catg_id : {
        required: true,
      },

      project_id : {
        required: true,
      },

      designation_id : {
        required: true,
      },

      department_id : {
        required: true,
      },

      date_of_birth : {
        required: true,
      },

      mobile_no : {
        required: true,
        number: true,
      },

      phone_no : {
        number: true,
      },

      email : {

        email: true,
      },

      maritus_status : {
        required : true,
      },

      gender : {
        required : true,
      },

      religion : {
        required : true,
      },

      joining_date : {
        required : true,
      },

      confirmation_date : {
        required : true,
      },

      appointment_date : {
        required : true,
      },

      job_location : {
        required : true,
      },

      pasfort_photo : {
        required : true,
      },


      /* ============== Rules enden ============== */
    },

    messages: {
      pasfort_photo : {
        required: 'choose passport File!',
      },
      job_location : {
        required: 'please enter job location!',
      },
      appointment_date : {
        required: 'please select appointment date!',
      },
      confirmation_date : {
        required: 'please select confirmation date!',
      },
      joining_date : {
        required: 'please select joining date!',
      },
      religion : {
        required: 'please select religion!',
      },
      gender : {
        required: 'please select gender!',
      },
      maritus_status : {
        required: 'please select maritus!',
      },
      email : {
        email: 'please enter valid email!',
      },
      mobile_no : {
        required: 'please enter moblie number!',
      },
      date_of_birth : {
        required: 'please select dob!',
      },
      department_id : {
        required: 'please select department!',
      },
      designation_id : {
        required: 'please select designation!',
      },
      emp_catg_id : {
        required: 'please select employee category!',
      },
      project_id : {
        required: 'please select project name!',
      },
      emp_name : {
        required: 'please enter your name!',
      },
      akama_no : {
        required: 'please enter iqama no!',
        number: 'Iqama no is invalid!',
      },
      akama_expire : {
        required: 'please select Iqama expire date!',
      },
      present_address : {
        required: 'please enter your present address!',
      },
      country_id : {
        required: 'please select country name!',
      },
      division_id : {
        required: 'please select division name!',
      },
      district_id : {
        required: 'please select district name!',
      },
      district_id : {
        required: 'please enter post code number!',
      },
      details : {
        required: 'details your address!',
      },
      emp_type_id : {
        required: 'please select employee type name!',
      },


    },
    errorPlacement: function(error, element)
    {
      if (element.is(":radio"))
      {
          error.appendTo(element.parents('.gender'));
      }
      else if(element.is(":file")){
          error.appendTo(element.parents('.passfortFiles'));
      }
      else
      {
          error.insertAfter( element );
      }

     }

  });
});

$(document).ready(function(){
  $("#project_image_upload").validate({
    rules: {
      project_image : {
        required: true,
      },

    },
    messages: {
      project_image : {
        required: 'choose Project Image!',
      },
    },

    errorPlacement: function(error, element)
    {
      if (element.is(":radio"))
      {
          error.appendTo(element.parents('.gender'));
      }
      else if(element.is(":file")){
          error.appendTo(element.parents('.project_image'));
      }
      else
      {
          error.insertAfter( element );
      }

     }





  });
});
