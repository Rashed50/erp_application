<template lang="html">
    <Breadcrumb title="Employee Issues" />

    <div class="main-content-wrapper mt-4">
        <div class="container-fluid">
            <v-card style="padding: 5px; margin-top: 15px;">
                <div class="row">
                    <div class="col-md-12 mb-2">
                        <div class="row align-items-center g-2">
                            <div class="col-md-2">
                                <v-btn v-if="can(['employee-inventory.issue'])" type="button" @click="openIssue"
                                    class="text-none text-white" color="success" rounded="0" variant="flat">
                                    <i class="fa-solid fa-hand-holding me-2"></i> {{ $t('Issue Item') }}
                                </v-btn>
                            </div>
                            <div class="col-md-3">
                                <SearchSelect v-model="filters.employee_id" :items="lookups.employees"
                                    :item-title="(e) => `${e.employee_code} - ${e.name}`"
                                    :placeholder="$t('All Employees')" />
                            </div>
                            <div class="col-md-2">
                                <SearchSelect v-model="filters.warehouse_id" :items="lookups.warehouses"
                                    item-title="name" :placeholder="$t('All Stores')" />
                            </div>
                            <div class="col-md-2">
                                <select class="form-select" v-model="filters.status">
                                    <option value="">{{ $t('All Status') }}</option>
                                    <option value="issued">{{ $t('Issued') }}</option>
                                    <option value="returned">{{ $t('Returned') }}</option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex gap-2">
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
                                <th class="text-left">{{ $t('Issue Date') }}</th>
                                <th class="text-left">{{ $t('Employee') }}</th>
                                <th class="text-left">{{ $t('Item') }}</th>
                                <th class="text-left">{{ $t('From Store') }}</th>
                                <th class="text-right">{{ $t('Issued') }}</th>
                                <th class="text-right">{{ $t('Returned') }}</th>
                                <th class="text-left">{{ $t('Expected Return') }}</th>
                                <th class="text-center">{{ $t('Status') }}</th>
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
                            <tr v-else v-for="(record, index) in items" :key="record.id">
                                <td>{{ (pagination.page - 1) * pagination.perPage + index + 1 }}</td>
                                <td>{{ record.issue_date }}</td>
                                <td>{{ record.employee_code }} - {{ record.employee_name }}</td>
                                <td>
                                    {{ record.item_name }}
                                    <small v-if="record.serial_number" class="text-muted">({{ record.serial_number }})</small>
                                </td>
                                <td>{{ record.warehouse_name || '-' }}</td>
                                <td class="text-right">{{ qty(record.quantity) }}</td>
                                <td class="text-right">{{ qty(record.returned_qty) }}</td>
                                <td>{{ record.expected_return_date || '-' }}</td>
                                <td class="text-center">
                                    <span :class="statusClass(record.status)">{{ $t(statusLabel(record.status)) }}</span>
                                </td>
                                <td class="text-center">
                                    <button v-if="record.status === 'issued' && can(['employee-inventory.return'])"
                                        type="button" class="table-action-button" :title="$t('Take Back')"
                                        @click="openReturn(record)">
                                        <i class="fa-solid fa-rotate-left"></i>
                                    </button>
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

    <!-- Issue -->
    <v-dialog v-model="issueDialog" max-width="650">
        <v-card>
            <v-card-title>{{ $t('Issue Item to Employee') }}</v-card-title>
            <v-card-text>
                <div class="row">
                    <div class="col-md-6 form-group mb-3">
                        <label>{{ $t('From Store:') }} <span class="text-danger">*</span></label>
                        <SearchSelect v-model="issueForm.warehouse_id" :items="lookups.warehouses" item-title="name"
                            :error="issueErrors.warehouse_id" :placeholder="$t('Select Store')"
                            @update:model-value="loadSerials" />
                    </div>
                    <div class="col-md-6 form-group mb-3">
                        <label>{{ $t('Employee:') }} <span class="text-danger">*</span></label>
                        <SearchSelect v-model="issueForm.employee_id" :items="lookups.employees"
                            :item-title="(e) => `${e.employee_code} - ${e.name}`" :error="issueErrors.employee_id"
                            :placeholder="$t('Select Employee')" />
                    </div>
                    <div class="col-md-8 form-group mb-3">
                        <label>{{ $t('Item:') }} <span class="text-danger">*</span></label>
                        <SearchSelect v-model="issueForm.item_id" :items="issuableItems" item-value="item_id"
                            :item-title="(i) => `${i.item_code} - ${i.item_name}`" :error="issueErrors.item_id"
                            :placeholder="$t('Select Item')" @update:model-value="loadSerials" />
                        <small class="text-muted">{{ $t('Asset items are handed over from the Asset Register.') }}</small>
                    </div>
                    <div class="col-md-4 form-group mb-3">
                        <label>{{ $t('Quantity:') }} <span class="text-danger">*</span></label>
                        <input type="number" min="0" step="any" class="form-control" v-model="issueForm.quantity"
                            :disabled="isSerialItem" />
                        <div v-if="issueErrors.quantity" class="error-msg">{{ issueErrors.quantity }}</div>
                    </div>
                    <div class="col-md-12 form-group mb-3" v-if="isSerialItem">
                        <label>{{ $t('Serial No:') }} <span class="text-danger">*</span></label>
                        <SearchSelect v-model="issueForm.serial_id" :items="serials" item-title="serial_number"
                            :error="issueErrors.serial_id" :placeholder="$t('Select Serial No')" />
                    </div>
                    <div class="col-md-6 form-group mb-3">
                        <label>{{ $t('Expected Return Date:') }}</label>
                        <input type="date" class="form-control" v-model="issueForm.expected_return_date" />
                    </div>
                    <div class="col-md-12 form-group mb-3">
                        <label>{{ $t('Remarks:') }}</label>
                        <textarea class="form-control" rows="2" v-model="issueForm.remarks"></textarea>
                    </div>
                </div>
                <div v-for="(message, i) in otherErrors(issueErrors, ['warehouse_id', 'employee_id', 'item_id', 'quantity', 'serial_id'])"
                    :key="i" class="error-msg">{{ message }}</div>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn class="text-none" color="grey-lighten-3" rounded="0" variant="flat" @click="issueDialog = false">
                    {{ $t('Cancel') }}
                </v-btn>
                <v-btn class="text-none text-white" color="blue-darken-4" rounded="0" variant="flat"
                    :disabled="isSubmitting" :loading="isSubmitting" @click="submitIssue">
                    {{ $t('Issue') }}
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>

    <!-- Take back -->
    <v-dialog v-model="returnDialog" max-width="550">
        <v-card v-if="selected">
            <v-card-title>{{ $t('Take Back') }} — {{ selected.item_name }}</v-card-title>
            <v-card-text>
                <p class="mb-3">
                    {{ selected.employee_code }} - {{ selected.employee_name }}:
                    {{ $t('{count} still held', { count: qty(selected.outstanding_qty) }) }}
                </p>
                <div class="form-group mb-3">
                    <label>{{ $t('Quantity:') }} <span class="text-danger">*</span></label>
                    <input type="number" min="0" :max="selected.outstanding_qty" step="any" class="form-control"
                        v-model="returnForm.quantity" :disabled="!!selected.serial_id" />
                    <div v-if="returnErrors.quantity" class="error-msg">{{ returnErrors.quantity }}</div>
                </div>
                <div class="form-group mb-3">
                    <label>{{ $t('Return To Store:') }} <span class="text-danger">*</span></label>
                    <SearchSelect v-model="returnForm.destination_warehouse_id" :items="lookups.warehouses"
                        item-title="name" :error="returnErrors.destination_warehouse_id"
                        :placeholder="$t('Select Store')" />
                </div>
                <div class="form-group mb-3">
                    <label>{{ $t('Condition:') }} <span class="text-danger">*</span></label>
                    <select class="form-select" v-model="returnForm.condition">
                        <option v-for="condition in lookups.conditions" :key="condition" :value="condition">
                            {{ $t(statusLabel(condition)) }}
                        </option>
                    </select>
                </div>
                <div class="form-group mb-3">
                    <label>{{ $t('Reason:') }}</label>
                    <input type="text" class="form-control" v-model="returnForm.reason" maxlength="255" />
                </div>
                <div v-for="(message, i) in otherErrors(returnErrors, ['quantity', 'destination_warehouse_id'])"
                    :key="i" class="error-msg">{{ message }}</div>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn class="text-none" color="grey-lighten-3" rounded="0" variant="flat" @click="returnDialog = false">
                    {{ $t('Cancel') }}
                </v-btn>
                <v-btn class="text-none text-white" color="blue-darken-4" rounded="0" variant="flat"
                    :disabled="isSubmitting" :loading="isSubmitting" @click="submitReturn">
                    {{ $t('Take Back') }}
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
    ITEM_TYPES, fetchSerials, fillValidationErrors, otherErrors, qty, statusClass, statusLabel, useInventoryLookups,
} from '@/views/asset/helpers';

