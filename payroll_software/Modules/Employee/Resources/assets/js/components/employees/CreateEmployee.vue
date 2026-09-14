<template>
    <div class="row bread_part">
        <div class="col-sm-12 bread_col">
            <h4 class="pull-left page-title bread_title">Employee Information</h4>
            <ol class="breadcrumb pull-right">
                <li><a href="">Dashboard</a></li>
                <li><a href="">Employee Information</a></li>
                <li class="active">Add</li>
            </ol>
        </div>
    </div>

    <!-- Step Bar Indicator -->
    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-md-12">
                    <article class="card">
                        <div class="card-body">
                            <div class="track">
                                <div class="step active">
                                    <span class="icon"><i class="fa fa-check"></i> </span>
                                    <span class="text">First Step</span>
                                </div>
                                <div class="step">
                                    <span class="icon"><i class="fa fa-check"></i> </span>
                                    <span class="text">Second Step</span>
                                </div>
                            </div>
                        </div>
                    </article>
                </div>
            </div>

            <!-- Session Flash -->
            <div class="row">
                <div class="col-md-3"></div>
                <div class="col-md-7">
                    <!-- Success Message -->
                    <div v-if="flashMessage && flashMessage.type === 'success'" class="alert alert-success alertsuccess"
                        role="alert" style="margin-left: -20px">
                        <strong>{{ flashMessage.message }}</strong>
                    </div>

                    <!-- Error Message -->
                    <div v-if="flashMessage && flashMessage.type === 'error'" class="alert alert-warning alerterror"
                        role="alert" style="margin-left: -20px">
                        <strong>{{ flashMessage.message }}</strong>
                    </div>
                </div>
                <div class="col-md-2"></div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import { toast } from 'vue3-toastify';

export default {
    data() {
        return {
            flashMessage: null,  // Stores flash message
        };
    },

    props: {
        data: {
            type: Object,
            required: true
        }
    },

    methods: {
        handleFileUpload(event) {
            // Handle file upload logic
        },

        resetForm() {
            // Reset form logic
        },

        submitForm() {
            // Example form submission (you can replace it with your logic)
            // axios.post('/api/submit-form', this.data)
            //     .then(response => {
            //         // Set flash message on success
            //         this.flashMessage = { type: 'success', message: 'Form submitted successfully!' };
            //         toast.success('Form submitted successfully!');
            //     })
            //     .catch(error => {
            //         // Set flash message on error
            //         this.flashMessage = { type: 'error', message: 'There was an error submitting the form.' };
            //         toast.error('There was an error submitting the form.');
            //     });
        }
    },

    watch: {
        flashMessage(newValue) {
            // Trigger toast notifications for flash messages
            if (newValue) {
                if (newValue.type === 'error') {
                    toast.error(newValue.message);
                } else if (newValue.type === 'success') {
                    toast.success(newValue.message);
                }
            }
        }
    }
};
</script>

<style scoped>
/* Add your custom styles here */
</style>
