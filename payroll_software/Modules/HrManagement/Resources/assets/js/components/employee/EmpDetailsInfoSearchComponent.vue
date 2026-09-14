<template>
  <div class="row">

    <!-- ===== Search Bar ===== -->
    <div class="col-md-12">
      <div class="card search-card">
        <div class="row form-group align-items-center mb-0">
          <label class="col-md-4 control-label text-right">Employee Searching by</label>
          <div class="col-md-3">
            <select v-model="searchBy" class="form-select">
              <option value="employee_id">Employee ID</option>
              <option value="akama_no">Iqama Number</option>
              <option value="passfort_no">Passport</option>
              <option value="1">Master Searching</option>
            </select>
          </div>
          <div class="col-md-4">
            <form @submit.prevent="searchEmployee" class="d-flex">
              <input type="text" v-model="searchInpute" ref="input_employee_id"
                placeholder="Enter ID / Iqama / Passport No" class="form-control" required
                @keyup.enter="searchEmployee" />
              <button type="submit" class="btn btn-primary ms-2 px-4">
                <i class="fas fa-search me-1"></i> SEARCH
              </button>
            </form>
          </div>
        </div>
        <div v-if="is_searching == 1" class="loading-overlay">
          <div class="spinner-border text-primary"></div>
        </div>
      </div>
    </div>

    <!-- ===== Multiple Employee Result (Master Searching) ===== -->
    <div class="col-lg-12" v-if="is_searching == 3">
      <div class="card mt-3">
        <div class="section-label">
          <i class="fas fa-list me-2"></i>Master Searching Result ({{ employeeList.length }} found)
        </div>
        <div class="table-responsive">
          <table class="table table-bordered table-hover mb-0 m-table">
            <thead>
              <tr>
                <th>S.N</th>
                <th>Emp. ID</th>
                <th>Name</th>
                <th>Iqama</th>
                <th>Passport</th>
                <th>Project</th>
                <th>Sponsor</th>
                <th>Salary</th>
                <th>Job Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(emp, index) in employeeList" :key="emp.emp_auto_id || index">
                <td>{{ index + 1 }}</td>
                <td>{{ emp.employee_id }}</td>
                <td>{{ emp.employee_name }}</td>
                <td>{{ emp.akama_no }}<br><small>{{ emp.akama_expire_date }}</small></td>
                <td>{{ emp.passfort_no }}<br><small>{{ emp.passfort_expire_date }}</small></td>
                <td>{{ emp.proj_name || '-' }}</td>
                <td>{{ emp.spons_name || '-' }}</td>
                <td>{{ emp.hourly_rent == true || emp.hourly_rent == 1 ? 'Hourly' : 'Basic Salary' }}</td>
                <td>{{ emp.title || '-' }}</td>
                <td>
                  <button class="btn btn-sm btn-primary" @click="selectEmployeeFromList(emp)">
                    <i class="fas fa-eye"></i> View
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ===== Result Card ===== -->
    <div class="col-lg-12" v-if="is_searching == 2">
      <div class="card profile-card bg-primary mt-1">

        <!-- Card Header -->

        <div class="card-body p-0">

          <!-- ══════════════════════════════════════════
               SECTION 1 — PRIORITY INFO + PHOTO
               Layout: | th(20%) | td(30%) | th(20%) | td(30-photo%) | photo(160px fixed) |
               Using pixel-based photo col so remaining 4 cols share equally
          ══════════════════════════════════════════ -->
          <div class="section-label">
            <i class="fas fa-id-badge me-2"></i>Personal &amp; Identity
          </div>

          <div class="table-responsive">
            <table class="table table-bordered p-table mb-0">
              <colgroup>
                <col style="width:20%">
                <col style="width:30%">
                <col style="width:15%">
                <col style="width:23%">
                <col style="width:12%">
              </colgroup>
              <tbody>
                <tr>
                  <th>Employee ID</th>
                  <td style="color:red; font-weight: bold; font-size: 18px;">{{ form.employee_id }}</td>
                  <th>Full Name</th>
                  <td>{{ form.emp_name || '-' }}</td>
                  <td rowspan="4" class="photo-cell">
                    <div class="passport-frame">
                      <img :src="getAttachmentUrl(employeeData.profile_photo)" alt="Not Found" class="passport-photo" />
                      <!-- <div class="passport-overlay">
                        <i class="fas fa-eye"></i>
                        <span>Preview</span>
                      </div> -->
                    </div>
                  </td>
                </tr>
                <tr>
                  <th>Iqama No</th>
                  <td style="color:red; font-weight: bold; font-size:14px;">{{ this.employeeData.akama_no }}, {{
                    this.employeeData.akama_expire }}

                    <a v-if="this.employeeData.akama_photo" :href="getAttachmentUrl(this.employeeData.akama_photo)"
                      target="_blank">
                      <i class="fas fa-eye fa-lg view_icon"></i>
                    </a>
                  </td>
                  <th>Job Status</th>
                  <th style="color:red; font-weight: bold; font-size: 18px;">
                    {{ (this.employeeData.job_status > 0 ? this.employeeData.title : "Waiting for Approval") }} , {{
                      this.employeeData.isNightShift == 0 ? "Day Shift" : "Night Shift" }}
                  </th>
                </tr>
                <tr>
                  <th>Project</th>
                  <td style="color:blue; font-weight: bold; font-size: 14px;">{{ this.employeeData.proj_name || '-' }}
                  </td>


                  <th>Salary</th>
                  <td style="color:red; font-weight: bold; font-size: 18px;"> {{ this.employeeData.hourly_employee == 1
                    ? "Hourly" : "Basic" }} {{ (this.employeeData.salary_status == 1 ? ', Active' : ", Hold") }}</td>

                </tr>
                <tr>
                  <th>Abshar Mobile</th>
                  <td>{{ this.employeeData.mobile_no || '-' }}</td>
                  <th>Blood Group</th>
                  <td>
                    <span v-if="this.employeeData.blood_group" class="badge-blood">{{ this.employeeData.blood_group
                    }}</span>
                    <span v-else>-</span>
                    {{ this.employeeData.emp_type_id == 1 ? ", Direct Employee" : ", Indirect Employee" }}
                  </td>
                </tr>
                <tr>
                  <th>Designation</th>
                  <td style="color:red; font-weight: bold; font-size:14px;">{{ this.employeeData.catg_name || '-' }}
                  </td>

                  <th>Passport No</th>
                  <td colspan="2">
                    {{ this.employeeData.passfort_no || '-' }}, {{ this.employeeData.passfort_expire_date || '-' }}
                    <a v-if="this.employeeData.pasfort_photo" :href="getAttachmentUrl(this.employeeData.pasfort_photo)"
                      target="_blank">
                      <i class="fas fa-eye fa-lg view_icon"></i>
                    </a>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- ══════════════════════════════════════════
               SECTION 2 — ADDITIONAL DETAILS
               Exact same 4-col widths as priority (no photo col)
               th=20%  td=30%  th=20%  td=30%  → total 100%
          ══════════════════════════════════════════ -->
          <div class="section-label section-label-alt">
            <i class="fas fa-info-circle me-2"></i>Additional Details
          </div>

          <div class="table-responsive">
            <table class="table table-bordered s-table mb-0">
              <colgroup>
                <col style="width:20%">
                <col style="width:30%">
                <col style="width:20%">
                <col style="width:30%">
              </colgroup>
              <tbody>

                <tr>
                  <th>Sponsor</th>
                  <td style="color:purple; font-weight: bold; font-size: 14px;">{{ this.employeeData.spons_name || '-'
                  }}</td>
                  <th>Agency</th>
                  <td style="color:orange; font-weight: bold; font-size: 14px;">{{ this.employeeData.agc_title || '-' }}
                  </td>
                </tr>
                <tr>
                  <th>Email</th>
                  <td>{{ this.employeeData.email || '-' }}</td>
                  <th>Department</th>
                  <td>{{ this.employeeData.dep_name || '-' }}</td>
                </tr>

                <tr>
                  <th>Present Address</th>
                  <td>{{ this.employeeData.present_address || '-' }} <br> Villa:{{ this.employeeData.ofb_name || '-' }}
                  </td>
                  <th>Permanent Address</th>
                  <td>
                    {{ [this.employeeData.details, this.employeeData.post_code, this.employeeData.district_name,
                    this.employeeData.division_name, this.employeeData.country_name,
                    ].filter(Boolean).join(', ') || '-' }}
                  </td>
                </tr>

                <tr>
                  <th>Mobile No 2</th>
                  <td>{{ this.employeeData.phone_no || '-' }}</td>
                  <th>Home Contact</th>
                  <td>{{ this.employeeData.country_phone_no || '-' }}</td>
                </tr>
                <tr>
                  <th>- </th>
                  <td>- </td>
                  <th>Remarks</th>
                  <td>{{ this.employeeData.remarks || '-' }}</td>
                </tr>

                <tr>
                  <th>Marital Status</th>
                  <td>{{ this.employeeData.maritus_status == '1' ? 'Married' : 'Unmarried' }}</td>
                  <th>Gender</th>
                  <td>Male</td>
                  <!-- <td>{{ this.employeeData.gender == 'M' ? 'Male' : 'Female' }}</td> -->
                </tr>
                <tr>
                  <th>Religion</th>
                  <td>{{ religionName || '-' }}</td>
                  <th>Ref. Employee</th>
                  <td>{{ this.employeeData.ref_employee_id || '-' }}</td>
                </tr>
                <tr class="date-row">
                  <th>Appointment Date</th>
                  <td>{{ this.employeeData.appointment_date || '-' }}</td>
                  <th>Joining Date</th>
                  <td>{{ this.employeeData.joining_date || '-' }}</td>
                </tr>

                <tr class="date-row">
                  <th>Date of Birth</th>
                  <td>{{ this.employeeData.date_of_birth || '-' }}</td>
                  <th></th>
                  <td>{{ this.employeeData.emp_auto_id }}</td>
                </tr>



              </tbody>
            </table>
          </div>

          <!-- ══════════════════════════════════════════
               SECTION 3 — DOCUMENTS
          ══════════════════════════════════════════ -->
          <div class="section-label section-label-docs">
            <i class="fas fa-folder-open me-2"></i>Documents
          </div>

          <div class="documents-section">
            <a v-if="this.employeeData.akama_photo" :href="getAttachmentUrl(this.employeeData.akama_photo)"
              target="_blank" class="doc-btn doc-iqama">
              <i class="fas fa-id-card"></i> <i class="fas fa-eye fa-lg view_icon"></i> <span>Iqama </span>
            </a>

            <a v-if="this.employeeData.pasfort_photo" :href="getAttachmentUrl(this.employeeData.pasfort_photo)"
              target="_blank" class="doc-btn doc-passport">
              <i class="fas fa-passport"></i><span>Passport</span>
            </a>
            <a v-if="this.employeeData.medical_report" :href="getAttachmentUrl(this.employeeData.medical_report)"
              target="_blank" class="doc-btn doc-passport">
              <i class="fas fa-passport"></i><span>Medical Report</span>
            </a>
            <a v-if="this.employeeData.covid_certificate" :href="getAttachmentUrl(this.employeeData.covid_certificate)"
              target="_blank" class="doc-btn doc-passport">
              <i class="fas fa-passport"></i><span>Covid Certificate</span>
            </a>
            <!-- <a v-if="this.employeeData.blood_group_paper" :href="getAttachmentUrl(this.employeeData.blood_group_paper)"
              target="_blank" class="doc-btn doc-passport">
              <i class="fas fa-passport"></i><span>blood_group_paper </span>
            </a>
            <a v-if="this.employeeData.educational_papers" :href="getAttachmentUrl(this.employeeData.educational_papers)"
              target="_blank" class="doc-btn doc-passport">
              <i class="fas fa-passport"></i><span>educational_papers </span>
            </a> -->
            <a v-if="this.employeeData.employee_appoint_latter"
              :href="getAttachmentUrl(this.employeeData.employee_appoint_latter)" target="_blank"
              class="doc-btn doc-agreement">
              <i class="fas fa-file-contract"></i><span>Agreement</span>
            </a>
            <a v-if="this.employeeData.ajeer_file" :href="getAttachmentUrl(this.employeeData.azeer_file)"
              target="_blank" class="doc-btn doc-agreement">
              <i class="fas fa-file-contract"></i><span>AZEER</span>
            </a>

          </div>

        </div>
      </div>
    </div>

  </div>
