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
                                <label for="account_type_id">{{ $t('Account Type:') }} <span class="text-danger">*</span></label>
                                <select id="account_type_id" class="form-control" v-model="form.account_type_id" required>
                                    <option value="" disabled>{{ $t('Select item') }}</option>
                                    <option v-for="type in accountTypes" :key="type.id" :value="type.id">
                                        {{ $t(type.name) }}
                                    </option>
                                </select>
                                <div v-if="errors.account_type_id" class="error-msg">{{ errors.account_type_id }}</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="name">{{ $t('Account Name:') }} <span class="text-danger">*</span></label>
                                <input type="text" id="name" class="form-control" :placeholder="$t('Account Name...')"
                                    v-model="form.name" required />
                                <div v-if="errors.name" class="error-msg">{{ errors.name }}</div>
                            </div>
                        </div>

                        <div class="col-md-6" v-for="(level, index) in levels" :key="index">
                            <div class="form-group mb-3">
                                <label :for="`parent_level_${index}`">{{ $t('Sub Account Of:') }}</label>
                                <select :id="`parent_level_${index}`" class="form-control" v-model="level.selected"
                                    @change="onLevelChange(index)">
                                    <option value="">
                                        {{ index === 0 ? $t('None (top-level account)') : $t('None (stay at this level)') }}
                                    </option>
                                    <option v-for="account in level.options" :key="account.id" :value="account.id">
                                        {{ account.account_number }} {{ account.name }}
                                    </option>
                                </select>
                                <div v-if="index === 0 && errors.parent_id" class="error-msg">{{ errors.parent_id }}</div>
                                <small class="text-muted" v-if="index === 0">
                                    {{ $t(form.account_type_id ? 'Open accounts of the selected type.' : 'Select an account type first.') }}
                                </small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="account_number">{{ $t('Account Number:') }}</label>
                                <input type="text" id="account_number" class="form-control"
                                    :placeholder="$t('Account Number...')" v-model="form.account_number" />
                                <div v-if="errors.account_number" class="error-msg">{{ errors.account_number }}</div>
                                <small class="text-muted" v-if="form.parent_id">{{ $t('Suggested from the parent account.') }}</small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="opening_date">{{ $t('Opening Date:') }} <span class="text-danger">*</span></label>
                                <input type="date" id="opening_date" class="form-control" v-model="form.opening_date"
                                    required />
                                <div v-if="errors.opening_date" class="error-msg">{{ errors.opening_date }}</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="opening_balance">{{ $t('Opening Balance:') }} <span class="text-danger">*</span></label>
                                <input type="number" id="opening_balance" class="form-control" min="0" step="0.01"
                                    :placeholder="$t('Opening Balance...')" v-model="form.opening_balance" required />
                                <div v-if="errors.opening_balance" class="error-msg">{{ errors.opening_balance }}</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-check mb-2">
                                <input type="checkbox" class="form-check-input" id="is_transaction"
                                    v-model="form.is_transaction" />
                                <label class="form-check-label" for="is_transaction">
                                    {{ $t('Transaction account (entries can be posted to it)') }}
                                </label>
                                <div v-if="errors.is_transaction" class="error-msg">{{ errors.is_transaction }}</div>
                            </div>
                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" id="active_status"
                                    v-model="form.active_status" />
                                <label class="form-check-label" for="active_status">{{ $t('Active') }}</label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <v-btn type="submit" class="text-none text-white mr-2" color="success" rounded="0"
                                variant="flat" :disabled="isSubmitting" :loading="isSubmitting">
                                <i class="fa-solid fa-check me-2"></i> {{ $t('Save') }}
                            </v-btn>
                            <v-btn class="text-none text-white" color="error" rounded="0" variant="flat"
                                :to="{ name: 'admin_ledger_accounts_list' }">
                                <i class="fa-solid fa-xmark me-2"></i> {{ $t('Cancel') }}
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
import { t } from '@/i18n';

const router = useRouter();

const { form, errors, isSubmitting, submit } = useStoreForm({
    account_type_id: '',
    name: '',
    parent_id: '',
    account_number: '',
    opening_date: new Date().toISOString().slice(0, 10),
    opening_balance: 0,
    is_transaction: true,
    active_status: true,
})

const { items: accountTypes, fetchData: loadAccountTypes } = useFetch('/api/account-types')

// One select per level of the hierarchy, loaded lazily from the parent-child
// links: the type's top-level accounts first, then the children of each pick.
const levels = ref([])

// When a type has a single top-level account (e.g. "1000 Assets"), that root is
// the implicit parent and the picker starts from its children.
const rootAccountId = ref('')

const fetchChildren = async (params) => {
    try {
        const { data } = await axios.get('/api/ledger-accounts/children', { params })
        return data.data
    } catch (e) {
        toast.error(t('Failed to load accounts, try again.'))
        return []
    }
}

// The deepest chosen account is the new account's parent.
const syncParent = () => {
    const chosen = levels.value.map((level) => level.selected).filter(Boolean)
    form.parent_id = chosen.length ? chosen[chosen.length - 1] : rootAccountId.value
}

const onLevelChange = async (index) => {
    levels.value.splice(index + 1)
    syncParent()

    const selectedId = levels.value[index].selected
    if (!selectedId) {
        return
    }

    const options = await fetchChildren({ parent_id: selectedId })
    if (options.length && levels.value[index]?.selected === selectedId) {
        levels.value.push({ options, selected: '' })
    }
}

watch(() => form.account_type_id, async (typeId) => {
    levels.value = []
    rootAccountId.value = ''
    form.parent_id = ''

    if (!typeId) {
        return
    }

    const roots = await fetchChildren({ account_type_id: typeId })
    if (roots.length === 1) {
        const children = await fetchChildren({ parent_id: roots[0].id })
        if (children.length) {
            rootAccountId.value = roots[0].id
            levels.value = [{ options: children, selected: '' }]
            syncParent()
            return
        }
    }

    levels.value = [{ options: roots, selected: '' }]
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
        toast.error(t('Failed to fetch the next account number, try again.'))
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
})
</script>
