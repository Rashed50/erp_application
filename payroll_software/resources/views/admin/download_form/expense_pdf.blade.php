<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Expense Inv.</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        /* The key CSS for the header */
        header {
            position: fixed;
            top: -50px; /* Adjust as needed */
            left: 0;
            right: 0;
            height: 50px;
            text-align: center;
            /* line-height: 35px; */
            font-size: 14px;
        }

        /* The key CSS for the footer */
        footer {
            position: fixed;
            bottom: -50px; /* Adjust as needed */
            left: 0;
            right: 0;
            height: 40px;
            text-align: center;
            font-size: 11px;
            color: #555;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            padding-bottom: 10px;

        }

        /* Adjust content margins to prevent overlap */
        .content {
            padding: 20px;
            margin-top: 60px;    /* Slightly more than header height */
            margin-bottom: 60px; /* Slightly more than footer height */
        }

        h1, h2, p {
            margin: 0 0 10px 0;
        }



    .header-table {
         width: 100%;
         margin-bottom: 12px;
         border-collapse: collapse;
         padding: 18px;
         border-radius: 3px;
         margin: 0;
      }

      .header-table td {
         vertical-align: middle;
      }

      .company-title {
         font-size: 14px;
         font-weight: bold;
         margin-bottom: 4px;
         color: #222;
      }

      .subtitle {
         font-size: 12px;
         color: #666;
      }



         .info-table {
         width: 100%;
         border-collapse: collapse;
         font-size: 13px;
         margin-top: 20px;
         margin-bottom: 10px;
      }

      .info-table td {
         vertical-align: top;
         padding: 5px;
         border: none;
      }
      .info-table span {
        display: inline-block;
        margin-bottom: 5px;
        text-decoration: none;
    }


      .content-hr {
         border: 0;
         border-top: 2px dotted #bbb;
         margin: 20px 0;
      }

    .watermark {
         position: fixed;
         top: 40%;
         left: 15%;
         opacity: 0.05;
         font-size: 100px;
         transform: rotate(-30deg);
         z-index: -1;
      }

          .top-hr {
         border: 0;
         border-top: 3px solid lightblue;
         margin: 0;
      }

        /* Also ensure the signature table spreads out */
        .signature-table-container {
            width: 100%;
            table-layout: fixed; /* Forces equal column widths */
            border-collapse: collapse;
            margin-top: 50px;
            font-size:12px;
        }
          .signatures {
            display: flex;
            justify-content: space-between;
            width: 100%;
            margin-top: 50px;
        }

        .signature-block {
            width: 30%; /* Adjusted for four columns */
            text-align: center;
            white-space: nowrap; /* 🔥 This prevents text from breaking into multiple lines */
        }

        .signature-line {
            border-bottom: 1px solid #000;
            /*height: 20px;*/
            margin-bottom: 5px;
        }
         .td_amount{
            text-align: right;
            padding: 2px;
            font-weight: 600;
        }

    </style>
