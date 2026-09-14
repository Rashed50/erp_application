@extends('layouts.admin-master')
@section('content')

<style>
      /* Employee Information Table */
     #employeeinfo {
            font-family: Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }
        #employeeinfo td,
        #employeeinfo th {
            border: 1px solid #ddd;
            font-size: 10px;
            padding: 5px;
        }

        #employeeinfo tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        #employeeinfo tr:hover {
            background-color: #ddd;
        }

        #employeeinfo th {
            padding-top: 5px;
            padding-bottom: 5px;
            text-align: center;
            background-color: #EAEDED;
            color: black;
        }
        .td__value{
            text-align: center;
            font-style:bold;
        }
        .td__total{
            text-align: center;
            font-style:bold;
            color: black;
        }
        
    </style>


<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Dashboard</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="#">Dashboard</a></li>
            <li class="active">Home</li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-md-6 col-xl-3">
        <a href="https://asloobb.com/" target="_blank">
            <div class="mini-stat clearfix bx-shadow bg-white">
                 <span class="mini-stat-icon bg-secondary">
                        <img src = "{{asset('contents/admin')}}/assets/images/svg/building.svg" width="40px" height="40px" alt=""/>
                    </span>
                <div class="mini-stat-info text-right text-dark mini_stat_info">
                       Company Profile
                </div>
            </div>
         </a>
    </div>
    <div class="col-md-6 col-xl-3">
         <div class="mini-stat clearfix bx-shadow bg-white text-dark">
            {{-- <span class="mini-stat-icon bg-secondary">
                <img src = "{{asset('contents/admin')}}/assets/images/svg/people-fill.svg" width="40px" height="40px" alt=""/>
            </span> --}}
            {{-- <div class="mini-stat-info text-right text-dark mini_stat_info"> --}}
                Active = {{ $noOfAsloobEmp }} + {{ $noOfOtherEmp }} <br>
                Vacation = {{ $noOfEmpl_vacation }} <br>
                Total Employees = {{ $noOfEmpl_vacation+ $noOfAsloobEmp + $noOfOtherEmp }}
            {{-- </div> --}}
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="mini-stat clearfix bx-shadow bg-white">
             <span class="mini-stat-icon bg-secondary">
                <img src = "{{asset('contents/admin')}}/assets/images/svg/kanban-fill.svg" width="40px" height="40px" alt=""/>
            </span>
            <div class="mini-stat-info text-right text-dark mini_stat_info">
                 <span class="counter text-dark">{{ $noOfProjects }}</span>
                Total Running Project
            </div>
        </div>
    </div>
 

    <div class="col-md-6 col-xl-3">
        <div class="mini-stat clearfix bx-shadow bg-white">
             <span class="mini-stat-icon bg-secondary">
                <img src = "{{asset('contents/admin')}}/assets/images/svg/person-fill.svg" width="40px" height="40px" alt=""/>
            </span>
            <div class="mini-stat-info text-right text-dark mini_stat_info">
                 <span class="counter text-dark">{{ $noOfUsers }}</span>
                Total Users
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-xl-3">
        <!--<a href="{{ url('admin/company/vehicle/report/all-records') }}"   target="_blank">-->
        <!--    <div class="mini-stat clearfix bx-shadow bg-white">-->
        <!--        <span class="mini-stat-icon bg-secondary">-->
        <!--            <img src = "{{asset('contents/admin')}}/assets/images/svg/car-front.svg" width="40px" height="40px" alt=""/>-->
        <!--        </span>-->
        <!--        <div class="mini-stat-info text-right text-dark mini_stat_info">-->
        <!--            <span class="counter text-dark">{{ $total_vehicles }}</span>-->
        <!--                Total Vehicle-->
        <!--        </div>-->
        <!--    </div>-->
        <!--</a>-->
        <div class="mini-stat clearfix bx-shadow bg-white">
                <a href="{{ url('admin/company/vehicle/report/all-records') }}"   target="_blank">
                    <span class="mini-stat-icon bg-secondary">
                        <img src = "{{asset('contents/admin')}}/assets/images/svg/car-front.svg" width="40px" height="40px" alt=""/>
                    </span>
                    <div class="mini-stat-info text-right text-dark mini_stat_info">
                        <span class="counter text-dark">{{ $total_vehicles }}</span>
                            Total Vehicles
                    </div>
                </a>
                <table id="employeeinfo">
                    <tr>
                        <th>S.N</th>
                        <th>Project Name</th>
                        <th>Total</th>
                    </tr>
                    @foreach ($vehicle_summary as $vs)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $vs->proj_name }}</td>
                            <td>{{ $vs->no_of_vehicles }}</td>

                        </tr>

                    @endforeach
                </table>
            </div>
    </div>
    
     <!-- Yesterday Dayshift Attendance Summary -->
    <div class="col-md-6 col-xl-3">
        <table id="employeeinfo">                    
            <tr> <td colspan="6" class="td__value">Yesterday Day Shift Attendance</td></tr>
            <tr>
                <th>S.N</th>
                <th>Project Name</th>
                <!--<th>Shift</th>-->
                <th>Total</th>
                <th>Present</th>
                <th>Absent</th>
            </tr>
            @php
              $yest_dayshift_total_emp = 0;
              $yest_dayshift_present = 0;
             
              $yest_ave_total = 0;
              $yest_ave_present = 0;
              
            @endphp
            @foreach($yesterday_dayshift_attend_summary as $record)
            @php
              
               $yest_dayshift_total_emp += $record->total_emp ;
               $yest_dayshift_present += $record->total_present;
               
                if($record->proj_id == 29  || $record->proj_id == 42){
                    $yest_ave_total += $record->total_emp;  
                    $yest_ave_present += $record->total_present;
                }
               
            @endphp
                <tr>                           
                    <td>{{$loop->iteration }}</td>
                    <td>{{$record->proj_name}} </td>
                    <!--<td>{{ $record->is_night_shift == 1 ? 'Night':'Day'}}</td>-->
                    <td class="td__value">{{ $record->total_emp }}</td>
                    <td class="td__value">{{ $record->total_present }}</td>
                    <td class="td__value">{{ $record->total_emp - $record->total_present }}</td>
                </tr>
            @endforeach
             <tr>
                 <td colspan="2" class="td__total">Total</td>
                 <td class="td__total">{{$yest_dayshift_total_emp}}</td>
                 <td class="td__total">{{$yest_dayshift_present}}</td>
                 <td class="td__total">{{$yest_dayshift_total_emp - $yest_dayshift_present}}</td>
            </tr>

        </table>

        
    </div>

    
    <!-- Yesterday Nightshift Attendance Summary -->
    <div class="col-md-6 col-xl-3">
        <table id="employeeinfo">                    
            <tr> <td colspan="6" class="td__value">Yesterday Night Shift Attendance</td></tr>
            <tr>
                <th>S.N</th>
                <th>Project Name</th>
                <!--<th>Shift</th>-->
                <th>Total</th>
                <th>Present</th>
                <th>Absent</th>
            </tr>
            @php
              $yest_nightshift_total_emp = 0;
              $yest_nightshift_present = 0;   
              
             @endphp
            
            @foreach($yesterday_nightshift_attend_summary as $record)
            @php
              
               $yest_nightshift_total_emp += $record->total_emp ;
               $yest_nightshift_present += $record->total_present;
               
                if($record->proj_id == 29  || $record->proj_id == 42){
                    $yest_ave_total += $record->total_emp;  
                    $yest_ave_present += $record->total_present;
                }
              
            @endphp
                <tr>                           
                    <td>{{$loop->iteration }}</td>
                    <td>{{$record->proj_name}} </td>
                    <!--<td>{{ $record->is_night_shift == 1 ? 'Night':'Day'}}</td>-->
                    <td class="td__value">{{ $record->total_emp }}</td>
                    <td class="td__value">{{ $record->total_present }}</td>
                    <td class="td__value">{{ $record->total_emp - $record->total_present }}</td>
                </tr>
            @endforeach
            <tr>
                 <td colspan="2" class="td__total">Total</td>
                 <td class="td__total">{{$yest_nightshift_total_emp}}</td>
                 <td class="td__total">{{$yest_nightshift_present}}</td>
                 <td class="td__total">{{$yest_nightshift_total_emp - $yest_nightshift_present}}</td>
            </tr>
            
             <tr>
                 <td colspan="2" class="td__total">Avenue Mall (Day+Night)</td>
                 <td class="td__total">{{$yest_ave_total}}</td>
                 <td class="td__total">{{$yest_ave_present}}</td>
                 <td class="td__total">{{$yest_ave_total - $yest_ave_present}}</td>
            </tr>

        </table> 
        
    </div>
    
    <!-- Today Attendance Summary -->
    <div class="col-md-6 col-xl-3">
                <table id="employeeinfo">                    
                    <tr> <td colspan="6" class="td__value">Today Attendance</td></tr>
                    <tr>
                        <th>S.N</th>
                        <th>Project Name</th>
                        <th>Shift</th>
                        <th>Total</th>
                        <th>Present</th>
                        <th>Absent</th>
                    </tr>
                    @php    $ave_total_emp = 0; $ave_total_presence = 0;  $total_emp = 0;  $total_present = 0; @endphp
                    @foreach($attendance_summary as $record)
                    @php
                      $total_emp += $record->total_emp;
                      $total_present +=$record->total_present;
                      
                      if($record->proj_id == 29  || $record->proj_id == 42){
                        $ave_total_emp += $record->total_emp;  
                        $ave_total_presence += $record->total_present;
                      }
                      
                    @endphp
                        <tr>                           
                            <td>{{$loop->iteration }}</td>
                            <td>{{ $record->proj_name}}</td>
                            <td>{{ $record->is_night_shift == 1 ? 'Night':'Day'}}</td>
                            <td class="td__value">{{ $record->total_emp }}</td>
                            <td class="td__value">{{ $record->total_present }}</td>
                            <td class="td__value">{{ $record->total_emp - $record->total_present }}</td>
                        </tr>
                    @endforeach
                    <tr>
                         <td colspan="3" class="td__total">Today Total</td>
                         <td class="td__total">{{$total_emp}}</td>
                         <td class="td__total">{{$total_present}}</td>
                         <td class="td__total">{{$total_emp - $total_present}}</td>
                    </tr>
                     <tr>
                         <td colspan="3" class="td__total">Yesterday </td>
                         <td class="td__total"> {{ $noOfAsloobEmp + $noOfOtherEmp }}  </td>
                         <td class="td__total">{{$yesterday_present}}</td>
                         <td class="td__total"> {{ $noOfAsloobEmp + $noOfOtherEmp - $yesterday_present }} </td>
                    </tr> 

                </table>

                
    </div>
    
</div>
@endsection
