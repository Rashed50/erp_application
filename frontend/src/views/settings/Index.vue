<template lang="html">
    <Breadcrumb title="Settings" />

    <div class="main-content-wrapper mt-4">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <v-card elevation="1" class="rounded-lg mb-4 px-3 py-2 settings-menu">
                        <div class="d-flex flex-wrap ga-2">
                            <v-btn v-for="section in sections" :key="section.key"
                                :color="activeSection === section.key ? 'blue-darken-4' : 'grey-lighten-3'"
                                :variant="activeSection === section.key ? 'flat' : 'tonal'" class="text-none"
                                @click="selectSection(section.key)">
                                <v-icon start>{{ section.icon }}</v-icon>
                                {{ $t(section.label) }}
                            </v-btn>
                        </div>
                    </v-card>

                    <component :is="activeComponent" />
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import Breadcrumb from '@/components/common/Breadcrumb.vue'
import CompanyProfile from './CompanyProfile.vue'
import WorkingSetting from './WorkingSetting.vue'
import LeaveSetting from './LeaveSetting.vue'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()

const sections = [
    { key: 'company', label: 'Company Profile', icon: 'mdi-domain', component: CompanyProfile },
    { key: 'working', label: 'Working Setting', icon: 'mdi-briefcase-clock-outline', component: WorkingSetting },
    { key: 'leave', label: 'Leave Setting', icon: 'mdi-calendar-account-outline', component: LeaveSetting },
]

// The open section lives in the URL (?section=leave) so a reload or shared link keeps it.
const activeSection = computed(() =>
    sections.some((section) => section.key === route.query.section) ? route.query.section : 'company'
)
const activeComponent = computed(() => sections.find((section) => section.key === activeSection.value).component)

const selectSection = (key) => {
    router.replace({ query: { ...route.query, section: key } })
}
</script>
<style scoped>
.settings-menu {
    border-bottom: 3px solid #0d47a1;
}
</style>
