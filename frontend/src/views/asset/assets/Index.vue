<template lang="html">
    <Breadcrumb title="Asset Register" />

    <div class="main-content-wrapper mt-4">
        <div class="container-fluid">
            <v-card style="padding: 5px; margin-top: 15px;">
                <div class="row">
                    <div class="col-md-12 mb-2">
                        <div class="row align-items-center g-2">
                            <div class="col-md-2">
                                <select class="form-select" v-model="filters.status">
                                    <option value="">{{ $t('All Status') }}</option>
                                    <option v-for="status in statuses" :key="status" :value="status">
                                        {{ $t(statusLabel(status)) }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <SearchSelect v-model="filters.warehouse_id" :items="lookups.warehouses"
                                    item-title="name" :placeholder="$t('All Stores')" />
                            </div>
                            <div class="col-md-3">
                                <SearchSelect v-model="filters.employee_id" :items="lookups.employees"
                                    :item-title="(e) => `${e.employee_code} - ${e.name}`"
                                    :placeholder="$t('All Employees')" />
                            </div>
                            <div class="col-md-5">
                                <div class="search-wrapper d-flex align-center gap-2">
                                    <v-text-field variant="outlined" density="compact"
                                        :placeholder="$t('Asset tag, serial or item...')" v-model="filters.search"
                                        hide-details class="flex-grow-1"></v-text-field>
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
                                <th class="text-left">{{ $t('Asset Tag') }}</th>
                                <th class="text-left">{{ $t('Item') }}</th>
                                <th class="text-left">{{ $t('Serial No') }}</th>
                                <th class="text-left">{{ $t('Location / Custodian') }}</th>
                                <th class="text-center">{{ $t('Condition') }}</th>
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
                            <tr v-else v-for="(asset, index) in items" :key="asset.id">
                                <td>{{ (pagination.page - 1) * pagination.perPage + index + 1 }}</td>
                                <td>{{ asset.asset_tag }}</td>
                                <td>{{ asset.item_code }} - {{ asset.item_name }}</td>
                                <td>{{ asset.serial_number }}</td>
                                <td>
                                    <template v-if="asset.current_employee_id">
                                        <i class="fa-solid fa-user me-1"></i>
                                        {{ asset.current_employee_code }} - {{ asset.current_employee_name }}
                                    </template>
                                    <template v-else-if="asset.current_warehouse_id">
                                        <i class="fa-solid fa-warehouse me-1"></i> {{ asset.current_warehouse_name }}
                                    </template>
                                    <template v-else>-</template>
                                </td>
                                <td class="text-center">{{ $t(statusLabel(asset.condition)) }}</td>
                                <td class="text-center">
                                    <span :class="statusClass(asset.status)">{{ $t(statusLabel(asset.status)) }}</span>
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
                                                <button type="button" class="menu-link" @click="openHistory(asset)">
                                                    {{ $t('History') }}
                                                </button>
                                            </li>
                                            <li class="menu-item"
                                                v-if="asset.status === 'in_stock' && can(['assets.assign'])">
                                                <button type="button" class="menu-link" @click="openAssign(asset)">
                                                    {{ $t('Assign to Employee') }}
                                                </button>
                                            </li>
                                            <li class="menu-item"
                                                v-if="asset.status === 'assigned' && can(['assets.return'])">
                                                <button type="button" class="menu-link" @click="openReturn(asset)">
                                                    {{ $t('Take Back') }}
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

    <!-- Assign -->
    <v-dialog v-model="assignDialog" max-width="550">
        <v-card v-if="selected">
            <v-card-title>{{ $t('Assign Asset') }} — {{ selected.asset_tag }}</v-card-title>
            <v-card-text>
                <div class="form-group mb-3">
                    <label>{{ $t('Employee:') }} <span class="text-danger">*</span></label>
                    <SearchSelect v-model="assignForm.employee_id" :items="lookups.employees"
                        :item-title="(e) => `${e.employee_code} - ${e.name}`" :error="assignErrors.employee_id"
                        :placeholder="$t('Select Employee')" />
                </div>
                <div class="form-group mb-3">
                    <label>{{ $t('Expected Return Date:') }}</label>
                    <input type="date" class="form-control" v-model="assignForm.expected_return_date" />
                </div>
                <div class="form-group mb-3">
                    <label>{{ $t('Remarks:') }}</label>
                    <textarea class="form-control" rows="2" v-model="assignForm.remarks"></textarea>
                </div>
                <div v-for="(message, i) in otherErrors(assignErrors, ['employee_id'])" :key="i" class="error-msg">
                    {{ message }}
                </div>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn class="text-none" color="grey-lighten-3" rounded="0" variant="flat" @click="assignDialog = false">
                    {{ $t('Cancel') }}
                </v-btn>
                <v-btn class="text-none text-white" color="blue-darken-4" rounded="0" variant="flat"
                    :disabled="isSubmitting" :loading="isSubmitting" @click="submitAssign">
                    {{ $t('Assign') }}
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>

    <!-- Take back -->
    <v-dialog v-model="returnDialog" max-width="550">
        <v-card v-if="selected">
            <v-card-title>{{ $t('Take Back Asset') }} — {{ selected.asset_tag }}</v-card-title>
            <v-card-text>
                <p class="mb-3">
                    {{ $t('From') }}: <strong>{{ selected.current_employee_code }} - {{ selected.current_employee_name }}</strong>
                </p>
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
                <div v-for="(message, i) in otherErrors(returnErrors, ['destination_warehouse_id'])" :key="i"
                    class="error-msg">{{ message }}</div>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn class="text-none" color="grey-lighten-3" rounded="0" variant="flat" @click="returnDialog = false">
                    {{ $t('Cancel') }}
                </v-btn>
                <v-btn class="text-none text-white" color="blue-darken-4" rounded="0" variant="flat"
                    :disabled="isSubmitting || !activeAssignmentId" :loading="isSubmitting" @click="submitReturn">
                    {{ $t('Take Back') }}
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>

    <!-- History -->
    <v-dialog v-model="historyDialog" max-width="900">
        <v-card v-if="detail">
            <v-card-title>{{ detail.asset_tag }} — {{ detail.item_name }}</v-card-title>
            <v-card-text>
                <div class="row mb-3">
                    <div class="col-md-4"><strong>{{ $t('Serial No') }}:</strong> {{ detail.serial_number }}</div>
                    <div class="col-md-4"><strong>{{ $t('Purchase Cost') }}:</strong> {{ Number(detail.purchase_cost).toFixed(2) }}</div>
                    <div class="col-md-4"><strong>{{ $t('Purchase Date') }}:</strong> {{ detail.purchase_date || '-' }}</div>
                    <div class="col-md-4"><strong>{{ $t('Warranty Until') }}:</strong> {{ detail.warranty_expiry || '-' }}</div>
                </div>
                <h6>{{ $t('Assignments') }}</h6>
                <v-table class="custom-bordered">
                    <thead>
                        <tr>
                            <th class="text-left">{{ $t('Employee') }}</th>
                            <th class="text-left">{{ $t('From Store') }}</th>
                            <th class="text-left">{{ $t('Assigned') }}</th>
                            <th class="text-left">{{ $t('Returned') }}</th>
                            <th class="text-left">{{ $t('Assigned By') }}</th>
                            <th class="text-center">{{ $t('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="!detail.assignments?.length">
                            <td colspan="6" class="text-center py-3">{{ $t('Never assigned.') }}</td>
                        </tr>
                        <tr v-for="assignment in detail.assignments" :key="assignment.id">
                            <td>{{ assignment.employee_code }} - {{ assignment.employee_name }}</td>
                            <td>{{ assignment.warehouse_name || '-' }}</td>
                            <td>{{ assignment.assigned_date }}</td>
                            <td>{{ assignment.returned_date || '-' }}</td>
                            <td>{{ assignment.assigned_by_name || '-' }}</td>
                            <td class="text-center">
                                <span :class="statusClass(assignment.status)">{{ $t(statusLabel(assignment.status)) }}</span>
                            </td>
                        </tr>
                    </tbody>
                </v-table>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn class="text-none" color="grey-lighten-3" rounded="0" variant="flat"
                    @click="historyDialog = false">
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
import {
    fillValidationErrors, otherErrors, statusClass, statusLabel, useInventoryLookups,
} from '@/views/asset/helpers';

const { items, payload, loading, filters, pagination, fetchData, changePage, changePerPage, resetFilters } =
    usePaginatedFetch('/api/asset/assets', { search: '', status: '', warehouse_id: '', employee_id: '' })

const { can } = usePermission()
const { lookups, loadLookups } = useInventoryLookups()

const statuses = computed(() => payload.value.statuses || [])

const selected = ref(null)
const isSubmitting = ref(false)

const clear = (target) => {
    for (const key in target) delete target[key]
}

const loadDetail = async (assetId) => {
    try {
        const { data } = await axios.get(`/api/asset/assets/${assetId}`)
        return data.data
    } catch (err) {
        toast.error(err.response?.data?.message || t('common.failedToLoad'))
        return null
    }
}

/* ---------- assign ---------- */
const assignDialog = ref(false)
const assignForm = reactive({ employee_id: '', expected_return_date: '', remarks: '' })
const assignErrors = reactive({})

const openAssign = (asset) => {
    selected.value = asset
    Object.assign(assignForm, { employee_id: '', expected_return_date: '', remarks: '' })
    clear(assignErrors)
    assignDialog.value = true
}

const submitAssign = async () => {
    clear(assignErrors)
    if (!assignForm.employee_id) assignErrors.employee_id = t('Please select an employee.')
    if (Object.keys(assignErrors).length) return

    isSubmitting.value = true
    try {
        const { data } = await axios.post(`/api/asset/assets/${selected.value.id}/assign`, {
            ...assignForm,
            expected_return_date: assignForm.expected_return_date || null,
        })
        if (data.success) {
            toast.success(data.message)
            assignDialog.value = false
            fetchData()
        }
    } catch (err) {
        fillValidationErrors(err, assignErrors)
    } finally {
        isSubmitting.value = false
    }
}

/* ---------- take back ---------- */
const returnDialog = ref(false)
const returnForm = reactive({ destination_warehouse_id: '', condition: 'good', reason: '' })
const returnErrors = reactive({})
const activeAssignmentId = ref(null)

const openReturn = async (asset) => {
    selected.value = asset
    activeAssignmentId.value = null
    Object.assign(returnForm, { destination_warehouse_id: '', condition: 'good', reason: '' })
    clear(returnErrors)
    returnDialog.value = true

    const detail = await loadDetail(asset.id)
    const active = detail?.assignments?.find(assignment => assignment.status === 'assigned')
    activeAssignmentId.value = active?.id ?? null
    returnForm.destination_warehouse_id = active?.warehouse_id ?? ''
}

const submitReturn = async () => {
    clear(returnErrors)
    if (!returnForm.destination_warehouse_id) returnErrors.destination_warehouse_id = t('Please select a store.')
    if (Object.keys(returnErrors).length) return

    isSubmitting.value = true
    try {
        const { data } = await axios.post(`/api/asset/asset-assignments/${activeAssignmentId.value}/return`, returnForm)
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

/* ---------- history ---------- */
const historyDialog = ref(false)
const detail = ref(null)

const openHistory = async (asset) => {
    detail.value = asset
    historyDialog.value = true
    detail.value = (await loadDetail(asset.id)) ?? asset
}

onMounted(() => {
    fetchData()
    loadLookups()
})
</script>
