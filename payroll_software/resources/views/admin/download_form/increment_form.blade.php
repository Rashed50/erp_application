<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Evaluation Form</title>
    {{-- <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin> --}}
    {{-- <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet"> --}}
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            /* font-family: Arial, Helvetica, sans-serif; */
            margin: 0;
            /* padding: 10px; */
            background-color: white;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;

        }

        .form-container {
            width: 100%;
            max-width: 841px;
            background-color: #ffffff;
            border: 1px solid gray;
             /* padding: 5px;;
            padding-left: 10px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); */
            padding: 10px;
            margin: 2px;

        }

         .header-section {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            margin-top: 5px;
        }

        .header-column {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .header-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 5px 5px;
            border-bottom: 1px dashed #ccc;
            margin-bottom: 5px;
        }

        /* .header {
            text-align: center;
            margin-bottom: 4px;
        }

        .header h1 {
            font-size: 12px;
            font-weight:300;
            color: #333;
            margin: 0;
            text-transform: uppercase;
        }

        .header .company-logo {
            font-size: 30px;
            font-weight: 300;
            color: #004d99;
            margin-bottom: 2px;
        }

        .header p {
            margin: 5px 0 0;
            font-size: 12px;
            color: #555;
        } */

        .section-title {
            background-color: #004d99;
            color: #fff;
            padding: 3px;
            font-weight: 300;
            /* border-radius: 5px; */
            margin-bottom: 5px;
            text-align: center;
        }

        .section-content {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 5px;
            margin-bottom: 5px;
        }

        .grid-item {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 5px;  /* item gap */
            align-items: center;
        }

        .grid-item span, .grid-item input {
            font-size: 12px;

        }

        .grid-item span {
            font-weight: 500;
            color: black;
            padding-left: 20px;
        }

        .grid-item input, .grid-item textarea {
            width: 100%;
            padding: 5px;
            border: 1px solid #ccc;
            /* border-radius: 5px; */
            font-size: 12px;
            /* box-sizing: border-box; */
        }

        .grid-item.full-width {
            grid-column: 1 / -1;
        }

        .salary-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin: 1px 5px;
        }

        .salary-column {
            background-color: #f9f9f9;
            padding:5px 5px;
            padding-left: 10px;
            border-radius: 8px;
        }

        .salary-column .title {
            font-weight: 100;
            margin-bottom: 0px;
            text-align: center;
            color: #004d99;
            /* background-color: #004d99;
            color: #fff;
            padding: 1px; */
        }

        .salary-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 5px 0;
            border-bottom: 1px dashed #ddd;
        }

        .salary-item:last-child {
            border-bottom: none;
        }

        .salary-item label {
            font-size: 12px;
            color: black;
            font-weight: 200;
        }

        .salary-item span {
            font-size: 12px;
            color:black;
        }

        .comments-section {
            margin-bottom: 0px;
        }

        .comments-section textarea {
            width: 100%;
            height: 70px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            resize: vertical;
            font-size: 12px;
            box-sizing: border-box;
            color: black;
        }

        .section-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2px;
        }

        .section-table th, .section-table td {
            border: 1px solid #ccc;
            padding: 5px 10px;
            text-align: left;
            font-size: 12px;
        }

        .section-table th {
            background-color: #f0f0f0;
            font-weight: 100;
            color: black;
        }

        .section-table td:nth-child(3) {
            text-align: center;
            width: 80px;
        }

        .section-table td:nth-child(4) {
            text-align: center;
            width: 80px;
        }

        .total-score {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            margin-bottom: 1px;
            font-weight: 700;
        }

        .total-score span {
            margin-right: 10px;
            font-size: 12px;
        }

        .total-score input {
            width: 100px;
        }

        .overall-rating {
            margin-bottom: 10px;
            margin-top: 5px;
            font-size: 12px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
        }

        .overall-rating label {
            margin-right: 15px;
            font-weight: 500;
            color: #333;
        }

        .overall-rating input[type="checkbox"] {
            margin-right: 5px;
        }

        .signature-section {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 30px;
            margin-top: 5px;
        }

        .signature-column {
            display: flex;
            flex-direction: column;
        }

        .signature-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 5px 5px;
            border-bottom: 1px dashed #ccc;
            margin-bottom: 5px;
        }

        .signature-item span {
            font-size: 12px;
            font-weight: 500;
            color: black;
        }

        .signature-item input {
            width: 150px;
            border: none;
            text-align: right;
            font-style: italic;
        }



        #employeeinfo {
            border-collapse: collapse;
            width: 100%;
            font-size: 12px;

        }

        #employeeinfo td,
        #employeeinfo th {
            border: 1px solid #ddd;
            padding: 10px;
        }

        #employeeinfo tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        #employeeinfo tr:hover {
            background-color: #ddd;
        }

        #employeeinfo th {
            padding-top: 3px;
            padding-bottom: 3px;
            text-align: center;
            background-color: #EAEDED;
            color: black;
        }

        #employeeinfo td {
            padding-top: 5px;
            padding-bottom: 5px;
            /*text-align: center;*/

        }

        #employeeinfo tr:last-child td {
         border-bottom: 1px solid #ddd;; /* Remove bottom border */
         border-right: 1px solid #ddd;; /* or border-left: none */
        }
    </style>