</template>

<script>
import axios from 'axios';
import { inject } from 'vue';
import { toast } from 'vue3-toastify';
import { EmployeeSearchAPIForInfoEdit } from "../../routes.js";
import { useAuth } from "../../../../../../../resources/js/components/useAuth.js";
const auth = inject('auth');

export default {
  name: 'EmployeeInformationView',
  data() {
    return {
      searchBy: 'employee_id',
      searchInpute: '',
      employeeData: null,
      employeeList: [],
      // 0 = idle, 1 = loading, 2 = single result, 3 = multiple results (master searching)
      is_searching: 0,
      sponsors: [],
      departments: [],
      projects: [],
      designations: [],
      employeeTypes: [],
      agencies: [],
      countries: [],
      divisions: [],
      districts: [],
      religions: [
        { relig_id: 1, relig_name: 'Muslim' },
        { relig_id: 2, relig_name: 'Hinduism' },
        { relig_id: 3, relig_name: 'Christianity' }
      ],
      form: {
        emp_auto_id: '', employee_id: '', emp_name: '', agency_id: '',
        sponsor_id: '', passport_expire_date: '',
        akama_no: '', akama_expire: '', mobile_no: '', phone_no: '',
        country_phone_no: '', email: '', present_address: '',
        country_id: '', division_id: '', district_id: '', post_code: '',
        details: '', emp_type_id: '', designation_id: '', project_id: '',
        department_id: '', date_of_birth: '', blood_group: '',
        maritus_status: '1', gender: '1', religion: '', ref_employee_id: '',
        remarks: '', appointment_date: '', joining_date: '', confirmation_date: '',
        akama_photo: '',
        ajeer_file: '',
        medical_report: '',
        appoint_letter: '',
        covid_certificate: '',
        medical_report: '',
        educational_papers: '',
        blood_group_paper: '',
        passfort_no: '',
        profile_photo: '',
      }
    };
  },
  computed: {
    designationName() { const d = this.designations.find(x => x.catg_id == this.form.designation_id); return d ? d.catg_name : ''; },
    projectName() { const p = this.projects.find(x => x.proj_id == this.form.project_id); return p ? p.proj_name : ''; },
    sponsorName() { const s = this.sponsors.find(x => x.spons_id == this.form.sponsor_id); return s ? s.spons_name : ''; },
    agencyName() { const a = this.agencies.find(x => x.agc_info_auto_id == this.form.agency_id); return a ? a.agc_title : ''; },
    departmentName() { const d = this.departments.find(x => x.dep_id == this.form.department_id); return d ? d.dep_name : ''; },
    religionName() { const r = this.religions.find(x => x.relig_id == this.form.religion); return r ? r.relig_name : ''; },
    countryName() { const c = this.countries.find(x => x.id == this.form.country_id); return c ? c.country_name : ''; },
    divisionName() { const d = this.divisions.find(x => x.division_id == this.form.division_id); return d ? d.division_name : ''; },
    districtName() { const d = this.districts.find(x => x.district_id == this.form.district_id); return d ? d.district_name : ''; }
  },
  mounted() { this.$refs.input_employee_id.focus(); },
  methods: {
    setFormFromEmployeeData() {
      if (!this.employeeData) return;
      this.form = {
        emp_auto_id: this.employeeData.emp_auto_id || '',
        employee_id: this.employeeData.employee_id || '',
        emp_name: this.employeeData.employee_name || '',
        agency_id: this.employeeData.agc_info_auto_id || '',
        sponsor_id: this.employeeData.sponsor_id || '',
        passfort_no: this.employeeData.passfort_no || '',
        passport_expire_date: this.employeeData.passfort_expire_date || '',
        akama_no: this.employeeData.akama_no || '',
        akama_expire: this.employeeData.akama_expire_date || '',
        mobile_no: this.employeeData.mobile_no || '',
        phone_no: this.employeeData.phone_no || '',
        country_phone_no: this.employeeData.country_phone_no || '',
        email: this.employeeData.email || '',
        present_address: this.employeeData.present_address || '',
        country_id: this.employeeData.country_id || '',
        division_id: this.employeeData.division_id || '',
        district_id: this.employeeData.district_id || '',
        post_code: this.employeeData.post_code || '',
        details: this.employeeData.details || '',
        emp_type_id: this.employeeData.emp_type_id || '',
        designation_id: this.employeeData.designation_id || '',
        project_id: this.employeeData.project_id || '',
        department_id: this.employeeData.department_id || '',
        date_of_birth: this.employeeData.date_of_birth || '',
        blood_group: this.employeeData.blood_group || '',
        maritus_status: this.employeeData.is_married ? this.employeeData.is_married.toString() : '1',
        gender: this.employeeData.gender === 'F' ? '2' : '1',
        religion: this.employeeData.religion_id || '',
        ref_employee_id: this.employeeData.ref_employee_id || '',
        remarks: this.employeeData.remarks || '',
        appointment_date: this.employeeData.appointment_date || '',
        joining_date: this.employeeData.joining_date || '',
        confirmation_date: this.employeeData.confirmation_date || '',


        akama_photo: this.employeeData.akama_photo || '',
        passfort_file: this.employeeData.pasfort_photo || '',
        ajeer_file: this.employeeData.employee_appoint_latter || '',
        medical_report: this.employeeData.medical_report || '',
        covid_certificate: this.employeeData.covid_certificate || '',
        appoint_letter: this.employeeData.appoint_letter || '',
        appoint_blood_group_paperletter: this.employeeData.blood_group_paper || '',
        educational_papers: this.employeeData.educational_papers || '',

      };
    },
    async searchEmployee() {
      if (!this.searchInpute) { toast.error("Employee ID/Iqama/Passport is required!"); return; }
      if (this.searchInpute.length <= 1) { toast.error("Invalid Input!"); return; }
      this.is_searching = 1;
      try {
        const response = await axios.get(EmployeeSearchAPIForInfoEdit, {
          params: { search_by: this.searchBy, employee_searching_value: this.searchInpute }
        });
        if (response.data.success === false) {
          this.is_searching = 0;
          toast.error(response.data.message || "Employee not found");
          return;
        }
        const data = response.data;

        this.sponsors = data.sponsors || [];
        this.departments = data.departments || [];
        this.projects = data.projects || [];
        this.designations = data.designations || [];
        this.employeeTypes = data.employeeTypes || [];
        this.agencies = data.agencies || [];
        this.countries = data.countries || [];
        this.divisions = data.divisions || [];
        this.districts = data.districts || [];

        const findEmployee = data.findEmployee || [];

        // Master Searching (searchBy == '1') can return multiple employees.
        // Show the list table if more than one record is found.
        if (this.searchBy == '1' && findEmployee.length > 1) {
          this.employeeList = findEmployee;
          this.employeeData = null;
          this.is_searching = 3;
          return;
        }

        if (findEmployee.length === 0) {
          this.is_searching = 0;
          toast.error("Employee not found");
          return;
        }

        this.employeeData = findEmployee[0];
        this.setFormFromEmployeeData();
        this.is_searching = 2;
      } catch (error) {
        this.is_searching = 0;
        toast.error(error.response?.data?.message || "Operation Failed");
        console.error("Error:", error);
      }
    },

    // When user clicks "View" on a row from the Master Searching result list
    selectEmployeeFromList(emp) {
      this.employeeData = emp;
      this.setFormFromEmployeeData();
      this.is_searching = 2;
    },


    getAttachmentUrl(path) {
      return (import.meta.env.VITE_AWS_S3_ENDPOINT + path);
    },

  }
};
</script>

