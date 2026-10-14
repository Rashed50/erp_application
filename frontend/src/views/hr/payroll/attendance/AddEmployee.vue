<template>
    <div class="container mt-4">
        <!-- Header & Navigation Tabs -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Attendance Entry Management</h2>
            <div class="btn-group">
                <button class="btn btn-primary" :class="{ active: currentTab === 'search' }"
                    @click="currentTab = 'search'">
                    <i class="fa-solid fa-magnifying-glass me-1"></i> Search & Manage
                </button>
                <button class="btn btn-success" :class="{ active: currentTab === 'in' }" @click="currentTab = 'in'">
                    <i class="fa-solid fa-right-to-bracket me-1"></i> Punch IN
                </button>
            </div>
        </div>

        <!-- TAB 1: SEARCH & LIST VIEW -->
        <div v-if="currentTab === 'search'" class="card p-4 shadow-sm border-0">
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Filter by Date</label>
                    <input type="date" class="form-control" v-model="filterDate" />
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Search Employee Name / ID</label>
                    <input type="text" class="form-control" v-model="searchQuery" placeholder="Enter name or ID..." />
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Employee ID</th>
                            <th>Name</th>
                            <th>Date</th>
                            <th>First Punch (IN)</th>
                            <th>Last Punch (OUT)</th>
                            <th>Worked Hours</th>
                            <th>Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="filteredAttendances.length === 0">
                            <td colspan="9" class="text-center text-muted py-3">No attendance records found.</td>
                        </tr>
                        <tr v-for="(item, index) in filteredAttendances" :key="item.id">
                            <td>{{ index + 1 }}</td>
                            <td>{{ item.employee_code }}</td>
                            <td>{{ item.employee_name }}</td>
                            <td>{{ item.attendance_date }}</td>
                            <td><span class="badge bg-light text-dark border">{{ item.first_punch_at || '-' }}</span>
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ item.last_punch_at || '-' }}</span>
                            </td>
                            <td>{{ item.worked_hours }} hrs</td>
                            <td>
                                <span :class="getStatusBadge(item.status)">{{ item.status }}</span>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-danger" @click="deleteRecord(item.id)">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TAB 2: PUNCH IN / OUT SEARCH & ADD VIEW -->
        <div v-if="currentTab === 'in'" class="card p-4 shadow-sm border-0">
            <h4 class="text-success mb-3">Punch IN / Attendance Entry</h4>

            <!-- Search Criteria Bar -->
            <div class="row g-3 bg-light p-3 rounded mb-4">
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Date</label>
                    <input type="date" class="form-control" v-model="form.attendance_date" />
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Shift</label>
                    <select class="form-select" v-model="form.attendance_shift_id">
                        <option value="1">Morning Shift (08:00 AM)</option>
                        <option value="2">Day Shift (09:00 AM)</option>
                        <option value="3">Night Shift (08:00 PM)</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Employee ID / Code</label>
                    <div class="input-group">
                        <input type="text" class="form-control" v-model="searchEmpId" placeholder="e.g. EMP001" />
                        <button class="btn btn-outline-secondary" type="button" @click="findEmployee">Search</button>
                    </div>
                </div>
            </div>

            <!-- Found Employee Form -->
            <div v-if="selectedEmployee" class="border p-4 rounded bg-white">
                <div class="alert alert-info mb-3">
                    <strong>Found:</strong> {{ selectedEmployee.name }} ({{ selectedEmployee.code }}) · Department: {{
                        selectedEmployee.department }}
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">First Punch At (IN Time)</label>
                        <input type="datetime-local" class="form-control" v-model="form.first_punch_at" />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Last Punch At (OUT Time - Optional)</label>
                        <input type="datetime-local" class="form-control" v-model="form.last_punch_at" />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Status</label>
                        <select class="form-select" v-model="form.status">
                            <option value="present">Present</option>
                            <option value="late">Late</option>
                            <option value="half_day">Half Day</option>
                            <option value="leave">Leave</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Notes / Remarks</label>
                        <input type="text" class="form-control" v-model="form.notes"
                            placeholder="Optional comments..." />
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button class="btn btn-success text-white px-4" @click="saveAttendance">
                        <i class="fa-solid fa-check me-1"></i> Save Attendance
                    </button>
                    <button class="btn btn-secondary" @click="currentTab = 'search'">Cancel</button>
                </div>
            </div>

            <div v-else-if="searched && !selectedEmployee" class="alert alert-warning text-center py-3">
                No employee found matching ID "<strong>{{ searchEmpId }}</strong>"
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const currentTab = ref('search')
const filterDate = ref(new Date().toISOString().slice(0, 10))
const searchQuery = ref('')

