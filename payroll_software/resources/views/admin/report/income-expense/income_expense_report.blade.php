<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Invoice</title>

        <style type="text/css">
            * {
                font-family: Verdana, Arial, sans-serif;
                margin: 0;
            }
            table{
                border-spacing: 0;
            }
            td{
                padding: 0;
            }

            tfoot tr td{
                font-weight: bold;
                font-size: x-small;
            }
            .gray {
                background-color: lightgray
            }
            .font{
            font-size: 15px;
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
                color: green;;
                font-size: 16px;
                font-weight: normal;
                font-family: serif;
                margin-top: 20px;
            }

            .main-border{
                border: 1px solid;
            }
            .table-border{
                border: 1px solid;
                font-size: 11px;
                text-align: center;
                padding: 5px 0px;
            }
            .single-padding{
                padding: 10px;
            }
            .single-paddingTwo{
                padding: 15px 0px;
            }
            .single-padding-three{
                padding: 5px;
            }
            table.main-body-border {
                border: 2px solid #000000;
                margin: 80px 0px 20px 80px;
                padding: 0px 0px 20px 0px;
            }
        </style>
    </head>
    <body>
        <table class="main-body-border">
            <tr>
                <td>
                    <table width="100%" style="padding: 10px 5px 10px 5px;">
                        <tr>
                            <td width="1100" valign="top">
                                <p class="font" style="font-size: 13px;">
                                    <span>Month</span><span style="padding-left:5px;font-size: 12px;">: </span><br><br>
                                    <span>Project</span><span style="padding-left:5px;font-size: 12px;">: -----------------</span><br><br>

                                    <strong>Income</strong><br><br>
                                    <span style="padding-left: 27px;">Total Invoice Amount</span>
                                </p>
                            </td>
                        </tr>
                    </table>
                    <!-- <table>
                        <tr>
                            <th style="width:165px;padding: 10px;">SL NO</th>
                            <th style="width:165px;padding: 10px;">AAAA</th>
                            <th style="width:165px;padding: 10px;">AAAA</th>
                            <th style="width:165px;padding: 10px;">AAAA</th>
                            <th style="width:165px;padding: 10px;">AAAA</th>
                            <th style="width:165px;padding: 10px;">Amount</th>
                        </tr>
                        <tr>
                            <td style="width:165px;padding: 10px;text-align: center;">1</td>
                            <td style="width:165px;padding: 10px;text-align: center;">A</td>
                            <td style="width:165px;padding: 10px;text-align: center;">B</td>
                            <td style="width:165px;padding: 10px;text-align: center;">C</td>
                            <td style="width:165px;padding: 10px;text-align: center;">D</td>
                            <td style="width:165px;padding: 10px;text-align: center;">500</td>
                        </tr>
                        <tr>
                            <td style="width:165px;padding: 10px;text-align: center;">2</td>
                            <td style="width:165px;padding: 10px;text-align: center;">A</td>
                            <td style="width:165px;padding: 10px;text-align: center;">B</td>
                            <td style="width:165px;padding: 10px;text-align: center;">C</td>
                            <td style="width:165px;padding: 10px;text-align: center;">D</td>
                            <td style="width:165px;padding: 10px;text-align: center;">500</td>
                        </tr>
                        <tr>
                            <td style="width:165px;padding: 10px;text-align: center;">3</td>
                            <td style="width:165px;padding: 10px;text-align: center;">A</td>
                            <td style="width:165px;padding: 10px;text-align: center;">B</td>
                            <td style="width:165px;padding: 10px;text-align: center;">C</td>
                            <td style="width:165px;padding: 10px;text-align: center;">D</td>
                            <td style="width:165px;padding: 10px;text-align: center;">500</td>
                        </tr>
                        <tr>
                            <td style="width:165px;padding: 10px;text-align: center;">4</td>
                            <td style="width:165px;padding: 10px;text-align: center;">A</td>
                            <td style="width:165px;padding: 10px;text-align: center;">B</td>
                            <td style="width:165px;padding: 10px;text-align: center;">C</td>
                            <td style="width:165px;padding: 10px;text-align: center;">D</td>
                            <td style="width:165px;padding: 10px;text-align: center;">500</td>
                        </tr>
                        <tr>
                            <td style="width:165px;padding: 10px;text-align: center;">5</td>
                            <td style="width:165px;padding: 10px;text-align: center;">A</td>
                            <td style="width:165px;padding: 10px;text-align: center;">B</td>
                            <td style="width:165px;padding: 10px;text-align: center;">C</td>
                            <td style="width:165px;padding: 10px;text-align: center;">D</td>
                            <td style="width:165px;padding: 10px;text-align: center;">500</td>
                        </tr>
                    </table> -->
                    <table width="100%" style="padding: 0px 0px 0px 0px;">
                        <tr>
                            <th class="table-border">Sl No.</th>
                            <th class="table-border">AAAA</th>
                            <th class="table-border">BBBB</th>
                            <th class="table-border">CCCC</th>
                            <th class="table-border">DDDD</th>
                            <th class="table-border">EEEE</th>
                        </tr>
                        <tr>
                            <td class="table-border">1</td>
                            <td class="table-border">AAAA</td>
                            <td class="table-border">BBBB</td>
                            <td class="table-border">CCCC</td>
                            <td class="table-border">DDDD</td>
                            <td class="table-border">EEEE</td>
                        </tr>
                        <tr>
                            <td colspan="6" class="table-border">
                                <strong style="float: left;">Employee Salary Total Amount: 2000</strong>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="6" class="table-border">
                                <strong style="float: left;">Transport Cost: 3%</strong>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="6" class="table-border">
                                <strong style="float: left;">Sponsor Expense : 5%</strong>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="6">
                                <strong style="float: left;font-size: 11px;">Vat Expense : 5%</strong>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
</html>