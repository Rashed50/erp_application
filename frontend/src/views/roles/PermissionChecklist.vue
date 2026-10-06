<template>
    <div class="row">
        <div v-if="loading" class="col-md-12 py-3">
            <v-progress-linear indeterminate color="primary"></v-progress-linear>
        </div>

        <div v-for="group in groups" :key="group.key" class="mb-3 col-md-4">
            <div class="d-flex align-items-center mb-1">
                <input type="checkbox" :id="group.key + '-group'" :checked="isGroupSelected(group)"
                    :disabled="!group.permissions.length" @change="toggleGroup(group, $event.target.checked)">
                <label :for="group.key + '-group'" class="ms-2 fw-bold text-primary">{{ $t(group.name) }}</label>
            </div>

            <div class="ms-4">
                <div v-for="perm in group.permissions" :key="perm.id" class="form-check">
                    <input type="checkbox" class="form-check-input" :id="'perm-' + perm.id" :value="perm.name"
                        v-model="selected">
                    <label :for="'perm-' + perm.id" class="form-check-label">{{ perm.name }}</label>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import axios from 'axios';

// Permissions grouped by their category (permission_categories), with a
// trailing "Uncategorized" group for any permission not yet in one.
const selected = defineModel({ type: Array, default: () => [] })

const categories = ref([])
const uncategorized = ref([])
const loading = ref(false)

const groups = computed(() => {
    const list = categories.value.map(category => ({
        key: `category-${category.id}`,
        name: category.name,
        permissions: category.permissions ?? [],
    }))

    if (uncategorized.value.length) {
        list.push({ key: 'uncategorized', name: 'Uncategorized', permissions: uncategorized.value })
    }

    return list
})

const fetchCategories = async () => {
    loading.value = true
    try {
        const { data } = await axios.get('/api/permission-categories')
        categories.value = data.data?.categories ?? []
        uncategorized.value = data.data?.uncategorized ?? []
    } catch (e) {
        console.error(e)
    } finally {
        loading.value = false
    }
}

const isGroupSelected = (group) =>
    group.permissions.length > 0 && group.permissions.every(p => selected.value.includes(p.name))

const toggleGroup = (group, checked) => {
    const names = group.permissions.map(p => p.name)
    selected.value = checked
        ? [...new Set([...selected.value, ...names])]
        : selected.value.filter(name => !names.includes(name))
}

onMounted(() => {
    fetchCategories()
})
</script>

<style scoped>
.ms-2 {
    margin-left: 8px;
}

.ms-4 {
    margin-left: 16px;
}

.fw-bold {
    font-weight: 600;
}

.text-primary {
    color: #1976d2;
}
</style>
