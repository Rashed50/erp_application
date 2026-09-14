jQuery.validator.addMethod("noSpace", function(value, element) {
    return value == '' || value.trim().length != 0;
}, "No space please and don't leave it empty");
jQuery.validator.addMethod("customEmail", function(value, element) {
  return this.optional( element ) || /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/.test( value );
}, "Please enter valid email address!");

$.validator.addMethod( "alphanumeric", function( value, element ) {
return this.optional( element ) || /^\w+$/i.test( value );
}, "Letters, numbers, and underscores only please" );








var $registrationForm = $('#registration');
if($registrationForm.length){
  $registrationForm.validate({
      rules:{
          //username is the name of the textbox

          emp_id: {
              required: true,
          },
          emp_name: {
              required: true,
          },
          akama_no: {
              required: true,
              alphanumeric: true
          },
          akama_expire: {
              required: true,
          },
          present_address: {
              required: true
          },
          country_id: {
              required: true
          },
          division_id: {
              required: true
          },
          district_id: {
              required: true
          },
          post_code: {
              required: true
          },
          details: {
              required: true
          },
          present_address: {
              required: true
          },
          emp_type_id: {
              required: true
          },
          designation_id: {
              required: true
          },
          department_id: {
              required: true
          },
          date_of_birth: {
              required: true
          },
          phone_no: {
              required: true
          },
          email: {
              required: true,
              email:true,
          },
          maritus_status: {
              required: true
          },
          gender: {
              required: true
          },
          religion: {
              required: true
          },
          joining_date: {
              required: true
          },
          confirmation_date: {
              required: true
          },
          appointment_date: {
              required: true
          },
          job_status: {
              required: true
          },
          job_location: {
              required: true
          },
          salary_amount: {
              required: true
          },
          salary_basic: {
              required: true
          },
          emp_insert_date: {
              required: true
          },
          /* designation validation */
          desig_name:{
            required: true
          },
          desig_code:{
            required: true,
            alphanumeric: true
          },
      },
      messages:{

          emp_id: {
              required: 'Please generate employee Id no!'
          },
          emp_name: {
              required: 'Please enter Employee name!'
          },
          akama_no: {
              required: 'Please enter correct akama no!'
          },
          akama_expire: {
            required: 'Please select akama expire date!'
          },
          present_address: {
            required: 'Please details your present address!'
          },
          country_id: {
              required: 'Please select country!'
          },
          division_id: {
              required: 'Please select division!'
          },
          district_id: {
              required: 'Please select district!'
          },
          post_code: {
              required: 'Please enter post code!'
          },
          details: {
              required: 'Please enter details!'
          },
          emp_type_id: {
              required: 'Please select employee type!'
          },
          designation_id: {
              required: 'Please select designation!'
          },
          department_id: {
              required: 'Please select designation!'
          },
          date_of_birth: {
              required: 'Please insert your d-o-b!'
          },
          phone_no: {
              required: 'Please enter your phone number!'
          },
          email: {
              required: 'Please enter your email!'
          },
          maritus_status: {
              required: 'Please select your maritus status!'
          },
          gender: {
              required: 'Please select your gender!'
          },
          religion: {
              required: 'Please select your religion!'
          },
          joining_date: {
              required: 'Please select your joining date!'
          },
          confirmation_date: {
              required: 'Please select your confirmation date!'
          },
          appointment_date: {
              required: 'Please select your confirmation date!'
          },
          job_status: {
              required: 'Please select job status!'
          },
          job_location: {
              required: 'Please select job location!'
          },
          salary_amount: {
              required: 'Please type your salary!'
          },
          salary_basic: {
              required: 'Please type your basic salary!'
          },
          emp_insert_date: {
              required: 'Please select date!'
          },
          /* designation validation */
          desig_name:{
            required: 'please enter designation name'
          },
          desig_code:{
            required: 'please enter designation code',
          },

      },
      errorPlacement: function(error, element)
      {
        if (element.is(":radio"))
        {
            error.appendTo(element.parents('.gender'));
        }
        else if(element.is(":checkbox")){
            error.appendTo(element.parents('.hobbies'));
        }
        else
        {
            error.insertAfter( element );
        }

       }
  });
}
/* salary details form validation */
// var $salary_detailsForm = $('#salary_details_form');
// if($salary_detailsForm.length){
//     $salary_detailsForm.validate({
//       rules:{
//           //username is the name of the textbox
//           basic_amount: {
//             required: true,
//             number: true,
//
//           },
//           basic_hours: {
//             required: true,
//             number: true,
//
//           },
//           house_rent: {
//             required: true,
//             number: true,
//
//           },
//           mobile_allowance: {
//             required: true,
//             number: true,
//
//           },
//           medical_allowance: {
//             required: true,
//             number: true,
//
//           },
//           local_travel_allowance: {
//             required: true,
//             number: true,
//
//           },
//           conveyance_allowance: {
//             required: true,
//             number: true,
//
//           },
//           others1: {
//             required: true,
//             number: true,
//
//           },
//           others2: {
//             required: true,
//             number: true,
//
//           },
//
//       },
//       messages:{
//           house_rent:{
//             number: 'please enter number value',
//           },
//       },
//       errorPlacement: function(error, element)
//       {
//         if (element.is(":radio"))
//         {
//             error.appendTo(element.parents('.gender'));
//         }
//         else if(element.is(":checkbox")){
//             error.appendTo(element.parents('.hobbies'));
//         }
//         else
//         {
//             error.insertAfter( element );
//         }
//
//        }
//     });
// }
