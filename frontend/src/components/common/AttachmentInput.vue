<template>
    <div class="form-group mb-3">
        <label for="attachment">{{ $t('Attachment') }}</label>
        <input type="file" id="attachment" ref="fileInput" class="form-control"
            accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx" @change="handleChange" />
        <small class="text-muted d-block">{{ $t('JPG, PNG, PDF, Word or Excel, max 20MB.') }}</small>
        <div v-if="currentUrl && !modelValue && !remove" class="d-flex align-items-center mt-1">
            <a :href="currentUrl" target="_blank" class="me-2">
                <i class="fa-solid fa-paperclip me-1"></i>{{ $t('View current attachment') }}
            </a>
            <v-btn size="small" variant="text" color="error" class="text-none" @click="emit('update:remove', 1)">
                {{ $t('Remove attachment') }}
            </v-btn>
        </div>
        <div v-if="error" class="error-msg">{{ error }}</div>
    </div>
</template>

<script setup>
// An invoice/voucher file. v-model holds the newly chosen File; on an edit form,
// `currentUrl` links the saved file and v-model:remove (0/1) asks the backend to delete it.
defineProps({
    modelValue: { type: File, default: null },
    currentUrl: { type: String, default: '' },
    remove: { type: Number, default: 0 },
    error: { type: String, default: '' },
})

const emit = defineEmits(['update:modelValue', 'update:remove'])

const handleChange = (event) => {
    emit('update:modelValue', event.target.files[0] ?? null)
    emit('update:remove', 0)
}
</script>
