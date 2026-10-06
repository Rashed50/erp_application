<template>
    <div class="row">
        <div :class="colClass">
            <div class="form-group mb-3">
                <label>{{ $t('Division:') }}</label>
                <SearchSelect v-model="divisionId" :items="divisions" :item-title="locationName"
                    :placeholder="$t('Select Division')" :error="errors.division"
                    :addable="canAdd" :add-title="$t('Add Division')" @add="openAdd('division')" />
                <div v-if="errors.division" class="error-msg">{{ errors.division }}</div>
            </div>
        </div>
        <div :class="colClass">
            <div class="form-group mb-3">
                <label>{{ $t('District:') }}</label>
                <SearchSelect v-model="districtId" :items="districts" :item-title="locationName"
                    :loading="loadingDistricts" :disabled="!divisionId"
                    :placeholder="divisionId ? $t('Select District') : $t('Select a division first')"
                    :error="errors.district" :addable="canAdd" :add-disabled="!divisionId"
                    :add-title="$t('Add District')" @add="openAdd('district')" />
                <div v-if="errors.district" class="error-msg">{{ errors.district }}</div>
            </div>
        </div>
        <div :class="colClass">
            <div class="form-group mb-3">
                <label>{{ $t('Thana / Upazila:') }}</label>
                <SearchSelect v-model="upazilaId" :items="upazilas" :item-title="locationName"
                    :loading="loadingUpazilas" :disabled="!districtId"
                    :placeholder="districtId ? $t('Select Thana') : $t('Select a district first')"
                    :error="errors.upazila" :addable="canAdd" :add-disabled="!districtId"
                    :add-title="$t('Add Thana')" @add="openAdd('upazila')" />
                <div v-if="errors.upazila" class="error-msg">{{ errors.upazila }}</div>
            </div>
        </div>
    </div>

    <QuickAddDialog v-if="adding" v-model="addDialog" :title="$t(adding.title)" :endpoint="adding.endpoint"
        :payload="adding.payload" :parent-label="adding.parentLabel" with-bangla-name @created="onCreated" />
</template>

<script>
import axios from 'axios';
import { ref } from 'vue';

// Divisions rarely change, so every picker in the app shares one list and
// one request; a division added from any picker shows up in all of them.
const sharedDivisions = ref([])
let divisionsRequest = null
const loadDivisions = () => {
    divisionsRequest ??= axios.get('/api/locations/divisions')
        .then(({ data }) => { sharedDivisions.value = data.data ?? [] })
        .catch((e) => {
            divisionsRequest = null // let the next picker retry
            console.error(e)
        })
    return divisionsRequest
}
</script>

<script setup>
import { locale, t } from '@/i18n';
import { usePermission } from '@/composables/usePermission';
import QuickAddDialog from '@/components/common/QuickAddDialog.vue';
import SearchSelect from '@/components/common/SearchSelect.vue';

// Cascading division → district → thana (upazila) pickers. Each list loads
// when its parent changes, and a child that does not belong to the new
// parent is cleared. Users with `locations.create` get a "+" beside each
// picker to add a missing entry, which is then selected.
defineProps({
    errors: { type: Object, default: () => ({}) },
    colClass: { type: String, default: 'col-md-4' },
})

const divisionId = defineModel('division', { default: '' })
const districtId = defineModel('district', { default: '' })
const upazilaId = defineModel('upazila', { default: '' })

const { can } = usePermission()
const canAdd = computed(() => can(['locations.create']))

const divisions = sharedDivisions
const districts = ref([])
const upazilas = ref([])
const loadingDistricts = ref(false)
const loadingUpazilas = ref(false)

const locationName = (item) => (locale.value === 'bn' && item.bn_name ? item.bn_name : item.name)
const nameOf = (list, id) => {
    const item = list.find((row) => row.id === id)
    return item ? locationName(item) : ''
}

const loadChildren = async (url, params, list, loading, childModel) => {
    list.value = []
    if (!Object.values(params)[0]) {
        childModel.value = ''
        return
    }

    loading.value = true
    try {
        const { data } = await axios.get(url, { params })
        list.value = data.data ?? []
        if (childModel.value && !list.value.some((item) => item.id === childModel.value)) {
            childModel.value = ''
        }
    } catch (e) {
        console.error(e)
    } finally {
        loading.value = false
    }
}

watch(divisionId, (id) => loadChildren('/api/locations/districts', { division_id: id }, districts, loadingDistricts, districtId), { immediate: true })
watch(districtId, (id) => loadChildren('/api/locations/upazilas', { district_id: id }, upazilas, loadingUpazilas, upazilaId), { immediate: true })

onMounted(loadDivisions)

// Quick add
const addDialog = ref(false)
const adding = ref(null)

const openAdd = (level) => {
    adding.value = {
        division: {
            level,
            title: 'Add Division',
            endpoint: '/api/locations/divisions',
            payload: {},
            parentLabel: '',
        },
        district: {
            level,
            title: 'Add District',
            endpoint: '/api/locations/districts',
            payload: { division_id: divisionId.value },
            parentLabel: t('Division: {name}', { name: nameOf(divisions.value, divisionId.value) }),
        },
        upazila: {
            level,
            title: 'Add Thana',
            endpoint: '/api/locations/upazilas',
            payload: { district_id: districtId.value },
            parentLabel: t('District: {name}', { name: nameOf(districts.value, districtId.value) }),
        },
    }[level]
    addDialog.value = true
}

const byName = (a, b) => a.name.localeCompare(b.name)

const onCreated = (record) => {
    const level = adding.value?.level
    if (level === 'division') {
        sharedDivisions.value = [...sharedDivisions.value, record].sort(byName)
        divisionId.value = record.id
    } else if (level === 'district') {
        districts.value = [...districts.value, record].sort(byName)
        districtId.value = record.id
    } else if (level === 'upazila') {
        upazilas.value = [...upazilas.value, record].sort(byName)
        upazilaId.value = record.id
    }
}
</script>
