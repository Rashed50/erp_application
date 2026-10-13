<template lang="html">
    <Breadcrumb title="Inventory Stock" />

    <div class="main-content-wrapper mt-4">
        <div class="container-fluid">
            <v-card style="padding: 5px; margin-top: 15px;">
                <v-tabs v-model="tab" color="blue-darken-3" class="mb-3">
                    <v-tab value="stock">{{ $t('Current Stock') }}</v-tab>
                    <v-tab value="ledger">{{ $t('Stock Movements') }}</v-tab>
                </v-tabs>

                <!-- Current stock -->
                <div v-show="tab === 'stock'" class="row">
                    <div class="col-md-12 mb-2">
                        <div class="row align-items-center g-2">
                            <div class="col-md-3">
                                <SearchSelect v-model="stock.filters.warehouse_id" :items="lookups.warehouses"
                                    item-title="name" :placeholder="$t('All Stores')" />
                            </div>
                            <div class="col-md-3">
                                <SearchSelect v-model="stock.filters.item_id" :items="lookups.items"
                                    item-value="item_id" :item-title="(i) => `${i.item_code} - ${i.item_name}`"
                                    :placeholder="$t('All Items')" />
                            </div>
                            <div class="col-md-2">
                                <v-checkbox v-model="stock.filters.low_stock" :true-value="1" :false-value="''"
                                    :label="$t('Low stock only')" density="compact" hide-details></v-checkbox>
                            </div>
                            <div class="col-md-4 d-flex gap-2">
                                <v-btn type="button" @click="stock.fetchData" class="text-none text-white"
                                    color="blue-darken-3" rounded="0" variant="flat" min-width="90">
                                    {{ $t('Search') }}
                                </v-btn>
                                <v-btn @click.prevent="stock.resetFilters" class="text-none" color="grey-lighten-3"
                                    rounded="0" variant="flat" min-width="90">
                                    {{ $t('Reload') }}
                                </v-btn>
                            </div>
                        </div>
                    </div>

                    <v-table class="custom-bordered">
                        <thead>
                            <tr>
                                <th class="text-left">#</th>
                                <th class="text-left">{{ $t('Store') }}</th>
                                <th class="text-left">{{ $t('Item') }}</th>
                                <th class="text-right">{{ $t('Quantity') }}</th>
                                <th class="text-right">{{ $t('Reserved') }}</th>
                                <th class="text-right">{{ $t('Available') }}</th>
                                <th class="text-right">{{ $t('Minimum Stock') }}</th>
                                <th class="text-left">{{ $t('Unit') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="stock.loading">
                                <td colspan="8" class="text-center py-4">
                                    <v-progress-linear indeterminate color="primary"></v-progress-linear>
                                    {{ $t('Loading...') }}
                                </td>
                            </tr>
                            <tr v-else-if="!stock.items.length">
                                <td colspan="8" class="text-center py-4">{{ $t('No records found.') }}</td>
                            </tr>
                            <tr v-else v-for="(row, index) in stock.items" :key="row.id"
                                :class="{ 'table-danger': qty(row.available_quantity) <= qty(row.minimum_stock) && qty(row.minimum_stock) > 0 }">
                                <td>{{ (stock.pagination.page - 1) * stock.pagination.perPage + index + 1 }}</td>
                                <td>{{ row.warehouse_name }}</td>
                                <td>{{ row.item_code }} - {{ row.item_name }}</td>
                                <td class="text-right">{{ qty(row.quantity) }}</td>
                                <td class="text-right">{{ qty(row.reserved_quantity) }}</td>
                                <td class="text-right">{{ qty(row.available_quantity) }}</td>
                                <td class="text-right">{{ qty(row.minimum_stock) }}</td>
                                <td>{{ row.unit_name || '-' }}</td>
                            </tr>
                        </tbody>
                    </v-table>

                    <BasePagination :current-page="stock.pagination.page" :per-page="stock.pagination.perPage"
                        :total="stock.pagination.total" :last-page="stock.pagination.lastPage"
                        @page-change="stock.changePage" @per-page-change="stock.changePerPage" />
                </div>

                <!-- Movement ledger -->
                <div v-show="tab === 'ledger'" class="row">
                    <div class="col-md-12 mb-2">
                        <div class="row align-items-center g-2">
                            <div class="col-md-2">
                                <SearchSelect v-model="ledger.filters.warehouse_id" :items="lookups.warehouses"
                                    item-title="name" :placeholder="$t('All Stores')" />
                            </div>
                            <div class="col-md-2">
                                <SearchSelect v-model="ledger.filters.item_id" :items="lookups.items"
                                    item-value="item_id" :item-title="(i) => `${i.item_code} - ${i.item_name}`"
                                    :placeholder="$t('All Items')" />
                            </div>
                            <div class="col-md-2">
                                <select class="form-select" v-model="ledger.filters.transaction_type">
                                    <option value="">{{ $t('All Types') }}</option>
                                    <option v-for="type in transactionTypes" :key="type" :value="type">
                                        {{ $t(statusLabel(type)) }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="date" class="form-control" v-model="ledger.filters.date_from" />
                            </div>
                            <div class="col-md-2">
                                <input type="date" class="form-control" v-model="ledger.filters.date_to" />
                            </div>
                            <div class="col-md-2 d-flex gap-2">
                                <v-btn type="button" @click="ledger.fetchData" class="text-none text-white"
                                    color="blue-darken-3" rounded="0" variant="flat">
                                    {{ $t('Search') }}
                                </v-btn>
                                <v-btn @click.prevent="ledger.resetFilters" class="text-none" color="grey-lighten-3"
                                    rounded="0" variant="flat">
                                    {{ $t('Reload') }}
                                </v-btn>
                            </div>
                        </div>
                    </div>

                    <v-table class="custom-bordered">
                        <thead>
                            <tr>
                                <th class="text-left">{{ $t('Date') }}</th>
                                <th class="text-left">{{ $t('Type') }}</th>
                                <th class="text-left">{{ $t('Item') }}</th>
                                <th class="text-left">{{ $t('Serial No') }}</th>
                                <th class="text-right">{{ $t('Quantity') }}</th>
                                <th class="text-left">{{ $t('From') }}</th>
                                <th class="text-left">{{ $t('To') }}</th>
                                <th class="text-left">{{ $t('Employee') }}</th>
                                <th class="text-left">{{ $t('By') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="ledger.loading">
                                <td colspan="9" class="text-center py-4">
                                    <v-progress-linear indeterminate color="primary"></v-progress-linear>
                                    {{ $t('Loading...') }}
                                </td>
                            </tr>
                            <tr v-else-if="!ledger.items.length">
                                <td colspan="9" class="text-center py-4">{{ $t('No records found.') }}</td>
                            </tr>
                            <tr v-else v-for="row in ledger.items" :key="row.id">
                                <td>{{ String(row.transaction_date).slice(0, 16).replace('T', ' ') }}</td>
                                <td>{{ $t(statusLabel(row.transaction_type)) }}</td>
                                <td>{{ row.item_code }} - {{ row.item_name }}</td>
                                <td>{{ row.serial_number || '-' }}</td>
                                <td class="text-right">{{ qty(row.quantity) }}</td>
                                <td>{{ row.from_warehouse_name || '-' }}</td>
                                <td>{{ row.to_warehouse_name || '-' }}</td>
                                <td>{{ row.employee_name || '-' }}</td>
                                <td>{{ row.performed_by_name || '-' }}</td>
                            </tr>
                        </tbody>
                    </v-table>

                    <BasePagination :current-page="ledger.pagination.page" :per-page="ledger.pagination.perPage"
                        :total="ledger.pagination.total" :last-page="ledger.pagination.lastPage"
                        @page-change="ledger.changePage" @per-page-change="ledger.changePerPage" />
                </div>
            </v-card>
        </div>
    </div>
</template>
<script setup>
import BasePagination from '@/components/common/BasePagination.vue';
import Breadcrumb from '@/components/common/Breadcrumb.vue';
import SearchSelect from '@/components/common/SearchSelect.vue';
import { usePaginatedFetch } from '@/composables/usePaginatedFetch';
import { qty, statusLabel, useInventoryLookups } from '@/views/asset/helpers';

const tab = ref('stock')

// reactive() unwraps the composables' refs so the template can use stock.items etc.
const stock = reactive(usePaginatedFetch('/api/asset/stocks', { warehouse_id: '', item_id: '', low_stock: '' }))
const ledger = reactive(usePaginatedFetch('/api/asset/stock-transactions', {
    warehouse_id: '', item_id: '', transaction_type: '', date_from: '', date_to: '',
}))

const { lookups, loadLookups } = useInventoryLookups()

const transactionTypes = computed(() => ledger.payload.types || [])

let ledgerLoaded = false
watch(tab, (value) => {
    if (value === 'ledger' && !ledgerLoaded) {
        ledgerLoaded = true
        ledger.fetchData()
    }
})

onMounted(() => {
    stock.fetchData()
    loadLookups()
})
</script>
