import { useAuthStore } from '@/stores/auth'

export function usePermission() {
    const auth = useAuthStore()

    const can = (permissions = []) => {
        if (!permissions.length) return true

        // auth not ready yet
        if (!auth.user || !Array.isArray(auth.user.permissions)) {
            return false
        }

        // permissions on the user are already flat name strings
        return permissions.some(p => auth.user.permissions.includes(p))
    }

    return { can }
}
