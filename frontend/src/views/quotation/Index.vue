<template lang="html">
    <div class="main-content-wrapper mt-4">
        <div class="container-fluid">

            <!-- Header: page title on the left, view buttons on the right -->
            <div class="d-flex justify-content-between align-items-center mt-3 mb-3">
                <h4 class="page-title mb-0">{{ $t(currentView.title) }}</h4>

                <div class="d-flex align-center gap-2">
                    <v-btn v-for="view in views" :key="view.key" class="text-none"
                        :class="{ 'text-white': activeView === view.key }" :color="view.color"
                        :variant="activeView === view.key ? 'flat' : 'outlined'" rounded="0" @click="setView(view.key)">
                        <i class="fa-solid me-1" :class="view.icon"></i> {{ $t(view.label) }}
                    </v-btn>
                </div>
            </div>


            <!-- View: list -->
            <template v-if="activeView === 'list'">
                <v-card style="padding: 5px; margin-top: 15px;">
                    <div class="row">
                        <div class="col-md-12 mb-2">
                            <div class="row align-items-center">
                                <div class="col-md-4"></div>
                                <div class="col-md-3">
                                    <select class="form-select" v-model="filters.customer_id">
                                        <option value="">{{ $t('All Customers') }}</option>
                                        <option v-for="customer in customers" :key="customer.id" :value="customer.id">
                                            {{ customer.name }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <div class="search-wrapper d-flex align-center gap-2">
                                        <v-text-field variant="outlined" density="compact"
                                            :placeholder="$t('Search invoice no...')" v-model="filters.search"
                                            hide-details class="flex-grow-1"></v-text-field>
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
                                    <th class="text-left">{{ $t('Invoice No') }}</th>
                                    <th class="text-left">{{ $t('Customer') }}</th>
                                    <th class="text-left">{{ $t('Issue Date') }}</th>
                                    <th class="text-right">{{ $t('Net Total') }}</th>
                                    <th class="text-center">{{ $t('File') }}</th>
                                    <th class="text-center">{{ $t('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="loading">
                                    <td colspan="7" class="text-center py-4">
                                        <v-progress-linear indeterminate color="primary" size="30"></v-progress-linear>
                                        {{ $t('Loading...') }}
                                    </td>
                                </tr>
                                <tr v-else-if="!items.length">
                                    <td colspan="7" class="text-center py-4">
                                        {{ $t('No records found.') }}
                                    </td>
                                </tr>
                                <tr v-else v-for="(item, index) in items" :key="item.id">
                                    <td>{{ (pagination.page - 1) * pagination.perPage + index + 1 }}</td>
                                    <td>{{ item.invoice_number }}</td>
                                    <td>{{ item.customer_name }}</td>
                                    <td>{{ item.issue_date }}</td>
                                    <td class="text-right">{{ Number(item.net_total).toFixed(2) }}</td>
                                    <td class="text-center">
                                        <a v-if="item.file_url" :href="item.file_url" target="_blank" rel="noopener"
                                            :title="$t('Open attachment')">
                                            <i class="fa-solid fa-paperclip"></i>
                                        </a>
                                        <span v-else>-</span>
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
                                                    <button type="button" class="menu-link"
                                                        @click="handlePrintInvoice(item)">
                                                        {{ $t('Print Quotation') }}
                                                    </button>
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
            </template>

            <!-- View: add -->
            <AddQuotationForm v-else-if="activeView === 'add'" @saved="onQuotationSaved" @cancel="setView('list')" />

            <!--
                To add one more button + component:
                  1) add an entry to `views` in the script,
                  2) import the component,
                  3) add a line here, for example:
                     <QuotationReport v-else-if="activeView === 'report'" />
            -->
        </div>
    </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';
import { toast } from 'vue3-toastify';
import BasePagination from '@/components/common/BasePagination.vue';
import { usePaginatedFetch } from '@/composables/usePaginatedFetch';
import { useFetch } from '@/composables/useFetch';
import { usePrintable } from '@/composables/usePrintable';
import { t } from '@/i18n';

import AddQuotationForm from './Add.vue';

// One entry per button in the header. `key` decides which component is shown.
const views = [
    { key: 'list', label: 'Search', title: 'All Quotation', icon: 'fa-search', color: 'blue-darken-3' },
    { key: 'add', label: 'New', title: 'Add New Quotation', icon: 'fa-plus', color: 'green-darken-3' },
];

const activeView = ref('list');

const currentView = computed(() => views.find((view) => view.key === activeView.value) ?? views[0]);

const setView = (key) => {
    activeView.value = key;

    // The list may be stale after adding a record, so reload it when coming back
    if (key === 'list') fetchData();
};

const onQuotationSaved = () => {
    setView('list');
};

const { printInvoice } = usePrintable();

const {
    items,
    loading,
    filters,
    pagination,
    fetchData,
    changePage,
    changePerPage,
    resetFilters,
} = usePaginatedFetch('/api/quotations', {
    search: '',
    customer_id: '',
});

const { items: customers, fetchData: loadCustomers } = useFetch('/api/customers', { per_page: 100 });

const handlePrintInvoice = async (item) => {
    try {
        const { data } = await axios.get(`/api/quotations/${item.id}`);
        if (!data.success) return;

        const quotation = data.data;

        printInvoice({
            title: 'Quotation',
            partyLabel: 'Customer:',
            partyName: quotation.customer_name,
            meta: [
                { label: 'Quotation No:', value: quotation.invoice_number },
                { label: 'Issue Date:', value: quotation.issue_date },
            ],
            items: quotation.items ?? [],
            totals: quotation,
            notes: quotation.notes,
        });
    } catch (e) {
        toast.error(e.response?.data?.message || t('Failed to load the quotation.'));
    }
};

const handleDelete = async (item) => {
    const result = await Swal.fire({
        title: t('Are you sure?'),
        text: t('Delete quotation "{invoice}"?', { invoice: item.invoice_number }),
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: t('Yes, delete'),
        cancelButtonText: t('Cancel'),
    });

    if (!result.isConfirmed) return;

    try {
        const resp = await axios.delete(`/api/quotations/${item.id}`);

        if (resp.data.success) {
            toast.success(resp.data.message);
            fetchData();
        }
    } catch (e) {
        toast.error(e.response?.data?.message || t('Failed to delete quotation.'));
    }
};

onMounted(() => {
    fetchData();
    loadCustomers();
});
</script>