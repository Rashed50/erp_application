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
                                        <label>Customer:</label>
                                        <input type="text" class="form-control" :value="customerName" disabled />
                                        <small class="text-muted">A sale's customer cannot be changed after creation.</small>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="invoice_number">Invoice Number:</label>
                                        <input type="text" id="invoice_number" class="form-control"
                                            v-model="form.invoice_number" required />
                                        <div v-if="errors.invoice_number" class="error-msg">{{ errors.invoice_number }}</div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="issue_date">Issue Date:</label>
                                        <input type="date" id="issue_date" class="form-control"
                                            v-model="form.issue_date" required />
                                        <div v-if="errors.issue_date" class="error-msg">{{ errors.issue_date }}</div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="due_date">Due Date:</label>
                                        <input type="date" id="due_date" class="form-control" v-model="form.due_date" />
                                        <div v-if="errors.due_date" class="error-msg">{{ errors.due_date }}</div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="description">Description:</label>
                                        <input type="text" id="description" class="form-control"
                                            v-model="form.description" />
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <label for="notes">Notes:</label>
                                        <textarea id="notes" class="form-control" rows="2" v-model="form.notes"></textarea>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <h5 class="mb-3">Items</h5>
                            <div v-if="errors.items" class="error-msg mb-2">{{ errors.items }}</div>

                            <div class="table-responsive">
                                <table class="table table-bordered align-middle">
                                    <thead>
                                        <tr>
                                            <th>Item Name</th>
                                            <th>Description</th>
                                            <th style="width: 90px;">Qty</th>
                                            <th style="width: 120px;">Unit Price</th>
                                            <th style="width: 110px;">Discount</th>
                                            <th style="width: 110px;">VAT</th>
                                            <th style="width: 120px;">Line Total</th>
                                            <th style="width: 50px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(item, index) in form.items" :key="index">
                                            <td><input type="text" class="form-control" v-model="item.item_name" required /></td>
                                            <td><input type="text" class="form-control" v-model="item.description" /></td>
                                            <td><input type="number" min="0.01" step="0.01" class="form-control" v-model.number="item.qty" required /></td>
                                            <td><input type="number" min="0" step="0.01" class="form-control" v-model.number="item.unit_price" required /></td>
                                            <td><input type="number" min="0" step="0.01" class="form-control" v-model.number="item.discount" /></td>
                                            <td><input type="number" min="0" step="0.01" class="form-control" v-model.number="item.vat" /></td>
                                            <td class="text-end">{{ lineTotal(item).toFixed(2) }}</td>
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

                            <v-btn type="button" class="text-none mb-3" color="grey-lighten-3" rounded="0"
                                variant="flat" @click="addItem">
                                + Add Item
                            </v-btn>

                            <div class="row justify-content-end">
                                <div class="col-md-4">
                                    <table class="table table-sm">
                                        <tbody>
                                            <tr>
                                                <td>Total Amount</td>
                                                <td class="text-end">{{ totals.totalAmount.toFixed(2) }}</td>
                                            </tr>
                                            <tr>
                                                <td>Discount</td>
                                                <td class="text-end">{{ totals.discountAmount.toFixed(2) }}</td>
                                            </tr>
                                            <tr>
                                                <td>VAT</td>
                                                <td class="text-end">{{ totals.vatAmount.toFixed(2) }}</td>
                                            </tr>
                                            <tr class="fw-bold">
                                                <td>Net Total</td>
                                                <td class="text-end">{{ totals.netTotal.toFixed(2) }}</td>
                                            </tr>
                                            <tr>
                                                <td>Paid Amount</td>
                                                <td class="text-end">{{ paidAmount.toFixed(2) }}</td>
                                            </tr>
                                            <tr class="fw-bold">
                                                <td>Due Amount</td>
                                                <td class="text-end">{{ dueAmount.toFixed(2) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <small class="text-muted">Totals are recalculated by the server on save; the linked customer ledger entry is kept in sync automatically. Reducing the total below the amount already paid is rejected.</small>
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

const customerName = ref('')
const paidAmount = ref(0)
const dueAmount = ref(0)

const emptyItem = () => ({ item_name: '', description: '', qty: 1, unit_price: 0, discount: 0, vat: 0 })

// customer_id is intentionally not part of the submitted form — the backend
// does not accept changing a sale's customer after creation.
const { form, errors, isSubmitting, submit } = useStoreForm({
    invoice_number: '',
    issue_date: '',
    due_date: '',
    description: '',
    notes: '',
    items: [emptyItem()],
})

const addItem = () => form.items.push(emptyItem())
const removeItem = (index) => {
    if (form.items.length > 1) form.items.splice(index, 1)
}

const lineTotal = (item) => {
    const qty = Number(item.qty) || 0
    const unitPrice = Number(item.unit_price) || 0
    const discount = Number(item.discount) || 0
    const vat = Number(item.vat) || 0
    return (qty * unitPrice) - discount + vat
}

const totals = computed(() => {
    let totalAmount = 0
    let discountAmount = 0
    let vatAmount = 0

    form.items.forEach((item) => {
        totalAmount += (Number(item.qty) || 0) * (Number(item.unit_price) || 0)
        discountAmount += Number(item.discount) || 0
        vatAmount += Number(item.vat) || 0
    })

    return {
        totalAmount,
        discountAmount,
        vatAmount,
        netTotal: totalAmount - discountAmount + vatAmount,
    }
})

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
            paidAmount.value = Number(sale.paid_amount)
            dueAmount.value = Number(sale.due_amount)
            form.invoice_number = sale.invoice_number
            form.issue_date = sale.issue_date
            form.due_date = sale.due_date
            form.description = sale.description
            form.notes = sale.notes
            form.items = (sale.items ?? []).map((item) => ({
                item_name: item.item_name,
                description: item.description,
                qty: item.qty,
                unit_price: item.unit_price,
                discount: item.discount,
                vat: item.vat,
            }))
            if (!form.items.length) form.items = [emptyItem()]
        }
    } catch (e) {
        console.error(e)
    }
}

onMounted(() => {
    loadSale()
})
</script>
