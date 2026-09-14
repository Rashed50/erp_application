<template>
    <div>
        <!-- breadcrumb -->
        <div class="row">
            <div class="col-md-6">
                <h4>Sale List</h4>
            </div>
            <div class="col-md-6 text-right">
                <!-- <button type="button" class="btn btn-success"></button> -->
            </div>
        </div>
        <div class="card mt-2">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <!-- Search and Page Set -->
                        <!-- <div class="row">
                            <div class="col-sm-12 col-md-6">
                                <div class="dataTables_length" id="alltableinfo_length">
                                    <label><strong>Show </strong>
                                        <select v-model="perPage" @change="fetchTableData"
                                            class="custom-select custom-select-sm">
                                            <option value="10">10</option>
                                            <option value="25">25</option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>
                                        </select>
                                        <strong> entries</strong>
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <div id="alltableinfo_filter" class="dataTables_filter">
                                    <label><strong>Search:</strong>
                                        <input v-model="search" @input="fetchTableData" type="search"
                                            :class="{ 'sky-border': search.length > 0 }"
                                            class="form-control form-control-lg" placeholder="Search...">
                                    </label>
                                </div>
                            </div>
                        </div> -->

                        <table class="table table-bordered custom_table mb-0 dataTable table-hover no-footer">
                            <thead>
                                <tr>
                                    <th>Ref.</th>
                                    <th>Invoice No.</th>
                                    <th>Total</th>
                                    <th>Vat</th>
                                    <th>Retention</th>
                                    <th>Grand Total</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, index) in data.sales" :key="index">
                                    <td>{{ item.sr_auto_id }}</td>
                                    <td>{{ item.sr_invoice_no }}</td>
                                    <td>{{ item.sr_total_amount }}</td>
                                    <td>{{ item.sr_vat_amount }}</td>
                                    <td>{{ item.retention_amount }}</td>
                                    <td>{{ item.sr_grand_total_amount }}</td>
                                    <td>
                                        <span class="badge badge-success" v-if="item.is_draft == 0">Posted</span>
                                        <span class="badge badge-danger" v-else>In Draft</span>
                                    </td>
                                    <td>
                                        <div class="flex-container">
                                            <a class="view_btn" v-if="hasPermission('accounts_sales_invoice_view')"  @click="viewPage(item.sr_auto_id)">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a v-if="item.is_draft == 1 && hasPermission('accounts_sales_invoice_edit') " class="edit_btn"
                                                @click="editPage(item.sr_auto_id)">
                                                <i class="fa fa-pencil-square-o"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>



<script>
import { SalesDetails, SalesEdit } from "../../routes";
    // role and permission composable
    import { inject } from 'vue';
    import {useAuth} from "../../../../../../../resources/js/components/useAuth.js";
    const auth = inject('auth')

export default {
        setup() {
            const { hasPermission } = useAuth();

            return {
                hasPermission,
            };
        },

    props: ['data'],

    data() {
        return {

        };
    },

    methods: {
        viewPage(id) {
            window.location = SalesDetails + '/' + id;
        },

        editPage(id) {
            window.location = SalesEdit + '/' + id;
        },
    },
}


</script>


<style scoped>
.flex-container{
        display: flex;
        flex-direction: row;
        justify-content: start;
        gap:10px;
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
</style>
