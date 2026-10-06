<template>
    <div class="search-select-wrapper">
        <v-autocomplete v-model="selected" :items="items" :item-title="itemTitle" :item-value="itemValue"
            :placeholder="placeholder" :disabled="disabled" :loading="loading" :clearable="clearable"
            :no-data-text="$t('No matching options')" :id="id" density="compact" variant="outlined" hide-details
            auto-select-first class="search-select" :class="{ 'is-invalid': !!error }" />
        <button v-if="addable" type="button" class="btn btn-outline-success search-select-add"
            :title="addTitle || $t('Add New')" :disabled="addDisabled" @click="emit('add')">
            <i class="fa-solid fa-plus"></i>
        </button>
    </div>
</template>

<script setup>
// A dropdown you can type into to filter the options. Takes plain strings
// or objects (`itemTitle` / `itemValue` pick the label and value fields).
// Clearing it sets the model back to `emptyValue` (default '') so filters
// and forms see the same "nothing selected" value as a native <select>.
// With `addable`, a "+" button beside it emits `add` so the parent can open
// a dialog to create a missing option.
const props = defineProps({
    items: { type: Array, default: () => [] },
    itemTitle: { type: [String, Function], default: 'name' },
    itemValue: { type: [String, Function], default: 'id' },
    placeholder: { type: String, default: '' },
    disabled: { type: Boolean, default: false },
    loading: { type: Boolean, default: false },
    clearable: { type: Boolean, default: true },
    error: { type: String, default: '' },
    emptyValue: { default: '' },
    id: { type: String, default: undefined },
    addable: { type: Boolean, default: false },
    addTitle: { type: String, default: '' },
    addDisabled: { type: Boolean, default: false },
})

const emit = defineEmits(['add'])

const model = defineModel({ default: '' })

const selected = computed({
    get: () => (model.value === '' || model.value === undefined ? null : model.value),
    set: (value) => {
        model.value = value === null || value === undefined ? props.emptyValue : value
    },
})
</script>

<style scoped>
.search-select-wrapper {
    display: flex;
    align-items: stretch;
    gap: 6px;
}

.search-select {
    flex: 1 1 auto;
    min-width: 0;
}

.search-select-add {
    flex: 0 0 auto;
    width: 40px;
    padding: 0;
}

/* Match the height of the Bootstrap .form-control inputs around it. */
.search-select :deep(.v-field) {
    border-radius: 0.375rem;
    background: #fff;
}

.search-select :deep(.v-field__input) {
    min-height: 38px;
    padding-top: 6px;
    padding-bottom: 6px;
}

.search-select.is-invalid :deep(.v-field__outline) {
    color: #dc3545;
}
</style>
