<!-- Modules/Payroll/Resources/assets/js/components/Table/DataTableComponent.vue -->
<template>
    <div>
        <div class="table-responsive">
            <div
                id="alltableinfo_wrapper"
                class="dataTables_wrapper dt-bootstrap4 no-footer"
            >
                <!-- Search and Page Set -->
                <div class="row">
                    <!-- Page Set -->
                    <div class="col-sm-12 col-md-6">
                        <div class="dataTables_length" id="alltableinfo_length">
                            <label>
                                <strong>Show </strong>
                                <select
                                    v-model="localPageSize"
                                    @change="
                                        $emit('page-size-change', localPageSize)
                                    "
                                    class="custom-select custom-select-sm"
                                    style="width: 80px"
                                >
                                    <option>10</option>
                                    <option>25</option>
                                    <option>50</option>
                                    <option>100</option>
                                </select>
                                <strong> entries</strong>
                            </label>
                        </div>
                    </div>

                    <!-- Search -->
                    <div class="col-sm-12 col-md-6">
                        <div id="alltableinfo_filter" class="dataTables_filter">
                            <label>
                                <strong>Search:</strong>
                                <input
                                    v-model="localSearch"
                                    @input="handleSearchInput"
                                    type="search"
                                    class="form-control form-control-lg min_height search-input-custom"
                                    :placeholder="searchPlaceholder"
                                />
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Table Content -->
                <div class="row">
                    <table
                        class="table table-bordered custom_table mb-0 table-hover no-footer"
                    >
                        <colgroup>
                            <slot name="colgroup">
                                <col
                                    v-for="(col, i) in columns"
                                    :key="i"
                                    :style="{ width: col.width || 'auto' }"
                                />
                            </slot>
                        </colgroup>

                        <thead>
                            <tr>
                                <th v-for="col in columns" :key="col.field">
                                    {{ col.title }}
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            <!-- Loading -->
                            <tr v-if="loading">
                                <td
                                    :colspan="columns.length"
                                    class="text-center"
                                >
                                    <i
                                        class="fas fa-spinner fa-spin fa-2x text-blue-800"
                                    ></i>
                                    <p class="mt-3 text-muted">
                                        Loading data...
                                    </p>
                                </td>
                            </tr>

                            <!-- No Data -->
                            <tr v-else-if="data.length === 0">
                                <td
                                    :colspan="columns.length"
                                    class="text-center"
                                >
                                    <slot name="no-data">
                                        <i class="fas fa-inbox fa-3x mb-3"></i
                                        ><br />
                                        No data found
                                    </slot>
                                </td>
                            </tr>

                            <!-- Rows -->
                            <tr
                                v-for="(row, index) in data"
                                :key="row.id || index"
                            >
                                <td
                                    v-for="col in columns"
                                    :key="col.field"
                                    :class="[
                                        'tailwind-table-cell py-2 px-3',
                                        col.bodyClass || '',
                                    ]"
                                >
                                    <slot
                                        :name="`cell-${col.field}`"
                                        :row="row"
                                        :index="index"
                                    >
                                        {{ row[col.field] || "-" }}
                                    </slot>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Data Count and Pagination -->
                <div class="row" v-if="totalRows > 0">
                    <div class="col-sm-12 col-md-5">
                        <div
                            class="dataTables_info"
                            role="status"
                            aria-live="polite"
                        >
                            <strong>
                                Showing
                                {{ (currentPage - 1) * pageSize + 1 }} to
                                {{
                                    Math.min(currentPage * pageSize, totalRows)
                                }}
                                of {{ totalRows }} entries
                            </strong>
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-7">
                        <div class="dataTables_paginate paging_simple_numbers">
                            <ul class="pagination">
                                <li
                                    class="paginate_button page-item"
                                    :class="{ disabled: currentPage === 1 }"
                                >
                                    <a
                                        class="page-link"
                                        href="#"
                                        @click.prevent="
                                            $emit(
                                                'page-change',
                                                currentPage - 1
                                            )
                                        "
                                        >Previous</a
                                    >
                                </li>

                                <li
                                    class="paginate_button page-item"
                                    v-for="n in visiblePages"
                                    :key="n"
                                    :class="{
                                        active: n === currentPage,
                                        disabled: n === '...',
                                    }"
                                >
                                    <a
                                        class="page-link"
                                        href="#"
                                        @click.prevent="$emit('page-change', n)"
                                        >{{ n }}</a
                                    >
                                </li>

                                <li
                                    class="paginate_button page-item"
                                    :class="{
                                        disabled: currentPage === totalPages,
                                    }"
                                >
                                    <a
                                        class="page-link"
                                        href="#"
                                        @click.prevent="
                                            $emit(
                                                'page-change',
                                                currentPage + 1
                                            )
                                        "
                                        >Next</a
                                    >
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        columns: { type: Array, required: true },
        data: { type: Array, default: () => [] },
        loading: { type: Boolean, default: false },
        currentPage: { type: Number, default: 1 },
        totalPages: { type: Number, default: 1 },
        totalRows: { type: Number, default: 0 },
        pageSize: { type: Number, default: 10 },
        searchQuery: { type: String, default: "" },

        // Search placeholder 
        searchPlaceholder: {
            type: String,
            default: "Search...",
        },

        debounceDelay: {
            type: Number,
            default: 500,
        },
    },

    emits: ["page-change", "page-size-change", "search-change"],

    data() {
        return {
            localSearch: this.searchQuery,
            localPageSize: this.pageSize,
            debounceTimer: null,
        };
    },

    computed: {
        visiblePages() {
            const delta = 2;
            const range = [];
            const current = this.currentPage;
            const last = this.totalPages;

            for (
                let i = Math.max(2, current - delta);
                i <= Math.min(last - 1, current + delta);
                i++
            ) {
                range.push(i);
            }
            if (current - delta > 2) range.unshift("...");
            if (current + delta < last - 1) range.push("...");
            range.unshift(1);
            if (last > 1) range.push(last);

            return range.filter((v, i, a) => a.indexOf(v) === i);
        },
    },

    methods: {
        handleSearchInput() {
            clearTimeout(this.debounceTimer);
            this.debounceTimer = setTimeout(() => {
                this.$emit("search-change", this.localSearch.trim());
            }, this.debounceDelay);
        },
    },

    watch: {
        searchQuery(val) {
            this.localSearch = val;
        },
        pageSize(val) {
            this.localPageSize = val;
        },
    },
};
</script>

<style scoped>
.search-input-custom {
    min-width: 200px;
}

@media (max-width: 576px) {
    .search-input-custom {
        min-width: 140px !important;
    }
}

@media (min-width: 577px) and (max-width: 768px) {
    .search-input-custom {
        min-width: 180px !important;
    }
}

@media (min-width: 769px) {
    .search-input-custom {
        min-width: 300px !important;
    }
}
</style>
