<template lang="html">
    <Breadcrumb title="Units" />

    <div class="main-content-wrapper mt-4">
        <div class="container-fluid">
            <v-card style="padding: 5px; margin-top: 15px;">
                <div class="row">
                    <div class="col-md-12 mb-2">
                        <div class="row align-items-center g-2">
                            <div class="col-md-2">
                                <v-btn v-if="can(['inventory-units.create'])" type="button" @click="openAddDialog"
                                    class="text-none text-white" color="success" rounded="0" variant="flat">
                                    <i class="fa-solid fa-plus me-2"></i> {{ $t('Add Unit') }}
                                </v-btn>
                            </div>
                            <div class="col-md-2">
                                <select class="form-select" v-model="filters.status">
                                    <option value="">{{ $t('All Status') }}</option>
                                    <option value="1">{{ $t('Active') }}</option>
                                    <option value="0">{{ $t('Inactive') }}</option>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <div class="search-wrapper d-flex align-center gap-2">
                                    <v-text-field variant="outlined" density="compact" :placeholder="$t('Search...')"
                                        v-model="filters.search" hide-details class="flex-grow-1"></v-text-field>
                                    <v-btn type="button" @click="fetchData" class="text-none text-white"
                                        color="blue-darken-3" rounded="0" variant="flat" min-width="90">
                                        {{ $t('Search') }}
                                    </v-btn>
                                    <v-btn @click.prevent="resetFilters" class="text-none" color="grey-lighten-3"
                                        rounded="0" variant="flat" min-width="90">
                                        {{ $t('Reload') }}
                                    </v-btn>
                                </div>
                            </div>
                        </div>
                    </div>

                    <v-table class="custom-bordered">
                        <thead>
                            <tr>
                                <th class="text-left">#</th>
                                <th class="text-left">{{ $t('Unit Name') }}</th>
                                <th class="text-left">{{ $t('Short Name') }}</th>
                                <th class="text-center">{{ $t('Status') }}</th>
                                <th class="text-center">{{ $t('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="loading">
                                <td colspan="5" class="text-center py-4">
                                    <v-progress-linear indeterminate color="primary"></v-progress-linear>
                                    {{ $t('Loading...') }}
                                </td>
                            </tr>
                            <tr v-else-if="!items.length">
                                <td colspan="5" class="text-center py-4">{{ $t('No records found.') }}</td>
                            </tr>
                            <tr v-else v-for="(unit, index) in items" :key="unit.id">
                                <td>{{ (pagination.page - 1) * pagination.perPage + index + 1 }}</td>
                                <td>{{ unit.name }}</td>
                                <td>{{ unit.short_name }}</td>
                                <td class="text-center">
                                    <span :class="unit.status ? 'badge bg-success' : 'badge bg-secondary'">
                                        {{ $t(unit.status ? 'Active' : 'Inactive') }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <button v-if="can(['inventory-units.update'])" type="button"
                                        class="table-action-button" @click="openEditDialog(unit)">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </v-table>

                    <BasePagination :current-page="pagination.page" :per-page="pagination.perPage"
                        :total="pagination.total" :last-page="pagination.lastPage" @page-change="changePage"
                        @per-page-change="changePerPage" />
                </div>
            </v-card>
        </div>
    </div>

    <v-dialog v-model="dialog" max-width="500">
        <v-card>
            <v-card-title>{{ $t(editingId ? 'Update Unit' : 'Add Unit') }}</v-card-title>
            <v-card-text>
                <form @submit.prevent="handleSubmit">
                    <div class="form-group mb-3">
                        <label for="unit_name">{{ $t('Unit Name:') }} <span class="text-danger">*</span></label>
                        <input type="text" id="unit_name" class="form-control" v-model="form.name" maxlength="100"
                            :placeholder="$t('e.g. Pieces')" required />
                        <div v-if="errors.name" class="error-msg">{{ errors.name }}</div>
                    </div>
                    <div class="form-group mb-3">
                        <label for="unit_short_name">{{ $t('Short Name:') }} <span class="text-danger">*</span></label>
                        <input type="text" id="unit_short_name" class="form-control" v-model="form.short_name"
                            maxlength="20" :placeholder="$t('e.g. pcs')" required />
                        <div v-if="errors.short_name" class="error-msg">{{ errors.short_name }}</div>
                    </div>
                    <div class="form-group mb-3">
                        <label for="unit_status">{{ $t('Status:') }}</label>
                        <select id="unit_status" class="form-select" v-model="form.status">
                            <option :value="true">{{ $t('Active') }}</option>
                            <option :value="false">{{ $t('Inactive') }}</option>
                        </select>
                    </div>
                </form>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn class="text-none" color="grey-lighten-3" rounded="0" variant="flat" @click="dialog = false">
                    {{ $t('Cancel') }}
                </v-btn>
                <v-btn class="text-none text-white" color="blue-darken-4" rounded="0" variant="flat"
                    :disabled="isSubmitting" :loading="isSubmitting" @click="handleSubmit">
                    {{ $t(editingId ? 'Update' : 'Save') }}
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
<script setup>
import axios from 'axios';
import { toast } from 'vue3-toastify';
import BasePagination from '@/components/common/BasePagination.vue';
import Breadcrumb from '@/components/common/Breadcrumb.vue';
import { usePermission } from '@/composables/usePermission';
import { usePaginatedFetch } from '@/composables/usePaginatedFetch';
import { t } from '@/i18n';
import { fillValidationErrors } from '@/views/asset/helpers';

const { items, loading, filters, pagination, fetchData, changePage, changePerPage, resetFilters } =
    usePaginatedFetch('/api/asset/units', { search: '', status: '' })

const { can } = usePermission()

const initialForm = () => ({ name: '', short_name: '', status: true })

const dialog = ref(false)
const editingId = ref(null)
const form = reactive(initialForm())
const errors = reactive({})
const isSubmitting = ref(false)

const clearErrors = () => {
    for (const key in errors) delete errors[key]
}

const openAddDialog = () => {
    editingId.value = null
    Object.assign(form, initialForm())
    clearErrors()
    dialog.value = true
}

const openEditDialog = (unit) => {
    editingId.value = unit.id
    Object.assign(form, { name: unit.name, short_name: unit.short_name, status: unit.status })
    clearErrors()
    dialog.value = true
}

const handleSubmit = async () => {
    clearErrors()
    if (!form.name) errors.name = t('The name field is required.')
    if (!form.short_name) errors.short_name = t('The short name field is required.')
    if (Object.keys(errors).length) return

    isSubmitting.value = true

    try {
        const { data } = editingId.value
            ? await axios.put(`/api/asset/units/${editingId.value}`, form)
            : await axios.post('/api/asset/units', form)

        if (data.success) {
            toast.success(data.message)
            dialog.value = false
            fetchData()
        }
    } catch (err) {
        fillValidationErrors(err, errors)
    } finally {
        isSubmitting.value = false
    }
}

onMounted(fetchData)
</script>
