<template>
    <v-dialog v-model="open" max-width="450">
        <v-card>
            <v-card-title>{{ title }}</v-card-title>
            <v-card-text>
                <form @submit.prevent="save">
                    <div v-if="parentLabel" class="mb-3 text-muted small">{{ parentLabel }}</div>
                    <div class="form-group mb-3">
                        <label>{{ $t('Name:') }} <span class="text-danger">*</span></label>
                        <input ref="nameInput" type="text" class="form-control" v-model="form.name" maxlength="255" />
                        <div v-if="errors.name" class="error-msg">{{ errors.name }}</div>
                    </div>
                    <div v-if="withBanglaName" class="form-group mb-3">
                        <label>{{ $t('Name (Bangla):') }}</label>
                        <input type="text" class="form-control" v-model="form.bn_name" maxlength="255" />
                        <div v-if="errors.bn_name" class="error-msg">{{ errors.bn_name }}</div>
                    </div>
                    <div v-if="withDescription" class="form-group mb-3">
                        <label>{{ $t('Description:') }}</label>
                        <textarea class="form-control" rows="2" maxlength="255" v-model="form.description"></textarea>
                        <div v-if="errors.description" class="error-msg">{{ errors.description }}</div>
                    </div>
                    <div v-for="(message, key) in otherErrors" :key="key" class="error-msg">{{ message }}</div>
                </form>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn class="text-none" color="grey-lighten-3" rounded="0" variant="flat" @click="open = false">
                    {{ $t('Cancel') }}
                </v-btn>
                <v-btn class="text-none text-white" color="blue-darken-4" rounded="0" variant="flat"
                    :disabled="saving" :loading="saving" @click="save">
                    {{ $t('Save') }}
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script setup>
import axios from 'axios';
import { toast } from 'vue3-toastify';
import { t } from '@/i18n';
import { fillValidationErrors } from '@/views/asset/helpers';

// A small "add new" dialog for a dropdown's missing option: posts the name
// (plus `payload`, e.g. the parent id) to `endpoint` and emits `created`
// with the saved record so the caller can add it to the list and select it.
const props = defineProps({
    title: { type: String, required: true },
    endpoint: { type: String, required: true },
    payload: { type: Object, default: () => ({}) },
    parentLabel: { type: String, default: '' },
    withBanglaName: { type: Boolean, default: false },
    withDescription: { type: Boolean, default: false },
})

const emit = defineEmits(['created'])

const open = defineModel({ type: Boolean, default: false })

const form = reactive({ name: '', bn_name: '', description: '' })
const errors = reactive({})
const saving = ref(false)
const nameInput = ref(null)

const FORM_FIELDS = ['name', 'bn_name', 'description']
const otherErrors = computed(() =>
    Object.fromEntries(Object.entries(errors).filter(([key]) => !FORM_FIELDS.includes(key))),
)

const clearErrors = () => {
    for (const key in errors) delete errors[key]
}

watch(open, async (isOpen) => {
    if (!isOpen) return
    Object.assign(form, { name: '', bn_name: '', description: '' })
    clearErrors()
    await nextTick()
    nameInput.value?.focus()
})

const save = async () => {
    clearErrors()
    if (!form.name.trim()) {
        errors.name = t('The name field is required.')
        return
    }

    const body = { ...props.payload, name: form.name.trim() }
    if (props.withBanglaName) body.bn_name = form.bn_name
    if (props.withDescription) body.description = form.description

    saving.value = true
    try {
        const { data } = await axios.post(props.endpoint, body)
        if (data.success) {
            toast.success(data.message)
            emit('created', data.data)
            open.value = false
        }
    } catch (err) {
        fillValidationErrors(err, errors)
    } finally {
        saving.value = false
    }
}
</script>
