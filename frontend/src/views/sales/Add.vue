<template lang="html">
    <Breadcrumb title="Add New Sale" buttonText="Back Sales" :buttonLink="{ name: 'admin_sales_list' }"
        buttonIcon="list" />

    <div class="main-content-wrapper mt-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-1"></div>
                <div class="col-md-10">
                    <v-card style="padding: 5px; margin: 15px 0px;">
                        <form @submit.prevent="handleSubmit">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="customer_id">{{ $t('Customer:') }}</label>
                                        <select id="customer_id" class="form-control" v-model="form.customer_id" required>
                                            <option value="" disabled>{{ $t('Select a Customer') }}</option>
                                            <option v-for="customer in customers" :key="customer.id" :value="customer.id">
                                                {{ customer.name }}
                                            </option>
                                        </select>
                                        <div v-if="errors.customer_id" class="error-msg">{{ errors.customer_id }}</div>
                                    </div>
                                </div>

                                <div class="col-md-8">
                                    <WorkOrderSelect v-model="form.work_order_id" :customer-id="form.customer_id"
                                        :error="errors.work_order_id" />
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="invoice_number">{{ $t('Invoice Number:') }}</label>
                                        <input type="text" id="invoice_number" class="form-control"
                                            v-model="form.invoice_number" required />
                                        <div v-if="errors.invoice_number" class="error-msg">{{ errors.invoice_number }}</div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="issue_date">{{ $t('Issue Date:') }}</label>
                                        <input type="date" id="issue_date" class="form-control"
                                            v-model="form.issue_date" required />
                                        <div v-if="errors.issue_date" class="error-msg">{{ errors.issue_date }}</div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="due_date">{{ $t('Due Date:') }}</label>
                                        <input type="date" id="due_date" class="form-control"
                                            v-model="form.due_date" />
                                        <div v-if="errors.due_date" class="error-msg">{{ errors.due_date }}</div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="description">{{ $t('Description:') }}</label>
                                        <input type="text" id="description" class="form-control"
                                            v-model="form.description" />
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <label for="notes">{{ $t('Notes:') }}</label>
                                        <textarea id="notes" class="form-control" rows="2" v-model="form.notes"></textarea>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <AttachmentInput v-model="form.attachment" :error="errors.attachment" />
                                </div>
                            </div>

                            <hr>

                            <InvoiceLineItems v-model:items="form.items" v-model:overall-discount="form.overall_discount"
                                v-model:overall-vat="form.overall_vat" :products="products" :errors="errors" />

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
import AttachmentInput from '@/components/common/AttachmentInput.vue';
import InvoiceLineItems, { emptyInvoiceItem } from '@/components/common/InvoiceLineItems.vue';
import WorkOrderSelect from '@/components/common/WorkOrderSelect.vue';
import { useFetch } from '@/composables/useFetch';
import { useStoreForm } from '@/composables/useStoreForm';
import { setToast } from "@/helpers/toast";
import { useRouter } from 'vue-router';

const router = useRouter();

const { form, errors, isSubmitting, submit } = useStoreForm({
    customer_id: '',
    work_order_id: '',
    invoice_number: '',
    issue_date: new Date().toISOString().slice(0, 10),
    due_date: '',
    description: '',
    notes: '',
    attachment: null,
    overall_discount: 0,
    overall_vat: 0,
    items: [emptyInvoiceItem()],
})

const { items: customers, fetchData: loadCustomers } = useFetch('/api/customers', { per_page: 100 })

// Only active products are offered; a line can still be a free-text custom item.
const { items: products, fetchData: loadProducts } = useFetch('/api/products', { per_page: 500, active_status: 1 })

const handleSubmit = async () => {
    if (!form.due_date) delete form.due_date

    const resp = await submit('/api/sales', 'post')

    if (resp && resp.success) {
        setToast('success', resp.message)
        router.push({ name: 'admin_sales_list' })
    }
}

onMounted(() => {
    loadCustomers()
    loadProducts()
})
</script>
