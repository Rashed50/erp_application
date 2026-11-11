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
                                    <option value="">{{ $t('All Customers') }}</option>
                                    <option v-for="customer in customers" :key="customer.id" :value="customer.id">
                                        {{ customer.name }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" v-model="filters.status">
                                    <option value="">{{ $t('All Status') }}</option>
                                    <option v-for="status in WORK_ORDER_STATUSES" :key="status" :value="status">
                                        {{ $t(status) }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <div class="search-wrapper d-flex align-center gap-2">
                                    <v-text-field variant="outlined" density="compact" :placeholder="$t('Search...')"
                                        v-model="filters.search" hide-details class="flex-grow-1"></v-text-field>
                                    <v-btn type="button" @click="fetchData" class="text-none text-white"
                                        color="blue-darken-3" rounded="0" variant="flat" min-width="100">
                                        {{ $t('Search') }}
                                    </v-btn>
                                    <v-btn @click.prevent="resetFilters" class="text-none" color="grey-lighten-3"
                                        rounded="0" variant="flat" min-width="100">
                                        {{ $t('Reload') }}
                                    </v-btn>
                                    <v-btn @click.prevent="print" class="text-none" color="grey-lighten-3"
                                        rounded="0" variant="flat" min-width="100" :loading="printing"
                                        :disabled="!items.length">
                                        <i class="fa-solid fa-print me-1"></i> {{ $t('Print') }}
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
                                <th class="text-left">{{ $t('Work Order No') }}</th>
                                <th class="text-left">{{ $t('Work Title') }}</th>
                                <th class="text-left">{{ $t('Customer') }}</th>
                                <th class="text-left">{{ $t('Issue Date') }}</th>
                                <th class="text-left">{{ $t('Deliver Date') }}</th>
                                <th class="text-right">{{ $t('Total Amount') }}</th>
                                <th class="text-right">{{ $t('Retention') }}</th>
                                <th class="text-right">{{ $t('Paid') }}</th>
                                <th class="text-right">{{ $t('Outstanding') }}</th>
                                <th class="text-center">{{ $t('Status') }}</th>
                                <th class="text-left">{{ $t('Inserted By') }}</th>
                                <th class="text-center">{{ $t('Approval') }}</th>
                                <th class="text-center">{{ $t('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>

                            <!-- Loading State with Vuetify Spinner -->
                            <tr v-if="loading">
                                <td colspan="14" class="text-center py-4">
                                    <v-progress-linear indeterminate color="primary" size="30"></v-progress-linear>
                                    {{ $t('Loading...') }}
                                </td>
                            </tr>

                            <!-- No Data -->
                            <tr v-else-if="!items.length">
                                <td colspan="14" class="text-center py-4">
                                    {{ $t('No records found.') }}
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
                                    <small class="text-muted d-block">{{ $t(item.payments_count === 1 ? '{count} payment' : '{count} payments', { count: item.payments_count }) }}</small>
                                </td>
                                <td class="text-right">{{ Number(item.outstanding_amount).toFixed(2) }}</td>
                                <td class="text-center">
                                    <span :class="statusBadgeClass(item.status)">{{ $t(item.status) }}</span>
                                </td>
                                <td>{{ item.created_by_name || '-' }}</td>
                                <td class="text-center">
                                    <ApprovalBadge :approved-by="item.approved_by" :approved-at="item.approved_at"
                                        :approved-by-name="item.approved_by_name" />
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
                                            <li class="menu-item">
                                                <button type="button" class="menu-link" @click="openView(item)">
                                                    {{ $t('View') }}
                                                </button>
                                            </li>
                                            <li class="menu-item" v-if="!item.approved_by && can(['work-orders.approve'])">
                                                <button type="button" class="menu-link" @click="approve(item, 'work order')">
                                                    {{ $t('Approve') }}
                                                </button>
                                            </li>
                                            <li class="menu-item" v-if="can(['work-orders.update'])">
                                                <router-link :to="{ name: 'admin_work_order_edit', params: { id: item.id } }"
                                                    class="menu-link">
                                                    {{ $t('Edit') }}
                                                </router-link>
                                            </li>
                                            <li class="menu-item" v-if="can(['work-orders.delete'])">
                                                <button type="button" class="menu-link" @click="handleDelete(item)">
                                                    {{ $t('Delete') }}
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

    <v-dialog v-model="viewDialog" max-width="800">
        <v-card>
            <v-card-title>{{ $t('Work Order Details') }}</v-card-title>
            <v-card-text>
                <div v-if="viewLoading" class="text-center py-4">
                    <v-progress-linear indeterminate color="primary"></v-progress-linear>
                    {{ $t('Loading...') }}
                </div>
                <div v-else-if="selectedWorkOrder" class="row">
                    <div class="col-md-6 mb-3">
                        <strong>{{ $t('Work Order No') }}:</strong> {{ selectedWorkOrder.work_order_no }}
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>{{ $t('Work Title') }}:</strong> {{ selectedWorkOrder.work_title }}
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>{{ $t('Customer') }}:</strong> {{ selectedWorkOrder.customer_name || '-' }}
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>{{ $t('Quotation') }}:</strong> {{ selectedWorkOrder.quotation_number || '-' }}
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>{{ $t('Issue Date') }}:</strong> {{ selectedWorkOrder.issue_date || '-' }}
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>{{ $t('Deliver Date') }}:</strong> {{ selectedWorkOrder.deliver_date || '-' }}
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>{{ $t('Total Amount') }}:</strong> {{ amount(selectedWorkOrder.total_amount) }}
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>{{ $t('Retention') }}:</strong> {{ amount(selectedWorkOrder.retention_amount) }}
                        ({{ Number(selectedWorkOrder.retention_percent) }}%)
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>{{ $t('Paid') }}:</strong> {{ amount(selectedWorkOrder.paid_amount) }}
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>{{ $t('Outstanding') }}:</strong> {{ amount(selectedWorkOrder.outstanding_amount) }}
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>{{ $t('Status') }}:</strong> {{ $t(selectedWorkOrder.status) }}
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>{{ $t('Inserted By') }}:</strong> {{ selectedWorkOrder.created_by_name || '-' }}
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>{{ $t('Approved By') }}:</strong> {{ selectedWorkOrder.approved_by_name || '-' }}
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>{{ $t('Attachment') }}:</strong>
                        <a v-if="selectedWorkOrder.attachment_url" :href="selectedWorkOrder.attachment_url"
                            target="_blank" rel="noopener">
                            {{ $t('Open attachment') }}
                        </a>
                        <span v-else>-</span>
                    </div>
                </div>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn class="text-none" color="grey-lighten-3" rounded="0" variant="flat" @click="viewDialog = false">
                    {{ $t('Close') }}
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>

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
import { usePrintable } from '@/composables/usePrintable';
import { useFetch } from '@/composables/useFetch';
import { usePermission } from '@/composables/usePermission';
import { WORK_ORDER_STATUSES } from './statuses';
import { t } from '@/i18n';

const { can } = usePermission()

const {
    items,
    loading,
    filters,
    pagination,
    fetchData,
    fetchAll,
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

const amount = (value) => Number(value || 0).toFixed(2)
const viewDialog = ref(false)
const viewLoading = ref(false)
const selectedWorkOrder = ref(null)

const openView = async (item) => {
    viewDialog.value = true
    viewLoading.value = true
    selectedWorkOrder.value = null

    try {
        const { data } = await axios.get(`/api/work-orders/${item.id}`)
        selectedWorkOrder.value = data.data
    } catch (e) {
        viewDialog.value = false
        toast.error(e.response?.data?.message || t('Failed to load work order details.'))
    } finally {
        viewLoading.value = false
    }
}

// Print every row matching the current filters in the shared report layout.
const { printTable } = usePrintable()
const printing = ref(false)
const print = async () => {
    printing.value = true
    try {
        printTable({
            title: t('Work Order List'),
            items: await fetchAll(),
            columns: [
                { key: 'index', label: '#' },
                { key: 'work_order_no', label: t('Work Order No') },
                { key: 'work_title', label: t('Work Title') },
                { key: 'customer_name', label: t('Customer') },
                { key: 'issue_date', label: t('Issue Date') },
                { key: 'deliver_date', label: t('Deliver Date') },
                { key: 'total_amount', label: t('Total Amount'), align: 'right', format: amount },
                { key: 'retention_amount', label: t('Retention'), align: 'right', format: amount },
                { key: 'paid_amount', label: t('Paid'), align: 'right', format: amount },
                { key: 'outstanding_amount', label: t('Outstanding'), align: 'right', format: amount },
                { key: 'status', label: t('Status'), format: (value) => t(value) },
                { key: 'created_by_name', label: t('Inserted By') },
                { key: 'approved_by_name', label: t('Approved By') },
            ],
        })
    } catch (e) {
        toast.error(e.response?.data?.message || t('Failed to load data for printing.'))
    } finally {
        printing.value = false
    }
}

const statusBadgeClass = (status) => ({
    'Pending': 'badge bg-warning text-dark',
    'In Progress': 'badge bg-info text-dark',
    'Completed': 'badge bg-success',
    'Cancelled': 'badge bg-secondary',
}[status] || 'badge bg-light text-dark')

const handleDelete = async (item) => {
    const result = await Swal.fire({
        title: t('Are you sure?'),
        text: t('Delete work order "{number}"? This cannot be undone.', { number: item.work_order_no }),
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: t('Yes, delete'),
        cancelButtonText: t('Cancel'),
    })

    if (!result.isConfirmed) return

    try {
        const resp = await axios.delete(`/api/work-orders/${item.id}`)
        if (resp.data.success) {
            toast.success(resp.data.message)
            fetchData()
        }
    } catch (e) {
        toast.error(e.response?.data?.message || t('Failed to delete work order.'))
    }
}

onMounted(() => {
    fetchData()
    loadCustomers()
})

</script>
