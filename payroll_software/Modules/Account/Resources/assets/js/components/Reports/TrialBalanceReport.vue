<template>
    <div>
        <!-- breadcrumb -->
        <div class="row">
            <div class="col-md-6">
                <h4>Trial Balance Report</h4>
            </div>
        </div>

        <!-- body -->
        <div class="card mt-2">
            <div class="card-header">
                <h4>Trial Balance Report</h4>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group" :class="{'has-error': errors.report_type}">
                            <label for="report_type">Report Type</label>
                            <select class="form-control" id="report_type" name="report_type"
                                    v-model="form.report_type">
                                <option value="as_of_date">As of Date</option>
                                <option value="as_of_period">As of Period</option>
                            </select>
                            <span class="help-block text-danger" v-if="errors.report_type"
                                  v-text="errors.report_type[0]"></span>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group" :class="{'has-error': errors.from_date}">
                            <label for="from_date">From Date</label>
                            <input type="date" class="form-control" id="from_date" name="from_date"
                                   v-model="form.from_date"
                                   placeholder="Enter from date">
                            <span class="help-block text-danger" v-if="errors.from_date"
                                  v-text="errors.from_date[0]"></span>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group" :class="{'has-error': errors.to_date}">
                            <label for="to_date">To Date</label>
                            <input type="date" class="form-control" id="to_date" name="to_date"
                                   v-model="form.to_date"
                                   placeholder="Enter to date">
                            <span class="help-block text-danger" v-if="errors.to_date"
                                  v-text="errors.to_date[0]"></span>
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


// import 'vue-multiselect/dist/vue-multiselect.css'


import {defineComponent} from "vue";
import Multiselect from 'vue-multiselect'

export default defineComponent({
    name: "TrialBalanceReport",
    components: {
        Multiselect
    },
    data() {
        return {
            form: {
                report_type: 'as_of_date',
                from_date: '',
                to_date: '',
            },
            errors: {},
            is_loading: false,
        }
    },
    methods: {
        downloadReport() {
            this.is_loading = true;
            axios.post(window.location.href, this.form, {
                responseType: 'blob'
            }).then(response => {
                const url = window.URL.createObjectURL(new Blob([response.data]));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', `trial_balance_report_${new Date().toISOString()}.pdf`);
                document.body.appendChild(link);
                link.click();
                this.is_loading = false;
            }).catch(error => {
                this.is_loading = false;
                if (error.response.status === 422) {
                    this.errors = error.response.data.errors;
                }
            });

        }
    },
})

</script>
<style lang="">

</style>
