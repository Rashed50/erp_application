<template>
    <!-- Who entered a record and when, for the "Created By" column of every list. -->
    <span>{{ name || fallback }}</span>
    <small v-if="at" class="d-block text-muted text-nowrap">{{ formatDateTime(at) }}</small>
</template>
<script setup>
import { formatDateTime } from '@/helpers/formatDateTime'

defineProps({
    name: { type: String, default: null },
    at: { type: String, default: null },
    fallback: { type: String, default: '-' },
})
</script>
