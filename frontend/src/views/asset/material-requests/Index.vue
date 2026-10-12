<template lang="html">
    <Breadcrumb title="Material Requests" />

    <div class="main-content-wrapper mt-4">
        <div class="container-fluid">
            <v-card style="padding: 5px; margin-top: 15px;">
                <div class="row">
                    <div class="col-md-12 mb-2">
                        <div class="row align-items-center g-2">
                            <div class="col-md-2">
                                <v-btn v-if="can(['material-requests.create'])" type="button" @click="openAddDialog"
                                    class="text-none text-white" color="success" rounded="0" variant="flat">
                                    <i class="fa-solid fa-plus me-2"></i> {{ $t('New Request') }}
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
                                <th class="text-left">{{ $t('Request No') }}</th>
                                <th class="text-left">{{ $t('Date') }}</th>
                                <th class="text-left">{{ $t('Requesting Store') }}</th>
                                <th class="text-left">{{ $t('Items') }}</th>
                                <th class="text-center">{{ $t('Priority') }}</th>
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
                            <tr v-else v-for="(request, index) in items" :key="request.id">
                                <td>{{ (pagination.page - 1) * pagination.perPage + index + 1 }}</td>
                                <td>{{ request.request_no }}</td>
                                <td>{{ request.request_date }}</td>
                                <td>{{ request.warehouse_name }}</td>
                                <td>
                                    <div v-for="line in request.items" :key="line.id">
                                        {{ line.item_name }} × {{ qty(line.requested_qty) }}
                                    </div>
                                </td>
                                <td class="text-center">{{ $t(statusLabel(request.priority)) }}</td>
                                <td class="text-center">
                                    <span :class="statusClass(request.status)">{{ $t(statusLabel(request.status)) }}</span>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="table-action-button" @click="openView(request)">
                                        <i class="fa-solid fa-eye"></i>
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

    <!-- New request -->
    <v-dialog v-model="dialog" max-width="800">
        <v-card>
            <v-card-title>{{ $t('New Material Request') }}</v-card-title>
            <v-card-text>
                <div class="row">
                    <div class="col-md-6 form-group mb-3">
                        <label>{{ $t('Requesting Store:') }} <span class="text-danger">*</span></label>
                        <SearchSelect v-model="form.warehouse_id" :items="lookups.warehouses" item-title="name"
                            :error="errors.warehouse_id" :placeholder="$t('Select Store')" />
                    </div>
                    <div class="col-md-3 form-group mb-3">
                        <label>{{ $t('Priority:') }} <span class="text-danger">*</span></label>
                        <select class="form-select" v-model="form.priority">
                            <option v-for="priority in priorities" :key="priority" :value="priority">
                                {{ $t(statusLabel(priority)) }}
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3 form-group mb-3">
                        <label>{{ $t('Required By:') }}</label>
                        <input type="date" class="form-control" v-model="form.required_date" />
                        <div v-if="errors.required_date" class="error-msg">{{ errors.required_date }}</div>
                    </div>
                </div>

                <LineItems v-model="form.items" :items="lookups.items" qty-key="requested_qty" />

                <div class="form-group mt-3">
                    <label>{{ $t('Remarks:') }}</label>
                    <textarea class="form-control" rows="2" v-model="form.remarks"></textarea>
                </div>
                <div v-for="(message, i) in otherErrors(errors, ['warehouse_id', 'required_date'])" :key="i"
                    class="error-msg">{{ message }}</div>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn class="text-none" color="grey-lighten-3" rounded="0" variant="flat" @click="dialog = false">
                    {{ $t('Cancel') }}
                </v-btn>
                <v-btn class="text-none text-white" color="blue-darken-4" rounded="0" variant="flat"
                    :disabled="isSubmitting" :loading="isSubmitting" @click="handleSubmit">
                    {{ $t('Submit') }}
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>

    <!-- View / approve -->
    <v-dialog v-model="viewDialog" max-width="800">
        <v-card v-if="viewing">
            <v-card-title class="d-flex align-center">
                {{ viewing.request_no }}
                <span class="ms-3" :class="statusClass(viewing.status)">{{ $t(statusLabel(viewing.status)) }}</span>
            </v-card-title>
            <v-card-text>
                <div class="row mb-3">
                    <div class="col-md-4"><strong>{{ $t('Requesting Store') }}:</strong> {{ viewing.warehouse_name }}</div>
                    <div class="col-md-4"><strong>{{ $t('Date') }}:</strong> {{ viewing.request_date }}</div>
                    <div class="col-md-4"><strong>{{ $t('Required By') }}:</strong> {{ viewing.required_date || '-' }}</div>
                    <div class="col-md-4"><strong>{{ $t('Requested By') }}:</strong> {{ viewing.requested_by_name || '-' }}</div>
                    <div class="col-md-4"><strong>{{ $t('Approved By') }}:</strong> {{ viewing.approved_by_name || '-' }}</div>
                    <div class="col-md-4"><strong>{{ $t('Priority') }}:</strong> {{ $t(statusLabel(viewing.priority)) }}</div>
                    <div class="col-md-12 mt-2" v-if="viewing.remarks"><strong>{{ $t('Remarks') }}:</strong> {{ viewing.remarks }}</div>
                </div>

                <v-table class="custom-bordered">
                    <thead>
                        <tr>
                            <th class="text-left">{{ $t('Item') }}</th>
                            <th class="text-right">{{ $t('Requested') }}</th>
                            <th class="text-right">{{ $t('Approved') }}</th>
                            <th class="text-right">{{ $t('Purchased') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="line in viewing.items" :key="line.id">
                            <td>{{ line.item_code }} - {{ line.item_name }}</td>
                            <td class="text-right">{{ qty(line.requested_qty) }}</td>
                            <td class="text-right" style="width: 140px">
                                <input v-if="canApprove" type="number" min="0" step="any"
                                    class="form-control form-control-sm text-end" v-model="approvedQty[line.id]" />
                                <template v-else>{{ qty(line.approved_qty) }}</template>
                            </td>
                            <td class="text-right">{{ qty(line.purchased_qty) }}</td>
                        </tr>
                    </tbody>
                </v-table>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn class="text-none" color="grey-lighten-3" rounded="0" variant="flat" @click="viewDialog = false">
                    {{ $t('Close') }}
                </v-btn>
                <template v-if="canApprove">
                    <v-btn class="text-none text-white" color="red-darken-2" rounded="0" variant="flat"
                        @click="reject">
                        {{ $t('Reject') }}
                    </v-btn>
                    <v-btn class="text-none text-white" color="success" rounded="0" variant="flat" @click="approve">
                        {{ $t('Approve') }}
                    </v-btn>
                </template>
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
import LineItems from '@/views/asset/LineItems.vue';
import {
    confirmWorkflowAction, fillValidationErrors, otherErrors, qty, statusClass, statusLabel, useInventoryLookups,
} from '@/views/asset/helpers';

const { items, payload, loading, filters, pagination, fetchData, changePage, changePerPage, resetFilters } =
    usePaginatedFetch('/api/asset/material-requests', { status: '', warehouse_id: '' })

const { can } = usePermission()
const { lookups, loadLookups } = useInventoryLookups()

const statuses = computed(() => payload.value.statuses || [])
const priorities = ['low', 'normal', 'high', 'urgent']

/* ---------- new request ---------- */
const initialForm = () => ({
    warehouse_id: '',
    priority: 'normal',
    required_date: '',
    remarks: '',
    items: [{ item_id: '', requested_qty: 1 }],
})

const dialog = ref(false)
const form = reactive(initialForm())
const errors = reactive({})
const isSubmitting = ref(false)

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
    if (Object.keys(errors).length) return

    isSubmitting.value = true
    try {
        const { data } = await axios.post('/api/asset/material-requests', {
            ...form,
            required_date: form.required_date || null,
            items: form.items.filter(line => line.item_id),
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

/* ---------- view / approve ---------- */
const viewDialog = ref(false)
const viewing = ref(null)
const approvedQty = reactive({})

const canApprove = computed(() => viewing.value?.status === 'submitted' && can(['material-requests.approve']))

const openView = (request) => {
    viewing.value = request
    for (const key in approvedQty) delete approvedQty[key]
    request.items.forEach(line => { approvedQty[line.id] = qty(line.requested_qty) })
    viewDialog.value = true
}

const afterAction = (updated) => {
    if (!updated) return
    viewing.value = updated
    fetchData()
}

const approve = async () => {
    afterAction(await confirmWorkflowAction(
        `/api/asset/material-requests/${viewing.value.id}/approve`,
        t('Approve this material request?'),
        { items: viewing.value.items.map(line => ({ id: line.id, approved_qty: Number(approvedQty[line.id] || 0) })) },
    ))
}

const reject = async () => {
    afterAction(await confirmWorkflowAction(
        `/api/asset/material-requests/${viewing.value.id}/reject`,
        t('Reject this material request?'),
    ))
}

onMounted(() => {
    fetchData()
    loadLookups()
})
</script>
