<template>
    <span v-if="approvedBy" class="badge bg-success" :title="approvedAt ? $t('approval.approvedAt', { date: approvedAt }) : ''">
        {{ $t('approval.approved') }}
    </span>
    <span v-else class="badge bg-warning text-dark">{{ $t('approval.pending') }}</span>
</template>
<script setup>
defineProps({
    approvedBy: { type: [Number, String], default: null },
    approvedAt: { type: String, default: null },
})
</script>
