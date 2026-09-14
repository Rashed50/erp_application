@extends('layouts.admin-master')
@section('title')Attendance OUT @endsection
@section('content')

<style>
  input[type="number"], textarea {

        width: 80px;
        display: inline-block;
        border: 1px solid grey;
        border-radius: 4px;
        box-sizing: border-box;

}

</style>


<div class="row bread_part">
  <div class="col-sm-12 bread_col">
    <h4 class="pull-left page-title bread_title">Employee Attendence (OUT) </h4>
    <ol class="breadcrumb pull-right">
      <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
      <li class="active">Employee Attendence OUT List</li>
    </ol>
  </div>
</div>

<!-- Session Message Display Section !-->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        @if(Session::has('success'))
        <div class="alert alert-success alertsuccess" role="alert">
            <strong>{{ Session::get('success') }}</strong>
        </div>
        @endif
        @if(Session::has('error'))
        <div class="alert alert-warning alerterror" role="alert">
            <strong>{{ Session::get('error') }}</strong>
        </div>
        @endif
    </div>
    <div class="col-md-2"></div>
</div>

<!-- Searching Employee !-->
<div class="row">
  <div class="col-md-12">
    <form class="form-horizontal" id="employeeEntryTimeList" action="" method="">
      <div class="card">
        <div class="card-body card_form">

          <div class="form-group row custom_form_group">

            <label class="col-md-1 control-label">Date:</label>
            <div class="col-md-2">
              <?php $today = date("Y-m-d");
                  $next_date = date('Y-m-d',strtotime((-1*$allow_days).' days'));
              ?>
              <input type="hidden" id="allows_days" name="allows_days" value="{{$allow_days}}">
              <input type="date" name="date" id="search_date" value="<?= date("Y-m-d") ?>" min="{{ $next_date }}" max="{{date('Y-m-d',strtotime('1 days')) }}" class="form-control">
            </div>

            <div class="col-md-2">
                <input name="night_shift" id="night_shift" type="checkbox" value="0">
                <label for="saldetlabel">Night Shift</label>
             </div>
             <label class="col-md-1 control-label">Project:</label>
              <div class="col-md-2">
                <select class="form-select" name="proj_name">
                  <option value="">Select Project</option>
                  @foreach($project as $proj)
                  <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                  @endforeach
                </select>
              </div>
              <input type="text" placeholder="Enter Multiple Emp. ID (e.g 1020,1030)" name="multi_emp_id"  id="multi_emp_id" class="form-control col-md-3">
            <div class="col-md-1">
              <button type="button" onclick="searchingAlreadyAttendanceINEmployeeList()" class="btn btn-primary waves-effect">Search</button>
            </div>
          </div>

        </div>

      </div>
    </form>
  </div>

</div>

<!-- Employee List with Attendance OUT !-->
<div class="row d-none" id="employee_list_section">
  <div class="col-lg-12">
    <div class="card">
      <form id="employee-entry-in-form" action="{{ route('employee.attendance.out.multiple.emp') }}" onsubmit="atten_out_submit_button.disabled = true;" method="post">
                @csrf
            <div class="card-header">
                <div class="row">
                    <div class="form-group row custom_form_group" >
                              <input type="hidden" id="atten_in_time" value="0" required>
                              <input type="hidden" name="selected_atten_date" id="selected_atten_date"  >

                            <label class="col-md-3 control-label">Attendance Out Time:</label>
                            <div class="col-md-3">
                              <input type="number" class="form-control"  style="width: 200px;" id="atten_out_time"  name="atten_out_time" placeholder="Time(24 Format)"
                                    onkeyup="calculateAllEmployeeWorkingTime()" step = 0.5 required>
                                    &nbsp &nbsp &nbsp &nbsp &nbsp
                              <label style="background-color: red"  id="lbl_all_emp_ot_hours" >
                            </div>
                            <div class="col-md-2">
                              <button type="button" onclick="checkUnCheckAllEmployeeForAttendanceOut()" class="btn btn-primary waves-effect">Check/Uncheck</button>
                               &nbsp;&nbsp;&nbsp; <label class="control-label" id="lbl_total_selected_counter"> </label>
                            </div>
                            <div class="col-md-1">
                           </div>
                            <div class="col-md-2">
                              <button type="submit" id ="atten_out_submit_button" name="atten_out_submit_button" class="btn btn-primary waves-effect"> Attendance Submit</button>
                            </div>
                    </div>
                </div>
            </div>
        <div class="card-body">
            <div class="row">
              <div class="col-12">
                <div class="table-responsive">
                    <div style="max-height: 500px !important;overflow-y: auto;">
                        <span id="data_not_found" class="d-none">Data Not Found!</span>
                        <table id="alltableinfo" class="table table-bordered custom_table mb-0">
                            <thead>
                              <tr>
                                  <th>S.N</th>
                                    <th>Emp.ID</th>
                                    <th>Name</th>
                                    <th>Date</th>
                                    <th>Shift</th>
                                    <th>Type</th>
                                    <th>Trade</th>
                                    <th>Time(IN)</th>
                                    <th>Emp.ID</th>
                                    <th colspan="2">Action</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="employee_entry_time_list_view"></tbody>
                        </table>
                    </div>
                </div>
              </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>




