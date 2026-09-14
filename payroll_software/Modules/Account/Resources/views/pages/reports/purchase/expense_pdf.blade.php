<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="utf-8">
   <title>Expense Report</title>
   <style type="text/css">
      body {
         font-family: Arial, sans-serif;
         margin: 0;
         font-size: 14px;
         color: #333;
      }

      @page {
         size: A4;
         margin-top: 5mm;
         margin-bottom: 14mm;
         margin-left: 14mm;
         margin-right: 14mm;

         @bottom-left {
            content: "Generated on: {{ $current_datetime }}";
            font-size: 8pt;
            color: #888;
         }

         @bottom-right {
            content: "Page " counter(page) " of " counter(pages);
            font-size: 8pt;
            color: #888;
         }
      }

      .header-table {
         width: 100%;
         margin-bottom: 12px;
         border-collapse: collapse;
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
         font-size: 11px;
         color: #666;
      }

      .section-title {
         background-color: #222;
         color: #fff;
         padding: 6px 10px;
         font-size: 15px;
         font-weight: bold;
         margin: 18px 0 8px;
         border-radius: 3px;
      }

      .text-center {
         text-align: center;
      }

      .text-left {
         text-align: left;
      }

      .text-right {
         text-align: right;
      }

      .top-hr {
         border: 0;
         border-top: 0.5px solid gray;
         margin: 15px 0;
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

      .watermark2 {
         position: fixed;
         top: 47%;
         left: 42%;
         opacity: 0.05;
         font-size: 100px;
         transform: rotate(-30deg);
         z-index: -1;
      }

      .text-right {
         text-align: right;
      }

      /* Additional styles for better PDF rendering */
      .info-table {
         width: 100%;
         border-collapse: collapse;
         font-size: 14px;
         margin-top: 20px;
         margin-bottom: 10px;
      }

      .info-table td {
         vertical-align: top;
         padding: 5px;
         border: none;
      }
   </style>
</head>

<body>
   <!-- Watermark -->
   <div class="watermark">ASLOOB BEDAA CO.</div>

   <!-- Header -->
   <table class="header-table">
      <tr>
         <td style="width: 70%;">
            <img src="{{ public_path('contents/admin/assets/images/logo_new.png') }}"
               style="width: 130px; height: auto;">
         </td>
         <td style="width: 30%; text-align: end;">
            <div class="company-title">ASLOOB BEDAA CO.</div>
            <div class="subtitle">Riyadh 12611, Saudi Arabia</div>
            <div class="subtitle">+966 54 765 7236</div>
         </td>
      </tr>
   </table>

   <hr class="top-hr">

   <table class="info-table">
      <tr>
         <!-- Expence By -->
         <td style="width: 70%;">
            <strong>Expense By,</strong><br>
            <span style="margin-top: 5px;">Name: Md Rakib Hassan</span><br>
            <span>Trade: Publishing, and web</span><br>
            <span>Location: Al Arfaj, Al Olaya</span><br>
            <span>Project Name: Test Project</span><br>
         </td>

         <!-- Reference -->
         <td style="width: 30%;">
            <span>Reference No: RF10000001</span><br>
            <span>Issue Date: 04 Sep 2025</span><br>
            <span>Due Date: 09 Sep 2025</span><br>
         </td>
      </tr>
   </table>

   <hr class="content-hr">

   <table style="width: 100%; border-collapse: collapse; font-size: 14px; margin-top: 20px; margin-bottom: 10px;">
      <tr>
         <td style="width: 50%; vertical-align: top; padding: 5px; border: none;">
            <strong>Invoice Description,</strong><br>
            <span style="margin-top: 5px;">Lorem Ipsum is simply dummy text of the printing and typesetting industry.
               Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer
               took a galley of type and scrambled it to make a type specimen book. It has survived not only five
               centuries.</span><br>
         </td>

         <td style="width: 10%;"></td>

         <td style="width: 40%; vertical-align: top; padding: 5px; border: none;">
            <table style="width: 100%; font-size: 14px; border-collapse: collapse;">
               <tr>
                  <td style="padding: 2px;">Total (Before VAT)</td>
                  <td style="padding: 2px;" class="text-right">1000/-</td>
               </tr>
               <tr style="border-bottom: 1px solid #999;">
                  <td style="padding: 2px;">Total VAT</td>
                  <td style="padding: 2px;" class="text-right">5/-</td>
               </tr>
               <tr>
                  <td style="padding: 2px;">Total</td>
                  <td style="padding: 2px;" class="text-right">1005/-</td>
               </tr>
               <tr style="border-bottom: 1px solid #999;">
                  <td style="padding: 2px;">Paid</td>
                  <td style="padding: 2px;" class="text-right">700/-</td>
               </tr>
               <tr>
                  <td style="padding: 2px;">Due Amount</td>
                  <td style="padding: 2px;" class="text-right">305/-</td>
               </tr>
            </table>

         </td>
      </tr>
   </table>

</body>

</html>