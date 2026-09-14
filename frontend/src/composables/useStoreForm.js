import { ref, reactive } from 'vue'
import axios from 'axios'

export function useStoreForm(initialData = {}) {
  // reactive form data
  const form = reactive({ ...initialData })
  const imageInput = ref(null)

  // state
  const isSubmitting = ref(false)
  const errors = reactive({})
  const successMessage = ref(null)
  const errorMessage = ref(null)

  // clear errors
  const clearErrors = () => {
    for (const key in errors) delete errors[key]
  }

  // clear messages
  const clearMessages = () => {
    successMessage.value = null
    errorMessage.value = null
  }

    // submit function
    const submit = async (url, method = 'post') => {
        isSubmitting.value = true
        clearErrors()
        clearMessages()

        try {
            const payload = { ...form }
            const response = method.toLowerCase() === 'put'
                ? await axios.put(url, payload)
                : await axios.post(url, payload)

            if (response.data.message) {
                successMessage.value = response.data.message
            }

            isSubmitting.value = false
            return response.data
        } catch (err) {
            if (err.response?.status === 422) {
                const respErrors = err.response.data.data
                for (const key in respErrors) {
                    errors[key] = respErrors[key].join(' ')
                }
            } else {
                errorMessage.value =
                    err.response?.data?.message || err.message || 'Something went wrong'
            }

            isSubmitting.value = false
            return null
        }
    }




  // reset form
  const resetForm = () => {
    for (const key in form) form[key] = initialData[key] ?? ''
    clearErrors()
    clearMessages()
  }

  const resetFileInput = () => {
    if (imageInput.value) {
        imageInput.value.value = ''
    }
  }

  return {
    form,
    errors,
    successMessage,
    errorMessage,
    isSubmitting,
    submit,
    resetForm,
    resetFileInput,
    imageInput

  }
}
