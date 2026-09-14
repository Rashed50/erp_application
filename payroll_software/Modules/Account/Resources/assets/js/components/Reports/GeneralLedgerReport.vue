<template>
    <div>
        <!-- breadcrumb -->
        <div class="row">
            <div class="col-md-6">
                <h4>General Ledger Report</h4>
            </div>
        </div>

        <!-- body -->
        <div class="card mt-2">
            <div class="card-header">
                <h4>General Ledger Report</h4>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="from_date">From Date</label>
                            <input type="date" class="form-control" id="from_date" name="from_date"
                                   v-model="form.from_date"
                                   placeholder="Enter from date">
                        </div>
                    </div>


                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="to_date">To Date</label>
                            <input type="date" class="form-control" id="to_date" name="to_date"
                                   v-model="form.to_date"
                                   placeholder="Enter to date">
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-group">
                            <label for="account">Account</label>

                            <Multiselect
                                v-model="selectAccount"
                                :options="accounts"
                                id="ajax"
                                label="chart_of_acct_name"
                                track-by="chart_of_acct_number"
                                placeholder="Select Account"
                                :close-on-select="true"
                                :clear-on-select="false"
                                :preserve-search="true"
                                :multiple="false"
                                :taggable="false"
                                @search-change="getAccounts"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <button class="btn btn-primary" @click="downloadReport">Download</button>
            </div>

        </div>
    </div>
</template>
<script>


import {defineComponent} from "vue";
import {ChartOfAccountListAPI} from "../../routes";
import axios from "axios";
import Multiselect from 'vue-multiselect'

import 'vue-multiselect/dist/vue-multiselect.css'

export default defineComponent({
    name: "GeneralLedgerReport",
    components: {
        Multiselect
    },
    data() {
        return {
            form: {
                from_date: '',
                to_date: '',
                account: ''
            },

            accounts: [],
            isAccountSearchLoading: false,
            selectAccount: null
        }
    },

    mounted() {
        this.getAccounts('A');
    },


    methods: {
        getAccounts(query) {
            this.isAccountSearchLoading = true;
            axios.get(ChartOfAccountListAPI + '?search=' + query).then(response => {
                this.accounts = response.data.data;
                this.isAccountSearchLoading = false;
            }).catch(error => {
                console.log(error);
            })
        },

        downloadReport() {
            if (this.form.from_date === '' || this.form.to_date === '' || this.form.account === '') {
                alert('Please fill all the fields');
                return;
            }

            axios.post(window.location.href, this.form, {
                responseType: 'blob'
            }).then(response => {
                const url = window.URL.createObjectURL(new Blob([response.data]));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', `General Ledger Report ${this.selectAccount.chart_of_acct_number} ${this.form.from_date} - ${this.form.to_date}.pdf`);
                document.body.appendChild(link);
                link.click();
            }).catch(error => {
                console.log(error);
            })
        }
    },

    watch: {
        selectAccount: function (val) {
            this.form.account = val.chart_of_acct_id;
        }
    }
})

</script>