<style scoped>
/* ─── Search Card ─────────────────────────────── */
.search-card {
  padding: 16px 20px;
  border: 1px solid #dee2e6;
  border-radius: 8px;
  background: #fff;
}

.control-label {
  font-weight: 600;
  padding-top: 8px;
  color: #495057;
}

.loading-overlay {
  display: flex;
  justify-content: center;
  padding: 16px 0 4px;
}

/* ─── Profile Card ────────────────────────────── */
.profile-card {
  border: 1px solid #dee2e6;
  border-radius: 10px;
  overflow: hidden;
  box-shadow: 0 2px 14px rgba(0, 0, 0, 0.08);
}

/* ─── Card Header ─────────────────────────────── */
.profile-header {
  display: flex;
  align-items: center;
  color: #fff;
  padding: 13px 20px;
  font-size: 15px;
  font-weight: 700;
}

.emp-id-badge {
  font-size: 12px;
  font-weight: 600;
  background: rgba(255, 255, 255, 0.18);
  border: 1px solid rgba(255, 255, 255, 0.35);
  border-radius: 20px;
  padding: 3px 12px;
}

/* ─── Section Labels ──────────────────────────── */
.section-label {
  display: flex;
  align-items: center;
  padding: 6px 16px;
  font-size: 11.5px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.7px;
  background: #eff4ff;
  border-top: 1px solid #dbe4ff;
  border-bottom: 1px solid #dbe4ff;
  color: #1a56db;
}

