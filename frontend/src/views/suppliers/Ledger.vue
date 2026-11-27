<template lang="html">
    <Breadcrumb title="Supplier Ledger" buttonText="Back Suppliers" :buttonLink="{ name: 'admin_suppliers_list' }"
        buttonIcon="list" />

    <div class="main-content-wrapper mt-4">
        <div class="container-fluid">

            <!-- Supplier Summary -->
            <v-card style="padding: 15px; margin-top: 15px;">
                <div class="row">
                    <div class="col-md-3"><strong>{{ $t('Name:') }}</strong> {{ supplier.name }}</div>
                    <div class="col-md-3"><strong>{{ $t('Email:') }}</strong> {{ supplier.email }}</div>
                    <div class="col-md-3"><strong>{{ $t('Phone:') }}</strong> {{ supplier.phone }}</div>
                    <div class="col-md-3">
                        <strong>{{ $t('Status:') }}</strong>
                        <span :class="supplier.active_status ? 'badge bg-success' : 'badge bg-secondary'">
                            {{ $t(supplier.active_status ? 'Active' : 'Inactive') }}
                        </span>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-3"><strong>{{ $t('Opening Balance:') }}</strong> {{ Number(supplier.opening_balance ?? 0).toFixed(2) }}</div>
                    <div class="col-md-3"><strong>{{ $t('Current Balance:') }}</strong> {{ Number(supplier.current_balance ?? 0).toFixed(2) }}</div>
                </div>
            </v-card>

            <!-- Add Transaction -->
            <v-card style="padding: 15px; margin-top: 15px;" v-if="can(['supplier-transactions.create'])">
                <h5 class="mb-3">{{ $t('Add Transaction') }}</h5>
                <form @submit.prevent="handleAddTransaction">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label>{{ $t('Type:') }}</label>
                                <select class="form-control" v-model="form.transaction_type" required>
                                    <option value="Purchase">{{ $t('Purchase') }}</option>
                                    <option value="Bill Payment">{{ $t('Bill Payment') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label>{{ $t('Amount:') }}</label>
                                <input type="number" step="0.01" min="0.01" class="form-control"
                                    v-model.number="form.amount" required />
                                <div v-if="errors.debit || errors.credit" class="error-msg">
                                    {{ errors.debit || errors.credit }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label>{{ $t('Transaction Date:') }}</label>
                                <input type="date" class="form-control" v-model="form.transaction_date" required />
                                <div v-if="errors.transaction_date" class="error-msg">{{ errors.transaction_date }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label>{{ $t('Payment Account (Cash/Bank):') }}</label>
                                <select class="form-control" v-model="form.payment_account_id"
                                    :disabled="!isBillPayment" :required="isBillPayment">
                                    <option value="" disabled>{{ $t('Select item') }}</option>
                                    <option v-for="account in paymentAccounts" :key="account.id" :value="account.id">
                                        {{ account.account_number }} - {{ account.name }}
                                    </option>
                                </select>
                                <small v-if="!isBillPayment" class="text-muted">
                                    {{ $t('Select Bill Payment to enable this account.') }}
                                </small>
                                <div v-if="errors.payment_account_id" class="error-msg">{{ errors.payment_account_id }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label>{{ $t('Invoice No:') }}</label>
                                <input type="text" class="form-control" v-model="form.invoice_no" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label>{{ $t('Notes:') }}</label>
                                <input type="text" class="form-control" v-model="form.notes" />
                            </div>
                        </div>
                    </div>
                    <v-btn type="submit" class="text-none text-white" color="blue-darken-4" rounded="0"
                        variant="flat" :disabled="isSubmitting" :loading="isSubmitting">
                        {{ $t('Record Transaction') }}
                    </v-btn>
                </form>
            </v-card>

            <!-- Transaction List -->
            <v-card style="padding: 5px; margin-top: 15px;">
                <v-table class="custom-bordered">
                    <thead>
                        <tr>
                            <th class="text-left">{{ $t('Date') }}</th>
                            <th class="text-left">{{ $t('Type') }}</th>
                            <th class="text-left">{{ $t('Invoice No') }}</th>
                            <th class="text-right">{{ $t('Debit') }}</th>
                            <th class="text-right">{{ $t('Credit') }}</th>
                            <th class="text-left">{{ $t('Notes') }}</th>
                            <th class="text-center">{{ $t('Status') }}</th>
                            <th class="text-left">{{ $t('Created By') }}</th>
                            <th class="text-center">{{ $t('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="loading">
                            <td colspan="9" class="text-center py-4">
                                <v-progress-linear indeterminate color="primary" size="30"></v-progress-linear>
                                {{ $t('Loading...') }}
                            </td>
                        </tr>
                        <tr v-else-if="!items.length">
                            <td colspan="9" class="text-center py-4">{{ $t('No transactions found.') }}</td>
                        </tr>
                        <tr v-else v-for="item in items" :key="item.id">
                            <td>{{ item.transaction_date }}</td>
                            <td>{{ $t(item.transaction_type) }}</td>
                            <td>{{ item.invoice_no }}</td>
                            <td class="text-right">{{ Number(item.debit).toFixed(2) }}</td>
                            <td class="text-right">{{ Number(item.credit).toFixed(2) }}</td>
                            <td>{{ item.notes }}</td>
                            <td class="text-center">
                                <span :class="item.status ? 'badge bg-success' : 'badge bg-secondary'">
                                    {{ $t(item.status ? 'Active' : 'Reversed') }}
                                </span>
                            </td>
                            <td><CreatedInfo :name="item.created_by_name" :at="item.created_at" /></td>
                            <td class="text-center">
                                <v-btn v-if="item.status && can(['supplier-transactions.delete'])" size="small"
                                    color="red-darken-2" variant="text" @click="handleReverse(item)">
                                    {{ $t('Reverse') }}
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
import CreatedInfo from '@/components/common/CreatedInfo.vue';
import axios from 'axios';
import Swal from 'sweetalert2';
import { toast } from 'vue3-toastify';
import BasePagination from '@/components/common/BasePagination.vue';
import Breadcrumb from '@/components/common/Breadcrumb.vue';
import { useFetch } from '@/composables/useFetch';
import { usePaginatedFetch } from '@/composables/usePaginatedFetch';
import { usePermission } from '@/composables/usePermission';
import { useRoute } from 'vue-router';
import { t } from '@/i18n';

const route = useRoute();
const { can } = usePermission()

const supplier = ref({})

const {
    items,
    loading,
    pagination,
    fetchData,
    changePage,
    changePerPage,
} = usePaginatedFetch(`/api/suppliers/${route.params.id}/transactions`)

const form = reactive({
    transaction_type: 'Purchase',
    amount: null,
    transaction_date: new Date().toISOString().slice(0, 10),
    invoice_no: '',
    payment_account_id: '',
    notes: '',
})
const errors = reactive({})
const isSubmitting = ref(false)

const isBillPayment = computed(() => form.transaction_type === 'Bill Payment')

// Account type id of Asset, as seeded by the backend (account_types table).
const ASSET = 1

// Only open cash/bank accounts can be selected for a bill payment.
const { items: paymentAccounts, fetchData: loadPaymentAccounts } = useFetch('/api/ledger-accounts', {
    per_page: 500,
    approved: 1,
    account_type_id: ASSET,
    is_transaction: 1,
    active_status: 1,
    is_closed: 0,
})

watch(isBillPayment, (billPayment) => {
    if (billPayment) {
        if (!paymentAccounts.value.length) loadPaymentAccounts()
    } else {
        form.payment_account_id = ''
    }
})

const loadSupplier = async () => {
    try {
        const { data } = await axios.get(`/api/suppliers/${route.params.id}`)
        if (data.success) supplier.value = data.data
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
        debit: isBillPayment.value ? form.amount : 0,
        credit: isBillPayment.value ? 0 : form.amount,
        payment_account_id: isBillPayment.value ? form.payment_account_id : null,
    }

    try {
        const { data } = await axios.post(`/api/suppliers/${route.params.id}/transactions`, payload)
        if (data.success) {
            toast.success(data.message)
            form.amount = null
            form.invoice_no = ''
            form.notes = ''
            fetchData()
            loadSupplier()
        }
    } catch (e) {
        if (e.response?.status === 422) {
            const respErrors = e.response.data.data
            for (const key in respErrors) errors[key] = respErrors[key].join(' ')
        } else {
            toast.error(e.response?.data?.message || t('Failed to record transaction.'))
        }
    } finally {
        isSubmitting.value = false
    }
}

const handleReverse = async (item) => {
    const result = await Swal.fire({
        title: t('Are you sure?'),
        text: t('This reverses the transaction and restores the supplier balance.'),
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: t('Yes, reverse'),
        cancelButtonText: t('Cancel'),
    })

    if (!result.isConfirmed) return

    try {
        const { data } = await axios.delete(`/api/suppliers/${route.params.id}/transactions/${item.id}`)
        if (data.success) {
            toast.success(data.message)
            fetchData()
            loadSupplier()
        }
    } catch (e) {
        toast.error(e.response?.data?.message || t('Failed to reverse transaction.'))
    }
}

onMounted(() => {
    loadSupplier()
    fetchData()
})
</script>
