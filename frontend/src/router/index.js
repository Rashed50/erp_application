import AppLayout from '@/layout/AuthenticatedLayout.vue';
import { createRouter, createWebHistory } from "vue-router";
import { useAuthStore } from '@/stores/auth'
import { setToast } from "@/helpers/toast";

import { toast } from "vue3-toastify";

const routes = [


    {
        path: '/',
        name: 'admin_login',
        component: () => import('@/views/Login.vue'),
        meta: {
            title: 'Login Account'
        }
    },

    {
        path: '/:pathMatch(.*)*',
        name: 'not-found',
        component: () => import('@/views/NotFound.vue'),
        meta: {
            title: '404 - Not Found!'
        }
    },

    {
        path: '/admin-v2/permission',
        name: 'access_block',
        component: () => import('@/views/AccessBlock.vue'),
        meta: {
            title: '403 - Access Denied!'
        }
    },

    /* ========= protected route START ========= */
    {
        path: '/admin',
        component: AppLayout,
        meta: { requiresAuth: true },
        children: [
            {
                path: 'dashboard',
                name: 'admin_dashboard',
                component: () => import('@/views/Dashboard.vue'),
                meta: {
                    title: 'Dashboard',
                }
            },


            /* ====================== Role Route START ====================== */
            {
                path: 'roles',
                name: 'admin_roles',
                component: () => import('@/views/roles/Index.vue'),
                meta: {
                    title: 'All Role List',
                    permissions: ['roles.view']
                }
            },

            {
                path: 'role-add',
                name: 'admin_role_add',
                component: () => import('@/views/roles/Add.vue'),
                meta: {
                    title: 'Add Role',
                    permissions: ['roles.create']
                }
            },

            {
                path: 'role-edit/:id',
                name: 'admin_role_edit',
                component: () => import('@/views/roles/Edit.vue'),
                meta: {
                    title: 'Edit Role',
                    permissions: ['roles.update']
                }
            },
            /* ====================== User Route  ====================== */
            {
                path: 'users',
                name: 'admin_users',
                component: () => import('@/views/users/Index.vue'),
                meta: {
                    title: 'All User List',
                    permissions: ['users.view']
                }
            },

            {
                path: 'user-add',
                name: 'admin_user_add',
                component: () => import('@/views/users/Add.vue'),
                meta: {
                    title: 'Add User',
                    permissions: ['users.create']
                }
            },

            {
                path: 'user-edit/:id',
                name: 'admin_user_edit',
                component: () => import('@/views/users/Edit.vue'),
                meta: {
                    title: 'Edit User',
                    permissions: ['users.update']
                }
            },

            /* ====================== Customer Route START ====================== */
            {
                path: 'customers',
                name: 'admin_customers_list',
                component: () => import('@/views/customers/Index.vue'),
                meta: {
                    title: 'All Customer List',
                    permissions: ['customers.view']
                }
            },

            {
                path: 'customer-add',
                name: 'admin_customer_add',
                component: () => import('@/views/customers/Add.vue'),
                meta: {
                    title: 'Add Customer',
                    permissions: ['customers.create']
                }
            },

            {
                path: 'customer-edit/:id',
                name: 'admin_customer_edit',
                component: () => import('@/views/customers/Edit.vue'),
                meta: {
                    title: 'Edit Customer',
                    permissions: ['customers.update']
                }
            },

            {
                path: 'customer-ledger/:id',
                name: 'admin_customer_ledger',
                component: () => import('@/views/customers/Ledger.vue'),
                meta: {
                    title: 'Customer Ledger',
                    permissions: ['customers.view']
                }
            },
            /* ====================== Customer Route END ====================== */

            /* ====================== Work Order Route START ====================== */
            {
                path: 'work-orders',
                name: 'admin_work_orders_list',
                component: () => import('@/views/work-orders/Index.vue'),
                meta: {
                    title: 'All Work Order List',
                    permissions: ['work-orders.view']
                }
            },

            {
                path: 'work-order-add',
                name: 'admin_work_order_add',
                component: () => import('@/views/work-orders/Add.vue'),
                meta: {
                    title: 'Add Work Order',
                    permissions: ['work-orders.create']
                }
            },

            {
                path: 'work-order-edit/:id',
                name: 'admin_work_order_edit',
                component: () => import('@/views/work-orders/Edit.vue'),
                meta: {
                    title: 'Edit Work Order',
                    permissions: ['work-orders.update']
                }
            },
            /* ====================== Work Order Route END ====================== */

            /* ====================== Supplier Route START ====================== */
            {
                path: 'suppliers',
                name: 'admin_suppliers_list',
                component: () => import('@/views/suppliers/Index.vue'),
                meta: {
                    title: 'All Supplier List',
                    permissions: ['suppliers.view']
                }
            },

            {
                path: 'supplier-add',
                name: 'admin_supplier_add',
                component: () => import('@/views/suppliers/Add.vue'),
                meta: {
                    title: 'Add Supplier',
                    permissions: ['suppliers.create']
                }
            },

            {
                path: 'supplier-edit/:id',
                name: 'admin_supplier_edit',
                component: () => import('@/views/suppliers/Edit.vue'),
                meta: {
                    title: 'Edit Supplier',
                    permissions: ['suppliers.update']
                }
            },

            {
                path: 'supplier-ledger/:id',
                name: 'admin_supplier_ledger',
                component: () => import('@/views/suppliers/Ledger.vue'),
                meta: {
                    title: 'Supplier Ledger',
                    permissions: ['suppliers.view']
                }
            },
            /* ====================== Supplier Route END ====================== */

            /* ====================== Purchase Route START ====================== */
            {
                path: 'purchases',
                name: 'admin_purchases_list',
                component: () => import('@/views/purchases/Index.vue'),
                meta: {
                    title: 'All Purchase List',
                    permissions: ['purchases.view']
                }
            },

            {
                path: 'purchase-add',
                name: 'admin_purchase_add',
                component: () => import('@/views/purchases/Add.vue'),
                meta: {
                    title: 'Add Purchase',
                    permissions: ['purchases.create']
                }
            },

            {
                path: 'purchase-edit/:id',
                name: 'admin_purchase_edit',
                component: () => import('@/views/purchases/Edit.vue'),
                meta: {
                    title: 'Edit Purchase',
                    permissions: ['purchases.update']
                }
            },
            /* ====================== Purchase Route END ====================== */

            /* ====================== Supplier Payment Route START ====================== */
            {
                path: 'accounting/purchase/payment/list',
                name: 'admin_supplier_payments_list',
                component: () => import('@/views/supplier-payments/Index.vue'),
                meta: {
                    title: 'Supplier Payments',
                    permissions: ['supplier-payments.view']
                }
            },

            {
                path: 'accounting/purchase/payment/index',
                name: 'admin_supplier_payment_add',
                component: () => import('@/views/supplier-payments/Add.vue'),
                meta: {
                    title: 'Supplier Payment',
                    permissions: ['supplier-payments.create']
                }
            },
            /* ====================== Supplier Payment Route END ====================== */

            /* ====================== Product Route START ====================== */
            {
                path: 'accounting/product/list',
                name: 'admin_products_list',
                component: () => import('@/views/products/Index.vue'),
                meta: {
                    title: 'Products',
                    permissions: ['products.view']
                }
            },
            /* ====================== Product Route END ====================== */

            /* ====================== Sale Route START ====================== */
            {
                path: 'sales',
                name: 'admin_sales_list',
                component: () => import('@/views/sales/Index.vue'),
                meta: {
                    title: 'All Sale List',
                    permissions: ['sales.view']
                }
            },

            {
                path: 'sale-add',
                name: 'admin_sale_add',
                component: () => import('@/views/sales/Add.vue'),
                meta: {
                    title: 'Add Sale',
                    permissions: ['sales.create']
                }
            },

            {
                path: 'sale-edit/:id',
                name: 'admin_sale_edit',
                component: () => import('@/views/sales/Edit.vue'),
                meta: {
                    title: 'Edit Sale',
                    permissions: ['sales.update']
                }
            },
            /* ====================== Sale Route END ====================== */

            /* ====================== Ledger Account Route START ====================== */
            {
                path: 'ledger-accounts',
                name: 'admin_ledger_accounts_list',
                component: () => import('@/views/ledger-accounts/Index.vue'),
                meta: {
                    title: 'Chart of Accounts',
                    permissions: ['ledger-accounts.view']
                }
            },

            {
                path: 'ledger-account-add',
                name: 'admin_ledger_account_add',
                component: () => import('@/views/ledger-accounts/Add.vue'),
                meta: {
                    title: 'Add Account',
                    permissions: ['ledger-accounts.create']
                }
            },

            {
                path: 'ledger-account-edit/:id',
                name: 'admin_ledger_account_edit',
                component: () => import('@/views/ledger-accounts/Edit.vue'),
                meta: {
                    title: 'Edit Account',
                    permissions: ['ledger-accounts.update']
                }
            },
            /* ====================== Ledger Account Route END ====================== */

            /* ====================== Internal Fund Transfer Route START ====================== */
            {
                path: 'accounting/internal-fund-transfer',
                name: 'admin_fund_transfer_add',
                component: () => import('@/views/fund-transfers/Add.vue'),
                meta: {
                    title: 'Internal Fund Transfer',
                    permissions: ['fund-transfers.create']
                }
            },

            {
                path: 'accounting/internal-fund-transfer/list',
                name: 'admin_fund_transfers_list',
                component: () => import('@/views/fund-transfers/Index.vue'),
                meta: {
                    title: 'Internal Fund Transfers',
                    permissions: ['fund-transfers.view']
                }
            },
            /* ====================== Internal Fund Transfer Route END ====================== */

            /* ====================== Income/Expense Route START ====================== */
            {
                path: 'income-expenses',
                name: 'admin_income_expenses_list',
                component: () => import('@/views/income-expenses/Index.vue'),
                meta: {
                    title: 'Income & Expenses',
                    permissions: ['income-expenses.view']
                }
            },

            {
                path: 'income-expense-add',
                name: 'admin_income_expense_add',
                component: () => import('@/views/income-expenses/Add.vue'),
                meta: {
                    title: 'Add Income / Expense Entry',
                    permissions: ['income-expenses.create']
                }
            },

            {
                path: 'income-expense-edit/:id',
                name: 'admin_income_expense_edit',
                component: () => import('@/views/income-expenses/Edit.vue'),
                meta: {
                    title: 'Edit Income / Expense Entry',
                    permissions: ['income-expenses.update']
                }
            },
            /* ====================== Income/Expense Route END ====================== */

            {
                path: 'settings',
                name: 'admin_settings',
                component: () => import('@/views/settings/Index.vue'),
                meta: {
                    title: 'Settings',
                    permissions: ['settings.update']
                }
            },

            {
                path: 'profile',
                name: 'admin_profile',
                component: () => import('@/views/Profile.vue'),
                meta: {
                    title: 'Profile',
                }
            },



        ]
    },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
    scrollBehavior(){
        return {
            top: 0
        }
    }
});


