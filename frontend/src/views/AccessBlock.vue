<template>
    <div class="access-denied-wrapper">
        <h1>403</h1>
        <p>Access Denied</p>
        <span>You do not have permission to access this page.</span>

        <div class="actions">
            <button class="btn-back" @click="goBack">Go Back</button>
            <button class="btn-home" @click="goHome">Go Home</button>
        </div>
    </div>
</template>

<script setup>
import { useRouter } from 'vue-router'

const router = useRouter()

const goBack = () => {
    router.back()
}

const goHome = () => {
    router.push({ name: 'admin_dashboard' }) // change route name if needed
}
</script>

<style scoped>
.access-denied-wrapper {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    height: 100vh;
    text-align: center;
    background-color: #fff3f3;
    color: #333;
    padding: 20px;
    box-sizing: border-box;
}

.access-denied-wrapper h1 {
    font-size: 7rem;
    margin: 0;
    font-weight: bold;
    color: #d32f2f;
}

.access-denied-wrapper p {
    font-size: 1.6rem;
    margin: 10px 0 5px;
    font-weight: 600;
}

.access-denied-wrapper span {
    font-size: 1rem;
    color: #666;
    margin-bottom: 25px;
}

.actions {
    display: flex;
    gap: 15px;
}

.btn-back,
.btn-home {
    padding: 10px 25px;
    border-radius: 5px;
    font-weight: 500;
    border: none;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-back {
    background-color: #9e9e9e;
    color: #fff;
}

.btn-back:hover {
    background-color: #757575;
}

.btn-home {
    background-color: #1976d2;
    color: #fff;
}

.btn-home:hover {
    background-color: #1565c0;
}
</style>
