<template lang="html">
    <Breadcrumb title="Permissions" :buttons="[
        { text: 'Roles', link: { name: 'admin_roles' }, icon: 'fa-solid fa-user-shield' }
    ]" />

    <div class="main-content-wrapper mt-4">
        <div class="container-fluid">

            <v-card style="padding: 5px; margin-top: 15px;">
                <div class="row align-items-center mb-2">
                    <div class="col-md-6">
                        <v-btn v-if="can(['permissions.create'])" type="button" @click="openCategoryDialog()"
                            class="text-none text-white" color="success" rounded="0" variant="flat">
                            <i class="fa-solid fa-plus me-2"></i> {{ $t('Add Category') }}
                        </v-btn>
                    </div>
                    <div class="col-md-6">
                        <v-text-field variant="outlined" density="compact" :placeholder="$t('Search...')"
                            v-model="search" hide-details></v-text-field>
                    </div>
                </div>

                <div v-if="loading" class="py-4 text-center">
                    <v-progress-linear indeterminate color="primary"></v-progress-linear>
                    {{ $t('Loading...') }}
                </div>

                <div v-else-if="!filteredGroups.length" class="py-4 text-center">
                    {{ $t('No records found.') }}
                </div>

                <div v-else class="row">
                    <div v-for="group in filteredGroups" :key="group.id ?? 'uncategorized'" class="col-md-4 mb-3">
                        <div class="permission-category h-100">
                            <div class="permission-category__header">
                                <span class="fw-bold">
                                    {{ $t(group.name) }}
                                    <span class="badge bg-secondary ms-1">{{ group.permissions.length }}</span>
                                </span>
                                <span v-if="group.id" class="d-flex gap-1">
                                    <button v-if="can(['permissions.update'])" type="button"
                                        class="table-action-button" :title="$t('Rename')"
                                        @click="openCategoryDialog(group)">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                    <button v-if="can(['permissions.create'])" type="button"
                                        class="table-action-button" :title="$t('Add Permission')"
                                        @click="openPermissionDialog(group)">
                                        <i class="fa-solid fa-plus"></i>
                                    </button>
                                </span>
                            </div>
                            <ul class="permission-category__list">
                                <li v-for="permission in group.permissions" :key="permission.id">
                                    <i class="fa-solid fa-key me-2 text-muted"></i>{{ permission.name }}
                                </li>
                                <li v-if="!group.permissions.length" class="text-muted">
                                    {{ $t('No permissions yet.') }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </v-card>

        </div>
    </div>

    <!-- Add / Rename Category Dialog -->
    <v-dialog v-model="categoryDialog" max-width="500">
        <v-card>
            <v-card-title>{{ $t(categoryForm.id ? 'Rename Category' : 'Add Category') }}</v-card-title>
            <v-card-text>
                <form @submit.prevent="submitCategory">
                    <div class="form-group mb-3">
                        <label for="permission_category_name">{{ $t('Category Name:') }} <span
                                class="text-danger">*</span></label>
                        <input type="text" id="permission_category_name" class="form-control"
                            v-model="categoryForm.name" maxlength="100" required />
                        <div v-if="categoryErrors.name" class="error-msg">{{ categoryErrors.name }}</div>
                    </div>
                </form>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn class="text-none" color="grey-lighten-3" rounded="0" variant="flat"
                    @click="categoryDialog = false">
                    {{ $t('Cancel') }}
                </v-btn>
                <v-btn class="text-none text-white" color="blue-darken-4" rounded="0" variant="flat"
                    :disabled="isSubmitting" :loading="isSubmitting" @click="submitCategory">
                    {{ $t(categoryForm.id ? 'Update' : 'Save') }}
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>

    <!-- Add Permission Dialog -->
    <v-dialog v-model="permissionDialog" max-width="500">
        <v-card>
            <v-card-title>{{ $t('Add Permission') }}</v-card-title>
            <v-card-text>
                <form @submit.prevent="submitPermission">
                    <div class="form-group mb-3">
                        <label for="permission_category">{{ $t('Category:') }} <span
                                class="text-danger">*</span></label>
                        <select id="permission_category" class="form-select" v-model="permissionForm.categoryId"
                            required>
                            <option v-for="category in categories" :key="category.id" :value="category.id">
                                {{ $t(category.name) }}
                            </option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="permission_name">{{ $t('Permission Name:') }} <span
                                class="text-danger">*</span></label>
                        <input type="text" id="permission_name" class="form-control" v-model="permissionForm.name"
                            maxlength="125" placeholder="module.action" required />
                        <small class="text-muted">{{ $t('e.g. users.view, sales.approve') }}</small>
                        <div v-if="permissionErrors.name" class="error-msg">{{ permissionErrors.name }}</div>
                    </div>
                </form>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn class="text-none" color="grey-lighten-3" rounded="0" variant="flat"
                    @click="permissionDialog = false">
                    {{ $t('Cancel') }}
                </v-btn>
                <v-btn class="text-none text-white" color="blue-darken-4" rounded="0" variant="flat"
                    :disabled="isSubmitting" :loading="isSubmitting" @click="submitPermission">
                    {{ $t('Save') }}
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>

</template>
<script setup>
import axios from 'axios';
import { toast } from 'vue3-toastify';
import Breadcrumb from '@/components/common/Breadcrumb.vue';
import { usePermission } from '@/composables/usePermission';
import { t } from '@/i18n';

const { can } = usePermission()

const categories = ref([])
const uncategorized = ref([])
const loading = ref(false)
const search = ref('')
const isSubmitting = ref(false)

const fetchData = async () => {
    loading.value = true
    try {
        const { data } = await axios.get('/api/permission-categories')
        categories.value = data.data?.categories ?? []
        uncategorized.value = data.data?.uncategorized ?? []
    } catch (err) {
        toast.error(err.response?.data?.message || t('common.failedToLoad'))
    } finally {
        loading.value = false
    }
}

// Categories (plus an "Uncategorized" group when needed), narrowed by the search text.
const filteredGroups = computed(() => {
    const groups = categories.value.map(c => ({ ...c, permissions: c.permissions ?? [] }))
    if (uncategorized.value.length) {
        groups.push({ id: null, name: 'Uncategorized', permissions: uncategorized.value })
    }

    const term = search.value.trim().toLowerCase()
    if (!term) return groups

    return groups
        .map(group => group.name.toLowerCase().includes(term)
            ? group
            : { ...group, permissions: group.permissions.filter(p => p.name.includes(term)) })
        .filter(group => group.name.toLowerCase().includes(term) || group.permissions.length)
})

const applyValidationErrors = (err, errors) => {
    if (err.response?.status === 422) {
        const respErrors = err.response.data.data || {}
        for (const key in respErrors) {
            errors[key] = respErrors[key].join(' ')
        }
        return
    }
    toast.error(err.response?.data?.message || t('An error occurred while saving.'))
}

const clear = (errors) => {
    for (const key in errors) delete errors[key]
}

/* ---------- category ---------- */
const categoryDialog = ref(false)
const categoryForm = reactive({ id: null, name: '' })
const categoryErrors = reactive({})

const openCategoryDialog = (category = null) => {
    Object.assign(categoryForm, { id: category?.id ?? null, name: category?.name ?? '' })
    clear(categoryErrors)
    categoryDialog.value = true
}

const submitCategory = async () => {
    clear(categoryErrors)
    if (!categoryForm.name.trim()) {
        categoryErrors.name = t('The name field is required.')
        return
    }

    isSubmitting.value = true
    try {
        const payload = { name: categoryForm.name.trim() }
        const { data } = categoryForm.id
            ? await axios.put(`/api/permission-categories/${categoryForm.id}`, payload)
            : await axios.post('/api/permission-categories', payload)

        if (data.success) {
            toast.success(data.message)
            categoryDialog.value = false
            fetchData()
        }
    } catch (err) {
        applyValidationErrors(err, categoryErrors)
    } finally {
        isSubmitting.value = false
    }
}

/* ---------- permission ---------- */
const permissionDialog = ref(false)
const permissionForm = reactive({ categoryId: null, name: '' })
const permissionErrors = reactive({})

const openPermissionDialog = (category) => {
    Object.assign(permissionForm, { categoryId: category.id, name: '' })
    clear(permissionErrors)
    permissionDialog.value = true
}

const submitPermission = async () => {
    clear(permissionErrors)
    if (!permissionForm.name.trim()) {
        permissionErrors.name = t('The name field is required.')
        return
    }

    isSubmitting.value = true
    try {
        const { data } = await axios.post(
            `/api/permission-categories/${permissionForm.categoryId}/permissions`,
            { name: permissionForm.name },
        )

        if (data.success) {
            toast.success(data.message)
            permissionDialog.value = false
            fetchData()
        }
    } catch (err) {
        applyValidationErrors(err, permissionErrors)
    } finally {
        isSubmitting.value = false
    }
}

onMounted(() => {
    fetchData()
})

</script>

<style scoped>
.permission-category {
    border: 1px solid #e0e0e0;
    border-radius: 4px;
}

.permission-category__header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 12px;
    background: #f5f7fa;
    border-bottom: 1px solid #e0e0e0;
    color: #1976d2;
}

.permission-category__list {
    list-style: none;
    margin: 0;
    padding: 8px 12px;
}

.permission-category__list li {
    padding: 3px 0;
    font-size: 14px;
}
</style>