const { items, loading, filters, pagination, fetchData, changePage, changePerPage, resetFilters } =
    usePaginatedFetch('/api/asset/employee-inventory', { employee_id: '', warehouse_id: '', status: '' })

const { can } = usePermission()
const { lookups, loadLookups, itemById } = useInventoryLookups()

const isSubmitting = ref(false)

const clear = (target) => {
    for (const key in target) delete target[key]
}

/* ---------- issue ---------- */
const ASSET_TYPE_ID = ITEM_TYPES[0].id

const issueDialog = ref(false)
const initialIssue = () => ({
    warehouse_id: '', employee_id: '', item_id: '', quantity: 1, serial_id: '', expected_return_date: '', remarks: '',
})
const issueForm = reactive(initialIssue())
const issueErrors = reactive({})
const serials = ref([])

const issuableItems = computed(() => lookups.items.filter(item => item.itype_id !== ASSET_TYPE_ID))
const isSerialItem = computed(() => itemById(issueForm.item_id)?.track_type === 'serial')

const loadSerials = async () => {
    issueForm.serial_id = ''
    if (isSerialItem.value) issueForm.quantity = 1
    serials.value = isSerialItem.value
        ? (await fetchSerials(issueForm.warehouse_id, issueForm.item_id)).filter(s => s.status === 'in_stock')
        : []
}

