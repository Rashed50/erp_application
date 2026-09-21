<template lang="html">
    <Breadcrumb title="Add Account" buttonText="Back Accounts" :buttonLink="{ name: 'admin_ledger_accounts_list' }"
        buttonIcon="list" />

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
                                        <label for="name">Name:</label>
                                        <input type="text" id="name" class="form-control" v-model="form.name"
                                            required />
                                        <div v-if="errors.name" class="error-msg">{{ errors.name }}</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="account_number">Account Number:</label>
                                        <input type="text" id="account_number" class="form-control"
                                            v-model="form.account_number" />
                                        <div v-if="errors.account_number" class="error-msg">{{ errors.account_number }}</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="parent_id">Parent Account:</label>
                                        <select id="parent_id" class="form-control" v-model="form.parent_id">
                                            <option value="">None (top-level account)</option>
                                            <option v-for="group in groupAccounts" :key="group.id" :value="group.id">
                                                {{ group.account_number }} {{ group.name }}
                                            </option>
                                        </select>
                                        <div v-if="errors.parent_id" class="error-msg">{{ errors.parent_id }}</div>
                                        <small class="text-muted">Only open group accounts can have children.</small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="account_type_id">Type:</label>
                                        <select id="account_type_id" class="form-control" v-model="form.account_type_id"
                                            :disabled="!!form.parent_id" required>
                                            <option value="" disabled>Select a Type</option>
                                            <option v-for="type in accountTypes" :key="type.id" :value="type.id">
                                                {{ type.name }}
                                            </option>
                                        </select>
                                        <div v-if="errors.account_type_id" class="error-msg">{{ errors.account_type_id }}</div>
                                        <small class="text-muted">
                                            {{ form.parent_id ? 'Inherited from the parent account.' : 'The type cannot be changed after creation.' }}
                                        </small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="opening_date">Opening Date:</label>
                                        <input type="date" id="opening_date" class="form-control"
                                            v-model="form.opening_date" />
                                        <div v-if="errors.opening_date" class="error-msg">{{ errors.opening_date }}</div>
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
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="active_status"
                                            v-model="form.active_status" />
                                        <label class="form-check-label" for="active_status">Active</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <v-btn type="submit" class="text-none text-white mr-2" color="blue-darken-4"
                                        rounded="0" variant="flat" :disabled="isSubmitting" :loading="isSubmitting">
                                        Submit
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
import Breadcrumb from '@/components/common/Breadcrumb.vue';
import { useFetch } from '@/composables/useFetch';
import { useStoreForm } from '@/composables/useStoreForm';
import { setToast } from "@/helpers/toast";
import { useRouter } from 'vue-router';

const router = useRouter();

const { form, errors, isSubmitting, submit } = useStoreForm({
    name: '',
    account_number: '',
    parent_id: '',
    account_type_id: '',
    opening_date: new Date().toISOString().slice(0, 10),
    is_transaction: false,
    active_status: true,
})

const { items: accountTypes, fetchData: loadAccountTypes } = useFetch('/api/account-types')

// Only open, active group accounts can be a parent.
const { items: groupAccounts, fetchData: loadGroupAccounts } = useFetch('/api/ledger-accounts', {
    per_page: 200,
    is_transaction: 0,
    active_status: 1,
})

// A child always takes its parent's type, so mirror it in the (disabled) select.
watch(() => form.parent_id, (parentId) => {
    const parent = groupAccounts.value.find((g) => g.id === parentId)
    if (parent) {
        form.account_type_id = parent.account_type_id
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
