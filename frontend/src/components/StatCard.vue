<template>
  <v-card class="summary-card h-100" flat>
    <v-card-text>
      <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
          <div class="text-muted small">{{ title }}</div>
          <div class="h4 fw-bold mb-0 mt-2">{{ value }}</div>
        </div>
        <div class="summary-icon">
          <i :class="icon"></i>
        </div>
      </div>
      <div class="d-flex align-items-center gap-2 text-success small fw-semibold">
        <i :class="trendIcon"></i>
        {{ change }}
      </div>
    </v-card-text>
  </v-card>
</template>

<script setup>
const props = defineProps({
  title: { type: String, required: true },
  value: { type: String, required: true },
  change: { type: String, required: true },
  icon: { type: String, required: true },
  trendIcon: { type: String, required: true },
})
</script>
