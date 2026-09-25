<template lang="html">
    <Breadcrumb title="Chart Of Account" buttonText="Back Accounts" :buttonLink="{ name: 'admin_ledger_accounts_list' }"
        buttonIcon="list" />

    <div class="main-content-wrapper mt-4">
        <div class="container-fluid">
            <v-card style="padding: 5px; margin: 15px 0px;">
                <form @submit.prevent="handleSubmit">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="account_type_id">Account Type: <span class="text-danger">*</span></label>
                                <select id="account_type_id" class="form-control" v-model="form.account_type_id" required>
                                    <option value="" disabled>Select item</option>
                                    <option v-for="type in accountTypes" :key="type.id" :value="type.id">
                                        {{ type.name }}
                                    </option>
                                </select>
                                <div v-if="errors.account_type_id" class="error-msg">{{ errors.account_type_id }}</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="name">Account Name: <span class="text-danger">*</span></label>
                                <input type="text" id="name" class="form-control" placeholder="Account Name..."
                                    v-model="form.name" required />
                                <div v-if="errors.name" class="error-msg">{{ errors.name }}</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="parent_id">Sub Account Of:</label>
                                <select id="parent_id" class="form-control" v-model="form.parent_id"
                                    :disabled="!form.account_type_id">
                                    <option value="">None (top-level account)</option>
                                    <option v-for="group in parentOptions" :key="group.id" :value="group.id">
                                        {{ group.account_number }} {{ group.name }}
                                    </option>
                                </select>
                                <div v-if="errors.parent_id" class="error-msg">{{ errors.parent_id }}</div>
                                <small class="text-muted">
                                    {{ form.account_type_id ? 'Open group accounts of the selected type.' : 'Select an account type first.' }}
                                </small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="account_number">Account Number:</label>
                                <input type="text" id="account_number" class="form-control"
                                    placeholder="Account Number..." v-model="form.account_number" />
                                <div v-if="errors.account_number" class="error-msg">{{ errors.account_number }}</div>
                                <small class="text-muted" v-if="form.parent_id">Suggested from the parent account.</small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="opening_date">Opening Date: <span class="text-danger">*</span></label>
                                <input type="date" id="opening_date" class="form-control" v-model="form.opening_date"
                                    required />
                                <div v-if="errors.opening_date" class="error-msg">{{ errors.opening_date }}</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="balance">Opening Balance: <span class="text-danger">*</span></label>
                                <input type="number" id="balance" class="form-control" min="0" step="0.01"
                                    placeholder="Opening Balance..." v-model="form.balance" required />
                                <div v-if="errors.balance" class="error-msg">{{ errors.balance }}</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-check mb-2">
                                <input type="checkbox" class="form-check-input" id="is_transaction"
                                    v-model="form.is_transaction" />
                                <label class="form-check-label" for="is_transaction">
                                    Transaction account (entries can be posted to it)
                                </label>
                                <div v-if="errors.is_transaction" class="error-msg">{{ errors.is_transaction }}</div>
                            </div>
                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" id="active_status"
                                    v-model="form.active_status" />
                                <label class="form-check-label" for="active_status">Active</label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <v-btn type="submit" class="text-none text-white mr-2" color="success" rounded="0"
                                variant="flat" :disabled="isSubmitting" :loading="isSubmitting">
                                <i class="fa-solid fa-check me-2"></i> Save
                            </v-btn>
                            <v-btn class="text-none text-white" color="error" rounded="0" variant="flat"
                                :to="{ name: 'admin_ledger_accounts_list' }">
                                <i class="fa-solid fa-xmark me-2"></i> Cancel
                            </v-btn>
                        </div>
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
import { useStoreForm } from '@/composables/useStoreForm';
import { setToast } from "@/helpers/toast";
import { useRouter } from 'vue-router';

const router = useRouter();

const { form, errors, isSubmitting, submit } = useStoreForm({
    account_type_id: '',
    name: '',
    parent_id: '',
    account_number: '',
    opening_date: new Date().toISOString().slice(0, 10),
    balance: 0,
    is_transaction: true,
    active_status: true,
})

const { items: accountTypes, fetchData: loadAccountTypes } = useFetch('/api/account-types')

// Only open, active group accounts can be a parent.
const { items: groupAccounts, fetchData: loadGroupAccounts } = useFetch('/api/ledger-accounts', {
    per_page: 500,
    is_transaction: 0,
    active_status: 1,
    is_closed: 0,
})

// A child must share its parent's type, so only offer parents of the selected type.
const parentOptions = computed(() => groupAccounts.value.filter((g) => g.account_type_id === form.account_type_id))

watch(() => form.account_type_id, () => {
    if (!parentOptions.value.some((g) => g.id === form.parent_id)) {
        form.parent_id = ''
    }
})

// Like the payroll module, picking a parent suggests the next child number, e.g. 1000.3.
watch(() => form.parent_id, async (parentId) => {
    if (!parentId) {
        form.account_number = ''
        return
    }

    try {
        const { data } = await axios.get(`/api/ledger-accounts/${parentId}/next-account-number`)
        form.account_number = data.data.account_number
    } catch (e) {
        toast.error('Failed to fetch the next account number, try again.')
    }
})

const handleSubmit = async () => {
    const resp = await submit('/api/ledger-accounts', 'post')

    if (resp && resp.success) {
        setToast('success', resp.message)
        router.push({ name: 'admin_ledger_accounts_list' })
    }
}

onMounted(() => {
    loadAccountTypes()
    loadGroupAccounts()
})
</script>
