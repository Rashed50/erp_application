// Shared formatting for the HR & Payroll screens.

import axios from 'axios'
import { intlLocale } from '@/i18n'

// Open cash/bank accounts that advances and salaries are paid from (or repaid into).
export const fetchPaymentAccounts = async () => {
    const { data } = await axios.get('/api/hr/payment-accounts')
    return data.success ? data.data : []
}

export const accountLabel = (account) => `${account.account_number} - ${account.name}`

// Labels for the salary revision fields named in field changes and the edit log.
export const SALARY_FIELD_LABELS = {
    effective_date: 'Effective Date',
    basic_salary: 'Basic Salary',
    house_rent: 'House Rent',
    medical_allowance: 'Medical Allowance',
    transport_allowance: 'Transport Allowance',
    food_allowance: 'Food Allowance',
    other_allowance: 'Other Allowance',
    overtime_rate: 'Overtime Rate (per hour)',
    other_deduction: 'Fixed Monthly Deduction',
    deduction_basis: 'Absence Deduction Based On',
    change_reason: 'Change Reason',
    designation: 'Designation',
    status: 'Status',
    remarks: 'Remarks',
}

// +1,500.00 / -500.00
export const signedMoney = (value) => `${Number(value) > 0 ? '+' : ''}${money(value)}`

// "House Rent +2,000.00, Medical Allowance -500.00"
export const fieldChangesText = (changes, translate) => (changes || [])
    .map((change) => `${translate(SALARY_FIELD_LABELS[change.field] || change.field)} ${signedMoney(change.difference)}`)
    .join(', ')

export const SALARY_STATUSES =['Generated', 'Approved', 'Partially Paid', 'Paid', 'Cancelled']

export const ADVANCE_STATUSES = ['Running', 'Completed']

export const money = (value) =>
    Number(value || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })

// 'YYYY-MM' for the current month, the format every HR month filter uses.
export const currentMonth = () => new Date().toISOString().slice(0, 7)

// 'YYYY-MM' -> 'September 2026'
export const monthLabel = (month) => {
    if (!month) return ''
    const [year, monthNumber] = month.split('-').map(Number)
    return new Date(year, monthNumber - 1, 1).toLocaleString(intlLocale(), { month: 'long', year: 'numeric' })
}

export const salaryStatusClass = (status) => ({
    Generated: 'badge bg-info text-dark',
    Approved: 'badge bg-primary',
    'Partially Paid': 'badge bg-warning text-dark',
    Paid: 'badge bg-success',
    Cancelled: 'badge bg-secondary',
}[status] || 'badge bg-light text-dark')

export const employeeStatusClass = (status) => ({
    Active: 'badge bg-success',
    Inactive: 'badge bg-secondary',
    Resigned: 'badge bg-warning text-dark',
    Terminated: 'badge bg-danger',
}[status] || 'badge bg-light text-dark')

export const advanceStatusClass = (status) => ({
    Running: 'badge bg-warning text-dark',
    Completed: 'badge bg-success',
}[status] || 'badge bg-light text-dark')
