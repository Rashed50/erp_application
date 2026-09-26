<template lang="html">
    <Breadcrumb title="Add New Work Order" buttonText="Back Work Orders" :buttonLink="{ name: 'admin_work_orders_list' }"
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
                                        <label for="customer_id">Customer:</label>
                                        <select id="customer_id" class="form-control" v-model="form.customer_id" required>
                                            <option value="" disabled>Select a Customer</option>
                                            <option v-for="customer in customers" :key="customer.id" :value="customer.id">
                                                {{ customer.name }}
                                            </option>
                                        </select>
                                        <div v-if="errors.customer_id" class="error-msg">{{ errors.customer_id }}</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="work_order_no">Work Order No:</label>
                                        <input type="text" id="work_order_no" class="form-control"
                                            v-model="form.work_order_no" required />
                                        <div v-if="errors.work_order_no" class="error-msg">{{ errors.work_order_no }}</div>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <label for="work_title">Work Title:</label>
                                        <input type="text" id="work_title" class="form-control" v-model="form.work_title"
                                            required />
                                        <div v-if="errors.work_title" class="error-msg">{{ errors.work_title }}</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="issue_date">Issue Date:</label>
                                        <input type="date" id="issue_date" class="form-control" v-model="form.issue_date"
                                            required />
                                        <div v-if="errors.issue_date" class="error-msg">{{ errors.issue_date }}</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="deliver_date">Deliver Date:</label>
                                        <input type="date" id="deliver_date" class="form-control"
                                            v-model="form.deliver_date" :min="form.issue_date" />
                                        <div v-if="errors.deliver_date" class="error-msg">{{ errors.deliver_date }}</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="total_amount">Total Amount:</label>
                                        <input type="number" step="0.01" min="0" id="total_amount" class="form-control"
                                            v-model.number="form.total_amount" required />
                                        <div v-if="errors.total_amount" class="error-msg">{{ errors.total_amount }}</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="retention_percent">Retention (%):</label>
                                        <input type="number" step="0.01" min="0" max="100" id="retention_percent"
                                            class="form-control" v-model.number="form.retention_percent" />
                                        <div v-if="errors.retention_percent" class="error-msg">{{ errors.retention_percent }}</div>
                                        <small class="text-muted">Retention amount: {{ retentionAmount }}</small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="status">Status:</label>
                                        <select id="status" class="form-control" v-model="form.status" required>
                                            <option v-for="status in WORK_ORDER_STATUSES" :key="status" :value="status">
                                                {{ status }}
                                            </option>
                                        </select>
                                        <div v-if="errors.status" class="error-msg">{{ errors.status }}</div>
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
import { WORK_ORDER_STATUSES } from './statuses';

const router = useRouter();

const { form, errors, isSubmitting, submit } = useStoreForm({
    customer_id: '',
    work_title: '',
    work_order_no: '',
    issue_date: new Date().toISOString().slice(0, 10),
    total_amount: 0,
    retention_percent: 0,
    deliver_date: '',
    status: 'Pending',
})

const { items: customers, fetchData: loadCustomers } = useFetch('/api/customers', { per_page: 100 })

const retentionAmount = computed(() =>
    ((Number(form.total_amount) || 0) * (Number(form.retention_percent) || 0) / 100).toFixed(2)
)

const handleSubmit = async () => {
    const resp = await submit('/api/work-orders', 'post')

    if (resp && resp.success) {
        setToast('success', resp.message)
        router.push({ name: 'admin_work_orders_list' })
    }
}

onMounted(() => {
    loadCustomers()
})
</script>
