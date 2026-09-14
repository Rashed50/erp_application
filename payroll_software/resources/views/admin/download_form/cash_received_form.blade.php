<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Adv.Receipt</title>
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
            width: 98%;
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
            padding: 1px;
            color: #000;
            text-transform: capitalize;
            font-family: "Times New Roman", Times, serif;
        }

        table td {
            text-align: center;
            font-size: 12px;
            border: 1px ridge gray;
            padding: 1px;
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
            height: 30px;
        }

        .td__fix_amount{
            font-size: 14px;
            color: black;
            text-align: left;
            padding-left: 10px;
            font-weight: bold;
            width:150px;
            color: black;
            background:#FBD2AF;
            height: 30px;
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
        .td__passport{
            font-size: 12px;
            color: black;
            text-align: left;
            font-weight: bold;
            padding-left: 10px;
        }

        .td__amount{
            font-size: 13px;
            color: black;
            text-align: left;
            font-weight: bold;
            padding-left: 10px;
            width: 100px;
        }

        .td__in_word{
            font-size: 13px;
            color: black;
            text-align: left;
            font-weight: bold;
            padding-left: 10px;
        }

        .declaration__box {
            /* width: 200px; */
            height: 180px;
            border: 0px solid #B3E6FA;
            padding: 10px;
            color: black;
            font-weight: bold;
            word-break: normal;
            text-align: justify;
            text-justify: inter-word;
            font-size: 14px;
            text-transform: initial;
        }

        .remarks__section {
            border: 0px solid #B3E6FA;
            padding: 10px;
            background: #B3E6FA;
            font-weight: bold;
            word-break: normal;
            text-align: center;
            font-size: 14px;
         }

        .remarks__box {
            border: 0px solid #B3E6FA;
            padding: 10px;
            color: #000;
            font-weight: normal;
            word-break: normal;
            text-align: justify;
            text-justify: inter-word;
            font-size: 14px;
            text-transform: initial;
            vertical-align:top
        }


        .box__signature {
            width: 100px;
            height: 30px;
            border: 1px solid gray;
            margin-top: 5px;
            margin-right: 10px;
            color: black;
        }
        .box__thumb_print{
            margin-left:10px;
             border: 1px solid #abcccc;
             background:#B3E6FA;
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
                 <img src="{{ asset($company->com_logo) }}"  alt="" width="184px" height="80px">


            </div>
        </section>
        <section style="padding:5px; text-align:center; font-weight:bold; margin-bottom:10px;"> <strong class="form_title">
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
                    <td class="td__fixed_width" > Received Date </td>
                    <td class="td__emp_name" colspan="2"> {{ date('d-m-Y', strtotime($received_date)) }} </td>
                    <td class="td__fixed_width"> Designation  </td>
                    <td class="td__emp_name" >  {{ $employee->catg_name }} </td>
                 </tr>


                 <tr>
                    <td class="td__fixed_width" > Receive Type </td>
                    <td class="td__emp_name" colspan="2">{{ $receiver_type }}  </td>
                    <td class="td__fixed_width"> Mobile Number  </td>
                    <td class="td__emp_name" >  {{ $employee->mobile_no }} </td>
                 </tr>

                 <tr>
                    <td class="td__fixed_width" > Project Name </td>
                    <td class="td__emp_name" colspan="4">{{ $project_name }}  </td>

                 </tr>

              </tbody>
            </table>
        </section>
        <br><br>

        <section class="table__part">
            <table>

                <tbody>
                 <tr>
                    <br>
                    <td class="td__fix_amount" > Received Amount </td>
                    <td class="td__amount"> {{ $amount > 0 ? (number_format($amount, 2, '.', ',')).' SAR' : '' }}   </td>
                    <td class="td__fix_amount" style="text-align: center">In Word </td>
                    <td class="td__in_word" colspan="2">{{  $amount > 0 ? $in_word.' SAR Only' : ''}}</td>
                 </tr>

                <tr>
                    <td class="declaration__box"  colspan="5">
                        {{$declaration_text}}
                        <br><br><br><br>
                        <div style="background: red;">
                            <div style="width:50%; float: left; " >
                                 <strong class="box__thumb_print">
                                       Biometric Thumb   </strong>
                            </div>
                            <div style="width:50%; float: left;" >

                                <p style="text-align: right; font-weight:bold; padding-right:30px">
                                <strong class="box__received_by">    Received By   </strong>
                                <br><br><br><br><br><br><br>
                                ------------------------------- <br>
                                {{$employee->employee_name}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            </p></div>
                        </div>

                    </td>
                </tr>
                <tr>
                    <td colspan="3" class="remarks__section">  Remarks Section </td>
                    <td colspan="2" class="remarks__section">  Iqama File  </td>
                </tr>

                <tr>
                    <td class="remarks__box"  colspan="3">
                            {{$remarks}}
                    </td>
                    <td style="width: 300px; height:190px" colspan="3">
                        <img src="{{ config('app.aws_s3_bucket_url').$employee->akama_photo }}"   alt="Iqama File not Found" width="300px" height="190px" >
                    </td>
                </tr>


              </tbody>
            </table>
        </section>
        <br><br>
        <section>
            {{-- Officer Signature --}}
            <div class="row" style="padding-top: 20px; padding-right:30px;">
                <div class="officer-signature" style="display: flex; justify-content:space-between; font-size:11px">
                    <p style="font-size: 13px; font-weight:bold"><b>{{$prepared_by}} <br> Prepare By </b></p>
                    <p style="font-size: 13px; font-weight:bold"><b>  <br> Checked By </b></p>
                    <p style="font-size: 13px; font-weight:bold"><b> <br>Accountant/Cashier </b></p>
                </div>
            </div>
            {{-- Officer Signature --}}
        </section>
    </div>
</body>

</html>

<script>
    window.onload = function() {
        window.print();
    };
</script>
