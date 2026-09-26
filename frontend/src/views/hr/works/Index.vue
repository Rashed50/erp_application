<template lang="html">
    <Breadcrumb title="Monthly Work History" buttonText="Enter Monthly Work" :buttonLink="{ name: 'admin_hr_works_entry' }"
        buttonIcon="fa-solid fa-calendar-plus" />

    <div class="main-content-wrapper mt-4">
        <div class="container-fluid">
            <v-card style="padding: 5px; margin-top: 15px;">
                <div class="row align-items-center mb-2">
                    <div class="col-md-2">
                        <input type="month" class="form-control" v-model="filters.month" />
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" v-model="filters.department">
                            <option value="">All Departments</option>
                            <option v-for="department in options.departments" :key="department" :value="department">
                                {{ department }}
                            </option>
                        </select>
                    </div>
                    <div class="col-md-7">
                        <div class="search-wrapper d-flex align-center gap-2">
                            <v-text-field variant="outlined" density="compact" placeholder="Employee ID or name..."
                                v-model="filters.search" hide-details class="flex-grow-1"></v-text-field>
                            <v-btn type="button" @click="fetchData" class="text-none text-white" color="blue-darken-3"
                                rounded="0" variant="flat" min-width="100">
                                Search
                            </v-btn>
                            <v-btn @click.prevent="resetFilters" class="text-none" color="grey-lighten-3" rounded="0"
                                variant="flat" min-width="100">
                                Reload
                            </v-btn>
                        </div>
                    </div>
                </div>

                <v-table class="custom-bordered">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th>Employee</th>
                            <th>Department</th>
                            <th class="text-right">Working</th>
                            <th class="text-right">Present</th>
                            <th class="text-right">Absent</th>
                            <th class="text-right">Paid Leave</th>
                            <th class="text-right">Unpaid Leave</th>
                            <th class="text-right">OT Hours</th>
                            <th class="text-right">Bonus</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="loading">
                            <td colspan="11" class="text-center py-4">
                                <v-progress-linear indeterminate color="primary"></v-progress-linear>
                                Loading...
                            </td>
                        </tr>
                        <tr v-else-if="!items.length">
                            <td colspan="11" class="text-center py-4">No work records found.</td>
                        </tr>
                        <tr v-else v-for="item in items" :key="item.id">
                            <td>{{ monthLabel(item.salary_month) }}</td>
                            <td>{{ item.employee_code }} - {{ item.employee_name }}</td>
                            <td>{{ item.department }}</td>
                            <td class="text-right">{{ item.working_days }}</td>
                            <td class="text-right">{{ item.present_days }}</td>
                            <td class="text-right">{{ item.absent_days }}</td>
                            <td class="text-right">{{ item.paid_leave_days }}</td>
                            <td class="text-right">{{ item.unpaid_leave_days }}</td>
                            <td class="text-right">{{ item.overtime_hours }}</td>
                            <td class="text-right">{{ money(item.bonus) }}</td>
                            <td class="text-center">
                                <router-link v-if="can(['employee-works.update'])"
                                    :to="{ name: 'admin_hr_works_entry', query: { employee_id: item.employee_id, month: item.salary_month } }">
                                    Open
                                </router-link>
                            </td>
                        </tr>
                    </tbody>
                </v-table>

                <BasePagination :current-page="pagination.page" :per-page="pagination.perPage" :total="pagination.total"
                    :last-page="pagination.lastPage" @page-change="changePage" @per-page-change="changePerPage" />
            </v-card>
        </div>
    </div>
</template>

<script setup>
import axios from 'axios';
import BasePagination from '@/components/common/BasePagination.vue';
import Breadcrumb from '@/components/common/Breadcrumb.vue';
import { usePaginatedFetch } from '@/composables/usePaginatedFetch';
import { usePermission } from '@/composables/usePermission';
import { currentMonth, money, monthLabel } from '../helpers';

const { can } = usePermission()

const {
    items,
    loading,
    filters,
    pagination,
    fetchData,
    changePage,
    changePerPage,
    resetFilters,
} = usePaginatedFetch('/api/hr/employee-works', {
    month: currentMonth(),
    department: '',
    search: '',
})

const options = ref({ departments: [] })

onMounted(async () => {
    fetchData()
    try {
        const { data } = await axios.get('/api/hr/employees/options')
        if (data.success) options.value = data.data
    } catch (e) {
        console.error(e)
    }
})
</script>
