<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>  Single Emp. Salary
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
                margin: 0 auto 20px;

            }


            @page {
                size: A4 landscape;
                margin: 15mm 0mm 10mm 0mm;
                /* top, right,bottom, left */

            }
            a[href]:after {
                content: none !important;
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
        /*   End of Page Print Setting */

        .main__wrap {
            width: 90%;
            margin: 20px auto;
        }

        .header__part {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .title__part {
            text-align: center;
        }

        /* table part */
        .table__part {
            display: flex;
        }

        table {
            width: 100%;
            padding: 10px;
        }

        table,
        tr {
            border: 1px solid #333;
            border-collapse: collapse;
        }

        /* table tr {} */

        table th {
            font-size: 13px;
        }

        table td {
            text-align: left;
            font-size: 13px;

        }

        th,
        td {
            padding: 5px 2px;
            /* Top,Right,Bottom,left */
        }

        .td__left {

            text-align: left
        }

        .td__center {
            text-align: center
        }

        .td__right {
            text-align: right
        }

        .td__name {
            padding-bottom: 25px;
        }

        .td__bold {
            font-weight: 700;
        }

        .td__emplyoeeId {
            font-size: 14px;
            padding-bottom: 25px;
            color: blue;
            font-weight: 900;
            text-align: center
        }

        .td__sponser {
            color: green;
            font-weight: 300;
            text-align: center;

        }

        .sponser__name {
            font-size: 10px;
            font-weight: 100;

        }

        .country {
            color: red;
            text-align: center;
            font-size: 10px;
        }

        .employe__trade {
            color: red;
            text-align: center;
            font-size: 10px;
        }

        .td__project {
            font-size: 8px;
            padding-bottom: 5px;
            color: red;
            font-weight: 100;
            text-align: center;

        }


        .td__multi__project {
              font-size: 10px;
            padding-right: 20px;
            color: red;
            text-align: right;
        }


        .td__red__color {
            color: black;
            text-align: center;
            font-size: 12px;
        }

        .td__total {
            font-weight: bold;
            text-align: center;
            color: black;
            font-size: 14px;
        }

        .box__signature {
            width: 200px;
            height: 30px;
            border: 1px solid gray;
            margin-top: 5px;
            margin-left: 0px;
            font-size: 14px;
            color: lightgray;
        }

        .td__gross_salary {
            font-size: 14px;
            color: navy;
            font-weight: 900;
            text-align: center
        }

        body {
            overflow: hidden;
        }


        /* Preloader */

        #preloader {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #fff;
            /* change if the mask should have another color then white */
            z-index: 99;
            /* makes sure it stays on top */
        }

        #status {
            width: 200px;
            height: 200px;
            position: absolute;
            left: 50%;
            /* centers the loading animation horizontally one the screen */
            top: 50%;
            /* centers the loading animation vertically one the screen */
            background-image: url(https://raw.githubusercontent.com/niklausgerber/PreLoadMe/master/img/status.gif);
            /* path to your loading animation */
            background-repeat: no-repeat;
            background-position: center;
            margin: -100px 0 0 -100px;
            /* is width and height divided by two */

            border: 16px solid #f3f3f3;
            border-radius: 50%;
            border-top: 16px solid blue;
            border-right: 16px solid green;
            border-bottom: 16px solid red;
            border-left: 16px solid pink;
            width: 120px;
            height: 120px;
            -webkit-animation: spin 2s linear infinite;
            animation: spin 2s linear infinite;
        }
    </style>
    <!-- style -->
</head>

<body>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <!-- Preloader -->
    <div id="preloader">
        <div id="status">&nbsp;</div>
    </div>
    <div class="main__wrap">
        <!-- header part-->
        <section class="header__part">
            <!-- date -->
            <div class="date__part">
                <p> Salary Month : <strong class="td__red__color"> <br> {{ $monthName }}, {{$salaryYear}} </strong> </p>
            </div>
            <!-- title -->
            <div class="title__part">
                <h4>{{$company->comp_name_en}} <small>{{$company->comp_name_arb}} </small> </h4>
                <address class="address">
                    {{$company->comp_address}}
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
                <thead>
                     <tr>
                        <th>SL<br>No </th>
                        <th> Emp. <br> ID </th>
                        <th> Employee Name </th>
                        <th> Iqama No<br><br> BS/Rate </th>
                        <th>Sponsor <br><br>Basic Hrs. </th>
                        <th>Nat <br><br> OT/Amt</th>
                        <th>Working <br>Days  <hr> P &nbsp; &nbsp; L</th>
                        <th> Trade <br> <br> (Food+Other) </th>
                        <th> <br>Total <br> Salary</th>
                        <th style="text-align: right;"> Deduction  <br> <hr> (S.T+Adv.+C.D) </th>
                        <th> <br><hr> ID Renew</th>
                        <th> <br> Net <br> Salary </th>
                        <th> Project <br> Signature </th>
                    </tr>
                </thead>
                <tbody>


                    @php
                    $total_hour = "";
                    $total_day = "";
                    $multi_project = "";
                    $total_others_amount = $salary->house_rent + $salary->mobile_allowance  + $salary->medical_allowance + $salary->local_travel_allowance + $salary->conveyance_allowance + $salary->others + $salary->slh_bonus_amount;

                    $findMultipleProjectEmployee = App\Models\EmployeeMultiProjectWorkHistory::where('emp_id',$salary->emp_auto_id)->where('month',$salary->slh_month)->where('year',$salary->slh_year)->get();
                    @endphp

                    <tr style="border-bottom:0;">
                        <td class="td__center td__name"> 1 <br></td>
                        <td class="td__center td__emplyoeeId"> {{ $salary->employee_id }} </td>
                        <td class="td__left td__name"> <span>{{ $salary->employee_name }}</span> </td>

                        <td class="td__center"> {{ $salary->akama_no }} <br><br> {{ $salary->basic_amount }}/{{ $salary->hourly_rent }} </td>

                        <td class="td__sponser"><span class="sponser__name"> {{Str::limit( $salary->spons_name,15)}}</span> <br> {{ $salary->slh_total_hours }} </td>
                        <td class="td__center"> <span class="country"> {{ Str::limit($salary->country_name,3) }}</span> <br><br>{{ $salary->slh_total_overtime }}/{{ $salary->slh_overtime_amount }}</td>
                        <td  class="td__sponser">{{ $salary->slh_total_working_days - $salary->paid_leave  }} &nbsp;&nbsp;{{ $salary->paid_leave }} <br><br> {{ $salary->slh_total_working_days  }} </td>

                        <td class="td__center"> <span class="employe__trade"> {{ Str::limit($salary->catg_name,10) }}</span> <br><br>
                            {{round($salary->food_allowance) }} + {{ round($total_others_amount) }}
                         </td>

                        <td class="td__gross_salary"><br> {{round( $salary->slh_all_include_amount)}} </td>
                         <td class="td__center" style="text-align: right;"> <br>{{ round($salary->slh_saudi_tax) }} +
                            {{round($salary->slh_other_advance) }} + {{ $salary->slh_food_deduction }}
                        </td>
                        <th><br> {{round( $salary->slh_iqama_advance) }}</th>
                        <td class="td__gross_salary"> <br>{{ round($salary->slh_total_salary )}}</td>
                        <td class="td__project"> {{ Str::limit($salary->project->proj_name,40) }}<br>
                            <div class="box__signature">
                                 @if($salary->salary_status >=2)
                                   <b> Salary Hold, </b>
                                  @endif
                                {{  $salary->Status == 0 ? 'Unpaid':'Paid'}}
                         </div>
                        </td>
                    </tr>


                    @if($findMultipleProjectEmployee->count()>1)
                    @foreach($findMultipleProjectEmployee as $multiData)
                    @php
                    $multi_pro_total_hour = $total_hour.($multiData->total_hour."/".$multiData->total_day)."<br>";
                    $multi_project = $multi_project.$multiData->projectName->proj_name."<br>";
                    @endphp

                    @endforeach
                    
                     <tr style="border:0; padding:0">
                        <td colspan="4"></td>
                        <td class="td__center">{!! $multi_pro_total_hour !!}</td>
                        <td class="td__center"> </td>
                        <td> </td>
                        <td> </td>
                        <td> </td>
                        <td> </td>
                        <td colspan="3" class="td__multi__project">{!! $multi_project !!}</td>
                    </tr>

                    @endif
                </tbody>
            </table>
        </section>




        <section>
           {{-- Officer Signature --}}
            <div class="row" style="padding-top: 80px;">
                <div class="officer-signature" style="display: flex; justify-content:space-between; text-align: center; ">
                    <p>  <b>  ------------------------ <br> {{$login_name}} </b> <br> Prepared By  </p>
                    <p>  <b>  ------------------------ <br>   </b> Accounts/HR  </p>
                    <p>  <b>  ------------------------ <br>   </b> Approved By  </p>
                </div>
            </div>
            <p style="text-align: center; color:grey">Online System Generated Statement Authorized by Accounts Department</p>
        </section>

    </div>
</body>


<script>
    $(window).on('load', function() { // makes sure the whole site is loaded
        $('#status').fadeOut(); // will first fade out the loading animation
        $('#preloader').delay(350).fadeOut('slow'); // will fade out the white DIV that covers the website.
        $('body').delay(350).css({
            'overflow': 'visible'
        });
    })
</script>

</html>
