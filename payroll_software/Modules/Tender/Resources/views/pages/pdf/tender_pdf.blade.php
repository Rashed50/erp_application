@extends('tender::pages.pdf.layouts.master')

@section('title', 'Tender PDF')

@section('style')
<style>
    #materials {
        width: 100%;
        border-collapse: collapse;
        /* page-break-inside: avoid; */
    }

    #materials td:first-child {
        text-align: start;
        vertical-align: top;
    }

    #materials td:nth-child(2) {
        text-align: left;
    }

    @media print {

        /* এই সেলটি পরবর্তী পেজে চলে যাবে যদি কনটেন্ট বেশি হয় */
        .general-conditions {
            page-break-before: always;
        }
    }

    #abcd{
        border-collapse: collapse;
        border: solid 1px black;
    }

    #abcd td, #abcd th {
        border: 1px solid black;
        padding: 5px;
    }

    #abcd td:nth-child(3),
    #abcd td:nth-child(4),
    #abcd td:nth-child(5) {
        text-align: center; 
    }
</style>
@endsection


@section('content')
<h3 style="margin-bottom: 0;">{{ $data['company_representative_name'] }}</h3>
<span style="margin-top: 2px;">P.O Box # {{ $data['post_box'] }}</span><br>
<span>{{ $data['city'] }}</span><br>
<span>Tel: {{ $data['phone_number'] }}, Fax: {{ $data['fax_number'] }}</span><br>
<span>CR. {{ $data['cr_no'] }}</span><br>
<span>{{ $data['country'] }}</span><br>

<h3 style="margin-bottom: 0;">Attention: {{ $data['company_representative_name'] }}</h3>
<p style="margin-top: 2px;">{{ $data['company_representative_designation'] }}</p>

<h3 style="margin-bottom: 0;">Ref Project: {{ $data['ref_project'] }}</h3>
<p style="margin-top: 2px; margin-left: 40px;">Letter of Intent: {{ $data['letter_of_intent'] }}</p>

<p style="margin-bottom: 0;">Dear Sir,</p>
<p class="paragraph">
    We are pleased to advise that, we are prepared to enter into a formal subcontract Agreement with yourselves
    for the provision of Super Structure Installation as described above for {{ $data['ref_project'] }}.
    This letter constitutes the letter of intent of ASLOOB BEDAA CONTRACTING CO. to enter into such a contract
    with <strong>{{ $data['company_representative_name'] }}</strong> on a Re-measurable basis constituting,
    ASLOOB
    BEDAA CONTRACTING CO. Standard Terms and conditions, Bills of Quantities, AFC Drawings, Specifications, Time
    Schedule for a total sum of
</p>

<h3 style="margin: 5px 0 0 0;">SAR {{$data['total_amount']}}</h3>
<h3 style="margin: 2px 0;">
    ({{$data['total_amount_written_in_words']}}, excluding VAT.)
</h3>
<p class="paragraph">
    The terms and conditions of the formal agreement shall be as below:
</p>

<span><strong>1)</strong> Advance payment – {{$data['advance_payment']}}%.</span><br>
<span><strong>2)</strong> Retention – {{$data['retention']}}%.</span><br>

<span><strong>3) PAYMENT TERMS:</strong></span><br>

<div class="text-justify">
    3.1) {{$data['actual_executed_quantities_word']}} percent ({{$data['actual_executed_quantities']}}%) of the actual
    executed quantities will be payable after thirty (30) days of
    submitting Consultant / Client (Aecom / Nesma) approved executed quantities.
</div>

<div class="text-justify">
    <strong>4)</strong> All works shall be in full compliance with the project specifications, AFC drawings, standard
    codes and practices. Co-ordination and interfacing with the MEP, Structural, Architectural subcontractor and any
    other subcontractors
    whose works are adjacent or against or impact the installation of {{$data['installation']}}. Installation of
    {{$data['installation']}} Works system for Shuttering, scaffolding, rebar Cutting-Bending, Rebar fixing, Concrete
    Pouring to complete the job,
