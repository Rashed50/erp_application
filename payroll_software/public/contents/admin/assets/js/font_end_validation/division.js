jQuery.validator.addMethod("validName", function(value, element) {
  return this.optional( element ) || /^[a-zA-Z]+$/.test( value );
}, "Please enter valid name!");

$(document).ready(function(){
  $("#designation-form").validate({
    rules: {
      catg_name: {
        required : true,
        validName: true,
      },
      emp_type_id: {
        required : true,
      }
    },

    messages: {
      catg_name: {
        required : "please enter designation name",
      },
      emp_type_id: {
        required : "please select Employee type",
      }
    },


  });
});
