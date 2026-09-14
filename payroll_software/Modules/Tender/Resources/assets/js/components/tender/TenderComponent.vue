<template>
    <div>
        <!-- breadcrumb -->
        <div class="row">
            <div class="col-md-6">
                <h4>Tender Create</h4>
            </div>
            <div class="col-md-6 text-right"></div>
        </div>

        <!-- body -->
        <div class="card mt-2">
            <form class="card-body" @submit.prevent="submit" enctype="multipart/form-data">
                <button class="btn btn-success btn-sm mt-2" type="submit" :disabled="uploading" @click="fetchPdf">
                    <i v-if="uploading" class="fa fa-spinner fa-spin"></i>
                    <i v-else class="fa fa-check"></i>
                    &nbsp; Open PDF
                </button>
            </form>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import { toast } from 'vue3-toastify';
import { TenderPDF } from "../../routes";
export default {
    data() {
        return {
            // Add any data needed here
        };
    },

    methods: {
        async fetchPdf() {
            try {
                const params = new URLSearchParams({
                    is_download: 1, 
                }).toString();

                window.open(`${TenderPDF}?${params}`, '_blank');

            } catch (error) {
                toast.error("Failed to open PDF");
                console.error('Error opening PDF:', error);
            }
        }
    }
};
</script>

<style scoped>
/* Add any specific styles here */
</style>
