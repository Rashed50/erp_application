@extends('layouts.admin-master')
@section('title') Employee Bonus @endsection
@section('content')

@section('internal-css')
<style media="screen">
    a.checkButton {
        background: teal;
        color: #fff !important;
        font-size: 13px;
        padding: 5px 10px;
        cursor: pointer;
    }

</style>
@endsection

<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Employee Bonun Records</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active">Employee Bonus</li>
        </ol>
    </div>
</div>

<!-- Session Message Flash -->
<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-7">
        @if(Session::has('success'))
        <div class="alert alert-success alertsuccess" role="alert" style="margin-left: -20px">
            <strong>{{Session::get('success')}}</strong>
        </div>
        @endif
        @if(Session::has('error'))
        <div class="alert alert-warning alerterror" role="alert" style="margin-left: -20px">
            <strong>{{Session::get('error')}} </strong>
        </div>
        @endif
    </div>
    <div class="col-md-2"></div>
</div>
<!-- Employee Searching Section  -->
<div class="row">
    <div class="col-md-12">
                        <div class="card"><br>
                            <div class="card-body card_form" style="padding-top: 0;">
                                    <div  class="row form-group custom_form_group">
                                        <label class="col-md-2 control-label d-block" > Search By</label>
                                        <div class="col-md-2">

                                            <select class="form-select" name="searchBy" id="searchBy" required>
                                                <option value="employee_id">  Employee ID</option>
                                                <option value="akama_no">  Iqama </option>
                                                <option value="passfort_no">  Passport</option>

                                            </select>
                                        </div>

                                        <div class="col-md-2">
                                            <input type="text" placeholder="Enter ID/Iqama/Passport No"
                                                class="form-control" id="empl_info" name="empl_info"
                                                value="{{ old('empl_info') }}" required autofocus>
                                            <span id="employee_not_found_error_show" class="d-none"
                                                style="color: red"></span>
                                            @if ($errors->has('empl_info'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('empl_info') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                        <div class="col-md-2">
                                        <button type="submit"  onclick="singleEmoloyeeDetails()"   class="btn btn-primary waves-effect">SEARCH EMP.</button>
                                        </div>

                                        <div class="col-md-2">
                                            <button type="submit"  onclick="searchAnEmployeeBonusRecords()"    class="btn btn-primary waves-effect">BONUS SEARCH </button>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" data-toggle="modal" data-target="#bonus_report_modal"   class="btn btn-primary waves-effect">BONUS REPORT </button>
                                        </div>

                                    </div>
                            </div>
                        </div>
    </div>
</div>



<!--  Employee Bonus Modal -->
<div class="modal fade" id="employee_bonus_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Employee Bonus Insertion Form  <span class="text-danger" id="errorData"></span></h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
                <div class="modal-body">
                        <input type="hidden" id="emp_auto_id" name="emp_auto_id" value="">
                        <div class="form-group row custom_form_group">
                            <label class="col-sm-2 control-label">ID, Name & Iqama:</label>
                            <div class="col-sm-10">
                                <span id ="employee_details" style="color:red"> </span>
                            </div>
                        </div>
                        <div class="form-group row custom_form_group">
                            <label class="col-sm-2 control-label">Bonus Type</label>
                            <div class="col-md-10">
                                            <select class="form-select" name="bonus_type" id="bonus_type" required>

                                                <option value="5"> AnnualBonus </option>
                                                <option value="10"> Performance Bonus  </option>
                                                 <option value="15"> Festival Bonus  </option>
                                                  <option value="30"> Single Air Ticket  </option>
                                                   <option value="35">  Return Air Ticket </option>
                                                   <option value="37"> Round Air Ticket  </option>
                                                    <option value="40"> One Month Salary  </option>
                                                    <option value="45"> Leave Salary  </option>
                                                    <option value="50"> Bonus and Air Ticket  </option>




                                            </select>
                                            @if ($errors->has('bonus_type'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('bonus_type') }}</strong>
                                    </span>
                                    @endif
                            </div>
                        </div>

                        <div class="form-group row custom_form_group">
                            <label class="col-sm-2 control-label">Month</label>
                            <div class="col-md-10">
                                            <select class="form-select" name="month" id="month" required>
                                                <option value="1">January</option>
                                                <option value="2">February</option>
                                                <option value="3">March</option>
                                                <option value="4">April</option>
                                                <option value="5">May</option>
                                                <option value="6">June</option>
                                                <option value="7">July</option>
                                                <option value="8">August</option>
                                                <option value="9">September</option>
                                                <option value="10">October</option>
                                                <option value="11">November</option>
                                                <option value="12">December</option>
                                            </select>
                                            @if ($errors->has('month'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('month') }}</strong>
                                    </span>
                                    @endif
                            </div>
                        </div>

                        <div class="form-group row custom_form_group">
                            <label class="col-sm-2 control-label">Year</label>
                            <div class="col-md-10">
                                    <select class="form-select" id="year"  name="year" required>
                                        @foreach(range(date('Y'), date('Y')-5) as $y)
                                        <option value="{{$y}}" {{$y}}>{{$y}}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('year'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('year') }}</strong>
                                    </span>
                                    @endif
                            </div>
                        </div>

                        <div class="form-group row custom_form_group">
                            <label class="col-sm-2 control-label">Bonus Amount</label>
                            <div class="col-sm-6">
                                <input type="number" id="bonus_amount" class="form-control " name="bonus_amount"   min="0" step="1"  autofocus required>
                                    @if ($errors->has('bonus_amount'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('bonus_amount') }}</strong>
                                    </span>
                                    @endif
                            </div>

                        </div>

                        <div class="form-group row custom_form_group">
                            <label class="col-sm-2 control-label">Remarks</label>
                            <div class="col-sm-10">
                            <input type="text" id="remarks" class="form-control " name="remarks" >
                            </div>
                        </div>

                        <br><br>
                    <button type="submit" id="submitbutton" name="submitbutton" onclick="submitBonusSalaryInformationFormData()"  class="btn btn-success">Save</button>

                </div>

            <!-- </form> -->
        </div>
    </div>
</div>


<!-- Employee Bonus Records -->
<div class="row d-block" id= "emp_bonus_records_section">
    <div class="col-12">
        <div class="table-responsive">

            <table id="alltableinfo" class="table table-bordered custom_table mb-0">
                <thead>
                    <tr>
                        <th>S.N</th>
                        <th>Emp. Id</th>
                        <th>Name</th>
                        <th>Akama No</th>
                        <th>Sponer</th>
                        <th>Basic/Hourly</th>
                        <th>Bonus Type</th>
                        <th>Month,Year</th>
                        <th>Amount</th>
                        <th>Manage</th>
                    </tr>
                </thead>
                <tbody id="emp_bonus_records_table"></tbody>
            </table>
        </div>
    </div>
</div>



{{-- Bonus Report Modal --}}
<div class="modal fade" id="bonus_report_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Employee Bonus Report <span class="text-danger" id="errorData"></span></h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                    <div class="modal-body">
                        <div class="card-body card_form" style="padding-top: 0;">
                            <div class="form-group row custom_form_group">
                                <label class="col-sm-3 control-label">From:</label>
                                <div class="col-sm-5">
                                    <input type="date" class="form-control" name='from_date' id="from_date" value="{{ date("Y-m-d") }}">
                                </div>
                            </div>
                            <div class="form-group row custom_form_group">
                                <label class="col-sm-3 control-label"  >To:</label>
                                <div class="col-sm-5">
                                    <input type="date" class="form-control" name='to_date' id="to_date" value="{{ date("Y-m-d") }}">
                                </div>
                            </div>
                            <div class="form-group row custom_form_group">
                                <label class="col-sm-3 control-label" hidden >Report Type:</label>
                                <div class="col-sm-9" hidden>
                                    <select class="form-select" name="report_type" required>
                                    <option value="1">bonus list</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6 text-left">
                        <button type="button" class="btn btn-primary" onclick="showBonusReport()">Process</button>
                        </div>
                        <div class="col-sm-6 text-right">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">

    /* form validation */
    $(document).ready(function() {
        $("#employee_bonus_modal").validate({
            submitHandler: function(form) {
                return false;
            },
            rules: {
                bonus_amount: {
                    required: true,
                },

                month: {
                    required: true,
                },
                year: {
                    required: true,
                },
                bonus_type:{required:true,},

            },

            messages: {
                bonus_amount: {
                    required: "You Must Be Input This Field!",
                },
                month: {
                    required: "Please Select Month Name",
                },
                year: {
                    required: "Please Select Year",
                },
                bonus_type:{required:"Please Select Bonus Type"},

            },


        });

    });
    // Method For Reset All Loaded Data
    function resetEmpInfo() {
         // employee_info_section

    }
    $('#empl_info').keydown(function (e) {
        if (e.keyCode == 13) {
            singleEmoloyeeDetails();
        }
    })

    function showMessage(type , message){

           const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            })

                Toast.fire({
                    type: type,
                    title: message
                })
    }

    //   Single Employee Details Info
    function singleEmoloyeeDetails() {

        //alert(operationType);

        resetEmpInfo() // reset UI Employe Info
        var searchType = $('#searchBy').find(":selected").val();
        var searchValue = $("#empl_info").val();
        if ($("#empl_info").val().length === 0) {
            showMessage('error',"Pleaase Enter Employee ID, Iqama or Passport Number");
            return;
         }
            $.ajax({
                type: 'POST',
                url: "{{ route('active.employee.searching.searching-with-multitype.parameter') }}",
                data: {
                    search_by: searchType,
                    employee_searching_value: searchValue
                },
                dataType: 'json',
                success: function (response) {
                    if (response.success == false) {
                        $('#emp_auto_id').val(null);
                        $("span[id='employee_not_found_error_show']").text('Please Enter An Active Employee Id');
                        $("span[id='employee_not_found_error_show']").addClass('d-block').removeClass('d-none');
                        return ;

                    } else {
                        $("span[id='employee_not_found_error_show']").removeClass('d-block').addClass('d-none');
                       // https://asloobbpayroll.com/
                    }
                    if (response.total_emp > 1) {

                    } else {
                            anemp =  response.findEmployee;
                            $('#emp_auto_id').val(anemp[0].emp_auto_id);
                            $('#employee_details').text(anemp[0].employee_id+", "+anemp[0].employee_name+", "+anemp[0].akama_no+",Basic/Hourly: "+anemp[0].basic_amount+"/"+anemp[0].hourly_rent);
                            $('#employee_bonus_modal').modal('show');
                    }
                } // end of success
            }); // end of ajax calling

    }

    function submitBonusSalaryInformationFormData(){

        var emp_auto_id = $('#emp_auto_id').val();
        var month = $('#month').val();
        var year = $('#year').val();
        var amount = $('#bonus_amount').val();
        var remarks = $('#remarks').val();
        var bonus_type = $('#bonus_type').find(":selected").val();
        // check blank string
        if(!amount || !emp_auto_id || !month || !year || !bonus_type){
            showMessage('error',"Please Input All Required Information");
            return;
        }
        document.getElementById('submitbutton').disabled = true
        amount = parseInt(amount);
        $.ajax({

            type:'POST',
            url:"{{route('employee.salary.bonus.insert.request')}}",
            data:{
                emp_auto_id:emp_auto_id,
                month:month,
                year:year,
                bonus_amount:amount,
                bonus_type:bonus_type,
                remarks:remarks,
            },
            success:function(response){

                document.getElementById('submitbutton').disabled = false;
                if(response.status == 200){
                        $('#employee_bonus_modal').modal('hide')
                        showMessage('success',"Successfully Completed");
                        createBonusSalarySheet(response.data.bonus_auto_id);
                }else {
                        showMessage('error',response.error);
                }

            },
            error:function(response){

                showMessage('error','The given data was invalid');
                document.getElementById('submitbutton').disabled = false;
            }

        });

    }

    function createBonusSalarySheet(id){
        var url = '{{ route("employee.salary.bonus.paper.create", ":id") }}';
        url = url.replace(':id', id);
        window.open(url, '_blank');
    }

    function searchAnEmployeeBonusRecords(){


        var searchType = $('#searchBy').find(":selected").val();
        var searchValue = $("#empl_info").val();
        $.ajax({
            type:"GET",
            url:"{{ route('employee.salary.bonus.search') }}",
            data:{
                searchType:searchType,
                searchValue:searchValue,
            },
            dataType: 'json',
            success:function(response){

              if(response.status != 200){
                showMessage('error',response.message);
                return;
              }
               if(response.records.length ==0){
                showMessage('error','Bonus Record Not Found');
                return;
              }

                var counter = 0;
                var rows = '';
                $.each(response.records, function(key, value) {
                        counter++;
                        var bonusTypeEnum = value.bonus_type;
                        rows += `
                                <tr>
                                    <td>${counter}</td>
                                    <td>${value.employee_id}</td>
                                    <td>${value.employee_name}</td>
                                    <td>${value.akama_no}</td>
                                    <td>${value.spons_name}</td>
                                    <td>${value.hourly_employee == 1 ? 'Hourly' : 'Basic'}</td>
                                    <td>${getBonusTypeName(value.bonus_type)}</td>
                                    <td>${getMonthName(value.month) }, ${value.year}</td>
                                    <td>${value.amount }</td>
                                    <td>
                                    <a href="#" onClick="deleteEmployeeBonusRecord(${value.bonus_auto_id})" vallue="${value.bonus_auto_id}" title="Delete"><i class="fa fa-trash fa-lg delete_icon"></i></a>
                                    &nbsp;&nbsp;&nbsp;
                                    </td>
                                </tr>
                               `

                    });
                $('#emp_bonus_records_table').html(rows);

            }
        });
    }

    function deleteEmployeeBonusRecord(bonus_auto_id){
       // alert(bonus_auto_id);
           var id = bonus_auto_id;
        Swal.fire({
            title: 'Do You Want to Delete The Record',
            showCancelButton: true,
            focusConfirm: false,
            confirmButtonText: `Delete`,
            cancelButtonText: `Cancel`,
            }).then((result) => {
                if (result.value) {
                    var url = '{{ route("employee.salary.bonus.delete.request", ":id") }}';
                    url = url.replace(':id', id);

                    $.ajax({
                            type:"DELETE",
                            url: url ,
                            success:function(response){
                                 if(response.status == 200){
                                    showMessage('success',response.message);
                                    searchAnEmployeeBonusRecords();
                                 }else {
                                    Swal.fire(response.message, '', 'error')
                                    showMessage('error',response.message);
                                 }
                            }
                    });

                }
            })


    }


     function showBonusReport(){

            var baseUrl = '{{ route("employee.salary.bonus.paper.create", ":id") }}';
            // url = url.replace(':id', id);
            // window.open(url, '_blank');
            var from = $('#from_date').val();
            var to = $('#to_date').val();


            // 2. Construct parameters
            const params = new URLSearchParams({
                from_date: from,
                to_date: to,
                operation_type:2, // 1 for bonus slip , 2 for bonus report
            });

            // 3. Combine and Open
            const finalUrl = `${baseUrl}?${params.toString()}`;

            // '_blank' tells the browser to open in a new tab
            window.open(finalUrl, '_blank');

    }

    function getMonthName(monthNumber) {
        const date = new Date();
        date.setMonth(monthNumber - 1);
        return date.toLocaleString('default', { month: 'short' });
    }
    function getBonusTypeName(bonusTypeValue) {
        const bonusTypeNames = {
            5: 'Annual Bonus',
            10: 'Performance Bonus',
            15: 'Festival Bonus',
            30: 'Single Air Ticket',
            35: 'Return Air Ticket',
            37: 'Round Air Ticket',
            40: 'One Month Salary',
            45: 'Leave Salary',
            50: 'Bonus & Air Ticket'
        };
        return bonusTypeNames[bonusTypeValue] || 'Other Bonus';
    }

    // Reset Modal Previous Data
    $('#employee_bonus_modal').on('hidden.bs.modal', function (e) {
      $(this)
      .find("input,textarea,select").val('').end()
      .find("input[type=checkbox], input[type=radio]").prop("checked", "").end();
    })

     // salary closing modal hidden
     $('#employee_salary_closing_modal').on('hidden.bs.modal', function (e) {
      $(this)
      .find("input,textarea,select,hidden").val('').end()
      .find("input[type=checkbox], input[type=radio]").prop("checked", "").end();
    })


</script>

<!-- added this for Multiple Selection dropdownlist  -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta2/css/bootstrap-select.min.css" integrity="sha512-mR/b5Y7FRsKqrYZou7uysnOdCIJib/7r5QeJMFvLNHNhtye3xJp1TdJVPLtetkukFn227nKpXD9OjUc09lx97Q==" crossorigin="anonymous"/>
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta2/js/bootstrap-select.min.js" integrity="sha512-FHZVRMUW9FsXobt+ONiix6Z0tIkxvQfxtCSirkKc5Sb4TKHmqq1dZa8DphF0XqKb3ldLu/wgMa8mT6uXiLlRlw==" crossorigin="anonymous"></script>

@endsection
