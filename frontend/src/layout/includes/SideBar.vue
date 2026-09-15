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

            <!-- Customer Menu START -->
            <v-list-item class="" v-if="can(['customers.view'])">
                <router-link :to="{ name: 'admin_customers_list' }" class="custom_router_link">
                    <span class="sidebar-menu-icon">
                        <i class="fa-solid fa-people-arrows"></i>
                    </span>
                    Customers
                </router-link>
            </v-list-item>
            <!-- Customer Menu END -->

            <!-- Supplier Menu START -->
            <v-list-item class="" v-if="can(['suppliers.view'])">
                <router-link :to="{ name: 'admin_suppliers_list' }" class="custom_router_link">
                    <span class="sidebar-menu-icon">
                        <i class="fa-solid fa-truck-field"></i>
                    </span>
                    Suppliers
                </router-link>
            </v-list-item>
            <!-- Supplier Menu END -->

            <!-- Purchase Menu START -->
            <v-list-item class="" v-if="can(['purchases.view'])">
                <router-link :to="{ name: 'admin_purchases_list' }" class="custom_router_link">
                    <span class="sidebar-menu-icon">
                        <i class="fa-solid fa-cart-shopping"></i>
                    </span>
                    Purchases
                </router-link>
            </v-list-item>
            <!-- Purchase Menu END -->

            <v-list-item class="" v-if="can(['prescription-list', 'prescription-create', 'prescription-view'])">
                <router-link :to="{ name: 'admin_prescription' }" class="custom_router_link">
                    <span class="sidebar-menu-icon">
                        <i class="fa-solid fa-file-medical"></i>
                    </span>
                    Prescription
                </router-link>
            </v-list-item>

            <v-list-item class="" v-if="can(['customer-list', 'customer-view', 'customer-create'])">
                <router-link :to="{ name: 'admin_customers' }" class="custom_router_link">
                    <span class="sidebar-menu-icon">
                        <i class="fa-solid fa-users"></i>
                    </span>
                    Patient
                </router-link>
            </v-list-item>

            <!-- Medicine (Product) Menu -->
            <v-list-group class="" v-if="
                can(['product-list']) ||
                can(['medical-test-list']) ||
                can(['question-answer-list'])
            ">
                <template v-slot:activator="{ props }">
                    <v-list-item v-bind="props">
                        <div class="custom_dropdown_router_link custom_mb_10">
                            <span class="sidebar-menu-icon">
                                <i class="fa-solid fa-capsules"></i>
                            </span>
                            Medicine
                        </div>
                    </v-list-item>
                </template>
                <div>
                    <router-link :to="{ name: 'admin_products' }" class="custom_router_sub_link"
                        v-if="can(['product-list'])">
                        <span class="ml-3">
                            <span class="dot_list"><i class="fa-solid fa-circle"></i></span>
                            Medicine List
                        </span>
                    </router-link>
                    <!-- <router-link :to="{ name: 'medical_test' }" class="custom_router_sub_link"
                        v-if="can(['medical-test-list'])">
                        <span class="ml-3">
                            <span class="dot_list"><i class="fa-solid fa-circle"></i></span>
                            Medical Test
                        </span>
                    </router-link> -->

                    <router-link :to="{ name: 'durations' }" class="custom_router_sub_link"
                        v-if="can(['question-answer-list'])">
                        <span class="ml-3">
                            <span class="dot_list"><i class="fa-solid fa-circle"></i></span>
                            Durations
                        </span>
                    </router-link>
                </div>
            </v-list-group>

            <!-- Product Purchase START -->
            <v-list-group class="" v-if="can(['purchase-list'])">
                <template v-slot:activator="{ props }">
                    <v-list-item v-bind="props">
                        <div class="custom_dropdown_router_link custom_mb_10">
                            <span class="sidebar-menu-icon">
                                <i class="fa-solid fa-address-card"></i>
                            </span>
                            Stock Manage
                        </div>
                    </v-list-item>
                </template>
                <div>
                    <router-link :to="{ name: 'admin_purchase_list' }" class="custom_router_sub_link">
                        <span class="ml-3">
                            <span class="dot_list"><i class="fa-solid fa-circle"></i></span>
                            Stock In
                        </span>
                    </router-link>
                    <router-link :to="{ name: 'admin_drop_reports' }" class="custom_router_sub_link">
                        <span class="ml-3">
                            <span class="dot_list"><i class="fa-solid fa-circle"></i></span>
                            Stock Report
                        </span>
                    </router-link>
                </div>
            </v-list-group>
            <!-- Product Purchase END -->


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
