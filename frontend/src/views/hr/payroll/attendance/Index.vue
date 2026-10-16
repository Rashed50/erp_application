<template>
    <div class="main-content-wrapper mt-4">
        <div class="container-fluid">

            <!-- Header & tab buttons -->
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="m-0">{{ $t('Attendance Management') }}</h4>

                <div class="d-flex gap-2">
                    <v-btn v-for="tab in tabs" :key="tab.id" class="text-none"
                        :class="{ 'text-white': activeTab === tab.id }" :color="tab.color"
                        :variant="activeTab === tab.id ? 'flat' : 'outlined'" rounded="0" @click="activeTab = tab.id">
                        <i class="fa-solid me-1" :class="tab.icon"></i> {{ $t(tab.label) }}
                    </v-btn>
                </div>
            </div>

            <!-- Each child loads its own data from the API -->
            <AttendanceList v-if="activeTab === 1" />
            <AttendanceIn v-else-if="activeTab === 2" @cancel="activeTab = 1" />
            <AttendanceOut v-else-if="activeTab === 3" @cancel="activeTab = 1" />
        </div>
    </div>
</template>

<script>
import AttendanceList from './AttendanceList.vue';
import AttendanceIn from './AttendanceIn.vue';
import AttendanceOut from './AttendanceOut.vue';

export default {
    name: 'AttendanceMain',

    components: {
        AttendanceList,
        AttendanceIn,
        AttendanceOut
    },

    data() {
        return {
            activeTab: 1, // 1 = List/Search, 2 = IN, 3 = OUT

            tabs: [
                { id: 1, label: 'Search', icon: 'fa-search', color: 'blue-darken-3' },
                { id: 2, label: 'IN', icon: 'fa-plus', color: 'green-darken-3' },
                { id: 3, label: 'Out', icon: 'fa-minus', color: 'red-darken-3' }
            ]
        };
    }
};
</script>