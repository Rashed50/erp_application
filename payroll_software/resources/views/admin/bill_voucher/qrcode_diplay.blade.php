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
            color: green;
            ;
            font-size: 16px;
            font-weight: normal;
            font-family: serif;
            margin-top: 20px;
        }

        .main-border {
            border: 1px solid;
        }

        .table-border {
            border: 1px solid;
            font-size: 11px;
            text-align: center;
            padding: 5px 0px;
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
            border: 2px solid #000000;
            margin: 80px 0px 20px 60px;
            padding: 0px 0px 20px 0px;
        }
    </style>
</head>

<body>
 
<table width="100%" style="padding: 10px 5px 10px 5px;">
                    <tr>
                        <td width="500" valign="top">
                            <p class="font" style="font-size: 13px;">
                             
                               </p>
                        </td>
                        <td width="100">
                            <img src="{{$qrCoded}}" alt="Red dot" />
                        </td>
                        <td width="500" align="right">
                            <p class="font" style="font-size: 13px;">
                            
                               </p>
                        </td>
                    </tr>
                </table>
  

</body>

</html>