<template lang="html">
    <Breadcrumb title="Add New Supplier" buttonText="Back Suppliers" :buttonLink="{ name: 'admin_suppliers_list' }"
        buttonIcon="list" />

    <div class="main-content-wrapper mt-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-2"></div>
                <div class="col-md-8">
                    <!-- Table Card -->
                    <v-card style="padding: 5px; margin: 15px 0px;">
                        <form @submit.prevent="handleSubmit">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="name">{{ $t('Name:') }}</label>
                                        <input type="text" id="name" class="form-control" v-model="form.name"
                                            required />
                                        <div v-if="errors.name" class="error-msg">{{ errors.name }}</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="supplier_type">{{ $t('Supplier Type:') }}</label>
                                        <select id="supplier_type" class="form-select" v-model="form.supplier_type">
                                            <option value="goods">{{ $t('Goods') }}</option>
                                            <option value="service">{{ $t('Service') }}</option>
                                        </select>
                                        <div v-if="errors.supplier_type" class="error-msg">{{ errors.supplier_type }}</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="email">{{ $t('Email:') }}</label>
                                        <input type="email" id="email" class="form-control" v-model="form.email" />
                                        <div v-if="errors.email" class="error-msg">{{ errors.email }}</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="phone">{{ $t('Phone:') }}</label>
                                        <input type="text" id="phone" class="form-control" v-model="form.phone" />
                                        <div v-if="errors.phone" class="error-msg">{{ errors.phone }}</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="vat_no">{{ $t('VAT No:') }}</label>
                                        <input type="text" id="vat_no" class="form-control" v-model="form.vat_no" />
                                        <div v-if="errors.vat_no" class="error-msg">{{ errors.vat_no }}</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="contact_person">{{ $t('Contact Person:') }}</label>
                                        <input type="text" id="contact_person" class="form-control"
                                            v-model="form.contact_person" />
                                        <div v-if="errors.contact_person" class="error-msg">{{ errors.contact_person }}</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="contact_person_phone">{{ $t('Contact Person Phone:') }}</label>
                                        <input type="text" id="contact_person_phone" class="form-control"
                                            v-model="form.contact_person_phone" />
                                        <div v-if="errors.contact_person_phone" class="error-msg">{{ errors.contact_person_phone }}</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="contact_person_email">{{ $t('Contact Person Email:') }}</label>
                                        <input type="email" id="contact_person_email" class="form-control"
                                            v-model="form.contact_person_email" />
                                        <div v-if="errors.contact_person_email" class="error-msg">{{ errors.contact_person_email }}</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="payment_term">{{ $t('Payment Term (days):') }}</label>
                                        <input type="number" min="0" id="payment_term" class="form-control"
                                            v-model.number="form.payment_term" />
                                        <div v-if="errors.payment_term" class="error-msg">{{ errors.payment_term }}</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="opening_balance">{{ $t('Opening Balance:') }}</label>
                                        <input type="number" step="0.01" min="0" id="opening_balance"
                                            class="form-control" v-model.number="form.opening_balance" />
                                        <div v-if="errors.opening_balance" class="error-msg">{{ errors.opening_balance }}</div>
                                        <small class="text-muted">{{ $t('This becomes the supplier\'s starting balance and cannot be changed later.') }}</small>
                                    </div>
                                </div>

                                <div class="col-md-6" v-if="form.opening_balance > 0">
                                    <div class="form-group mb-3">
                                        <label for="opening_balance_account_id">
                                            {{ $t('Opening Balance Debit Account:') }}
                                            <span v-if="form.supplier_type === 'service'" class="text-danger">*</span>
                                        </label>
                                        <select id="opening_balance_account_id" class="form-select"
                                            v-model="form.opening_balance_account_id"
                                            :required="form.supplier_type === 'service'">
                                            <option value="">{{ $t('Assets (default)') }}</option>
                                            <optgroup v-for="group in accountGroups" :key="group.type" :label="$t(group.type)">
                                                <option v-for="account in group.accounts" :key="account.id" :value="account.id">
                                                    {{ account.account_number }} - {{ account.name }}
                                                </option>
                                            </optgroup>
                                        </select>
                                        <div v-if="errors.opening_balance_account_id" class="error-msg">{{ errors.opening_balance_account_id }}</div>
                                        <small class="text-muted">{{ $t('Credited to Accounts Payable; for a service supplier choose the expense account.') }}</small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3 mt-4">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="active_status"
                                                v-model="form.active_status" />
                                            <label class="form-check-label" for="active_status">{{ $t('Active') }}</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <label for="address">{{ $t('Address:') }}</label>
                                        <textarea id="address" class="form-control" rows="2"
                                            v-model="form.address"></textarea>
                                        <div v-if="errors.address" class="error-msg">{{ errors.address }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <v-btn type="submit" class="text-none text-white mr-2" color="blue-darken-4"
                                        rounded="0" variant="flat" :disabled="isSubmitting" :loading="isSubmitting">
                                        {{ $t('Submit') }}
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
import { groupAccountsByType } from '@/helpers/groupAccountsByType';
import { setToast } from "@/helpers/toast";
import { useRouter } from 'vue-router';

const router = useRouter();

const { form, errors, isSubmitting, submit } = useStoreForm({
    name: '',
    supplier_type: 'goods',
    email: '',
    phone: '',
    address: '',
    vat_no: '',
    payment_term: 20,
    contact_person: '',
    contact_person_phone: '',
    contact_person_email: '',
    opening_balance: 0,
    opening_balance_account_id: '',
    active_status: true,
})

// Accounts Payable is always the credit side, so it is not offered as the debit side.
const ACCOUNTS_PAYABLE_NUMBER = '2010'

const { items: accounts, fetchData: loadAccounts } = useFetch('/api/ledger-accounts', {
    per_page: 500,
    is_transaction: 1,
    active_status: 1,
    is_closed: 0,
})

const accountGroups = computed(() => groupAccountsByType(accounts.value.filter((a) => a.account_number !== ACCOUNTS_PAYABLE_NUMBER)))

onMounted(loadAccounts)

const handleSubmit = async () => {
    const resp = await submit('/api/suppliers', 'post')

    if (resp && resp.success) {
        setToast('success', resp.message)
        router.push({ name: 'admin_suppliers_list' })
    }
}
</script>
