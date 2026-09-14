<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Salary Process Status</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #000;
            margin-top: 0px;
            margin-bottom: 10px;
            margin-left: 10px;
            margin-right: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table td {
            vertical-align: middle;
            padding-bottom: 5px;
            text-align: right;
        }
        .logo {
            height: 50px;
        }
        .title-bar {
            background-color: #003366;
            color: white;
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            padding: 8px;
        }
        .info-table {
            margin-top: 10px;
            width: 30%;
            float: right;
        }
        .statement-info {
            text-align: left;
            font-size: 11px;
            padding: 5px;
        }
        .details-summary-table {
            margin-top: 15px;
            clear: both;
        }
        .section-header {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 8px;
            padding-bottom: 4px;
            border-bottom: 1px solid #003366;
        }
        .inner-table {
            width: 100%;
            border: none;
        }
        .inner-table td {
            padding: 4px 0;
            font-size: 11px;
            border: none;
        }
        .inner-table strong {
            display: inline-block;
            width: 140px;
            font-weight: bold;
        }
        .activity-table {
            margin-top: 15px;
            border: 1px solid #000;
        }
        .activity-table th {
            background-color: #d9b38c;
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
            font-size: 13px;
            font-weight: bold;
        }
        .activity-table td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
            height: 25px;
            font-size: 13px;
        }

        /* Row Colors */
        .status_done {
            background-color: #D9EAD3;
            color: #38761D;
        }
        .status_pending {
            background-color: #F9CB9C;
            color: #000;
        }
        .text-red {
            color: #A61C00;
        }
        /* Checkmark and Cross Icons for PDF */
        .icon {
            font-family: 'DejaVu Sans', sans-serif; /* Supports symbols */
            margin-right: 5px;

        }




    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td>
                <img src="{{ public_path('contents/admin/assets/images/logo_new_2.png') }}" class="logo" alt="Company Logo">
            </td>
        </tr>
    </table>


    <div style="margin-top: 20px; font-weight: bold; font-size: 13px;">Salary Processing Report for {{ $month_name }}-{{ $year }} </div>

    <table class="activity-table">
        <thead>
            <tr>
                <th>S.N</th>
                <th>Project Name</th>
                <th>Asloob Sponsor </th>
                <th>Others Sponsor</th>
        </thead>
        <tbody>

            @foreach ($records as $arecord )
            <tr>
                <td>{{ $loop->iteration }} </td>
                <td style=" text-align: left;padding-left:10px">{{ $arecord->proj_name  }}</td>
                @if($arecord->asloob_process)
                    <td class="status_done" style="background-color: #84d888; color: #000;">
                    <span class="icon">☑</span> Done
                @else
                    <td class="status_pending" style="background-color: #F4CCCC; color: #000;">
                    <span class="icon">☒</span> Pending
                </td>
                 @endif

                @if($arecord->other_process)
                      <td class="status_done" style="background-color: #84d888; color: #000;">
                    <span class="icon">☑</span> Done
                @else
                    <td class="status_pending" style="background-color: #F4CCCC; color: #000;">
                    <span class="icon">☒</span> Pending
                </td>

                @endif


            </tr>
            @endforeach
        </tbody>
    </table>



    {{-- <table class="signature-table">
        <tr>
            <td>
                <div class="signature-line"></div>
                Prepared By:
                {{ $login_name }}
            </td>
            <td>
                <div class="signature-line"></div>
                Verify By : Accounts Department <br>
            </td>
            <td>
                <div class="signature-line"></div>
                Approved By: CEO <br>
            </td>
        </tr>
    </table> --}}


</body>
</html>
