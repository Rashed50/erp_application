<template>
    <div class="container mt-4">
        <!-- Header & Navigation Menu aligned in the same row -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="m-0">Attendance Management System</h4>

            <div class="btn-group" role="group">
                <button class="btn" :class="activeTab === 1 ? 'btn-primary' : 'btn-outline-primary'"
                    @click="activeTab = 1">
                    <i class="fa-solid fa-search me-1"></i> Search
                </button>
                <button class="btn" :class="activeTab === 2 ? 'btn-success' : 'btn-outline-success'"
                    @click="activeTab = 2">
                    <i class="fa-solid fa-plus me-1"></i> IN
                </button>
                <button class="btn" :class="activeTab === 3 ? 'btn-danger' : 'btn-outline-danger'"
                    @click="activeTab = 3">
                    <i class="fa-solid fa-minus me-1"></i> Out
                </button>
            </div>
        </div>

        <!-- Dynamic Components Container -->
        <div class="card p-4 shadow-sm border-0">
            <!-- List / Search Component (Tab 1) -->
            <AttendanceList v-if="activeTab === 1" :records="attendanceList" @delete-record="handleDelete" />

            <!-- Attendance IN Component (Tab 2) -->
            <AttendanceIn v-if="activeTab === 2" :employees="attendanceList" @mark-in="handleMarkIn"
                @cancel="activeTab = 1" />

            <!-- Attendance OUT Component (Tab 3) -->
            <AttendanceOut v-if="activeTab === 3" :employees="attendanceList" @mark-out="handleMarkOut"
                @cancel="activeTab = 1" />
        </div>
    </div>
</template>

<script>
import AttendanceList from './AttendanceList.vue'
import AttendanceIn from './AttendanceIn.vue'
import AttendanceOut from './AttendanceOut.vue'

export default {
    name: 'AttendanceMain',
    components: {
        AttendanceList,
        AttendanceIn,
        AttendanceOut
    },
    data() {
        return {
            activeTab: 1, // 1 for List/Search, 2 for IN, 3 for OUT
            attendanceList: [
                { id: 1, name: 'John Doe', code: 'EMP001', date: '2026-10-14', inTime: '09:00 AM', outTime: '05:00 PM', status: 'present' },
                { id: 2, name: 'Jane Smith', code: 'EMP002', date: '2026-10-14', inTime: '09:15 AM', outTime: '', status: 'late' },
            ]
        }
    },
    methods: {
        handleAddEmployee(newEmp) {
            this.attendanceList.unshift({
                id: Date.now(),
                ...newEmp,
                date: new Date().toISOString().slice(0, 10),
                inTime: '',
                outTime: '',
                status: 'absent'
            })
            this.activeTab = 1
        },
        handleMarkIn(data) {
            const emp = this.attendanceList.find(i => i.id === data.id)
            if (emp) {
                emp.inTime = data.time
                emp.status = 'present'
            }
            this.activeTab = 1
        },
        handleMarkOut(data) {
            const emp = this.attendanceList.find(i => i.id === data.id)
            if (emp) {
                emp.outTime = data.time
            }
            this.activeTab = 1
        },
        handleDelete(id) {
            this.attendanceList = this.attendanceList.filter(i => i.id !== id)
        }
    }
}
</script>