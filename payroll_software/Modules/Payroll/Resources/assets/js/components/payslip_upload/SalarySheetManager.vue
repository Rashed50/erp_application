<template>
    <div>
        <div class="row mb-1">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-2">
                                <button
                                    @click="activeSection = 'add'"
                                    class="btn btn-primary w-100"
                                    :class="{ 'btn-primary': activeSection === 'add', 'btn-outline-primary': activeSection !== 'add' }"
                                >
                                    <i class="fas fa-plus"></i> Upload Payslip
                                </button>
                            </div>
                            <div class="col-sm-2">
                                <button
                                    @click="activeSection = 'search'"
                                    class="btn btn-primary w-100"
                                    :class="{ 'btn-primary': activeSection === 'search', 'btn-outline-primary': activeSection !== 'search' }"
                                >
                                    <i class="fas fa-search"></i> Search Payslip
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Salary Sheet Section -->
        <div v-if="activeSection === 'add'" class="row">
            <AddSalarySheet  />
        </div>

        <!-- Search Salary Sheet Section -->
        <div v-else-if="activeSection === 'search'" class="row">
            <SearchSalarySheet   />
        </div>
    </div>
</template>

<script>

import { inject } from 'vue';
import axios from 'axios';
import { toast } from 'vue3-toastify';
import { useAuth } from "../../../../../../../resources/js/components/useAuth.js";

import AddSalarySheet from './UploadSalarySheet.vue';
import SearchSalarySheet from './SearchSalarySheet.vue';

export default {
    name: 'SalarySheetManager',
    components: {
        AddSalarySheet,
        SearchSalarySheet
    },
    data() {
        return {
            activeSection: '' // 'add' or 'search'
        }
    }
}
</script>
