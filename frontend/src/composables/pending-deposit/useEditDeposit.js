import { toast } from 'vue3-toastify'
import Form from 'vform'

export function useEditDeposit() {
    // form
    const editForm = reactive(
        new Form({
            id: '',
            mobile: '',
            reference_mobile: '',
            paymentOption: '',
            tnx_id: '',
            amount: '',
            gateway_fee: '',
            grand_total: '',
        })
    )

    // modal state
    const editModalVisible = ref(false)
    const editDialog = ref(false)

    // submit state
    const submitting = ref(false)

    // selected row
    const selectedDeposit = ref({})

    // payment methods
    const paymentMethods = [
        { label: 'ব্যাংক', value: 'ব্যাংক' },
        { label: 'ক্যাশ', value: 'ক্যাশ' },
        { label: 'বিকাশ', value: 'বিকাশ' },
        { label: 'নগদ', value: 'নগদ' },
    ]

    // open modal + fill form
    const openEditModal = (item) => {
        selectedDeposit.value = { ...item }

        editForm.fill({
            id: item.id ?? '',
            mobile: item.mobile ?? '',
            reference_mobile: item.reference_mobile ?? '',
            paymentOption: item.paymentOption ?? '',
            tnx_id: item.tnx_id ?? '',
            amount: item.amount ?? '',
            gateway_fee: item.gateway_fee ?? '',
            grand_total: item.grand_total ?? '',
        })

        editModalVisible.value = true
        editDialog.value = true
    }



    // submit update ( return updated row)
    const submitEditDeposit = async () => {
        submitting.value = true

        try {
        const res = await editForm.post(
            `/apis/pending-deposit-update/${editForm.id}`
        )

        if (res.data.status === true) {
            toast.success(res.data.message)
            closeEditModal()

            // backend data priority
            return res.data.deposit ?? {
            ...selectedDeposit.value,
            ...editForm.data(),
            }
        }

        toast.error(res.data.message)
        return false

        } catch (error) {
        toast.error(error.response?.data?.message || 'Something went wrong')
        return false
        } finally {
        submitting.value = false
        }
    }




    // submit update
    // const submitEditDeposit = async (onSuccess = null) => {
    //     submitting.value = true

    //     try {
    //         const res = await editForm.post(
    //             `/apis/pending-deposit-update/${editForm.id}`
    //         )

    //         if (res.data.status == true) {
    //             toast.success(res.data.message)
    //             closeEditModal()

    //             // call parent callback if provided
    //             if (typeof onSuccess === 'function') {
    //                 onSuccess()
    //             }

    //             return res
    //         }
    //         else {
    //             toast.error(res.data.message)
    //             return res
    //         }
    //     } catch (error) {
    //         toast.error(error.response?.data?.message || 'Something went wrong')
    //         throw error
    //     } finally {
    //         submitting.value = false
    //     }
    // }

    // close modal
    const closeEditModal = () => {
        editModalVisible.value = false
        editDialog.value = false
        editForm.reset()
    }

    return {
        editForm,
        editModalVisible,
        editDialog,
        selectedDeposit,
        paymentMethods,
        openEditModal,
        submitEditDeposit,
        closeEditModal,
    }
}
