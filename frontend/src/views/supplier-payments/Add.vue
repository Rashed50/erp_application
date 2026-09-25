<template lang="html">
    <Breadcrumb title="Supplier Payment" buttonText="Back Payments" :buttonLink="{ name: 'admin_supplier_payments_list' }"
        buttonIcon="list" />

    <div class="main-content-wrapper mt-4">
        <div class="container-fluid">
            <v-card style="padding: 15px 5px; margin: 15px 0px;">
                <form @submit.prevent="handleSubmit">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <!-- Credit Account -->
                            <div class="row mt-2">
                                <div class="col-md-3">
                                    <label for="payment_account_id" class="form-label text-end d-block">
                                        Credit Account <span class="text-danger">*</span>
                                    </label>
                                </div>
                                <div class="col-md-9">
                                    <select id="payment_account_id" class="form-control input-height"
                                        v-model="form.payment_account_id" required>
                                        <option value="" disabled>Select item</option>
                                        <option v-for="account in creditAccounts" :key="account.id" :value="account.id">
                                            {{ account.account_number }} - {{ account.name }}
                                        </option>
                                    </select>
                                    <div v-if="errors.payment_account_id" class="error-msg">{{ errors.payment_account_id }}</div>
                                </div>
                            </div>

                            <!-- Payment Date -->
                            <div class="row mt-2">
                                <div class="col-md-3">
                                    <label for="payment_date" class="form-label text-end d-block">
                                        Payment Date <span class="text-danger">*</span>
                                    </label>
                                </div>
                                <div class="col-md-9">
                                    <input type="date" id="payment_date" class="form-control input-height"
                                        v-model="form.payment_date" :max="today" required />
                                    <div v-if="errors.payment_date" class="error-msg">{{ errors.payment_date }}</div>
                                </div>
                            </div>

                            <!-- Bill Amount -->
                            <div class="row mt-2">
                                <div class="col-md-3">
                                    <label for="bill_amount" class="form-label text-end d-block">
                                        Bill Amount <span class="text-danger">*</span>
                                    </label>
                                </div>
                                <div class="col-md-9">
                                    <input type="number" id="bill_amount" class="form-control input-height" step="0.01"
                                        min="0.01" :max="selectedPurchase ? selectedPurchase.due_amount : null"
                                        placeholder="Bill Amount..." v-model="form.bill_amount" required />
                                    <div v-if="errors.bill_amount" class="error-msg">{{ errors.bill_amount }}</div>
                                </div>
                            </div>

                            <!-- Total -->
                            <div class="row mt-2">
                                <div class="col-md-3">
                                    <label class="form-label text-end d-block">Total</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" class="form-control input-height" :value="total.toFixed(2)" disabled />
                                </div>
                            </div>

                            <!-- Remarks -->
                            <div class="row mt-2">
                                <div class="col-md-3">
                                    <label for="remarks" class="form-label text-end d-block">Remarks</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" id="remarks" class="form-control input-height"
                                        placeholder="Remarks..." v-model="form.remarks" />
                                    <div v-if="errors.remarks" class="error-msg">{{ errors.remarks }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <!-- Supplier -->
                            <div class="row mt-2">
                                <div class="col-md-3">
                                    <label for="supplier_id" class="form-label text-end d-block">
                                        Supplier <span class="text-danger">*</span>
                                    </label>
                                </div>
                                <div class="col-md-9">
                                    <select id="supplier_id" class="form-control input-height" v-model="form.supplier_id"
                                        required>
                                        <option value="" disabled>Select item</option>
                                        <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">
                                            {{ supplier.name }} (Due: {{ Number(supplier.current_balance).toFixed(2) }})
                                        </option>
                                    </select>
                                    <div v-if="errors.supplier_id" class="error-msg">{{ errors.supplier_id }}</div>
                                </div>
                            </div>

                            <!-- Purchase Invoice (optional) -->
                            <div class="row mt-2">
                                <div class="col-md-3">
                                    <label for="purchase_id" class="form-label text-end d-block">Purchase Invoice</label>
                                </div>
                                <div class="col-md-9">
                                    <select id="purchase_id" class="form-control input-height" v-model="form.purchase_id"
                                        :disabled="!form.supplier_id">
                                        <option value="">None (general bill payment)</option>
                                        <option v-for="purchase in duePurchases" :key="purchase.id" :value="purchase.id">
                                            {{ purchase.invoice_number }} (Due: {{ Number(purchase.due_amount).toFixed(2) }})
                                        </option>
                                    </select>
                                    <div v-if="errors.purchase_id" class="error-msg">{{ errors.purchase_id }}</div>
                                </div>
                            </div>

                            <!-- Invoice Number -->
                            <div class="row mt-2" v-if="!form.purchase_id">
                                <div class="col-md-3">
                                    <label for="invoice_no" class="form-label text-end d-block">Invoice No</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" id="invoice_no" class="form-control input-height"
                                        placeholder="Invoice Number..." v-model="form.invoice_no" />
                                    <div v-if="errors.invoice_no" class="error-msg">{{ errors.invoice_no }}</div>
                                </div>
                            </div>

                            <!-- Bank Charge -->
                            <div class="row mt-2">
                                <div class="col-md-3">
                                    <label for="bank_charge" class="form-label text-end d-block">
                                        Bank Charge <span class="text-danger">*</span>
                                    </label>
                                </div>
                                <div class="col-md-9">
                                    <input type="number" id="bank_charge" class="form-control input-height" step="0.01"
                                        min="0" placeholder="Bank Charge..." v-model="form.bank_charge" required />
                                    <div v-if="errors.bank_charge" class="error-msg">{{ errors.bank_charge }}</div>
                                </div>
                            </div>

                            <!-- Attachment -->
                            <div class="row mt-2">
                                <div class="col-md-3">
                                    <label for="attachment" class="form-label text-end d-block">Attachment</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="file" id="attachment" ref="attachmentInput" class="form-control"
                                        accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx" @change="handleFileUpload" />
                                    <div v-if="errors.attachment" class="error-msg">{{ errors.attachment }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end" v-if="can(['supplier-payments.create'])">
                        <v-btn type="submit" class="text-none text-white" color="success" rounded="0" variant="flat"
                            :disabled="isSubmitting" :loading="isSubmitting">
                            <i class="fa-solid fa-check me-2"></i> Save
                        </v-btn>
                    </div>
                </form>
            </v-card>
        </div>
    </div>

</template>
<script setup>
import axios from 'axios';
import { toast } from 'vue3-toastify';
import Breadcrumb from '@/components/common/Breadcrumb.vue';
import { useFetch } from '@/composables/useFetch';
import { usePermission } from '@/composables/usePermission';
import { objectToFormData } from '@/helpers/objectToFormData';

const { can } = usePermission()

const today = new Date().toISOString().slice(0, 10)

const initialForm = () => ({
    payment_account_id: '',
    payment_date: today,
    bill_amount: '',
    remarks: '',
    supplier_id: '',
    purchase_id: '',
    invoice_no: '',
    bank_charge: 0,
    attachment: null,
})

const form = reactive(initialForm())
const errors = reactive({})
const isSubmitting = ref(false)
const attachmentInput = ref(null)

// Account type id of Asset, as seeded by the backend (account_types table).
const ASSET = 1

// Money can only leave an open asset (cash/bank) transaction account.
const { items: creditAccounts, fetchData: loadCreditAccounts } = useFetch('/api/ledger-accounts', {
    per_page: 500,
    account_type_id: ASSET,
    is_transaction: 1,
    active_status: 1,
    is_closed: 0,
})

const { items: suppliers, fetchData: loadSuppliers } = useFetch('/api/suppliers', {
    per_page: 500,
    active_status: 1,
})

const purchases = ref([])
const duePurchases = computed(() => purchases.value.filter((p) => p.due_amount > 0))
const selectedPurchase = computed(() => duePurchases.value.find((p) => p.id === form.purchase_id))

const total = computed(() => (parseFloat(form.bill_amount) || 0) + (parseFloat(form.bank_charge) || 0))

// A purchase belongs to one supplier, so reload the invoices whenever the supplier changes.
watch(() => form.supplier_id, async (supplierId) => {
    form.purchase_id = ''
    purchases.value = []

    if (!supplierId) return

    try {
        const { data } = await axios.get('/api/purchases', { params: { supplier_id: supplierId, per_page: 500 } })
        purchases.value = data.data?.purchases ?? []
    } catch (e) {
        toast.error('Failed to load the supplier purchases.')
    }
})

// Paying a specific invoice defaults the bill amount to what is still due on it.
watch(selectedPurchase, (purchase) => {
    if (purchase) {
        form.bill_amount = purchase.due_amount
    }
})

const handleFileUpload = (event) => {
    form.attachment = event.target.files[0] ?? null
}

const clearErrors = () => {
    for (const key in errors) delete errors[key]
}

const resetForm = () => {
    Object.assign(form, initialForm())
    if (attachmentInput.value) {
        attachmentInput.value.value = ''
    }
    clearErrors()

    // The supplier due amounts shown in the dropdown changed with this payment.
    loadSuppliers()
}

const handleSubmit = async () => {
    isSubmitting.value = true
    clearErrors()

    try {
        const payload = { ...form }
        if (!payload.purchase_id) delete payload.purchase_id
        if (payload.purchase_id) delete payload.invoice_no

        const { data } = await axios.post('/api/accounting/purchase/payment/store', objectToFormData(payload))

        // Like the payroll module, stay on the form and clear it for the next payment.
        if (data.success) {
            toast.success(data.message)
            resetForm()
        }
    } catch (err) {
        if (err.response?.status === 422) {
            const respErrors = err.response.data.data
            for (const key in respErrors) {
                errors[key] = respErrors[key].join(' ')
            }
        } else {
            toast.error(err.response?.data?.message || 'An error occurred while saving.')
        }
    } finally {
        isSubmitting.value = false
    }
}

onMounted(() => {
    loadCreditAccounts()
    loadSuppliers()
})
</script>

<style scoped>
.input-height {
    height: 43px;
}
</style>
