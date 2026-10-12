<template lang="html">
    <Breadcrumb title="Store Transfers" />

    <div class="main-content-wrapper mt-4">
        <div class="container-fluid">
            <v-card style="padding: 5px; margin-top: 15px;">
                <div class="row">
                    <div class="col-md-12 mb-2">
                        <div class="row align-items-center g-2">
                            <div class="col-md-2">
                                <v-btn v-if="can(['inventory-transfers.create'])" type="button" @click="openAddDialog"
                                    class="text-none text-white" color="success" rounded="0" variant="flat">
                                    <i class="fa-solid fa-plus me-2"></i> {{ $t('New Transfer') }}
                                </v-btn>
                            </div>
                            <div class="col-md-2">
                                <select class="form-select" v-model="filters.from_warehouse_id">
                                    <option value="">{{ $t('From: All Stores') }}</option>
                                    <option v-for="w in lookups.warehouses" :key="w.id" :value="w.id">{{ w.name }}</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select class="form-select" v-model="filters.to_warehouse_id">
                                    <option value="">{{ $t('To: All Stores') }}</option>
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
                            <div class="col-md-4 d-flex gap-2">
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
                                <th class="text-left">{{ $t('Transfer No') }}</th>
                                <th class="text-left">{{ $t('Date') }}</th>
                                <th class="text-left">{{ $t('From Store') }}</th>
                                <th class="text-left">{{ $t('To Store') }}</th>
                                <th class="text-left">{{ $t('Items') }}</th>
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
                            <tr v-else v-for="(transfer, index) in items" :key="transfer.id">
                                <td>{{ (pagination.page - 1) * pagination.perPage + index + 1 }}</td>
                                <td>{{ transfer.transfer_no }}</td>
                                <td>{{ transfer.transfer_date }}</td>
                                <td>{{ transfer.from_warehouse_name }}</td>
                                <td>{{ transfer.to_warehouse_name }}</td>
                                <td>
                                    <div v-for="line in transfer.items" :key="line.id">
                                        {{ line.item_name }} × {{ qty(line.requested_qty) }}
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span :class="statusClass(transfer.status)">{{ $t(statusLabel(transfer.status)) }}</span>
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
                                                <button type="button" class="menu-link" @click="openView(transfer)">
                                                    {{ $t('View') }}
                                                </button>
                                            </li>
                                            <li class="menu-item" v-for="action in availableActions(transfer)"
                                                :key="action.key">
                                                <button type="button" class="menu-link"
                                                    @click="runAction(transfer, action)">
                                                    {{ $t(action.label) }}
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

    <!-- New transfer -->
    <v-dialog v-model="dialog" max-width="800">
        <v-card>
            <v-card-title>{{ $t('New Transfer') }}</v-card-title>
            <v-card-text>
                <div class="row">
                    <div class="col-md-4 form-group mb-3">
                        <label>{{ $t('From Store:') }} <span class="text-danger">*</span></label>
                        <SearchSelect v-model="form.from_warehouse_id" :items="lookups.warehouses" item-title="name"
                            :error="errors.from_warehouse_id" :placeholder="$t('Select Store')" />
                    </div>
                    <div class="col-md-4 form-group mb-3">
                        <label>{{ $t('To Store:') }} <span class="text-danger">*</span></label>
                        <SearchSelect v-model="form.to_warehouse_id" :items="lookups.warehouses" item-title="name"
                            :error="errors.to_warehouse_id" :placeholder="$t('Select Store')" />
                    </div>
                    <div class="col-md-4 form-group mb-3">
                        <label>{{ $t('Transfer Date:') }}</label>
                        <input type="date" class="form-control" v-model="form.transfer_date" />
                    </div>
                </div>

                <LineItems v-model="form.items" :items="lookups.items" />
                <small class="text-muted d-block mt-2">
                    {{ $t('Serial-tracked items are sent oldest units first.') }}
                </small>

                <div class="form-group mt-3">
                    <label>{{ $t('Remarks:') }}</label>
                    <textarea class="form-control" rows="2" v-model="form.remarks"></textarea>
                </div>
                <div v-for="(message, i) in otherErrors(errors, ['from_warehouse_id', 'to_warehouse_id'])" :key="i"
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

    <!-- View transfer -->
    <v-dialog v-model="viewDialog" max-width="800">
        <v-card v-if="viewing">
            <v-card-title class="d-flex align-center">
                {{ viewing.transfer_no }}
                <span class="ms-3" :class="statusClass(viewing.status)">{{ $t(statusLabel(viewing.status)) }}</span>
            </v-card-title>
            <v-card-text>
                <div class="row mb-3">
                    <div class="col-md-4"><strong>{{ $t('From Store') }}:</strong> {{ viewing.from_warehouse_name }}</div>
                    <div class="col-md-4"><strong>{{ $t('To Store') }}:</strong> {{ viewing.to_warehouse_name }}</div>
                    <div class="col-md-4"><strong>{{ $t('Date') }}:</strong> {{ viewing.transfer_date }}</div>
                    <div class="col-md-3"><strong>{{ $t('Requested By') }}:</strong> {{ viewing.requested_by_name || '-' }}</div>
                    <div class="col-md-3"><strong>{{ $t('Approved By') }}:</strong> {{ viewing.approved_by_name || '-' }}</div>
                    <div class="col-md-3"><strong>{{ $t('Dispatched By') }}:</strong> {{ viewing.dispatched_by_name || '-' }}</div>
                    <div class="col-md-3"><strong>{{ $t('Received By') }}:</strong> {{ viewing.received_by_name || '-' }}</div>
                </div>
                <v-table class="custom-bordered">
                    <thead>
                        <tr>
                            <th class="text-left">{{ $t('Item') }}</th>
                            <th class="text-right">{{ $t('Requested') }}</th>
                            <th class="text-right">{{ $t('Sent') }}</th>
                            <th class="text-right">{{ $t('Received') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="line in viewing.items" :key="line.id">
                            <td>{{ line.item_code }} - {{ line.item_name }}</td>
                            <td class="text-right">{{ qty(line.requested_qty) }}</td>
                            <td class="text-right">{{ qty(line.sent_qty) }}</td>
                            <td class="text-right">{{ qty(line.received_qty) }}</td>
                        </tr>
                    </tbody>
                </v-table>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn class="text-none" color="grey-lighten-3" rounded="0" variant="flat" @click="viewDialog = false">
                    {{ $t('Close') }}
                </v-btn>
                <v-btn v-for="action in availableActions(viewing)" :key="action.key" class="text-none text-white"
                    color="blue-darken-4" rounded="0" variant="flat" @click="runAction(viewing, action)">
                    {{ $t(action.label) }}
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
import LineItems from '@/views/asset/LineItems.vue';
import {
    confirmWorkflowAction, fillValidationErrors, otherErrors, qty, statusClass, statusLabel, useInventoryLookups,
} from '@/views/asset/helpers';

const { items, payload, loading, filters, pagination, fetchData, changePage, changePerPage, resetFilters } =
    usePaginatedFetch('/api/asset/transfers', { status: '', from_warehouse_id: '', to_warehouse_id: '' })

const { can } = usePermission()
const { lookups, loadLookups } = useInventoryLookups()

const statuses = computed(() => payload.value.statuses || [])

/* ---------- workflow: requested -> approved -> in transit -> received ---------- */
const ACTIONS = [
    { key: 'approve', status: 'requested', permission: 'inventory-transfers.approve', label: 'Approve', confirm: 'Approve this transfer?' },
    { key: 'dispatch', status: 'approved', permission: 'inventory-transfers.dispatch', label: 'Dispatch', confirm: 'Dispatch this transfer? Stock will leave the source store.' },
    { key: 'receive', status: 'in_transit', permission: 'inventory-transfers.receive', label: 'Receive', confirm: 'Receive this transfer into the destination store?' },
]

const availableActions = (transfer) =>
    ACTIONS.filter(action => action.status === transfer.status && can([action.permission]))

const runAction = async (transfer, action) => {
    const updated = await confirmWorkflowAction(`/api/asset/transfers/${transfer.id}/${action.key}`, t(action.confirm))
    if (!updated) return
    if (viewing.value?.id === transfer.id) viewing.value = updated
    fetchData()
}

/* ---------- new transfer ---------- */
const initialForm = () => ({
    from_warehouse_id: '',
    to_warehouse_id: '',
    transfer_date: new Date().toISOString().slice(0, 10),
    remarks: '',
    items: [{ item_id: '', quantity: 1 }],
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
    if (!form.from_warehouse_id) errors.from_warehouse_id = t('Please select a store.')
    if (!form.to_warehouse_id) errors.to_warehouse_id = t('Please select a store.')
    if (Object.keys(errors).length) return

    isSubmitting.value = true
    try {
        const { data } = await axios.post('/api/asset/transfers', {
            ...form,
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

/* ---------- view ---------- */
const viewDialog = ref(false)
const viewing = ref(null)

const openView = async (transfer) => {
    viewing.value = transfer
    viewDialog.value = true
    try {
        const { data } = await axios.get(`/api/asset/transfers/${transfer.id}`)
        viewing.value = data.data
    } catch (err) {
        toast.error(err.response?.data?.message || t('common.failedToLoad'))
    }
}

onMounted(() => {
    fetchData()
    loadLookups()
})
</script>
