<!DOCTYPE html>
<html lang="en">

<head>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Performance Form</title>
    <!-- style -->
    <link href="https://db.onlinewebfonts.com/c/b6a69f971181a779fa603b85e27cf9b7?family=Algerian+Mesa+Plain" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            outline: 0;
        }


        @media print {
            .container {
                max-width: 95%;
                margin: auto ;
            }

            @page {
                size: A4 portrait;
                margin: 10mm 2mm 10mm 10mm;
                /* top, right,bottom, left */

            }

            .print__button {
                visibility: hidden;
            }


            table { page-break-after:auto }
            tr    { page-break-inside:avoid; page-break-after:auto }
            td    { page-break-inside:avoid; page-break-after:auto }
            thead { display:table-header-group }
            tfoot { display:table-footer-group }
           

        }


        .main__wrap {
            width: 95%;
            margin: 5px auto;
           
        }

        .header__part {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .title__part {
            text-align: center;
            font-size: 12px;
            color: #10385A;
        }

        .title_left__part {
            text-align: left;
            font-size: 12px;
        }

        .address {
            font-size: 12px;
        }

        .print__part {
            text-align: right;
            font-size: 10px;
            padding-right: 20px;
        }

        /* table part */
        .table__part {
            display: flex;
        }

        table {
            width: 99%;
            padding: 0px;
            align: center
        }

        table,
        tr {
            border: 1px ridge gray;
            border-collapse: collapse;
            margin: 0px;
            padding: 0px;
        }

        table th {
            font-size: 12px;
            border: 1px ridge gray;
            font-weight: bold;
            padding: 5px;
            color: #000;
            text-transform: capitalize;
            font-family: "Times New Roman", Times, serif;
        }

        table td {
            text-align: center;
            font-size: 13px;
            border: 1px ridge gray;            
            padding: 5px;
            margin: 0px;
            text-transform: capitalize;
            font-family: "Times New Roman", Times, serif;
            /* Top,Right,Bottom,left */
        } 

        .form_title{
            background-color:#c1b49a;  padding:10px; "
            font-family: "Algerian Mesa Plain";
        }
 
        .td__fixed_width{
            font-size: 14px;
            color: black;
            text-align: left;
            padding-left: 10px;
            font-weight: bold;
            width:120px;  
            color: black;
            background:#B3E6FA;  
            height: 20px;        
        } 

        .td__emp_id {
            font-size: 15px;
            color: black;
            text-align: left;
            font-weight: bold;
            padding-left: 10px;    
                   
        } 

        .td__emp_name {
            font-size: 13px;
            color: black;
            text-align: left;
            font-weight: bold;
            padding-left: 10px;            
        }         
       
        .td__iqama{
            font-size: 12px;
            color: black;
            text-align: left;
            font-weight: bold;
            padding-left: 10px; 
        } 

        .td__in_word{
            font-size: 13px;
            color: black;
            text-align: left;
            font-weight: bold;
            padding-left: 10px; 
        }

        .salary__section {     
            border: 1px solid gray ;       
            padding:5px;
            background:#FBD2AF;  
            font-weight: bold;
            word-break: normal;
            text-align: center;              
            font-size: 15px;
         } 
        .recommendation__section {     
            border: 0px solid #B3E6FA;             
            padding:5px;
            background:#FBD2AF;   
            font-weight: bold;
            word-break: normal;
            text-align: center; 
            vertical-align: top;
            font-size: 15px;
         } 

         .remarks__section{
            font-size: 14px;
            color: black;
            text-align: center;
            padding-left: 10px;
            font-weight: bold;          
            color: black;
            background:#EAEDED;
            height: 7px;  
            border: 0px solid #FBD2AF;    
        }


         .signature__seection {                 
            padding-right:20px;  
            font-weight: bold;
            word-break: normal;
            text-align: right; 
            vertical-align: bottom;
            font-size: 14px;
            width: 50%;
         } 

        
         .remarks {     
                       
            padding:5px; 
            font-weight: bold;
            word-break: normal;
            text-align: left; 
            vertical-align: top;
            font-size: 13px;
            word-wrap: break-word;
            overflow:;
         } 
        .box__thumb_print{
            margin-left:10px;
             /* border: 1px solid #abcccc;
             background:#B3E6FA; */
             padding:10px;
             text-align:center; margin-right:20px;
        }
        .box__received_by{
            border: 1px solid #abcccc; background:#B3E6FA; padding:10px; text-align:center; margin-right:20px;
        } 

    </style>
    <!-- style -->
</head>

<body>
    <div class="main__wrap">
    <section class="header__part">
            <!-- Left Part -->
            <div class="title_left__part">                 
            </div>
            <!-- title -->
            <div class="title__part"   >
                               
            </div>
               <!-- Right Part new logo ration 1:2.31 -->
            <div class="print__part">
                 <button type="" onclick="window.print()" class="print__button">Print</button>
                 <!--<img src="{{ asset('contents/admin') }}/assets/images/logo_icon.png" alt="" class="image-resize"  width="160" height="80">-->
                 <img src="{{ asset($company->com_logo) }}"  alt="" width="184px" height="80px">
            </div>
        </section>
        <section style="padding:10px; text-align:center; font-weight:bold; margin-bottom:10px;"> <strong class="form_title">
            {{$form_title}}
            </strong>  </section>
        
        <section class="table__part">
            <table> 
                        
                <tbody>           
              
                <tr > 
                  <td class="td__fixed_width"> Employee ID </td>
                  <th class="td__emp_id" colspan="2" > <span>{{ $employee->employee_id }} </span> </th>                  
                  <td class="td__fixed_width"> Iqama No </td>
                  <td class="td__iqama"> {{ $employee->akama_no }}  </td>   
                 </tr>

                 <tr>
                    <td class="td__fixed_width"> Employee Name </td>
                    <td class="td__emp_name" colspan="2"> {{ $employee->employee_name }} </td>
                    <td class="td__fixed_width"> Passport No </td>
                    <td class="td__emp_name" >{{ $employee->passfort_no }}  </td>  
                 </tr>
                 <tr>
                    <td class="td__fixed_width" >Designation </td>
                    <td class="td__emp_name" colspan="2"> {{ $employee->catg_name }} </td>
                    <td class="td__fixed_width"> Mobile Number  </td>
                    <td class="td__emp_name"> {{ $employee->mobile_no }} </td> 
                 </tr>
                 

                 <tr>
                    <td class="td__fixed_width"> Working project </td>
                    <td class="td__emp_name" colspan="2" > {{ $employee->proj_name }} </td> 
                    <td class="td__fixed_width" > Salary Type   </td> 
                    <td class="td__emp_name" > {{ $employee->hourly_employee == 1 ? "Hourly": "Basic" }} </td>     
                 </tr>  
                
              </tbody> 
            </table>
        </section>
         <br>
        <section class="table__part">
            <table>
                
                   @php
                            $startDate = new DateTime($employee->joining_date != null ? $employee->joining_date : new DateTime()); // Convert joining_date to DateTime
                            $endDate = new DateTime(); // Current date
                            $interval = $startDate->diff($endDate);
                            if($interval->y ==0 ){
                                $total_duration = ' ';
                            }else if($interval->y == 1 ){
                                $total_duration = $interval->y.' Year ';
                            }else{
                                $total_duration = $interval->y.' Years ';
                            }
                            if($interval->m == 0){
                                $total_duration =  $total_duration .' ';
                            }else if($interval->m == 1 ){
                                $total_duration =  $total_duration.$interval->m.' Month ';
                            }else{
                                $total_duration =  $total_duration.$interval->m.' Months ';
                            }

                        @endphp
                        
                <tr>
                    <td class="salary__section" colspan="3" >Present Salary Details</td>
                    <td class="salary__section" colspan="3">Proposed Salary Details</td>
                </tr>
                <tr>                     
                    <td class="td__emp_name"  > Salary Type </td>
                    <td class="td__emp_name" colspan="2"> {{ $employee->hourly_employee == 1 ? "Hourly": "Basic" }}  </td>
                    <td class="td__emp_name"  > Salary Type </td>
                    <td class="td__emp_name" colspan="2"> {{ $employee->new_salary_type == 1 ? "Hourly": "Basic" }}  </td>                     
                 </tr> 
                 
                 <tr>                     
                    <td class="td__emp_name"  > Amount </td>
                    <td class="td__emp_name" colspan="2"> {{ $employee->hourly_employee == 1 ? $employee->hourly_rent: $employee->basic_amount  }} SAR </td>
                    <td class="td__emp_name"> Amount </td>
                    <td class="td__emp_name" colspan="2"> {{$amount}} SAR  </td>                      
                 </tr> 
                 <tr>                     
                    <td class="td__emp_name"> Others </td>
                    <td class="td__emp_name" colspan="2"> {{ $employee->mobile_allowance + $employee->medical_allowance +$employee->local_travel_allowance +$employee->others +$employee->conveyance_allowance}} SAR </td>
                    <td class="td__emp_name"> Others </td>
                    <td class="td__emp_name" colspan="2"> {{ $employee->mobile_allowance + $employee->medical_allowance +$employee->local_travel_allowance +$employee->others +$employee->conveyance_allowance  }} SAR </td>                      
                 </tr>
                 
                   <tr>
                    <td class="td__emp_name"  > Working Duration </td>
                    <td class="td__emp_name" colspan="2"> <b>  {{   $total_duration  }}</b>  </td>
                    <td class="td__emp_name">   </td>
                    <td class="td__emp_name" colspan="2">   </td>
                 </tr>
                 
                 <tr>                     
                    <td class="td__emp_name">Last Increment</td>
                    <td class="td__emp_name" colspan="2"> {{ $employee->last_increment_amount >0 ? ($employee->last_increment_amount.' SAR, '.$employee->last_increment_date) : '-' }}</td>
                    <td class="td__emp_name"> Effective Date  </td>
                    <td class="td__emp_name" colspan="2" > {{$effective_date}}, Minimum Duration  {{$employee->increment_duration == 1 ? $employee->increment_duration.' Year': $employee->increment_duration.' Years' }}  </td>                         
                                     
                 </tr>
                 <tr>
                    <td class="remarks__section" colspan="4"> Remarks </td>
                    <td class="remarks__section" colspan="2">Iqama </td>
                </tr>
                 <tr>
                    <td class="remarks" rowspan="3" colspan="4">  {{$remarks}}   </td>
                    <td rowspan="4" colspan="2" style="width: 240px; height:152px"  >
                        <img src="{{ asset($employee->akama_photo) }}"  alt="Iqama File not Found" width="300px" height="190px" > 
                    </td>                       
                 </tr> 
                <tr>  </tr> 
                <tr>  </tr> 
                <tr>  </tr> 
                
                <tr>
                    <th class="recommendation__section" colspan="6">Performance Recommendation</th>
                </tr> 
                <tr>
                    <td colspan="3" class="signature__seection"  > <br><br><br> Supervisor/Foreman</td>
                    <td class="signature__seection" colspan="3"> <br> <br><br> <strong> Construction/Project Manager  </strong> </td>
                </tr> 
                <tr> 
                    <td class="signature__seection" colspan="3" > <br><br> HR Manager    </td>
                    <td class="signature__seection" colspan="3"> <br> <br>   Accounts Department   </td>
                </tr>                            
            </table>
        </section> 
            <br><br>
            <div>                                  
                <br><br>
                <div>
                    <div style="width:80%; float: left; " >
                         <strong class="box__thumb_print"> 
                                </strong>
                    </div>
                    <div style="width:20%; float: left; " >
                        
                        <div style="text-align: center; font-weight:bold; padding-right:30px; width:300px; float: right;   ">
                            <hr style="height: 2px; color:black">
                           President <br>
                           Asloob Bedaa Contracting Company 
                        </div>
                    </div>
                </div>
            </div> 
    </div>
</body>

</html>

<script>
    window.onload = function() {
        window.print();
    };
</script>
