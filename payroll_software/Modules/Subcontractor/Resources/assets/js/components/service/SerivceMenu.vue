<template>
    <div class="row">
        <div class="col-md-12 text-center">
            <button class="btn btn-primary m-2" @click="createNew" >New Service</button>
            <button class="btn btn-warning m-2" @click="searchSubcontractor" v-if="hasPermission('search_service_of_subcontractor')">Search</button>
            <button class="btn btn-danger m-2" @click="showInvoice" v-if="hasPermission('subcontractor_reports_section_menu')"> <i
                    class="fa fa-file-invoice me-2" aria-hidden="true"></i>
                Prepare Invoice
            </button>
        </div>
        <hr>
    </div>
    <div class="row">
        <div v-if="showForm == 1">
            <sc_new_service :data_for_form=service_form_data></sc_new_service>
        </div>

        <div v-else-if="showForm == 2">
            <sc_search_service @edit-service="editPage" :data_for_form=service_form_data>
            </sc_search_service>
        </div>

        <div v-else-if="showForm == 4">
            <sc_edit_service :service-id="currentEditId" :data_for_form=service_form_data @cancel="handleCancel">
            </sc_edit_service>
        </div>

        <div v-else-if="showForm == 5">
            <sc_invoice :data_for_form=service_form_data></sc_invoice>
        </div>
    </div>


</template>

<script>


import { inject } from 'vue';
import { useAuth } from "../../../../../../../resources/js/components/useAuth.js";

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
            showForm: 1, // Initially 1 = new , 2= search, 3 = report
            currentEditId: null,
        };
    },
    props: ["service_form_data"], // Receives data from index Blade
    methods: {
        createNew() {
            this.showForm = 1
        },

        searchSubcontractor() {
            this.showForm = 2
        },

        showInvoice() {
            this.showForm = 5
        },

        editPage(id) {
            // console.log("Service Id =", id)
            this.currentEditId = id;
            this.showForm = 4;
        },

        handleCancel() {
            // Return to search view when cancel is clicked
            this.showForm = 2;
        },
    },

};
</script>

<style scoped>
.m-2 {
    margin: 5px;
}
</style>
