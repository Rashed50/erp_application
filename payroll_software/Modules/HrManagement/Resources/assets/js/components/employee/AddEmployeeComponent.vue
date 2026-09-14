<template>
    <div class="container-fluid">
        <!-- Step tracker -->
        <div class="row">
            <div class="col-md-12">
                <div class="row justify-content-center text-center">
                    <div class="col-6 col-md-3">
                        <span class="badge rounded-pill"
                            :class="currentStep >= 1 ? 'bg-success' : 'bg-secondary'">1</span>
                        <div class="mt-1"><small><strong>Employee Information</strong></small></div>
                    </div>
                    <div class="col-6 col-md-3">
                        <span class="badge rounded-pill"
                            :class="currentStep >= 2 ? 'bg-success' : 'bg-secondary'">2</span>
                        <div class="mt-1"><small><strong>Salary Details</strong></small></div>
                    </div>
                    <div class="col-6 col-md-3">
                        <span class="badge rounded-pill"
                            :class="currentStep >= 3 ? 'bg-success' : 'bg-secondary'">3</span>
                        <div class="mt-1"><small><strong>File Upload</strong></small></div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="this.currentStep == 1">
            <form id="employeeForm" novalidate @submit.prevent="submitForm">

                <!-- ══════════════════════════════════════════
                     CARD 1 — Employee / Identity / Present Address
                ══════════════════════════════════════════ -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header"><strong>Employee Information</strong></div>
                            <div class="card-body">
                                <div class="row g-2">



                                    <!-- Employee ID -->
                                    <div class="col-md-6 form-group">
                                        <div class="row g-2 align-items-center">
                                            <label class="col-12 col-sm-4 col-form-label">Employee ID:<span
                                                    class="text-danger">*</span></label>
                                            <div class="col-12 col-sm-8">
                                                <input type="text" class="form-control" v-model="form.employee_id"
                                                    readonly required />
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Employee for -->
                                    <div class="col-md-6 form-group">
                                        <div class="row g-2 align-items-center">
                                            <label class="col-12 col-sm-4 col-form-label">Employee of:<span
                                                    class="text-danger">*</span></label>
                                            <div class="col-12 col-sm-8">
                                                <select class="form-select" v-model="form.company_id"
                                                    @change="getNewEmployeeID()" required>
                                                    <option value="1">Asloob International Co.</option>
                                                    <option value="1">Asloob Bedda Co.</option>
                                                    <option value="1">Bedaa General Co.</option>
                                                    <option value="2">Other Employee</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Name -->
                                    <div class="col-md-6 form-group" :class="{ 'has-error': errors.emp_name }">
                                        <div class="row g-2 align-items-center">
                                            <label class="col-12 col-sm-4 col-form-label">Employee Name:<span
                                                    class="text-danger">*</span></label>
                                            <div class="col-12 col-sm-8">
                                                <input type="text" class="form-control" v-model="form.emp_name"
                                                    placeholder="Input Employee Name Here" autofocus required
                                                    @input="errors.emp_name = false" />
                                                <span class="invalid-feedback d-block" v-if="errors.emp_name">You
                                                    must fill in this field!</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Agency -->
                                    <div class="col-md-6 form-group" :class="{ 'has-error': errors.agency_id }">
                                        <div class="row g-2 align-items-center">
                                            <label class="col-12 col-sm-4 col-form-label">Agency Name:<span
                                                    class="text-danger">*</span></label>
                                            <div class="col-12 col-sm-8">
                                                <select class="form-select" v-model="form.agency_id" required
                                                    @change="errors.agency_id = false">
                                                    <option value="">Select Agency Name</option>
                                                    <option v-for="a in options.agencies" :key="a.agc_info_auto_id"
                                                        :value="a.agc_info_auto_id">{{ a.agc_title }}</option>
                                                </select>
                                                <span class="invalid-feedback d-block" v-if="errors.agency_id">You
                                                    must select this field!</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Passport No + Passport Expire Date (stacked, same column) -->
                                    <div class="col-md-6 form-group" :class="{ 'has-error': errors.passfort_no }">
                                        <div class="row g-2 align-items-center mb-1">
                                            <label class="col-12 col-sm-4 col-form-label">Passport No:<span
                                                    class="text-danger">*</span></label>
                                            <div class="col-12 col-sm-8">
                                                <!-- FIX: maxlength="15" added; input stays type="text" so
                                                     .length works reliably for validation -->
                                                <input type="text" class="form-control" v-model="form.passfort_no"
                                                    maxlength="15" placeholder="Input Passport Number Here" required
                                                    @input="checkThisEmployeePassportNumber()" />

                                                <span class="invalid-feedback d-block" v-if="errors.passfort_no">
                                                    {{ errors.passfort_no_msg }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="row g-2 align-items-center">
                                            <label class="col-12 col-sm-4 col-form-label">Passport Expire:<span
                                                    class="text-danger">*</span></label>
                                            <div class="col-12 col-sm-8">
                                                <input type="date" class="form-control"
                                                    v-model="form.passport_expire_date" required />
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Iqama No + Iqama Expire Date (stacked, same column) -->
                                    <div class="col-md-6 form-group" :class="{ 'has-error': errors.akama_no }">
                                        <div class="row g-2 align-items-center mb-1">
                                            <label class="col-12 col-sm-4 col-form-label">Iqama No:<span
                                                    class="text-danger">*</span></label>
                                            <div class="col-12 col-sm-8">
                                                <!-- FIX: type="number" -> type="text" (with inputmode="numeric").
                                                     v-model on type="number" sometimes yields a JS Number, which
                                                     breaks .length checks below. maxlength="10" also added. -->
                                                <input type="text" inputmode="numeric" class="form-control"
                                                    v-model="form.akama_no" maxlength="10"
                                                    placeholder="Input Iqama Number Here" required
                                                    @input="checkThisEmployeeIqamaNumber()" />

                                                <span class="invalid-feedback d-block" v-if="errors.akama_no">
                                                    {{ errors.akama_no_msg }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="row g-2 align-items-center">
                                            <label class="col-12 col-sm-4 col-form-label">Iqama Expire:<span
                                                    class="text-danger">*</span></label>
                                            <div class="col-12 col-sm-8">
                                                <input type="date" class="form-control" v-model="form.akama_expire"
                                                    required />
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Sponsor -->
                                    <div class="col-md-6 form-group" :class="{ 'has-error': errors.sponsor_id }">
                                        <div class="row g-2 align-items-center">
                                            <label class="col-12 col-sm-4 col-form-label">Sponsor Name:<span
                                                    class="text-danger">*</span></label>
                                            <div class="col-12 col-sm-8">
                                                <select class="form-select" v-model="form.sponsor_id" required
                                                    @change="errors.sponsor_id = false">
                                                    <option value="">Select Sponsor Name</option>
                                                    <option v-for="s in options.sponsors" :key="s.spons_id"
                                                        :value="s.spons_id">{{ s.spons_name }}</option>
                                                </select>
                                                <span class="invalid-feedback d-block" v-if="errors.sponsor_id">You
                                                    must select this field!</span>
                                            </div>
                                        </div>
                                    </div>


                                    <!-- Designation (above Present Address) -->
                                    <div class="col-md-6 form-group">
                                        <div class="row g-2 align-items-center">
                                            <label class="col-12 col-sm-4 col-form-label">Designation:<span
                                                    class="text-danger">*</span></label>
                                            <div class="col-12 col-sm-8">
                                                <select class="form-select" v-model="form.designation_id" required>
                                                    <option value="">Select Designation</option>
                                                    <option v-for="d in options.designations" :key="d.catg_id"
                                                        :value="d.catg_id">{{ d.catg_name }}</option>
                                                </select>
                                                <span class="invalid-feedback d-block" v-if="errors.designation_id">{{ errors.department_id_msg }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Employee Type -->
                                    <div class="col-md-6 form-group" :class="{ 'has-error': errors.emp_type_id }">
                                        <div class="row g-2 align-items-center">
                                            <label class="col-12 col-sm-4 col-form-label">Employee Type:<span
                                                    class="text-danger">*</span></label>
                                            <div class="col-12 col-sm-8">
                                                <select class="form-select" v-model="form.emp_type_id" required
                                                    @change="errors.emp_type_id = false">
                                                    <option value="">Select Employee Type</option>
                                                    <option v-for="t in options.empTypes" :key="t.id" :value="t.id">{{
                                                        t.name }}</option>
                                                </select>
                                                <span class="invalid-feedback d-block" v-if="errors.emp_type_id">You
                                                    must select this field!</span>
                                            </div>
                                        </div>
                                    </div>



                                    <!-- Villa (above Present Address) -->
                                    <div class="col-md-6 form-group">
                                        <div class="row g-2 align-items-center">
                                            <label class="col-12 col-sm-4 col-form-label">Villa Name</label>
                                            <div class="col-12 col-sm-8">
                                                <select class="form-select" v-model="form.accomd_ofb_id">
                                                    <option value="">Select Villa Name</option>
                                                    <option v-for="v in options.villas" :key="v.ofb_id"
                                                        :value="v.ofb_id">{{ v.ofb_name }} — {{ v.ofb_city_name }}
                                                    </option>
                                                </select>

                                                <span class="invalid-feedback d-block" v-if="errors.accomd_ofb_id">You
                                                    must select this field!</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Hourly employee -->
                                    <div class="col-md-6 form-group">
                                        <div class="row g-2 align-items-center">
                                            <label class="col-12 col-sm-4 col-form-label">Hourly:<span
                                                    class="text-danger"></span></label>
                                            <div class="col-12 col-sm-8">
                                                <div class="form-check mt-1">
                                                    <input class="form-check-input" type="checkbox" value="1"
                                                        id="hourly_employee" v-model="form.hourly_employee" />
                                                    <label class="form-check-label" for="hourly_employee">Hourly
                                                        Employee</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Abshar Mobile -->
                                    <div class="col-md-6 form-group">
                                        <div class="row g-2 align-items-center">
                                            <label class="col-12 col-sm-4 col-form-label">Abshar Mobile No:<span
                                                    class="text-danger">*</span></label>
                                            <div class="col-12 col-sm-8">
                                                <input type="tel" class="form-control" v-model="form.mobile_no"
                                                    placeholder="Input Mobile Number" required />

                                                <span class="invalid-feedback d-block" v-if="errors.mobile_no">You
                                                    must input this field!</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Present address (last field of this card) -->
                                    <div class="col-md-12 form-group">
                                        <div class="row g-2 align-items-center">
                                            <label class="col-12 col-sm-2 col-form-label">Present Address:</label>
                                            <div class="col-12 col-sm-10">
                                                <textarea class="form-control" rows="1" v-model="form.present_address"
                                                    placeholder="Input Present Address Here"></textarea>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD 2 — Permanent Address -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header"><strong>Permanent Address</strong></div>
                            <div class="card-body">
                                <div class="row g-2">

                                    <!-- Country -->
                                    <div class="col-md-6 form-group">
                                        <div class="row g-2 align-items-center">
                                            <label class="col-12 col-sm-4 col-form-label">Country</label>
                                            <div class="col-12 col-sm-8">
                                                <select class="form-select" v-model="form.country_id"
                                                    @change="searchDivisionsByCountry()" >
                                                    <option value="">Select Country</option>
                                                    <option v-for="c in options.countries" :key="c.id" :value="c.id">
                                                        {{ c.country_name }}</option>
                                                </select>

                                                <span class="invalid-feedback d-block" v-if="errors.country_id">You
                                                    must select any of this
                                                    field!</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Division -->
                                    <div class="col-md-6 form-group">
                                        <div class="row g-2 align-items-center">
                                            <label class="col-12 col-sm-4 col-form-label">Division</label>
                                            <div class="col-12 col-sm-8">
                                                <select class="form-select" v-model="form.division_id"
                                                    @change="searchDistrictsByDivision()">
                                                    <option value="">Select Division</option>
                                                    <option v-for="d in divisions" :key="d.division_id"
                                                        :value="d.division_id">{{ d.division_name }}</option>
                                                </select>

                                                <span class="invalid-feedback d-block" v-if="errors.division_id">You
                                                    must select any of this
                                                    field!</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- District -->
                                    <div class="col-md-6 form-group">
                                        <div class="row g-2 align-items-center">
                                            <label class="col-12 col-sm-4 col-form-label">District</label>
                                            <div class="col-12 col-sm-8">
                                                <select class="form-select" v-model="form.district_id">
                                                    <option value="">Select District</option>
                                                    <option v-for="d in districts" :key="d.district_id"
                                                        :value="d.district_id">{{ d.district_name }}</option>
                                                </select>
                                                <span class="invalid-feedback d-block" v-if="errors.district_id">You
                                                    must select any of this
                                                    field!</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Post code -->
                                    <div class="col-md-6 form-group">
                                        <div class="row g-2 align-items-center">
                                            <label class="col-12 col-sm-4 col-form-label">Post Code</label>
                                            <div class="col-12 col-sm-8">
                                                <input type="text" class="form-control" v-model="form.post_code"
                                                    placeholder="Input Post Code" />
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Address details -->
                                    <div class="col-md-12 form-group">
                                        <div class="row g-2 align-items-center">
                                            <label class="col-12 col-sm-2 col-form-label">Address Details</label>
                                            <div class="col-12 col-sm-10">
                                                <textarea class="form-control" rows="1" v-model="form.details"
                                                    placeholder="Input Address Details"></textarea>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD 3 — Contact & Other Details -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header"><strong>Contact &amp; Other Details</strong></div>
                            <div class="card-body">
                                <div class="row g-2">

                                    <!-- Project -->
                                    <div class="col-md-6 form-group">
                                        <div class="row g-2 align-items-center">
                                            <label class="col-12 col-sm-4 col-form-label">Select Project:</label>
                                            <div class="col-12 col-sm-8">
                                                <select class="form-select" v-model="form.project_id">
                                                    <option value="">Select Here</option>
                                                    <option v-for="p in options.projects" :key="p.proj_id"
                                                        :value="p.proj_id">{{ p.proj_name }}</option>
                                                </select>

                                                <span class="invalid-feedback d-block" v-if="errors.project_id">You
                                                    must select any of this
                                                    field!</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Department -->
                                    <div class="col-md-6 form-group" :class="{ 'has-error': errors.department_id }">
                                        <div class="row g-2 align-items-center">
                                            <label class="col-12 col-sm-4 col-form-label">Select Department:</label>
                                            <div class="col-12 col-sm-8">
                                                <select class="form-select" v-model="form.department_id"
                                                    @change="errors.department_id = false">
                                                    <option value="">Select Department</option>
                                                    <option v-for="d in options.departments" :key="d.dep_id"
                                                        :value="d.dep_id">{{ d.dep_name }}</option>
                                                </select>
                                                <span class="invalid-feedback d-block" v-if="errors.department_id">You
                                                    must select any of this
                                                    field!</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Email -->
                                    <div class="col-md-6 form-group" :class="{ 'has-error': errors.email }">
                                        <div class="row g-2 align-items-center">
                                            <label class="col-12 col-sm-4 col-form-label">Email:</label>
                                            <div class="col-12 col-sm-8">
                                                <input type="email" class="form-control" v-model="form.email"
                                                    placeholder="Input Email Address"
                                                    @input="checkThisEmployeeEmail()" />
                                                <span class="invalid-feedback d-block" v-if="errors.email">
                                                    {{ errors.error_msg }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Mobile 2 -->
                                    <div class="col-md-6 form-group">
                                        <div class="row g-2 align-items-center">
                                            <label class="col-12 col-sm-4 col-form-label">Mobile Number2</label>
                                            <div class="col-12 col-sm-8">
                                                <input type="tel" class="form-control" v-model="form.phone_no"
                                                    placeholder="Input Phone Number" />
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Home country phone -->
                                    <div class="col-md-6 form-group">
                                        <div class="row g-2 align-items-center">
                                            <label class="col-12 col-sm-4 col-form-label">Home Country Contact
                                                No:</label>
                                            <div class="col-12 col-sm-8">
                                                <input type="tel" class="form-control" v-model="form.country_phone_no"
                                                    placeholder="Home Country Contact Number" />
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Date of birth -->
                                    <div class="col-md-6 form-group">
                                        <div class="row g-2 align-items-center">
                                            <label class="col-12 col-sm-4 col-form-label">Date Of Birth:</label>
                                            <div class="col-12 col-sm-8">
                                                <input type="date" class="form-control" v-model="form.date_of_birth" />
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Blood group -->
                                    <div class="col-md-6 form-group">
                                        <div class="row g-2 align-items-center">
                                            <label class="col-12 col-sm-4 col-form-label">Blood Group:</label>
                                            <div class="col-12 col-sm-8">
                                                <select class="form-select" v-model="form.blood_group">
                                                    <option value="A+">A+</option>
                                                    <option value="A-">A-</option>
                                                    <option value="B+">B+</option>
                                                    <option value="B-">B-</option>
                                                    <option value="O+">O+</option>
                                                    <option value="O-">O-</option>
                                                    <option value="AB+">AB+</option>
                                                    <option value="AB-">AB-</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Marital status -->
                                    <div class="col-md-6 form-group">
                                        <div class="row g-2 align-items-center">
                                            <label class="col-12 col-sm-4 col-form-label">Marital Status:</label>
                                            <div class="col-12 col-sm-8">
                                                <select class="form-select" v-model="form.maritus_status">
                                                    <option value="0">Unmarried</option>
                                                    <option value="1">Married</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Gender -->
                                    <div class="col-md-6 form-group">
                                        <div class="row g-2 align-items-center">
                                            <label class="col-12 col-sm-4 col-form-label">Gender:</label>
                                            <div class="col-12 col-sm-8">
                                                <select class="form-select" v-model="form.gender">
                                                    <option value="M">Male</option>
                                                    <option value="F">Female</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Religion -->
                                    <div class="col-md-6 form-group">
                                        <div class="row g-2 align-items-center">
                                            <label class="col-12 col-sm-4 col-form-label">Religion:</label>
                                            <div class="col-12 col-sm-8">
                                                <select class="form-select" v-model="form.religion">
                                                    <option value="1">Muslim</option>
                                                    <option value="2">Hinduism</option>
                                                    <option value="3">Christianity</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Reference person -->
                                    <div class="col-md-6 form-group">
                                        <div class="row g-2 align-items-center">
                                            <label class="col-12 col-sm-4 col-form-label">Reference Person</label>
                                            <div class="col-12 col-sm-8">
                                                <input type="text" class="form-control" v-model="form.ref_employee_id"
                                                    placeholder="Input Reference Name or Employee ID" />
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Remarks -->
                                    <div class="col-md-6 form-group">
                                        <div class="row g-2 align-items-center">
                                            <label class="col-12 col-sm-4 col-form-label">Remarks</label>
                                            <div class="col-12 col-sm-8">
                                                <input type="text" class="form-control" v-model="form.remarks"
                                                    placeholder="Input Remarks Here" />
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Appointment date -->
                                    <div class="col-md-6 form-group">
                                        <div class="row g-2 align-items-center">
                                            <label class="col-12 col-sm-4 col-form-label">Appointment Date:</label>
                                            <div class="col-12 col-sm-8">
                                                <input type="date" class="form-control"
                                                    v-model="form.appointment_date" />
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Joining date -->
                                    <div class="col-md-6 form-group">
                                        <div class="row g-2 align-items-center">
                                            <label class="col-12 col-sm-4 col-form-label">Joining Date:</label>
                                            <div class="col-12 col-sm-8">
                                                <input type="date" class="form-control" v-model="form.joining_date" />
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Confirmation date -->
                                    <div class="col-md-6 form-group">
                                        <div class="row g-2 align-items-center">
                                            <label class="col-12 col-sm-4 col-form-label">Confirmation Date:</label>
                                            <div class="col-12 col-sm-8">
                                                <input type="date" class="form-control"
                                                    v-model="form.confirmation_date" />
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit -->
                <div class="row mt-3">
                    <div class="col-md-12 text-center">
                        <button type="button" class="btn btn-secondary waves-effect me-2">Cancel</button>
                        <button type="submit" class="btn btn-primary waves-effect" :disabled="is_submitted">
                            {{ is_submitted ? 'Saving…' : 'Next' }}
                        </button>
                    </div>
                </div>

            </form>
        </div>

        <!-- Salary Update Section -->
        <div v-else-if="currentStep === 2" class="row">
            <SalaryUpdateComponent :data="data" :searched_employee_id="this.new_employee_id"
                @updateSalaryCompletedCallBack="updateSalaryCompletedCallBack"></SalaryUpdateComponent>
        </div>

        <!-- File Update Section -->
        <div v-else-if="currentStep === 3" class="row">
            <FileUpdateComponent :data="data" :searched_employee_id="this.new_employee_id"
                @uploadEmployeeFilesCompletedCallBack="uploadEmployeeFilesCompletedCallBack"></FileUpdateComponent>
        </div>

    </div>
</template>

<script>
import { inject } from 'vue';
import axios from 'axios';
import { toast } from 'vue3-toastify';
import { NewEmployeeIDAPI, NewEmployeeInsertAPI, DivisionSearchByAPI, DistrictSearchByAPI, EmployeeCheckUniqueIDAPI } from '../../routes.js'
import SalaryUpdateComponent from './SalaryUpdateComponent.vue';
import FileUpdateComponent from './FileUpdateComponent.vue';
import { useAuth } from "../../../../../../../resources/js/components/useAuth.js";

const today = new Date().toISOString().split('T')[0]; // Get today's date in YYYY-MM-DD format

export default {
    setup() {
        const auth = inject('auth');
        const { hasPermission } = useAuth();

        return {
            hasPermission,
        };
    },
    name: 'AddEmployeeComponent',
    components: {
        SalaryUpdateComponent,
        FileUpdateComponent,
    },
    props: {
        data: {
            type: Object,
            required: true,
        },
    },
    data() {
        return {
            is_submitted: false,
            new_employee_id: '',
            currentStep: 1,
            divisions: [],
            districts: [],

            form: {
                company_id: '1',
                employee_id: this.data.empIdGeneret || '',
                emp_name: '',
                agency_id: '',
                sponsor_id: '',
                passfort_no: '',
                passport_expire_date: '',
                akama_no: '',
                akama_expire: today,
                accomd_ofb_id: '',
                mobile_no: '',
                phone_no: '',
                country_phone_no: '',
                email: '',
                present_address: '',
                country_id: '',
                division_id: '',
                district_id: '',
                post_code: '',
                details: '',
                project_id: '',
                department_id: '',
                emp_type_id: '',
                designation_id: '',
                hourly_employee: false,
                appointment_date: today,
                joining_date: today,
                confirmation_date: today,
                date_of_birth: '',
                blood_group: 'A+',
                maritus_status: '0',
                gender: 'M',
                religion: 1,
                ref_employee_id: '',
                remarks: '',
            },
            errors: {
                emp_name: false,
                agency_id: false,
                project_id: false,
                sponsor_id: false,
                passfort_no: false,
                passfort_no_msg: '',
                akama_no: false,
                akama_no_msg: '',
                email: false,
                email_msg: '',
                department_id: false,
                country_id: false,
                division_id: false,
                district_id: false,
                emp_type_id: false,
                designation_id: false,
                department_id_msg:"",
                accomd_ofb_id: false,
                mobile_no: false,
            },

            is_searching: 0,
        };
    },
    computed: {
        options() {
            return {
                agencies: this.data.agencies || [],
                sponsors: this.data.sponsors || [],
                countries: this.data.country || [],
                departments: this.data.allDepartment || [],
                empTypes: this.data.empTypes || [],
                designations: this.data.designations,
                projects: this.data.projects || [],
                villas: this.data.accomdOfficeBuilding || [],
            };
        },
    },

    mounted() {
        this.getNewEmployeeID();
    },
    methods: {
        // callback function to handle the event emitted from SalaryUpdateComponent
        updateSalaryCompletedCallBack(isCompleted) {
            console.log('Salary update completed:', isCompleted);
            this.currentStep = 3;
        },
        // callback function to handle the event emitted from FileUpdateComponent
        uploadEmployeeFilesCompletedCallBack(isCompleted) {
            console.log('File upload completed:', isCompleted);
            this.currentStep = 1;
        },
        resetErrors() {
            Object.keys(this.errors).forEach(key => {
                this.errors[key] = (key === 'passfort_no_msg' || key === 'akama_no_msg') ? '' : false;
            });
        },

        validateForm() {
            this.resetErrors();
            let isValid = true;

            if (!this.form.emp_name) { this.errors.emp_name = true; isValid = false; }
            if (!this.form.agency_id) { this.errors.agency_id = true; isValid = false; }
            if (!this.form.sponsor_id) { this.errors.sponsor_id = true; isValid = false; }
            if (!this.form.emp_type_id) { this.errors.emp_type_id = true; isValid = false; }
            if (!this.form.division_id) { this.errors.division_id = true; isValid = false; }
            if (!this.form.district_id) { this.errors.district_id = true; isValid = false; }
            if (!this.form.country_id) { this.errors.country_id = true; isValid = false; }
            if (!this.form.department_id) { this.errors.department_id = true; isValid = false; }
            if (!this.form.project_id) { this.errors.project_id = true; isValid = false; }
            if (!this.form.designation_id) { this.errors.designation_id = true; isValid = false; this.errors.department_id_msg= "You must select this field!" }
            if (!this.form.accomd_ofb_id) { this.errors.accomd_ofb_id = true; isValid = false; }
            if (!this.form.mobile_no) { this.errors.mobile_no = true; isValid = false; }

            // Passport No: required, max 15 characters
            if (!this.form.passfort_no) {
                this.errors.passfort_no = true;
                this.errors.passfort_no_msg = 'This passport number is required!';
                isValid = false;
            } else if (this.form.passfort_no.length > 15) {
                this.errors.passfort_no = true;
                this.errors.passfort_no_msg = 'Passport No must not exceed 15 characters!';
                isValid = false;
            }

            // Iqama No: required, exactly 10 digits
            if (!this.form.akama_no) {
                this.errors.akama_no = true;
                this.errors.akama_no_msg = 'This Iqama number is required!';
                isValid = false;
            } else if (String(this.form.akama_no).length !== 10) {
                this.errors.akama_no = true;
                this.errors.akama_no_msg = 'Iqama number must be exactly 10 digits!';
                isValid = false;
            }

            if (!this.form.employee_id) {
                toast.error('Employee ID was not generated yet. Please reload the page and try again.');
                isValid = false;
            }

            return isValid;
        },

        async submitForm() {
            if (!this.validateForm()) {
                toast.error('Please fill in all the required fields!');
                return;
            }

            // Block submit if a live uniqueness check already flagged a duplicate.
            if (this.errors.passfort_no || this.errors.akama_no) {
                toast.error('Please fix the Passport/Iqama number !');
                return;
            }

            try {
                this.is_submitted = true;
                let formData = new FormData();

                formData.append('company_id', this.form.company_id);
                formData.append('employee_id', this.form.employee_id);
                formData.append('emp_name', this.form.emp_name);
                formData.append('agency_id', this.form.agency_id);
                formData.append('sponsor_id', this.form.sponsor_id);
                formData.append('passfort_no', this.form.passfort_no);
                formData.append('passport_expire_date', this.form.passport_expire_date);
                formData.append('akama_no', this.form.akama_no);
                formData.append('akama_expire', this.form.akama_expire);
                formData.append('accomd_ofb_id', this.form.accomd_ofb_id);
                formData.append('mobile_no', this.form.mobile_no);
                formData.append('phone_no', this.form.phone_no);
                formData.append('country_phone_no', this.form.country_phone_no);
                formData.append('email', this.form.email);
                formData.append('present_address', this.form.present_address);
                formData.append('country_id', this.form.country_id);
                formData.append('division_id', this.form.division_id);
                formData.append('district_id', this.form.district_id);
                formData.append('post_code', this.form.post_code);
                formData.append('details', this.form.details);
                formData.append('project_id', this.form.project_id);
                formData.append('department_id', this.form.department_id);
                formData.append('emp_type_id', this.form.emp_type_id);
                formData.append('designation_id', this.form.designation_id === undefined || this.form.designation_id === 'undefined' ? '' : this.form.designation_id);
                formData.append('hourly_employee', this.form.hourly_employee ? '1' : '0');
                formData.append('appointment_date', this.form.appointment_date);
                formData.append('joining_date', this.form.joining_date);
                formData.append('confirmation_date', this.form.confirmation_date);
                formData.append('date_of_birth', this.form.date_of_birth);
                formData.append('blood_group', this.form.blood_group);
                formData.append('maritus_status', this.form.maritus_status);
                formData.append('gender', this.form.gender);
                formData.append('religion', this.form.religion);
                formData.append('ref_employee_id', this.form.ref_employee_id);
                formData.append('remarks', this.form.remarks);

                const response = await axios.post(NewEmployeeInsertAPI, formData);

                if (response.data.status === 200) {
                    this.new_employee_id = this.form.employee_id;
                    this.resetErrors();
                    toast.success(response.data.message || 'Employee Added Successfully!');
                    this.currentStep = 2;
                } else {
                    toast.error(response.data.error ? (response.data.error + ' ' + response.data.message) : ('Insert Failed: ' + response.data.message));
                }

            } catch (error) {
                console.error(error);
                if (error.response && error.response.status === 422 && error.response.data.errors) {
                    const firstError = Object.values(error.response.data.errors)[0][0];
                    toast.error(firstError);
                } else {
                    toast.error('Error occurred, Please Try Again: ' + error.message);
                }
            } finally {
                this.is_submitted = false;
            }
        },

        async checkEmployeeId() {
            await this.checkingThisEmployeeEmpIdPassportIqamaAndEmail(this.form.employee_id, 'employee_id');
        },


        async checkThisEmployeePassportNumber() {
            const value = this.form.passfort_no;

            if (!value || value.length === 0) {
                this.errors.passfort_no = false;
                this.errors.passfort_no_msg = '';
                return;
            }

            if (value.length < 4) {
                this.errors.passfort_no = true;
                this.errors.passfort_no_msg = 'Passport No must be at least 4 digits.';
                return;
            }

            this.errors.passfort_no = false;
            this.errors.passfort_no_msg = '';
            await this.checkingThisEmployeeEmpIdPassportIqamaAndEmail(value, 'passfort_no');
        },


        async checkThisEmployeeIqamaNumber() {
            const value = this.form.akama_no;

            if (!value || value.length === 0) {
                this.errors.akama_no = false;
                this.errors.akama_no_msg = '';
                return;
            }

            if (value.length !== 10) {
                this.errors.akama_no = true;
                this.errors.akama_no_msg = 'Iqama number must be exactly 10 digits.';
                return;
            }

            this.errors.akama_no = false;
            this.errors.akama_no_msg = '';
            await this.checkingThisEmployeeEmpIdPassportIqamaAndEmail(value, 'akama_no');
        },

        async checkThisEmployeeEmail() {
            const value = this.form.email;

            if (!value || value.length === 0) {
                this.errors.email = false;
                this.errors.email_msg = '';
                return;
            }

            // Basic email format check before hitting the API
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(value)) {
                this.errors.email = true;
                this.errors.email_msg = 'Please enter a valid email address.';
                return;
            }

            this.errors.email = false;
            this.errors.email_msg = '';
            await this.checkingThisEmployeeEmpIdPassportIqamaAndEmail(value, 'email');
        },

        async checkingThisEmployeeEmpIdPassportIqamaAndEmail(value, key) {
            const requestData = {
                value: value,
                dbcolum_name: key
            };

            this.is_searching = 1;

            try {
                const response = await axios.post(EmployeeCheckUniqueIDAPI, requestData);
                // console.log(`${key} check response:`, response.data);

                const alreadyExists = !!(response.data && response.data.data === 1);

                this.errors[key] = alreadyExists;

                if (key === 'passfort_no') {
                    this.errors.passfort_no_msg = alreadyExists ? 'This Passport Number Already Exist!' : '';
                } else if (key === 'akama_no') {
                    this.errors.akama_no_msg = alreadyExists ? 'This Iqama Number Already Exist!' : '';
                } else if (key === 'email') {
                    this.errors.email_msg = alreadyExists ? 'This Email Already Exist!' : '';
                }

            } catch (error) {
                console.error("Error:", error);
                toast.error('Data Checking Operation Failed, Please check Your Internet Connection');
            } finally {
                this.is_searching = 0;
            }
        },

        async getNewEmployeeID() {
            try {
                const formdata = new FormData();
                formdata.append('new_emp_type', this.form.company_id)
                const response = await axios.get(`${NewEmployeeIDAPI}` + `${this.form.company_id}`);
                if (response.data.status == 200) {
                    this.new_employee_id = response.data.data;
                    this.form.employee_id = response.data.data;
                    console.log('New Employee ID generated:', this.new_employee_id);
                }
            } catch (error) {
                console.error(error);
                toast.error('Failed to generate Employee ID, please reload the page.');
            }
        },

        async searchDivisionsByCountry() {
            this.form.division_id = '';
            this.form.district_id = '';
            this.districts = [];
            if (!this.form.country_id) { this.divisions = []; return; }
            const response = await axios.get(DivisionSearchByAPI + '/' + this.form.country_id);
            this.divisions = response.data;
        },
        async searchDistrictsByDivision() {
            this.form.district_id = '';
            if (!this.form.division_id) { this.districts = []; return; }
            const response = await axios.get(DistrictSearchByAPI + '/' + this.form.division_id);
            this.districts = response.data;
        },
    }
};
</script>

<style scoped>
.col-form-label {
    font-weight: 600;
    color: #495057;
    padding-top: 0.375rem;
    padding-bottom: 0.375rem;
}

.form-group {
    margin-bottom: 8px;
}

.has-error .form-control,
.has-error .form-select {
    border-color: #dc3545;
}

.card-body>.row>.form-group:last-child {
    margin-bottom: 0;
}

@media (min-width: 576px) {
    .col-form-label {
        text-align: right;
        padding-right: 10px;
    }
}
</style>
