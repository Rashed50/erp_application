<template lang="html">
    <Breadcrumb title="Edit Income / Expense Entry" buttonText="Back Entries"
        :buttonLink="{ name: 'admin_income_expenses_list' }" buttonIcon="list" />

    <div class="main-content-wrapper mt-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-2"></div>
                <div class="col-md-8">
                    <v-card style="padding: 5px; margin: 15px 0px;">
                        <form @submit.prevent="handleSubmit">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="type">Type:</label>
                                        <select id="type" class="form-control" v-model="form.type" required>
                                            <option value="expense">Expense</option>
                                            <option value="income">Income</option>
                                        </select>
                                        <div v-if="errors.type" class="error-msg">{{ errors.type }}</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="transaction_date">Date:</label>
                                        <input type="date" id="transaction_date" class="form-control"
                                            v-model="form.transaction_date" required />
                                        <div v-if="errors.transaction_date" class="error-msg">{{ errors.transaction_date }}</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="income_expense_account_id">
                                            {{ form.type === 'income' ? 'Income Category' : 'Expense Category' }}:
                                        </label>
                                        <select id="income_expense_account_id" class="form-control"
                                            v-model="form.income_expense_account_id" required>
                                            <option value="" disabled>Select a Category</option>
                                            <option v-for="account in categoryAccounts" :key="account.id" :value="account.id">
                                                {{ account.name }}
                                            </option>
                                        </select>
                                        <div v-if="errors.income_expense_account_id" class="error-msg">{{ errors.income_expense_account_id }}</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="payment_account_id">Payment Account (Cash/Bank):</label>
                                        <select id="payment_account_id" class="form-control"
                                            v-model="form.payment_account_id" required>
                                            <option value="" disabled>Select a Payment Account</option>
                                            <option v-for="account in assetAccounts" :key="account.id" :value="account.id">
                                                {{ account.name }}
                                            </option>
                                        </select>
                                        <div v-if="errors.payment_account_id" class="error-msg">{{ errors.payment_account_id }}</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="amount">Amount:</label>
                                        <input type="number" step="0.01" min="0.01" id="amount" class="form-control"
                                            v-model.number="form.amount" required />
                                        <div v-if="errors.amount" class="error-msg">{{ errors.amount }}</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="reference_no">Reference No:</label>
                                        <input type="text" id="reference_no" class="form-control"
                                            v-model="form.reference_no" />
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <label for="description">Description:</label>
                                        <textarea id="description" class="form-control" rows="2"
                                            v-model="form.description"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <v-btn type="submit" class="text-none text-white mr-2" color="blue-darken-4"
                                        rounded="0" variant="flat" :disabled="isSubmitting" :loading="isSubmitting">
                                        Update
                                    </v-btn>
                                </div>
                            </div>
                        </form>
                    </v-card>
                </div>
            </div>
        </div>
    </div>

</template>
<script setup>
import axios from 'axios';
import Breadcrumb from '@/components/common/Breadcrumb.vue';
import { useFetch } from '@/composables/useFetch';
import { useStoreForm } from '@/composables/useStoreForm';
import { setToast } from "@/helpers/toast";
import { useRouter, useRoute } from 'vue-router';

const router = useRouter();
const route = useRoute();

const { form, errors, isSubmitting, submit } = useStoreForm({
    type: 'expense',
    income_expense_account_id: '',
    payment_account_id: '',
    amount: null,
    transaction_date: '',
    reference_no: '',
    description: '',
})

const { items: accounts, fetchData: loadAccounts } = useFetch('/api/ledger-accounts', {
    per_page: 100,
    active_status: 1,
})

const categoryAccounts = computed(() => accounts.value.filter((a) => a.type === form.type))
const assetAccounts = computed(() => accounts.value.filter((a) => a.type === 'asset'))

// Only reset the category selection on a user-driven type change — not while
// the existing entry is still being loaded into the form.
const isLoading = ref(true)
watch(() => form.type, () => {
    if (isLoading.value) return
    form.income_expense_account_id = ''
})

const handleSubmit = async () => {
    const resp = await submit(`/api/income-expenses/${route.params.id}`, 'put')

    if (resp && resp.success) {
        setToast('success', resp.message)
        router.push({ name: 'admin_income_expenses_list' })
    }
}

const loadTransaction = async () => {
    try {
        const { data } = await axios.get(`/api/income-expenses/${route.params.id}`)
        if (data.success) {
            const transaction = data.data
            form.type = transaction.type
            form.income_expense_account_id = transaction.income_expense_account_id
            form.payment_account_id = transaction.payment_account_id
            form.amount = transaction.amount
            form.transaction_date = transaction.transaction_date
            form.reference_no = transaction.reference_no
            form.description = transaction.description
        }
    } catch (e) {
        console.error(e)
    } finally {
        isLoading.value = false
    }
}

onMounted(async () => {
    await loadAccounts()
    await loadTransaction()
})
</script>
