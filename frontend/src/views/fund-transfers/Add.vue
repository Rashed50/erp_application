<template lang="html">
    <Breadcrumb title="Internal Fund Transfer" buttonText="Back Transfers"
        :buttonLink="{ name: 'admin_fund_transfers_list' }" buttonIcon="list" />

    <div class="main-content-wrapper mt-4">
        <div class="container-fluid">
            <v-card style="padding: 15px 5px; margin: 15px 0px;">
                <form @submit.prevent="handleSubmit">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <!-- Sender (CR) -->
                            <div class="row mt-2">
                                <div class="col-md-3">
                                    <label for="credit_account_id" class="form-label text-end d-block">
                                        Sender (CR) <span class="text-danger">*</span>
                                    </label>
                                </div>
                                <div class="col-md-9">
                                    <select id="credit_account_id" class="form-control input-height"
                                        v-model="form.credit_account_id" required>
                                        <option value="" disabled>Select item</option>
                                        <option v-for="account in assetAccounts" :key="account.id" :value="account.id">
                                            {{ account.account_number }} - {{ account.name }}
                                        </option>
                                    </select>
                                    <div v-if="errors.credit_account_id" class="error-msg">{{ errors.credit_account_id }}</div>
                                </div>
                            </div>

                            <!-- Amount -->
                            <div class="row mt-2">
                                <div class="col-md-3">
                                    <label for="amount" class="form-label text-end d-block">
                                        Amount <span class="text-danger">*</span>
                                    </label>
                                </div>
                                <div class="col-md-9">
                                    <input type="number" id="amount" class="form-control input-height" step="0.01"
                                        min="0.01" placeholder="Transfer Amount..." v-model="form.amount" required />
                                    <div v-if="errors.amount" class="error-msg">{{ errors.amount }}</div>
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

                            <!-- VAT -->
                            <div class="row mt-2">
                                <div class="col-md-3">
                                    <label for="vat" class="form-label text-end d-block">
                                        VAT <span class="text-danger">*</span>
                                    </label>
                                </div>
                                <div class="col-md-9">
                                    <input type="number" id="vat" class="form-control input-height" step="0.01" min="0"
                                        placeholder="VAT..." v-model="form.vat" required />
                                    <div v-if="errors.vat" class="error-msg">{{ errors.vat }}</div>
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
                        </div>

                        <div class="col-md-6">
                            <!-- Receiver (DR) -->
                            <div class="row mt-2">
                                <div class="col-md-3">
                                    <label for="debit_account_id" class="form-label text-end d-block">
                                        Receiver (DR) <span class="text-danger">*</span>
                                    </label>
                                </div>
                                <div class="col-md-9">
                                    <select id="debit_account_id" class="form-control input-height"
                                        v-model="form.debit_account_id" required>
                                        <option value="" disabled>Select item</option>
                                        <option v-for="account in receiverAccounts" :key="account.id" :value="account.id">
                                            {{ account.account_number }} - {{ account.name }}
                                        </option>
                                    </select>
                                    <div v-if="errors.debit_account_id" class="error-msg">{{ errors.debit_account_id }}</div>
                                </div>
                            </div>

                            <!-- Receipt No -->
                            <div class="row mt-2">
                                <div class="col-md-3">
                                    <label for="receipt_no" class="form-label text-end d-block">Receipt No.</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" id="receipt_no" class="form-control input-height"
                                        placeholder="Receipt No..." v-model="form.receipt_no" />
                                    <div v-if="errors.receipt_no" class="error-msg">{{ errors.receipt_no }}</div>
                                </div>
                            </div>

                            <!-- Transfer Date -->
                            <div class="row mt-2">
                                <div class="col-md-3">
                                    <label for="transfer_date" class="form-label text-end d-block">
                                        Trans. Date <span class="text-danger">*</span>
                                    </label>
                                </div>
                                <div class="col-md-9">
                                    <input type="date" id="transfer_date" class="form-control input-height"
                                        v-model="form.transfer_date" :max="today" required />
                                    <div v-if="errors.transfer_date" class="error-msg">{{ errors.transfer_date }}</div>
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

                    <div class="d-flex justify-content-end" v-if="can(['fund-transfers.create'])">
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
    credit_account_id: '',
    amount: '',
    bank_charge: 0,
    vat: 0,
    debit_account_id: '',
    receipt_no: '',
    transfer_date: today,
    remarks: '',
    attachment: null,
})

const form = reactive(initialForm())
const errors = reactive({})
const isSubmitting = ref(false)
const attachmentInput = ref(null)

// Account type id of Asset, as seeded by the backend (account_types table).
const ASSET = 1

// Money can only move between open asset (cash/bank) transaction accounts.
const { items: assetAccounts, fetchData: loadAssetAccounts } = useFetch('/api/ledger-accounts', {
    per_page: 500,
    account_type_id: ASSET,
    is_transaction: 1,
    active_status: 1,
    is_closed: 0,
})

// The receiver cannot be the sender.
const receiverAccounts = computed(() => assetAccounts.value.filter((a) => a.id !== form.credit_account_id))

watch(() => form.credit_account_id, (senderId) => {
    if (form.debit_account_id === senderId) {
        form.debit_account_id = ''
    }
})

const total = computed(() => (parseFloat(form.amount) || 0) + (parseFloat(form.bank_charge) || 0) + (parseFloat(form.vat) || 0))

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
}

const handleSubmit = async () => {
    isSubmitting.value = true
    clearErrors()

    try {
        const { data } = await axios.post('/api/accounting/internal-fund-transfer-store', objectToFormData({ ...form }))

        // Like the payroll module, stay on the form and clear it for the next transfer.
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
    loadAssetAccounts()
})
</script>

<style scoped>
.input-height {
    height: 43px;
}
</style>
