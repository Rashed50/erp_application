<template>
    <div class="row">
        <div class="col-md-12 text-center">
            <button class="btn btn-primary m-2" @click="createNew" v-if="hasPermission('add_new_subcontractor')" >Add New</button>
            <button class="btn btn-primary m-2" @click="searchSubcontractor" v-if="hasPermission('search_subcontractor')">

                Search
            </button>
            <!-- <button
                class="btn btn-danger m-2"
                @click="showReport"
                data-target="#report_modal" v-if="hasPermission('subcontractor_reports_section_menu')">
                <i class="fa fa-file-text me-2" aria-hidden="true" ></i>
                Reports
            </button> -->

            <!-- <button @click.prevent="showReport()" data-toggle="modal"
                                    data-target="#report_modal"
                                    :class="{ 'btn-primary': currentSection === 'report', 'btn-outline-primary': currentSection !== 'report' }"
                                    class="btn me-2">
                                    <i class="fa fa-file-text me-2" aria-hidden="true"></i>
                                    Report
                                </button> -->
        </div>
        <hr />
    </div>
    <div class="row">
        <div v-if="showForm == 1">
            <subcontractor-create
                :data_for_form="data_for_subcontractor_form"
            ></subcontractor-create>
        </div>

        <div v-else-if="showForm == 2">
            <subcontractor_search
                @edit-subcontractor="editPage"
            ></subcontractor_search>
        </div>

        <!-- <div v-else-if="showForm == 3">
            <subcontractor_all_reports
                :data_for_form="data_for_subcontractor_form"
            >
            </subcontractor_all_reports>
        </div> -->

         <div v-else-if="showForm == 4">
            <subcontractor-edit
                :subcontractor-id="currentEditId"
                :data_for_form="data_for_subcontractor_form"
            >
            </subcontractor-edit>
        </div>


    </div>

    <!-- Modal -->
    <div
        class="modal fade"
        id="report_modal"
        tabindex="-1"
        role="dialog"
        aria-hidden="true"
    >
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Report Download</h5>
                    <button
                        type="button"
                        class="close"
                        data-dismiss="modal"
                        aria-label="Close"
                    >
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form
                        method="POST"
                        action=""
                        @submit.prevent="reportDownload"
                        target="_blank"
                    >
                        <div class="d-flex gap-2 mb-2 w-100">
                            <div class="w-100">
                                <label for="">From:</label>
                                <input
                                    type="date"
                                    required
                                    class="form-control w-100"
                                />
                            </div>
                            <div class="w-100">
                                <label for="">To:</label>
                                <input
                                    type="date"
                                    required
                                    class="form-control w-100"
                                />
                            </div>
                        </div>

                        <div class="mb-2">
                            <label for="">Ticket Type:</label>
                            <select class="form-select" required>
                                <option value="">Select Project</option>
                                <option value="1">Ticket Details</option>
                                <option value="2">Ticket Summary</option>
                            </select>
                        </div>

                        <div class="mt-4 d-flex justify-content-end">
                            <button
                                type="button"
                                class="btn btn-secondary me-2"
                                data-dismiss="modal"
                            >
                                <i
                                    class="fa fa-times me-2"
                                    aria-hidden="true"
                                ></i>
                                Close
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i
                                    class="fa fa-arrow-down me-2"
                                    aria-hidden="true"
                                ></i>
                                Download
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { inject } from 'vue';
import { useAuth } from "../../../../../../resources/js/components/useAuth.js";

const auth = inject('auth')

export default {
    setup() {
        const { hasPermission } = useAuth();

        return {
            hasPermission,
        };
    },
    data() {
        return {
            showForm: 1, // Initially 1 = new , 2= search, 3 = report, 4 = edit
            currentEditId: null,
        };
    },
    props: ["data_for_subcontractor_form"], // Receives data from index Blade
    methods: {
        createNew() {
            this.showForm = 1;
        },

        searchSubcontractor() {
            this.showForm = 2;
        },
        showReport() {
            this.showForm = 3;
        },

        editPage(id) {
            this.currentEditId = id;
            this.showForm = 4;
        },
    },
};
</script>

<style scoped>
.m-2 {
    margin: 5px;
}
</style>