</head>
<body>

    <!-- Watermark -->
   <div class="watermark">ASLOOB BEDAA CO.</div>
    <header>

   <!-- Header -->
   <table class="header-table">

      <tr>
         <td style="width: 70%;">
            <!--<img src="{{ public_path('contents/admin/assets/images/logo_new.png') }}"-->
            <!--   style="width: 130px; height: auto;">-->
               <img src="{{ asset($company->com_logo) }}"  alt="Logo not found" width="200px" height="60px">
               {{-- logo ratio 4.49: 1 --}}
         </td>
         <td style="width: 30%; text-align: end;">
            <div class="company-title">ASLOOB BEDAA CO.</div>
            <div class="subtitle"> {{ $company->comp_address }}</div>
            <!--<div class="subtitle">+966 54 765 7236, {{ $company->comp_phone1 }} </div>-->
            <!--<div class="subtitle">{{ $company->comp_email1 }} </div>-->
         </td>
      </tr>

   </table>

   <hr class="top-hr">
    </header>

    <div class="content">
        <h4 style="text-align: center;">BILL INVOICE</h4><br>

        <table class="info-table">
                <tr>
                    <!-- Expence By -->
                    <td style="width: 70%;">
                        <strong>Expensed By</strong><br>
                        <span style="margin-top: 5px;"> {{ $employee->employee_name  }} </span><br>
                        <span > {{ $employee->employee_id  }},{{ $employee->catg_name  }} </span><br>
                        <span> {{ $employee->akama_no  }},Mobile No:{{ $employee->mobile_no }} </span><br>
                        <span>Project:  {{ $project_name }}</span><br>

                    </td>

                    <!-- Reference -->
                    <td style="width: 30%;">
                         @php
                            $randomNum = date('dmY').rand(0, 99)
                        @endphp

                         <span>Invoice No:  {{$randomNum}} </span><br>
                         <span>Reference No: {{ $invoice_no }}</span><br>
                        <span>Issue Date:  {{ $issue_date }} </span><br>
                        <span>Due Date:  {{ $received_date  }} </span><br>
                    </td>
                </tr>
        </table>

        <hr class="content-hr">
        <br><br>

        <table style="width: 100%; border-collapse: collapse; font-size: 12px; margin-top: 20px; margin-bottom: 10px;">
            <tr>
                <td style="width: 50%; vertical-align: top; padding: 5px; border: none;">
                    <strong>Invoice Description</strong><br>
                    <span style="margin-top: 5px;margin-right: 10px; text-align:justify">  {{ $remarks }}
                    </span><br>
                </td>

                 @php
                    $due_amount = 0.00;
                    $vat_amount = 0.00;
                 @endphp
                <td style="width: 10%;"></td>

                <td style="width: 40%; vertical-align: top; padding: 5px; border: none;">
                    <table style="width: 100%; font-size: 13px; border-collapse: collapse;">
                        <tr  style="font-weight: bold;">
                            <td   class="td_amount">Invoice Amount</td>
                            <td   class="td_amount"> {{ number_format(round(($amount),2) , 2, '.', ',') }}</td>
                        </tr>
                        <tr style="border-bottom: 1px solid #999;font-weight: bold;">
                            <td   class="td_amount">VAT</td>
                            <td   class="td_amount"> {{  number_format(round(($vat_amount),2) , 2, '.', ',')  }} </td>
                        </tr>
                        <tr  style="font-weight: bold;">
                            <td   class="td_amount">Total</td>
                            <td   class="td_amount"> {{ number_format(round(($amount),2) , 2, '.', ',')  }}</td>
                        </tr>
                        <tr style="border-bottom: 1px solid #999;font-weight: bold;">
                            <td   class="td_amount">Paid</td>
                            <td   class="td_amount"> {{ number_format(round(($amount),2) , 2, '.', ',')  }}</td>
                        </tr>
                        <tr  style="font-weight: bold;">
                            <td   class="td_amount">Due Amount</td>
                            <td  class="td_amount">{{  number_format(round(($due_amount),2) , 2, '.', ',')  }}</td>
                        </tr>
                    </table>
                </td>
            </tr>


                <tr><td colspan="3" style="font-weight: bold"><br><br><br></td></tr>
                <tr><td colspan="3"><span  style="font-weight: bold">In Words:</span>{{ $in_word }}</td></tr>


        </table>
        <br><br><br> <br><br><br>
        <table class="signature-table-container">
            <tr>
                <td style="padding: 10px;">
                    <div class="signature-block">
                        <div class="signature-line"></div>
                        <p><strong>Prepared By</strong></p>
                        <p>{{ $prepared_by }}</p>
                    </div>
                </td>

                <td style="padding: 10px;">
                    <div class="signature-block">
                        <div class="signature-line"></div>
                        <p><strong>Submitted By</strong></p>
                        <p>{{ $employee->employee_name }}</p>
                    </div>
                </td>

                 <td style="padding: 10px;">
                    <div class="signature-block">
                        <div class="signature-line"></div>
                            <p><strong>Approved By </strong></p>
                            <p></p>
                        </div>
                    </div>
                </td>

            </tr>
        </table>



    </div>



    <footer>
        <p>&copy; {{ date('Y') }} Asloob Bedaa Contracting Company All Rights Reserved.</p>
        <p>Contact us at  {{ $company->comp_address }}, {{ $company->comp_email1 }} </p>
    </footer>

</body>
</html>
