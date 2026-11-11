<template lang="html">
    <Breadcrumb title="Add New Purchase" buttonText="Back Purchases" :buttonLink="{ name: 'admin_purchases_list' }"
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
                                        <label for="supplier_id">{{ $t('Supplier:') }}</label>
                                        <select id="supplier_id" class="form-control" v-model="form.supplier_id" required>
                                            <option value="" disabled>{{ $t('Select a Supplier') }}</option>
                                            <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">
                                                {{ supplier.name }}
                                            </option>
                                        </select>
                                        <div v-if="errors.supplier_id" class="error-msg">{{ errors.supplier_id }}</div>
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
import EmployeeSelect from '@/components/common/EmployeeSelect.vue';
import InvoiceLineItems, { emptyInvoiceItem } from '@/components/common/InvoiceLineItems.vue';
import WorkOrderOptionSelect from '@/components/common/WorkOrderOptionSelect.vue';
import { useFetch } from '@/composables/useFetch';
import { useStoreForm } from '@/composables/useStoreForm';
import { setToast } from "@/helpers/toast";
import { useRouter } from 'vue-router';

const router = useRouter();

const { form, errors, isSubmitting, submit } = useStoreForm({
    supplier_id: '',
    purchase_type: 'product',
    invoice_number: '',
    issue_date: new Date().toISOString().slice(0, 10),
    purchase_date: new Date().toISOString().slice(0, 10),
    work_order_id: '',
    description: '',
    notes: '',
    attachment: null,
    purchased_by_id: '',
    overall_discount: 0,
    overall_vat: 0,
    items: [emptyInvoiceItem()],
})

const { items: suppliers, fetchData: loadSuppliers } = useFetch('/api/suppliers', { per_page: 100 })

// Only active products are offered; a line can still be a free-text custom item.
const { items: products, fetchData: loadProducts } = useFetch('/api/products', { per_page: 500, active_status: 1 })

const handleSubmit = async () => {
    const resp = await submit('/api/purchases', 'post')

    if (resp && resp.success) {
        setToast('success', resp.message)
        router.push({ name: 'admin_purchases_list' })
    }
}

onMounted(() => {
    loadSuppliers()
    loadProducts()
})
</script>
