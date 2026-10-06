<template lang="html">
    <Breadcrumb title="Item Names" />

    <div class="main-content-wrapper mt-4">
        <div class="container-fluid">

            <!-- Table Card -->
            <v-card style="padding: 5px; margin-top: 15px;">
                <div class="row">
                    <div class="col-md-12 mb-2">
                        <div class="row align-items-center g-2">
                            <div class="col-md-2">
                                <v-btn v-if="can(['item-names.create'])" type="button" @click="openAddDialog"
                                    class="text-none text-white" color="success" rounded="0" variant="flat">
                                    <i class="fa-solid fa-plus me-2"></i> {{ $t('Add Item') }}
                                </v-btn>
                            </div>
                            <div class="col-md-2">
                                <select class="form-select" v-model="filters.icatg_id" @change="onFilterCategoryChange">
                                    <option value="">{{ $t('All Categories') }}</option>
                                    <option v-for="category in categories" :key="category.icatg_id"
                                        :value="category.icatg_id">
                                        {{ category.icatg_name }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select class="form-select" v-model="filters.iscatg_id">
                                    <option value="">{{ $t('All Sub Categories') }}</option>
                                    <option v-for="subCategory in filterSubCategories" :key="subCategory.iscatg_id"
                                        :value="subCategory.iscatg_id">
                                        {{ subCategory.iscatg_name }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <select class="form-select" v-model="filters.itype_id">
                                    <option value="">{{ $t('All Types') }}</option>
                                    <option v-for="type in ITEM_TYPES" :key="type.id" :value="type.id">
                                        {{ $t(type.name) }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-1">
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
                                        color="blue-darken-3" rounded="0" variant="flat" min-width="90">
                                        {{ $t('Search') }}
                                    </v-btn>
                                    <v-btn @click.prevent="resetAllFilters" class="text-none" color="grey-lighten-3"
                                        rounded="0" variant="flat" min-width="90">
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
                                <th class="text-left">{{ $t('Item Name') }}</th>
                                <th class="text-left">{{ $t('Title') }}</th>
                                <th class="text-left">{{ $t('Category') }}</th>
                                <th class="text-left">{{ $t('Sub Category') }}</th>
                                <th class="text-center">{{ $t('Type') }}</th>
                                <th class="text-center">{{ $t('Status') }}</th>
                                <th class="text-center">{{ $t('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>

                            <tr v-if="loading">
                                <td colspan="9" class="text-center py-4">
                                    <v-progress-linear indeterminate color="primary" size="30"></v-progress-linear>
                                    {{ $t('Loading...') }}
                                </td>
                            </tr>

                            <tr v-else-if="!items.length">
                                <td colspan="9" class="text-center py-4">
                                    {{ $t('No records found.') }}
                                </td>
                            </tr>

                            <tr v-else v-for="(item, index) in items" :key="item.item_id">
                                <td>{{ (pagination.page - 1) * pagination.perPage + index + 1 }}</td>
                                <td>{{ item.item_code }}</td>
                                <td>{{ item.item_name }}</td>
                                <td>{{ item.item_title }}</td>
                                <td>{{ item.icatg_name }}</td>
                                <td>{{ item.iscatg_name }}</td>
                                <td class="text-center">
                                    <span :class="item.itype_id === 1 ? 'badge bg-primary' : 'badge bg-info text-dark'">
                                        {{ $t(item.itype_name) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span :class="item.item_status ? 'badge bg-success' : 'badge bg-secondary'">
                                        {{ $t(item.item_status ? 'Active' : 'Inactive') }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <v-menu v-if="can(['item-names.update'])">
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
                                                    {{ $t(item.item_status ? 'Deactivate' : 'Activate') }}
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

    <!-- Add / Edit Item Dialog -->
    <v-dialog v-model="dialog" max-width="600">
        <v-card>
            <v-card-title>{{ $t(editingId ? 'Update Item' : 'Add Item') }}</v-card-title>
            <v-card-text>
                <form @submit.prevent="handleSubmit">
                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label for="item_category">{{ $t('Category:') }} <span class="text-danger">*</span></label>
                            <select id="item_category" class="form-select" v-model="form.icatg_id"
                                @change="onFormCategoryChange" required>
                                <option value="">{{ $t('Select Category') }}</option>
                                <option v-for="category in formCategories" :key="category.icatg_id"
                                    :value="category.icatg_id">
                                    {{ category.icatg_name }}
                                </option>
                            </select>
                            <div v-if="errors.icatg_id" class="error-msg">{{ errors.icatg_id }}</div>
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label for="item_sub_category">{{ $t('Sub Category:') }} <span
                                    class="text-danger">*</span></label>
                            <select id="item_sub_category" class="form-select" v-model="form.iscatg_id"
                                :disabled="!form.icatg_id" required>
                                <option value="">{{ $t('Select Sub Category') }}</option>
                                <option v-for="subCategory in formSubCategories" :key="subCategory.iscatg_id"
                                    :value="subCategory.iscatg_id">
                                    {{ subCategory.iscatg_name }}
                                </option>
                            </select>
                            <div v-if="errors.iscatg_id" class="error-msg">{{ errors.iscatg_id }}</div>
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label for="item_type">{{ $t('Item Type:') }} <span class="text-danger">*</span></label>
                            <select id="item_type" class="form-select" v-model="form.itype_id" required>
                                <option v-for="type in ITEM_TYPES" :key="type.id" :value="type.id">
                                    {{ $t(type.name) }}
                                </option>
                            </select>
                            <div v-if="errors.itype_id" class="error-msg">{{ errors.itype_id }}</div>
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label for="item_code">{{ $t('Code:') }} <span class="text-danger">*</span></label>
                            <input type="text" id="item_code" class="form-control" v-model="form.item_code"
                                maxlength="10" required />
                            <div v-if="errors.item_code" class="error-msg">{{ errors.item_code }}</div>
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label for="item_name">{{ $t('Item Name:') }} <span class="text-danger">*</span></label>
                            <input type="text" id="item_name" class="form-control" v-model="form.item_name"
                                maxlength="50" required />
                            <div v-if="errors.item_name" class="error-msg">{{ errors.item_name }}</div>
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label for="item_title">{{ $t('Item Title:') }} <span class="text-danger">*</span></label>
                            <input type="text" id="item_title" class="form-control" v-model="form.item_title"
                                maxlength="50" required />
                            <div v-if="errors.item_title" class="error-msg">{{ errors.item_title }}</div>
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label for="item_status">{{ $t('Status:') }}</label>
                            <select id="item_status" class="form-select" v-model="form.item_status">
                                <option :value="true">{{ $t('Active') }}</option>
                                <option :value="false">{{ $t('Inactive') }}</option>
                            </select>
                        </div>
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
import { ITEM_TYPES, confirmStatusChange, fillValidationErrors } from '@/views/asset/helpers';

const {
    items,
    loading,
    filters,
    pagination,
    fetchData,
    changePage,
    changePerPage,
    resetFilters,
} = usePaginatedFetch('/api/asset/item-names', {
    search: '',
    status: '',
    icatg_id: '',
    iscatg_id: '',
    itype_id: '',
})

const { can } = usePermission()

/* ---------- dropdown options (active records only) ---------- */
const categories = ref([])
const filterSubCategories = ref([])
const formSubCategoryOptions = ref([])

const fetchCategories = async () => {
    try {
        const { data } = await axios.get('/api/asset/item-categories/options')
        categories.value = data.data || []
    } catch (err) {
        toast.error(err.response?.data?.message || t('common.failedToLoad'))
    }
}

const fetchSubCategories = async (categoryId) => {
    if (!categoryId) return []
    try {
        const { data } = await axios.get('/api/asset/item-sub-categories/options', { params: { icatg_id: categoryId } })
        return data.data || []
    } catch (err) {
        toast.error(err.response?.data?.message || t('common.failedToLoad'))
        return []
    }
}

const onFilterCategoryChange = async () => {
    filters.iscatg_id = ''
    filterSubCategories.value = await fetchSubCategories(filters.icatg_id)
}

const resetAllFilters = () => {
    filterSubCategories.value = []
    resetFilters()
}

/* ---------- add / edit dialog ---------- */
const initialForm = () => ({
    icatg_id: '',
    iscatg_id: '',
    itype_id: ITEM_TYPES[0].id,
    item_name: '',
    item_title: '',
    item_code: '',
    item_status: true,
})

const dialog = ref(false)
const editingId = ref(null)
// The edited item's own category / sub category, kept selectable even if since deactivated.
const editingItem = ref(null)
const form = reactive(initialForm())
const errors = reactive({})
const isSubmitting = ref(false)

const formCategories = computed(() => {
    const current = editingItem.value
    if (!current || categories.value.some(c => c.icatg_id === current.icatg_id)) return categories.value
    return [{ icatg_id: current.icatg_id, icatg_name: current.icatg_name }, ...categories.value]
})

const formSubCategories = computed(() => {
    const current = editingItem.value
    if (!current || current.icatg_id !== form.icatg_id
        || formSubCategoryOptions.value.some(s => s.iscatg_id === current.iscatg_id)) {
        return formSubCategoryOptions.value
    }
    return [{ iscatg_id: current.iscatg_id, iscatg_name: current.iscatg_name }, ...formSubCategoryOptions.value]
})

const onFormCategoryChange = async () => {
    form.iscatg_id = ''
    formSubCategoryOptions.value = await fetchSubCategories(form.icatg_id)
}

const clearErrors = () => {
    for (const key in errors) delete errors[key]
}

const openAddDialog = () => {
    editingId.value = null
    editingItem.value = null
    formSubCategoryOptions.value = []
    Object.assign(form, initialForm())
    clearErrors()
    dialog.value = true
}

const openEditDialog = async (item) => {
    editingId.value = item.item_id
    editingItem.value = item
    Object.assign(form, {
        icatg_id: item.icatg_id,
        iscatg_id: item.iscatg_id,
        itype_id: item.itype_id,
        item_name: item.item_name,
        item_title: item.item_title,
        item_code: item.item_code,
        item_status: item.item_status,
    })
    clearErrors()
    dialog.value = true
    formSubCategoryOptions.value = await fetchSubCategories(item.icatg_id)
}

const handleSubmit = async () => {
    clearErrors()
    if (!form.icatg_id) errors.icatg_id = t('Please select a category.')
    if (!form.iscatg_id) errors.iscatg_id = t('Please select a sub category.')
    if (!form.item_name) errors.item_name = t('The name field is required.')
    if (!form.item_title) errors.item_title = t('The title field is required.')
    if (!form.item_code) errors.item_code = t('The code field is required.')
    if (Object.keys(errors).length) return

    isSubmitting.value = true

    try {
        const { data } = editingId.value
            ? await axios.put(`/api/asset/item-names/${editingId.value}`, form)
            : await axios.post('/api/asset/item-names', form)

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
        `/api/asset/item-names/${item.item_id}/status`,
        item.item_status,
        item.item_name,
    )
    if (changed) fetchData()
}

onMounted(() => {
    fetchCategories()
    fetchData()
})

</script>