.section-label-alt {
  background: #f0fff4;
  border-color: #bbf7d0;
  color: #15803d;
}

.section-label-docs {
  background: #fefce8;
  border-color: #fde68a;
  color: #92400e;
}

/* ═══════════════════════════════════════════════
   SHARED TABLE BASE
   Both .p-table and .s-table inherit these rules.
   table-layout:fixed is the key — it forces the
   browser to honour the colgroup widths exactly.
═══════════════════════════════════════════════ */
.p-table,
.s-table {
  table-layout: fixed;
  width: 100%;
  border-collapse: collapse;
  margin: 0;
}

/* ── th / td shared ── */
.p-table th,
.p-table td,
.s-table th,
.s-table td {
  padding: 9px 14px;
  font-size: 13px;
  vertical-align: middle;
  border-color: #e2e8f0 !important;
  overflow: hidden;
  text-overflow: ellipsis;
}

.p-table th,
.s-table th {
  background: #f8fafc;
  font-weight: 600;
  color: #374151;
  white-space: nowrap;
}

.p-table td,
.s-table td {
  background: #ffffff;
  color: #1f2937;
}

/* Zebra stripe — even rows */
.p-table tbody tr:nth-child(even) th,
.s-table tbody tr:nth-child(even) th {
  background: #f1f5f9;
}

