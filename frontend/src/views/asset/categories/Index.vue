<template lang="html">
    <Breadcrumb title="Item Categories" />

    <div class="main-content-wrapper mt-4">
        <div class="container-fluid">

            <!-- Table Card -->
            <v-card style="padding: 5px; margin-top: 15px;">
                <div class="row">
                    <div class="col-md-12 mb-2">
                        <div class="row align-items-center">
                            <div class="col-md-5">
                                <v-btn v-if="can(['item-categories.create'])" type="button" @click="openAddDialog"
                                    class="text-none text-white" color="success" rounded="0" variant="flat">
                                    <i class="fa-solid fa-plus me-2"></i> {{ $t('Add Category') }}
                                </v-btn>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" v-model="filters.status">
                                    <option value="">{{ $t('All Status') }}</option>
                                    <option value="1">{{ $t('Active') }}</option>
                                    <option value="0">{{ $t('Inactive') }}</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <div class="search-wrapper d-flex align-center gap-2">
                                    <v-text-field variant="outlined" density="compact" :placeholder="$t('Search...')"
                                        v-model="filters.search" hide-details class="flex-grow-1"></v-text-field>
                                    <v-btn type="button" @click="fetchData" class="text-none text-white"
                                        color="blue-darken-3" rounded="0" variant="flat" min-width="100">
                                        {{ $t('Search') }}
                                    </v-btn>
                                    <v-btn @click.prevent="resetFilters" class="text-none" color="grey-lighten-3"
                                        rounded="0" variant="flat" min-width="100">
                                        {{ $t('Reload') }}
                                    </v-btn>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Data Table START -->
                    <v-table class="custom-bordered">
                        <thead>
                            <tr>
                                <th class="text-left">#</th>
                                <th class="text-left">{{ $t('Code') }}</th>
                                <th class="text-left">{{ $t('Category Name') }}</th>
                                <th class="text-center">{{ $t('Sub Categories') }}</th>
                                <th class="text-center">{{ $t('Status') }}</th>
                                <th class="text-center">{{ $t('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>

                            <tr v-if="loading">
                                <td colspan="6" class="text-center py-4">
                                    <v-progress-linear indeterminate color="primary" size="30"></v-progress-linear>
                                    {{ $t('Loading...') }}
                                </td>
                            </tr>

                            <tr v-else-if="!items.length">
                                <td colspan="6" class="text-center py-4">
                                    {{ $t('No records found.') }}
                                </td>
                            </tr>

                            <tr v-else v-for="(item, index) in items" :key="item.icatg_id">
                                <td>{{ (pagination.page - 1) * pagination.perPage + index + 1 }}</td>
                                <td>{{ item.icatg_code }}</td>
                                <td>{{ item.icatg_name }}</td>
                                <td class="text-center">{{ item.sub_categories_count ?? 0 }}</td>
                                <td class="text-center">
                                    <span :class="item.status ? 'badge bg-success' : 'badge bg-secondary'">
                                        {{ $t(item.status ? 'Active' : 'Inactive') }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <v-menu v-if="can(['item-categories.update'])">
                                        <template v-slot:activator="{ props }">
                                            <button type="button" class="table-action-button" v-bind="props">
                                                <i class="fa-solid fa-bars"></i>
                                            </button>
                                        </template>
                                        <ul class="table-action-menu">
                                            <li class="menu-item">
                                                <button type="button" class="menu-link" @click="openEditDialog(item)">
                                                    {{ $t('Edit') }}
                                                </button>
                                            </li>
                                            <li class="menu-item">
                                                <button type="button" class="menu-link" @click="toggleStatus(item)">
                                                    {{ $t(item.status ? 'Deactivate' : 'Activate') }}
                                                </button>
                                            </li>
                                        </ul>
                                    </v-menu>
                                </td>
                            </tr>
                        </tbody>
                    </v-table>
                    <!-- Data Table END -->

                    <BasePagination :current-page="pagination.page" :per-page="pagination.perPage"
                        :total="pagination.total" :last-page="pagination.lastPage" @page-change="changePage"
                        @per-page-change="changePerPage" />

                </div>
            </v-card>

        </div>
    </div>

    <!-- Add / Edit Category Dialog -->
    <v-dialog v-model="dialog" max-width="500">
        <v-card>
            <v-card-title>{{ $t(editingId ? 'Update Category' : 'Add Category') }}</v-card-title>
            <v-card-text>
                <form @submit.prevent="handleSubmit">
                    <div class="form-group mb-3">
                        <label for="icatg_name">{{ $t('Category Name:') }} <span class="text-danger">*</span></label>
                        <input type="text" id="icatg_name" class="form-control" v-model="form.icatg_name" maxlength="50"
                            required />
                        <div v-if="errors.icatg_name" class="error-msg">{{ errors.icatg_name }}</div>
                    </div>
                    <div class="form-group mb-3">
                        <label for="icatg_code">{{ $t('Code:') }} <span class="text-danger">*</span></label>
                        <input type="text" id="icatg_code" class="form-control" v-model="form.icatg_code" maxlength="10"
                            required />
                        <div v-if="errors.icatg_code" class="error-msg">{{ errors.icatg_code }}</div>
                    </div>
                    <div class="form-group mb-3">
                        <label for="icatg_status">{{ $t('Status:') }}</label>
                        <select id="icatg_status" class="form-select" v-model="form.status">
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
import { confirmStatusChange, fillValidationErrors } from '@/views/asset/helpers';

const {
    items,
    loading,
    filters,
    pagination,
    fetchData,
    changePage,
    changePerPage,
    resetFilters,
} = usePaginatedFetch('/api/asset/item-categories', {
    search: '',
    status: '',
})

const { can } = usePermission()

const initialForm = () => ({
    icatg_name: '',
    icatg_code: '',
    status: true,
})

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

const openEditDialog = (item) => {
    editingId.value = item.icatg_id
    Object.assign(form, { icatg_name: item.icatg_name, icatg_code: item.icatg_code, status: item.status })
    clearErrors()
    dialog.value = true
}

const handleSubmit = async () => {
    clearErrors()
    if (!form.icatg_name) errors.icatg_name = t('The name field is required.')
    if (!form.icatg_code) errors.icatg_code = t('The code field is required.')
    if (Object.keys(errors).length) return

    isSubmitting.value = true

    try {
        const { data } = editingId.value
            ? await axios.put(`/api/asset/item-categories/${editingId.value}`, form)
            : await axios.post('/api/asset/item-categories', form)

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

const toggleStatus = async (item) => {
    const changed = await confirmStatusChange(
        `/api/asset/item-categories/${item.icatg_id}/status`,
        item.status,
        item.icatg_name,
    )
    if (changed) fetchData()
}

onMounted(() => {
    fetchData()
})

</script>
