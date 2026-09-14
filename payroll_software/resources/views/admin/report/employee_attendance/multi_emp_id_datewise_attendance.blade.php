<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Multi-ID Attend 
    </title>
    <!-- style -->
    <style>
        * {
            margin: 0;
            padding: 0;
            outline: 0;
        }


        @media print {
            .container {
                max-width: 98%;
                margin: 0 auto 10px;

            }
            
            @page {
                size: A4 landscape;
                margin: 15mm 0mm 10mm 0mm;
                /* top, right,bottom, left */

            }
            .print__button {
                  visibility: hidden;
               }

               th.td__friday, td.td__friday {
                background-color: #AED6F1;
                -webkit-print-color-adjust: exact;               
            }

            th.th_header, td.th_header {
                background-color: #B3E6FA;
                -webkit-print-color-adjust: exact;               
            }

            

             th.td__friday {
                background-color: red;
                -webkit-print-color-adjust: exact;
            } 

            table { page-break-after:auto }
            tr    { page-break-inside:avoid; page-break-after:auto }
            td    { page-break-inside:avoid; page-break-after:auto }
            thead { display:table-header-group }
            tfoot { display:table-footer-group }

        }


        .main__wrap {
            width: 95%;
            margin: 10px auto;
        }

        .header__part {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .title__part {
            text-align: center;
            font-size:12px;
        }
        .title_left__part{
            text-align: left;
            font-size:10px;
        }
        .address {
            font-size:10px;
        }
        .print__part{
            text-align: right;
            font-size:10px;
        }

        /* table part */
        .table__part {
            display: flex;
        }

        table {
            width: 100%;
            padding: 0px;
        }

        table,
        tr {
            border: 1px solid #333;
            border-collapse: collapse;
            margin: 0px;
            padding: 0px;
        }

        /* table tr {} */

        table th {
            font-size: 10px;
            border: 1px solid #333;
            font-weight: bold;
            color: #000;

        }

        table td {
            text-align: center;
            font-size: 10px;
            border: 1px solid #333;
            padding: 0px;
            margin: 0px;
            height:20px;

            /* Top,Right,Bottom,left */
        }
        .td__friday {
            background-color: red;
        }

        .th_header {
            background-color: #B3E6FA; 
            font-size: 12px;
            font-weight: bold;
            height:40px;
        }

        .td__s_n {
            font-size: 10px;
            color: black;
            text-align: center;
            width:20px;
        }

        .td__employee_id {
            font-size: 10px;
            color: red;
            text-align: center;
            font-weight: bold;
            padding: 0px;
            width:30px;
        }
        .td__emplyoee_name {
            font-size: 9px;
            color: blue;
            font-weight: 200;
            text-align: left;
            padding-left: 2px;
            width:130px;

        }

        .td__iqama {
            font-size:9px;
            color: black;
            font-weight: 100;
            text-align: center;
            width:50px;
        }

        .td__emp_trade {
            font-size: 9px;
            color: black;
            font-weight: 100;
            text-align: center;
            padding-left: 2px; 
            width:80px; 
        }

        .td__day{
            font-size: 9px;
            color: black;
            font-weight: 100;
            text-align: center;
            padding: 0px;
            margin:0px; 
        }
        .td__absent{
            font-size: 9px;
            color: red;
            font-weight: bold;
            text-align: center;
            padding: 0px;
            margin:0px; 
            
        }

        .td__total {
            font-size: 10px;
            color: black;
            text-align: center;
            font-weight: bold;
            padding: 0px;
        }

        a:link {
            color: white;
            background-color: white;
            text-decoration: none;
        }
    </style>
    <!-- style -->
</head>

<body>
    <div class="main__wrap">
        <!-- header part-->
        <section class="header__part">
            <!-- date -->
            <div class="title_left__part">
                <p> Attendance Report Month of <strong class="td__red__color"> {{ $monthName }}, {{$year}} </strong> </p>
            </div>
            <!-- title -->
            <div class="title__part">
                <h6>{{$company->comp_name_en}} <small>{{$company->comp_name_arb}} </small> </h6>
                <address class="address">
                    {{$company->comp_address}}
                </address>
                <br>
                <address class="address">
                   Multipel Employee Date to Date Attendance Summary
                </address>
               
            </div>
            <!-- print button -->
            <div class="print__part">
                <p> <strong>Print Date</strong> {{ Carbon\Carbon::now()->format('d/m/Y') }} </p>
                <button type="" onclick="window.print()" class="print__button">Print</button>
            </div>
        </section>
        <!-- table part -->
        <section class="table__part">
            <table>
                 <caption>  
                    <b>AW</b> = Attendance IN and OUT Pending,  &nbsp;
                    <b>TL</b> = Travel Leave,  &nbsp; 
                    <b>SL</b> = Sick Leave,  &nbsp; 
                    <b>PH</b> = Public Holiday,  &nbsp; 
                    <b>BW</b> = Bad Weather, &nbsp; 
                    <b>NW</b> = No Working at Site &nbsp;                 
                </caption>
                <thead>
                    <tr>
                        <th class="th_header" >S.N</th>
                        <th class="th_header">ID</th>
                        <th class="th_header">Name </th>
                        <th class="th_header">Iqama </th>
                        <th class="th_header">Trade </th>

                        @for($i=1; $i<=$numberOfDaysInThisMonth; $i++) @if($holidayArray[$i]==0) <th class="th_header">{{$day_name_in_month[$i]}} <hr>{{ $i }}</th>
                            @else
                            <th class="td__friday">{{$day_name_in_month[$i]}} <hr> {{ $i }}</th>
                            @endif

                            @endfor
                            <th  class="th_header">Total <br>Hours</th>
                            <th  class="th_header">Abs.</th>
                            <th  class="th_header">Basic</th>
                            <th  class="th_header">OT</th>
                            <th  class="th_header">Days</th>
                    </tr>
                </thead>
                <tbody>

                    @php
                    $snCounter = 1;
                    $grossWorkingHours = 0;
                    $gross_over_time = 0;
                    @endphp

                    @foreach($attendent_emp_list as $emp)

                    @php

                    $attendace_records = $emp->attendace_records;
                   
                  
                    @endphp
                    <tr style="border-bottom:0;">

                        <td class="td__s_n"> {{ $snCounter++ }}</td>
                        <td class="td__employee_id"> {{$emp->employee_id}}</td>
                        <td class="td__emplyoee_name"> {{ Str::limit($emp->employee_name,20) }} </td>
                        <td class="td__iqama">{{$emp->akama_no}}</td>
                        <td class="td__emp_trade">{{ Str::limit( $emp->catg_name,15) }},{{$emp->last_working_project_name}} </td>

                        @for($counter =1; $counter<= $numberOfDaysInThisMonth; $counter++)
                        @php 
                         if($counter < count($attendace_records))
                         { $arecord=$attendace_records[$counter]; }

                         $daily_work_hours=0 ;
                          $over_time=0 ;
                          if($arecord != null){
                           
                             $daily_work_hours = data_get($arecord,'daily_work_hours',0); 
                             $over_time = data_get($arecord,'over_time',0);
                              
                              
                             $daily_total_hours_array[$counter] += $daily_work_hours + $over_time; 
                            }
                             @endphp

                             @if($arecord == null)
                                @if($holidayArray[$counter]==0) 
                                <td class="td__absent">
                                <span>A</span>
                                </td>
                                @else
                                <td class="td__friday">
                                    <span></span>
                                </td>
                                @endif

                            @else
                              @if($holidayArray[$counter]==0) 
                                <td style="background-color: #{{$arecord->color_code}}">
                                <span>{{ $daily_work_hours > 0 ? $daily_work_hours : Str::upper(data_get( $arecord,'attendance_status',''))}}{{$over_time > 0 ? "/":""}}
                                    {{$over_time > 0 ? $over_time : ""}}</span>
                                </td>
                                @else
                                 @php   $totalHolidays -= 1;  @endphp
                                <td class="td__friday">
                                    <span>
                                        {{ $daily_work_hours > 0 ? $daily_work_hours : ""}}{{$over_time > 0 ? "/":""}}
                                        {{$over_time > 0 ? $over_time : ""}} </span>
                                </td>
                                @endif
                            @endif
                            @endfor                           
                            
                            <td class="td__total">{{ $emp->total_daily_work_hours + $emp->total_over_time}}</td>
                            <td class="td__total"> {{ $numberOfDaysInThisMonth -  $emp->total_working_days }}  </td>
                            <td  class="td__total">{{$emp->total_daily_work_hours }}</td>
                            <td class="td__total">{{$emp->total_over_time}}</td>
                            <td  class="td__total"> {{$emp->total_working_days}}  </td>

                            @php  
                            $grossWorkingHours += $emp->total_daily_work_hours  ;
                            $gross_over_time += $emp->total_over_time;
                            @endphp
                    </tr>
                    @endforeach
                    <br>
                    <tr>
                        <td colspan="5" class="td__total">Total </td>

                        @for($day = 1; $day<= $numberOfDaysInThisMonth; $day++)
                         <td class="td__total">{{ $daily_total_hours_array[$day] }}</td>
                            @endfor
                             <td class="td__total">{{ $grossWorkingHours+$gross_over_time}}</td>
                            <td></td>
                            <td class="td__total">{{ $grossWorkingHours}}</td>
                            <td class="td__total">{{ $gross_over_time}}</td>
                            <td class="td__total"></td>
 
                    </tr>
                </tbody>
                </tbody>
            </table>
        </section>
          <!-- Project Color Code ---------- -->
          <br>
        <section>
            <table style="width: 40%">
                <tr class="th_header">
                    <th>S.N <br><br></th>
                    <th>Project Name <br> <br></th>
                    <th>Color Code <br><br></th>
                </tr>
                @foreach($working_proj_list as $p)
                    <tr>
                        <td>{{$loop->iteration}}</td>
                        <td style="padding-left:5px;text-align: left;">{{$p->proj_name}}</td>
                        <td style="background-color: #{{$p->color_code}}">{{$p->color_code}}</td>
                    </tr>
                @endforeach
            </table>
            
        </section>
             <!--  Signature ---------- -->
        
        <br><br>
    <section>
        {{-- Officer Signature --}}
        <div class="row" style="padding-top: 20px;">
            <div class="officer-signature" style="display: flex; justify-content:space-between; font-size:9px">
              <p>   <b> {{$prepared_by}} </b> <br> Prepared By</p>
                <p><br>Checked By</p>
                <p><br>Verified By</p>
            </div>
        </div>
        {{-- Officer Signature --}}
    </section>
    </div>
</body>

</html>