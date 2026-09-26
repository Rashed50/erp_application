<template lang="html">
    <Breadcrumb title="Employees" buttonText="Add Employee" :buttonLink="{ name: 'admin_hr_employee_add' }"
        buttonIcon="fa-solid fa-user-plus" />

    <div class="main-content-wrapper mt-4">
        <div class="container-fluid">
            <v-card style="padding: 5px; margin-top: 15px;">
                <div class="row align-items-center mb-2">
                    <div class="col-md-2">
                        <select class="form-select" v-model="filters.department">
                            <option value="">All Departments</option>
                            <option v-for="department in options.departments" :key="department" :value="department">
                                {{ department }}
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" v-model="filters.designation">
                            <option value="">All Designations</option>
                            <option v-for="designation in options.designations" :key="designation" :value="designation">
                                {{ designation }}
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" v-model="filters.status">
                            <option value="">All Status</option>
                            <option v-for="status in options.statuses" :key="status" :value="status">{{ status }}</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <div class="search-wrapper d-flex align-center gap-2">
                            <v-text-field variant="outlined" density="compact" placeholder="ID, name, phone, email..."
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
                            <th class="text-left">#</th>
                            <th class="text-left">Employee ID</th>
                            <th class="text-left">Name</th>
                            <th class="text-left">Department</th>
                            <th class="text-left">Designation</th>
                            <th class="text-left">Phone</th>
                            <th class="text-left">Joining Date</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="loading">
                            <td colspan="9" class="text-center py-4">
                                <v-progress-linear indeterminate color="primary"></v-progress-linear>
                                Loading...
                            </td>
                        </tr>
                        <tr v-else-if="!items.length">
                            <td colspan="9" class="text-center py-4">No employees found.</td>
                        </tr>
                        <tr v-else v-for="(item, index) in items" :key="item.id">
                            <td>{{ (pagination.page - 1) * pagination.perPage + index + 1 }}</td>
                            <td>{{ item.employee_code }}</td>
                            <td>
                                <router-link :to="{ name: 'admin_hr_employee_show', params: { id: item.id } }">
                                    {{ item.name }}
                                </router-link>
                            </td>
                            <td>{{ item.department }}</td>
                            <td>{{ item.designation }}</td>
                            <td>{{ item.phone }}</td>
                            <td>{{ item.joining_date }}</td>
                            <td class="text-center">
                                <span :class="employeeStatusClass(item.status)">{{ item.status }}</span>
                            </td>
                            <td class="text-center">
                                <v-menu>
                                    <template v-slot:activator="{ props }">
                                        <button type="button" class="table-action-button" v-bind="props">
                                            <i class="fa-solid fa-bars"></i>
                                        </button>
                                    </template>
                                    <ul class="table-action-menu">
                                        <li class="menu-item">
                                            <router-link :to="{ name: 'admin_hr_employee_show', params: { id: item.id } }"
                                                class="menu-link">
                                                Details
                                            </router-link>
                                        </li>
                                        <li class="menu-item" v-if="can(['employees.update'])">
                                            <router-link :to="{ name: 'admin_hr_employee_edit', params: { id: item.id } }"
                                                class="menu-link">
                                                Edit
                                            </router-link>
                                        </li>
                                        <li class="menu-item" v-if="can(['employees.delete'])">
                                            <button type="button" class="menu-link" @click="handleDelete(item)">
                                                Delete
                                            </button>
                                        </li>
                                    </ul>
                                </v-menu>
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
import Swal from 'sweetalert2';
import { toast } from 'vue3-toastify';
import BasePagination from '@/components/common/BasePagination.vue';
import Breadcrumb from '@/components/common/Breadcrumb.vue';
import { usePaginatedFetch } from '@/composables/usePaginatedFetch';
import { usePermission } from '@/composables/usePermission';
import { employeeStatusClass } from '../helpers';

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
} = usePaginatedFetch('/api/hr/employees', {
    search: '',
    department: '',
    designation: '',
    status: '',
})

const options = ref({ departments: [], designations: [], statuses: [] })

const handleDelete = async (item) => {
    const result = await Swal.fire({
        title: 'Are you sure?',
        text: `Delete employee "${item.name}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete',
        cancelButtonText: 'Cancel',
    })

    if (!result.isConfirmed) return

    try {
        const resp = await axios.delete(`/api/hr/employees/${item.id}`)
        if (resp.data.success) {
            toast.success(resp.data.message)
            fetchData()
        }
    } catch (e) {
        // An employee with salary history cannot be deleted (422).
        toast.error(e.response?.data?.message || 'Failed to delete employee.')
    }
}

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
