<template>
    <router-link :to="toLink" v-if="toLink">
        <v-card class="stat-card" :class="color" elevation="6">
            <v-card-text class="stat-body">
                <div>
                    <h2 class="stat-number">
                        {{ Number(value || 0).toLocaleString('en-IN') }}
                    </h2>
                    <p class="stat-label">{{ title }}</p>
                </div>

                <v-icon size="64" class="stat-icon">
                    <i :class="icon"></i>
                </v-icon>
            </v-card-text>

            <div class="stat-footer">
                <v-icon size="18">mdi-arrow-up-circle</v-icon>
            </div>
        </v-card>
    </router-link>

    <v-card class="stat-card" :class="color" elevation="6" v-else>
        <v-card-text class="stat-body">
            <div>
                <h2 class="stat-number">
                    {{ Number(value || 0).toLocaleString('en-IN') }}
                </h2>
                <p class="stat-label">{{ title }}</p>
            </div>

            <v-icon size="64" class="stat-icon">
                <i :class="icon"></i>
            </v-icon>
        </v-card-text>

        <div class="stat-footer">
            <v-icon size="18">mdi-arrow-up-circle</v-icon>
        </div>
    </v-card>

</template>

<script setup>
defineProps({
    title: String,
    value: Number,
    icon: String,
    color: String,
    toLink: String
})
</script>

<style scoped>
.stat-card {
    border-radius: 12px;
    overflow: hidden;
    color: #fff;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 25px rgba(0, 0, 0, 0.2);
}

.stat-body {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
}

.stat-number {
    font-size: 34px;
    font-weight: 700;
    margin: 0;
}

.stat-label {
    font-size: 14px;
    opacity: 0.9;
}

.stat-icon {
    opacity: 0.25;
}

.stat-footer {
    background: rgba(0, 0, 0, 0.15);
    text-align: center;
    padding: 6px;
    cursor: pointer;
}

/* Backgrounds */
.bg-success {
    background: linear-gradient(135deg, #2ecc71, #27ae60);
}

.bg-warning {
    background: linear-gradient(135deg, #f1c40f, #f39c12);
}

.bg-primary {
    background: linear-gradient(135deg, #3498db, #2980b9);
}

.bg-info {
    background: linear-gradient(135deg, #1abc9c, #16a085);
}

.bg-danger {
    background: linear-gradient(135deg, #e74c3c, #c0392b);
}
</style>
