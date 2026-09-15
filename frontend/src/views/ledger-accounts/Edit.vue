<template lang="html">
    <Breadcrumb title="Edit Ledger Account" buttonText="Back Accounts" :buttonLink="{ name: 'admin_ledger_accounts_list' }"
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

                                <!-- Type is fixed at creation time so entries already posted against
                                     this account stay correctly classified. -->
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label>Type:</label>
                                        <input type="text" class="form-control text-capitalize" :value="type" disabled />
                                    </div>
                                </div>

                                <div class="col-md-6">
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

const type = ref('')

const { form, errors, isSubmitting, submit } = useStoreForm({
    name: '',
    active_status: true,
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
            form.name = data.data.name
            form.active_status = data.data.active_status
            type.value = data.data.type
        }
    } catch (e) {
        console.error(e)
    }
}

onMounted(() => {
    loadAccount()
})
</script>
