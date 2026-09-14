<template lang="html">
    <Breadcrumb title="Create New Role" buttonText="Back Roles" :buttonLink="{ name: 'admin_roles' }"
        buttonIcon="list" />

    <div class="main-content-wrapper mt-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-2"></div>
                <div class="col-md-8">
                    <!-- Table Card -->
                    <v-card style="padding: 5px; margin: 15px 0px;">
                        <form @submit.prevent="handleSubmit">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="">Role Name:</label>
                                        <input type="text" class="form-control" v-model="form.name" required>
                                        <div v-if="errors.name" class="error-msg">{{ errors.name }}</div>
                                    </div>

                                    <div class="form-group mt-4">
                                        <label>Assign Permissions:</label>
                                        <div class="row">
                                            <div v-for="(groupPermissions, groupName) in groupedPermissions"
                                                :key="groupName" class="mb-3 col-md-4">
                                                <div class="d-flex align-items-center mb-1">
                                                    <input type="checkbox" :id="groupName + '-group'"
                                                        :checked="isGroupSelected(groupName)"
                                                        @change="toggleGroup(groupName, $event.target.checked)">
                                                    <label :for="groupName + '-group'"
                                                        class="ms-2 fw-bold text-primary">{{
                                                            groupName }}</label>
                                                </div>

                                                <div class="ms-4">
                                                    <div v-for="perm in groupPermissions" :key="perm.id"
                                                        class="form-check">
                                                        <input type="checkbox" class="form-check-input"
                                                            :id="'perm-' + perm.id" :value="perm.name"
                                                            v-model="form.permissions">
                                                        <label :for="'perm-' + perm.id" class="form-check-label">{{
                                                            perm.name
                                                            }}</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div v-if="errors.permissions" class="error-msg">{{ errors.permissions }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-12">
                                    <v-btn type="submit" class="text-none text-white mr-2" color="blue-darken-4"
                                        rounded="0" variant="flat" :disabled="isSubmitting" :loading="isSubmitting">
                                        Submit
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
import Breadcrumb from '@/components/common/Breadcrumb.vue';
import { useFetch } from '@/composables/useFetch';
import { setToast } from "@/helpers/toast";
import { useRouter } from 'vue-router';


const router = useRouter();
// store data property
const form = reactive({
    name: '',
    permissions: [],
});
const errors = reactive({});
const isSubmitting = ref(false);

const clearErrors = () => {
    for (const key in errors) {
        delete errors[key];
    }
};


// Submit updated role - new implementation
const handleSubmit = async () => {
    isSubmitting.value = true;
    clearErrors();
    try {
        const resp = await axios.post('/api/roles', form);
        if (resp.data && resp.data.success) {
            setToast('success', resp.data.message);
            router.push({ name: 'admin_roles' });
        }
    } catch (e) {
        if (e.response && e.response.status === 422) {
            const respErrors = e.response.data.data;
            if (respErrors) {
                for (const key in respErrors) {
                    errors[key] = respErrors[key].join(' ');
                }
            }
        } else {
            setToast('error', e.response?.data?.message || 'An unexpected error occurred.');
            console.error(e);
        }
    } finally {
        isSubmitting.value = false;
    }
}

// fetch data property
const { items, fetchData } = useFetch('/api/permissions')

onMounted(() => {
    fetchData()
})


const groupedPermissions = computed(() => {
    const groups = {}

    items.value.forEach(p => {
        // permission names are "group.action" (e.g. "users.view")
        const [group] = p.name.split('.')

        if (!groups[group]) groups[group] = []
        groups[group].push(p)
    })

    return groups
})



// Check if all permissions in group are selected
const isGroupSelected = (groupName) => {
    const names = groupedPermissions.value[groupName].map(p => p.name)
    return names.every(name => form.permissions.includes(name))
}

// Toggle group selection
const toggleGroup = (groupName, checked) => {
    const names = groupedPermissions.value[groupName].map(p => p.name)
    if (checked) {
        names.forEach(name => {
            if (!form.permissions.includes(name)) form.permissions.push(name)
        })
    } else {
        form.permissions = form.permissions.filter(name => !names.includes(name))
    }
}

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
