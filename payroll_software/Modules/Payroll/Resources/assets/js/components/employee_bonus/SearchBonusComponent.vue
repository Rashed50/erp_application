<template>
    <div>
        <!-- Search Panel matching Image Layout -->
        <div class="card p-3 shadow-sm mb-4">
            <div class="row align-items-center justify-content-center">
                <label class="col-auto col-form-label fw-bold">Employee Searching by</label>

                <div class="col-md-3">
                    <select class="form-select" v-model="searchForm.searchType">
                        <option value="employee_id">Employee ID</option>
                        <option value="2">Iqama No</option>
                        <option value="3">Passport No</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <input type="text" class="form-control" v-model="searchForm.searchValue"
                        placeholder="Enter ID/Iqama/Passport No" @keyup.enter="searchRecords" />
                </div>

                <div class="col-auto">
                    <button class="btn btn-primary text-white fw-bold px-4" @click="searchRecords"
                        :disabled="isSearching">
                        {{ isSearching ? 'SEARCHING...' : 'SEARCH' }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Salary Pending Result Table -->
        <div class="row" v-if="showTable">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover custom_table mb-0">
                                <thead>
                                    <tr>
                                        <th>S.N</th>
                                        <th>Emp. Id</th>
                                        <th>Name</th>
                                        <th>Akama No</th>
                                        <th>Sponer</th>
                                        <th>Basic/Hourly</th>
                                        <th>Bonus Type</th>
                                        <th>Month,Year</th>
                                        <th>Amount</th>
                                        <th>Manage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in records" :key="item.bonus_auto_id || index">
                                        <td>{{ index + 1 }}</td>
                                        <td>{{ item.employee_id }}</td>
                                        <td>{{ item.employee_name }}</td>
                                        <td>{{ item.akama_no }}</td>
                                        <td>{{ item.spons_name }}</td>
                                        <td>{{ item.hourly_employee }}</td>
                                        <td>{{ item.bonus_type }}</td>
                                        <td>{{ item.month }} {{ item.year ? ', ' + item.year : '' }}</td>
                                        <td>{{ item.amount }}</td>

                                        <td class="text-center">
                                            <button @click="deleteRecord(item.bonus_auto_id)"
                                                v-if="hasPermission('salary_bonus_delete')"
                                                style="background: none; border: none; padding: 0; cursor: pointer; color: #dc3545;"
                                                title="Delete Record">
                                                <i class="fas fa-trash fa-lg"></i>
                                            </button>
                                        </td>
                                    </tr>

                                    <tr v-if="records.length === 0">
                                        <td colspan="10" class="text-center text-danger">No Records Found!</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import { toast } from 'vue3-toastify';
import { EmployeeBonusDeleteAPI, EmployeeBonusListAPI } from '../../routes.js';
import { inject } from 'vue';
import { useAuth } from '../../../../../../../resources/js/components/useAuth.js';
export default {
    name: 'EmployeeBonusSalaryRecords',

    setup() {
        const auth = inject('auth');
        const { hasPermission } = useAuth();

        return {
            hasPermission,
        };
    },
    data() {
        return {
            records: [],
            isSearching: false,
            showTable: false,
            searchForm: {
                searchValue: '',
                searchType: 'employee_id',
            }
        };
    },

    methods: {
        async searchRecords() {


            this.isSearching = true;
            this.showTable = true;


            try {
                const response = await axios.get(EmployeeBonusListAPI, {
                    params: {
                        searchValue: this.searchForm.searchValue,
                        searchType: this.searchForm.searchType,
                    }
                });

                //  console.log('hello-response', response);

                if (response.data.status === 200 && response.data.success) {
                    this.records = response.data.records || [];
                    this.showTable = true;
                } else {
                    this.records = [];
                    this.showTable = true;
                    toast.error(response.data.message || 'Operation Failed');
                }
            } catch (error) {
                console.error(error);
                this.records = [];
                this.showTable = true;
            } finally {
                this.isSearching = false;
            }
        },

        async deleteRecord(bonus_auto_id) {
            if (!confirm('Are you sure you want to delete this bonus record?')) return;

            try {
                const response = await axios.delete(`${EmployeeBonusDeleteAPI}/${bonus_auto_id}`);
                if (response.data.status === 200) {
                    toast.success('Successfully deleted');
                    this.searchRecords();
                } else {
                    toast.error(response.data.message || 'Failed to delete record.');
                }
            } catch (error) {
                console.error("Delete Failed", error);
                toast.error('An error occurred while deleting.');
            }
        }
    }
};
</script>