<template>
  <div class="row mt-1 justify-content-center align-items-center">
    <div class="col-md-2">
      <label for="Search By" class="col-md-2  text-right"> Search</label>
    </div>
    <div class="col-md-6">
      <input type="text" v-model="searchQuery" class="form-control" placeholder="Enter search term..." />
    </div>
    <div class="col-md-4">
      <button class="btn btn-primary" @click="performSearch">Search</button>
    </div>
  </div>

  <!-- Table Date Showing -->
  <div class="row">
    <div class="col-sm-12">
      <table class="table table-bordered custom_table mb-0 dataTable table-hover no-footer">
        <thead>
          <tr>
            <th>S.N</th>
            <th>ID </th>
            <th>Name</th>
            <th>Sponsor </th>
            <th>Passport</th>
            <th>Iqama</th>
            <th>Mobile</th>
            <th>Home</th>
            <th>Present</th>
            <th>Joined</th>
            <th>Created</th>
            <!-- <th>Remarks</th> -->
            <th>Manage</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, index) in tableData.data" :key="item.subcon_auto_id">
            <td>
              {{
                (tableData.current_page - 1) * tableData.per_page + index + 1
              }}
            </td>

            <td>{{ item.id_number }}</td>
            <td> {{ capitalize(item.subcon_name) }}</td>
            <td>{{ capitalize(item.spons_name) }}</td>
            <td>{{ item.passfort_no }}</td>
            <td>{{ item.iqama_no }}, {{ item.iqama_expire }}</td>
            <td>{{ item.mobile_no }} , {{ item.abshar_mobile_no }}</td>
            <td>{{ item.post_code }}, {{ item.details }}</td>
            <td>{{ item.present_address }}</td>
            <td>{{ item.joining_date }}</td>
            <td>{{ item.created_by }}</td>
            <!-- <td>{{ item.remarks }}</td> -->
            <td>
              <a v-if="item.iqama_file" class="fw-bold" :href="getAttachmentUrl(item.iqama_file)" target="_blank">
                |<i class="fa fa-eye"></i></a>

              <a v-if="item.passport_file" class="fw-bold" :href="getAttachmentUrl(item.passport_file)" target="_blank">
                |<i class="fa fa-eye"></i> </a>

              <a v-if="item.passport_file" class="fw-bold" :href="getAttachmentUrl(item.passport_file)" target="_blank">
                |<i class="fa fa-eye"></i> </a>

              <a class="edit_btn me-2" @click="$emit('edit-subcontractor', item.subcon_auto_id)"
                v-if="hasPermission('update_subcontractor')">
                |<i class="fa fa-pencil-square-o"></i>
              </a>
              <!-- <a class="edit_btn" @click="editPage(item.ticket_auto_id)">
                                        <i class="fa fa-pencil-square-o"></i>
                                    </a> -->
              <a class="delete_btn" @click="deleteItem(item.subcon_auto_id)"
                v-if="hasPermission('delete_subcontractor')">
                | <i class="fa fa-trash fa-md delete_icon"></i>
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
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";
import Swal from "sweetalert2";
// import { TicketListAPI } from "../../routes";
import { SearchSubContractorAPI, DeleteSubContractor } from "../routes.js";

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
      searchQuery: "", // Stores the user input
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
  computed: {
    totalPages() {
      return Array.from({ length: this.tableData.last_page }, (_, i) => i + 1);
    },
  },

  mounted() {
    this.performSearch();
  },
  methods: {
    async performSearch() {
      if (this.searchQuery.trim() === "") {
        // alert("Please enter a search term!");
        // return;
      }
      try {
        const response = await axios.get(SearchSubContractorAPI, {
          params: {
            //per_page: this.perPage,
            // page: parseInt(page, 10),
            searching_value: this.searchQuery,
          },
        });
        this.tableData.data = response.data;
        console.log("List Data =", response.data);
      } catch (Error) { }
      this.$emit("search", this.searchQuery); // Emit search event
    },

    getAttachmentUrl(path) {
      const baseUrl = `${window.location.origin}/storage/`;
      return `${baseUrl}${path}`;
    },

    changePage(page) {
      // this.fetchTableData(page);
    },

    viewPage(id) {
      // window.location = `${TicketDetails}/${id}`;
    },

    async deleteItem(id) {
      // console.log("------ ID =", id);

      // Show confirmation dialog
      const confirmDelete = await Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
      });

      if (!confirmDelete.isConfirmed) return;

      let toastId;
      try {
        // Show loading toast
        toastId = toast.loading("Deleting subcontractor...", {
          position: toast.POSITION.TOP_RIGHT,
        });

        const response = await axios.delete(`${DeleteSubContractor}/${id}`);

        // Dismiss loading toast before showing success message
        // toast.dismiss(toastId);
        toast.remove(toastId);

        if (response.data.success) {
          toast.success("Subcontractor deleted successfully", {
            autoClose: 3000,
            position: toast.POSITION.TOP_RIGHT,
          });

          // Refresh the data after successful deletion
          await this.performSearch();
        } else {
          toast.error(
            response.data.message || "Failed to delete subcontractor",
            {
              autoClose: 5000,
              position: toast.POSITION.TOP_RIGHT,
            }
          );
        }
      } catch (error) {
        console.error("Error deleting subcontractor:", error);

        // Ensure loading toast is dismissed in case of error
        // if (toastId) toast.dismiss(toastId);

        let errorMessage = "An error occurred while deleting";
        if (error.response) {
          errorMessage = error.response.data.message || errorMessage;
        } else if (error.request) {
          errorMessage = "Network error - please check your connection";
        }

        toast.error(errorMessage, {
          autoClose: 5000,
          position: toast.POSITION.TOP_RIGHT,
        });
      }
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
  },
};
</script>

<style scoped>
.row {
  margin-top: 10px;
}

.edit_btn {
  cursor: pointer;
}

.delete_btn {
  cursor: pointer;
}
</style>
