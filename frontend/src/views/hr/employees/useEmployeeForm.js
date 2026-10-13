import { ref } from 'vue'
import axios from 'axios'
import { useStoreForm } from '@/composables/useStoreForm'

export const emptyEmployeeDetail = () => ({
    national_id: '',
    passport_no: '',
    marital_status: '',
    blood_group: '',
    permanent_address: '',
    permanent_division_id: '',
    permanent_district_id: '',
    permanent_upazila_id: '',
    payment_method: 'Cash',
    emergency_contact_name: '',
    emergency_contact_relation: '',
    emergency_contact_phone: '',
    notes: '',
})

export const emptyEmployeeBank = () => ({
    bank_name: '',
    branch_name: '',
    account_name: '',
    account_no: '',
    routing_no: '',
})

// Form state for the Add/Edit employee pages plus the choice lists they need.
export function useEmployeeForm() {
    const formState = useStoreForm({
        employee_code: '',
        name: '',
        father_name: '',
        mother_name: '',
        date_of_birth: '',
        gender: '',
        phone: '',
        email: '',
        address: '',
        division_id: '',
        district_id: '',
        upazila_id: '',
        joining_date: new Date().toISOString().slice(0, 10),
        last_working_date: '',
        department_id: '',
        designation_id: '',
        employment_type: 'Permanent',
        status: 'Active',
        detail: emptyEmployeeDetail(),
        bank: emptyEmployeeBank(),
    })

    const options = ref({ departments: [], designations: [], statuses: [], employment_types: [], genders: [] })

    const loadOptions = async () => {
        try {
            const { data } = await axios.get('/api/hr/employees/options')
            if (data.success) options.value = data.data
        } catch (e) {
            console.error(e)
        }
    }

    return { ...formState, options, loadOptions }
}
