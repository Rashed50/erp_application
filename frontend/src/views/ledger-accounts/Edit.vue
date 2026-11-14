<template lang="html">
    <Breadcrumb title="Ledger Account Update" buttonText="Back Accounts"
        :buttonLink="{ name: 'admin_ledger_accounts_list' }" buttonIcon="list" />

    <div class="main-content-wrapper mt-4">
        <div class="container-fluid">
            <v-card style="padding: 5px; margin: 15px 0px;">
                <form @submit.prevent="handleSubmit">
                    <div v-if="isPredefined" class="alert alert-info py-2">
                        {{ $t('This is a predefined account. Only its name, number, opening date and status can be changed.') }}
                    </div>

                    <div class="row">
                        <!-- The type is fixed at creation time so entries already posted against
                             this account stay correctly classified. -->
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label>{{ $t('Account Type:') }}</label>
                                <input type="text" class="form-control" :value="accountType" disabled />
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

                        <div class="col-md-6" v-for="(level, index) in levels" :key="index" v-show="!isPredefined">
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
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="account_number">{{ $t('Account Number:') }}</label>
                                <input type="text" id="account_number" class="form-control"
                                    :placeholder="$t('Account Number...')" v-model="form.account_number" />
                                <div v-if="errors.account_number" class="error-msg">{{ errors.account_number }}</div>
                            </div>
                        </div>

                        <!-- The balance moves only through posted entries, so it is shown but not editable. -->
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label>{{ $t('Current Balance:') }}</label>
                                <input type="text" class="form-control" :value="Number(balance).toFixed(2)" disabled />
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
                            <div class="form-check mb-2">
                                <input type="checkbox" class="form-check-input" id="active_status"
                                    v-model="form.active_status" />
                                <label class="form-check-label" for="active_status">{{ $t('Active') }}</label>
                            </div>
                            <div class="form-check mb-2" v-if="!isPredefined">
                                <input type="checkbox" class="form-check-input" id="is_transaction"
                                    v-model="form.is_transaction" />
                                <label class="form-check-label" for="is_transaction">
                                    {{ $t('Transaction account (entries can be posted to it)') }}
                                </label>
                                <div v-if="errors.is_transaction" class="error-msg">{{ errors.is_transaction }}</div>
                            </div>
                            <div class="form-check mb-3" v-if="!isPredefined">
                                <input type="checkbox" class="form-check-input" id="is_closed"
                                    v-model="form.is_closed" />
                                <label class="form-check-label" for="is_closed">{{ $t('Closed') }}</label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <v-btn type="submit" class="text-none text-white mr-2" color="success" rounded="0"
                                variant="flat" :disabled="isSubmitting" :loading="isSubmitting">
                                <i class="fa-solid fa-check me-2"></i> {{ $t('Update') }}
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
import { useStoreForm } from '@/composables/useStoreForm';
import { setToast } from "@/helpers/toast";
import { useRouter, useRoute } from 'vue-router';
import { t } from '@/i18n';

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

// One select per level of the hierarchy, loaded lazily from the parent-child
// links. The account itself is left out so it cannot be moved under its own
// descendants.
const levels = ref([])

// When the type has a single top-level account (e.g. "1000 Assets"), that root
// is the implicit parent and the picker starts from its children.
const rootAccountId = ref('')

const fetchChildren = async (params) => {
    try {
        const { data } = await axios.get('/api/ledger-accounts/children', { params })
        return data.data.filter((account) => account.id !== Number(route.params.id))
    } catch (e) {
        toast.error(t('Failed to load accounts, try again.'))
        return []
    }
}

// The deepest chosen account is the account's parent.
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

// Parent ids from the top of the chart down to the account's own parent.
const loadAncestorIds = async (parentId) => {
    const ids = []
    const seen = new Set()

    while (parentId && !seen.has(parentId)) {
        seen.add(parentId)
        ids.unshift(parentId)
        const { data } = await axios.get(`/api/ledger-accounts/${parentId}`)
        parentId = data.data.parent_id
    }

    return ids
}

// Rebuild the selects so the account's current parent chain is preselected.
const buildLevels = async (typeId, parentId) => {
    const chain = await loadAncestorIds(parentId)
    const roots = await fetchChildren({ account_type_id: typeId })

    let parentOfLevel = null
    if (roots.length === 1 && chain[0] === roots[0].id) {
        rootAccountId.value = chain.shift()
        parentOfLevel = rootAccountId.value
    }

    const built = []
    for (let i = 0; i <= chain.length; i++) {
        const options = i === 0 && !parentOfLevel
            ? roots
            : await fetchChildren({ parent_id: parentOfLevel })

        if (options.length || i === 0) {
            built.push({ options, selected: chain[i] ?? '' })
        }
        parentOfLevel = chain[i]
    }

    levels.value = built
}

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
        toast.error(t('Failed to fetch the next account number, try again.'))
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

            if (!account.is_predefined) {
                await buildLevels(account.account_type_id, account.parent_id)
                syncParent()
            }

            await nextTick()
            isLoaded.value = true
        }
    } catch (e) {
        console.error(e)
    }
}

onMounted(() => {
    loadAccount()
})
</script>
