<template>
    <div class="container">
        <div class="card">
            <div class="card-body">
                <!-- <h5>Add Subcontractor</h5> -->
                <form @submit.prevent="submitForm">
                    <!-- Row 1 -->
                    <div class="row mb-1">
                        <div class="col-md-6 d-flex align-items-center">
                            <label class="col-md-4 text-right">Sponsor Name</label>
                            <select v-model="form.sponsor_id" class="form-select col-md-8"
                                @change="changeSponsorDropdown" required>
                                <option value="" disabled>Select...</option>
                                <option v-for="sponsor in data_for_form.sub_con_sponsors" :key="sponsor.spons_id"
                                    :value="sponsor.spons_id">
                                    {{ sponsor.spons_name }}
                                </option>
                            </select>
                        </div>
                        <!-- <div class="col-md-6 d-flex align-items-center">
                            <label class="col-md-4 text-right">Opening Balance</label>
                            <input type="text" v-model="form.opening_balance" class="form-control col-md-8" required />
                        </div> -->

                        <div class="col-md-6 d-flex align-items-center">
                            <label class="col-md-4 text-right">ID Number</label>
                            <input type="text" v-model="form.id_number" class="form-control col-md-8" required />
                        </div>
                    </div>

                    <!-- Row 1 -->
                    <div class="row mb-1">
                        <div class="col-md-6 d-flex align-items-center">
                            <label class="col-md-4 text-right">Subcontractor Name</label>
                            <input type="text" v-model="form.subcon_name" class="form-control col-md-8" required />
                        </div>
                        <div class="col-md-6 d-flex align-items-center">
                            <label class="col-md-4 text-right">Passport No</label>
                            <input type="text" v-model="form.passfort_no" class="form-control col-md-8" required />
                        </div>
                    </div>

                    <!-- Row 2 -->
                    <div class="row mb-1">
                        <div class="col-md-6 d-flex align-items-center">
                            <label class="col-md-4 text-right">Iqama No</label>
                            <input type="text" v-model="form.iqama_no" class="form-control col-md-8" required />
                        </div>
                        <div class="col-md-6 d-flex align-items-center">
                            <label class="col-md-4 text-right">Expiry Date</label>
                            <input type="date" v-model="form.pass_expire" class="form-control col-md-8" required />
                        </div>
                    </div>

                    <!-- Row 3 -->
                    <div class="row mb-1">
                        <div class="col-md-6 d-flex align-items-center">
                            <label class="col-md-4 text-right">Expiry Date</label>
                            <input type="date" v-model="form.iqama_expire" class="form-control col-md-8" required />
                        </div>
                        <div class="col-md-6 d-flex align-items-center">
                            <label class="col-md-4 text-right">Mobile Number</label>
                            <input type="text" v-model="form.mobile_no" class="form-control col-md-8" required />
                        </div>
                    </div>

                    <!-- Row 4 -->
                    <div class="row mb-1">
                        <div class="col-md-6 d-flex align-items-center">
                            <label class="col-md-4 text-right">Abshar Mobile No.</label>
                            <input type="text" v-model="form.abshar_mobile_no" class="form-control col-md-8" />
                        </div>
                        <div class="col-md-6 d-flex align-items-center">
                            <label class="col-md-4 text-right">Country Contact No.</label>
                            <input type="text" v-model="form.country_contact_no" class="form-control col-md-8" />
                        </div>
                    </div>

                    <!-- Row 5 -->
                    <div class="row mb-1">
                        <div class="col-md-6 d-flex align-items-center">
                            <label class="col-md-4 text-right">Country</label>
                            <select v-model="form.country_id" class="form-select" @change="divisionFilter()" required>
                                <option value="" disabled>Select...</option>
                                <option v-for="country in data_for_form.countries" :key="country.id"
                                    :value="country.id">
                                    {{ country.country_name }}
                                </option>
                            </select>
                        </div>
                        <div class="col-md-6 d-flex align-items-center">
                            <label class="col-md-4 text-right">Division</label>
                            <!-- <select v-model="form.division_id" id="division_id" class="form-select" required>
                                <option value="" disabled>Select...</option>
                                <option v-for="dv in data_for_form.divisions" :key="dv.division_id"
                                    :value="dv.division_id">
                                    {{ dv.division_name }}
                                </option>
                            </select> -->

                            <select v-model="form.division_id" id="division_id" class="form-select" required
                                :disabled="loadingDivisions">
                                <option value="" disabled>Select...</option>
                                <option v-if="loadingDivisions" disabled>Loading divisions...</option>
                                <option v-else v-for="dv in data_for_form.divisions" :key="dv.division_id"
                                    :value="dv.division_id">
                                    {{ dv.division_name }}
                                </option>
                            </select>
                        </div>

                    </div>

                    <!-- Row 6 -->
                    <div class="row mb-1">
                        <div class="col-md-6 d-flex align-items-center">
                            <label class="col-md-4 text-right">Details</label>
                            <textarea v-model="form.details" class="form-control col-md-8"></textarea>
                        </div>

                        <div class="col-md-6 d-flex align-items-center">
                            <label class="col-md-4 text-right">Opening Balance</label>
                            <input type="text" v-model="form.opening_balance" class="form-control col-md-8" required />
                        </div>
                    </div>

                    <!-- Row 7 -->
                    <div class="row mb-1">
                        <div class="col-md-6 d-flex align-items-center">
                            <label class="col-md-4 text-right">Joining Date</label>
                            <input type="date" v-model="form.joining_date" class="form-control col-md-8" />
                        </div>
                        <div class="col-md-6 d-flex align-items-center">
                            <label class="col-md-4 text-right">Contract File</label>
                            <input type="file" @change="handleFileUpload('contract_paper', $event)"
                                class="form-control col-md-6" />

                        </div>
                    </div>

                    <!-- Row 8 -->
                    <div class="row mb-1">
                        <div class="col-md-6 d-flex align-items-center">
                            <label class="col-md-4 text-right">Iqama File</label>
                            <input type="file" @change="handleFileUpload('iqama_file', $event)"
                                class="form-control col-md-8" />
                        </div>
                        <div class="col-md-6 d-flex align-items-center">
                            <label class="col-md-4 text-right">Passport File</label>
                            <input type="file" @change="handleFileUpload('passport_file', $event)"
                                class="form-control col-md-8" />
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <button type="submit" :disabled="isSaveBtnClicked" id="sc_save_btn"
                                v-if="hasPermission('add_new_subcontractor')" class="btn btn-primary mt-3">Save</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import { toast } from 'vue3-toastify';
