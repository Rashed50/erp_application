<template lang="html">
    <Breadcrumb title="Products" />

    <div class="main-content-wrapper mt-4">
        <div class="container-fluid">

            <!-- Table Card -->
            <v-card style="padding: 5px; margin-top: 15px;">
                <div class="row">
                    <div class="col-md-12 mb-2">
                        <div class="row align-items-center">
                            <div class="col-md-5">
                                <v-btn v-if="can(['products.create'])" type="button" @click="openAddDialog"
                                    class="text-none text-white" color="success" rounded="0" variant="flat">
                                    <i class="fa-solid fa-plus me-2"></i> Add Product
                                </v-btn>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" v-model="filters.active_status">
                                    <option value="">All Status</option>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <div class="search-wrapper d-flex align-center gap-2">
                                    <v-text-field variant="outlined" density="compact" placeholder="Search..."
                                        v-model="filters.search" hide-details class="flex-grow-1"></v-text-field>
                                    <v-btn type="button" @click="fetchData" class="text-none text-white"
                                        color="blue-darken-3" rounded="0" variant="flat" min-width="100">
                                        Search
                                    </v-btn>
                                    <v-btn @click.prevent="resetFilters" class="text-none" color="grey-lighten-3"
                                        rounded="0" variant="flat" min-width="100">
                                        Reload
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
                                <th class="text-left">Code</th>
                                <th class="text-left">Name</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>

                            <tr v-if="loading">
                                <td colspan="5" class="text-center py-4">
                                    <v-progress-linear indeterminate color="primary" size="30"></v-progress-linear>
                                    Loading...
                                </td>
                            </tr>

                            <tr v-else-if="!items.length">
                                <td colspan="5" class="text-center py-4">
                                    No records found.
                                </td>
                            </tr>

                            <tr v-else v-for="(item, index) in items" :key="item.id">
                                <td>{{ (pagination.page - 1) * pagination.perPage + index + 1 }}</td>
                                <td>{{ item.code }}</td>
                                <td>{{ item.name }}</td>
                                <td class="text-center">
                                    <span :class="item.active_status ? 'badge bg-success' : 'badge bg-secondary'">
                                        {{ item.active_status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <v-menu v-if="can(['products.update', 'products.delete'])">
                                        <template v-slot:activator="{ props }">
                                            <button type="button" class="table-action-button" v-bind="props">
                                                <i class="fa-solid fa-bars"></i>
                                            </button>
                                        </template>
                                        <ul class="table-action-menu">
                                            <li class="menu-item" v-if="can(['products.update'])">
                                                <button type="button" class="menu-link" @click="openEditDialog(item)">
                                                    Edit
                                                </button>
                                            </li>
                                            <li class="menu-item" v-if="can(['products.delete'])">
                                                <button type="button" class="menu-link" @click="handleDelete(item)">
                                                    Delete
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

    <!-- Add / Edit Product Dialog -->
    <v-dialog v-model="dialog" max-width="500">
        <v-card>
            <v-card-title>{{ editingId ? 'Update Product' : 'Add Product' }}</v-card-title>
            <v-card-text>
                <form @submit.prevent="handleSubmit">
                    <div class="form-group mb-3">
                        <label for="product_name">Name: <span class="text-danger">*</span></label>
                        <input type="text" id="product_name" class="form-control" v-model="form.name" required />
                        <div v-if="errors.name" class="error-msg">{{ errors.name }}</div>
                    </div>
                    <div class="form-group mb-3">
                        <label for="product_code">Code:</label>
                        <input type="text" id="product_code" class="form-control" v-model="form.code"
                            :placeholder="editingId ? '' : 'Leave blank to auto-generate'" :required="!!editingId" />
                        <div v-if="errors.code" class="error-msg">{{ errors.code }}</div>
                    </div>
                    <div class="form-group mb-3">
                        <label for="product_status">Status:</label>
                        <select id="product_status" class="form-select" v-model="form.active_status">
                            <option :value="true">Active</option>
                            <option :value="false">Inactive</option>
                        </select>
                    </div>
                </form>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn class="text-none" color="grey-lighten-3" rounded="0" variant="flat" @click="dialog = false">
                    Cancel
                </v-btn>
                <v-btn class="text-none text-white" color="blue-darken-4" rounded="0" variant="flat"
                    :disabled="isSubmitting" :loading="isSubmitting" @click="handleSubmit">
                    {{ editingId ? 'Update' : 'Save' }}
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>

</template>
<script setup>
import axios from 'axios';
import Swal from 'sweetalert2';
import { toast } from 'vue3-toastify';
import BasePagination from '@/components/common/BasePagination.vue';
import Breadcrumb from '@/components/common/Breadcrumb.vue';
import { usePermission } from '@/composables/usePermission';
import { usePaginatedFetch } from '@/composables/usePaginatedFetch';

const {
    items,
    loading,
    filters,
    pagination,
    fetchData,
    changePage,
    changePerPage,
    resetFilters,
} = usePaginatedFetch('/api/products', {
    search: '',
    active_status: '',
})

const { can } = usePermission()

const initialForm = () => ({
    name: '',
    code: '',
    active_status: true,
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
    editingId.value = item.id
    Object.assign(form, { name: item.name, code: item.code, active_status: item.active_status })
    clearErrors()
    dialog.value = true
}

const handleSubmit = async () => {
    if (!form.name) {
        errors.name = 'The name field is required.'
        return
    }

    isSubmitting.value = true
    clearErrors()

    try {
        const payload = { ...form }
        if (!payload.code) delete payload.code

        const { data } = editingId.value
            ? await axios.put(`/api/products/${editingId.value}`, payload)
            : await axios.post('/api/products', payload)

        if (data.success) {
            toast.success(data.message)
            dialog.value = false
            fetchData()
        }
    } catch (err) {
        if (err.response?.status === 422) {
            const respErrors = err.response.data.data
            for (const key in respErrors) {
                errors[key] = respErrors[key].join(' ')
            }
        } else {
            toast.error(err.response?.data?.message || 'An error occurred while saving.')
        }
    } finally {
        isSubmitting.value = false
    }
}

const handleDelete = async (item) => {
    const result = await Swal.fire({
        title: 'Are you sure?',
        text: `Delete product "${item.name}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete',
        cancelButtonText: 'Cancel',
    })

    if (!result.isConfirmed) return

    try {
        const resp = await axios.delete(`/api/products/${item.id}`)
        if (resp.data.success) {
            toast.success(resp.data.message)
            fetchData()
        }
    } catch (e) {
        toast.error(e.response?.data?.message || 'Failed to delete product.')
    }
}

onMounted(() => {
    fetchData()
})

</script>
