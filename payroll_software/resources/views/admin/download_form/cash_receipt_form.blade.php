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
            /* border: 1px ridge gray; */
            border-collapse: collapse;
            margin: 0px;
            padding: 0px;
            background-color: #B3E6FA
        }

        table th {
            font-size: 12px;
            /* border: 1px ridge gray; */
            font-weight: bold;
            padding: 5px;
            color: #000;
            text-transform: capitalize;
            font-family: "Times New Roman", Times, serif;
        }

        table td {
            text-align: center;
            font-size: 13px;
             /* border: 1px ridge gray;             */
            padding: 15px;
            margin: 0px;
            text-transform: capitalize;
            font-family: "Times New Roman", Times, serif;
           
            /* Top,Right,Bottom,left */
        } 

        .form_title{
            background-color:#10385A;  padding:10px; "
            font-family: "Algerian Mesa Plain";
            color:white;
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
            color: black;
            background:#B3E6FA; 
            width: 20px;

                
        }

        .td__date {
            font-size: 13px;
            color: black;
            text-align: right;
            font-weight: bold;
            padding-left: 10px;  
              
                   
        } 

        .td__emp_name{
            font-size: 13px;
            color: black;
            text-align: left;
            font-weight: bold;
            padding-left: 10px; 
        }


        .td__receipt_no {
            font-size: 13px;
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
        
        }

        .td__in_word{
            font-size: 13px;
            color: black;
            text-align: left;
            font-weight: bold;
            padding-left: 10px; 
        } 
        .remarks__section {     
            border: 0px solid #B3E6FA;             
            padding: 10px;
            background: #FBD2AF; 
            font-weight: bold;
            word-break: normal;
            text-align: center; 
            font-size: 14px;
         }  
 

        .box__amount {
            width: 70px;
            height: 10px;
            border: 3px solid gray;
            color: black;
            
        }

      
    

        .box__received_by{
            border: 2px solid #000;  
             padding-top:10px;
             padding-left: 0px;
             padding-bottom: 10px;
             text-align:center;  

             margin: 0px;
        }

        .box__thumb_print{
            margin-left:10px;
             border: 1px solid #abcccc;
             background: #fff; 
             padding:10px;
             color: #B3E6FA;
             text-align:center; margin-right:20px;
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
        <section style="padding:10px; text-align:center; font-size:20px; font-weight:bold; margin-bottom:10px;"> <strong class="form_title">
            {{$form_title}}
            </strong>  </section>
        
        <section class="table__part">
            

            <table>
                 
                <tbody>     
                 <tr>
                    <td class="td__receipt_no"  >Receipt Number <u>  &nbsp;&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;&nbsp;</u> </td>                  
                    <td class="td__date"   > Date: {{ $received_date }} </td>                  
                  </tr>

                 <tr>
                    <td class="td__emp_name" colspan="2"  > Received with thanks from -------------------------------------------------------------------------------------------------------- </td>                       
                 </tr> 

                 <tr>                    
                    <td class="td__emp_name" class="2"> Amount <input type="text" style="width: 100px; height:20px; border:#000" class="box__thumb_print">
                        Amount in Words----------------------------------------------------
                    </td>                                      
                  </tr> 
                  <tr>
                    <td class="td__emp_name" colspan="2" >By  {{ $payment_method == 'CASH' ? $payment_method: ('BANK '.'Cheque No') }} -------------------------------------------- Name of Bank:------------------------------------  </td>                  
                  </tr> 

                 <tr>                     
                    <td class="td__emp_name" colspan="2" >For the Purpose of ---------------------------------------------------------------
                        ------------------------------------------- </td>                  
                 </tr>  
                              
              </tbody> 
            </table>
        </section>
       
        <br><br>
        <section>
            {{-- Officer Signature --}}
            <div class="row" style="padding-top: 20px; padding-right:30px;">
                <div class="officer-signature" style="display: flex; justify-content:space-between; font-size:11px">
                    <p style="font-size: 13px; font-weight:bold"><b> ------------------------- <br> Accounts Approval </b></p>
                    <p style="font-size: 13px; font-weight:bold"><b>  <br> </b></p>
                    <p style="font-size: 13px; font-weight:bold"><b> ---------------------<br> &nbsp;&nbsp;&nbsp;&nbsp;Received By </b></p>
                </div>
            </div>
            {{-- Officer Signature --}}
        </section>
    </div>
</body>

</html>



<div style="background: red;">
    <div style="width:40%; float: left; padding:0px; margin:0px" >
         {{-- <strong class="box__received_by">    {{ $amount > 0 ? (number_format($amount, 2, '.', ',')).' SAR' : '' }} --}}
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;   </strong>
    </div>
    <div style="width:50%; float: left;" >                                
      </div>
</div>

<script>
    window.onload = function() {
        window.print();
    };
</script>