.p-table tbody tr:nth-child(even) td,
.s-table tbody tr:nth-child(even) td {
  background: #f9fafb;
}

/* ── Date row — compact ── */
.s-table tr.date-row th,
.s-table tr.date-row td {
  padding-top: 5px;
  padding-bottom: 5px;
  font-size: 12.5px;
  color: #6b7280;
}

.s-table tr.date-row th {
  color: #374151;
}

/* ─── Blood Group Badge ───────────────────────── */
.badge-blood {
  display: inline-block;
  background: #dc2626;
  color: #fff;
  font-weight: 700;
  font-size: 11.5px;
  border-radius: 4px;
  padding: 2px 10px;
  letter-spacing: 0.5px;
}

/* ─── Photo Cell (rowspan 5) ──────────────────── */
.photo-cell {
  padding: 12px !important;
  background: #f8fafc !important;
  text-align: center;
  vertical-align: top !important;
  border-left: 2px solid #dbe4ff !important;
}

.passport-frame {
  position: relative;
  width: 110px;
  height: 140px;
  margin: 0 auto;
  border: 3px solid #1a56db;
  border-radius: 5px;
  overflow: hidden;
  box-shadow: 0 3px 12px rgba(26, 86, 219, 0.20);
  cursor: pointer;
}

.passport-photo {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
  transition: transform 0.2s ease;
}

