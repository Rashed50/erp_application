<template>
    <!-- Menu Bar -->
    <v-navigation-drawer v-model="drawer" class="navigation__drawer">
        <div class="sidebar__header">
            <div class="identity">
                <p><span class="badge rounded-pill bg-success">{{ auth?.user?.roles?.[0] }}</span></p>
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
            <v-list-group value="accounts" v-if="canAny([
                'customers.view', 'sales.view', 'suppliers.view', 'purchases.view',
                'supplier-payments.view', 'ledger-accounts.view', 'income-expenses.view', 'fund-transfers.view',
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

                <!-- Ledger Book -->
                <v-list-group value="accounts-ledger-book"
                    v-if="canAny(['ledger-accounts.view', 'income-expenses.view', 'fund-transfers.view'])">
                    <template v-slot:activator="{ props }">
                        <v-list-item v-bind="props">
                            <div class="custom_dropdown_router_link custom_mb_10 ml-3">
                                <span class="sidebar-menu-icon">
                                    <i class="fa-solid fa-book"></i>
                                </span>
                                Ledger Book
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

                        <router-link :to="{ name: 'admin_income_expenses_list' }" class="custom_router_sub_link"
                            v-if="can(['income-expenses.view'])">
                            <span class="ml-5">
                                <span class="dot_list"><i class="fa-solid fa-circle"></i></span>
                                Income &amp; Expenses
                            </span>
                        </router-link>

                        <router-link :to="{ name: 'admin_fund_transfer_add' }" class="custom_router_sub_link"
                            v-if="can(['fund-transfers.create'])">
                            <span class="ml-5">
                                <span class="dot_list"><i class="fa-solid fa-circle"></i></span>
                                Internal Fund Transfer
                            </span>
                        </router-link>

                        <router-link :to="{ name: 'admin_fund_transfers_list' }" class="custom_router_sub_link"
                            v-if="can(['fund-transfers.view'])">
                            <span class="ml-5">
                                <span class="dot_list"><i class="fa-solid fa-circle"></i></span>
                                Fund Transfer List
                            </span>
                        </router-link>
                    </div>
                </v-list-group>

                <!-- Customer & Sale -->
                <v-list-group value="accounts-customer-sale"
                    v-if="canAny(['customers.view', 'sales.view'])">
                    <template v-slot:activator="{ props }">
                        <v-list-item v-bind="props">
                            <div class="custom_dropdown_router_link custom_mb_10 ml-3">
                                <span class="sidebar-menu-icon">
                                    <i class="fa-solid fa-people-arrows"></i>
                                </span>
                                Customer &amp; Sale
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
                        <router-link :to="{ name: 'admin_sales_list' }" class="custom_router_sub_link"
                            v-if="can(['sales.view'])">
                            <span class="ml-5">
                                <span class="dot_list"><i class="fa-solid fa-circle"></i></span>
                                Sales
                            </span>
                        </router-link>
                    </div>
                </v-list-group>

                <!-- Supplier & Purchase -->
                <v-list-group value="accounts-supplier-purchase"
                    v-if="canAny(['suppliers.view', 'purchases.view', 'supplier-payments.view'])">
                    <template v-slot:activator="{ props }">
                        <v-list-item v-bind="props">
                            <div class="custom_dropdown_router_link custom_mb_10 ml-3">
                                <span class="sidebar-menu-icon">
                                    <i class="fa-solid fa-truck-field"></i>
                                </span>
                                Supplier &amp; Purchase
                            </div>
                        </v-list-item>
                    </template>
                    <div>
                        <router-link :to="{ name: 'admin_suppliers_list' }" class="custom_router_sub_link"
                            v-if="can(['suppliers.view'])">
                            <span class="ml-5">
                                <span class="dot_list"><i class="fa-solid fa-circle"></i></span>
                                Suppliers
                            </span>
                        </router-link>
                        <router-link :to="{ name: 'admin_purchases_list' }" class="custom_router_sub_link"
                            v-if="can(['purchases.view'])">
                            <span class="ml-5">
                                <span class="dot_list"><i class="fa-solid fa-circle"></i></span>
                                Purchases
                            </span>
                        </router-link>
                        <router-link :to="{ name: 'admin_supplier_payment_add' }" class="custom_router_sub_link"
                            v-if="can(['supplier-payments.create'])">
                            <span class="ml-5">
                                <span class="dot_list"><i class="fa-solid fa-circle"></i></span>
                                Purchase Payment
                            </span>
                        </router-link>
                        <router-link :to="{ name: 'admin_supplier_payments_list' }" class="custom_router_sub_link"
                            v-if="can(['supplier-payments.view'])">
                            <span class="ml-5">
                                <span class="dot_list"><i class="fa-solid fa-circle"></i></span>
                                Purchase Payment List
                            </span>
                        </router-link>
                    </div>
                </v-list-group>

                
            </v-list-group>
            <!-- Accounts Menu END -->

            <v-list-item class="" v-if="can(['settings-view'])">
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


                                    <img :src="auth?.user?.image_url" alt="Profile" />

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
import { useRoute, useRouter } from 'vue-router';
const route = useRoute();
const { can } = usePermission()
const canAny = (perms) => perms.some((p) => can([p]))

// drawer state
const drawer = ref(null)
const { static_image_path } = usePaths();

const auth = useAuthStore()
const router = useRouter()

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