</div>

<div class="paragraph">
    <span>
        {{$data['company_representative_name']}} is deemed to have included all cost due to changes in legislation,
        taxation and
        duties until
        the contracted scope of works are completed in all respects except for any of such changes which are
        reimbursable by
        Client under main contract.
    </span>

    <h4 class="mt-2">Sub Contractor {{$data['company_representative_name']}}</h4>
    <span>
        . shall be responsible to deploy adequate qualified Supervisor / quality personnel as per Shomoul standards. Sub
        Contractor {{$data['company_representative_name']}} shall also be responsible for needful Saudization as
        required by MOL / Shomoul. Sub Contractor {{$data['company_representative_name']}} shall strictly comply with
        Shomoul Standards (Safety,
        Health and Environment Management) requirements. Asloob Bedaa Contracting Co. shall provide (shared) Fork Lift,
        BobCat (facilities) for Shifting pallets to required locations only based on work schedule.
    </span><br>
    <p class="mt-2">
        Sub Contractor {{$data['company_representative_name']}} shall be responsible of shifting of all materials within
        the required plot location. However, delivery of all materials to the plots shall be provided by Asloob Bedaa
        Contracting Co.
    </p>
</div>

<div class="paragraph">
    <table id="materials">
        <tr>
            <td width="5%">
                <h3>6.</h3>
            </td>
            <td width="95%">
                <h3>WORK SCHEDULE & DURATION:</h3>
                Detailed schedule shall be agreed and respected as per project overall plan.
            </td>
        </tr>

        <tr>
            <td width="5%">
                <h3>7.</h3>
            </td>
            <td width="95%">
                <h3>PENALTY:</h3>
                <p class="text-justify">
                    Delay in completion of works beyond the agreed schedule will be subject to penalty of one percent
                    (1%) per week of total
                    order value up to max. ten percent (10%) of the total order value. However, in the event of delayed
                    payment
                    disbursement, the period of delayed until the moment of disbursement will not be considered in
                    calculating any fines.
                </p>
            </td>
        </tr>

        <tr>
            <td width="5%">
                <h3>8.</h3>
            </td>
            <td width="95%" class="general-conditions">
                <h3>GENERAL CONDITIONS:</h3>
                <p class="text-justify">
                    8.1) The prices include all labor cost & staff accommodation, insurance, taxes/fees and other cost
                    according to Saudi
                    Arabian Law and regulation.
                </p>
                <p class="text-justify">
                    8.2) The above mentioned scope of work price is fixed and not subject to any price escalation.
                </p>
                <p class="text-justify">
                    8.3) Mobilization/demobilization of equipment & manpower with all required equipment & tools are the
                    subcontractor
                    responsibilities.
                </p>
                <p class="text-justify">
                    8.4) The subcontractor is responsible to secure the area of his equipment and material including
                    guarding of his
                    equipment, and materials. Asloob Bedaa Contracting will provide space for storage.
                </p>
                <p class="text-justify">
                    8.5) This scope of work shall be executed in accordance with the project specs. /standards
                </p>
            </td>
        </tr>

        <tr>
            <td width="5%">
                <h3></h3>
            </td>
            <td width="95%" class="general-conditions">
                <p class="text-justify">
                    8.6) The Subcontractor will assign adequate and professional manpower & staff for scope of works in
                    order to finalize
                    the scope of works on or before the scheduled time.
                </p>
                <p class="text-justify">
                    8.7) The Subcontractor will provide all personnel, labor, QCs, HSE, equipment and tools required for
                    the execution and
                    completion of the work per The Subcontractor’s scope.
                </p>
                <p class="text-justify">
                    8.8) The Subcontractor is responsible to assign permanently civil engineer or Ass.
                </p>
                
                <p class="text-justify">
                    8.9) The Subcontractor is responsible for the quality and acceptance of their works by ABCC.
                </p>
                
                <p class="text-justify">
                    8.10) The Subcontractor will perform the work in a safe and workmanlike manner and in accordance
                    with all applicable
                    Contractor and Company health, safety, and environment (HSE) and quality assurance requirements
                    (QA/QC).
                </p>
                
                <p class="text-justify">
                    8.11) The Subcontractor will conduct its operations in accordance with the relevant laws,
                    regulations, decrees, and/or
                    official government orders of the Kingdom of Saudi Arabia, having jurisdiction over the area in
                    which the works are
                    performed.
                </p>
                
                <p class="text-justify">
                    8.12) The Subcontractor will be responsible to arrange accommodation, insurance, messing,
                    transportation and insurance
                    to their manpower on the project site without any responsibility or obligations to ABCC.
                </p>
                
                <p class="text-justify">
                    8.13) All equipment/tools related to the scope of work of the Subcontractor used at site shall be
                    insured by The
                    Subcontractor.
                </p>
                
                <p class="text-justify">
                    8.14) All equipment must carry TUV certificates by third party.
                </p>
                
                <p class="text-justify">
                    8.15) The Subcontractor indemnifies ABCC against all actions, suits, claims, demands, cost, charge
                    or expenses of what
                    so ever arising in connection with death or injury suffered by persons employed by The Subcontractor
                    if by no fault of
                    ABCC.
                </p>
                
                <p class="text-justify">
                    8.16) The Subcontractor will be responsible for informing and reporting to Contractor immediately
                    upon the occurrence of
                    any event which may, now or in the future, impede the proper and timely execution of the work so
                    that appropriate
                    remedial action may be taken.
                </p>
                
                <p class="text-justify">
                    8.17) All The Subcontractor employees/labor shall be under its own sponsorship & responsibility in
                    front of the relevant
                    authorities, and all sanctions/penalties that may be applied in case of any violation of such in the
                    labor law or any
                    decrees/resolutions of the ministry of labor, ministry of interior & SEC.
                </p>
                
                <p class="text-justify">
                    8.18) Attached terms & conditions related to VAT to be considered as part of this W.O.
                </p>
                
                <p class="text-justify">
                    8.19) Remove excavated materials and excess slurry from site to Municipality approved dumping
                    location as required is
                    the Subcontractor responsibility.
                </p>
            </td>
        </tr>

        <tr>
            <td width="5%">
                <h3>9.</h3>
            </td>
            <td width="95%">
                <h3>VALUE ADDED TAX (VAT):</h3>
                <p class="text-justify">
                    See Appendix 8.1
                </p>
            </td>
        </tr>

        <tr>
            <td width="5%">
                <h3>10.</h3>
            </td>
            <td width="95%">
                <h3>SUSPENSION / TERMINATION:</h3>
                <p class="text-justify">
                    SEM may suspend / terminate part or all of the subcontractor scope of work through written notice to the sub-contractor,
                    the procedures of such suspension / termination will be in accordance with the main contract terms and conditions with
                    the project client.
                </p>
            </td>
        </tr>

        <tr>
            <td width="5%">
                <h3>11.</h3>
            </td>
            <td width="95%">
                <h3>WITHDRAWING OF WORKS:</h3>
                <p class="text-justify">
                    If subcontractor failed to comply with or delayed the performance of their scope of work of this P.O. ABCC has the right
                    to withdraw partially or totally the above-mentioned scope of work and execute these works by themselves or through
                    another subcontractor and deduct all impact costs from subcontractor account.
                </p>
            </td>
        </tr>

        <tr>
            <td width="5%">
                <h3>12.</h3>
            </td>
            <td width="95%">
                <h3>SETTLEMENT OF DISPUTES:</h3>
                <p class="text-justify">
                    Any dispute arising out of or in connection with this Purchase Order shall be resolved and settled amicably, and if no
                    amicable settlement of a dispute will reach then such dispute will be resolved at Saudi courts of Riyadh City
                    exclusively.
                </p>
            </td>
        </tr>

        <tr>
            <td width="5%">
                <h3>13.</h3>
            </td>
            <td width="95%">
                <h3>INSURANCE:</h3>
                <p class="text-justify">
                    Subcontractor is responsible to ensure his own vehicle, equipment and manpower related to this W.O. scope of works under
                    insurance policy as per the requirement of Saudi Law and submit copy of this insurance policy to ABCC.
                </p>
            </td>
        </tr>

        <tr>
            <td width="5%">
                <h3>14.</h3>
            </td>
            <td width="95%">
                <h3>LIABILITY:</h3>
                <p class="text-justify">
                    Subcontractor indemnifies ABCC against all actions, suits, claims, demands, cost, charge or expenses of what so ever
                    arising in connection with death or injury suffered by persons employed by SUBCONTRACTOR.
                </p>
            </td>
        </tr>

        <tr>
            <td width="5%">
                <h3>15.</h3>
            </td>
            <td width="95%">
                <h3>CLIENT APPROVAL:</h3>
                <p class="text-justify">
                    This Work Order is subject to SEC approval for SUBCONTRACTOR as a subcontract for civil works.
                </p>
            </td>
        </tr>

        <tr>
            <td width="5%">
                <h3>16.</h3>
            </td>
            <td width="95%">
                <h3>ACCEPTANCE:</h3>
                <p class="text-justify">
                    Subcontractor is kindly requested to confirm their acceptance to this order by signing this copy and send it back
                    immediately to ABCC by return e-mail. Manufacturer / SUBCONTRACTOR shall provide the signed and stamped copy of this
                    order within maximum three (3) days from the date of Order, otherwise this Order will be considered as acknowledged and
                    accepted by the Manufacturer / Supplier, any comments raised by Manufacturer after the aforesaid duration shall not be
                    acceptable for any reason whatsoever.
                </p>
            </td>
        </tr>
    </table>
