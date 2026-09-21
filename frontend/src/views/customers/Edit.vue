<template lang="html">
    <Breadcrumb title="Edit Customer" buttonText="Back Customers" :buttonLink="{ name: 'admin_customers_list' }"
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
                                        <label for="name">Name:</label>
                                        <input type="text" id="name" class="form-control" v-model="form.name"
                                            required />
                                        <div v-if="errors.name" class="error-msg">{{ errors.name }}</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="email">Email:</label>
                                        <input type="email" id="email" class="form-control" v-model="form.email" />
                                        <div v-if="errors.email" class="error-msg">{{ errors.email }}</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="phone">Phone:</label>
                                        <input type="text" id="phone" class="form-control" v-model="form.phone" />
                                        <div v-if="errors.phone" class="error-msg">{{ errors.phone }}</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="vat_no">VAT No:</label>
                                        <input type="text" id="vat_no" class="form-control" v-model="form.vat_no" />
                                        <div v-if="errors.vat_no" class="error-msg">{{ errors.vat_no }}</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="contact_person">Contact Person:</label>
                                        <input type="text" id="contact_person" class="form-control"
                                            v-model="form.contact_person" />
                                        <div v-if="errors.contact_person" class="error-msg">{{ errors.contact_person }}</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="contact_person_phone">Contact Person Phone:</label>
                                        <input type="text" id="contact_person_phone" class="form-control"
                                            v-model="form.contact_person_phone" />
                                        <div v-if="errors.contact_person_phone" class="error-msg">{{ errors.contact_person_phone }}</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="contact_person_email">Contact Person Email:</label>
                                        <input type="email" id="contact_person_email" class="form-control"
                                            v-model="form.contact_person_email" />
                                        <div v-if="errors.contact_person_email" class="error-msg">{{ errors.contact_person_email }}</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="country">Country:</label>
                                        <input type="text" id="country" class="form-control" v-model="form.country" />
                                        <div v-if="errors.country" class="error-msg">{{ errors.country }}</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="payment_term">Payment Term (days):</label>
                                        <input type="number" min="0" id="payment_term" class="form-control"
                                            v-model.number="form.payment_term" />
                                        <div v-if="errors.payment_term" class="error-msg">{{ errors.payment_term }}</div>
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

                                <!-- Opening balance is fixed at creation time and only moves afterwards
                                     through ledger transactions, so it is shown here for reference only. -->
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label>Opening Balance:</label>
                                        <input type="text" class="form-control" :value="openingBalance" disabled />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label>Current Balance:</label>
                                        <input type="text" class="form-control" :value="currentBalance" disabled />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3 mt-4">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="active_status"
                                                v-model="form.active_status" />
                                            <label class="form-check-label" for="active_status">Active</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <label for="address">Address:</label>
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
import { useStoreForm } from '@/composables/useStoreForm';
import { setToast } from "@/helpers/toast";
import { useRouter, useRoute } from 'vue-router';

const router = useRouter();
const route = useRoute();

// opening_balance is intentionally not part of the submitted form — the
// backend does not accept it on update, only display it for reference.
const openingBalance = ref('0.00')
const currentBalance = ref('0.00')

const { form, errors, isSubmitting, submit } = useStoreForm({
    name: '',
    email: '',
    phone: '',
    address: '',
    vat_no: '',
    payment_term: 20,
    contact_person: '',
    contact_person_phone: '',
    contact_person_email: '',
    country: '',
    opening_date: '',
    active_status: true,
})

const handleSubmit = async () => {
    const resp = await submit(`/api/customers/${route.params.id}`, 'put')

    if (resp && resp.success) {
        setToast('success', resp.message)
        router.push({ name: 'admin_customers_list' })
    }
}

const loadCustomer = async () => {
    try {
        const { data } = await axios.get(`/api/customers/${route.params.id}`)
        if (data.success) {
            const customer = data.data
            form.name = customer.name
            form.email = customer.email
            form.phone = customer.phone
            form.address = customer.address
            form.vat_no = customer.vat_no
            form.payment_term = customer.payment_term
            form.contact_person = customer.contact_person
            form.contact_person_phone = customer.contact_person_phone
            form.contact_person_email = customer.contact_person_email
            form.country = customer.country
            form.opening_date = customer.opening_date
            form.active_status = customer.active_status
            openingBalance.value = Number(customer.opening_balance).toFixed(2)
            currentBalance.value = Number(customer.current_balance).toFixed(2)
        }
    } catch (e) {
        console.error(e)
    }
}

onMounted(() => {
    loadCustomer()
})
</script>