const openIssue = () => {
    Object.assign(issueForm, initialIssue())
    serials.value = []
    clear(issueErrors)
    issueDialog.value = true
}

const submitIssue = async () => {
    clear(issueErrors)
    if (!issueForm.warehouse_id) issueErrors.warehouse_id = t('Please select a store.')
    if (!issueForm.employee_id) issueErrors.employee_id = t('Please select an employee.')
    if (!issueForm.item_id) issueErrors.item_id = t('Please select an item.')
    if (isSerialItem.value && !issueForm.serial_id) issueErrors.serial_id = t('Please select a serial number.')
    if (Object.keys(issueErrors).length) return

    isSubmitting.value = true
    try {
        const { data } = await axios.post('/api/asset/employee-inventory/issue', {
            ...issueForm,
            serial_id: issueForm.serial_id || null,
            expected_return_date: issueForm.expected_return_date || null,
        })
        if (data.success) {
            toast.success(data.message)
            issueDialog.value = false
            fetchData()
        }
    } catch (err) {
        fillValidationErrors(err, issueErrors)
    } finally {
        isSubmitting.value = false
    }
}

/* ---------- take back ---------- */
const returnDialog = ref(false)
const selected = ref(null)
const returnForm = reactive({ quantity: 1, destination_warehouse_id: '', condition: 'good', reason: '' })
const returnErrors = reactive({})

const openReturn = (record) => {
    selected.value = record
    Object.assign(returnForm, {
        quantity: qty(record.outstanding_qty),
        destination_warehouse_id: record.warehouse_id ?? '',
        condition: 'good',
        reason: '',
    })
    clear(returnErrors)
    returnDialog.value = true
}

const submitReturn = async () => {
    clear(returnErrors)
    if (!returnForm.destination_warehouse_id) returnErrors.destination_warehouse_id = t('Please select a store.')
    if (Object.keys(returnErrors).length) return

    isSubmitting.value = true
    try {
        const { data } = await axios.post(`/api/asset/employee-inventory/${selected.value.id}/return`, returnForm)
        if (data.success) {
            toast.success(data.message)
            returnDialog.value = false
            fetchData()
        }
    } catch (err) {
        fillValidationErrors(err, returnErrors)
    } finally {
        isSubmitting.value = false
    }
}

onMounted(() => {
    fetchData()
    loadLookups()
})
</script>
