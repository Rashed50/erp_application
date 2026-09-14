<template>
    <v-app id="inspire">
        <!-- sidebar -->
        <SideBar></SideBar>
        <!-- Page Content -->
        <v-main color="green">
            <router-view></router-view>
        </v-main>

        <!-- Footer -->
        <v-footer padless app inset style="padding: 0!important;">
            <div class="px-4 py-2 bg-gray text-center w-100">
                {{ new Date().getFullYear() }} — <strong> Develop By CERRS</strong>
            </div>
        </v-footer>

    </v-app>
</template>

<script>
import SideBar from './includes/SideBar.vue';
export default {
    components: {
        SideBar,
    },
}
</script>