</div><br><br>


<div class="mt-2">
    <p>The schedule of works is; -</p>
    <p>
        Duration for complete Block Works, Assigned Areas including mobilization, Supervision, preparation of site as per
        Drawings, Handing over of all activities shall as per the attached construction schedule (Annexure 3).
    </p>

    <p class="mt-1" style="margin-top: 10px;">
        As a token of your acceptance please sign / stamp this document and send it back to us for record and necessary onward action.
    </p>
   
    <table width="100%" class="mt-2">
        <tr>
            <td width="50%" style="text-align: start;">
                Yours Faithfully,
            </td>
            <td width="50%" style="text-align: center;">
                Accepted By
            </td>
        </tr>

        <tr>
            <td width="50%" style="text-align: start;">
                Mohd. Kitab Ali
            </td>
            <td width="50%" style="text-align: center;">
                ALI MOHAMMED ALI ALHORAIBI
            </td>
        </tr>

        <tr>
            <td width="50%" style="text-align: start;">
                Project Manager
            </td>
            <td width="50%" style="text-align: center;">
                Owner of the Company
            </td>
        </tr>
    </table>
</div>

<div style="page-break-before: always;">
    <p>Enclosure:</p>
    <p>Annexure 1: Responsibility Matrix (03 page)</p>
    <p>Annexure 2: Bill of Quantity (01 page)</p>
    <p>Annexure 3 & 4 : Schedule of Works (1 page)</p>
