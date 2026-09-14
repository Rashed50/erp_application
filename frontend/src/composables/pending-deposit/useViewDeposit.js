export function useViewDeposit() {
    // form
    const viewForm = reactive({
        name: '',
        mobile: '',
        member_number: '',
        reference_mobile: '',
        paymentOption: '',
        tnx_id: '',
        amount: '',
        gateway_fee: '',
        grand_total: '',
        create_date: '',
    })

    // modal state
    const viewModalVisible = ref(false)
    const viewDialog = ref(false)

    // selected row
    const selectedDeposit = ref({})

    // open modal + fill form
    const openViewModal = (item) => {
        selectedDeposit.value = { ...item }

        viewForm.name = item.name ?? 'N/A'
        viewForm.mobile = item.mobile ?? 'N/A'
        viewForm.member_number = item.member_number ?? 'N/A'
        viewForm.reference_mobile = item.reference_mobile ?? 'N/A'
        viewForm.paymentOption = item.paymentOption ?? 'N/A'
        viewForm.tnx_id = item.tnx_id ?? 'N/A'
        viewForm.amount = item.amount ?? 'N/A'
        viewForm.gateway_fee = item.gateway_fee ?? 'N/A'
        viewForm.grand_total = item.grand_total ?? 'N/A'
        viewForm.create_date = item.created_at_formatted ?? 'N/A'

        viewModalVisible.value = true
        viewDialog.value = true
    }

    // close modal
    const closeViewModal = () => {
        viewModalVisible.value = false
        viewDialog.value = false
        viewForm.reset()
    }

    return {
        viewForm,
        viewModalVisible,
        viewDialog,
        selectedDeposit,
        openViewModal,
        closeViewModal,
    }
}