// Mock Employee database list for frontend searching
const employeesDb = ref([
    { id: 101, code: 'EMP001', name: 'John Doe', department: 'Engineering' },
    { id: 102, code: 'EMP002', name: 'Jane Smith', department: 'HR' },
    { id: 103, code: 'EMP003', name: 'Robert Johnson', department: 'Finance' },
])

// Daily attendances list (matches your migration columns)
const attendances = ref([
    {
        id: 1,
        employee_auto_id: 101,
        employee_code: 'EMP001',
        employee_name: 'John Doe',
        attendance_date: new Date().toISOString().slice(0, 10),
        attendance_shift_id: 2,
        status: 'present',
        first_punch_at: '09:00 AM',
        last_punch_at: '05:00 PM',
        worked_hours: 8.00,
        notes: 'On time'
    }
])

// Form state for new/punch entry
const searchEmpId = ref('')
const searched = ref(false)
const selectedEmployee = ref(null)

const form = ref({
    attendance_date: new Date().toISOString().slice(0, 10),
    attendance_shift_id: '2',
    status: 'present',
    first_punch_at: '',
    last_punch_at: '',
    notes: ''
})

// Search employee by ID in punch tab
const findEmployee = () => {
    searched.value = true
    selectedEmployee.value = employeesDb.value.find(
        e => e.code.toLowerCase() === searchEmpId.value.trim().toLowerCase()
    ) || null
}

// Filter list based on date and name/code
const filteredAttendances = computed(() => {
    return attendances.value.filter(item => {
        const matchesDate = !filterDate.value || item.attendance_date === filterDate.value
        const matchesQuery = !searchQuery.value ||
            item.employee_name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            item.employee_code.toLowerCase().includes(searchQuery.value.toLowerCase())
        return matchesDate && matchesQuery
    })
})

const getStatusBadge = (status) => {
    switch (status) {
        case 'present': return 'badge bg-success'
        case 'late': return 'badge bg-warning text-dark'
        case 'half_day': return 'badge bg-info text-dark'
        case 'leave': return 'badge bg-secondary'
        default: return 'badge bg-danger'
    }
}

const saveAttendance = () => {
    if (!selectedEmployee.value) return

    // Push into local state matching migration structure
    attendances.value.unshift({
        id: Date.now(),
        employee_auto_id: selectedEmployee.value.id,
        employee_code: selectedEmployee.value.code,
        employee_name: selectedEmployee.value.name,
        attendance_date: form.value.attendance_date,
        attendance_shift_id: form.value.attendance_shift_id,
        status: form.value.status,
        first_punch_at: form.value.first_punch_at || '-',
        last_punch_at: form.value.last_punch_at || '-',
        worked_hours: 8.00,
        notes: form.value.notes
    })

    // Reset and switch back to search tab
    searchEmpId.value = ''
    selectedEmployee.value = null
    searched.value = false
    currentTab.value = 'search'
}

const deleteRecord = (id) => {
    if (confirm('Are you sure you want to delete this record?')) {
        attendances.value = attendances.value.filter(i => i.id !== id)
    }
}
</script>