</div>

<div style="page-break-before: always;">
    <h4 style="text-decoration: underline">Annexure 1: RESPONSIBILITY MATRIX</h4>
    <table width="100%" class="mt-1" id="abcd">
        <thead>
            <tr>
                <th colspan="5">MATRIX OF RESPONSIBILITY FOR ARCHITECTURAL INSTALLATION SUBCONTRACT</th>
            </tr>
            <tr>
                <th width="5%">No.</th>
                <th width="50%">DESCRIPTION</th>
                <th width="15%">NESMA</th>
                <th width="15%">ASLOOB</th>
                <th width="15%">SUBCON</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>Visas Cost</td>
                <td></td>
                <td></td>
                <td>Yes</td>
            </tr>
            <tr>
                <td>2</td>
                <td>Visas Procedure</td>
                <td></td>
                <td></td>
                <td>Yes</td>
            </tr>
            <tr>
                <td>3</td>
                <td>Food</td>
                <td></td>
                <td></td>
                <td>Yes</td>
            </tr>
            <tr>
                <td>4</td>
                <td>Accommodation</td>
                <td></td>
                <td></td>
                <td>Yes</td>
            </tr>
            <tr>
                <td>5</td>
                <td>Transportation of Manpower Within Site, from Accommodation to Site, and from Site to Accommodation</td>
                <td></td>
                <td></td>
                <td>Yes</td>
            </tr>
            <tr>
                <td>6</td>
                <td>Gate pass, badges etc.…</td>
                <td>Yes</td>
                <td>Yes</td>
                <td></td>
            </tr>
            <tr>
                <td>7</td>
                <td>Transportation of Permanent Material Within Site limits (From Store to site & between different areas)</td>
                <td></td>
                <td></td>
                <td>Yes</td>
            </tr>
            <tr>
                <td>8</td>
                <td>QA/QC Manager</td>
                <td>Yes</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>9</td>
                <td>QC Inspectors</td>
                <td>Yes</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>10</td>
                <td>Compliance with Quality Manual</td>
                <td>Yes</td>
                <td>Yes</td>
                <td>Yes</td>
            </tr>
            <tr>
                <td>11</td>
                <td>Safety Manager</td>
                <td>Yes</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>12</td>
                <td>Safety inspector (1 HSE inspector for every 50 Labour)</td>
                <td></td>
                <td>YesYes</td>
                <td></td>
            </tr>
            <tr>
                <td>13</td>
                <td>Compliance with Safety Manual</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>14</td>
                <td>Safety PPE, Including Gloves, Safety Belt etc...</td>
                <td>Yes</td>
                <td>Yes</td>
                <td>Yes</td>
            </tr>
            <tr>
                <td>15</td>
                <td>Permanent Material for Construction</td>
                <td>Yes</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>16</td>
                <td>All Consumable Materials</td>
                <td>Yes</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>17</td>
                <td>Scaffolding & Formwork</td>
                <td>Yes</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>18</td>
                <td>Erection, Handling, Dismantling, And Relocation of Scaffolding and formwork</td>
                <td></td>
                <td></td>
                <td>Yes</td>
            </tr>
            <tr>
                <td>19</td>
                <td>Steel, Concrete, Formwork Materials</td>
                <td>Yes</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>20</td>
                <td>Hand tools, Construction tools (i.e. hammers, trowels, concrete vibrators, etc..)</td>
                <td></td>
                <td></td>
                <td>Yes</td>
            </tr>
            <tr>
                <td>21</td>
                <td>Supply his own Fabrication Tools to be Utilized in the Subcontracted Works</td>
                <td></td>
                <td></td>
                <td>Yes</td>
            </tr>
            <tr>
                <td>22</td>
                <td>Angle, Grinder, Concrete Chipping Machine</td>
                <td></td>
                <td>Yes</td>
                <td></td>
            </tr>
            <tr>
                <td>23</td>
                <td>Tower Cranes</td>
                <td>Yes</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>24</td>
                <td>Hoist</td>
                <td>Yes</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>25</td>
                <td>Generators</td>
                <td>Yes</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>26</td>
                <td>Site Office with Water, and Electricity</td>
                <td>Yes</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>27</td>
                <td>Internet in Site Office</td>
                <td></td>
                <td></td>
                <td>Yes</td>
            </tr>
            <tr>
                <td>28</td>
                <td>Daily Manpower Report</td>
                <td></td>
                <td></td>
                <td>Yes</td>
            </tr>
            <tr>
                <td>29</td>
                <td>Planning & Scheduling the Subcontracted Works</td>
                <td>Yes</td>
                <td>Yes</td>
                <td></td>
            </tr>
            <tr>
                <td>30</td>
                <td>Weekly, Monthly Progress Report</td>
                <td></td>
                <td>Yes</td>
                <td></td>
            </tr>
            <tr>
                <td>31</td>
                <td>Submittal of Quantification based invoices</td>
                <td></td>
                <td></td>
                <td>Yes</td>
            </tr>
            <tr>
                <td>32</td>
                <td>insurance for materials and equipment</td>
                <td>Yes</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>33</td>
                <td>Labor Workmanship Insurance</td>
                <td></td>
                <td></td>
                <td>Yes</td>
            </tr>
            <tr>
                <td>34</td>
                <td>Vehicle & Equipment Insurance</td>
                <td></td>
                <td></td>
                <td>Yes</td>
            </tr>
            <tr>
                <td>35</td>
                <td>Development of RFC Request For Clarification</td>
                <td>Yes</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>36</td>
                <td>Shop drawings when required</td>
                <td>Yes</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>37</td>
                <td>Red line and As built Drawings</td>
                <td>Yes</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>38</td>
                <td>Housekeeping related to Subcontracted Works</td>
                <td></td>
                <td></td>
                <td>Yes</td>
            </tr>
            <tr>
                <td>39</td>
                <td>Protection of material related to Subcontracted on site</td>
                <td></td>
                <td></td>
                <td>Yes</td>
            </tr>
            <tr>
                <td>40</td>
                <td>Punch listing, and repair Works</td>
                <td>Yes</td>
                <td>Yes</td>
                <td>Yes</td>
            </tr>
            <tr>
                <td>41</td>
                <td>Surveying Works (Nesma Team to provide Benchmarks. Review Completed Work & Handle Inspection with the Consultant)</td>
                <td></td>
                <td>Yes</td>
                <td></td>
            </tr>
            <tr>
                <td>42</td>
                <td>Mixing Mortar & Grout to comply with project specs, approved submittal, and approved mix design. Daily test samples
                required by third party for review & approval</td>
                <td></td>
                <td></td>
                <td>Yes</td>
            </tr>
            <tr>
                <td>43</td>
                <td>Wastage beyond (Concrete 1% & rebar 2%)</td>
                <td></td>
                <td></td>
                <td>Yes</td>
            </tr>
            <tr>
                <td>44</td>
                <td>Cutting by Grinder for MEP Box Fixing Works</td>
                <td></td>
                <td></td>
                <td>Yes</td>
            </tr>
            <tr>
                <td>45</td>
                <td>Fixing of Anchor Bolt with Template</td>
                <td></td>
                <td></td>
                <td>Yes</td>
            </tr>
            <tr>
                <td>46</td>
                <td>Curing as necessary (Workmanship only)</td>
                <td></td>
                <td>Yes</td>
                <td></td>
            </tr>
            <tr>
                <td>47</td>
                <td>Transportation of main equipment</td>
                <td>Yes</td>
                <td>Yes</td>
                <td></td>
            </tr>
            <tr>
                <td>48</td>
                <td>Transportation of Main Equipment from the Location where it is Unloaded by Tower Crane to the Location of Installation
                (Fork lift and Bobcat by Nesma)</td>
                <td></td>
                <td></td>
                <td>Yes</td>
            </tr>
            <tr>
                <td>49</td>
                <td>Rebar Fabrication works</td>
                <td></td>
                <td>Yes</td>
                <td></td>
            </tr>
            <tr>
                <td>50</td>
                <td>Rebar Cutting and bending Machine</td>
                <td>Yes</td>
                <td>Yes</td>
                <td></td>
            </tr>
            <tr>
                <td>51</td>
                <td>Handing over works to consultant & Attending all Inspections</td>
                <td>Yes</td>
                <td>Yes</td>
                <td>Yes</td>
            </tr>
            <tr>
                <td>52</td>
                <td>Temporary Power Supply on Site till Temporary Panel.</td>
                <td>Yes</td>
                <td>Yes</td>
                <td></td>
            </tr>
            <tr>
                <td>53</td>
                <td>Water for Construction Work & Curing</td>
                <td>Yes</td>
                <td></td>
                <td></td>
            </tr>
            
            
        </tbody>
    </table>
</div>

@endsection