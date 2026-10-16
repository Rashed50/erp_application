<template>
    <div class="container mt-4">
       

        <!-- Navigation Menu / Tabs (Aligned to Right) -->
        <div class="d-flex justify-content-end mb-4">
            <div class="btn-group" role="group">
                <button class="btn" :class="activeTab === 'list' ? 'btn-primary' : 'btn-outline-primary'"
                    @click="activeTab = 'list'">
                    <i class="fa-solid fa-list me-1"></i> Attendance List / Search
                </button>
                <button class="btn" :class="activeTab === 'in' ? 'btn-success' : 'btn-outline-success'"
                    @click="activeTab = 'in'">
                    <i class="fa-solid fa-plus me-1"></i> Attendance IN
                </button>
                <button class="btn" :class="activeTab === 'out' ? 'btn-danger' : 'btn-outline-danger'"
                    @click="activeTab = 'out'">
                    <i class="fa-solid fa-minus me-1"></i> Attendance Out
                </button>
            </div>
        </div>

        <!-- Dynamic Components Container -->
        <div class="card p-4 shadow-sm border-0">
            <!-- List / Search Component -->
            <AttendanceList v-if="activeTab === 'list'" :records="attendanceList" @delete-record="handleDelete" />

            <!-- Attendance IN Component -->
            <AttendanceIn v-if="activeTab === 'in'" :employees="attendanceList" @mark-in="handleMarkIn"
                @cancel="activeTab = 'list'" />

            <!-- Attendance OUT Component -->
            <AttendanceOut v-if="activeTab === 'out'" :employees="attendanceList" @mark-out="handleMarkOut"
                @cancel="activeTab = 'list'" />
        </div>
    </div>
</template>

<script>
import AttendanceList from './AttendanceList.vue'
import AddEmployee from './AddEmployee.vue'
import AttendanceIn from './AttendanceIn.vue'
import AttendanceOut from './AttendanceOut.vue'

export default {
    name: 'AttendanceMain',
    components: {
        AttendanceList,
        AddEmployee,
        AttendanceIn,
        AttendanceOut
    },
    data() {
        return {
            activeTab: 'list',
            attendanceList: [
                { id: 1, name: 'John Doe', code: 'EMP001', date: '2026-10-13', inTime: '09:00 AM', outTime: '05:00 PM', status: 'Present' },
                { id: 2, name: 'Jane Smith', code: 'EMP002', date: '2026-10-13', inTime: '09:15 AM', outTime: '', status: 'Late' },
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
                status: 'Absent'
            })
            this.activeTab = 'list'
        },
        handleMarkIn(data) {
            const emp = this.attendanceList.find(i => i.id === data.id)
            if (emp) {
                emp.inTime = data.time
                emp.status = 'Present'
            }
            this.activeTab = 'list'
        },
        handleMarkOut(data) {
            const emp = this.attendanceList.find(i => i.id === data.id)
            if (emp) {
                emp.outTime = data.time
            }
            this.activeTab = 'list'
        },
        handleDelete(id) {
            this.attendanceList = this.attendanceList.filter(i => i.id !== id)
        }
    }
}
</script>