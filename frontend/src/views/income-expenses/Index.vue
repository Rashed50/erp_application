<template lang="html">
    <Breadcrumb title="Income &amp; Expenses" buttonText="Add Entry" :buttonLink="{ name: 'admin_income_expense_add' }"
        buttonIcon="fa-solid fa-money-bill-transfer" />

    <div class="main-content-wrapper mt-4">
        <div class="container-fluid">

            <!-- Table Card -->
            <v-card style="padding: 5px; margin-top: 15px;">
                <div class="row">
                    <div class="col-md-12 mb-2">
                        <div class="row align-items-center">
                            <div class="col-md-4"></div>
                            <div class="col-md-2">
                                <select class="form-select" v-model="filters.type">
                                    <option value="">All Types</option>
                                    <option value="income">Income</option>
                                    <option value="expense">Expense</option>
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
                                <th class="text-left">#</th>
                                <th class="text-left">Date</th>
                                <th class="text-center">Type</th>
                                <th class="text-left">Category Account</th>
                                <th class="text-left">Payment Account</th>
                                <th class="text-right">Amount</th>
                                <th class="text-left">Reference No</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="loading">
                                <td colspan="8" class="text-center py-4">
                                    <v-progress-linear indeterminate color="primary" size="30"></v-progress-linear>
                                    Loading...
                                </td>
                            </tr>
                            <tr v-else-if="!items.length">
                                <td colspan="8" class="text-center py-4">No records found.</td>
                            </tr>
                            <tr v-else v-for="(item, index) in items" :key="item.id">
                                <td>{{ (pagination.page - 1) * pagination.perPage + index + 1 }}</td>
                                <td>{{ item.transaction_date }}</td>
                                <td class="text-center">
                                    <span :class="item.type === 'income' ? 'badge bg-success' : 'badge bg-danger'">
                                        {{ item.type === 'income' ? 'Income' : 'Expense' }}
                                    </span>
                                </td>
                                <td>{{ item.account_name }}</td>
                                <td>{{ item.payment_account_name }}</td>
                                <td class="text-right">{{ Number(item.amount).toFixed(2) }}</td>
                                <td>{{ item.reference_no }}</td>
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
                                                    :to="{ name: 'admin_income_expense_edit', params: { id: item.id } }"
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
} = usePaginatedFetch('/api/income-expenses', {
    type: '',
    from_date: '',
    to_date: '',
})

watch(() => [filters.type, filters.from_date, filters.to_date], fetchData)

const handleDelete = async (item) => {
    const result = await Swal.fire({
        title: 'Are you sure?',
        text: 'Delete this entry? This cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete',
        cancelButtonText: 'Cancel',
    })

    if (!result.isConfirmed) return

    try {
        const resp = await axios.delete(`/api/income-expenses/${item.id}`)
        if (resp.data.success) {
            toast.success(resp.data.message)
            fetchData()
        }
    } catch (e) {
        toast.error(e.response?.data?.message || 'Failed to delete entry.')
    }
}

onMounted(() => {
    fetchData()
})

</script>
