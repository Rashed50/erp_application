<template lang="html">
    <v-card elevation="2" class="rounded-lg overflow-hidden">
        <div class="bg-blue-darken-4 px-4 py-3 d-flex align-items-center">
            <v-icon color="white" class="me-2">mdi-briefcase-clock-outline</v-icon>
            <h5 class="mb-0 text-white font-weight-bold">{{ $t('Working Setting') }}</h5>
        </div>

        <v-card-text class="p-4 p-md-5 text-center text-muted">
            <v-icon size="48" class="mb-3">mdi-progress-wrench</v-icon>
            <p class="mb-0">{{ $t('Working days and office hours settings will be available here soon.') }}</p>
        </v-card-text>
    </v-card>
</template>
