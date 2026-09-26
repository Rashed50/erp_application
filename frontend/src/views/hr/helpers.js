// Shared formatting for the HR & Payroll screens.

export const SALARY_STATUSES = ['Generated', 'Approved', 'Paid', 'Cancelled']

export const money = (value) =>
    Number(value || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })

// 'YYYY-MM' for the current month, the format every HR month filter uses.
export const currentMonth = () => new Date().toISOString().slice(0, 7)

// 'YYYY-MM' -> 'September 2026'
export const monthLabel = (month) => {
    if (!month) return ''
    const [year, monthNumber] = month.split('-').map(Number)
    return new Date(year, monthNumber - 1, 1).toLocaleString('en-US', { month: 'long', year: 'numeric' })
}

export const salaryStatusClass = (status) => ({
    Generated: 'badge bg-info text-dark',
    Approved: 'badge bg-primary',
    Paid: 'badge bg-success',
    Cancelled: 'badge bg-secondary',
}[status] || 'badge bg-light text-dark')

export const employeeStatusClass = (status) => ({
    Active: 'badge bg-success',
    Inactive: 'badge bg-secondary',
    Resigned: 'badge bg-warning text-dark',
    Terminated: 'badge bg-danger',
}[status] || 'badge bg-light text-dark')
