<template>
    <span v-if="approvedBy" class="badge bg-success" :title="approvedAt ? `Approved at ${approvedAt}` : ''">
        Approved
    </span>
    <span v-else class="badge bg-warning text-dark">Pending</span>
</template>
<script setup>
defineProps({
    approvedBy: { type: [Number, String], default: null },
    approvedAt: { type: String, default: null },
})
</script>
