<template lang="html">
    <Breadcrumb title="Customer Ledger" buttonText="Back Customers" :buttonLink="{ name: 'admin_customers_list' }"
        buttonIcon="list" />

    <div class="main-content-wrapper mt-4">
        <div class="container-fluid">

            <!-- Customer Summary -->
            <v-card style="padding: 15px; margin-top: 15px;">
                <div class="row">
                    <div class="col-md-3"><strong>Name:</strong> {{ customer.name }}</div>
                    <div class="col-md-3"><strong>Email:</strong> {{ customer.email }}</div>
                    <div class="col-md-3"><strong>Phone:</strong> {{ customer.phone }}</div>
                    <div class="col-md-3">
                        <strong>Status:</strong>
                        <span :class="customer.active_status ? 'badge bg-success' : 'badge bg-secondary'">
                            {{ customer.active_status ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-3"><strong>Opening Balance:</strong> {{ Number(customer.opening_balance ?? 0).toFixed(2) }}</div>
                    <div class="col-md-3"><strong>Current Balance:</strong> {{ Number(customer.current_balance ?? 0).toFixed(2) }}</div>
                </div>
            </v-card>

            <!-- Add Transaction -->
            <v-card style="padding: 15px; margin-top: 15px;" v-if="can(['customer-transactions.create'])">
                <h5 class="mb-3">Add Transaction</h5>
                <form @submit.prevent="handleAddTransaction">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group mb-3">
                                <label>Type:</label>
                                <select class="form-control" v-model="form.transaction_type" required>
                                    <option value="Invoice">Invoice</option>
                                    <option value="Payment Received">Payment Received</option>
                                    <option value="Adjustment">Adjustment</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-3">
                                <label>Direction:</label>
                                <select class="form-control" v-model="form.direction" required>
                                    <option value="debit">Debit (increases balance)</option>
                                    <option value="credit">Credit (decreases balance)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-3">
                                <label>Amount:</label>
                                <input type="number" step="0.01" min="0.01" class="form-control"
                                    v-model.number="form.amount" required />
                                <div v-if="errors.debit" class="error-msg">{{ errors.debit }}</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-3">
                                <label>Transaction Date:</label>
                                <input type="date" class="form-control" v-model="form.transaction_date" required />
                                <div v-if="errors.transaction_date" class="error-msg">{{ errors.transaction_date }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label>Invoice No:</label>
                                <input type="text" class="form-control" v-model="form.invoice_no" />
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group mb-3">
                                <label>Notes:</label>
                                <input type="text" class="form-control" v-model="form.notes" />
                            </div>
                        </div>
                    </div>
                    <v-btn type="submit" class="text-none text-white" color="blue-darken-4" rounded="0"
                        variant="flat" :disabled="isSubmitting" :loading="isSubmitting">
                        Record Transaction
                    </v-btn>
                </form>
            </v-card>

            <!-- Transaction List -->
            <v-card style="padding: 5px; margin-top: 15px;">
                <v-table class="custom-bordered">
                    <thead>
                        <tr>
                            <th class="text-left">Date</th>
                            <th class="text-left">Type</th>
                            <th class="text-left">Invoice No</th>
                            <th class="text-right">Debit</th>
                            <th class="text-right">Credit</th>
                            <th class="text-left">Notes</th>
                            <th class="text-center">Status</th>
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
                            <td colspan="8" class="text-center py-4">No transactions found.</td>
                        </tr>
                        <tr v-else v-for="item in items" :key="item.id">
                            <td>{{ item.transaction_date }}</td>
                            <td>{{ item.transaction_type }}</td>
                            <td>{{ item.invoice_no }}</td>
                            <td class="text-right">{{ Number(item.debit).toFixed(2) }}</td>
                            <td class="text-right">{{ Number(item.credit).toFixed(2) }}</td>
                            <td>{{ item.notes }}</td>
                            <td class="text-center">
                                <span :class="item.status ? 'badge bg-success' : 'badge bg-secondary'">
                                    {{ item.status ? 'Active' : 'Reversed' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <v-btn v-if="item.status && can(['customer-transactions.delete'])" size="small"
                                    color="red-darken-2" variant="text" @click="handleReverse(item)">
                                    Reverse
                                </v-btn>
                            </td>
                        </tr>
                    </tbody>
                </v-table>

                <BasePagination :current-page="pagination.page" :per-page="pagination.perPage"
                    :total="pagination.total" :last-page="pagination.lastPage" @page-change="changePage"
                    @per-page-change="changePerPage" />
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
import { usePermission } from '@/composables/usePermission';
import { useRoute } from 'vue-router';

const route = useRoute();
const { can } = usePermission()

const customer = ref({})

const {
    items,
    loading,
    pagination,
    fetchData,
    changePage,
    changePerPage,
} = usePaginatedFetch(`/api/customers/${route.params.id}/transactions`)

const form = reactive({
    transaction_type: 'Invoice',
    direction: 'debit',
    amount: null,
    transaction_date: new Date().toISOString().slice(0, 10),
    invoice_no: '',
    notes: '',
})
const errors = reactive({})
const isSubmitting = ref(false)

const loadCustomer = async () => {
    try {
        const { data } = await axios.get(`/api/customers/${route.params.id}`)
        if (data.success) customer.value = data.data
    } catch (e) {
        console.error(e)
    }
}

const handleAddTransaction = async () => {
    isSubmitting.value = true
    for (const key in errors) delete errors[key]

    const payload = {
        transaction_type: form.transaction_type,
        invoice_no: form.invoice_no || null,
        transaction_date: form.transaction_date,
        notes: form.notes || null,
        debit: form.direction === 'debit' ? form.amount : 0,
        credit: form.direction === 'credit' ? form.amount : 0,
    }

    try {
        const { data } = await axios.post(`/api/customers/${route.params.id}/transactions`, payload)
        if (data.success) {
            toast.success(data.message)
            form.amount = null
            form.invoice_no = ''
            form.notes = ''
            fetchData()
            loadCustomer()
        }
    } catch (e) {
        if (e.response?.status === 422) {
            const respErrors = e.response.data.data
            for (const key in respErrors) errors[key] = respErrors[key].join(' ')
        } else {
            toast.error(e.response?.data?.message || 'Failed to record transaction.')
        }
    } finally {
        isSubmitting.value = false
    }
}

const handleReverse = async (item) => {
    const result = await Swal.fire({
        title: 'Are you sure?',
        text: 'This reverses the transaction and restores the customer balance.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, reverse',
        cancelButtonText: 'Cancel',
    })

    if (!result.isConfirmed) return

    try {
        const { data } = await axios.delete(`/api/customers/${route.params.id}/transactions/${item.id}`)
        if (data.success) {
            toast.success(data.message)
            fetchData()
            loadCustomer()
        }
    } catch (e) {
        toast.error(e.response?.data?.message || 'Failed to reverse transaction.')
    }
}

onMounted(() => {
    loadCustomer()
    fetchData()
})
</script>
