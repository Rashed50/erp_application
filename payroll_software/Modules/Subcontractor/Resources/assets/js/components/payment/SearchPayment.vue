<template>
    <div class="p-4 border rounded-lg bg-white">
        <div class="row mb-1">
            <label class="col-md-2 col-form-label text-right">Subcontractor</label>
            <div class="col-md-3">
                <!-- <select v-model="form.subcon_auto_id" class="form-select" required>
                    <option value="">Select Subcontractor</option>
                    <option v-for="sc in data_for_form.subcontractors" :key="sc.subcon_auto_id"
                        :value="sc.subcon_auto_id">
                        {{ sc.subcon_name }}
                    </option>
                </select> -->

                <Multiselect v-model="form.subcon_auto_id" mode="multiple" :options="subcontractorOptions"
                    value-prop="value" track-by="label" label="label" placeholder="Select Subcontractors"
                    :searchable="true" :close-on-select="false" :clear-on-select="false" :create-option="false"
                    :hide-selected="false" :caret="true" class="multiselect-blue">
                    <template #tag="{ option, handleTagRemove }">
                        <div class="multiselect-tag is-user">
                            {{ option.label }}
                            <span class="multiselect-tag-remove" @click="handleTagRemove(option, $event)">
                                <i class="fa fa-times"></i>
                            </span>
                        </div>
                    </template>
                    <template #multiplelabel="{ values }">
                        <div class="multiselect-multiple-label">
                            {{ values.length }} Subcontractors Selected
                        </div>
                    </template>
                </Multiselect>
            </div>
            <label class="col-md-1 col-form-label text-right">Year</label>
            <div class="col-md-2">
                <select v-model="form.year" class="form-select">

                    <option v-for="year in years" :key="year" :value="year">
                        {{ year }}
                    </option>
                </select>
            </div>

            <label class="col-md-1 col-form-label  text-right">Month</label>
            <div class="col-md-2">
                <select v-model="form.month" class="form-select">
                    <option value="">Select Month</option>
                    <option v-for="(month, index) in months" :key="index" :value="index + 1">
                        {{ month }}
                    </option>
                </select>
            </div>
            <div class="col-md-1">
                <button type="button" @click="searchServicesForListView" class="btn btn-primary">
                    Search
                </button>
            </div>
        </div>
    </div>
    <hr />

    <!-- Table Date Showing -->
    <div class="row">
        <div class="col-sm-12">
            <table class="table table-bordered custom_table mb-0 dataTable table-hover no-footer">
                <thead>
                    <tr>
                        <th>S.N</th>
                        <th>Subcontractor</th>
                        <th>Payment Type</th>
                        <th>Month,Year</th>
                        <th>Amount</th>
                        <th>Created By</th>
                        <th>Created</th>
                        <th>Remarks</th>
                        <th>Manage</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, index) in tableData.data" :key="item.subcon_pay_auto_id">
                        <td>
                            {{ index + 1 }}
                        </td>
                        <td>{{ item.subcon_name }}</td>
                        <td>
                            {{ item.payment_method == 1 ? "CASH" : "BANK" }}
                        </td>
                        <td>
                            {{ this.months[item.month - 1] }},{{ item.year }}
                        </td>
                        <td>{{ item.grand_total }}</td>
                        <td>{{ item.name }}</td>
                        <td>{{ item.created_at }}</td>
                        <td>{{ item.remarks == null ? "-" : item.remarks }}</td>
                        <td>
                            <a v-if="item.payment_file" class="fw-bold" :href="getAttachmentUrl(item.payment_file)"
                                target="_blank">
                                <i class="fa fa-eye"></i>
                            </a>
                            ||

                            <a class="edit_btn" @click="$emit('edit-payment', item.subcon_pay_auto_id)">
                                <i class="fa fa-pencil-square-o"></i>
                            </a>
                            ||

                            <a class="delete_btn" @click="deleteItem(item.subcon_pay_auto_id)">
                                <i class="fa fa-trash fa-md delete_icon"></i></a>
                        </td>
                    </tr>
                    <tr v-if="tableData.data.length === 0">
                        <td colspan="4" class="text-center">
                            No data available in table
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';
import Swal from 'sweetalert2';
import Multiselect from '@vueform/multiselect';
import { Payment_URLS } from "../../routes";
const currentMonth = new Date().getMonth();
const currentYear = new Date().getFullYear();