// import Multiselect from '@vueform/multiselect';
import "@vueform/multiselect/themes/default.css";
import { StoreSubContractor, GetDivisionsByCountry } from "../routes.js";

import { inject } from 'vue';
import { useAuth } from "../../../../../../resources/js/components/useAuth.js";

const auth = inject('auth')

export default {
    setup() {
        const { hasPermission } = useAuth();

        return {
            hasPermission,
        };
    },
    data() {
        return {
            form: {
                sponsor_id: "",
                subcon_name: "",
                passfort_no: "",
                id_number: "",
                pass_expire: "",
                iqama_no: "",
                iqama_expire: "", // Format: YYYY-MM-DD
                mobile_no: "",
                abshar_mobile_no: "",
                country_contact_no: "",
                country_id: 1,
                division_id: 13,
                details: "",
                joining_date: this.getCurrentDate(),
                opening_balance: 0,
                entry_date: "",
                iqama_file: null,
                passport_file: null,
                contract_paper: null,
            },
            isSaveBtnClicked: false,
            loadingDivisions: false,

        };
    },
    // props: ["countries"], // Receives data from Blade
    props: { // Receives data from Blade
        data_for_form: {
            type: Object,
            required: true
        }
    },
    mounted() {
        // Set the current date when the component loads
        this.form.pass_expire = this.getCurrentDate();
        this.form.iqama_expire = this.getCurrentDate();
        this.form.joining_date = this.getCurrentDate();
    },
    methods: {
        getCurrentDate() {
            const today = new Date();
            return today.toISOString().split("T")[0]; // Format: YYYY-MM-DD
        },

        handleFileUpload(field, event) {
            this.form[field] = event.target.files[0];
        },

        async submitForm() {
            this.isSaveBtnClicked = true;
            let formData = new FormData();
            for (let key in this.form) {
                formData.append(key, this.form[key]);
            }
            // debugger;
            //  console.log(this.form.id_number);
            //  formData.append('id_number',10);

            try {
                const response = await axios.post(StoreSubContractor, formData);
                console.log(response);
                if (response.status == 201) {
                    toast.success('Created Successfully');
                    this.resetForm();
                } else {
                    toast.error("Operation Failed, Try Again");
                }
                this.isSaveBtnClicked = false;
                // Reset form fields or handle navigation
            } catch (error) {
                toast.error("Operation Failed, Try Again");
                this.isSaveBtnClicked = false;
            } finally {
                this.isSaveBtnClicked = false;
            }
        },

        resetForm() {

            this.isSaveBtnClicked = false;
            this.form.sponsor_id = "",
                this.form.subcon_name = "",
                this.form.passfort_no = "",

                this.form.pass_expire = this.getCurrentDate();
            this.form.iqama_expire = this.getCurrentDate();

            this.form.iqama_no = "",
                this.form.mobile_no = "",
                this.form.id_number = "",
                this.form.abshar_mobile_no = "",
                this.form.country_contact_no = "",
                this.form.country_id = 1,
                this.form.division_id = 13,
                this.form.details = "",
                this.form.joining_date = this.getCurrentDate(),
                this.form.opening_balance = 0,
                this.form.entry_date = "",
                this.form.iqama_file = null,
                this.form.passport_file = null,
                this.form.contract_paper = null
        },

        async divisionFilter() {
            try {
                this.form.division_id = '';

                if (!this.form.country_id) {
                    this.data_for_form.divisions = [];
                    return;
                }

                this.loadingDivisions = true;

                const response = await axios.get(`${GetDivisionsByCountry}/${this.form.country_id}`);

                if (response.data.success) {
                    this.data_for_form.divisions = response.data.divisions;
                    this.loadingDivisions = false;

                    // Optional: If you want to automatically select the first division
                    // if (response.data.divisions.length > 0) {
                    //     this.form.division_id = response.data.divisions[0].division_id;
                    // }
                } else {
                    console.error('API returned error:', response.data.message);
                    this.data_for_form.divisions = [];
                }
            } catch (error) {
                console.error('Error fetching divisions:', error);

                // Handle different error cases
                if (error.response) {
                    // The request was made and the server responded with a status code
                    if (error.response.status === 404) {
                        console.error('Country not found');
                    } else {
                        console.error('Server error:', error.response.data);
                    }
                } else if (error.request) {
                    // The request was made but no response was received
                    console.error('No response received from server');
                } else {
                    // Something happened in setting up the request
                    console.error('Request setup error:', error.message);
                }

                this.data_for_form.divisions = [];
            } finally {
                this.loadingDivisions = false;
            }
        },
        changeSponsorDropdown(event) {
            const selectedIndex = event.target.selectedIndex;
            this.form.subcon_name = event.target.options[selectedIndex].text;
        }
    },
};
</script>

<style scoped>
label {
    font-weight: bold;
    margin-right: 10px;
}
</style>
