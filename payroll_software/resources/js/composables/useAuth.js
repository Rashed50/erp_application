import { ref } from "vue";

const user = ref(window.authUser || null);
const roles = ref(window.userRoles || []);
const permissions = ref(window.userPermissions || []);

export function useAuth() {
    const hasRole = (roleName) => {
        return roles.value.includes(roleName);
    };

    const hasPermission = (permissionName) => {
        return permissions.value.includes(permissionName);
    };

    const hasAnyPermission = (...perms) => {
        return perms.some((p) => permissions.value.includes(p));
    };

    const hasAllPermissions = (...perms) => {
        return perms.every((p) => permissions.value.includes(p));
    };

    return {
        user,
        roles,
        permissions,
        hasRole,
        hasPermission,
        hasAnyPermission,
        hasAllPermissions,
    };
}


//! Example:-


// Permission Check
//<div v-if="hasPermission('set-advance')">



// Roll Check
// <div v-if="hasRole('Admin')"></div>;



// Or
// <div v-if="hasPermission('set-advance') || hasPermission('manage-advance')"></div>



// And
// <div v-if="hasPermission('set-advance') && hasPermission('manage-advance')"></div>



// If any one
// <div v-if="hasAnyPermission('set-advance', 'manage-advance')"></div>



// If all
// <div v-if="hasAllPermissions('set-advance', 'manage-advance')"></div>
