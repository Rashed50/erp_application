<template lang="html">
    <Breadcrumb title="Edit Sale" buttonText="Back Sales" :buttonLink="{ name: 'admin_sales_list' }"
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
                                        <label>{{ $t('Customer:') }}</label>
                                        <input type="text" class="form-control" :value="customerName" disabled />
                                        <small class="text-muted">{{ $t('A sale\'s customer cannot be changed after creation.') }}</small>
                                    </div>
                                </div>

                                <div class="col-md-8">
                                    <WorkOrderSelect v-model="form.work_order_id" :customer-id="customerId"
                                        :error="errors.work_order_id" :disabled="paidAmount > 0"
                                        :hint="paidAmount > 0 ? 'The work order cannot be changed after a payment has been received.' : ''" />
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
                                        <input type="date" id="due_date" class="form-control" v-model="form.due_date" />
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
                            </div>

                            <hr>

                            <InvoiceLineItems v-model:items="form.items" v-model:overall-discount="form.overall_discount"
                                v-model:overall-vat="form.overall_vat" :products="products" :errors="errors"
                                :note="$t('Totals are recalculated by the server on save; the linked customer ledger entry is kept in sync automatically. Reducing the total below the amount already paid is rejected.')">
                                <template #totals>
                                    <tr>
                                        <td>{{ $t('Paid Amount') }}</td>
                                        <td class="text-end">{{ paidAmount.toFixed(2) }}</td>
                                    </tr>
                                    <tr class="fw-bold">
                                        <td>{{ $t('Due Amount') }}</td>
                                        <td class="text-end">{{ dueAmount.toFixed(2) }}</td>
                                    </tr>
                                </template>
                            </InvoiceLineItems>

                            <div class="row">
                                <div class="col-md-12">
                                    <v-btn type="submit" class="text-none text-white mr-2" color="blue-darken-4"
                                        rounded="0" variant="flat" :disabled="isSubmitting" :loading="isSubmitting">
                                        {{ $t('Update') }}
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
import InvoiceLineItems, { emptyInvoiceItem, toInvoiceFormItem } from '@/components/common/InvoiceLineItems.vue';
import WorkOrderSelect from '@/components/common/WorkOrderSelect.vue';
import { useFetch } from '@/composables/useFetch';
import { useStoreForm } from '@/composables/useStoreForm';
import { setToast } from "@/helpers/toast";
import { useRouter, useRoute } from 'vue-router';

const router = useRouter();
const route = useRoute();

const customerName = ref('')
const customerId = ref('')
const paidAmount = ref(0)
const dueAmount = ref(0)

// customer_id is intentionally not part of the submitted form — the backend
// does not accept changing a sale's customer after creation.
const { form, errors, isSubmitting, submit } = useStoreForm({
    work_order_id: '',
    invoice_number: '',
    issue_date: '',
    due_date: '',
    description: '',
    notes: '',
    overall_discount: 0,
    overall_vat: 0,
    items: [emptyInvoiceItem()],
})

// Only active products are offered; a line can still be a free-text custom item.
const { items: products, fetchData: loadProducts } = useFetch('/api/products', { per_page: 500, active_status: 1 })

const handleSubmit = async () => {
    const resp = await submit(`/api/sales/${route.params.id}`, 'put')

    if (resp && resp.success) {
        setToast('success', resp.message)
        router.push({ name: 'admin_sales_list' })
    }
}

const loadSale = async () => {
    try {
        const { data } = await axios.get(`/api/sales/${route.params.id}`)
        if (data.success) {
            const sale = data.data
            customerName.value = sale.customer_name
            customerId.value = sale.customer_id
            form.work_order_id = sale.work_order_id ?? ''
            paidAmount.value = Number(sale.paid_amount)
            dueAmount.value = Number(sale.due_amount)
            form.invoice_number = sale.invoice_number
            form.issue_date = sale.issue_date
            form.due_date = sale.due_date
            form.description = sale.description
            form.notes = sale.notes
            form.items = (sale.items ?? []).map(toInvoiceFormItem)
            if (!form.items.length) form.items = [emptyInvoiceItem()]
            form.overall_discount = sale.overall_discount ?? 0
            form.overall_vat = sale.overall_vat ?? 0
        }
    } catch (e) {
        console.error(e)
    }
}

onMounted(() => {
    loadSale()
    loadProducts()
})
</script>
