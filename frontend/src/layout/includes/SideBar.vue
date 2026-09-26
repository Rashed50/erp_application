<template>
    <!-- Menu Bar -->
    <v-navigation-drawer v-model="drawer" class="navigation__drawer">
        <div class="sidebar__header">
            <img :src="settings.company?.logo_url || logoPlaceholder" :data-placeholder="logoPlaceholder"
                alt="Company Logo" />
            <div class="identity">
                <p class="mb-1 company-name">{{ settings.company?.company_name || 'SN' }}</p>
                <!-- <p><span class="badge rounded-pill bg-success">{{ auth?.user?.roles?.[0] }}</span></p> -->
            </div>
        </div>
        <!-- sidebar body -->
        <v-list dense>
            <!-- menu start -->

            <v-list-item class="">
                <router-link :to="{ name: 'admin_dashboard' }" class="custom_router_link">
                    <span class="sidebar-menu-icon">
                        <i class="fa-solid fa-house"></i>
                    </span>
                    Dashboard
                </router-link>
            </v-list-item>

            <!-- User Menu START -->
            <v-list-item class="" v-if="can(['users.view'])">
                <router-link :to="{ name: 'admin_users' }" class="custom_router_link">
                    <span class="sidebar-menu-icon">
                        <i class="fa-solid fa-address-card"></i>
                    </span>
                    User
                </router-link>
            </v-list-item>
            <!-- User Menu END -->

            <!-- Accounts Menu START -->
            <!-- Grouped and named like the payroll_software Accounting sidebar. -->
            <v-list-group value="accounts" v-if="canAny([
                'ledger-accounts.view', 'supplier-payments.view', 'supplier-payments.create',
                'fund-transfers.view', 'fund-transfers.create', 'customers.view', 'work-orders.view', 'products.view', 'sales.view',
                'sales.create', 'purchases.view', 'purchases.create', 'income-expenses.view',
                'income-expenses.create', 'suppliers.view', 'suppliers.create',
            ])">
                <template v-slot:activator="{ props }">
                    <v-list-item v-bind="props">
                        <div class="custom_dropdown_router_link custom_mb_10">
                            <span class="sidebar-menu-icon">
                                <i class="fa-solid fa-wallet"></i>
                            </span>
                            Accounting
                        </div>
                    </v-list-item>
                </template>

                <!-- Account Setting -->
                <v-list-group value="accounts-setting" v-if="canAny(['ledger-accounts.view'])">
                    <template v-slot:activator="{ props }">
                        <v-list-item v-bind="props">
                            <div class="custom_dropdown_router_link custom_mb_10 ml-3">
                                <span class="sidebar-menu-icon">
                                    <i class="fa-solid fa-diagram-project"></i>
                                </span>
                                Account Setting
                            </div>
                        </v-list-item>
                    </template>
                    <div>
                        <router-link :to="{ name: 'admin_ledger_accounts_list' }" class="custom_router_sub_link"
                            v-if="can(['ledger-accounts.view'])">
                            <span class="ml-5">
                                <span class="dot_list"><i class="fa-solid fa-circle"></i></span>
                                General Ledger
                            </span>
                        </router-link>
                    </div>
                </v-list-group>

                <!-- Payment -->
                <v-list-group value="accounts-payment" v-if="canAny([
                    'supplier-payments.view', 'supplier-payments.create', 'fund-transfers.view', 'fund-transfers.create',
                ])">
                    <template v-slot:activator="{ props }">
                        <v-list-item v-bind="props">
                            <div class="custom_dropdown_router_link custom_mb_10 ml-3">
                                <span class="sidebar-menu-icon">
                                    <i class="fa-solid fa-money-bill-transfer"></i>
                                </span>
                                Payment
                            </div>
                        </v-list-item>
                    </template>
                    <div>
                        <router-link :to="{ name: 'admin_supplier_payment_add' }" class="custom_router_sub_link"
                            v-if="can(['supplier-payments.create'])">
                            <span class="ml-5">
                                <span class="dot_list"><i class="fa-solid fa-circle"></i></span>
                                Supplier Payment
                            </span>
                        </router-link>
                        <router-link :to="{ name: 'admin_supplier_payments_list' }" class="custom_router_sub_link"
                            v-if="can(['supplier-payments.view'])">
                            <span class="ml-5">
                                <span class="dot_list"><i class="fa-solid fa-circle"></i></span>
                                Supplier Payment List
                            </span>
                        </router-link>
                        <router-link :to="{ name: 'admin_fund_transfer_add' }" class="custom_router_sub_link"
                            v-if="can(['fund-transfers.create'])">
                            <span class="ml-5">
                                <span class="dot_list"><i class="fa-solid fa-circle"></i></span>
                                Internal Transfer
                            </span>
                        </router-link>
                        <router-link :to="{ name: 'admin_fund_transfers_list' }" class="custom_router_sub_link"
                            v-if="can(['fund-transfers.view'])">
                            <span class="ml-5">
                                <span class="dot_list"><i class="fa-solid fa-circle"></i></span>
                                Internal Transfer List
                            </span>
                        </router-link>
                    </div>
                </v-list-group>

                <!-- Sales -->
                <v-list-group value="accounts-sales"
                    v-if="canAny(['customers.view', 'work-orders.view', 'products.view', 'sales.create', 'sales.view'])">
                    <template v-slot:activator="{ props }">
                        <v-list-item v-bind="props">
                            <div class="custom_dropdown_router_link custom_mb_10 ml-3">
                                <span class="sidebar-menu-icon">
                                    <i class="fa-solid fa-money-bill"></i>
                                </span>
                                Sales
                            </div>
                        </v-list-item>
                    </template>
                    <div>
                        <router-link :to="{ name: 'admin_customers_list' }" class="custom_router_sub_link"
                            v-if="can(['customers.view'])">
                            <span class="ml-5">
                                <span class="dot_list"><i class="fa-solid fa-circle"></i></span>
                                Customers
                            </span>
                        </router-link>
                        <router-link :to="{ name: 'admin_work_orders_list' }" class="custom_router_sub_link"
                            v-if="can(['work-orders.view'])">
                            <span class="ml-5">
                                <span class="dot_list"><i class="fa-solid fa-circle"></i></span>
                                Work Order
                            </span>
                        </router-link>
                        <router-link :to="{ name: 'admin_products_list' }" class="custom_router_sub_link"
                            v-if="can(['products.view'])">
                            <span class="ml-5">
                                <span class="dot_list"><i class="fa-solid fa-circle"></i></span>
                                Products
                            </span>
                        </router-link>
                        <router-link :to="{ name: 'admin_sale_add' }" class="custom_router_sub_link"
                            v-if="can(['sales.create'])">
                            <span class="ml-5">
                                <span class="dot_list"><i class="fa-solid fa-circle"></i></span>
                                New Sales
                            </span>
                        </router-link>
                        <router-link :to="{ name: 'admin_sales_list' }" class="custom_router_sub_link"
                            v-if="can(['sales.view'])">
                            <span class="ml-5">
                                <span class="dot_list"><i class="fa-solid fa-circle"></i></span>
                                Search Sales
                            </span>
                        </router-link>
                    </div>
                </v-list-group>

                <!-- Purchase -->
                <v-list-group value="accounts-purchase" v-if="canAny(['purchases.create', 'purchases.view'])">
                    <template v-slot:activator="{ props }">
                        <v-list-item v-bind="props">
                            <div class="custom_dropdown_router_link custom_mb_10 ml-3">
                                <span class="sidebar-menu-icon">
                                    <i class="fa-solid fa-cart-shopping"></i>
                                </span>
                                Purchase
                            </div>
                        </v-list-item>
                    </template>
                    <div>
                        <router-link :to="{ name: 'admin_purchase_add' }" class="custom_router_sub_link"
                            v-if="can(['purchases.create'])">
                            <span class="ml-5">
                                <span class="dot_list"><i class="fa-solid fa-circle"></i></span>
                                New Purchase
                            </span>
                        </router-link>
                        <router-link :to="{ name: 'admin_purchases_list' }" class="custom_router_sub_link"
                            v-if="can(['purchases.view'])">
                            <span class="ml-5">
                                <span class="dot_list"><i class="fa-solid fa-circle"></i></span>
                                Purchase List
                            </span>
                        </router-link>
                    </div>
                </v-list-group>

                <!-- Daily Expense -->
                <v-list-group value="accounts-daily-expense"
                    v-if="canAny(['income-expenses.create', 'income-expenses.view'])">
                    <template v-slot:activator="{ props }">
                        <v-list-item v-bind="props">
                            <div class="custom_dropdown_router_link custom_mb_10 ml-3">
                                <span class="sidebar-menu-icon">
                                    <i class="fa-solid fa-receipt"></i>
                                </span>
                                Daily Expense
                            </div>
                        </v-list-item>
                    </template>
                    <div>
                        <router-link :to="{ name: 'admin_income_expense_add' }" class="custom_router_sub_link"
                            v-if="can(['income-expenses.create'])">
                            <span class="ml-5">
                                <span class="dot_list"><i class="fa-solid fa-circle"></i></span>
                                Add New Expense
                            </span>
                        </router-link>
                        <router-link :to="{ name: 'admin_income_expenses_list' }" class="custom_router_sub_link"
                            v-if="can(['income-expenses.view'])">
                            <span class="ml-5">
                                <span class="dot_list"><i class="fa-solid fa-circle"></i></span>
                                Search Expense
                            </span>
                        </router-link>
                    </div>
                </v-list-group>

                <!-- Suppliers -->
                <v-list-group value="accounts-suppliers" v-if="canAny(['suppliers.view', 'suppliers.create'])">
                    <template v-slot:activator="{ props }">
                        <v-list-item v-bind="props">
                            <div class="custom_dropdown_router_link custom_mb_10 ml-3">
                                <span class="sidebar-menu-icon">
                                    <i class="fa-solid fa-truck-field"></i>
                                </span>
                                Suppliers
                            </div>
                        </v-list-item>
                    </template>
                    <div>
                        <router-link :to="{ name: 'admin_suppliers_list' }" class="custom_router_sub_link"
                            v-if="can(['suppliers.view'])">
                            <span class="ml-5">
                                <span class="dot_list"><i class="fa-solid fa-circle"></i></span>
                                List
                            </span>
                        </router-link>
                        <router-link :to="{ name: 'admin_supplier_add' }" class="custom_router_sub_link"
                            v-if="can(['suppliers.create'])">
                            <span class="ml-5">
                                <span class="dot_list"><i class="fa-solid fa-circle"></i></span>
                                Add
                            </span>
                        </router-link>
                    </div>
                </v-list-group>
            </v-list-group>
            <!-- Accounts Menu END -->

            <!-- HR Menu START -->
            <v-list-group value="hr" v-if="canAny([
                'employees.view', 'employees.create', 'employee-works.view', 'employee-works.create',
                'payroll.view', 'payroll.generate', 'hr-reports.view',
            ])">
                <template v-slot:activator="{ props }">
                    <v-list-item v-bind="props">
                        <div class="custom_dropdown_router_link custom_mb_10">
                            <span class="sidebar-menu-icon">
                                <i class="fa-solid fa-people-group"></i>
                            </span>
                            HR
                        </div>
                    </v-list-item>
                </template>
                <div>
                    <router-link :to="{ name: 'admin_hr_dashboard' }" class="custom_router_sub_link"
                        v-if="canAny(['employees.view', 'payroll.view'])">
                        <span class="ml-5">
                            <span class="dot_list"><i class="fa-solid fa-circle"></i></span>
                            HR Dashboard
                        </span>
                    </router-link>
                    <router-link :to="{ name: 'admin_hr_employees_list' }" class="custom_router_sub_link"
                        v-if="can(['employees.view'])">
                        <span class="ml-5">
                            <span class="dot_list"><i class="fa-solid fa-circle"></i></span>
                            Employees
                        </span>
                    </router-link>
                    <router-link :to="{ name: 'admin_hr_employee_add' }" class="custom_router_sub_link"
                        v-if="can(['employees.create'])">
                        <span class="ml-5">
                            <span class="dot_list"><i class="fa-solid fa-circle"></i></span>
                            Add Employee
                        </span>
                    </router-link>
                    <router-link :to="{ name: 'admin_hr_works_entry' }" class="custom_router_sub_link"
                        v-if="canAny(['employee-works.create', 'employee-works.update'])">
                        <span class="ml-5">
                            <span class="dot_list"><i class="fa-solid fa-circle"></i></span>
                            Monthly Work Entry
                        </span>
                    </router-link>
                    <router-link :to="{ name: 'admin_hr_works_list' }" class="custom_router_sub_link"
                        v-if="can(['employee-works.view'])">
                        <span class="ml-5">
                            <span class="dot_list"><i class="fa-solid fa-circle"></i></span>
                            Work History
                        </span>
                    </router-link>
                    <router-link :to="{ name: 'admin_hr_payroll_generate' }" class="custom_router_sub_link"
                        v-if="can(['payroll.generate'])">
                        <span class="ml-5">
                            <span class="dot_list"><i class="fa-solid fa-circle"></i></span>
                            Generate Salary
                        </span>
                    </router-link>
                    <router-link :to="{ name: 'admin_hr_salary_sheet' }" class="custom_router_sub_link"
                        v-if="can(['payroll.view'])">
                        <span class="ml-5">
                            <span class="dot_list"><i class="fa-solid fa-circle"></i></span>
                            Salary Sheet
                        </span>
                    </router-link>
                    <router-link :to="{ name: 'admin_hr_reports' }" class="custom_router_sub_link"
                        v-if="can(['hr-reports.view'])">
                        <span class="ml-5">
                            <span class="dot_list"><i class="fa-solid fa-circle"></i></span>
                            Reports
                        </span>
                    </router-link>
                </div>
            </v-list-group>
            <!-- HR Menu END -->

            <v-list-item class="" v-if="can(['settings.update'])">
                <router-link :to="{ name: 'admin_settings' }" class="custom_router_link">
                    <span class="sidebar-menu-icon">
                        <i class="fa-solid fa-gear"></i>
                    </span>
                    Settings
                </router-link>
            </v-list-item>


            <v-list-item class="">
                <button type="button" @click="logoutAccount" class="custom_router_link">
                    <span class="sidebar-menu-icon">
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </span>
                    Logout
                </button>
            </v-list-item>


            <!-- menu end -->
        </v-list>
        <!-- sidebar body -->
    </v-navigation-drawer>

    <!-- =========================================== header part =========================================== -->
    <v-app-bar>
        <div class="header_toggle_icon">
            <i class="fa-solid fa-bars" @click="toggleDrawer"></i>
        </div>

        <div class="header_right_side_wrapper">

            <div class="custom_header_top_company_content">

                <v-menu>
                    <template v-slot:activator="{ props }">
                        <div class="app-bar-menu-icon">
                            <span v-bind="props">
                                <div class="d-flex justify-center">

                                    <div class="mt-2 mr-2">
                                        {{ auth?.user?.name }}
                                    </div>


                                    <img :src="auth?.user?.image_url || userPlaceholder" :data-placeholder="userPlaceholder"
                                        alt="Profile" />

                                </div>
                            </span>
                        </div>
                    </template>

                    <div class="menu-list">
                        <ul>
                            <li>
                                <router-link :to="{ name: 'admin_profile' }">Profile</router-link>
                            </li>
                            <li>
                                <button type="button" @click="logoutAccount">Logout</button>
                            </li>
                        </ul>
                    </div>
                </v-menu>
            </div>
        </div>
    </v-app-bar>
    <!-- =========================================== header part =========================================== -->


