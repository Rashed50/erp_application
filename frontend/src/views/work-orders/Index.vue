<template lang="html">
    <Breadcrumb title="All Work Orders" buttonText="Add Work Order" :buttonLink="{ name: 'admin_work_order_add' }"
        buttonIcon="fa-solid fa-plus" />

    <div class="main-content-wrapper mt-4">
        <div class="container-fluid">

            <!-- Table Card -->
            <v-card style="padding: 5px; margin-top: 15px;">
                <div class="row">
                    <div class="col-md-12 mb-2">
                        <div class="row align-items-center">
                            <div class="col-md-2"></div>
                            <div class="col-md-3">
                                <select class="form-select" v-model="filters.customer_id">
                                    <option value="">All Customers</option>
                                    <option v-for="customer in customers" :key="customer.id" :value="customer.id">
                                        {{ customer.name }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" v-model="filters.status">
                                    <option value="">All Status</option>
                                    <option v-for="status in WORK_ORDER_STATUSES" :key="status" :value="status">
                                        {{ status }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <div class="search-wrapper d-flex align-center gap-2">
                                    <v-text-field variant="outlined" density="compact" placeholder="Search..."
                                        v-model="filters.search" hide-details class="flex-grow-1"></v-text-field>
                                    <v-btn type="button" @click="fetchData" class="text-none text-white"
                                        color="blue-darken-3" rounded="0" variant="flat" min-width="100">
                                        Search
                                    </v-btn>
                                    <v-btn @click.prevent="resetFilters" class="text-none" color="grey-lighten-3"
                                        rounded="0" variant="flat" min-width="100">
                                        Reload
                                    </v-btn>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Data Table START -->
                    <v-table class="custom-bordered">
                        <thead>
                            <tr>
                                <th class="text-left">#</th>
                                <th class="text-left">Work Order No</th>
                                <th class="text-left">Work Title</th>
                                <th class="text-left">Customer</th>
                                <th class="text-left">Issue Date</th>
                                <th class="text-left">Deliver Date</th>
                                <th class="text-right">Total Amount</th>
                                <th class="text-right">Retention</th>
                                <th class="text-right">Paid</th>
                                <th class="text-right">Outstanding</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Approval</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>

                            <!-- Loading State with Vuetify Spinner -->
                            <tr v-if="loading">
                                <td colspan="13" class="text-center py-4">
                                    <v-progress-linear indeterminate color="primary" size="30"></v-progress-linear>
                                    Loading...
                                </td>
                            </tr>

                            <!-- No Data -->
                            <tr v-else-if="!items.length">
                                <td colspan="13" class="text-center py-4">
                                    No records found.
                                </td>
                            </tr>

                            <!-- Data Rows -->
                            <tr v-else v-for="(item, index) in items" :key="item.id">
                                <td>{{ (pagination.page - 1) * pagination.perPage + index + 1 }}</td>
                                <td>{{ item.work_order_no }}</td>
                                <td>{{ item.work_title }}</td>
                                <td>{{ item.customer_name }}</td>
                                <td>{{ item.issue_date }}</td>
                                <td>{{ item.deliver_date || '-' }}</td>
                                <td class="text-right">{{ Number(item.total_amount).toFixed(2) }}</td>
                                <td class="text-right">
                                    {{ Number(item.retention_amount).toFixed(2) }}
                                    <small class="text-muted">({{ Number(item.retention_percent) }}%)</small>
                                </td>
                                <td class="text-right">
                                    {{ Number(item.paid_amount).toFixed(2) }}
                                    <small class="text-muted d-block">{{ item.payments_count }} payment{{ item.payments_count === 1 ? '' : 's' }}</small>
                                </td>
                                <td class="text-right">{{ Number(item.outstanding_amount).toFixed(2) }}</td>
                                <td class="text-center">
                                    <span :class="statusBadgeClass(item.status)">{{ item.status }}</span>
                                </td>
                                <td class="text-center">
                                    <ApprovalBadge :approved-by="item.approved_by" :approved-at="item.approved_at" />
                                </td>
                                <!-- actions -->
                                <td class="text-center">
                                    <v-menu>
                                        <template v-slot:activator="{ props }">
                                            <button type="button" class="table-action-button" v-bind="props">
                                                <i class="fa-solid fa-bars"></i>
                                            </button>
                                        </template>
                                        <ul class="table-action-menu">
                                            <li class="menu-item" v-if="!item.approved_by && can(['work-orders.approve'])">
                                                <button type="button" class="menu-link" @click="approve(item, 'work order')">
                                                    Approve
                                                </button>
                                            </li>
                                            <li class="menu-item" v-if="can(['work-orders.update'])">
                                                <router-link :to="{ name: 'admin_work_order_edit', params: { id: item.id } }"
                                                    class="menu-link">
                                                    Edit
                                                </router-link>
                                            </li>
                                            <li class="menu-item" v-if="can(['work-orders.delete'])">
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
                    <!-- Data Table END -->

                    <BasePagination :current-page="pagination.page" :per-page="pagination.perPage"
                        :total="pagination.total" :last-page="pagination.lastPage" @page-change="changePage"
                        @per-page-change="changePerPage" />

                </div>
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
import ApprovalBadge from '@/components/common/ApprovalBadge.vue';
import { useApproval } from '@/composables/useApproval';
import { usePaginatedFetch } from '@/composables/usePaginatedFetch';
import { useFetch } from '@/composables/useFetch';
import { usePermission } from '@/composables/usePermission';
import { WORK_ORDER_STATUSES } from './statuses';

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
} = usePaginatedFetch('/api/work-orders', {
    search: '',
    customer_id: '',
    status: '',
})

// customer dropdown for the filter
const { items: customers, fetchData: loadCustomers } = useFetch('/api/customers', { per_page: 100 })

const { approve } = useApproval('/api/work-orders', () => fetchData())

const statusBadgeClass = (status) => ({
    'Pending': 'badge bg-warning text-dark',
    'In Progress': 'badge bg-info text-dark',
    'Completed': 'badge bg-success',
    'Cancelled': 'badge bg-secondary',
}[status] || 'badge bg-light text-dark')

const handleDelete = async (item) => {
    const result = await Swal.fire({
        title: 'Are you sure?',
        text: `Delete work order "${item.work_order_no}"? This cannot be undone.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete',
        cancelButtonText: 'Cancel',
    })

    if (!result.isConfirmed) return

    try {
        const resp = await axios.delete(`/api/work-orders/${item.id}`)
        if (resp.data.success) {
            toast.success(resp.data.message)
            fetchData()
        }
    } catch (e) {
        toast.error(e.response?.data?.message || 'Failed to delete work order.')
    }
}

onMounted(() => {
    fetchData()
    loadCustomers()
})

</script>
