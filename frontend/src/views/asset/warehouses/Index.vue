<template lang="html">
    <Breadcrumb title="Stores / Warehouses" />

    <div class="main-content-wrapper mt-4">
        <div class="container-fluid">
            <v-card style="padding: 5px; margin-top: 15px;">
                <div class="row">
                    <div class="col-md-12 mb-2">
                        <div class="row align-items-center g-2">
                            <div class="col-md-2">
                                <v-btn v-if="can(['warehouses.create'])" type="button" @click="openAddDialog"
                                    class="text-none text-white" color="success" rounded="0" variant="flat">
                                    <i class="fa-solid fa-plus me-2"></i> {{ $t('Add Store') }}
                                </v-btn>
                            </div>
                            <div class="col-md-2">
                                <select class="form-select" v-model="filters.warehouse_type">
                                    <option value="">{{ $t('All Types') }}</option>
                                    <option v-for="type in WAREHOUSE_TYPES" :key="type.id" :value="type.id">
                                        {{ $t(type.name) }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select class="form-select" v-model="filters.status">
                                    <option value="">{{ $t('All Status') }}</option>
                                    <option value="1">{{ $t('Active') }}</option>
                                    <option value="0">{{ $t('Inactive') }}</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <div class="search-wrapper d-flex align-center gap-2">
                                    <v-text-field variant="outlined" density="compact" :placeholder="$t('Search...')"
                                        v-model="filters.search" hide-details class="flex-grow-1"></v-text-field>
                                    <v-btn type="button" @click="fetchData" class="text-none text-white"
                                        color="blue-darken-3" rounded="0" variant="flat" min-width="90">
                                        {{ $t('Search') }}
                                    </v-btn>
                                    <v-btn @click.prevent="resetFilters" class="text-none" color="grey-lighten-3"
                                        rounded="0" variant="flat" min-width="90">
                                        {{ $t('Reload') }}
                                    </v-btn>
                                </div>
                            </div>
                        </div>
                    </div>

                    <v-table class="custom-bordered">
                        <thead>
                            <tr>
                                <th class="text-left">#</th>
                                <th class="text-left">{{ $t('Code') }}</th>
                                <th class="text-left">{{ $t('Store Name') }}</th>
                                <th class="text-left">{{ $t('Type') }}</th>
                                <th class="text-left">{{ $t('Parent Store') }}</th>
                                <th class="text-left">{{ $t('Store Manager') }}</th>
                                <th class="text-center">{{ $t('Status') }}</th>
                                <th class="text-center">{{ $t('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="loading">
                                <td colspan="8" class="text-center py-4">
                                    <v-progress-linear indeterminate color="primary"></v-progress-linear>
                                    {{ $t('Loading...') }}
                                </td>
                            </tr>
                            <tr v-else-if="!items.length">
                                <td colspan="8" class="text-center py-4">{{ $t('No records found.') }}</td>
                            </tr>
                            <tr v-else v-for="(warehouse, index) in items" :key="warehouse.id">
                                <td>{{ (pagination.page - 1) * pagination.perPage + index + 1 }}</td>
                                <td>{{ warehouse.warehouse_code }}</td>
                                <td>{{ warehouse.name }}</td>
                                <td>{{ $t(warehouse.warehouse_type_name) }}</td>
                                <td>{{ warehouse.parent_name || '-' }}</td>
                                <td>{{ warehouse.manager_employee_name || '-' }}</td>
                                <td class="text-center">
                                    <span :class="warehouse.status ? 'badge bg-success' : 'badge bg-secondary'">
                                        {{ $t(warehouse.status ? 'Active' : 'Inactive') }}
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
                                                <button type="button" class="menu-link" @click="openStock(warehouse)">
                                                    {{ $t('View Stock') }}
                                                </button>
                                            </li>
                                            <li class="menu-item" v-if="can(['warehouses.update'])">
                                                <button type="button" class="menu-link"
                                                    @click="openEditDialog(warehouse)">
                                                    {{ $t('Edit') }}
                                                </button>
                                            </li>
                                        </ul>
                                    </v-menu>
                                </td>
                            </tr>
                        </tbody>
                    </v-table>

                    <BasePagination :current-page="pagination.page" :per-page="pagination.perPage"
                        :total="pagination.total" :last-page="pagination.lastPage" @page-change="changePage"
                        @per-page-change="changePerPage" />
                </div>
            </v-card>
        </div>
    </div>

    <!-- Add / Edit Store -->
    <v-dialog v-model="dialog" max-width="650">
        <v-card>
            <v-card-title>{{ $t(editingId ? 'Update Store' : 'Add Store') }}</v-card-title>
            <v-card-text>
                <div class="row">
                    <div class="col-md-6 form-group mb-3">
                        <label for="wh_code">{{ $t('Code:') }} <span class="text-danger">*</span></label>
                        <input type="text" id="wh_code" class="form-control" v-model="form.warehouse_code"
                            maxlength="50" />
                        <div v-if="errors.warehouse_code" class="error-msg">{{ errors.warehouse_code }}</div>
                    </div>
                    <div class="col-md-6 form-group mb-3">
                        <label for="wh_name">{{ $t('Store Name:') }} <span class="text-danger">*</span></label>
                        <input type="text" id="wh_name" class="form-control" v-model="form.name" maxlength="200" />
                        <div v-if="errors.name" class="error-msg">{{ errors.name }}</div>
                    </div>
                    <div class="col-md-6 form-group mb-3">
                        <label for="wh_type">{{ $t('Store Type:') }} <span class="text-danger">*</span></label>
                        <select id="wh_type" class="form-select" v-model="form.warehouse_type">
                            <option v-for="type in WAREHOUSE_TYPES" :key="type.id" :value="type.id">
                                {{ $t(type.name) }}
                            </option>
                        </select>
                        <div v-if="errors.warehouse_type" class="error-msg">{{ errors.warehouse_type }}</div>
                    </div>
                    <div class="col-md-6 form-group mb-3">
                        <label for="wh_parent">{{ $t('Parent Store:') }}</label>
                        <SearchSelect id="wh_parent" v-model="form.parent_id" :items="parentOptions"
                            :item-title="(w) => `${w.warehouse_code} - ${w.name}`" :error="errors.parent_id"
                            :placeholder="$t('None')" />
                    </div>
                    <div class="col-md-6 form-group mb-3">
                        <label for="wh_manager">{{ $t('Store Manager:') }}</label>
                        <SearchSelect id="wh_manager" v-model="form.manager_employee_id" :items="lookups.employees"
                            :item-title="(e) => `${e.employee_code} - ${e.name}`" :error="errors.manager_employee_id"
                            :placeholder="$t('None')" />
                    </div>
                    <div class="col-md-6 form-group mb-3">
                        <label for="wh_status">{{ $t('Status:') }}</label>
                        <select id="wh_status" class="form-select" v-model="form.status">
                            <option :value="true">{{ $t('Active') }}</option>
                            <option :value="false">{{ $t('Inactive') }}</option>
                        </select>
                    </div>
                    <div class="col-md-12 form-group mb-3">
                        <label for="wh_address">{{ $t('Address:') }}</label>
                        <input type="text" id="wh_address" class="form-control" v-model="form.address"
                            maxlength="255" />
                        <div v-if="errors.address" class="error-msg">{{ errors.address }}</div>
                    </div>
                </div>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn class="text-none" color="grey-lighten-3" rounded="0" variant="flat" @click="dialog = false">
                    {{ $t('Cancel') }}
                </v-btn>
                <v-btn class="text-none text-white" color="blue-darken-4" rounded="0" variant="flat"
                    :disabled="isSubmitting" :loading="isSubmitting" @click="handleSubmit">
                    {{ $t(editingId ? 'Update' : 'Save') }}
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>

    <!-- Current stock of one store -->
    <v-dialog v-model="stockDialog" max-width="800">
        <v-card>
            <v-card-title>{{ $t('Stock in {name}', { name: stockWarehouse?.name || '' }) }}</v-card-title>
            <v-card-text>
                <v-progress-linear v-if="stockLoading" indeterminate color="primary"></v-progress-linear>
                <v-table v-else class="custom-bordered">
                    <thead>
                        <tr>
                            <th class="text-left">{{ $t('Item') }}</th>
                            <th class="text-right">{{ $t('Quantity') }}</th>
                            <th class="text-right">{{ $t('Available') }}</th>
                            <th class="text-left">{{ $t('Unit') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="!stocks.length">
                            <td colspan="4" class="text-center py-4">{{ $t('No stock in this store.') }}</td>
                        </tr>
                        <tr v-for="stock in stocks" :key="stock.id">
                            <td>{{ stock.item_code }} - {{ stock.item_name }}</td>
                            <td class="text-right">{{ qty(stock.quantity) }}</td>
                            <td class="text-right">{{ qty(stock.available_quantity) }}</td>
                            <td>{{ stock.unit_name || '-' }}</td>
                        </tr>
                    </tbody>
                </v-table>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn class="text-none" color="grey-lighten-3" rounded="0" variant="flat"
                    @click="stockDialog = false">
                    {{ $t('Close') }}
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
<script setup>
import axios from 'axios';
import { toast } from 'vue3-toastify';
import BasePagination from '@/components/common/BasePagination.vue';
import Breadcrumb from '@/components/common/Breadcrumb.vue';
import SearchSelect from '@/components/common/SearchSelect.vue';
import { usePermission } from '@/composables/usePermission';
import { usePaginatedFetch } from '@/composables/usePaginatedFetch';
import { t } from '@/i18n';
import { WAREHOUSE_TYPES, fillValidationErrors, qty, useInventoryLookups } from '@/views/asset/helpers';

const { items, loading, filters, pagination, fetchData, changePage, changePerPage, resetFilters } =
    usePaginatedFetch('/api/asset/warehouses', { search: '', status: '', warehouse_type: '' })

const { can } = usePermission()
const { lookups, loadLookups } = useInventoryLookups()

/* ---------- add / edit ---------- */
const initialForm = () => ({
    warehouse_code: '',
    name: '',
    warehouse_type: WAREHOUSE_TYPES[0].id,
    parent_id: '',
    manager_employee_id: '',
    address: '',
    status: true,
})

const dialog = ref(false)
const editingId = ref(null)
const form = reactive(initialForm())
const errors = reactive({})
const isSubmitting = ref(false)

// A store cannot be its own parent.
const parentOptions = computed(() => lookups.warehouses.filter(w => w.id !== editingId.value))

const clearErrors = () => {
    for (const key in errors) delete errors[key]
}

const openAddDialog = () => {
    editingId.value = null
    Object.assign(form, initialForm())
    clearErrors()
    dialog.value = true
}

const openEditDialog = (warehouse) => {
    editingId.value = warehouse.id
    Object.assign(form, {
        warehouse_code: warehouse.warehouse_code,
        name: warehouse.name,
        warehouse_type: warehouse.warehouse_type,
        parent_id: warehouse.parent_id ?? '',
        manager_employee_id: warehouse.manager_employee_id ?? '',
        address: warehouse.address ?? '',
        status: warehouse.status,
    })
    clearErrors()
    dialog.value = true
}

const handleSubmit = async () => {
    clearErrors()
    if (!form.warehouse_code) errors.warehouse_code = t('The code field is required.')
    if (!form.name) errors.name = t('The name field is required.')
    if (Object.keys(errors).length) return

    isSubmitting.value = true
    const payload = { ...form, parent_id: form.parent_id || null, manager_employee_id: form.manager_employee_id || null }

    try {
        const { data } = editingId.value
            ? await axios.put(`/api/asset/warehouses/${editingId.value}`, payload)
            : await axios.post('/api/asset/warehouses', payload)

        if (data.success) {
            toast.success(data.message)
            dialog.value = false
            fetchData()
            loadLookups()
        }
    } catch (err) {
        fillValidationErrors(err, errors)
    } finally {
        isSubmitting.value = false
    }
}

/* ---------- stock view ---------- */
const stockDialog = ref(false)
const stockLoading = ref(false)
const stockWarehouse = ref(null)
const stocks = ref([])

const openStock = async (warehouse) => {
    stockWarehouse.value = warehouse
    stocks.value = []
    stockDialog.value = true
    stockLoading.value = true
    try {
        const { data } = await axios.get(`/api/asset/warehouses/${warehouse.id}`)
        stocks.value = data.data?.stocks || []
    } catch (err) {
        toast.error(err.response?.data?.message || t('common.failedToLoad'))
    } finally {
        stockLoading.value = false
    }
}

onMounted(() => {
    fetchData()
    loadLookups()
})
</script>
