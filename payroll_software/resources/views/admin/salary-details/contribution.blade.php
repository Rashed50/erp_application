@extends('layouts.admin-master')
@section('title') Employee CPF Contribution @endsection
@section('content')

<div class="row bread_part">
  <div class="col-sm-12 bread_col">
    <h4 class="pull-left page-title bread_title">Employee CPF Contribution</h4>
    <ol class="breadcrumb pull-right">
      <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
      <li class="active">Employee CPF Contribution</li>
    </ol>
  </div>
</div>

<div class="row">
  <div class="col-md-2"></div>
  <div class="col-md-8">
    @if(Session::has('success'))
    <div class="alert alert-success alertsuccess" role="alert">
      <strong>Successfully!</strong> Set CPF Contribution Amount.
    </div>
    @endif
  </div>
  <div class="col-md-2"></div>
</div>



<div class="row">
  <div class="col-md-2"></div>
  <div class="col-md-8">
    <div class="card">
      <div class="card-body card_form">
        <div class="form-group row custom_form_group">
          <label class="col-sm-3 control-label">Employee ID:</label>
          <div class="col-sm-4">
            <input type="text" class="form-control typeahead" placeholder="Input Employee ID" name="emp_id" id="emp_id_search" onkeyup="empSearch()" onfocus="showResult()" onblur="hideResult()">
            <div id="showEmpId"></div>
            <span id="error_show" class="d-none" style="color: red"> Employee Id Dosen,t Match!</span>
          </div>
          <div class="col-sm-3">
            <button type="submit" onclick="contribution()" style="margin-top: 2px" class="btn btn-primary waves-effect">SEARCH</button>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-2"></div>
</div>

<div class="row">
  <div class="col-md-2"></div>
  <div class="col-md-8">
    <form class="" action="{{ route('update-contribution.amount') }}" method="post">
      @csrf
      <div id="contribution-form" class="card d-none">
        <div class="card-body card_form">
          <input type="hidden" name="empId" id="emplIdShow" value="">
          <div class="form-group row custom_form_group">
            <label class="col-sm-3 control-label">Employee Name:</label>
            <div class="col-sm-7">
              <input type="text" class="form-control" disabled id="employeeName" value="">
            </div>
          </div>
          <div class="form-group row custom_form_group">
            <label class="col-sm-3 control-label">IQama No:</label>
            <div class="col-sm-7">
              <input type="text" class="form-control" disabled id="employeeIQama" value="">
            </div>
          </div>
          <div class="form-group row custom_form_group">
            <label class="col-sm-3 control-label">Contribution Amount:</label>
            <div class="col-sm-7">
              <input type="text" class="form-control" name="amount" id="contributionAmount" value="">
            </div>
          </div>
          <div class="form-group row custom_form_group">
            <label class="col-sm-3 control-label">Saudi Tax:</label>
            <div class="col-sm-7">
              <input type="text" class="form-control" name="tax" id="saudiTax" value="">
            </div>
          </div>
        </div>
        <div class="card-footer card_footer_button text-center">
          <button type="submit" class="btn btn-primary waves-effect">SAVE</button>
        </div>
      </div>
    </form>
  </div>
  <div class="col-md-2"></div>
</div>

<!-- script -->
<script type="text/javascript">
  function contribution() {
    var employeeID = $("input[name='emp_id']").val();
    if (employeeID != "") {
      $.ajax({
        type: 'POST',
        url: "{{ route('emp-information-for.set-contribution') }}",
        data: {
          employeeID: employeeID
        },
        success: function(response) {
          if (response.data) {

            $('div[id="contribution-form"]').addClass('d-block').removeClass('d-none');
            $('span[id="error_show"]').removeClass('d-block').addClass('d-none');

            $('input[id="emplIdShow"]').val(response.data.emp_auto_id);
            $('input[id="employeeName"]').val(response.data.employee_name);
            // alert(response.data.employee_name);
            $('input[id="employeeIQama"]').val(response.data.akama_no);
            $('input[id="contributionAmount"]').val(response.data.salarydetails.cpf_contribution);
            $('input[id="saudiTax"]').val(response.data.salarydetails.saudi_tax);
          } else {
            $('div[id="contribution-form"]').addClass('d-none').removeClass('d-block');
            $('span[id="error_show"]').removeClass('d-none').addClass('d-block');
          }

        }
      });
    } else {

    }

  }
</script>



@endsection