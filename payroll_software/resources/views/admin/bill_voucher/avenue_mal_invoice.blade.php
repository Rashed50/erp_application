<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Invoice</title>

    <style type="text/css">
        * {
            font-family: "Times New Roman", Arial, sans-serif;
            margin: 0;
        }

        table {
            border-spacing: 0;
        }

        td {
            padding: 0;
        }

        tfoot tr td {
            font-weight: bold;
            font-size: x-small;
        }

        .gray {
            background-color: lightgray
        }

        .font {
            font-size: 11px;
        }

        .authority {
            /*text-align: center;*/
            float: right
        }

        .authority h5 {
            margin-top: -10px;
            color: green;
            /*text-align: center;*/
            margin-left: 35px;
        }

        .thanks p {
            color: green;
          
            font-size: 12px;
            font-weight: normal;
            font-family: serif;
            margin-top: 20px;
        }

        .main-border {
            border: 1px solid gray;
        }

        .table-border {
            border: 1px solid gray;
            font-size: 11px;
            text-align: center;
            padding: 2px 0px;
        }

        #invoice_table {
            font-family: Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;

        }
        #invoice_table td,
        #invoice_table th {
            border: 1px solid gray;
            padding: 8px;
        }


        .single-padding {
            padding: 10px;
        }

        .single-paddingTwo {
            padding: 15px 0px;
        }

        .single-padding-three {
            padding: 5px;
        }

        table.main-body-border {
            border: 1px solid #E5E7E9;
            margin: 0px 0px 0px 10px;
        }

        .print-btn {
            text-align: right;
            margin: 0px 50px 0px 0px;
        }

        .print__button {
            height: 35px;
            width: 80px;
            background-color: #bdb3b3;
            text-transform: uppercase;
            border-radius: 10px;
        }
        .header-content {
            margin: 24px 130px 0px 150px;
        }
        .grid-container0 {
            display: flex;
            justify-content: center;
        }
        #printable-area {
                position: absolute;
                margin: 10px;
                border: 1px solid gray;
        }

        .td__sn{
            text-align: center;
        }
        .td__description{
            text-align: left;
            padding-left:5px;
        }
        .td__qty{
            text-align: center;
        }
        .td__unit{
            text-align: center;
        }
        .td__unit_rate{
            text-align: center;
        }
        .td__total{
            text-align: right;
            padding-right:5px;
        }

        @media print {
            body * {
                visibility: visible;
            }
            #printable-area,
            #printable-area * {
                visibility: visible;
            }
            #printable-area {
                position: absolute;
                margin: 10px;
            }

            .container {
                max-width: 95%;
                margin: 10 auto;
            }

            @page {
                size: A4 portrait;
                margin: 35mm 0mm 5mm 0mm;
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
    </style>
    
</head>
<body>
  
    <div class="print-btn">    
             <button type="" onclick="window.print()" class="print__button">Print</button>
    </div>
    <div class="grid-container" id="printable-area">
       
        <table width="100%" style="padding: 10px 5px 10px 5px;">
            <caption><u>TAX INVOICE الفاتورة الضريبية  </u> </caption>
            <tr>
                <td width="500" valign="top">
                    <p class="font" style="font-size:10px;">
                          <b>Project:</b> <span style="padding-left:10px; padding-bottom:25px;">
                            {{$invoiceRecord->proj_name}}</span> <br>
                        <b>Date:</b><span>{{$invoiceRecord->submitted_date}}</span><br><br>
                        <b>Invoice No:</b><span>{{$invoiceRecord->invoice_no}}</span> <br><br>
                        <b>Contract No.:</b><span>{{$invoiceRecord->contract_no == null ? "":$invoiceRecord->contract_no}}</span> <br><br>
                        <b>Main Contractor:</b><span>{{$invoiceRecord->mainContractor->en_name}}</span><br>
                        <b>VAT No:</b><span>{{ $invoiceRecord->mainContractor->vat_no }}</span><br><br>                   
                        <b>Sub. Contractor:</b><span style="padding-left:5px;">
                            {{$invoiceRecord->subContractor->sb_comp_name}}</span>  <br>
                         <b>VAT No:</b><span>{{$invoiceRecord->subContractor->sb_vat_no}}</span><br>
                         <b>Address:</b><span>{{$invoiceRecord->subContractor->address}}</span><br><br>
                        <b>Attn.</b> <span style="padding-left: 5px;">: Accounts
                            Department {{$invoiceRecord->mainContractor->en_name}}</span> <br><br>
                    </p>
                </td>
                <td width="100">
                    <img src="{{$qrCoded}}" alt="Red dot" style="width:130px; height: 130px" />
                </td>
                <td width="500" valign="top" align="right">
                    <p class="font" style="font-size: 10px;">
                        <b>مشروع :</b> <span
                            style="padding-right: 10px;padding-bottom:5px;">مبنى سابك الجبيل الرئيسي
                            </span> <br>
                        <b>تاريخ :</b><span
                            style="padding-right:10px; padding-bottom:5px;">{{$invoiceRecord->submitted_date}} </span><br><br>
                        <b>رقم الفاتورة</b><span
                            style="padding-right:10px;padding-bottom:5px;">{{$invoiceRecord->invoice_no}}</span> <br><br>
                        <b>رقم العقد </b><span>{{$invoiceRecord->contract_no == null ? "":$invoiceRecord->contract_no}}</span> <br><br>
                        <b>المقاول الرئيسي</b><span style="padding-right: 10px;padding-bottom:5px;">شابورجي
                            بالونجي الشرق الأوسط {{$invoiceRecord->mainContractor->ar_name}}</span> <br>
                        <b> رقم ضريبة القيمة المضافة </b> <span>{{$invoiceRecord->mainContractor->vat_no}}</span><br><br>
                        <b>الفرعية. مقاول</b><span style="padding-right: 10px;padding-bottom:5px;">شركة أسلوب
                            الدولية للمقاولين {{$invoiceRecord->subContractor->sb_comp_name_arb}}</span> <br>
                        <b> رقم ضريبة القيمة المضافة </b> <span>{{$invoiceRecord->subContractor->sb_vat_no}}</span><br> 
                        <b> عنوان</b> <span>{{$invoiceRecord->subContractor->address}}</span><br><br>
                        <b>انتباه:</b> <span style="padding-left: 10px;padding-bottom:5px;"> قسم الحسابات  {{$invoiceRecord->mainContractor->ar_name}}</span> <br>
                    </p>
                </td>
            </tr>
        </table>

        <table id="invoice_table" width="100%" style="padding: 0px 0px 0px 0px; font-size:10px;">
            <tr>
                <th  >Sl NO.</th>
                <th  >ITEM NO. <br> (رقم الصنف)</th>
                <th  >DESCRIPTION <br> (وصف)</th>
                {{-- <th class="table-border">REFERENCE NO. <br> (رقم المرجع)</th> --}}
                <th  >UNIT <br> (وحدة)</th>
                <th >QUANTITY <br> (كمية)</th>
                <th >UNIT RATE <br> (معدل الوحدة)</th>
                <th >TOTAL <br> (المجموع)</th>
            </tr>
            @php
            $totalRetention = ($invoiceRecord->total_amount * $invoiceRecord->percent_of_retention) /
            100;
            $totalVAT = ($invoiceRecord->total_amount * $invoiceRecord->percent_of_vat) / 100;
            $counter = 1;
            @endphp

            @foreach($invoiceRecordDetails as $invRecord)
            <tr>
                <td class="td__sn">{{$counter++}}</td>
                <td class="td__sn" >{{$invRecord->item_no }}</td>
                <td class="td__description" >{{ $invRecord->items_details }}</td>
                <td class="td__unit" >{{ $invRecord->item_unit }}</td>
                <td class="td__qty" >{{number_format($invRecord->quantity,2) }}</td>
                <td class="td__unit_rate" >{{number_format($invRecord->rate,2) }}</td>
                <td class="td__total">
                    {{ number_format($invRecord->total,2)}}
                </td>
            </tr>
            @endforeach
             
            <tr>
                <td colspan="6">
                    <strong style="float: left; margin-left: 5px;">Total (Excluding VAT)</strong>
                    <strong style="float: right;padding-right: 10px;">الإجمالي (باستثناء ضريبة القيمة
                        المضافة)</strong>
                </td>
                <td class="table-border" colspan="2" style="text-align:right; padding-right:5px;">
                    <strong>{{number_format($invoiceRecord->items_grand_total_amount,2)}}</strong>
                </td>
            </tr>
            <tr>
                <td colspan="6" >
                    <strong style="float: left; margin-left: 5px;">Total VAT
                        ({{$invoiceRecord->percent_of_vat }}%)</strong>
                    <strong style="float: right;padding-right: 10px;">إجمالي ضريبة القيمة
                        المضافة</strong>
                </td>
                <td  colspan="2" style="text-align:right; padding-right:5px;">
                    <strong>{{ number_format( $invoiceRecord->total_vat,2) }}</strong>
                </td>
            </tr>
            <tr>
                <td colspan="6" >
                    <strong style="float: left; margin-left: 5px;">Total (Including VAT)</strong>
                    <strong style="float: right;padding-right: 10px;">المبلغ الإجمالي شاملاً ضريبة
                        القيمة المضافة</strong>
                </td>
                <td class="table-border" colspan="2" style="text-align:right; padding-right:5px;">
                    <strong>{{ number_format( $invoiceRecord->items_grand_total_amount + $invoiceRecord->total_vat,2) }}</strong>
                </td>
            </tr>
            <tr>
                <td colspan="6" >
                    <strong style="float: left; margin-left: 5px;">Retention
                        ({{number_format($invoiceRecord->percent_of_retention,1) }}%)</strong>
                    <strong
                        style="float: right;padding-right: 10px;">({{number_format($invoiceRecord->percent_of_retention,1)}}%)مبلغ
                        الاحتفاظ</strong>
                </td>
                <td  colspan="2" style="text-align:right; padding-right:5px;">
                    <strong>{{$invoiceRecord->total_retention}}</strong>
                </td>
            </tr>
            <tr>
                <td colspan="6" >
                    <strong style="float: left; margin-left: 5px;">Total Amount Including VAT and Exclusive
                        {{number_format($invoiceRecord->percent_of_retention,1)}}% Retention</strong>
                    <strong style="float: right;  padding-right:5px;">المبلغ الإجمالي شاملاً ضريبة
                        القيمة المضافة و حصريًا للاحتفاظ </strong>
                </td>
                <td  colspan="2" style="text-align:right; padding-right:5px;">
                    <strong>{{ number_format( ($invoiceRecord->total_amount -  $invoiceRecord->total_retention),2) }}</strong>
                </td>
            </tr>
            <tr>
                <td colspan="8" class="table-border single-paddingTwo">
                     
                        <p style="float: left;"><strong style="border-bottom: 1px solid;">In
                            Words:</strong>                         
                        {{$invoiceRecord->receiveable_amount_inword }}   {{$invoiceRecord->receiveable_amount >0 ? " SAR":"" }} 
                    </p>  
                </td>
            </tr>
            
            <tr>
                <td colspan="3" style="text-align: left">Value Added Tax Number</td>
                <td colspan="2" style="text-align: center">{{$invoiceRecord->mainContractor->vat_no }} </td>
                <td colspan="3" style="text-align: right">   رقم ضريبة القيمة المضافة </td>
            </tr>

            <tr>
                <td colspan="3" style="text-align: left">Value Added Tax Number</td>
                <td colspan="2" style="text-align: center">{{$invoiceRecord->subContractor->sb_vat_no }} </td>
                <td colspan="3" style="text-align: right">   رقم ضريبة القيمة المضافة </td>
            </tr>
        </table>

        <table width="100%" style="text-align: center; font-size:11px;">
            <td width="30%" valign="top" align="left">
                <p style="padding-top:30px;font-size: 11px; margin-left: 50px;">With Regards</p>
            </td>
            <td width="70%" align="right">
                <table>
                    <tr >
                        <td colspan="5" class="single-padding-three" text-align="left">
                            <strong  style="float: left;"><u>Please Pay To</u></strong ></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="single-padding-three" text-align="left"><strong
                                style="float: left;">Bank Name</strong></td>
                        <td colspan="5" class="single-padding-three" text-align="left"> <span
                                style="float: left;">{{$invoiceRecord->bankInfo->bank_name }}</span></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="single-padding-three" text-align="left"><strong
                                style="float: left;">Account No</strong></td>
                        <td colspan="5" class="single-padding-three" text-align="left"><span
                                style="float: left;">{{$invoiceRecord->bankInfo->account_no }}</span></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="single-padding-three" text-align="left"><strong
                                style="float: left;">Beneficiary Name</strong></td>
                        <td colspan="5" class="single-padding-three" text-align="left"><span
                                style="float: left;">{{$invoiceRecord->bankInfo->beneficiary_name }}</span></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="single-padding-three" text-align="left"><strong
                                style="float: left;">IBAN NUMBER</strong></td>
                        <td colspan="5" class="single-padding-three" text-align="left"><span
                                style="float: left;">{{$invoiceRecord->bankInfo->ibank_no }}</span></td>
                    </tr>

                </table>
            </td>
        </table>

        <table width="100%" style="padding: 20px 0px 15px 0px;font-size: 14px;">
            <tr>
                <td style="width: 200px;display: block;">
                    <p style="border-top: 1px solid #D5D8DC; margin-left: 5px;"></p>
                    <p style="margin-left: 30px;">{{$invoiceRecord->SubmitedBy->name }}</p>
                </td>
            </tr>
        </table>

        <table width="100%" style="">
            <tr>
                <td width="500" valign="top">
                    <p class="font">
                        <strong style="padding-bottom:5px;display:inline-block;margin-left: 50px;">Engineer</strong><br>
                    </p><p style="font-size: 12px;padding-bottom: 4px;margin-left: 10px;">
                        {{$invoiceRecord->subContractor->sb_comp_name}}
                    </p>
                    <p style="margin-left: 40px;font-size: 12px;">{{$invoiceRecord->SubmitedBy->phone_number }}</p>
                        <p style="border-bottom: 1px solid #000000;display: inline-block;padding-top: 5px;font-size: 13px;"> </p>
                    <p></p>
                </td>
            </tr>
        </table>                
    </div>
   
</body>

</html>
