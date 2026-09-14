<template>
    <div class="container">
        <div class="card">
            <div class="card-body">
                <!-- <h4 class="text-center fw-bold" style="color:blue">Edit Subcontractor</h4> -->
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-8">
                                <h3 class="card-title card_top_title"><i class="fab fa-gg-circle"></i>
                                    Update Subcontractor Information
                                </h3>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>

                    <form @submit.prevent="submitForm">
                        <div class="card-body card_form">
                            <!-- Row 1 -->
                            <div class="row mb-1">

                                <div class="col-md-6 d-flex align-items-center">
                                    <label class="col-md-4 text-right">Subcontractor Name</label>
                                    <input type="text" v-model="form.subcon_name" class="form-control col-md-8"
                                        required />
                                </div>
                                <div class="col-md-6 d-flex align-items-center">
                                    <label class="col-md-4 text-right">ID Number</label>
                                    <input type="text" v-model="form.id_number" class=" form-control col-md-8"
                                        required />
                                </div>

                            </div>

                            <!-- Row 2 -->
                            <div class="row mb-1">
                                <div class="col-md-6 d-flex align-items-center">
                                    <label class="col-md-4 text-right">Passport Expiry Date</label>
                                    <input type="date" v-model="form.pass_expire" class="form-control col-md-8"
                                        required />
                                </div>
                                <div class="col-md-6 d-flex align-items-center">
                                    <label class="col-md-4 text-right">Iqama Number</label>
                                    <input type="text" v-model="form.iqama_no" class="form-control col-md-8" required />
                                </div>
                            </div>

                            <!-- Row 3 -->
                            <div class="row mb-1">
                                <div class="col-md-6 d-flex align-items-center">
                                    <label class="col-md-4 text-right">Iqama Expiry Date</label>
                                    <input type="date" v-model="form.iqama_expire" class="form-control col-md-8"
                                        required />
                                </div>
                                <div class="col-md-6 d-flex align-items-center">
                                    <label class="col-md-4 text-right">Mobile Number</label>
                                    <input type="text" v-model="form.mobile_no" class="form-control col-md-8"
                                        required />
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
                                    <input type="text" v-model="form.country_contact_no"
                                        class="form-control col-md-8" />
                                </div>
                            </div>

                            <!-- Row 5 -->
                            <div class="row mb-1">
                                <div class="col-md-6 d-flex align-items-center">
                                    <label class="col-md-4 text-right">Country</label>
                                    <select v-model="form.country_id" class="form-select" @change="divisionFilter()"
                                        required>
                                        <option value="" disabled>Select...</option>
                                        <option v-for="country in data_for_form.countries" :key="country.id"
                                            :value="country.id">
                                            {{ country.country_name }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-6 d-flex align-items-center">
                                    <label class="col-md-4 text-right">Division</label>
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

                            <!-- Row 7 -->
                            <div class="row mb-1">
                                <div class="col-md-6 d-flex align-items-center">
                                    <label class="col-md-4 text-right">Joining Date</label>
                                    <input type="date" v-model="form.joining_date" class="form-control col-md-8" />
                                </div>
                                <div class="col-md-6 d-flex align-items-center">
                                    <label class="col-md-4 text-right">Opening Balance</label>
                                    <input type="text" v-model="form.opening_balance" class="form-control col-md-8"
                                        required />
                                </div>
                            </div>

                            <!-- Row 6 -->
                            <div class="row mb-1">
                                <div class="col-md-6 d-flex align-items-center">
                                    <label class="col-md-4 text-right">Details</label>
                                    <textarea v-model="form.details" class="form-control col-md-8"></textarea>
                                </div>

                                <div class="col-md-6 d-flex align-items-center">
                                    <label class="col-md-4 text-right">Passport Number</label>
                                    <input type="text" v-model="form.passfort_no" class="form-control col-md-8"
                                        required />
                                </div>
                            </div>

                        </div>

                        <!-- Submit Button -->
                        <div class="card-footer card_footer_button text-center">
                            <button type="submit" :disabled="isSaveBtnClicked" class="btn btn-primary">UPDATE
                                INFO</button>
                        </div>
                    </form>
                </div>



                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-8">
                                <h3 class="card-title card_top_title"><i class="fab fa-gg-circle"></i>
                                    Update Subcontractor Related File
                                </h3>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>

                    <form @submit.prevent="submitFile">
                        <div class="card-body card_form">

                            <!-- Contract File Upload -->
                            <div class="row">
                                <div class="col-md-6">
                                    <label class=" control-label text-right">Contract File:</label>
                                    <div class="input-group">
                                        <input type="file" @change="handleFileUpload('contract_paper', $event)"
                                            class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <!-- <p class="fs-1 text-center" style="color: #1B56FD;">
                                        <i class="fa fa-file" aria-hidden="true"></i>
                                    </p> -->

                                    <p v-if="!detailsData.contract_paper"
                                        class="fw-bold text-center fs-5 mt-4 text-danger">
                                        File Not Uploaded!</p>
                                    <p v-else class="text-success text-center fs-5 mt-4">
                                        File Uploaded:
                                        <a :href="'/storage/' + detailsData.contract_paper" target="_blank">
                                            View
                                            <i class="fa fa-file me-2" aria-hidden="true"></i>
                                        </a>
                                    </p>

                                </div>
                            </div>

                            <!-- Iqama File Upload -->
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label class=" control-label text-right">Iqama File:</label>
                                    <div class="input-group">
                                        <input type="file" @change="handleFileUpload('iqama_file', $event)"
                                            class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <!-- <p class="fw-bold text-center fs-5 mt-4 text-danger">File Not Uploaded!</p> -->

                                    <p v-if="!detailsData.iqama_file" class="fw-bold text-center fs-5 mt-4 text-danger">
                                        File Not Uploaded!</p>
                                    <p v-else class="text-success text-center fs-5 mt-4">
                                        File Uploaded:
                                        <a :href="'/storage/' + detailsData.iqama_file" target="_blank">
                                            View
                                            <i class="fa fa-file me-2" aria-hidden="true"></i>
                                        </a>
                                    </p>
                                </div>
                            </div>

                            <!-- Passport File Upload -->
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label class=" control-label text-right">Passport File:</label>
                                    <div class="input-group">
                                        <input type="file" @change="handleFileUpload('passport_file', $event)"
                                            class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <p v-if="!detailsData.passport_file"
                                        class="fw-bold text-center fs-5 mt-4 text-danger">
                                        File Not Uploaded!</p>
                                    <p v-else class="text-success text-center fs-5 mt-4">
                                        File Uploaded:
                                        <a :href="'/storage/' + detailsData.passport_file" target="_blank">
                                            View
                                            <i class="fa fa-file me-2" aria-hidden="true"></i>
                                        </a>
                                    </p>
                                </div>
                            </div>

                        </div>

                        <div class="card-footer card_footer_button text-center">
                            <button type="submit" class="btn btn-primary waves-effect">Update File</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</template>



<script>
import axios from 'axios';
import { toast } from 'vue3-toastify';
import { DetailsSubContractor, GetDivisionsByCountry, UpdateSubContractor, UpdateFileSubContractorAPI } from "../routes";

export default {
    data() {
        return {
            detailsData: [],
            form: {

                subcon_name: "",
                passfort_no: "",
                pass_expire: "",
                id_number: "",
                iqama_no: "",
                iqama_expire: "",
                mobile_no: "",
                abshar_mobile_no: "",
                country_contact_no: "",
                country_id: 1,
                division_id: 13,
                details: "",
                joining_date: this.getCurrentDate(),
                opening_balance: 0,
                entry_date: "",
            },

            file: {
                iqama_file: null,
                passport_file: null,
                contract_paper: null,
            },

            isSaveBtnClicked: false,
            loadingDivisions: false,
        };
    },
    props: {
        subcontractorId: {
            type: [Number, String],
            required: true
        },
        data_for_form: {
            type: Object,
            required: true
        }
    },
    methods: {
        getCurrentDate() {
            const today = new Date();
            return today.toISOString().substr(0, 10);
        },

        async fetchSubcontractorData() {

            this.divisionFilter();

            try {
                const response = await axios.get(`${DetailsSubContractor}/${this.subcontractorId}`);
                const data = response.data.data; // Access the nested data object

                this.detailsData = data;

                // Map the API response to your form fields
                this.form = {

                    subcon_name: data.subcon_name || '',
                    passfort_no: data.passfort_no || '',
                    id_number: data.id_number || '',
                    pass_expire: data.pass_expire ? data.pass_expire.split(' ')[0] : '', // Format date if needed
                    iqama_no: data.iqama_no || '',
                    iqama_expire: data.iqama_expire ? data.iqama_expire.split(' ')[0] : '', // Format date if needed
                    mobile_no: data.mobile_no || '',
                    abshar_mobile_no: data.abshar_mobile_no || '',
                    country_contact_no: data.country_contact_no || '',
                    country_id: data.country_id || 1,    // Default to 1 if null
                    division_id: data.division_id || 13, // Default to 13 if null
                    details: data.details || '',
                    joining_date: data.joining_date,// ? data.joining_date.split(' ')[0] : this.getCurrentDate(),
                    opening_balance: data.opening_balance,
                    entry_date: data.entry_date ? data.entry_date.split(' ')[0] : '',
                };

                // Now handle country and division in sequence
                if (data.country_id) {
                    this.form.country_id = data.country_id;

                    // Wait for divisions to load
                    await this.divisionFilter();

                    // Now set the division_id after divisions are loaded
                    if (data.division_id) {
                        // Check if the division exists in the loaded divisions
                        const divisionExists = this.data_for_form.divisions.some(
                            div => div.division_id == data.division_id
                        );

                        if (divisionExists) {
                            this.form.division_id = data.division_id;
                        } else {
                            console.warn('Division not found for selected country');
                            this.form.division_id = '';
                        }
                    }
                } else {
                    // Default country case
                    this.form.country_id = 1;
                    await this.divisionFilter();
                    this.form.division_id = 13;
                }


            } catch (error) {
                console.error("Error fetching subcontractor data:", error);
                // Show error to user, e.g.:
                this.$toast.error('Failed to load subcontractor data');
            }
        },

        handleFileUpload(field, event) {
            this.file[field] = event.target.files[0];
        },

        async submitForm() {
            this.isSaveBtnClicked = true;

            try {
                const formData = new FormData();

                // Append all form fields to FormData
                for (const key in this.form) {
                    if (this.form[key] !== null) {
                        formData.append(key, this.form[key]);
                    }
                }
               // debugger;
               // console.log(formData);
               // formData.append('id_number',1520);

                // Option 1: Using POST with _method override
                formData.append('_method', 'PUT');

                const response = await axios.post(
                    `${UpdateSubContractor}/${this.subcontractorId}`,
                    formData,
                    {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    }
                );

                // Handle successful update
                // console.log("Update successful:", response.data);
                toast.success('Update Successfully');
                // this.$emit('subcontractor-updated');

            } catch (error) {
                console.error("Error updating subcontractor:", error);
                toast.error("Operation Failed, Try Again");
                // Handle error (show message to user, etc.)
            } finally {
                this.isSaveBtnClicked = false;
            }
        },

        async submitFile() {
            try {
                const formData = new FormData();

                // Append files only if they are selected
                if (this.file.iqama_file) {
                    formData.append("iqama_file", this.file.iqama_file);
                }
                if (this.file.passport_file) {
                    formData.append("passport_file", this.file.passport_file);
                }
                if (this.file.contract_paper) {
                    formData.append("contract_paper", this.file.contract_paper);
                }


                // For Laravel PUT method via POST
                formData.append("_method", "PUT");

                const response = await axios.post(
                    `${UpdateFileSubContractorAPI}/${this.subcontractorId}`,
                    formData,
                    {
                        headers: {
                            "Content-Type": "multipart/form-data",
                        },
                    }
                );

                this.fetchSubcontractorData();
                toast.success("Files updated successfully!");

                // this.file.iqama_file     = null
                // this.file.passport_file  = null
                // this.file.contract_paper = null

            } catch (error) {
                console.error("File upload error:", error);
                toast.error("Failed to update files.");
            }
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
    },
    mounted() {
        this.fetchSubcontractorData();
    },
    watch: {
        // If the subcontractorId prop changes (e.g., editing a different record)
        subcontractorId(newVal) {
            if (newVal) {
                this.fetchSubcontractorData();
            }
        }
    }
};
</script>
