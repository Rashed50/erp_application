<template>
    <div class="row">
        <div class="col-md-12 text-center">
            <button class="btn btn-primary m-2" @click="createNew">New Service</button>
            <button class="btn btn-warning m-2" @click="searchSubcontractor">Search</button>
            <button class="btn btn-danger m-2" @click="showReport" data-target="#report_modal">
                <i class="fa fa-file-text me-2" aria-hidden="true"></i>
                Reports
            </button>
            <button class="btn btn-primary m-2" @click="newServiceName">New Service Name</button>

        </div>
        <hr>
    </div>
    <div class="row">
        <div v-if="showForm == 1">
            <vehicle_servicing :data_for_form=data_for_servicing_form></vehicle_servicing>
        </div>
        <div v-else-if="showForm == 2">
            <search_servicing @edit-vehicle="editPage" :data_for_form=data_for_servicing_form></search_servicing>
        </div>
         <div v-else-if="showForm == 3">
            <vehicle_maintenance_report  :data_for_form=data_for_servicing_form></vehicle_maintenance_report>
        </div>

        <div v-else-if="showForm == 4">
            <vehicle_service_name :data_for_form=data_for_servicing_form></vehicle_service_name>
        </div>


        <div v-else-if="showForm == 5">
            <vehicle_service_edit :vehicleId="currentEditId" :data_for_form=data_for_servicing_form @cancel="handleCancel">
            </vehicle_service_edit>
        </div>
    </div>


</template>

<script>


export default {
    data() {
        return {
            showForm: 1, // Initially 1 = new , 2= search, 3 = report
            currentEditId: null,
        };
    },
    props: ["data_for_servicing_form"], // Receives data from index Blade
    methods: {
        createNew() {
            this.showForm = 1
        },
        searchSubcontractor() {
            this.showForm = 2
        },
        showReport() {
            this.showForm = 3
        },
        newServiceName() {
            this.showForm = 4
        },
        editPage(id) {
            // console.log("Service Id =", id)
            this.currentEditId = id;
            this.showForm = 5;
        },
        handleCancel() {
            this.showForm = 2;
        }
    },

};
</script>

<style scoped>
.m-2 {
    margin: 5px;
}
</style>
