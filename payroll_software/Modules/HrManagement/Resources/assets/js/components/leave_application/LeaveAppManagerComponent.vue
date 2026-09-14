<template>
  <div>

        <div class="mb-3">
            <button  @click="printLeaveApplicationBlankForm()" :class="['btn ml-2 mt-2', 'btn-primary']"    >
                <i class="fas fa-print"></i> Blank Form
            </button>

            <button v-if="this.hasPermission('leave_application_submit')"   @click="openSection(1)"
                :class="['btn ml-2 mt-2', selected_menu === 1 ? 'btn-primary' : 'btn-secondary']"      >
                <i class="fas fa-plus"></i> New Application
            </button>

            <button v-if="this.hasPermission('leave_application_update')" @click.prevent="openSection(2)" :class="['btn ml-2 mt-2', selected_menu === 2 ? 'btn-primary' : 'btn-secondary']"    >
                <i class="fas fa-search"></i> Search
            </button>
            <button v-if="this.hasPermission('leave_application_approved_salary_closing')" @click.prevent="openSection(3)" :class="['btn ml-2 mt-2', selected_menu === 3 ? 'btn-primary' : 'btn-secondary']"    >
                <i class="fas fa-search"></i> Salary Closing
            </button>

        </div>
    <hr/>

    <!-- New Employee Insert Section -->
    <div v-if="selected_menu === 1" class="row">
      <LeaveApplicationComponent :leaveReasons="this.form_data.leave_reasons" :applicationStatuses="this.form_data.application_status"></LeaveApplicationComponent>
    </div>
    <div v-if="selected_menu === 2" class="row">
        <LeaveApplicationListComponent
            :leaveReasons="this.form_data.leave_reasons"
            :applicationStatuses="this.form_data.application_status"
            :can-update="canUpdate"
            :can-reject="canReject"
            :api-base-url="apiBaseUrl"
            :aws-s3-bucket-url="awsS3BucketUrl"

        ></LeaveApplicationListComponent>
    </div>
    <div v-if="selected_menu === 3" class="row">
        <LeaveApplSalaryPendingComponent
            :leaveReasons="this.form_data.leave_reasons"
            :applicationStatuses="this.form_data.application_status"
            :can-update="canUpdate"
            :can-reject="canReject"
            :api-base-url="apiBaseUrl"
            :aws-s3-bucket-url="awsS3BucketUrl"

        ></LeaveApplSalaryPendingComponent>
    </div>







  </div>
</template>

<script>
import { inject } from 'vue';
// import axios from 'axios';
// import { toast } from 'vue3-toastify';
import { useAuth } from "../../../../../../../resources/js/components/useAuth.js";
import {LeaveApplicationFormPrintAPI} from '../../routes.js'

// Import components normally (without defineAsyncComponent for simplicity)
import LeaveApplicationComponent from './LeaveApplicationComponent.vue';
import LeaveApplicationListComponent from './LeaveApplicationListComponent.vue';
import LeaveApplSalaryPendingComponent from './LeaveApplSalaryPendingComponent.vue';

export default {
  components: {
    LeaveApplicationComponent,
    LeaveApplicationListComponent,
    LeaveApplSalaryPendingComponent,


  },

  setup() {
    const auth = inject('auth');
    const { hasPermission } = useAuth();

    return {
      hasPermission,
    };
  },

  data() {
    return {
      selected_menu: 1, // default searching
    };
  },

mounted() {

    console.log('leave_reasons data:', this.form_data.leave_reasons);
},

  props: {
    form_data: {
      type: Object,
      required: true
    },
     canUpdate: {
            type: Boolean,
            default: true
        },
        canReject: {
            type: Boolean,
            default: true
        },
        apiBaseUrl: {
            type: String,
            default: '/admin'
        },
        awsS3BucketUrl: {
            type: String,
            default: ''
        }
  },
  watch(){
    console.log('hello leave applciation',this.form_data);
  },


  methods: {
    openSection(section) {

      console.log("selected menu ",section);
      this.selected_menu = section;
    },
    printLeaveApplicationBlankForm(){

                // Build query string
                const queryString = new URLSearchParams({
                    form_type: '1',
                }).toString();

                const url = `${LeaveApplicationFormPrintAPI}?${queryString}`;
                // Open in new tab
                window.open(url, '_blank');
        }
  },
};
</script>
