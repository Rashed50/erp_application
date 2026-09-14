<template>
    <div class="d-flex align-items-center gap-3">
        রেকর্ড দেখাও :
        <select class="form-select form-select-sm w-auto" :value="perPage" @change="changePerPage($event.target.value)">
            <option v-for="size in perPageOptions" :key="size" :value="size">
                {{ size }}
            </option>
        </select>
    </div>
</template>

<script setup>
const props = defineProps({
    perPage: { type: Number, required: true }
})

const emit = defineEmits(['update:perPage'])

const perPageOptions = [15, 25, 50, 100, 1000, 2000, 3000, 5000, 'All']

const changePerPage = (value) => {
    emit('update:perPage', value === 'All' ? 'All' : Number(value))
}
</script>
