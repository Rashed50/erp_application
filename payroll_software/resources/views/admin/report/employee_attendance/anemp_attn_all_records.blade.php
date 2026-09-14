<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atten. All Records
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
                max-width: 99%;
                margin: 0 auto 10px;

            }
            
            @page {
                size: A4 landscape;
                margin: 10mm 0mm 5mm 0mm;
                /* top, right,bottom, left */

            }
            .print__button {
                  visibility: hidden;
               }

               th.td__friday, td.td__friday {
                background-color: red;
                -webkit-print-color-adjust: exact;               
            }

            th.th_header, td.th_header {
                background-color: #B3E6FA; 
                -webkit-print-color-adjust: exact;               
            }

           


            /* th.td__friday {
                background-color: #AED6F1;
                -webkit-print-color-adjust: exact;
            } */

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
            font-size: 11px;
            border: 1px solid #333;
            font-weight: bold;
            color: #000;

        }

        table td {
            text-align: center;
            font-size: 11px;
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
            height: 30px; 
            font-size: 12px;
            font-weight: bold;
        }
        
        .project_th_header {
            background-color: #B3E6FA;
            height: 15px;
            font-size: 10px;
            font-weight: bold;
            padding: 2px;
        }
        
        .td__s_n {
            font-size: 10px;
            color: black;
            text-align: center;
            width:20px;
        } 

        .td__month_year {
            font-size:11px;
            color: black;
            text-align: center;
            padding:0px;          
        }

        .td__project {
            font-size: 10px;
            color: black;
            font-weight: 100;
            text-align: center;
            padding-left: 2px; 
            width:100px; 
        }

        .td__day{
            font-size: 11px;
            color: black;
            font-weight: 100;
            text-align: center;
            padding: 0px;
            margin:0px; 
        }
        .td__absent{
            font-size: 11px;
            color: red;
            font-weight: bold;
            text-align: center;
            padding-right: 2px;
            margin:0px; 
            
        }

        .td__total {
            font-size: 11px;
            color: black;
            text-align: right;
            font-weight: bold;
            padding-right: 2px;
        }

        a:link {
            color: white;
            background-color: white;
            text-decoration: none;
        }

        .em_td_header {
            background-color: #B3E6FA; 
             font-size: 12px;
            font-weight: bold;
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
                <p> <strong class="td__red__color"></strong> </p>
            </div>
            <!-- title -->
            <div class="title__part">
                <h6>{{$company->comp_name_en}} <small>{{$company->comp_name_arb}} </small> </h6>
                <address class="address">
                    {{$company->comp_address}}
                </address> <br>
                <!-- <p>An Employee Last One Year Attendance Record Details.</p> -->
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
               
                <tr>
                    <td class="em_td_header">Employee ID</td>
                    <td>{{$emp->employee_id}}</td>
                    <td  class="em_td_header">Mobile Number</td>
                    <td>{{$emp->mobile_no}}</td>
                    <td class="em_td_header">Sponsor</td>
                    <td>{{$emp->spons_name}}</td>
                </tr>
                <tr>
                    <td class="em_td_header">Employee Name</td>
                    <td>{{$emp->employee_name}}</td>
                    <td class="em_td_header">Iqama Number</td>
                    <td>{{$emp->akama_no}}</td>
                    <td class="em_td_header">Job Status</td>
                    <td>{{$emp->title}}</td>
                </tr>
                <tr>
                    <td class="em_td_header">Camp Name</td>
                    <td>{{$emp->ofb_name}}</td>
                </tr>
            </table>
            
        </section>
  
        <section class="table__part">
            <table>
                 <caption style="padding-top: 5px;">An Employee Last One Year Attendance Details</caption>
                <thead>
                    <tr>
                        <th class="th_header" >S.N</th>
                        <th class="th_header">Month <br> Year</th>
                        <th class="th_header">Project Name </th>                     
                        @for($i=1; $i<= 31; $i++)  
                            <th class="th_header"> {{ $i }}</th>                         
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
                            $holiday_array  = $emp->holiday_array;
                            $previous_day_project_id = null;
                        
                    @endphp
                    <tr style="border-bottom:0;">

                        <td class="td__s_n"> {{ $snCounter++ }}</td>
                        <td class="td__month_year">{{$emp->working_month}} <br> {{$emp->working_year}}</td>
                        <td class="td__project"> {{$emp->last_working_project_name}} </td>                      
                       
                    @for($counter = 1; $counter <= 31; $counter++)
                        @php 
                                                          
                            if($counter < count($attendace_records))
                            { $arecord = $attendace_records[$counter]; }
                            
                            $daily_work_hours=0 ;
                            $over_time=0 ;
                            if($arecord != null){                        
                                $daily_work_hours = data_get($arecord,'daily_work_hours',0); 
                                $over_time = data_get($arecord,'over_time',0);
                                $today_project_id = data_get($arecord,'proj_id',null);                            
                            }
                            
                        @endphp

                        @if($arecord == null)
                            @if($holiday_array[$counter]==0) 
                                <td class="td__absent">
                                <span>A</span>
                                </td>
                            @else
                                <td class="td__friday">
                                    <span></span>
                                </td>
                            @endif
                        @else
                            @if($holiday_array[$counter]==0) 
                                    <td style="background-color: #{{$arecord->color_code}}">
                                    <span>{{ $daily_work_hours > 0 ? $daily_work_hours : Str::upper(data_get( $arecord,'attendance_status',''))}}
                                        {{$over_time > 0 ? ("/".$over_time):""}}                                      
                                    </span>
                                    </td>                                 
                            @else
                            @php
                               $emp->total_holiday -= 1; 
                            @endphp                                
                                    <td class="td__friday"> 
                                        <span>{{ $daily_work_hours > 0 ? $daily_work_hours : ""}}
                                            /{{$over_time > 0 ? ($over_time):""}} </span>
                                    </td>
                            @endif
                        @endif
                    @endfor  

                        <td class="td__total">{{ $emp->total_daily_work_hours + $emp->total_over_time}}</td>
                        <td class="td__total"> {{ $emp->number_of_day_this_month -  $emp->total_working_days }}  </td>
                        <td  class="td__total">{{$emp->total_daily_work_hours }}</td>
                        <td class="td__total">{{$emp->total_over_time}}</td>
                        <td  class="td__total"> {{$emp->total_working_days}}  </td>
                        @php  
                        $grossWorkingHours += $emp->total_daily_work_hours  ;
                        $gross_over_time += $emp->total_over_time;
                        @endphp
                </tr>
                @endforeach
                    

                   
                </tbody>
                </tbody>
            </table>
        </section>
         <!-- Project Color Code ---------- -->
         <br>
         <section style="overflow: hidden;">
             <table style="width: 70%; float: left; margin-right:5px">
                 <tr >
                     <th  class="project_th_header" >S.N</th>
                     <th  class="project_th_header" > Project Name    </th>
                     <th class="project_th_header"  > Color Code   </th>
                     <th  class="project_th_header" > Timekeeper Name   </th>
                     <th class="project_th_header"  > Mobile Number   </th>
                 </tr>
                 @foreach($working_proj_list as $p)
                     <tr>
                         <td>{{$loop->iteration}}</td>
                         <td style="padding-left:5px;text-align: left;">{{$p->proj_name}}</td>
                         <td style="background-color: #{{$p->color_code}}">{{$p->color_code}}</td>
                         <td> {{$p->timekeeper_name}}</td>
                         <td> {{$p->timekeeper_mobile}}</td>

                     </tr>
                 @endforeach
             </table>


             <table style="width: 28%; float: left;">
                <tr>
                    <th  class="project_th_header" > Code   </th>
                    <th class="project_th_header"  > Meaning   </th>
                    <th  class="project_th_header" > Code   </th>
                    <th class="project_th_header"  > Meaning   </th>
                </tr>
                    <tr>
                        <td  >Friday</td>
                        <td style="background-color:red"></td>
                        <td  >BW</td>
                        <td> Bad Weather  </td>
                    </tr>
                    <tr>
                        <td  >AW</td>
                        <td> IN but not OUT  </td>
                        <td >PH</td>
                        <td > Public Holiday  </td>
                    </tr>
                    <tr>
                        <td >SL</td>
                        <td >Sick Leave  </td>
                        <td  >NW</td>
                        <td > No Work at Site  </td>
                    </tr>
                    <tr>
                        <td >TL</td>
                        <td > Travel Leave  </td>
                    </tr>

             </table>

         </section>
        <!--  Signature ---------- -->
     
        <br> 
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