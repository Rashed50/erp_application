<template>
    <div>
        <v-card style="padding: 5px; margin-top: 15px;">
            <div class="row align-items-center mb-2">
                <div class="col-md-2">
                    <input type="month" class="form-control" v-model="filters.month" />
                </div>

                <div class="col-md-3">
                    <SearchSelect v-model="filters.department" :items="options.departments" item-value="name"
                        :placeholder="$t('All Departments')" />
                </div>

                <div class="col-md-7">
                    <div class="search-wrapper d-flex align-center gap-2">
                        <v-text-field variant="outlined" density="compact" :placeholder="$t('Employee ID or name...')"
                            v-model="filters.search" hide-details class="flex-grow-1"></v-text-field>
                        <v-btn type="button" @click="fetchData" class="text-none text-white" color="blue-darken-3"
                            rounded="0" variant="flat" min-width="100">
                            {{ $t('Search') }}
                        </v-btn>
                        <v-btn @click.prevent="resetFilters" class="text-none" color="grey-lighten-3" rounded="0"
                            variant="flat" min-width="100">
                            {{ $t('Reload') }}
                        </v-btn>
                    </div>
                </div>
            </div>

            <BasePagination :current-page="pagination.page" :per-page="pagination.perPage" :total="pagination.total"
                :last-page="pagination.lastPage" @page-change="changePage" @per-page-change="changePerPage" />
        </v-card>
    </div>
</template>

<script>
import axios from 'axios';
import BasePagination from '@/components/common/BasePagination.vue';
import SearchSelect from '@/components/common/SearchSelect.vue';

export default {
    name: 'Process',
    emits: ['cancel'],

    components: {
        BasePagination,
        SearchSelect
    },

    data() {
        return {
            loading: false,

            filters: {
                month: this.currentMonth(),
                department: '',
                search: ''
            },

            options: {
                departments: []
            },

            pagination: {
                page: 1,
                perPage: 25,
                total: 0,
                lastPage: 1
            }
        };
    },

    mounted() {
        this.fetchOptions();
        this.fetchData();
    },

    methods: {
        // Current month in local time (YYYY-MM) for <input type="month">
        currentMonth() {
            const now = new Date();
            return new Date(
                now.getTime() - now.getTimezoneOffset() * 60000
            ).toISOString().slice(0, 7);
        },

        // Department list for the dropdown
        async fetchOptions() {
            try {
                const response = await axios.get('/api/hr/employees/options');

                if (response.data?.success) {
                    this.options = response.data.data;
                }
            } catch (error) {
                console.error('Options fetch error:', error);
            }
        },

        // Search button. Put the Process API call here.
        async fetchData() {
            if (this.loading) return;

            this.loading = true;

            try {
                // TODO: call the Process API with this.filters and this.pagination, e.g.
                // const response = await axios.get('/api/...', {
                //     params: { ...this.filters, page: this.pagination.page, per_page: this.pagination.perPage }
                // });
            } catch (error) {
                console.error('Process fetch error:', error);
            } finally {
                this.loading = false;
            }
        },

        // Reload button
        resetFilters() {
            this.filters = {
                month: this.currentMonth(),
                department: '',
                search: ''
            };
            this.pagination.page = 1;
            this.fetchData();
        },

        changePage(page) {
            this.pagination.page = page;
            this.fetchData();
        },

        changePerPage(perPage) {
            this.pagination.perPage = perPage;
            this.pagination.page = 1;
            this.fetchData();
        }
    }
};
</script>