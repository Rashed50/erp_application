<template lang="html">
    <Breadcrumb title="All Purchases" buttonText="Add Purchase" :buttonLink="{ name: 'admin_purchase_add' }"
        buttonIcon="fa-solid fa-cart-plus" />

    <div class="main-content-wrapper mt-4">
        <div class="container-fluid">

            <!-- Table Card -->
            <v-card style="padding: 5px; margin-top: 15px;">
                <div class="row">
                    <div class="col-md-12 mb-2">
                        <div class="row align-items-center">
                            <div class="col-md-4"></div>
                            <div class="col-md-3">
                                <select class="form-select" v-model="filters.supplier_id">
                                    <option value="">All Suppliers</option>
                                    <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">
                                        {{ supplier.name }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-5">
                                <div class="search-wrapper d-flex align-center gap-2">
                                    <v-text-field variant="outlined" density="compact" placeholder="Search invoice no..."
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
                                <th class="text-left">Invoice No</th>
                                <th class="text-left">Supplier</th>
                                <th class="text-left">Type</th>
                                <th class="text-left">Purchase Date</th>
                                <th class="text-right">Net Total</th>
                                <th class="text-right">Due</th>
                                <th class="text-center">Approval</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>

                            <!-- Loading State with Vuetify Spinner -->
                            <tr v-if="loading">
                                <td colspan="9" class="text-center py-4">
                                    <v-progress-linear indeterminate color="primary" size="30"></v-progress-linear>
                                    Loading...
                                </td>
                            </tr>

                            <!-- No Data -->
                            <tr v-else-if="!items.length">
                                <td colspan="9" class="text-center py-4">
                                    No records found.
                                </td>
                            </tr>

                            <!-- Data Rows -->
                            <tr v-else v-for="(item, index) in items" :key="item.id">
                                <td>{{ (pagination.page - 1) * pagination.perPage + index + 1 }}</td>
                                <td>{{ item.invoice_number }}</td>
                                <td>{{ item.supplier_name }}</td>
                                <td class="text-capitalize">{{ item.purchase_type }}</td>
                                <td>{{ item.purchase_date }}</td>
                                <td class="text-right">{{ Number(item.net_total).toFixed(2) }}</td>
                                <td class="text-right">
                                    <span :class="item.due_amount > 0 ? 'badge bg-danger' : 'badge bg-success'">
                                        {{ Number(item.due_amount).toFixed(2) }}
                                    </span>
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
                                            <li class="menu-item" v-if="!item.approved_by && can(['purchases.approve'])">
                                                <button type="button" class="menu-link" @click="approve(item, 'purchase')">
                                                    Approve
                                                </button>
                                            </li>
                                            <li class="menu-item" v-if="item.due_amount > 0 && can(['purchase-payments.create'])">
                                                <button type="button" class="menu-link" @click="openPaymentDialog(item)">
                                                    Record Payment
                                                </button>
                                            </li>
                                            <li class="menu-item">
                                                <router-link :to="{ name: 'admin_purchase_edit', params: { id: item.id } }"
                                                    class="menu-link">
                                                    Edit
                                                </router-link>
                                            </li>
                                            <li class="menu-item">
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

            <PaymentDialog v-model="paymentDialogOpen" :endpoint="`/api/purchases/${selectedPurchase?.id}/payments`"
                :due-amount="selectedPurchase?.due_amount ?? 0" title="Record Bill Payment" with-payment-account
                @recorded="fetchData" />

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
import PaymentDialog from '@/components/common/PaymentDialog.vue';
import { usePaginatedFetch } from '@/composables/usePaginatedFetch';
import { useFetch } from '@/composables/useFetch';
import { usePermission } from '@/composables/usePermission';

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
} = usePaginatedFetch('/api/purchases', {
    search: '',
    supplier_id: '',
})

// supplier dropdown for the filter
const { items: suppliers, fetchData: loadSuppliers } = useFetch('/api/suppliers', { per_page: 100 })

const paymentDialogOpen = ref(false)
const selectedPurchase = ref(null)

const openPaymentDialog = (item) => {
    selectedPurchase.value = item
    paymentDialogOpen.value = true
}

const { approve } = useApproval('/api/purchases', () => fetchData())

const handleDelete = async (item) => {
    const result = await Swal.fire({
        title: 'Are you sure?',
        text: `Delete purchase "${item.invoice_number}"? This reverses its supplier ledger entry.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete',
        cancelButtonText: 'Cancel',
    })

    if (!result.isConfirmed) return

    try {
        const resp = await axios.delete(`/api/purchases/${item.id}`)
        if (resp.data.success) {
            toast.success(resp.data.message)
            fetchData()
        }
    } catch (e) {
        toast.error(e.response?.data?.message || 'Failed to delete purchase.')
    }
}

onMounted(() => {
    fetchData()
    loadSuppliers()
})

</script>
