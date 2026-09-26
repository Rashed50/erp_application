import { ref } from 'vue'
import axios from 'axios'
import { useStoreForm } from '@/composables/useStoreForm'

export const emptyEmployeeDetail = () => ({
    national_id: '',
    passport_no: '',
    marital_status: '',
    blood_group: '',
    permanent_address: '',
    payment_method: 'Cash',
    bank_name: '',
    bank_branch: '',
    bank_account_name: '',
    bank_account_no: '',
    emergency_contact_name: '',
    emergency_contact_relation: '',
    emergency_contact_phone: '',
    notes: '',
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
        joining_date: new Date().toISOString().slice(0, 10),
        last_working_date: '',
        department: '',
        designation: '',
        employment_type: 'Permanent',
        status: 'Active',
        detail: emptyEmployeeDetail(),
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
