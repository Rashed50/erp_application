<template>
    <div class="row">
        <div class="col-md-12 text-center">
            <button class="btn btn-primary m-2" @click="createNew">
                Add Payment
            </button>
            <button class="btn btn-primary m-2" @click="searchSubcontractor">
                Search
            </button>
            <!-- <button
                class="btn btn-danger m-2"
                @click="showReport"
                data-target="#report_modal"
            >
                <i class="fa fa-file-text me-2" aria-hidden="true"></i>
                Reports
            </button> -->
        </div>
        <hr />
    </div>
    <div class="row">
        <div v-if="showForm == 1">
            <sc_new_payment :data_for_form="payment_form_data"></sc_new_payment>
        </div>
        <div v-else-if="showForm == 2">
            <sc_search_payment
                @edit-payment="editPage"
                :data_for_form="payment_form_data"
            ></sc_search_payment>
        </div>
        <div v-if="showForm == 4">
            <sc_edit_payment
                :payment_id="currentEditId"
                :data_for_form="payment_form_data"
            ></sc_edit_payment>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            showForm: 1, // Initially 1 = new , 2= search, 3 = report, 4 = edit
            currentEditId: null,
        };
    },
    props: ["payment_form_data"], // Receives data from index Blade
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
            // console.log("Payment ID =", id);
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
