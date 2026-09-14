<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><--::..PDF..::--></title>
	<!-- style -->
	<style>
		*{
			padding: 0;
			margin: 0;
			outline: 0;
			overflow: hidden;
		}
		ul{
			list-style: none;
		}
		a{
			text-decoration: none;
		}
		.pdf__wrap{
			width: 600px;
			margin:  10px auto;
			border: 1px solid #333;

		}
		/* header part */
		.header__part{
			border-bottom: 1px solid #333;
			padding: 20px;
			text-align: center;
		}
		.sub__title{

		}
		.title{

		}
		.descrip{

		}
		.address{
			font-size: 18px;
			font-weight: 600;
		}
		.phone{

		}
		/* body part */
		.body__part{
			padding: 20px;
		}
		.cust__info{
			margin:  0px 20px;
			display: flex;
			justify-content: space-between;
			align-items: center;
			margin-bottom: 10px;

		}
		.cust_info_left{

		}
		.cust_info_left ul{}
		.cust_info_left ul li{}
		.cust_info_left ul li strong{}

		.cust_info_right ul{}
		.cust_info_right ul li{
			margin-right: 70px;
		}
		.cust_info_right ul li strong{}
		/* customer details */
		.cust_details_table{}
		.cust_details_table table,th,td{
			border: 1px solid #333;
			border-collapse: collapse;
			padding: 10px;
		}
		.cust_details_table table{
			width: 95%;
			margin: 0 auto;
		}
		.cust_details_table table thead{}
		.cust_details_table table thead tr{}
		.cust_details_table table thead tr th{}

		.cust_details_table table tbody{}
		.cust_details_table table tbody tr{}
		.cust_details_table table tbody tr td{}
		/* footer part */
		.footer__part{
			padding: 20px;
		}
		.footer_part .ftpart_content{
			width: 90%;
			margin: 0 auto;
			display: flex;
			justify-content: space-between;
			align-items: center;
		}
		.footer_part .ftpart_content p{
			font-weight: bold;
			font-size: 18px;
		}
		.footer_part .ftpart_content p span{
			font-size: 20px;
			font-weight: 900;
		}
    /* qr code */
    .qr__code{

    }
    .qr_code_content{

    }

.da-code{
  position:relative;
  font-size:8px; /* change the size of this to see how ralph will scale up or down */
  height: 1em;
  width: 1em;
  margin:0 34em 34em 0;
  float:left;
}

.qr__code {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-bottom: 90px;
    margin-top: 16px;
}








    /* qr code */
		.print__menu{
			text-align: center;
		}
		.print__menu a {
			background: #ddd;
			padding: 5px 15px;
			display: inline-block;
			font-weight: 700;
			cursor: pointer;
		}
		@media print{
			.print__menu{
				display: none;
			}
		}
	</style>
	<!-- style -->
</head>
<body>
	<div class="print__menu">
		<a onclick="window.print()">Print Or Pdf</a>
	</div>
	<div class="pdf__wrap">
		<!-- header part -->
		<section class="header__part">
		 	<p class="sub__title">Memo</p>
		 	<h1 class="title">{{ $company->comp_name_en }}</h1>
		 	<p class="descrip">{{ $company->comp_address }}</p>
		 	<p class="phone"> <strong>Phone</strong> {{ $company->comp_phone1 }} , {{ $company->comp_phone2 }} </p>
		</section>
		<!-- body part -->
		<section class="body__part">
			<!-- customer info -->
			{{-- <div class="cust__info"> --}}
				<!-- customer info left -->
				{{-- <div class="cust_info_left">
					<ul>
						<li> <strong>Payment Id:</strong> Lorem, ipsum dolor. </li>
						<li> <strong>Payment Id:</strong> Lorem, ipsum dolor. </li>
						<li> <strong>Payment Id:</strong> Lorem, ipsum dolor. </li>
					</ul>
				</div> --}}
				<!-- customer info right -->
				{{-- <div class="cust_info_right">
					<ul>
						<li> <strong>Date:</strong> Lorem, ipsum dolor. </li>
						<li> <strong>Date:</strong> Lorem, ipsum dolor. </li>
					</ul>
				</div> --}}
			{{-- </div> --}}
			<!-- customer details in table-->
			<div class="cust_details_table">
				<table>
					<thead>
						<tr>
							<th>SL.No</th>
							<th>Description</th>
							<th>Qty</th>
							<th>Price</th>
							<th>Total</th>
						</tr>
					</thead>
					<!-- tbody -->
					<tbody>
						<!-- loop -->
						<tr>
							<td>1</td>
							<td>
								Lorem, ipsum dolor sit amet consectetur adipisicing elit. Dolor, deleniti?
							</td>
							<td style="text-align:right">3</td>
							<td style="text-align:right">8944</td>
							<td style="text-align:right">34039409</td>
						</tr>
						<tr>
							<td>1</td>
							<td>
								Lorem, ipsum dolor sit amet consectetur adipisicing elit. Dolor, deleniti?
							</td>
							<td style="text-align:right">3</td>
							<td style="text-align:right">8944</td>
							<td style="text-align:right">34039409</td>
						</tr>
						<!-- non loop -->
						<tr>
							<td></td>
							<td></td>
							<td></td>
							<td style="text-align:right">8944</td>
							<td style="text-align:right">3403</td>
						</tr>
					</tbody>
				</table>
			</div>
			<!-- end section -->
		</section>
    {{-- qr code --}}
    <section class="qr__code">
      <div class="qr_code_content">

		<img src="{{$qrCodedd}}" alt="Red dot" />
      </div>
    </section>
    {{-- qr code --}}
		<!-- footer part -->
		<section class="footer__part">
			<div class="ft_part_content">
				<p>lorem</p>
				<p> <span> &larr; </span> lorem <span> &larr; </span> </p>
				<p>lorem</p>
			</div>
		</section>
</body>
</html>
