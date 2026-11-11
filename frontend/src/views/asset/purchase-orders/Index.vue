<template lang="html">
    <Breadcrumb title="Purchase Orders" />

    <div class="main-content-wrapper mt-4">
        <div class="container-fluid">
            <v-card style="padding: 5px; margin-top: 15px;">
                <div class="row">
                    <div class="col-md-12 mb-2">
                        <div class="row align-items-center g-2">
                            <div class="col-md-2">
                                <v-btn v-if="can(['purchase-orders.create'])" type="button" @click="openAddDialog"
                                    class="text-none text-white" color="success" rounded="0" variant="flat">
                                    <i class="fa-solid fa-plus me-2"></i> {{ $t('New Purchase Order') }}
                                </v-btn>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" v-model="filters.supplier_id">
                                    <option value="">{{ $t('All Suppliers') }}</option>
                                    <option v-for="s in lookups.suppliers" :key="s.id" :value="s.id">{{ s.name }}</option>
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
                                <th class="text-left">{{ $t('PO No') }}</th>
                                <th class="text-left">{{ $t('Date') }}</th>
                                <th class="text-left">{{ $t('Supplier') }}</th>
                                <th class="text-left">{{ $t('Material Request') }}</th>
                                <th class="text-right">{{ $t('Grand Total') }}</th>
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
                            <tr v-else v-for="(order, index) in items" :key="order.id">
                                <td>{{ (pagination.page - 1) * pagination.perPage + index + 1 }}</td>
                                <td>{{ order.po_no }}</td>
                                <td>{{ order.purchase_date }}</td>
                                <td>{{ order.supplier_name }}</td>
                                <td>{{ order.material_request_no || '-' }}</td>
                                <td class="text-right">{{ Number(order.grand_total).toFixed(2) }}</td>
                                <td class="text-center">
                                    <span :class="statusClass(order.status)">{{ $t(statusLabel(order.status)) }}</span>
                                </td>
                                <td>{{ order.created_by_name || '-' }}</td>
                                <td>{{ order.approved_by_name || '-' }}</td>
                                <td class="text-center">
                                    <button type="button" class="table-action-button" @click="openView(order)">
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

    <!-- New purchase order -->
    <v-dialog v-model="dialog" max-width="1000">
        <v-card>
            <v-card-title>{{ $t('New Purchase Order') }}</v-card-title>
            <v-card-text>
                <div class="row">
                    <div class="col-md-4 form-group mb-3">
                        <label>{{ $t('Supplier:') }} <span class="text-danger">*</span></label>
                        <SearchSelect v-model="form.supplier_id" :items="lookups.suppliers" :error="errors.supplier_id"
                            :placeholder="$t('Select Supplier')" />
                    </div>
                    <div class="col-md-4 form-group mb-3" v-if="can(['material-requests.view'])">
                        <label>{{ $t('Against Material Request:') }}</label>
                        <SearchSelect v-model="form.material_request_id" :items="openRequests" item-title="request_no"
                            :error="errors.material_request_id" :placeholder="$t('None')"
                            @update:model-value="fillFromRequest" />
                    </div>
                    <div class="col-md-2 form-group mb-3">
                        <label>{{ $t('Order Date:') }} <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" v-model="form.purchase_date" />
                        <div v-if="errors.purchase_date" class="error-msg">{{ errors.purchase_date }}</div>
                    </div>
                    <div class="col-md-2 form-group mb-3">
                        <label>{{ $t('Expected Date:') }}</label>
                        <input type="date" class="form-control" v-model="form.expected_date" />
                        <div v-if="errors.expected_date" class="error-msg">{{ errors.expected_date }}</div>
                    </div>
                </div>

                <LineItems v-model="form.items" :items="lookups.items" with-price />

                <div class="form-group mt-3">
                    <label>{{ $t('Remarks:') }}</label>
                    <textarea class="form-control" rows="2" v-model="form.remarks"></textarea>
                </div>
                <div v-for="(message, i) in otherErrors(errors, formFields)" :key="i" class="error-msg">{{ message }}</div>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn class="text-none" color="grey-lighten-3" rounded="0" variant="flat" @click="dialog = false">
                    {{ $t('Cancel') }}
                </v-btn>
                <v-btn class="text-none text-white" color="blue-darken-4" rounded="0" variant="flat"
                    :disabled="isSubmitting" :loading="isSubmitting" @click="handleSubmit">
                    {{ $t('Save') }}
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>

    <!-- View order -->
    <v-dialog v-model="viewDialog" max-width="900">
        <v-card v-if="viewing">
            <v-card-title class="d-flex align-center">
                {{ viewing.po_no }}
                <span class="ms-3" :class="statusClass(viewing.status)">{{ $t(statusLabel(viewing.status)) }}</span>
            </v-card-title>
            <v-card-text>
                <div class="row mb-3">
                    <div class="col-md-4"><strong>{{ $t('Supplier') }}:</strong> {{ viewing.supplier_name }}</div>
                    <div class="col-md-4"><strong>{{ $t('Order Date') }}:</strong> {{ viewing.purchase_date }}</div>
                    <div class="col-md-4"><strong>{{ $t('Expected Date') }}:</strong> {{ viewing.expected_date || '-' }}</div>
                    <div class="col-md-4"><strong>{{ $t('Material Request') }}:</strong> {{ viewing.material_request_no || '-' }}</div>
                    <div class="col-md-4"><strong>{{ $t('Created By') }}:</strong> {{ viewing.created_by_name || '-' }}</div>
                    <div class="col-md-4"><strong>{{ $t('Approved By') }}:</strong> {{ viewing.approved_by_name || '-' }}</div>
                </div>

                <v-table class="custom-bordered mb-3">
                    <thead>
                        <tr>
                            <th class="text-left">{{ $t('Item') }}</th>
                            <th class="text-right">{{ $t('Ordered') }}</th>
                            <th class="text-right">{{ $t('Received') }}</th>
                            <th class="text-right">{{ $t('Unit Price') }}</th>
                            <th class="text-right">{{ $t('Total') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="line in viewing.items" :key="line.id">
                            <td>{{ line.item_code }} - {{ line.item_name }}</td>
                            <td class="text-right">{{ qty(line.quantity) }}</td>
                            <td class="text-right">{{ qty(line.received_qty) }}</td>
                            <td class="text-right">{{ Number(line.unit_price).toFixed(2) }}</td>
                            <td class="text-right">{{ Number(line.total).toFixed(2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="4" class="text-right"><strong>{{ $t('Grand Total') }}</strong></td>
                            <td class="text-right"><strong>{{ Number(viewing.grand_total).toFixed(2) }}</strong></td>
                        </tr>
                    </tbody>
                </v-table>

                <template v-if="viewing.receipts?.length">
                    <h6>{{ $t('Goods Receipts') }}</h6>
                    <v-table class="custom-bordered">
                        <thead>
                            <tr>
                                <th class="text-left">{{ $t('Receipt No') }}</th>
                                <th class="text-left">{{ $t('Date') }}</th>
                                <th class="text-left">{{ $t('Store') }}</th>
                                <th class="text-left">{{ $t('Items') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="receipt in viewing.receipts" :key="receipt.id">
                                <td>{{ receipt.receipt_no }}</td>
                                <td>{{ receipt.received_date }}</td>
                                <td>{{ receipt.warehouse_name }}</td>
                                <td>
                                    <div v-for="line in receipt.items" :key="line.id">
                                        {{ line.item_name }} × {{ qty(line.quantity) }}
                                        <small v-if="line.serial_numbers?.length" class="text-muted">
                                            ({{ line.serial_numbers.join(', ') }})
                                        </small>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </v-table>
                </template>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn class="text-none" color="grey-lighten-3" rounded="0" variant="flat" @click="viewDialog = false">
                    {{ $t('Close') }}
                </v-btn>
                <v-btn v-if="viewing.status === 'submitted' && can(['purchase-orders.approve'])"
                    class="text-none text-white" color="success" rounded="0" variant="flat" @click="approve">
                    {{ $t('Approve') }}
                </v-btn>
                <v-btn v-if="isReceivable && can(['purchase-orders.receive'])" class="text-none text-white"
                    color="blue-darken-4" rounded="0" variant="flat" @click="openReceive">
                    {{ $t('Receive Goods') }}
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>

    <!-- Receive goods -->
    <v-dialog v-model="receiveDialog" max-width="900">
        <v-card v-if="viewing">
            <v-card-title>{{ $t('Receive Goods') }} — {{ viewing.po_no }}</v-card-title>
            <v-card-text>
                <div class="row">
                    <div class="col-md-6 form-group mb-3">
                        <label>{{ $t('Receiving Store:') }} <span class="text-danger">*</span></label>
                        <SearchSelect v-model="receiveForm.warehouse_id" :items="lookups.warehouses" item-title="name"
                            :error="receiveErrors.warehouse_id" :placeholder="$t('Select Store')" />
                    </div>
                    <div class="col-md-3 form-group mb-3">
                        <label>{{ $t('Received Date:') }}</label>
                        <input type="date" class="form-control" v-model="receiveForm.received_date" />
                    </div>
                </div>

                <v-table class="custom-bordered">
                    <thead>
                        <tr>
                            <th class="text-left">{{ $t('Item') }}</th>
                            <th class="text-right">{{ $t('Remaining') }}</th>
                            <th class="text-right" style="width: 120px">{{ $t('Receive Qty') }}</th>
                            <th class="text-left">{{ $t('Serial Numbers') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="line in receiveForm.lines" :key="line.purchase_order_item_id">
                            <td>{{ line.label }}</td>
                            <td class="text-right">{{ line.remaining }}</td>
                            <td>
                                <!-- Serial-tracked: one unit received per serial number entered. -->
                                <input v-if="line.serialTracked" type="number" class="form-control text-end"
                                    :value="serialList(line).length" readonly />
                                <input v-else type="number" min="0" :max="line.remaining" step="any"
                                    class="form-control text-end" v-model="line.quantity" />
                            </td>
                            <td>
                                <template v-if="line.serialTracked">
                                    <textarea class="form-control form-control-sm" rows="2" v-model="line.serials"
                                        :placeholder="$t('Comma separated, e.g. SN001, SN002, SN003')"></textarea>
                                    <small :class="serialList(line).length > line.remaining ? 'text-danger' : 'text-muted'">
                                        {{ $t('{count} serial(s) = {count} unit(s) will be received', { count: serialList(line).length }) }}
                                    </small>
                                </template>
                                <span v-else class="text-muted">-</span>
                            </td>
                        </tr>
                    </tbody>
                </v-table>
                <div v-for="(message, i) in otherErrors(receiveErrors, ['warehouse_id'])" :key="i" class="error-msg">
                    {{ message }}
                </div>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn class="text-none" color="grey-lighten-3" rounded="0" variant="flat"
                    @click="receiveDialog = false">
                    {{ $t('Cancel') }}
                </v-btn>
                <v-btn class="text-none text-white" color="blue-darken-4" rounded="0" variant="flat"
                    :disabled="isReceiving" :loading="isReceiving" @click="submitReceive">
                    {{ $t('Receive') }}
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
    usePaginatedFetch('/api/asset/purchase-orders', { status: '', supplier_id: '' })

const { can } = usePermission()
const { lookups, loadLookups, itemById } = useInventoryLookups()

const statuses = computed(() => payload.value.statuses || [])
const today = () => new Date().toISOString().slice(0, 10)

/* ---------- new order ---------- */
const formFields = ['supplier_id', 'material_request_id', 'purchase_date', 'expected_date']

const emptyLine = () => ({ item_id: '', quantity: 1, unit_price: 0, discount: 0, tax: 0 })

const initialForm = () => ({
    supplier_id: '',
    material_request_id: '',
    purchase_date: today(),
    expected_date: '',
    remarks: '',
    items: [emptyLine()],
})

const dialog = ref(false)
const form = reactive(initialForm())
const errors = reactive({})
const isSubmitting = ref(false)
const openRequests = ref([])

const clearErrors = (target = errors) => {
    for (const key in target) delete target[key]
}

// Approved material requests that still have quantities left to order.
const loadOpenRequests = async () => {
    if (!can(['material-requests.view'])) return
    try {
        const [approved, partial] = await Promise.all(['approved', 'partially_purchased'].map(status =>
            axios.get('/api/asset/material-requests', { params: { status, per_page: 1000 } })))
        openRequests.value = [
            ...(approved.data.data?.material_requests || []),
            ...(partial.data.data?.material_requests || []),
        ]
    } catch (err) {
        toast.error(err.response?.data?.message || t('common.failedToLoad'))
    }
}

const fillFromRequest = (requestId) => {
    const request = openRequests.value.find(r => r.id === requestId)
    if (!request) return
    const lines = request.items
        .map(line => ({ ...emptyLine(), item_id: line.item_id, quantity: qty(line.approved_qty) - qty(line.purchased_qty) }))
        .filter(line => line.quantity > 0)
    form.items = lines.length ? lines : [emptyLine()]
}

const openAddDialog = () => {
    Object.assign(form, initialForm())
    clearErrors()
    dialog.value = true
    loadOpenRequests()
}

const handleSubmit = async () => {
    clearErrors()
    if (!form.supplier_id) errors.supplier_id = t('Please select a supplier.')
    if (Object.keys(errors).length) return

    isSubmitting.value = true
    try {
        const { data } = await axios.post('/api/asset/purchase-orders', {
            ...form,
            material_request_id: form.material_request_id || null,
            expected_date: form.expected_date || null,
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

const isReceivable = computed(() => ['approved', 'partially_received'].includes(viewing.value?.status))

const loadOrder = async (orderId) => {
    try {
        const { data } = await axios.get(`/api/asset/purchase-orders/${orderId}`)
        viewing.value = data.data
    } catch (err) {
        toast.error(err.response?.data?.message || t('common.failedToLoad'))
    }
}

const openView = async (order) => {
    viewing.value = order
    viewDialog.value = true
    await loadOrder(order.id)
}

const approve = async () => {
    const updated = await confirmWorkflowAction(
        `/api/asset/purchase-orders/${viewing.value.id}/approve`,
        t('Approve this purchase order?'),
    )
    if (updated) {
        await loadOrder(viewing.value.id)
        fetchData()
    }
}

/* ---------- receive goods ---------- */
const receiveDialog = ref(false)
const receiveForm = reactive({ warehouse_id: '', received_date: today(), lines: [] })
const receiveErrors = reactive({})
const isReceiving = ref(false)

const openReceive = () => {
    clearErrors(receiveErrors)
    receiveForm.warehouse_id = ''
    receiveForm.received_date = today()
    receiveForm.lines = viewing.value.items
        .map(line => {
            const remaining = qty(line.quantity) - qty(line.received_qty)
            return {
                purchase_order_item_id: line.id,
                label: `${line.item_code} - ${line.item_name}`,
                remaining,
                quantity: remaining,
                serialTracked: itemById(line.item_id)?.track_type === 'serial',
                serials: '',
            }
        })
        .filter(line => line.remaining > 0)
    receiveDialog.value = true
}

// Serial numbers are comma separated (new lines are accepted too).
const serialList = (line) => line.serials.split(/[,\n]/).map(s => s.trim()).filter(Boolean)

const submitReceive = async () => {
    clearErrors(receiveErrors)
    if (!receiveForm.warehouse_id) receiveErrors.warehouse_id = t('Please select a store.')
    receiveForm.lines.filter(line => line.serialTracked).forEach((line) => {
        const serials = serialList(line)
        if (serials.length > line.remaining) {
            receiveErrors[`serials_${line.purchase_order_item_id}`] = t('{item}: {count} serial numbers entered but only {remaining} remaining.', { item: line.label, count: serials.length, remaining: line.remaining })
        } else if (new Set(serials).size !== serials.length) {
            receiveErrors[`serials_${line.purchase_order_item_id}`] = t('{item}: the same serial number is entered twice.', { item: line.label })
        }
    })
    if (Object.keys(receiveErrors).length) return

    const lines = receiveForm.lines
        .map(line => {
            const serials = line.serialTracked ? serialList(line) : []
            return {
                purchase_order_item_id: line.purchase_order_item_id,
                quantity: line.serialTracked ? serials.length : Number(line.quantity),
                serials: serials.map(serial_number => ({ serial_number })),
            }
        })
        .filter(line => line.quantity > 0)

    if (!lines.length) {
        receiveErrors.items = t('Enter a quantity or serial numbers to receive.')
        return
    }

    isReceiving.value = true
    try {
        const { data } = await axios.post(`/api/asset/purchase-orders/${viewing.value.id}/receive`, {
            warehouse_id: receiveForm.warehouse_id,
            received_date: receiveForm.received_date,
            items: lines,
        })
        if (data.success) {
            toast.success(data.message)
            receiveDialog.value = false
            await loadOrder(viewing.value.id)
            fetchData()
        }
    } catch (err) {
        fillValidationErrors(err, receiveErrors)
    } finally {
        isReceiving.value = false
    }
}

onMounted(() => {
    fetchData()
    loadLookups()
})
</script>
