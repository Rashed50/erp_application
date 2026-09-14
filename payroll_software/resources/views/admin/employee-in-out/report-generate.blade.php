<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Attendence</title>
    <link rel="stylesheet" href="{{ asset('contents/admin') }}/assets/css/salary-bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('contents/admin') }}/assets/css/salary-style.css">

<style>
   /* table th {
            font-size: 13px;
        }

        table td {
            text-align: left;
            font-size: 13px;

        } */
        th,
        td {
            padding: 5px 10px 2px 10px;
            /* Top,Right,Bottom,left */
        }

        .td__friday{
          background-color:#AED6F1;
        }
</style>
  </head>
<body>
  <section class="salary">
      <div class="container">

        <!-- salary header -->
        <div class="row align-center">
            <div class="col-md-6">
                <div class="salary__header">
                    {{-- <h3><a href="#"> </a></h3> --}}
                    <div class="project_info" style="margin-left:0">
                        <p class="project_name"> All Employee Attendence Report</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6"></div>
        </div>
        <!-- salary bottom header -->
        <div class="salary__header-bottom">
          <div class="row">
            <div class="col-md-12">
              <div class="company_information" style="text-align:center">
                <h4>{{$company->comp_name_en}}  <small>{{$company->comp_name_arb}} </small> </h4>
                <address class="address">
                     {{$company->comp_address}}
                </address>
              </div>
            </div>

            {{-- second row --}}
            <div class="col-md-12">
              <div class="row">

                <div class="col-md-8">
                  <div class="project_info" style="margin-left:0">
                      <p class="project_name"> <strong> Month & Year: </strong> {{ $monthName }}-{{ $year }} </p>
                      <p class="project_name">  <strong>Project Name : </strong> {{ $projectName->proj_name }} </p>
                      <p class="project_name">  <strong>Sponser Name : </strong> {{ $sponserName->spons_name ?? 'All Sponse' }} </p>
                  </div>
                </div>


                <div class="col-md-4">
                  <div class="salary__download" style="text-align:right; display:flex-; justify-content: right; align-items:center; margin-bottom: 10px">
                    <p> <strong>Print Date</strong> {{ Carbon\Carbon::now()->format('d/m/Y') }} </p>
                    <a onclick="window.print()" class="print-button">PDF Or Pirnt</a>
                    {{-- {{ route('salary-report-pdf',$month) }} --}}
                  </div>

            </div>

          </div>
        </div>
        <!-- salary table -->

          <div class="salary__table-wrap">
              <div class="row">
                  <div class="col-md-12">
                      <table class="table attend__table table-responsive salary__table">
                        {{-- table-bordered attend__table --}}
                        <!-- table heading -->
                        <thead>
                          <tr>
                            <th>ID</th>
                            <th>Name & <br> Iqama, Trade </th>
                            <!-- Date  -->
                            @for($i=1; $i<=$numberOfDaysInThisMonth; $i++)
                            @if($holidayArray[$i] == 0)
                              <th>{{ $i }}</th>
                            @else 
                            <th style="background-color:#AED6F1">{{ $i }}</th>
                            @endif

                            @endfor
                            <!-- end date -->
                            <th>Overtime</th>
                            <th>Total</th>
                          </tr>
                        </thead>
                        <!-- table body -->
                        <tbody>
                          @foreach($directEmp as $emp) <!-- per employee -->
                          <tr>
                            <td> <span>{{ $emp->employee_id }}</span> </td>
                            <td> <span>{{ $emp->employee_name }} <br> {{ $emp->akama_no }}, {{ $emp->category->catg_name }}  </span> </td>
                            <!-- php block -->
                            @php
                            $allAttenDays = $emp->atten;
                            $perDayHours = $emp->perDayHours;
                            
                            $totalWorkingHour = $emp->totalWorkingHour;
                            $totalWorkingDays = $emp->totalWorkingDays;
                            @endphp


                          @for($counter =1;$counter<=$numberOfDaysInThisMonth+1;$counter++)
                            <div class="">

                            @if($holidayArray[$counter] == 0)
                                <td >
                                   <span>@if($counter <=$numberOfDaysInThisMonth)
                                   {{$totalWorkingDays[$counter]}}
                                   @else
                                   {{"="}}
                                   @endif
                                  </span>
                                 <span>{{ $allAttenDays[$counter]}}</span>
                                 </td>
                            @else 
                            <td style="background-color:#AED6F1"></td>
                            <!-- <td class="td__friday">22</td> -->
                            @endif
                                 
                            </div>
                            @endfor
                              <td>{{$totalWorkingHour}}</td>
                          </tr>
                          @endforeach
                          <br>
                          <tr>
                             <td>=</td>
                            <td>Total Hours</td>
                            @for($day = 1; $day<=$numberOfDaysInThisMonth+1; $day++)
                                <td>{{ $totalHoursList[$day] }}</td>
                            @endfor
                          </tr>
                        </tbody>


                      </table>

                  </div>
              </div>
          </div>
      </div>
  </section>
</body>
</html>
