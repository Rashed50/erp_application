<template>
    <div class="p-4 border rounded-lg bg-white">

        <div class="row mb-1">
            <label class="col-md-1 col-form-label text-right">Vehicle</label>
            <div class="col-md-3">
                <select v-model="form.veh_auto_id" class="form-select" required>
                    <option v-for="sc in data_for_form.vehicles" :key="sc.veh_id" :value="sc.veh_id">
                        {{ sc.veh_name }}
                    </option>
                </select>
            </div>

            <label class="col-md-1 col-form-label text-right">Year</label>
            <div class="col-md-2">
                <select v-model="form.year" class="form-select">
                    <option v-for="year in years" :key="year" :value="year">
                        {{ year }}
                    </option>
                </select>
            </div>

            <label class="col-md-1 col-form-label text-right">Month</label>
            <div class="col-md-2">
                <select v-model="form.month" class="form-select">
                    <option v-for="(month, index) in months" :key="index" :value="index + 1">
                        {{ month }}
                    </option>
                </select>
            </div>
            <div class="col-md-1">
                <button type="button" @click="searchServicesForListView" class="btn btn-primary">Search</button>
            </div>

        </div>
    </div>
    <hr>

    <!-- Table Date Showing -->
    <div class="row">
        <div class="col-sm-12">
            <table class="table table-bordered custom_table mb-0 dataTable table-hover no-footer">
                <thead>
                    <tr>
                        <th>S.N</th>
                        <th>Plat No.</th>
                        <th>Maintenance By</th>
                        <th>Amount </th>
                        <th>Date</th>
                        <th>Inserted By</th>
                        <th>Inserted At</th>
                        <th>Remarks</th>
                        <th>Manage</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, index) in tableData.data" :key="item.veh_ser_auto_id">

                        <td>
                            {{ index + 1 }}
                        </td>
                        <td> {{ item.veh_plate_number }}</td>
                        <td>{{ item.service_by }} , {{ item.employee_name }}</td>
                        <td>{{ item.payable_amount }} </td>
                        <td>{{ item.start_date }}</td>
                        <td>{{ item.created_by }}</td>
                        <td>{{ item.created_at }}</td>
                        <td>{{ item.remarks == null ? "-" : item.remarks }} </td>
                        <td>
                            <a v-if="item.invoice_file" class="fw-bold"
                                    :href="getAttachmentUrl(item.invoice_file)"
                                    target="_blank"> <i class="fas fa-eye fa-lg view_icon"></i> </a>|

                            <a class="edit_btn" @click="$emit('edit-vehicle', item.veh_ser_auto_id)">
                                <i class="fa fa-pencil-square-o"></i></a>||

                            <a class="mt-2" @click.prevent="confirmDelete(item)">
                                        <i class="fa fa-trash fa-md delete_icon" aria-hidden="true"></i>
                                    </a>
                        </td>
                    </tr>
                    <tr v-if="tableData.data.length === 0">
                        <td colspan="4" class="text-center">No data available in table</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>


</template>

<script>
import axios from "axios";
import { toast } from 'vue3-toastify';
import { VehicleServicing_URLS } from "../router.js";
import Swal from "sweetalert2";

const currentMonth = new Date().getMonth;
const currentYear = new Date().getFullYear();

export default {
    data() {
        return {
            services: [],
            searchQuery: "",
            form: {
                veh_auto_id: '',
                month:'',
                year: '',
            },
            months: [
                'January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'
            ],
            years: [currentYear, currentYear - 1, currentYear - 2, currentYear - 3], // Last 4 years + current year
            tableData: { data: [], total: 0, per_page: 10, current_page: 1, last_page: 1 },
            perPage: 10,
        };
    },
    props: {
        data_for_form: {
            type: Object,
            required: true
        }
    },
    computed: {
        totalPages() {
            return Array.from({ length: this.tableData.last_page }, (_, i) => i + 1);
        }
    },

    mounted() {
       // this.searchServicesForListView();
    },
    methods: {

        async searchServicesForListView() {
            try {

                    const params = {
                        veh_auto_id: this.form.veh_auto_id,
                        month: this.form.month,
                        year: this.form.year
                    };

                    const response1 = await axios.get(VehicleServicing_URLS.SearchServicingAPI, {
                        params: params
                    });
                    if(response1.data.status !== 200){
                        toast.error(response1.data.message || "Failed to fetch records");
                        return;

                    }else{
                        this.tableData.data = response1.data.data;
                        this.form.veh_auto_id = '';
                        this.form.month = '';
                        this.form.year = '';
                    }

            } catch (Error) {

                console.log('error');
                toast.error("Operation Failed , Reload Again")

            }
        },

        getAttachmentUrl(path)
        {
              return (import.meta.env.VITE_AWS_S3_ENDPOINT + path);
        },

        changePage(page) {
            // this.fetchTableData(page);
        },


        editPage(id) {
            //  window.location = `${TicketEdit}/${id}`;
        },

        async confirmDelete(record) {
            const result = await Swal.fire({
                title: 'Are you sure?',
                text: "This will delete the advance record permanently!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: 'Yes, delete it!'
            });

            if (result.isConfirmed) {
                try {
                    // Replace with your actual delete endpoint and payload
                    const response = await axios.get(`${VehicleServicing_URLS.DeleteVechicleMaintenanceRecord}/${record.veh_ser_auto_id}`);

                    if (response.data.status === 200) {
                        // Remove deleted record from list
                        toast.success('Record deleted successfully');
                    } else {
                         toast.error(response.data.message || "Failed to delete record");
                    }
                } catch (error) {
                    // console.error("Delete error:", error);
                     toast.error("An error occurred while deleting");
                }

                toast.success('Record deleted successfully');
                this.searchServicesForListView();
            }
        },


        formatDateTime(date) {
            if (!date) return '';

            // Format the date as DD/MM/YYYY
            const formattedDate = new Intl.DateTimeFormat('en-GB', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            }).format(new Date(date));

            // Format the time as hh:mm AM/PM
            const formattedTime = new Intl.DateTimeFormat('en-US', {
                hour: 'numeric',
                minute: 'numeric',
                hour12: true
            }).format(new Date(date));

            return { formattedDate, formattedTime };
        },

        capitalize(value) {
            if (!value) return '';
            return value.charAt(0).toUpperCase() + value.slice(1).toLowerCase();
        }

    },

};
</script>



<style scoped>
.row {
    margin-top: 10px;
}

.edit_btn {
    cursor: pointer
}

.delete_btn {
    cursor: pointer
}
</style>
