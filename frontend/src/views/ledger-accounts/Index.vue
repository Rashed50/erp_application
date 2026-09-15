<template lang="html">
    <Breadcrumb title="Ledger Accounts" buttonText="Add Account" :buttonLink="{ name: 'admin_ledger_account_add' }"
        buttonIcon="fa-solid fa-book" />

    <div class="main-content-wrapper mt-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-2"></div>
                <div class="col-md-8">
                    <!-- Table Card -->
                    <v-card style="padding: 5px; margin-top: 15px;">
                        <div class="row">
                            <div class="col-md-12 mb-2">
                                <div class="row align-items-center">
                                    <div class="col-md-6"></div>
                                    <div class="col-md-3">
                                        <select class="form-select" v-model="filters.type">
                                            <option value="">All Types</option>
                                            <option value="asset">Asset</option>
                                            <option value="income">Income</option>
                                            <option value="expense">Expense</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
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
                                        <th class="text-left">Name</th>
                                        <th class="text-left">Type</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="loading">
                                        <td colspan="5" class="text-center py-4">
                                            <v-progress-linear indeterminate color="primary" size="30"></v-progress-linear>
                                            Loading...
                                        </td>
                                    </tr>
                                    <tr v-else-if="!items.length">
                                        <td colspan="5" class="text-center py-4">No records found.</td>
                                    </tr>
                                    <tr v-else v-for="(item, index) in items" :key="item.id">
                                        <td>{{ (pagination.page - 1) * pagination.perPage + index + 1 }}</td>
                                        <td>{{ item.name }}</td>
                                        <td class="text-capitalize">{{ item.type }}</td>
                                        <td class="text-center">
                                            <span :class="item.active_status ? 'badge bg-success' : 'badge bg-secondary'">
                                                {{ item.active_status ? 'Active' : 'Inactive' }}
                                            </span>
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
                                                        <router-link
                                                            :to="{ name: 'admin_ledger_account_edit', params: { id: item.id } }"
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
                </div>
            </div>
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

const {
    items,
    loading,
    filters,
    pagination,
    fetchData,
    changePage,
    changePerPage,
} = usePaginatedFetch('/api/ledger-accounts', {
    type: '',
    active_status: '',
})

watch(() => [filters.type, filters.active_status], fetchData)

const handleDelete = async (item) => {
    const result = await Swal.fire({
        title: 'Are you sure?',
        text: `Delete account "${item.name}"? This cannot be undone.`,
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
        // An account referenced by a transaction cannot be deleted (422).
        toast.error(e.response?.data?.message || 'Failed to delete account.')
    }
}

onMounted(() => {
    fetchData()
})

</script>
