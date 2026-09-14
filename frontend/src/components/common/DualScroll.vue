<template>
    <!-- Top scrollbar (conditionally visible) -->
    <div class="dual-scroll-top" ref="topScroll" v-show="showTopScroll">
        <div ref="topContent"></div>
    </div>

    <!-- Table wrapper -->
    <div class="dual-scroll-body" ref="bodyScroll">
        <slot />
    </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, nextTick } from 'vue'

const topScroll = ref(null)
const topContent = ref(null)
const bodyScroll = ref(null)

const showTopScroll = ref(false)

let tableWrapper = null
let syncing = false
let observer = null

const syncTopToBody = () => {
    if (syncing || !tableWrapper) return
    syncing = true
    tableWrapper.scrollLeft = topScroll.value.scrollLeft
    syncing = false
}

const syncBodyToTop = () => {
    if (syncing || !tableWrapper) return
    syncing = true
    topScroll.value.scrollLeft = tableWrapper.scrollLeft
    syncing = false
}

const updateScroll = async () => {
    await nextTick()

    tableWrapper =
        bodyScroll.value.querySelector('.v-table__wrapper') ||
        bodyScroll.value

    if (!tableWrapper) return

    tableWrapper.style.overflowX = 'auto'

    const hasOverflow =
        tableWrapper.scrollWidth > tableWrapper.clientWidth

    showTopScroll.value = hasOverflow

    if (hasOverflow) {
        topContent.value.style.width =
            tableWrapper.scrollWidth + 'px'
        topContent.value.style.height = '1px'
    }
}

onMounted(async () => {
    await updateScroll()

    topScroll.value.addEventListener('scroll', syncTopToBody)
    window.addEventListener('resize', updateScroll)

    observer = new MutationObserver(updateScroll)
    observer.observe(bodyScroll.value, {
        childList: true,
        subtree: true,
    })
})

onBeforeUnmount(() => {
    observer?.disconnect()
    window.removeEventListener('resize', updateScroll)
})
</script>

<style scoped>
.dual-scroll-top {
    overflow-x: auto;
    overflow-y: hidden;
    height: 14px;
}

.dual-scroll-body {
    overflow: hidden;
}

.v-table__wrapper {
    overflow-x: auto;
    max-height: 70vh;
}
</style>
