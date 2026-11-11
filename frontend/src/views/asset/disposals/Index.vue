<template lang="html">
    <Breadcrumb title="Disposals" />

    <div class="main-content-wrapper mt-4">
        <div class="container-fluid">
            <v-card style="padding: 5px; margin-top: 15px;">
                <div class="row">
                    <div class="col-md-12 mb-2">
                        <div class="row align-items-center g-2">
                            <div class="col-md-2">
                                <v-btn v-if="can(['inventory-disposals.create'])" type="button" @click="openAddDialog"
                                    class="text-none text-white" color="success" rounded="0" variant="flat">
                                    <i class="fa-solid fa-plus me-2"></i> {{ $t('New Disposal') }}
                                </v-btn>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" v-model="filters.warehouse_id">
                                    <option value="">{{ $t('All Stores') }}</option>
                                    <option v-for="w in lookups.warehouses" :key="w.id" :value="w.id">{{ w.name }}</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select class="form-select" v-model="filters.status">
                                    <option value="">{{ $t('All Status') }}</option>
                                    <option v-for="status in statuses" :key="status" :value="status">
                                        {{ $t(statusLabel(status)) }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-5 d-flex gap-2">
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

                    <v-table class="custom-bordered">
                        <thead>
                            <tr>
                                <th class="text-left">#</th>
                                <th class="text-left">{{ $t('Disposal No') }}</th>
                                <th class="text-left">{{ $t('Date') }}</th>
                                <th class="text-left">{{ $t('Store') }}</th>
                                <th class="text-left">{{ $t('Reason') }}</th>
                                <th class="text-left">{{ $t('Items') }}</th>
                                <th class="text-center">{{ $t('Status') }}</th>
                                <th class="text-left">{{ $t('Inserted By') }}</th>
                                <th class="text-left">{{ $t('Approved By') }}</th>
                                <th class="text-center">{{ $t('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="loading">
                                <td colspan="10" class="text-center py-4">
                                    <v-progress-linear indeterminate color="primary"></v-progress-linear>
                                    {{ $t('Loading...') }}
                                </td>
                            </tr>
                            <tr v-else-if="!items.length">
                                <td colspan="10" class="text-center py-4">{{ $t('No records found.') }}</td>
                            </tr>
                            <tr v-else v-for="(disposal, index) in items" :key="disposal.id">
                                <td>{{ (pagination.page - 1) * pagination.perPage + index + 1 }}</td>
                                <td>{{ disposal.disposal_no }}</td>
                                <td>{{ disposal.disposal_date }}</td>
                                <td>{{ disposal.warehouse_name }}</td>
                                <td>{{ disposal.reason }}</td>
                                <td>
                                    <div v-for="line in disposal.items" :key="line.id">
                                        {{ line.item_name }} × {{ qty(line.quantity) }}
                                        <small v-if="line.serial_number" class="text-muted">({{ line.serial_number }})</small>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span :class="statusClass(disposal.status)">{{ $t(statusLabel(disposal.status)) }}</span>
                                </td>
                                <td>{{ disposal.created_by_name || '-' }}</td>
                                <td>{{ disposal.approved_by_name || '-' }}</td>
                                <td class="text-center">
                                    <v-btn v-if="disposal.status === 'draft' && can(['inventory-disposals.approve'])"
                                        size="small" class="text-none text-white" color="success" rounded="0"
                                        variant="flat" @click="runAction(disposal, 'approve')">
                                        {{ $t('Approve') }}
                                    </v-btn>
                                    <v-btn v-else-if="disposal.status === 'approved' && can(['inventory-disposals.dispose'])"
                                        size="small" class="text-none text-white" color="red-darken-2" rounded="0"
                                        variant="flat" @click="runAction(disposal, 'dispose')">
                                        {{ $t('Dispose') }}
                                    </v-btn>
                                    <span v-else-if="disposal.disposed_by_name" class="text-muted small">
                                        {{ disposal.disposed_by_name }}
                                    </span>
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

    <!-- New disposal -->
    <v-dialog v-model="dialog" max-width="850">
        <v-card>
            <v-card-title>{{ $t('New Disposal') }}</v-card-title>
            <v-card-text>
                <div class="row">
                    <div class="col-md-4 form-group mb-3">
                        <label>{{ $t('Store:') }} <span class="text-danger">*</span></label>
                        <SearchSelect v-model="form.warehouse_id" :items="lookups.warehouses" item-title="name"
                            :error="errors.warehouse_id" :placeholder="$t('Select Store')"
                            @update:model-value="reloadAllSerials" />
                    </div>
                    <div class="col-md-5 form-group mb-3">
                        <label>{{ $t('Reason:') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" v-model="form.reason" maxlength="255"
                            :placeholder="$t('e.g. Damaged beyond repair')" />
                        <div v-if="errors.reason" class="error-msg">{{ errors.reason }}</div>
                    </div>
                    <div class="col-md-3 form-group mb-3">
                        <label>{{ $t('Disposal Date:') }}</label>
                        <input type="date" class="form-control" v-model="form.disposal_date" />
                    </div>
                </div>

                <v-table class="custom-bordered">
                    <thead>
                        <tr>
                            <th class="text-left" style="min-width: 220px">{{ $t('Item') }}</th>
                            <th class="text-left" style="min-width: 180px">{{ $t('Serial No') }}</th>
                            <th class="text-right" style="width: 110px">{{ $t('Quantity') }}</th>
                            <th class="text-left">{{ $t('Note') }}</th>
                            <th style="width: 50px"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(line, index) in form.items" :key="index">
                            <td>
                                <SearchSelect v-model="line.item_id" :items="lookups.items" item-value="item_id"
                                    :item-title="(i) => `${i.item_code} - ${i.item_name}`"
                                    :placeholder="$t('Select Item')" @update:model-value="loadLineSerials(line)" />
                            </td>
                            <td>
                                <SearchSelect v-if="isSerialItem(line)" v-model="line.serial_id" :items="line.serials"
                                    :item-title="(s) => `${s.serial_number} (${$t(statusLabel(s.status))})`"
                                    :placeholder="$t('Select Serial No')" />
                                <span v-else class="text-muted">-</span>
                            </td>
                            <td>
                                <input type="number" min="0" step="any" class="form-control text-end"
                                    v-model="line.quantity" :disabled="isSerialItem(line)" />
                            </td>
                            <td><input type="text" class="form-control" v-model="line.reason" maxlength="255" /></td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-danger"
                                    :disabled="form.items.length === 1" @click="form.items.splice(index, 1)">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </v-table>
                <v-btn class="text-none mt-2" color="blue-darken-3" variant="outlined" rounded="0" size="small"
                    @click="form.items.push(emptyLine())">
                    <i class="fa-solid fa-plus me-1"></i> {{ $t('Add Line') }}
                </v-btn>

                <div class="form-group mt-3">
                    <label>{{ $t('Remarks:') }}</label>
                    <textarea class="form-control" rows="2" v-model="form.remarks"></textarea>
                </div>
                <div v-for="(message, i) in otherErrors(errors, ['warehouse_id', 'reason'])" :key="i"
                    class="error-msg">{{ message }}</div>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn class="text-none" color="grey-lighten-3" rounded="0" variant="flat" @click="dialog = false">
                    {{ $t('Cancel') }}
                </v-btn>
                <v-btn class="text-none text-white" color="blue-darken-4" rounded="0" variant="flat"
                    :disabled="isSubmitting" :loading="isSubmitting" @click="handleSubmit">
                    {{ $t('Save Draft') }}
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
import {
    confirmWorkflowAction, fetchSerials, fillValidationErrors, otherErrors, qty, statusClass, statusLabel,
    useInventoryLookups,
} from '@/views/asset/helpers';

const { items, payload, loading, filters, pagination, fetchData, changePage, changePerPage, resetFilters } =
    usePaginatedFetch('/api/asset/disposals', { status: '', warehouse_id: '' })

const { can } = usePermission()
const { lookups, loadLookups, itemById } = useInventoryLookups()

const statuses = computed(() => payload.value.statuses || [])

/* ---------- workflow: draft -> approved -> disposed ---------- */
const runAction = async (disposal, action) => {
    const text = action === 'approve'
        ? t('Approve disposal {no}?', { no: disposal.disposal_no })
        : t('Dispose {no}? The stock will be written off.', { no: disposal.disposal_no })

    if (await confirmWorkflowAction(`/api/asset/disposals/${disposal.id}/${action}`, text)) fetchData()
}

/* ---------- new disposal ---------- */
const emptyLine = () => ({ item_id: '', serial_id: '', quantity: 1, reason: '', serials: [] })

const initialForm = () => ({
    warehouse_id: '',
    reason: '',
    disposal_date: new Date().toISOString().slice(0, 10),
    remarks: '',
    items: [emptyLine()],
})

const dialog = ref(false)
const form = reactive(initialForm())
const errors = reactive({})
const isSubmitting = ref(false)

const isSerialItem = (line) => itemById(line.item_id)?.track_type === 'serial'

const loadLineSerials = async (line) => {
    line.serial_id = ''
    if (isSerialItem(line)) line.quantity = 1
    line.serials = isSerialItem(line) ? await fetchSerials(form.warehouse_id, line.item_id) : []
}

const reloadAllSerials = () => form.items.forEach(loadLineSerials)

const clearErrors = () => {
    for (const key in errors) delete errors[key]
}

const openAddDialog = () => {
    Object.assign(form, initialForm())
    clearErrors()
    dialog.value = true
}

const handleSubmit = async () => {
    clearErrors()
    if (!form.warehouse_id) errors.warehouse_id = t('Please select a store.')
    if (!form.reason) errors.reason = t('The reason field is required.')
    if (Object.keys(errors).length) return

    isSubmitting.value = true
    try {
        const { data } = await axios.post('/api/asset/disposals', {
            ...form,
            items: form.items
                .filter(line => line.item_id)
                .map(({ item_id, serial_id, quantity, reason }) => ({ item_id, serial_id: serial_id || null, quantity, reason })),
        })
        if (data.success) {
            toast.success(data.message)
            dialog.value = false
            fetchData()
        }
    } catch (err) {
        fillValidationErrors(err, errors)
    } finally {
        isSubmitting.value = false
    }
}

onMounted(() => {
    fetchData()
    loadLookups()
})
</script>