</head>
<body>

    <div class="form-container">

        <!-- <div class="header">
            <p>شركة أسلوب بدائع للمقاولات</p>
            <div class="company-logo">ASLOOB BEDAA CO.</div>
            <h1>EMPLOYEE PERFORMANCE EVALUATION FORM</h1>
            <p></p>
        </div> -->
        <div class="header-section">
            <div class="header-column"><img src="{{ asset($company->com_logo1) }}"  alt="Emp. Photo" width="135px" height="60px"></div>
            <div class="header-column">
                <h5 style="font-size: 12px; font:bold">EMPLOYEE PERFORMANCE EVALUATION FORM</h5>
            </div>
            <div class="header-column"> <img src="{{ asset($company->com_logo) }}"  alt="" width="120px" height="55px"></div>
        </div>

            @php
                $startDate = new DateTime($employee->joining_date != null ? $employee->joining_date : new DateTime()); // Convert joining_date to DateTime
                $endDate = new DateTime(); // Current date
                $interval = $startDate->diff($endDate);
                if($interval->y ==0 ){
                    $total_duration = ' ';
                }else if($interval->y == 1 ){
                    $total_duration = $interval->y.' Year ';
                }else{
                    $total_duration = $interval->y.' Years ';
                }
                if($interval->m == 0){
                    $total_duration =  $total_duration .' ';
                }else if($interval->m == 1 ){
                    $total_duration =  $total_duration.$interval->m.' Month ';
                }else{
                    $total_duration =  $total_duration.$interval->m.' Months ';
                }

            @endphp

        <div class="section-title">Employee Information</div>
        {{-- <div class="section-content">
            <div class="grid-item">
                <span>Employee ID:</span>
                <span> {{ $employee->employee_id }} </span>
            </div>
            <div class="grid-item">
                <span>Iqama & Passport:</span>
                <span> {{ $employee->akama_no }}, {{ $employee->passfort_no }}</span>
            </div>
            <div class="grid-item">
                <span> Emp. Name:</span>
                 <span>{{ $employee->employee_name }}</span>
            </div>
            <div class="grid-item">
                <span>Designation:</span>
                 <span>{{ $employee->catg_name }}</span>
            </div>
            <div class="grid-item">
                <span>Mobile Number:</span>
                <span>{{ $employee->mobile_no }} </span>
            </div>
            <div class="grid-item">
                <span>Working Project:</span>
                <span> {{ $employee->proj_name }}  </span>
            </div>
            <div class="grid-item">
                <span>Joining at:</span>
                <span>{{ $employee->joining_date }}  </span>
            </div>
            <div class="grid-item">
                <span> Last Increment  </span>
                <span> {{ $employee->last_increment_amount >0 ? ($employee->last_increment_amount.' SAR, '.$employee->last_increment_date) : '-' }} {{ $employee->last_increment_date != null ?  $employee->last_increment_date: ''   }} ,  {{$effective_date}}  </span>
            </div>
        </div>
         --}}

        <table id="employeeinfo">
            <tbody>
                <tr >
                  <td> Employee ID </td>
                  <td >{{ $employee->employee_id }} </th>
                  <td > Iqama No </td>
                  <td> {{ $employee->akama_no }}  </td>
                </tr>

                 <tr>
                    <td > Employee Name </td>
                    <td > {{ $employee->employee_name }} </td>
                    <td > Passport No </td>
                    <td class="td__emp_name" >{{ $employee->passfort_no }}  </td>
                 </tr>
                 <tr>
                    <td >Designation </td>
                    <td > {{ $employee->catg_name }} </td>
                    <td > Mobile Number  </td>
                    <td > {{ $employee->mobile_no }} </td>
                 </tr>


                 <tr>
                    <td > Working Project </td>
                    <td  > {{ $employee->proj_name }} </td>
                    <td >  </td>
                    <td>   </td>
                 </tr>
                <tr>
                    <td class="td__fixed_width" > Joining Date   </td>
                    <td class="td__emp_name" > {{ $employee->joining_date }} </td>
                    <td class="td__fixed_width"> Last Increment  </td>
                    <td class="td__emp_name" > {{ $employee->last_increment_amount >0 ? ($employee->last_increment_amount.' SAR, '.$employee->last_increment_date) : '-' }} {{ $employee->last_increment_date != null ?  $employee->last_increment_date: ''   }}   </td>
                </tr>
            </tbody>
        </table>


        <div class="salary-section">
            <div class="salary-column">
                <div class="title">Present Salary Details</div>
                <div class="salary-item">
                    <label>Salary Type</label>
                    <span>{{ $employee->hourly_employee == 1 ? "Hourly": "Basic" }} </span>
                </div>
                <div class="salary-item">
                    <label>Amount</label>
                    <span>{{ $employee->hourly_employee == 1 ? $employee->hourly_rent: $employee->basic_amount  }} SAR</span>
                </div>
                <div class="salary-item">
                    <label>Food/Others</label>
                    <span>{{ $employee->food_allowance }}/{{ $employee->mobile_allowance + $employee->medical_allowance +$employee->local_travel_allowance +$employee->others +$employee->conveyance_allowance}} SAR</span>
                </div>
                 <div class="salary-item">
                    <label>Working Duration</label>
                    <span>{{  $total_duration }}</span>
                </div>

            </div>
            <div class="salary-column">
                <div class="title">Proposed Salary Details</div>
                <div class="salary-item">
                    <label>Salary Type</label>
                    <span>  {{ $employee->new_salary_type == 1 ? "Hourly": "Basic" }}  </span>
                </div>
                <div class="salary-item">
                    <label>Amount</label>
                     {{-- <input type="text" placeholder="SAR"> --}}
                    <span> {{$amount}} SAR</span>
                </div>
                <div class="salary-item">
                    <label>Others</label>
                    {{-- <input type="text" placeholder="SAR"> --}}
                    <span>0 SAR</span>
                </div>
                <div class="salary-item">
                        <label>Minimum Duration</label>
                        <span>  {{$employee->increment_duration == 1 ? $employee->increment_duration.' Year': $employee->increment_duration.' Years' }}, Increment From:  {{$effective_date}}</span>
                </div>
            </div>
        </div>

        <div class="section-title">Employee's Commitment</div>
        <div class="comments-section">
            <textarea placeholder="I sincerely request your kind consideration for a salary increment, which I believe will serve as a strong motivation for me to remain committed and dedicated to the continued success of our company. Thank you very much for your attention to this matter, and I look forward to your favorable response."></textarea>
        </div>


        <div class="section-title">Evaluator's Comment</div>
        <table class="section-table">
            <thead>
                <tr style="text-align: center;font:bold">
                    <th ><b>Criteria</b></th>
                    <th><b>Description</b></th>
                    <th><b>Rating (1-5)</b></th>
                    <!-- <th>Weight (%)</th> -->
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Job Knowledge & Skills</td>
                    <td>Understands duties, technical knowledge, skill development</td>
                    <td></td>
                    <!-- <td></td> -->
                </tr>
                <tr>
                    <td>Quality of Work</td>
                    <td>Accuracy, attention to detail, meeting work standards</td>
                    <td></td>
                    <!-- <td></td> -->
                </tr>
                <tr>
                    <td>Productivity & Efficiency</td>
                    <td>Meets deadlines, manages workload, efficient use of time</td>
                    <td></td>
                    <!-- <td></td> -->
                </tr>
                <tr>
                    <td>Communication & Teamwork</td>
                    <td>Communication, collaboration, positive contribution</td>
                    <td></td>
                    <!-- <td></td> -->
                </tr>
                <tr>
                    <td>Dependability & Responsibility</td>
                    <td>Attendance, punctuality, accountability, reliability</td>
                    <td></td>
                    <!-- <td></td> -->
                </tr>
                <tr>
                    <td>Initiative & Problem-Solving</td>
                    <td>Takes initiative, innovation, decision-making ability</td>
                    <td></td>
                    <!-- <td></td> -->
                </tr>
            </tbody>
        </table>

        <!-- <div class="total-score">
            <span>Total Score (%):</span>
            <input type="text">
        </div> -->

        <div class="comments-section">
            <textarea placeholder="Please write your comments here ..."></textarea>
        </div>

        <div class="overall-rating">
            &nbsp;&nbsp; Overall Rating:
            <label><input type="checkbox" name="rating">Excellent</label>
            <label><input type="checkbox" name="rating">Good</label>
            <label><input type="checkbox" name="rating">Satisfactory</label>
            <label><input type="checkbox" name="rating">Needs Improvement</label>
            <label><input type="checkbox" name="rating">Unsatisfactory</label>
        </div>

        <div class="signature-section">
            <div class="signature-column">
                <div class="section-title">Performance Recommended By:</div>
                <div class="signature-item">
                    <span>Supervisor:</span>
                    <input type="text">
                </div>
                <div class="signature-item">
                    <span>Project Manager:</span>
                    <input type="text">
                </div>
                <div class="signature-item">
                    <span>Project Director:</span>
                    <input type="text">
                </div>  <br> <br> <br>
                <div class="signature-item">
                    <span> --------------------------------- <br> <b>COO</b></span>
                </div>
            </div>
            <div class="signature-column">
                <div class="section-title">Head Office Approved By:</div>
                <div class="signature-item">
                    <span>HR Manager:</span>
                    <input type="text">
                </div>
                <div class="signature-item">
                    <span>Accounts Department:</span>
                    <input type="text">
                </div>
                <div class="signature-item">
                    <span>Operation Director</span>
                    <input type="text">
                </div> <br> <br> <br>
                <div class="signature-item">
                    <span> ----------------------------- <br> <b>President</b></span>
                </div>
            </div>
        </div>
        <!-- <div style="height:20px; display: flex;gap: 50px;">
            <div style="flex: 1; background-color: #ecf0f1; padding-top: 20px; padding-bottom: 2px; text-align: center;">COO:------------------------------- <br style="padding: 0px; margin: 0px;"></div>
            <div style=" flex: 1; background-color: #ecf0f1; padding-top: 20px; padding-bottom: 2px; text-align: center;">President:------------------------------- <br style="padding: 0px; margin: 0px;"></div>
        </div> -->
    </div>

</body>
</html>

<script>
    window.onload = function() {
        window.print();
    };
</script>