.passport-overlay {
  position: absolute;
  inset: 0;
  background: rgba(26, 86, 219, 0.58);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 5px;
  opacity: 0;
  transition: opacity 0.2s ease;
  color: #fff;
  font-size: 12px;
  font-weight: 700;
}

.passport-overlay i {
  font-size: 22px;
}

.passport-frame:hover .passport-overlay {
  opacity: 1;
}

.passport-frame:hover .passport-photo {
  transform: scale(1.04);
}

.passport-name {
  margin-top: 7px;
  font-size: 11px;
  font-weight: 600;
  color: #374151;
  text-align: center;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  max-width: 120px;
  margin-left: auto;
  margin-right: auto;
}

/* ─── Documents Section ───────────────────────── */
.documents-section {
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
  padding: 14px 18px 16px;
  background: #fff;
}

.doc-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 7px 18px;
  border-radius: 6px;
  font-size: 13px;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.18s ease;
  border: 1.5px solid;
}

.doc-iqama {
  color: #1d4ed8;
  border-color: #1d4ed8;
  background: #eff6ff;
}

.doc-passport {
  color: #b45309;
  border-color: #d97706;
  background: #fffbeb;
}

.doc-agreement {
  color: #15803d;
  border-color: #16a34a;
  background: #f0fdf4;
}

.doc-iqama:hover {
  background: #1d4ed8;
  color: #fff;
}

.doc-passport:hover {
  background: #d97706;
  color: #fff;
}

.doc-agreement:hover {
  background: #16a34a;
  color: #fff;
}

/* ─── Master Searching table ──────────────────── */
.m-table th,
.m-table td {
  font-size: 13px;
  vertical-align: middle;
}

/* ─── Responsive ──────────────────────────────── */
@media (max-width: 768px) {
  .photo-cell {
    display: none;
  }
}
</style>
