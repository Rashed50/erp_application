<template lang="html">
    <Breadcrumb title="Ledger Account Update" buttonText="Back Accounts"
        :buttonLink="{ name: 'admin_ledger_accounts_list' }" buttonIcon="list" />

    <div class="main-content-wrapper mt-4">
        <div class="container-fluid">
            <v-card style="padding: 5px; margin: 15px 0px;">
                <form @submit.prevent="handleSubmit">
                    <div v-if="isPredefined" class="alert alert-info py-2">
                        This is a predefined account. Only its name, number, opening date and status can be changed.
                    </div>

                    <div class="row">
                        <!-- The type is fixed at creation time so entries already posted against
                             this account stay correctly classified. -->
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label>Account Type:</label>
                                <input type="text" class="form-control" :value="accountType" disabled />
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

                        <div class="col-md-6" v-if="!isPredefined">
                            <div class="form-group mb-3">
                                <label for="parent_id">Sub Account Of:</label>
                                <select id="parent_id" class="form-control" v-model="form.parent_id">
                                    <option value="">None (top-level account)</option>
                                    <option v-for="group in groupAccounts" :key="group.id" :value="group.id">
                                        {{ group.account_number }} {{ group.name }}
                                    </option>
                                </select>
                                <div v-if="errors.parent_id" class="error-msg">{{ errors.parent_id }}</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="account_number">Account Number:</label>
                                <input type="text" id="account_number" class="form-control"
                                    placeholder="Account Number..." v-model="form.account_number" />
                                <div v-if="errors.account_number" class="error-msg">{{ errors.account_number }}</div>
                            </div>
                        </div>

                        <!-- The balance moves only through posted entries, so it is shown but not editable. -->
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label>Current Balance:</label>
                                <input type="text" class="form-control" :value="Number(balance).toFixed(2)" disabled />
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
                            <div class="form-check mb-2">
                                <input type="checkbox" class="form-check-input" id="active_status"
                                    v-model="form.active_status" />
                                <label class="form-check-label" for="active_status">Active</label>
                            </div>
                            <div class="form-check mb-2" v-if="!isPredefined">
                                <input type="checkbox" class="form-check-input" id="is_transaction"
                                    v-model="form.is_transaction" />
                                <label class="form-check-label" for="is_transaction">
                                    Transaction account (entries can be posted to it)
                                </label>
                                <div v-if="errors.is_transaction" class="error-msg">{{ errors.is_transaction }}</div>
                            </div>
                            <div class="form-check mb-3" v-if="!isPredefined">
                                <input type="checkbox" class="form-check-input" id="is_closed"
                                    v-model="form.is_closed" />
                                <label class="form-check-label" for="is_closed">Closed</label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <v-btn type="submit" class="text-none text-white mr-2" color="success" rounded="0"
                                variant="flat" :disabled="isSubmitting" :loading="isSubmitting">
                                <i class="fa-solid fa-check me-2"></i> Update
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
import { useRouter, useRoute } from 'vue-router';

const router = useRouter();
const route = useRoute();

const accountType = ref('')
const accountTypeId = ref(null)
const balance = ref(0)
const isPredefined = ref(false)
const isLoaded = ref(false)

const { form, errors, isSubmitting, submit } = useStoreForm({
    name: '',
    account_number: '',
    parent_id: '',
    opening_date: '',
    is_transaction: false,
    is_closed: false,
    active_status: true,
})

// Candidate parents: open group accounts of the same type. The account itself
// and its own descendants are rejected by the API, so they are only filtered
// here for the account itself.
const { items: allGroups, fetchData: loadGroupAccounts } = useFetch('/api/ledger-accounts', {
    per_page: 500,
    is_transaction: 0,
    is_closed: 0,
})

const groupAccounts = computed(() => allGroups.value.filter((g) =>
    g.id !== Number(route.params.id)
    && g.account_type_id === accountTypeId.value
))

// Like the payroll module, moving the account under a new parent suggests
// the next child number there. The initial load must not overwrite the number.
watch(() => form.parent_id, async (parentId) => {
    if (!isLoaded.value || !parentId) {
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
    const resp = await submit(`/api/ledger-accounts/${route.params.id}`, 'put')

    if (resp && resp.success) {
        setToast('success', resp.message)
        router.push({ name: 'admin_ledger_accounts_list' })
    }
}

const loadAccount = async () => {
    try {
        const { data } = await axios.get(`/api/ledger-accounts/${route.params.id}`)
        if (data.success) {
            const account = data.data
            form.name = account.name
            form.account_number = account.account_number ?? ''
            form.parent_id = account.parent_id ?? ''
            form.opening_date = account.opening_date
            form.is_transaction = account.is_transaction
            form.is_closed = account.is_closed
            form.active_status = account.active_status
            accountType.value = account.account_type
            accountTypeId.value = account.account_type_id
            balance.value = account.balance
            isPredefined.value = account.is_predefined

            // The API rejects these keys outright for a predefined account, so
            // they must not be sent at all.
            if (account.is_predefined) {
                delete form.parent_id
                delete form.is_transaction
                delete form.is_closed
            }

            await nextTick()
            isLoaded.value = true
        }
    } catch (e) {
        console.error(e)
    }
}

onMounted(() => {
    loadGroupAccounts()
    loadAccount()
})
</script>
