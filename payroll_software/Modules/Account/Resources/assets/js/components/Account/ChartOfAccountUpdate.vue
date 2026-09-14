<template lang="">
    <div>
        <!-- breadcrumb -->
        <div class="row">
            <div class="col-md-6">
                <h4>Ledger Account Update</h4>
            </div>
            <div class="col-md-6 text-right">

            </div>
        </div>
        <!-- body -->
        <div class="card mt-2">
            <form class="card-body" @submit.prevent="updateChartOfAccount">
                <div class="d-flex flex-wrap">
                    <div class="col-md-6 form-group">
                        <label>Account Type: <span class="req_star">*</span> </label>
                        <Multiselect
                            required
                            v-model="selectedAccountType"
                            :options="accountTypeOptions"
                            placeholder="Select item"
                            :searchable="true"/>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Account Name: <span class="req_star">*</span> </label>
                        <input class="form-control form-control-lg" placeholder="Account Name... " required v-model="chart_of_acct_name" />
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Subaccount Of: <span class="req_star">*</span> </label>
                        <Multiselect
                            required
                            v-model="selectedMotherAccount"
                            :options="motherAccountOptions"
                            placeholder="Select item"
                            :searchable="true"
                            @select="getNextUniqueAccountNumber(selectedMotherAccount)"
                            />
                    </div>



                    <div class="col-md-6 form-group">
                        <label>Account Number: <span class="req_star">*</span> </label>
                        <input class="form-control form-control-lg" type="text" placeholder="Account Number... " required v-model="chart_of_acct_number" />
                    </div>

                    <div class="col-md-6 form-group">
                        <label>Opening Balance: <span class="req_star">*</span> </label>
                        <input class="form-control form-control-lg" type="number" placeholder="Opening Balance... " required v-model="acct_balance" />
                    </div>

                    <div class="col-md-6 form-group">
                        <label>Opening Date: <span class="req_star">*</span> </label>
                        <input class="form-control form-control-lg" type="date"  placeholder="Opening Date... " required v-model="opening_date" />
                    </div>

                    <div class="col-md-6 row form-group">
                        <div class="ms-4">
                            <label>Active Status  </label> &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;
                            <input  class="form-check-input"   type="checkbox"v-model="active_status" />
                            <label class="col-sm-6 control-label" style="text-align:left;">Active</label><br>
                        </div>
                    </div>
                </div>

                <button class="btn btn-success btn-sm mt-2 ms-2" type="submit" @click="force_save = 1" :disabled="uploading">
                    <i v-if="uploading" class="fa fa-spinner fa-spin"></i>
                    <i v-else class="fa fa-check"></i>
                    &nbsp;
                    Update
                </button>

                <a class="btn btn-danger btn-sm ms-2 mt-2" @click="CancelButton">
                    <i class="fa fa-times me-2" aria-hidden="true"></i> Cancel
                </a>
            </form>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import { toast } from 'vue3-toastify';
import Multiselect from '@vueform/multiselect'
import "@vueform/multiselect/themes/default.css"
import {ChartOfAccountCreate, ChartOfAccountList, ChartOfAccountEditPost,ChartOfAccountUniqueAccountNumber} from "../../routes";

export default {
    components: {
        Multiselect,
    },

    props: {
        data: {
            type: Object,
            required: true
        }
    },

    data() {
        return {
            selectedAccountType: null,
            selectedMotherAccount: null,
            chart_of_acct_name: '',
            chart_of_acct_number: '',
            acct_balance: '',
            opening_date: '',
            active_status: false,
            error: null,
            success: null,
            uploading: false,
             motherAccountOptions: [],
        }
    },
    watch: {

        // selectedAccountType(newVal) {
        //     if (!newVal) {
        //         this.selectedMotherAccount = null;
        //         this.motherAccountOptions = [];
        //         this.chart_of_acct_number = '';
        //     }
        // }
    },

    computed: {


        accountTypeOptions() {

            if (this.selectedAccountType) {
                this.motherAccountOptions = this.data.mother_accounts.filter(d => d.acct_type_id === this.selectedAccountType);
              //  this.chart_of_acct_number = this.motherAccountOptions.length > 0 ? this.motherAccountOptions[0].chart_of_acct_number : '';
              //  this.chart_of_acct_number =parseInt(this.chart_of_acct_number)+(this.motherAccountOptions.length);
            }
            return [{ label: 'Select item', value: null }, ...this.data.account_types.map(d => ({ label: d.acct_type_name, value: d.acct_type_id }))];
        },
        motherAccountOptions() {
            return [{ label: 'Select item', value: null }, ...this.motherAccountOptions.map(d => ({ label: d.chart_of_acct_name, value: d.chart_of_acct_id }))];
        }
    },

    mounted() {
        if (this.data.chart_of_account) {
            // console.log(this.data.chart_of_account);
            this.selectedAccountType = this.data.chart_of_account.acct_type_id;
            this.selectedMotherAccount = this.data.chart_of_account.parent_id;
            this.chart_of_acct_name = this.data.chart_of_account.chart_of_acct_name;
            this.chart_of_acct_number = this.data.chart_of_account.chart_of_acct_number;
            this.acct_balance = this.data.chart_of_account.acct_balance;
            this.opening_date = this.data.chart_of_account.opening_date;
            this.active_status = !!this.data.chart_of_account.active_status;
            this.accountId = this.data.chart_of_account.chart_of_acct_id;

        }
    },

    methods: {
        async updateChartOfAccount() {
            this.error = null;
            this.success = null;
            this.uploading = true;

            let formData = new FormData();

            formData.append('acct_type_id', this.selectedAccountType);
            formData.append('chart_of_acct_name', this.chart_of_acct_name);
            formData.append('chart_of_acct_number', this.chart_of_acct_number);
            formData.append('account_id', this.selectedMotherAccount);
            formData.append('acct_balance', this.acct_balance);
            formData.append('opening_date', this.opening_date);
            formData.append('active_status', this.active_status ? 1 : 0);

            // Log the formData contents
            // for (let pair of formData.entries()) {
            //     console.log(`${pair[0]}: ${pair[1]}`);
            // }

            // console.log("Id =", this.accountId);

            try {
                const response = await axios.post(`${ChartOfAccountEditPost}/${this.accountId}`, formData);
                // this.success = response.data.message;

                if (response.data.success === false){
                    toast.error(response.data.message);
                }else{
                    toast.success(response.data.message);
                }

            } catch (e) {
                // this.error = e.response?.data?.message || 'An error occurred';
                 toast.error(e.response?.data?.message || 'An error occurred');
            } finally {
                this.uploading = false;
            }
        },

        CancelButton() {
            window.location = ChartOfAccountList;
        },

        async getNextUniqueAccountNumber($motherAccountId) {

            console.log("selected mother account id ", $motherAccountId);
            if (!$motherAccountId) {
                this.chart_of_acct_number = '';
                return;
            }
           const selectedSubAcc = this.motherAccountOptions.filter(d => d.chart_of_acct_id === this.selectedMotherAccount);
           await axios.get(`${ChartOfAccountUniqueAccountNumber}/${$motherAccountId}`)
                .then(response => {
                    this.chart_of_acct_number =selectedSubAcc[0].chart_of_acct_number+ '.' + response.data.unique_account_number;
                })
                .catch(error => {
                    console.error('Error fetching unique account number:', error);
                    toast.error('Failed to Fetch unique account number, Try again');
                });
        }
    }
}
</script>
