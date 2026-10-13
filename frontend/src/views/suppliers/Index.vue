<template lang="html">
    <Breadcrumb title="All Suppliers" buttonText="Add Supplier" :buttonLink="{ name: 'admin_supplier_add' }"
        buttonIcon="fa-solid fa-user-plus" />

    <div class="main-content-wrapper mt-4">
        <div class="container-fluid">

            <!-- Table Card -->
            <v-card style="padding: 5px; margin-top: 15px;">
                <div class="row">
                    <div class="col-md-12 mb-2">
                        <div class="row align-items-center">
                            <div class="col-md-5"></div>
                            <div class="col-md-3">
                                <select class="form-select" v-model="filters.active_status">
                                    <option value="">{{ $t('All Status') }}</option>
                                    <option value="1">{{ $t('Active') }}</option>
                                    <option value="0">{{ $t('Inactive') }}</option>
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
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Data Table START -->
                    <v-table class="custom-bordered">
                        <thead>
                            <tr>
                                <th class="text-left">#</th>
                                <th class="text-left">{{ $t('Name') }}</th>
                                <th class="text-left">{{ $t('Email') }}</th>
                                <th class="text-left">{{ $t('Phone') }}</th>
                                <th class="text-right">{{ $t('Balance') }}</th>
                                <th class="text-center">{{ $t('Status') }}</th>
                                <th class="text-center">{{ $t('Approval') }}</th>
                                <th class="text-center">{{ $t('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>

                            <!-- Loading State with Vuetify Spinner -->
                            <tr v-if="loading">
                                <td colspan="8" class="text-center py-4">
                                    <v-progress-linear indeterminate color="primary" size="30"></v-progress-linear>
                                    {{ $t('Loading...') }}
                                </td>
                            </tr>

                            <!-- No Data -->
                            <tr v-else-if="!items.length">
                                <td colspan="8" class="text-center py-4">
                                    {{ $t('No records found.') }}
                                </td>
                            </tr>

                            <!-- Data Rows -->
                            <tr v-else v-for="(item, index) in items" :key="item.id">
                                <td>{{ (pagination.page - 1) * pagination.perPage + index + 1 }}</td>
                                <td>{{ item.name }}</td>
                                <td>{{ item.email }}</td>
                                <td>{{ item.phone }}</td>
                                <td class="text-right">{{ Number(item.current_balance).toFixed(2) }}</td>
                                <td class="text-center">
                                    <span :class="item.active_status ? 'badge bg-success' : 'badge bg-secondary'">
                                        {{ $t(item.active_status ? 'Active' : 'Inactive') }}
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
                                            <li class="menu-item" v-if="!item.approved_by && can(['suppliers.approve'])">
                                                <button type="button" class="menu-link" @click="approve(item, 'supplier')">
                                                    {{ $t('Approve') }}
                                                </button>
                                            </li>
                                            <li class="menu-item">
                                                <router-link :to="{ name: 'admin_supplier_ledger', params: { id: item.id } }"
                                                    class="menu-link">
                                                    {{ $t('Ledger') }}
                                                </router-link>
                                            </li>
                                            <li class="menu-item">
                                                <router-link :to="{ name: 'admin_supplier_edit', params: { id: item.id } }"
                                                    class="menu-link">
                                                    {{ $t('Edit') }}
                                                </router-link>
                                            </li>
                                            <li class="menu-item">
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

</template>
<script setup>
import axios from 'axios';
import Swal from 'sweetalert2';
import { toast } from 'vue3-toastify';
import BasePagination from '@/components/common/BasePagination.vue';
import Breadcrumb from '@/components/common/Breadcrumb.vue';
import { usePermission } from '@/composables/usePermission';
import ApprovalBadge from '@/components/common/ApprovalBadge.vue';
import { useApproval } from '@/composables/useApproval';
import { usePaginatedFetch } from '@/composables/usePaginatedFetch';
import { t } from '@/i18n';

const {
    items,
    loading,
    filters,
    pagination,
    fetchData,
    changePage,
    changePerPage,
    resetFilters,
} = usePaginatedFetch('/api/suppliers', {
    search: '',
    active_status: '',
})

const { can } = usePermission()
const { approve } = useApproval('/api/suppliers', () => fetchData())

const handleDelete = async (item) => {
    const result = await Swal.fire({
        title: t('Are you sure?'),
        text: t('Delete supplier "{name}"? This cannot be undone.', { name: item.name }),
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: t('Yes, delete'),
        cancelButtonText: t('Cancel'),
    })

    if (!result.isConfirmed) return

    try {
        const resp = await axios.delete(`/api/suppliers/${item.id}`)
        if (resp.data.success) {
            toast.success(resp.data.message)
            fetchData()
        }
    } catch (e) {
        // A supplier with ledger transactions or purchases cannot be deleted (422).
        toast.error(e.response?.data?.message || t('Failed to delete supplier.'))
    }
}

onMounted(() => {
    fetchData()
})

</script>
