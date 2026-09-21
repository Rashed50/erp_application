<template>
    <div class="breadcrumb-area">
        <div class="container-fluid">
            <div class="row align-items-center">
                <!-- Title -->
                <div class="col-md-6">
                    <h4>
                        <span><i :class="icon"></i></span>
                        {{ title }}
                    </h4>
                </div>

                <!-- Buttons -->
                <div class="col-md-6 d-flex justify-content-end align-items-center gap-2">
                    <template v-if="displayButtons.length">
                        <router-link v-for="(button, index) in displayButtons" :key="`${button.text}-${index}`"
                            :to="button.link" class="primary-button">
                            <i :class="button.icon || buttonIcon"></i>
                            {{ button.text }}
                        </router-link>
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
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
    },
    buttons: {
        type: Array,
        default: () => []
    }
})

const displayButtons = computed(() => {
    const list = Array.isArray(props.buttons) ? props.buttons : []

    if (props.buttonText && props.buttonLink) {
        list.unshift({
            text: props.buttonText,
            link: props.buttonLink,
            icon: props.buttonIcon,
        })
    }

    return list.filter(button => button && button.text && button.link)
})
</script>
