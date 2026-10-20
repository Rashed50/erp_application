<template lang="html">
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
                                <input type="date" id="issue_date" class="form-control" v-model="form.issue_date"
                                    required />
                                <div v-if="errors.issue_date" class="error-msg">{{ errors.issue_date }}</div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="discount_amount">{{ $t('Discount:') }}</label>
                                <input type="number" min="0" step="0.01" id="discount_amount" class="form-control"
                                    v-model.number="form.discount_amount" />
                                <div v-if="errors.discount_amount" class="error-msg">{{ errors.discount_amount }}</div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="vat_amount">{{ $t('VAT:') }}</label>
                                <input type="number" min="0" step="0.01" id="vat_amount" class="form-control"
                                    v-model.number="form.vat_amount" />
                                <div v-if="errors.vat_amount" class="error-msg">{{ errors.vat_amount }}</div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="file">{{ $t('Attachment:') }}</label>
                                <input type="file" id="file" class="form-control"
                                    accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx" @change="onFileChange" />
                                <div v-if="errors.file" class="error-msg">{{ errors.file }}</div>
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

                    <h5 class="mb-3">{{ $t('Items') }}</h5>
                    <div v-if="errors.items" class="error-msg mb-2">{{ errors.items }}</div>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead>
                                <tr>
                                    <th style="width: 200px;">{{ $t('Product') }}</th>
                                    <th>{{ $t('Item Name') }}</th>
                                    <th>{{ $t('Description') }}</th>
                                    <th style="width: 90px;">{{ $t('Qty') }}</th>
                                    <th style="width: 130px;">{{ $t('Unit') }}</th>
                                    <th style="width: 120px;">{{ $t('Unit Price') }}</th>
                                    <th style="width: 50px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, index) in form.items" :key="index">
                                    <td>
                                        <select class="form-control" v-model="item.product_id"
                                            @change="onProductChange(item)">
                                            <option value="">{{ $t('Custom item') }}</option>
                                            <option v-for="product in products" :key="product.id" :value="product.id">
                                                {{ product.code }} - {{ product.name }}
                                            </option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" v-model="item.item_name" required />
                                        <div v-if="errors[`items.${index}.item_name`]" class="error-msg">
                                            {{ errors[`items.${index}.item_name`] }}
                                        </div>
                                    </td>
                                    <td><input type="text" class="form-control" v-model="item.description" /></td>
                                    <td>
                                        <input type="number" min="0.01" step="0.01" class="form-control"
                                            v-model.number="item.qty" required />
                                        <div v-if="errors[`items.${index}.qty`]" class="error-msg">
                                            {{ errors[`items.${index}.qty`] }}
                                        </div>
                                    </td>
                                    <td>
                                        <select class="form-control" v-model="item.unit_id">
                                            <option value="">{{ $t('Select') }}</option>
                                            <option v-for="unit in units" :key="unit.id" :value="unit.id">
                                                {{ unit.name }}
                                            </option>
                                        </select>
                                        <div v-if="errors[`items.${index}.unit_id`]" class="error-msg">
                                            {{ errors[`items.${index}.unit_id`] }}
                                        </div>
                                    </td>
                                    <td>
                                        <input type="number" min="0" step="0.01" class="form-control"
                                            v-model.number="item.unit_price" required />
                                        <div v-if="errors[`items.${index}.unit_price`]" class="error-msg">
                                            {{ errors[`items.${index}.unit_price`] }}
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                            :disabled="form.items.length === 1" @click="removeItem(index)">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <v-btn type="button" class="text-none mb-3" color="grey-lighten-3" rounded="0" variant="flat"
                        @click="addItem">
                        {{ $t('+ Add Item') }}
                    </v-btn>

                    <div class="row justify-content-end">
                        <div class="col-md-4">
                            <table class="table table-sm">
                                <tbody>
                                    <tr>
                                        <td>{{ $t('Total Amount') }}</td>
                                        <td class="text-end">{{ totals.totalAmount.toFixed(2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ $t('Discount') }}</td>
                                        <td class="text-end">{{ totals.discountAmount.toFixed(2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ $t('VAT') }}</td>
                                        <td class="text-end">{{ totals.vatAmount.toFixed(2) }}</td>
                                    </tr>
                                    <tr class="fw-bold">
                                        <td>{{ $t('Net Total') }}</td>
                                        <td class="text-end">{{ totals.netTotal.toFixed(2) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                            <small class="text-muted">{{ $t('Totals are recalculated by the server on save.') }}</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 d-flex gap-2">
                            <v-btn type="submit" class="text-none text-white" color="blue-darken-4" rounded="0"
                                variant="flat" :disabled="isSubmitting" :loading="isSubmitting">
                                {{ $t('Submit') }}
                            </v-btn>
                            <v-btn type="button" class="text-none" color="grey-lighten-2" rounded="0" variant="flat"
                                @click="$emit('cancel')">
                                {{ $t('Cancel') }}
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
import { computed, onMounted, reactive, ref } from 'vue';
import { useFetch } from '@/composables/useFetch';
import { setToast } from '@/helpers/toast';
import { t } from '@/i18n';

const emit = defineEmits(['saved', 'cancel']);

const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5 MB, same as the server rule

// Today in local time (toISOString() alone would give the UTC date).
const today = () => {
    const now = new Date();
    return new Date(now.getTime() - now.getTimezoneOffset() * 60000).toISOString().slice(0, 10);
};

// discount and vat are not shown per line (they are set once for the whole quotation),
// but they stay in the payload as 0 because the server's line calculator expects them.
const emptyItem = () => ({
    product_id: '',
    item_name: '',
    description: '',
    qty: 1,
    unit_id: '',
    unit_price: 0,
    discount: 0,
    vat: 0,
});

const form = reactive({
    customer_id: '',
    invoice_number: '',
    issue_date: today(),
    discount_amount: 0,
    vat_amount: 0,
    notes: '',
    items: [emptyItem()],
});

const file = ref(null);
const errors = ref({});
const isSubmitting = ref(false);

const { items: customers, fetchData: loadCustomers } = useFetch('/api/customers', { per_page: 100 });
const { items: products, fetchData: loadProducts } = useFetch('/api/products', { per_page: 500, active_status: 1 });

// Units come from the inventory units table through a small lookup endpoint.
const units = ref([]);

const loadUnits = async () => {
    try {
        const { data } = await axios.get('/api/asset/lookups/inventory-units');
        units.value = data.data?.units ?? [];
    } catch (error) {
        console.error('Unit load error:', error);
        setToast('error', t('Failed to load units.'));
    }
};

// The product list only helps to fill the item name (quotation lines do not store a product).
const onProductChange = (item) => {
    const product = products.value.find((p) => p.id === item.product_id);
    if (product) item.item_name = product.name;
};

const onFileChange = (event) => {
    file.value = event.target.files[0] || null;
};

const addItem = () => form.items.push(emptyItem());
const removeItem = (index) => {
    if (form.items.length > 1) form.items.splice(index, 1);
};

const lineTotal = (item) => (Number(item.qty) || 0) * (Number(item.unit_price) || 0);

// Discount and VAT are quotation-level amounts typed above the items,
// so they flow straight into the summary below.
const totals = computed(() => {
    const totalAmount = form.items.reduce((sum, item) => sum + lineTotal(item), 0);
    const discountAmount = Number(form.discount_amount) || 0;
    const vatAmount = Number(form.vat_amount) || 0;

    return {
        totalAmount,
        discountAmount,
        vatAmount,
        netTotal: totalAmount - discountAmount + vatAmount,
    };
});

// A file cannot travel inside JSON, so the whole form is sent as multipart FormData.
// Laravel reads items[0][item_name] style keys back into the items array.
const buildFormData = () => {
    const fd = new FormData();

    fd.append('customer_id', form.customer_id);
    fd.append('invoice_number', form.invoice_number);
    fd.append('issue_date', form.issue_date);
    fd.append('discount_amount', form.discount_amount ?? 0);
    fd.append('vat_amount', form.vat_amount ?? 0);
    fd.append('notes', form.notes ?? '');

    form.items.forEach((item, index) => {
        ['item_name', 'description', 'unit_id', 'qty', 'unit_price', 'discount', 'vat'].forEach((key) => {
            fd.append(`items[${index}][${key}]`, item[key] ?? '');
        });
    });

    if (file.value) fd.append('file', file.value);

    return fd;
};

const handleSubmit = async () => {
    if (isSubmitting.value) return;

    errors.value = {};

    // These checks stop the request before it is sent, so each one shows its message
    // right under the field (a silent return here makes Submit look dead).
    if (totals.value.discountAmount > totals.value.totalAmount) {
        errors.value = { discount_amount: t('The discount cannot be greater than the total amount.') };
        return;
    }

    if (file.value && file.value.size > MAX_FILE_SIZE) {
        errors.value = { file: t('The attachment must not be larger than 5 MB.') };
        return;
    }

    isSubmitting.value = true;

    try {
        const { data } = await axios.post('/api/quotations', buildFormData());

        setToast('success', data.message);
        emit('saved');
    } catch (error) {
        // Validation errors may arrive as { errors: {...} } or inside { data: {...} }
        const raw = error.response?.data?.errors ?? error.response?.data?.data;

        if (raw && typeof raw === 'object' && !Array.isArray(raw)) {
            errors.value = Object.fromEntries(
                Object.entries(raw).map(([key, value]) => [key, Array.isArray(value) ? value[0] : value])
            );

            // Also as a toast, so an error on a field that has no inline slot is never missed
            setToast('error', Object.values(errors.value).join(' '));
        } else {
            setToast('error', error.response?.data?.message || t('Failed to save the quotation.'));
        }
    } finally {
        isSubmitting.value = false;
    }
};

onMounted(() => {
    loadCustomers();
    loadProducts();
    loadUnits();
});
</script>