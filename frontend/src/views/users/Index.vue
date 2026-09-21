<template lang="html">
    <Breadcrumb title="All User" :buttons="[
        { text: 'Add User', link: { name: 'admin_user_add' }, icon: 'fa-solid fa-user-plus' },
        { text: 'Roles', link: { name: 'admin_roles' }, icon: 'fa-solid fa-user-shield' }
    ]" />

    <div class="main-content-wrapper mt-4">
        <div class="container-fluid">

            <!-- Table Card -->
            <v-card style="padding: 5px; margin-top: 15px;">
                <div class="row">
                    <div class="col-md-12 mb-2">
                        <div class="row align-items-center">
                            <div class="col-md-8"></div>
                            <div class="col-md-4">
                                <div class="search-wrapper d-flex align-center gap-2">
                                    <v-text-field variant="outlined" density="compact" placeholder="Search..."
                                        v-model="filters.search" hide-details class="flex-grow-1"></v-text-field>
                                    <v-btn type="button" @click="fetchData" class="text-none text-white"
                                        color="blue-darken-3" rounded="0" variant="flat" min-width="100">
                                        Search
                                    </v-btn>
                                    <v-btn @click.prevent="resetFilters" class="text-none" color="grey-lighten-3"
                                        rounded="0" variant="flat" min-width="100">
                                        Reload
                                    </v-btn>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Data Table START -->
                    <v-table class="custom-bordered">
                        <thead>
                            <tr>
                                <th class="text-left">
                                    #
                                </th>
                                <th class="text-left">
                                    Name
                                </th>
                                <th class="text-left">
                                    Email
                                </th>
                                <th class="text-left">
                                    Role
                                </th>
                                <th class="text-center">
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody>

                            <!-- Loading State with Vuetify Spinner -->
                            <tr v-if="loading">
                                <td colspan="6" class="text-center py-4">
                                    <v-progress-linear indeterminate color="primary" size="30"></v-progress-linear>
                                    Loading...
                                </td>
                            </tr>

                            <!-- No Data -->
                            <tr v-else-if="!items.length">
                                <td colspan="6" class="text-center py-4">
                                    No records found.
                                </td>
                            </tr>

                            <!-- Data Rows -->
                            <tr v-else v-for="(item, index) in items" :key="item.id || index">
                                <td>{{ index + 1 }}</td>
                                <td>{{ item.name }}</td>
                                <td>{{ item.email }}</td>
                                <td>
                                    <span v-for="(role, index) in item.roles" :key="index">
                                        {{ role }}<span v-if="index < item.roles.length - 1">, </span>
                                    </span>
                                </td>
                                <!-- actions -->
                                <td class="text-center">
                                    <v-menu>
                                        <template v-slot:activator="{
                                            props,
                                        }">
                                            <button type="button" class="table-action-button" v-bind="props">
                                                <i class="fa-solid fa-bars"></i>
                                            </button>
                                        </template>
                                        <ul class="table-action-menu">
                                            <li class="menu-item">
                                                <router-link :to="{ name: 'admin_user_edit', params: { id: item.id } }"
                                                    class="menu-link">
                                                    Edit User/Assign
                                                </router-link>
                                            </li>
                                        </ul>
                                    </v-menu>
                                </td>
                            </tr>
                        </tbody>
                    </v-table>
                    <!-- Data Table END -->

                    <BasePagination :current-page="pagination.page" :per-page="pagination.perPage"
                        :total="pagination.total" :last-page="pagination.lastPage" @page-change="changePage"
                        @per-page-change="changePerPage" />

                </div>
            </v-card>

        </div>
    </div>

</template>
<script setup>
import BasePagination from '@/components/common/BasePagination.vue';
import Breadcrumb from '@/components/common/Breadcrumb.vue';
import { usePaginatedFetch } from '@/composables/usePaginatedFetch';

const search = ref('');

const {
    items,
    loading,
    error,
    filters,
    pagination,
    fetchData,
    changePage,
    changePerPage,
    resetFilters,
} = usePaginatedFetch('/api/users', {
    search,
})

onMounted(() => {
    fetchData()
})

</script>
