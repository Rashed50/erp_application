import { ref } from 'vue'

const user = ref(window.authUser || null)
const roles = ref(window.userRoles || [])
const permissions = ref(window.userPermissions || [])

export function useAuth() {
  const hasRole = (roleName) => {
    return roles.value.includes(roleName)
  }

  const hasPermission = (permissionName) => {
    return permissions.value.includes(permissionName)
  }

  return {
    user,
    roles,
    permissions,
    hasRole,
    hasPermission
  }
}