</template>

<script setup>
import { usePermission } from '@/composables/usePermission';
import { setToast } from "@/helpers/toast";
import { useAuthStore } from '@/stores/auth';
import { useSettingStore } from '@/stores/settings';
import { logoPlaceholder, userPlaceholder } from '@/helpers/imagePlaceholder';
import { useRoute, useRouter } from 'vue-router';
const route = useRoute();
const { can } = usePermission()
const canAny = (perms) => perms.some((p) => can([p]))

// drawer state
const drawer = ref(null)
const { static_image_path } = usePaths();

const auth = useAuthStore()
const router = useRouter()
const settings = useSettingStore()

onMounted(() => {
    if (!settings.companyLoaded) {
        settings.fetchCompany()
    }
})

const logoutAccount = async () => {
    try {
        await auth.logout()
        setToast('success', 'Successfully Logout Account!')
        router.push({ name: 'admin_login' })
    } catch (err) {
        setToast('error', err)
        console.log(err);
    }
}

watch(
    () => route.name,
    (newRoute) => {
        const hiddenRoutes = ['admin_prescription', 'admin_profile'];
        if (hiddenRoutes.includes(newRoute)) {
            drawer.value = false;
        } else {
            drawer.value = true;
        }
    },
    { immediate: true }
);


const toggleDrawer = () => {
    drawer.value = !drawer.value;
};

</script>

<style scoped>
.sidebar__header img {
    object-fit: contain;
    background: #fff;
}

.sidebar__header .identity .company-name {
    font-size: 15px;
    font-weight: 600;
    word-break: break-word;
}

.header_right_side_wrapper {
    display: flex;
    align-items: center;
}

.custom_header_top_language_content {
    padding-right: 15px;
}

.__custom__header__top__language {
    background-color: #ecedfd;
    border-radius: 30px;
    display: flex;
    align-items: center;
}

.__custom__header__top__language__english {
    font-size: 12px;
    padding: 1px 5px;
}

.__custom__header__top__language__bangla {
    font-size: 12px;
    padding: 1px 5px;
}

.language__select__active {
    background-color: #464deb;
    color: #ffffff;
    border-radius: 30px;
}

.custom_header_top_company_content {
    border-left: 1px solid #dddddd;
    text-align: center;
    padding-left: 15px;
}

p.custom-divider {
    color: #fff;
    border-bottom: 1px solid;
    margin: 7px 0;
    padding: 7px 0 7px 10px;
}

a.router-link-active.router-link-exact-active {
    background: #000;
}
</style>
