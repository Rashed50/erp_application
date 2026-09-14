<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 10px;
            margin-right: 20px;
            padding: 0;
            /* background-color: #f4f4f4; */
        }

        .form-container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid gray;
            /* background-color: #fff; */
             padding: 5px;
           /* padding-bottom: 10px;
            padding-top: 10px; */


        }

        h1,
         {
            text-align: center;
            margin-bottom: 15px;
        }

        h3 {
            text-align: center;
            margin-bottom: 5px;
        }

        .form-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .form-table td {
            padding:5px;
            border: 1px solid #000;
        }
        .form-table .label {
            font-weight: bold;
            background-color: #f0f0f0;
        }

        .checkbox-group {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .checkbox-group label {
            display: flex;
            align-items: center;
        }

        .checkbox-group input[type="checkbox"] {
            margin-right: 10px;
        }

        .empoyee_signature_section {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
            margin-bottom: 0px;
        }

        .signature-group {
            display: flex;
            justify-content: space-between;
            margin: 20px 10px;
        }

        .signature-group div {
            text-align: center;
        }

        .signature {
            text-align: right;
            font-weight: bold;
            margin-top: 10px;
        }

        textarea {
            width: 100%;
            height: 100px;
            /* margin-top: 10px; */
            border-radius: 4px;
        }

        .comments-section {
            margin-top: 20px;
            padding-right:10px;
        }

        .section_divider {
            padding: 2px 0px 2px 0px;
            /* top, right,bottom, left */
        }

        .section_divider hr {
            font-weight: bold;
        }


    @media print {
        /* .page {
            margin: 0;
            border: initial;
            border-radius: initial;
            width: initial;
            min-height: initial;
            box-shadow: initial;
            background: initial;
            page-break-after: always;
        } */

        @page {
                size: A4 portrait;
                margin: 5mm 5mm 5mm 5mm;
                /* top, right,bottom, left */
                /* border: 1px solid gray; */

            }

            .print__button {
                visibility: hidden;
            }
    }
    </style>
</head>

<body>
    {{-- <button type="" onclick="window.print()" class="print__button">Print</button> --}}

    <div class="form-container">
        <div style="height: 80px; width:72%; float: left;background-color: white; padding-bottom:20px; text-align:right">
            <h1 >Leave Application Form</h1>
            {{-- <label for="printdate" style="color: gray; font-size:10px;"> Print Date: {{ Carbon\Carbon::now()->format('d/m/Y') }}</label> --}}
        </div>
        <div style="height: 80px; width:23%; float: right; background-color:white;padding-bottom:20px;padding-right:5px;  ">
            <!--<img src="{{ asset('contents/admin') }}/assets/images/logo_icon.png" alt="" class="image-resize"  width="160" height="80">-->
            
            <img src="{{ asset($company->com_logo) }}"  alt="" width="184px" height="80px">
        </div>

       

        <table class="form-table">
            <tr>
                <td class="label">Emp. ID</td>
                <td> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </td>
                <td class="label">Working Project</td>
                <td>  </td>
            </tr>
            <tr>
                <td class="label">Name</td>
                <td>  </td>
                <td class="label">Service Duration</td>
                <td>   </td>
            </tr>
            <tr>
                <td class="label">Desig/Trade</td>
                <td>   </td>
                <td class="label">Mobile No</td>
                <td>  </td>
            </tr>
            <tr>
                <td class="label">Sponsor</td>
                <td>   </td>
                <td class="label">Country/Contact</td>
                <td>   </td>
            </tr>
            <tr>
                <td class="label">Iqama No</td>
                <td>  </td>
                <td class="label">Salary Type</td>
                <td> Hourly/Basic   </td>
            </tr>
            <tr>
                <td class="label">Joining Date</td>
                <td>   </td>
                <td class="label">Contract</td>
                <td>    </td>
            </tr>
        </table>

        <h3>Leave Type </h3>
        <div class="checkbox-group">
            <label><input type="checkbox"   > Emergency/Sick</label>
            <label><input type="checkbox"  > Exit-Entry</label>
            <label><input type="checkbox"  > Final Exit</label>
            <label><input type="checkbox"   > Others Leave</label>
        </div>
        <div class="checkbox-group">
            <label><input type="checkbox"  >Annual Vacation</label>
            <label>Vacation Duration: &nbsp;<input type="text"></label>

        </div>

        <div class="empoyee_signature_section">
            <p> --------------------- <br> &nbsp; &nbsp; &nbsp; &nbsp; Date</p>
            <p> ------------------------- <br>Employee Signature</p>
        </div>

        <div class="section_divider"><hr><hr></div>

        <h3>Authorization Signature | Only Official Use</h3> <br>
        <div class="signature-group">
            <div>
                <p>-----------------------</p>
                <p>Supervisor</p>
            </div>
            <div>
                <p>------------------------</p>
                <p>Project Manager</p>
            </div>
            <div>
                <p>-------------------------</p>
                <p>HR Manager</p>
            </div>
        </div>

        <div class="comments-section">
            <h3>Comments Section(Vacation benefits: e.g air ticket, bonus etc)</h3>
            <textarea name="" id="" rows="10" style=""></textarea>
        </div>
        <br><br>

        <div class="signature-group">
            <div>
                <p>-------------------------</p>
                <p>COO</p>
            </div>
            <div>
                <p>-------------------------</p>
                <p>CEO</p>
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
