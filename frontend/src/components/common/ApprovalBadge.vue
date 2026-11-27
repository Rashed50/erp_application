<template>
    <!-- approved_at decides: records approved by the system have no approver. -->
    <span v-if="approvedAt" class="badge bg-success" :title="$t('approval.approvedAt', { date: approvedAt })">
        {{ $t('approval.approved') }}
    </span>
    <span v-else class="badge bg-warning text-dark">{{ $t('approval.pending') }}</span>
    <small v-if="approvedAt && approvedByName" class="d-block text-muted">{{ $t('approval.by', { name: approvedByName }) }}</small>
</template>
<script setup>
defineProps({
    approvedBy: { type: [Number, String], default: null },
    approvedAt: { type: String, default: null },
    approvedByName: { type: String, default: null },
})
</script>
