<template>
    <div class="table-responsive">
        <div
            id="alltableinfo_wrapper"
            class="dataTables_wrapper dt-bootstrap4 no-footer"
        >
            <!-- Search & Per Page -->
            <!-- <div class="row mb-2">
                <div class="col-sm-12 col-md-6">
                    <label class="d-flex align-items-center gap-1">
                        Show
                        <select
                            v-model="localPerPage"
                            @change="onPerPageChange"
                            class="custom-select custom-select-sm form-control form-control-sm w-auto mx-1"
                        >
                            <option
                                v-for="option in perPageOptions"
                                :key="option"
                                :value="option"
                            >
                                {{ option }}
                            </option>
                        </select>
                        Entries
                    </label>
                </div>

                <div class="col-sm-12 col-md-6">
                    <div id="alltableinfo_filter" class="dataTables_filter">
                        <label
                            ><strong>Search:</strong>
                            <input
                                v-model="localSearch"
                                @input="onSearchChange"
                                type="search"
                                :class="{ 'sky-border': search.length > 0 }"
                                class="form-control form-control-lg"
                                placeholder="Search..."
                            />
                        </label>
                    </div>
                </div>
            </div> -->

            <div class="row">
                <div class="col-sm-12 col-md-6">
                    <div class="dataTables_length" id="alltableinfo_length">
                        <label
                            ><strong>Show </strong>
                            <select
                                v-model="localPerPage"
                                @change="onPerPageChange"
                                class="custom-select custom-select-sm"
                            >
                                <option
                                    v-for="option in perPageOptions"
                                    :key="option"
                                    :value="option"
                                >
                                    {{ option }}
                                </option>
                            </select>
                            <strong> entries</strong>
                        </label>
                    </div>
                </div>
                <div class="col-sm-12 col-md-6">
                    <div id="alltableinfo_filter" class="dataTables_filter">
                        <label
                            ><strong>Search:</strong>
                            <input
                                v-model="localSearch"
                                @input="onSearchChange"
                                type="search"
                                :class="{ 'sky-border': search.length > 0 }"
                                class="form-control form-control-lg"
                                placeholder="Search..."
                            />
                        </label>
                    </div>
                </div>
            </div>

            <!-- Table Date Showing -->
            <div class="row">
                <div class="col-sm-12">
                    <table
                        class="responsive table table-bordered custom_table mb-0 dataTable no-footer"
                    >
                        <thead>
                            <tr>
                                <th v-for="(col, i) in columns" :key="i">
                                    {{ col.label }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="!data.data || !data.data.length">
                                <td
                                    :colspan="columns.length"
                                    class="text-center"
                                >
                                    No data available in table
                                </td>
                            </tr>
                            <tr
                                v-for="(row, rowIndex) in data.data"
                                :key="rowIndex"
                            >
                                <td v-for="col in columns" :key="col.key">
                                    <slot :name="col.key" :row="row">
                                        {{ row[col.key] }}
                                    </slot>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <slot name="footer"></slot>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-2">
                <div>
                    Showing {{ data.from || 0 }} to {{ data.to || 0 }} of
                    {{ data.total || 0 }} entries
                </div>
                <ul class="pagination mb-0">
                    <li
                        class="page-item"
                        :class="{ disabled: !data.prev_page_url }"
                    >
                        <a
                            href="#"
                            class="page-link"
                            @click.prevent="changePage(data.current_page - 1)"
                            >Previous</a
                        >
                    </li>
                    <li
                        class="page-item"
                        v-for="page in totalPages"
                        :key="page"
                        :class="{ active: page === data.current_page }"
                    >
                        <a
                            href="#"
                            class="page-link"
                            @click.prevent="changePage(page)"
                            >{{ page }}</a
                        >
                    </li>
                    <li
                        class="page-item"
                        :class="{ disabled: !data.next_page_url }"
                    >
                        <a
                            href="#"
                            class="page-link"
                            @click.prevent="changePage(data.current_page + 1)"
                            >Next</a
                        >
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, ref, watch } from "vue";

// Props
const props = defineProps({
    data: { type: Object, required: true }, // Laravel pagination response
    columns: { type: Array, required: true }, // [{ key: 'name', label: 'Name' }]
    perPage: { type: Number, default: 10 },
    perPageOptions: { type: Array, default: () => [5, 10, 25, 50, 100] },
    search: { type: String, default: "" },
});

// Emits
const emit = defineEmits(["update:perPage", "update:search", "page-change"]);

const localPerPage = ref(props.perPage);
const localSearch = ref(props.search);

const totalPages = computed(() =>
    props.data.last_page
        ? Array.from({ length: props.data.last_page }, (_, i) => i + 1)
        : []
);

const onPerPageChange = () => emit("update:perPage", localPerPage.value);
const onSearchChange = () => emit("update:search", localSearch.value);
const changePage = (page) => emit("page-change", page);

// Sync props -> local
watch(
    () => props.perPage,
    (val) => (localPerPage.value = val)
);
watch(
    () => props.search,
    (val) => (localSearch.value = val)
);
</script>


<style scoped>
    .btn_width{
        min-width: 50px;
    }
    .filter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
        padding: 10px;
    }
    .filter-grid .mb-3 {
        min-width: 300px;
    }
    .input_h{
        height: 43px;
    }
    .flex-container{
        display: flex;
        flex-direction: row;
        justify-content: start;
        gap: 10px;
    }

    .flex-container a {
        font-size: 17px;
        text-align: center;
        cursor: pointer;
    }

    .view_btn:hover i{
        color: rgb(205, 11, 235);
    }
    .edit_btn:hover i{
        color: rgb(207, 46, 21);
    }
    .form-control {
        border: 2px solid #ccc;
        border-radius: 3px;
        padding: 8px;
        transition: border-color 0.3s;
        min-width: 300px;
    }

    .sky-border {
        border-color: skyblue;
        box-shadow: 0 0 5px rgba(135, 206, 250, 0.5);
    }

    .form-control:focus {
        border-color: dodgerblue;
        outline: none;
        box-shadow: 0 0 5px rgba(30, 144, 255, 0.5);
    }
</style>