export default {
    components: { Multiselect },
    data() {
        return {
            services: [],
            searchQuery: "",
            form: {
                subcon_auto_id: [],
                month: currentMonth + 1,
                year: new Date().getFullYear(),
            },
            months: [
                "January",
                "February",
                "March",
                "April",
                "May",
                "June",
                "July",
                "August",
                "September",
                "October",
                "November",
                "December",
            ],
            years: [currentYear, currentYear - 1], // Last 2 years + current year
            tableData: {
                data: [],
                total: 0,
                per_page: 10,
                current_page: 1,
                last_page: 1,
            },
            perPage: 10,
        };
    },
    props: {
        data_for_form: {
            type: Object,
            required: true,
        },
    },
    computed: {
        totalPages() {
            return Array.from(
                { length: this.tableData.last_page },
                (_, i) => i + 1
            );
        },

        subcontractorOptions() {
            if (!this.data_for_form || !this.data_for_form.subcontractors) return [];
            return this.data_for_form.subcontractors.map(sc => ({
                value: sc.subcon_auto_id,
                label: sc.subcon_name
            }))
        }
    },

    mounted() {
        // this.searchServicesForListView();
    },
    methods: {

        async searchServicesForListView() {
            try {
                // this.form.month = 1;
                // this.form.year = 2024;
                //  this.form.subcon_auto_id = 7;

                if (!this.form.subcon_auto_id || this.form.subcon_auto_id.length === 0) {
                    toast.error('Please Select At Least One Subcontractor')
                }
                console.log("Searching with form data:", this.form);

                const response = await axios.post(
                    Payment_URLS.SearchPaymentAPI,
                    this.form
                );
                this.tableData.data = response.data.data;
                // console.log(response);
            } catch (error) {

                console.error("Error fetching services:", error);
            }
        },

        getAttachmentUrl(path) {
            return (import.meta.env.VITE_AWS_S3_ENDPOINT + path);
            // const baseUrl = `${window.location.origin}/storage/`;
            // return `${baseUrl}${path}`;
        },


        changePage(page) {
            // this.fetchTableData(page);
        },

        viewPage(id) {
            // window.location = `${TicketDetails}/${id}`;
        },

        editPage(id) {
            //  window.location = `${TicketEdit}/${id}`;
        },

        formatDateTime(date) {
            if (!date) return "";

            // Format the date as DD/MM/YYYY
            const formattedDate = new Intl.DateTimeFormat("en-GB", {
                day: "2-digit",
                month: "2-digit",
                year: "numeric",
            }).format(new Date(date));

            // Format the time as hh:mm AM/PM
            const formattedTime = new Intl.DateTimeFormat("en-US", {
                hour: "numeric",
                minute: "numeric",
                hour12: true,
            }).format(new Date(date));

            return { formattedDate, formattedTime };
        },

        capitalize(value) {
            if (!value) return "";
            return value.charAt(0).toUpperCase() + value.slice(1).toLowerCase();
        },

        async deleteItem(id) {
            // Show confirmation dialog
            const confirmDelete = await Swal.fire({
                title: 'Are you sure?',
                text: "This will permanently delete the service record!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            });

            if (!confirmDelete.isConfirmed) return;

            try {
                const response = await axios.delete(`${Payment_URLS.DeletePaymentAPI}/${id}`);

                if (response.data.success) {
                    toast.success(response.data.message || 'Payment deleted successfully', {
                        autoClose: 3000,
                        position: toast.POSITION.TOP_RIGHT,
                    });

                    // Refresh data
                    await this.searchServicesForListView();

                } else {
                    toast.error(response.data.message || 'Failed to delete payment', {
                        autoClose: 5000,
                        position: toast.POSITION.TOP_RIGHT,
                    });
                }
            } catch (error) {
                console.error('Error deleting payment:', error);

                let errorMessage = 'An error occurred while deleting';
                if (error.response) {
                    errorMessage = error.response.data.message || errorMessage;
                    if (error.response.status === 404) {
                        errorMessage = 'Payment not found';
                    }
                } else if (error.request) {
                    errorMessage = 'Network error - please check your connection';
                }

                toast.error(errorMessage, {
                    autoClose: 5000,
                    position: toast.POSITION.TOP_RIGHT,
                });
            }
        }
    },
    // computed: {

    // },
    // methods: {

    //     getServiceType(type) {
    //         const types = { 1: "Manpower", 5: "Others", 10: "Reserve" };
    //         return types[type] || "Unknown";
    //     },

    //      searchServicesForListView(){
    //         try {

    //         const response =  axios.post(Service_URLS.SearchServiceAPI,this.form);
    //         this.tableData.data = response.data;
    //         console.log(response);
    //         } catch (error) {
    //             debugger;
    //             console.error("Error fetching services:", error);
    //         }

    //     },
    // },
    // mounted() {
    // },
};
</script>
<style scoped>
.row {
    margin-top: 10px;
}

.delete_btn {
    cursor: pointer
}

.edit_btn {
    cursor: pointer
}
</style>
