<template lang="html">
    <Breadcrumb title="Advance Salary" />

    <div class="main-content-wrapper mt-4">
        <div class="container-fluid">
            <v-card style="padding: 5px; margin-top: 15px;">
                <div class="row align-items-center mb-2">
                    <div class="col-md-2">
                        <input type="date" class="form-control" v-model="filters.from_date" :title="$t('From')" />
                    </div>
                    <div class="col-md-2">
                        <input type="date" class="form-control" v-model="filters.to_date" :title="$t('To')" />
                    </div>
                    <div class="col-md-2">
                        <SearchSelect v-model="filters.status" :items="statuses" :placeholder="$t('All Status')" />
                    </div>
                    <div class="col-md-6">
                        <div class="search-wrapper d-flex align-center gap-2">
                            <v-text-field variant="outlined" density="compact" :placeholder="$t('Employee ID or name...')"
                                v-model="filters.search" hide-details class="flex-grow-1"></v-text-field>
                            <v-btn type="button" @click="search" class="text-none text-white" color="blue-darken-3"
                                rounded="0" variant="flat" min-width="90">
                                {{ $t('Search') }}
                            </v-btn>
                            <v-btn @click.prevent="resetFilters" class="text-none" color="grey-lighten-3" rounded="0"
                                variant="flat" min-width="90">
                                {{ $t('Reload') }}
                            </v-btn>
                            <v-btn v-if="can(['employee-advances.create'])" @click="openForm(null)"
                                class="text-none text-white" color="green-darken-2" rounded="0" variant="flat">
                                <i class="fa-solid fa-circle-plus me-1"></i> {{ $t('New Advance') }}
                            </v-btn>
                        </div>
                    </div>
                </div>

                <v-table class="custom-bordered">
                    <thead>
                        <tr>
                            <th>{{ $t('Date') }}</th>
                            <th>{{ $t('Employee') }}</th>
                            <th>{{ $t('Purpose') }}</th>
                            <th class="text-right">{{ $t('Amount') }}</th>
                            <th class="text-right">{{ $t('Installment') }}</th>
                            <th>{{ $t('Deduction From') }}</th>
                            <th class="text-right">{{ $t('Recovered') }}</th>
                            <th class="text-right">{{ $t('Outstanding') }}</th>
                            <th>{{ $t('Status') }}</th>
                            <th class="text-center">{{ $t('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="loading">
                            <td colspan="10" class="text-center py-4">
                                <v-progress-linear indeterminate color="primary"></v-progress-linear>
                                {{ $t('Loading...') }}
                            </td>
                        </tr>
                        <tr v-else-if="!items.length">
                            <td colspan="10" class="text-center py-4">{{ $t('No advance salary found.') }}</td>
                        </tr>
                        <tr v-else v-for="item in items" :key="item.id">
                            <td>{{ item.advance_date }}</td>
                            <td>
                                {{ item.employee_code }} - {{ item.employee_name }}
                                <div class="small text-muted">{{ item.designation }}</div>
                            </td>
                            <td>{{ item.purpose }}</td>
                            <td class="text-right">{{ money(item.amount) }}</td>
                            <td class="text-right">
                                {{ money(item.installment_amount) }}
                                <div class="small text-muted">× {{ item.installment_count }}</div>
                            </td>
                            <td>{{ monthLabel(item.deduction_start_month) }}</td>
                            <td class="text-right">{{ money(item.recovered_amount) }}</td>
                            <td class="text-right"><strong>{{ money(item.outstanding_amount) }}</strong></td>
                            <td><span :class="advanceStatusClass(item.status)">{{ $t(item.status) }}</span></td>
                            <td class="text-center text-nowrap">
                                <v-btn size="small" variant="text" color="blue-darken-3" icon="mdi-eye"
                                    :title="$t('Details')" @click="openDetail(item)"></v-btn>
                                <v-btn v-if="can(['employee-advances.update'])" size="small" variant="text"
                                    color="orange-darken-3" icon="mdi-pencil" :title="$t('Edit')"
                                    @click="openForm(item)"></v-btn>
                                <v-btn v-if="can(['employee-advances.delete']) && !item.recovered_amount" size="small"
                                    variant="text" color="red-darken-2" icon="mdi-delete" :title="$t('Delete')"
                                    @click="handleDelete(item)"></v-btn>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot v-if="items.length">
                        <tr class="fw-bold">
                            <td colspan="3" class="text-right">{{ $t('Total ({count} advances)', { count: totals.count }) }}</td>
                            <td class="text-right">{{ money(totals.amount) }}</td>
                            <td colspan="2"></td>
                            <td class="text-right">{{ money(totals.recovered_amount) }}</td>
                            <td class="text-right">{{ money(totals.outstanding_amount) }}</td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </v-table>

                <BasePagination :current-page="pagination.page" :per-page="pagination.perPage" :total="pagination.total"
                    :last-page="pagination.lastPage" @page-change="changePage" @per-page-change="changePerPage" />
            </v-card>
        </div>
    </div>

    <AdvanceDialog v-model="formOpen" :advance="editing" :employees="employees" @saved="fetchData" />
    <AdvanceDetailDialog v-model="detailOpen" :advance-id="detailId" @changed="fetchData" />
</template>

<script setup>
import axios from 'axios';
import Swal from 'sweetalert2';
import { toast } from 'vue3-toastify';
import BasePagination from '@/components/common/BasePagination.vue';
import Breadcrumb from '@/components/common/Breadcrumb.vue';
import SearchSelect from '@/components/common/SearchSelect.vue';
import { usePaginatedFetch } from '@/composables/usePaginatedFetch';
import { usePermission } from '@/composables/usePermission';
import { useRoute } from 'vue-router';
import { ADVANCE_STATUSES, advanceStatusClass, money, monthLabel } from '../helpers';
import { t } from '@/i18n';
import AdvanceDialog from './AdvanceDialog.vue';
import AdvanceDetailDialog from './AdvanceDetailDialog.vue';

const route = useRoute()
const { can } = usePermission()

const statuses = computed(() => ADVANCE_STATUSES.map((status) => ({ id: status, name: t(status) })))

const {
    items,
    payload,
    loading,
    filters,
    pagination,
    fetchData,
    changePage,
    changePerPage,
    resetFilters,
} = usePaginatedFetch('/api/hr/employee-advances', {
    from_date: '',
    to_date: '',
    status: '',
    search: route.query.search || '',
})

const totals = computed(() => payload.value.totals ?? { count: 0, amount: 0, recovered_amount: 0, outstanding_amount: 0 })

const search = () => {
    pagination.page = 1
    fetchData()
}

const employees = ref([])
const formOpen = ref(false)
const editing = ref(null)
const detailOpen = ref(false)
const detailId = ref(null)

const openForm = (advance) => {
    editing.value = advance
    formOpen.value = true
}

const openDetail = (advance) => {
    detailId.value = advance.id
    detailOpen.value = true
}

const handleDelete = async (advance) => {
    const result = await Swal.fire({
        title: t('Delete this advance salary?'),
        text: `${advance.employee_code} - ${advance.employee_name}: ${money(advance.amount)}`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: t('Yes, delete'),
    })
    if (!result.isConfirmed) return

    try {
        const { data } = await axios.delete(`/api/hr/employee-advances/${advance.id}`)
        toast.success(data.message)
        fetchData()
    } catch (e) {
        toast.error(e.response?.data?.message || t('Failed to delete.'))
    }
}

onMounted(async () => {
    fetchData()
    if (!can(['employee-advances.create'])) return
    try {
        const { data } = await axios.get('/api/hr/employees', { params: { per_page: 1000, status: 'Active' } })
        employees.value = data.data?.employees ?? []
    } catch (e) {
        console.error(e)
    }
})
</script>
