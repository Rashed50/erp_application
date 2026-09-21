<template lang="html">
    <Breadcrumb title="Chart of Accounts" buttonText="Add Account" :buttonLink="{ name: 'admin_ledger_account_add' }"
        buttonIcon="fa-solid fa-book" />

    <div class="main-content-wrapper mt-4">
        <div class="container-fluid">
            <!-- Table Card -->
            <v-card style="padding: 5px; margin-top: 15px;">
                <div class="row">
                    <div class="col-md-12 mb-2">
                        <div class="row align-items-center">
                            <div class="col-md-3">
                                <input type="text" class="form-control" v-model="filters.search"
                                    placeholder="Search name or number" />
                            </div>
                            <div class="col-md-3"></div>
                            <div class="col-md-2">
                                <select class="form-select" v-model="filters.account_type_id">
                                    <option value="">All Types</option>
                                    <option v-for="type in accountTypes" :key="type.id" :value="type.id">
                                        {{ type.name }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select class="form-select" v-model="filters.is_transaction">
                                    <option value="">Groups &amp; Transaction</option>
                                    <option value="0">Groups only</option>
                                    <option value="1">Transaction only</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select class="form-select" v-model="filters.active_status">
                                    <option value="">All Status</option>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Data Table START -->
                    <v-table class="custom-bordered">
                        <thead>
                            <tr>
                                <th class="text-left">#</th>
                                <th class="text-left">Number</th>
                                <th class="text-left">Name</th>
                                <th class="text-left">Type</th>
                                <th class="text-left">Parent</th>
                                <th class="text-right">Balance</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Approval</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="loading">
                                <td colspan="9" class="text-center py-4">
                                    <v-progress-linear indeterminate color="primary" size="30"></v-progress-linear>
                                    Loading...
                                </td>
                            </tr>
                            <tr v-else-if="!items.length">
                                <td colspan="9" class="text-center py-4">No records found.</td>
                            </tr>
                            <tr v-else v-for="(item, index) in items" :key="item.id">
                                <td>{{ (pagination.page - 1) * pagination.perPage + index + 1 }}</td>
                                <td>{{ item.account_number }}</td>
                                <td :style="{ paddingLeft: `${item.sibling_level * 20 + 16}px` }">
                                    <span :class="{ 'fw-bold': !item.is_transaction }">{{ item.name }}</span>
                                    <span v-if="!item.is_transaction" class="badge bg-info text-dark ms-2">Group</span>
                                    <span v-if="item.is_predefined" class="badge bg-secondary ms-1">Predefined</span>
                                    <span v-if="item.is_closed" class="badge bg-dark ms-1">Closed</span>
                                </td>
                                <td>{{ item.account_type }}</td>
                                <td>{{ item.parent_name }}</td>
                                <td class="text-right">{{ Number(item.balance).toFixed(2) }}</td>
                                <td class="text-center">
                                    <span :class="item.active_status ? 'badge bg-success' : 'badge bg-secondary'">
                                        {{ item.active_status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
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
                                                v-if="!item.approved_by && can(['ledger-accounts.approve'])">
                                                <button type="button" class="menu-link"
                                                    @click="approve(item, 'account')">
                                                    Approve
                                                </button>
                                            </li>
                                            <li class="menu-item">
                                                <router-link
                                                    :to="{ name: 'admin_ledger_account_edit', params: { id: item.id } }"
                                                    class="menu-link">
                                                    Edit
                                                </router-link>
                                            </li>
                                            <li class="menu-item" v-if="!item.is_predefined">
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
} = usePaginatedFetch('/api/ledger-accounts', {
    search: '',
    account_type_id: '',
    is_transaction: '',
    active_status: '',
}, { perPage: 50 })

const { items: accountTypes, fetchData: loadAccountTypes } = useFetch('/api/account-types')

watch(() => [filters.search, filters.account_type_id, filters.is_transaction, filters.active_status], fetchData)

const { approve } = useApproval('/api/ledger-accounts', () => fetchData())

const handleDelete = async (item) => {
    const result = await Swal.fire({
        title: 'Are you sure?',
        text: `Delete account "${item.name}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete',
        cancelButtonText: 'Cancel',
    })

    if (!result.isConfirmed) return

    try {
        const resp = await axios.delete(`/api/ledger-accounts/${item.id}`)
        if (resp.data.success) {
            toast.success(resp.data.message)
            fetchData()
        }
    } catch (e) {
        // Predefined accounts, accounts with children, or with posted entries cannot be deleted (422).
        toast.error(e.response?.data?.message || 'Failed to delete account.')
    }
}

onMounted(() => {
    loadAccountTypes()
    fetchData()
})

</script>