<!-- FORM VALIDATION -->



 <script type="text/javascript">
//   $(document).ready(function() {

//     $("#employeeEntryTimeList").validate({
//       /* form tag off  */
//       submitHandler: function(form) {
//         return false;
//       },
//       /* form tag off  */
//       rules: {
//         proj_name: {
//           required: true,
//         },
//         date: {
//           required: true,
//         },
//       },

//       messages: {
//         proj_name: {
//           required: "You Must Be Select This Field!",
//         },
//         date: {
//           required: "You Must Be Chose This Field!",
//         },
//       },


//     });
//   });

// </script>

<script type="text/javascript">

  // any changes from keystrokes to arrow clicks and keyboard/mouse paste, not supported in IE <9.
  $('#atten_out_time').on('input', function() {
    calculateAllEmployeeWorkingTime();
  });

  function calculateAllEmployeeWorkingTime() {

     var outTime =  parseFloat($('#atten_out_time').val());
    var inTime =   parseFloat($('#atten_in_time').val());
    var timeDiff = outTime - inTime;
    if(inTime == 0  || outTime == 0){
      alert('Please Select Employees and Input Out Time for Attendance Out');
      return;
    }

    if (timeDiff < 0) {
      timeDiff += 24;

    }
    if (outTime > 23 || outTime < 0) {
      $('#atten_out_time').val('');
      $('#lbl_all_emp_ot_hours').text('');

    } else
    {
              document.getElementById("atten_out_submit_button").disabled = false;
          if(checkThisDateIsFriday()){
                $('#lbl_all_emp_ot_hours').text("Friday, Basic : 0, Overtime: "+timeDiff);
          }else {
                if (timeDiff >= 10)
                {
                  ot = timeDiff - 10;
                  var otText = 'Basic + OT : 10+' + '' + ot;
                  $('#lbl_all_emp_ot_hours').text(otText);
                } else {

                  ot = timeDiff;
                  var otText = 'Total Hours :' + ot;
                  $('#lbl_all_emp_ot_hours').text(otText);
                }
          }

    }

  }

  function checkThisDateIsFriday(){

      var date = document.getElementById('selected_atten_date').value;
      date    =  new Date(date);
      if (date.getDay() == 5) {
      // Friday
      return true;
      }
     return false;


  }





  // get Employee List for Out
   function searchingAlreadyAttendanceINEmployeeList() {

    var proj_name = $('select[name="proj_name"]').val();
    var date = $('input[name="date"]').val();

    var  allows_days =  $('input[name="allows_days"]').val();
    var emp_ids = $('input[name="multi_emp_id"]').val();

    var isNightShift = document.getElementById("night_shift").checked;
        isNightShift = isNightShift == true ? 1: 0;

    var currentDate = new Date();
    var input_date =new Date(date);
    var day_diff = currentDate.getDate()-input_date.getDate()

    // if(day_diff > allows_days || day_diff < -1){
    //     showSweetAlertMessage('error',"Select Valid Date ");
    //     return;
    // }

    $('#employee_entry_time_list_view').html('');
    $('#atten_in_time').val(0);
    $('input[name="selected_atten_date"]').val(date);
    document.getElementById("lbl_total_selected_counter").innerText = "";

    if (proj_name == "" || date == "") {
      showSweetAlertMessage('error','Please Input Valid Data');
      return;
    }
      $.ajax({
        type: 'POST',
        dataType: 'json',
        data: {
          proj_name: proj_name,
          date: date,
          isNightShift: isNightShift,
           emp_ids:emp_ids
        },
        url: "{{ route('show-employee-out.time') }}",
        success: function(response) {
            if (response.status != 200) {
                showSweetAlertMessage(response.error,response.message);
                return;
            }
            var attendance_in_time = 0;
            var rows = "";
            var counter = 1;
            $('#employee_list_section').removeClass('d-none').addClass('d-block');

            $.each(response.data, function(key, value) {
              attendance_in_time = value.emp_io_entry_time
              rows += `
                    <tr>
                        <td>${counter++}</td>
                        <td>${value.employee_id}</td>
                        <td>${value.employee_name}</td>
                        <td>${value.emp_io_date}-${value.emp_io_month}-${value.emp_io_year}</td>
                        <td> ${value.emp_io_shift == "1" ? 'Nigth Shift' : 'Day Shift'} </td>
                        <td> ${value.hourly_employee == 1 ? 'Hourly':'Basic'} </td>
                        <td>${value.catg_name}</td>
                        <td>${value.emp_io_entry_time}</td>
                        <td>${value.employee_id}</td>
                        <td style="color:#fff">${value.emp_io_id}</td>
                        <td> <input type="checkbox"  onclick="countSelectedCheckBox(${value.emp_auto_id})"  name="entry_out_checkbox-${value.emp_io_id}" id="entry_out_checkbox-${value.emp_io_id}" value="0"></td>
                        <td>
                              <div class="row  align-items-center">
                                  <input type="hidden" id="emp_io_id${value.emp_io_id}" name="emp_io_id_list[]" value="${value.emp_io_id}">
                                  <input type="hidden" id="shift${value.emp_io_id}" value="${value.emp_io_shift}">
                                  <input type="hidden" id="in_time${value.emp_io_id}" value="${value.emp_io_entry_time}">
                               </div>
                          </td>

                    </tr>
                    `
            });
            $('#employee_entry_time_list_view').html(rows);
            $('#atten_in_time').val(attendance_in_time);
        },
        error:function(response){
            $('#employee_list_section').removeClass('d-block').addClass('d-none');

            showSweetAlertMessage(response.error,response.message);
            return;
        }
      });

  }


   // Check Uncheck all Employee
    function checkUnCheckAllEmployeeForAttendanceOut(){

        let myTable = document.getElementById('employee_entry_time_list_view');
          var total_selected = 0;
        for (let row of myTable.rows) {
            allCell = row.cells;
            var chkboxId = "entry_out_checkbox-" + allCell[9].innerText;
            document.getElementById(chkboxId).checked = !(document.getElementById(chkboxId).checked) ;
           if(document.getElementById(chkboxId).checked)
                   total_selected += 1;
        }
     document.getElementById("lbl_total_selected_counter").innerText = "Total Selected: "+total_selected;

    }

          // checkbox click event
    function countSelectedCheckBox(value){

        let myTable = document.getElementById('employee_entry_time_list_view');
        var total_selected = 0;
            for (let row of myTable.rows) {
                allCell = row.cells;
                var chkboxId = "entry_out_checkbox-" + allCell[9].innerText;
                if(document.getElementById(chkboxId).checked)
                   total_selected += 1;

            }
        document.getElementById("lbl_total_selected_counter").innerText = "Total Selected: "+total_selected;

    }

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

</script>




@endsection
