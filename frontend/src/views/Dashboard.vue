<template lang="html">
    <v-card class="dashboard-wrapper">
        <div class="card-header">
            <h4>Admin Dashboard</h4>
            <p>Welcome, {{ auth.user.name ?? 'Boss' }}!</p>
        </div>

        <div class="card-body">
            <div class="dashboard-content">

                Dashboard content goes here. You can add your dashboard components, charts, and statistics in this section.

            </div>
        </div>
    </v-card>
</template>

<script setup>
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()

</script>

<style scoped>
.dashboard-wrapper {
    margin: 15px 10px 10px;
    padding: 0 !important;
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 15px;
    border-bottom: 1px solid #ddd;
    margin-bottom: 15px;
}

.card-header p {
    font-size: 18px;
    color: red;
    font-weight: 600;
}

.card-header-custom {
    background: #fff;
    border-radius: 4px 4px 0 0;
}

.card-body {
    padding: 10px 15px;
}
</style>