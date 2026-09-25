<template lang="html">
    <Breadcrumb title="Internal Fund Transfers" buttonText="New Transfer" :buttonLink="{ name: 'admin_fund_transfer_add' }"
        buttonIcon="fa-solid fa-right-left" />

    <div class="main-content-wrapper mt-4">
        <div class="container-fluid">

            <!-- Table Card -->
            <v-card style="padding: 5px; margin-top: 15px;">
                <div class="row">
                    <div class="col-md-12 mb-2">
                        <div class="row align-items-center">
                            <div class="col-md-3">
                                <input type="text" class="form-control" v-model="filters.search"
                                    placeholder="Search receipt no or remarks" />
                            </div>
                            <div class="col-md-1"></div>
                            <div class="col-md-2">
                                <select class="form-select" v-model="filters.account_id">
                                    <option value="">All Accounts</option>
                                    <option v-for="account in assetAccounts" :key="account.id" :value="account.id">
                                        {{ account.account_number }} {{ account.name }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="date" class="form-control" v-model="filters.from_date" placeholder="From" />
                            </div>
                            <div class="col-md-3">
                                <input type="date" class="form-control" v-model="filters.to_date" placeholder="To" />
                            </div>
                        </div>
                    </div>

                    <!-- Data Table START -->
                    <v-table class="custom-bordered">
                        <thead>
                            <tr>
                                <th class="text-left">S.N</th>
                                <th class="text-left">Date</th>
                                <th class="text-left">Receipt No</th>
                                <th class="text-left">Sender (CR)</th>
                                <th class="text-left">Receiver (DR)</th>
                                <th class="text-right">Amount</th>
                                <th class="text-right">Bank Charge</th>
                                <th class="text-right">VAT</th>
                                <th class="text-right">Total</th>
                                <th class="text-left">Remarks</th>
                                <th class="text-left">Created</th>
                                <th class="text-center">Approval</th>
                                <th class="text-center">Manage</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="loading">
                                <td colspan="13" class="text-center py-4">
                                    <v-progress-linear indeterminate color="primary" size="30"></v-progress-linear>
                                    Loading...
                                </td>
                            </tr>
                            <tr v-else-if="!items.length">
                                <td colspan="13" class="text-center py-4">No data available in table</td>
                            </tr>
                            <tr v-else v-for="(item, index) in items" :key="item.id">
                                <td>{{ (pagination.page - 1) * pagination.perPage + index + 1 }}</td>
                                <td>{{ item.transfer_date }}</td>
                                <td>{{ item.receipt_no || 'N/A' }}</td>
                                <td>{{ item.credit_account_number }} {{ item.credit_account_name }}</td>
                                <td>{{ item.debit_account_number }} {{ item.debit_account_name }}</td>
                                <td class="text-right">{{ Number(item.amount).toFixed(2) }}</td>
                                <td class="text-right">{{ Number(item.bank_charge).toFixed(2) }}</td>
                                <td class="text-right">{{ Number(item.vat).toFixed(2) }}</td>
                                <td class="text-right fw-bold">{{ Number(item.total_amount).toFixed(2) }}</td>
                                <td>{{ item.remarks }}</td>
                                <td>{{ item.created_by_name || 'Unknown' }}</td>
                                <td class="text-center">
                                    <ApprovalBadge :approved-by="item.approved_by" :approved-at="item.approved_at" />
                                </td>
                                <td class="text-center">
                                    <v-menu>
                                        <template v-slot:activator="{ props }">
                                            <button type="button" class="table-action-button" v-bind="props">
                                                <i class="fa-solid fa-bars"></i>
                                            </button>
                                        </template>
                                        <ul class="table-action-menu">
                                            <li class="menu-item"
                                                v-if="!item.approved_by && can(['fund-transfers.approve'])">
                                                <button type="button" class="menu-link" @click="approve(item, 'transfer')">
                                                    Approve
                                                </button>
                                            </li>
                                            <li class="menu-item" v-if="item.attachment_url">
                                                <a :href="item.attachment_url" target="_blank" class="menu-link">
                                                    Attachment
                                                </a>
                                            </li>
                                            <li class="menu-item" v-if="can(['fund-transfers.delete'])">
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
import ApprovalBadge from '@/components/common/ApprovalBadge.vue';
import BasePagination from '@/components/common/BasePagination.vue';
import Breadcrumb from '@/components/common/Breadcrumb.vue';
import { useApproval } from '@/composables/useApproval';
import { useFetch } from '@/composables/useFetch';
import { usePaginatedFetch } from '@/composables/usePaginatedFetch';
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
} = usePaginatedFetch('/api/accounting/internal-fund-transfer/list', {
    search: '',
    account_id: '',
    from_date: '',
    to_date: '',
})

// Account type id of Asset, as seeded by the backend (account_types table).
const ASSET = 1

const { items: assetAccounts, fetchData: loadAssetAccounts } = useFetch('/api/ledger-accounts', {
    per_page: 500,
    account_type_id: ASSET,
    is_transaction: 1,
})

watch(() => [filters.search, filters.account_id, filters.from_date, filters.to_date], fetchData)

const { approve } = useApproval('/api/accounting/internal-fund-transfer', () => fetchData())

const handleDelete = async (item) => {
    const result = await Swal.fire({
        title: 'Are you sure?',
        text: `Delete this transfer of ${Number(item.total_amount).toFixed(2)}? All its postings will be reversed.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete',
        cancelButtonText: 'Cancel',
    })

    if (!result.isConfirmed) return

    try {
        const resp = await axios.delete(`/api/accounting/internal-fund-transfer/${item.id}`)
        if (resp.data.success) {
            toast.success(resp.data.message)
            fetchData()
        }
    } catch (e) {
        toast.error(e.response?.data?.message || 'Failed to delete transfer.')
    }
}

onMounted(() => {
    loadAssetAccounts()
    fetchData()
})
</script>
