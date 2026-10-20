<template lang="html">
    <Breadcrumb title="Edit Purchase" buttonText="Back Purchases" :buttonLink="{ name: 'admin_purchases_list' }"
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
                                        <label>{{ $t('Supplier:') }}</label>
                                        <input type="text" class="form-control" :value="supplierName" disabled />
                                        <small class="text-muted">{{ $t('A purchase\'s supplier cannot be changed after creation.') }}</small>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="purchase_type">{{ $t('Purchase Type:') }}</label>
                                        <select id="purchase_type" class="form-control" v-model="form.purchase_type" required>
                                            <option value="product">{{ $t('Product') }}</option>
                                            <option value="service">{{ $t('Service') }}</option>
                                        </select>
                                        <div v-if="errors.purchase_type" class="error-msg">{{ errors.purchase_type }}</div>
                                    </div>
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
                                        <label for="purchase_date">{{ $t('Purchase Date:') }}</label>
                                        <input type="date" id="purchase_date" class="form-control"
                                            v-model="form.purchase_date" required />
                                        <div v-if="errors.purchase_date" class="error-msg">{{ errors.purchase_date }}</div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="description">{{ $t('Description:') }}</label>
                                        <input type="text" id="description" class="form-control"
                                            v-model="form.description" />
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <WorkOrderOptionSelect v-model="form.work_order_id" :error="errors.work_order_id" />
                                </div>

                                <div class="col-md-4">
                                    <EmployeeSelect v-model="form.purchased_by_id" :label="$t('Purchase By:')"
                                        :error="errors.purchased_by_id" />
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
                                :note="$t('Totals are recalculated by the server on save; the linked supplier ledger entry is kept in sync automatically. Reducing the total below the amount already paid is rejected.')">
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
import EmployeeSelect from '@/components/common/EmployeeSelect.vue';
import InvoiceLineItems, { emptyInvoiceItem, toInvoiceFormItem } from '@/components/common/InvoiceLineItems.vue';
import WorkOrderOptionSelect from '@/components/common/WorkOrderOptionSelect.vue';
import { useFetch } from '@/composables/useFetch';
import { useStoreForm } from '@/composables/useStoreForm';
import { setToast } from "@/helpers/toast";
import { useRouter, useRoute } from 'vue-router';

const router = useRouter();
const route = useRoute();

const supplierName = ref('')
const paidAmount = ref(0)
const dueAmount = ref(0)

// supplier_id is intentionally not part of the submitted form — the backend
// does not accept changing a purchase's supplier after creation.
const { form, errors, isSubmitting, submit } = useStoreForm({
    purchase_type: 'product',
    invoice_number: '',
    issue_date: '',
    purchase_date: '',
    work_order_id: '',
    description: '',
    notes: '',
    purchased_by_id: '',
    overall_discount: 0,
    overall_vat: 0,
    items: [emptyInvoiceItem()],
})

// Only active products are offered; a line can still be a free-text custom item.
const { items: products, fetchData: loadProducts } = useFetch('/api/products', { per_page: 500, active_status: 1 })

const handleSubmit = async () => {
    const resp = await submit(`/api/purchases/${route.params.id}`, 'put')

    if (resp && resp.success) {
        setToast('success', resp.message)
        router.push({ name: 'admin_purchases_list' })
    }
}

const loadPurchase = async () => {
    try {
        const { data } = await axios.get(`/api/purchases/${route.params.id}`)
        if (data.success) {
            const purchase = data.data
            supplierName.value = purchase.supplier_name
            paidAmount.value = Number(purchase.paid_amount)
            dueAmount.value = Number(purchase.due_amount)
            form.purchase_type = purchase.purchase_type
            form.invoice_number = purchase.invoice_number
            form.issue_date = purchase.issue_date
            form.purchase_date = purchase.purchase_date
            form.description = purchase.description
            form.work_order_id = purchase.work_order_id ?? ''
            form.notes = purchase.notes
            form.items = (purchase.items ?? []).map(toInvoiceFormItem)
            if (!form.items.length) form.items = [emptyInvoiceItem()]
            form.overall_discount = purchase.overall_discount ?? 0
            form.overall_vat = purchase.overall_vat ?? 0
            form.purchased_by_id = purchase.purchased_by_id ?? ''
        }
    } catch (e) {
        console.error(e)
    }
}

onMounted(() => {
    loadPurchase()
    loadProducts()
})
</script>