// title
router.afterEach((to) => {
    document.title = to.meta.title || 'Admin Dashboard'
})


router.beforeEach(async (to) => {
    const auth = useAuthStore()

    // Wait for auth check on first load, but only once
    if (!auth.authChecked) {
        try {
            await auth.checkAuth()
        } catch (err) {
            // checkAuth action already handles this
        }
    }

    // Protect routes that require auth
    if (to.meta.requiresAuth && !auth.isAuthenticated) {
        return { name: 'admin_login' } // redirect to login
    }

    // Redirect authenticated users away from login page
    if (to.name === 'admin_login' && auth.isAuthenticated) {
        return { name: 'admin_dashboard' } // redirect to dashboard
    }

    // Check for permissions
    if (to.meta.permissions && auth.isAuthenticated) {
        const userPermissions = auth.user?.permissions || [];
        const hasPermission = to.meta.permissions.some(p => userPermissions.includes(p));

        if (!hasPermission) {
            setToast('error', "You don't have permission to access this page.");
            return { name: 'access_block' };
        }
    }

    // Otherwise, allow navigation
})


router.afterEach(() => {
  const toastData = sessionStorage.getItem("toast");
  if (toastData) {
    try {
      const { type, message } = JSON.parse(toastData);
      if (type && message && typeof toast[type] === "function") {
        toast[type](message, {
          autoClose: 3000,
          hideProgressBar: true,
          position: toast.POSITION.TOP_RIGHT,
        });
      } else {
        toast.success(message); // fallback
      }
    } catch (e) {
      toast.success(toastData); // fallback if parsing fails
    }
    sessionStorage.removeItem("toast");
  }
});


export default router;



