<template>
    <div class="not-found-wrapper">
        <h1>404</h1>
        <p>Page Not Found</p>
        <button class="btn-back" @click="goBack">Go Back</button>
    </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
const router = useRouter()

const goBack = () => {
    router.back()  // navigates to previous page in history
}
</script>

<style scoped>
.not-found-wrapper {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    height: 100vh;
    text-align: center;
    background-color: #f5f5f5;
    color: #333;
    padding: 20px;
    box-sizing: border-box;
}

.not-found-wrapper h1 {
    font-size: 8rem;
    margin: 0;
    font-weight: bold;
}

.not-found-wrapper p {
    font-size: 1.5rem;
    margin: 10px 0 20px;
}

.btn-back {
    background-color: #1976d2;
    color: white;
    padding: 10px 25px;
    border-radius: 5px;
    font-weight: 500;
    border: none;
    cursor: pointer;
    transition: background-color 0.3s;
}

.btn-back:hover {
    background-color: #1565c0;
}
</style>
