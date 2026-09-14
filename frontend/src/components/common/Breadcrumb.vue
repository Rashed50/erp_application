<template>
    <div class="breadcrumb-area">
        <div class="container-fluid">
            <div class="row">
                <!-- Title -->
                <div class="col-md-6">
                    <h4>
                        <span><i :class="icon"></i></span>
                        {{ title }}
                    </h4>
                </div>

                <!-- Button -->
                <div class="col-md-6 d-flex justify-content-end">
                    <router-link v-if="buttonText && buttonLink" :to="buttonLink" class="primary-button">
                        <i :class="buttonIcon"></i>
                        {{ buttonText }}
                    </router-link>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    title: {
        type: String,
        required: true
    },
    icon: {
        type: String,
        default: "fa-brands fa-gg-circle"
    },
    buttonText: {
        type: String,
        default: null
    },
    // buttonLink can be a path string OR an object for named route
    buttonLink: {
        type: [String, Object],
        default: null
    },
    buttonIcon: {
        type: String,
        default: "fa-solid fa-circle-plus"
    }
})
</script>
