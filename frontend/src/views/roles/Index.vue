<template lang="html">
    <Breadcrumb title="Roles" buttonText="Add Role" :buttonLink="{ name: 'admin_role_add' }" />

    <div class="main-content-wrapper mt-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-2"></div>
                <div class="col-md-8">
                    <!-- Table Card -->
                    <v-card style="padding: 5px; margin-top: 15px;">
                        <div class="row">
                            <!-- Data Table START -->
                            <v-table class="custom-bordered">
                                <thead>
                                    <tr>
                                        <th class="text-left">
                                            #
                                        </th>
                                        <th class="text-left">
                                            {{ $t('Name') }}
                                        </th>
                                        <th class="text-center">
                                            {{ $t('Action') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <!-- Loading State with Vuetify Spinner -->
                                    <tr v-if="loading">
                                        <td colspan="6" class="text-center py-4">
                                            <v-progress-linear indeterminate color="primary"
                                                size="30"></v-progress-linear>
                                            {{ $t('Loading...') }}
                                        </td>
                                    </tr>

                                    <!-- No Data -->
                                    <tr v-else-if="!items.length">
                                        <td colspan="6" class="text-center py-4">
                                            {{ $t('No records found.') }}
                                        </td>
                                    </tr>

                                    <!-- Data Rows -->
                                    <tr v-else v-for="(item, index) in items" :key="item.id || index">
                                        <td>{{ index + 1 }}</td>
                                        <td>{{ item.name }}</td>
                                        <!-- actions -->
                                        <td class="text-center">
                                            <v-menu v-if="item.name != 'Super Admin'">
                                                <template v-slot:activator="{
                                                    props,
                                                }">
                                                    <button type="button" class="table-action-button" v-bind="props">
                                                        <i class="fa-solid fa-bars"></i>
                                                    </button>
                                                </template>
                                                <ul class="table-action-menu">
                                                    <li class="menu-item">
                                                        <router-link
                                                            :to="{ name: 'admin_role_edit', params: { id: item.id } }"
                                                            class="menu-link">
                                                            👁️ {{ $t('Edit') }}
                                                        </router-link>
                                                    </li>
                                                </ul>
                                            </v-menu>

                                        </td>
                                    </tr>
                                </tbody>
                            </v-table>
                            <!-- Data Table END -->
                        </div>
                    </v-card>
                </div>
            </div>
        </div>
    </div>

</template>
<script setup>
import Breadcrumb from '@/components/common/Breadcrumb.vue';
import { useFetch } from '@/composables/useFetch';


const { items, loading, error, filters, fetchData } = useFetch('/api/roles', {
    per_page: 100
})


onMounted(() => {
    fetchData()
})

</script>
