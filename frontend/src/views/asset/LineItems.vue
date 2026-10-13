<template>
    <!-- Editable item lines: item + quantity, optionally with price, discount and tax. -->
    <v-table class="custom-bordered">
        <thead>
            <tr>
                <th class="text-left" style="min-width: 240px">{{ $t('Item') }}</th>
                <th class="text-right" style="width: 120px">{{ $t('Quantity') }}</th>
                <template v-if="withPrice">
                    <th class="text-right" style="width: 120px">{{ $t('Unit Price') }}</th>
                    <th class="text-right" style="width: 110px">{{ $t('Discount') }}</th>
                    <th class="text-right" style="width: 110px">{{ $t('Tax') }}</th>
                    <th class="text-right" style="width: 120px">{{ $t('Total') }}</th>
                </template>
                <th style="width: 50px"></th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(line, index) in lines" :key="index">
                <td>
                    <SearchSelect v-model="line.item_id" :items="items" item-value="item_id"
                        :item-title="(item) => `${item.item_code} - ${item.item_name}${item.unit_name ? ` (${item.unit_name})` : ''}`"
                        :placeholder="$t('Select Item')" />
                </td>
                <td>
                    <input type="number" min="0" step="any" class="form-control text-end" v-model="line[qtyKey]" />
                </td>
                <template v-if="withPrice">
                    <td><input type="number" min="0" step="any" class="form-control text-end" v-model="line.unit_price" /></td>
                    <td><input type="number" min="0" step="any" class="form-control text-end" v-model="line.discount" /></td>
                    <td><input type="number" min="0" step="any" class="form-control text-end" v-model="line.tax" /></td>
                    <td class="text-right">{{ lineTotal(line).toFixed(2) }}</td>
                </template>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger" :disabled="lines.length === 1"
                        @click="lines.splice(index, 1)">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </td>
            </tr>
            <tr v-if="withPrice">
                <td colspan="5" class="text-right"><strong>{{ $t('Grand Total') }}</strong></td>
                <td class="text-right"><strong>{{ grandTotal.toFixed(2) }}</strong></td>
                <td></td>
            </tr>
        </tbody>
    </v-table>
    <v-btn class="text-none mt-2" color="blue-darken-3" variant="outlined" rounded="0" size="small" @click="addLine">
        <i class="fa-solid fa-plus me-1"></i> {{ $t('Add Line') }}
    </v-btn>
</template>
<script setup>
import SearchSelect from '@/components/common/SearchSelect.vue';

const props = defineProps({
    items: { type: Array, default: () => [] },
    qtyKey: { type: String, default: 'quantity' },
    withPrice: { type: Boolean, default: false },
})

const lines = defineModel({ type: Array, required: true })

const lineTotal = (line) =>
    Number(line[props.qtyKey] || 0) * Number(line.unit_price || 0) - Number(line.discount || 0) + Number(line.tax || 0)

const grandTotal = computed(() => lines.value.reduce((sum, line) => sum + lineTotal(line), 0))

const addLine = () => {
    lines.value.push(props.withPrice
        ? { item_id: '', [props.qtyKey]: 1, unit_price: 0, discount: 0, tax: 0 }
        : { item_id: '', [props.qtyKey]: 1 })
}
</script>
