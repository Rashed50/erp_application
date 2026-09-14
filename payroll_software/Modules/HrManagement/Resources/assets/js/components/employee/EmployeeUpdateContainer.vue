<template>
  <div>

    <div class="mb-3">
      <button v-if="this.hasPermission('employee-add')" @click="openSection(1)"
        :class="['btn mt-2', selected_menu === 1 ? 'btn-primary' : 'btn-secondary']">
        <i class="fas fa-plus"></i> Add New
      </button>


      <button v-if="this.hasPermission('employee_global_search')" @click.prevent="openSection(13)"
        :class="['btn ml-2 mt-2', selected_menu === 13 ? 'btn-primary' : 'btn-secondary']">
        <i class="fas fa-search"></i> Search
      </button>


      <button v-if="this.hasPermission('job-approve')" @click.prevent="openSection(14)"
        :class="['btn ml-2 mt-2', selected_menu === 14 ? 'btn-primary' : 'btn-secondary']">
        <i class="fas fa-search"></i>Unapproved Emp
      </button>

      <button v-if="this.hasPermission('employee-edit')" @click.prevent="openSection(2)"
        :class="['btn ml-2 mt-2', selected_menu === 2 ? 'btn-primary' : 'btn-secondary']">
        <i class="fas fa-edit"></i> All Information
      </button>
      <button v-if="this.hasPermission('salarydetails-edit')" @click.prevent="openSection(3)"
        :class="['btn ml-2 mt-2', selected_menu === 3 ? 'btn-primary' : 'btn-secondary']">
        <i class="fas fa-edit"></i> Salary
      </button>


      <!-- <button v-if="this.hasPermission('employee_related_file_upload')" @click.prevent="openSection(4)"
        :class="['btn ml-2 mt-2', selected_menu === 4 ? 'btn-primary' : 'btn-secondary']">
        <i class="fas fa-file-upload"></i> File Upload
      </button> -->
      <button  @click.prevent="openSection(4)"
        :class="['btn ml-2 mt-2', selected_menu === 4 ? 'btn-primary' : 'btn-secondary']">
        <i class="fas fa-file-upload"></i> File Upload
      </button>

      <button v-if="this.hasPermission('employee_iqama_no_update')" @click.prevent="openSection(5)"
        :class="['btn ml-2 mt-2', selected_menu === 5 ? 'btn-primary' : 'btn-secondary']">
        Iqama & Passport
      </button>
      <button v-if="this.hasPermission('employee_sponsor_update')" @click.prevent="openSection(6)"
        :class="['btn ml-2 mt-2', selected_menu === 6 ? 'btn-primary' : 'btn-secondary']">
        Sponsor
      </button>
      <button v-if="this.hasPermission('employee_agency_update')" @click.prevent="openSection(7)"
        :class="['btn ml-2 mt-2', selected_menu === 7 ? 'btn-primary' : 'btn-secondary']">
        Reference
        <!-- agency name update also here -->
      </button>
      <button v-if="this.hasPermission('employee_accommodation_update')" @click.prevent="openSection(8)"
        :class="['btn ml-2 mt-2', selected_menu === 8 ? 'btn-primary' : 'btn-secondary']">
        Accommodation
      </button>
      <button v-if="this.hasPermission('emp_blood_group_update')" @click.prevent="openSection(9)"
        :class="['btn ml-2 mt-2', selected_menu === 9 ? 'btn-primary' : 'btn-secondary']">
        Blood Group
      </button>
      <!-- <button v-if="this.hasPermission('add_remarks_on_employee_action')" @click.prevent="openSection(10)"
        :class="['btn ml-2 mt-2', selected_menu === 10 ? 'btn-primary' : 'btn-secondary']">
        AJEER File
      </button> -->
      <button v-if="this.hasPermission('employee_job_status_change_activity')" @click.prevent="openSection(10)"
        :class="['btn ml-2 mt-2', selected_menu === 10 ? 'btn-primary' : 'btn-secondary']">
        Activity Update

      </button>


      <button v-if="this.hasPermission('employee_designation_update')" @click.prevent="openSection(11)"
        :class="['btn ml-2 mt-2', selected_menu === 11 ? 'btn-primary' : 'btn-secondary']">
        Designation
      </button>
      <button v-if="this.hasPermission('single_employee_transfer')" @click.prevent="openSection(12)"
        :class="['btn ml-2 mt-2', selected_menu === 12 ? 'btn-primary' : 'btn-secondary']">
        Working Project
      </button>



    </div>
    <hr />

    <!-- New Employee Insert Section -->
    <div v-if="selected_menu === 1" class="row">
      <!-- <AddEmployeeComponent :data="data"></AddEmployeeComponent> -->
    </div>
    <div v-if="selected_menu === 2" class="row">
      <EmployeeInformationEditComponent :data="data"> </EmployeeInformationEditComponent>
    </div>

    <!-- Salary Update Section -->
    <div v-else-if="selected_menu === 3" class="row">
      <SalaryUpdateComponent :data="data"></SalaryUpdateComponent>
    </div>

    <!-- File Update Section -->
    <div v-else-if="selected_menu === 4" class="row">
      <FileUpdateComponent :data="data"></FileUpdateComponent>
    </div>

    <!-- iqama Update Section -->

    <div v-else-if="selected_menu === 5" class="row">
      <IqamaUpdateComponent :data="data"></IqamaUpdateComponent>
    </div>
    <div v-else-if="selected_menu === 6" class="row">
      <SponsorUpdateComponent :sponsors_list="this.data.sponsors"></SponsorUpdateComponent>
    </div>
    <div v-else-if="selected_menu === 7" class="row">
      <ReferenceUpdateComponent :agencies="this.data.agencies"></ReferenceUpdateComponent>
    </div>
    <div v-else-if="selected_menu === 8" class="row">
      <AccommodationInfoUpdateComponent :accommodations="this.data.accommodations"></AccommodationInfoUpdateComponent>
    </div>

    <div v-else-if="selected_menu === 9" class="row">
      <PhotoBloodUpdateComponent :data="data"></PhotoBloodUpdateComponent>
    </div>

    <div v-else-if="selected_menu === 10" class="row">
      <JobAndSalaryUpdateComponent></JobAndSalaryUpdateComponent>
    </div>
    <!-- <div v-else-if="selected_menu === 10" class="row">
      <DocumentUpdateComponent :document_list="this.data.docements"></DocumentUpdateComponent>
    </div> -->
    <div v-else-if="selected_menu === 11" class="row">
      <DesignationUpdateComponent :designations="this.data.designations"></DesignationUpdateComponent>
    </div>
    <div v-else-if="selected_menu === 12" class="row">

      <WorkingProjectUpdateComponent :project_list="this.data.projects"></WorkingProjectUpdateComponent>
    </div>
    <div v-else-if="selected_menu === 13" class="row">
      <EmpDetailsInfoSearchComponent></EmpDetailsInfoSearchComponent>
    </div>
    <div v-else-if="selected_menu === 14" class="row">
      <UnapprovedEmpComponent></UnapprovedEmpComponent>
    </div>



  </div>
</template>

<script>
import { inject } from 'vue';
import axios from 'axios';
import { toast } from 'vue3-toastify';
import { useAuth } from "../../../../../../../resources/js/components/useAuth.js";

// Import components normally (without defineAsyncComponent for simplicity)
import AddEmployeeComponent from './AddEmployeeComponent.vue';
import SalaryUpdateComponent from './SalaryUpdateComponent.vue';
import FileUpdateComponent from './FileUpdateComponent.vue';
import EmployeeInformationEditComponent from './EmployeeInformationEditComponent.vue';
import IqamaUpdateComponent from './IqamaUpdateComponent.vue';
import SponsorUpdateComponent from './SponsorUpdateComponent.vue';
import ReferenceUpdateComponent from './ReferenceUpdateComponent.vue';
import AccommodationInfoUpdateComponent from './AccommodationInfoUpdateComponent.vue';
import PhotoBloodUpdateComponent from './PhotoBloodUpdateComponent.vue';
// import DocumentUpdateComponent from './DocumentUpdateComponent.vue';
import DesignationUpdateComponent from './DesignationUpdateComponent.vue';
import WorkingProjectUpdateComponent from './WorkingProjectUpdateComponent.vue';
import EmpDetailsInfoSearchComponent from './EmpDetailsInfoSearchComponent.vue'
import UnapprovedEmpComponent from './UnapprovedEmployees.vue';
import JobAndSalaryUpdateComponent from './JobAndSalaryUpdateComponent.vue';

export default {
  components: {
    AddEmployeeComponent,
    SalaryUpdateComponent,
    FileUpdateComponent,
    EmployeeInformationEditComponent,
    IqamaUpdateComponent,
    SponsorUpdateComponent,
    ReferenceUpdateComponent,
    AccommodationInfoUpdateComponent,
    PhotoBloodUpdateComponent,
    //DocumentUpdateComponent,
    DesignationUpdateComponent,
    WorkingProjectUpdateComponent,
    EmpDetailsInfoSearchComponent,
    UnapprovedEmpComponent,
    JobAndSalaryUpdateComponent,

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
      selected_menu: 13, // default searching
    };
  },

  mounted() {

    console.log('Emp Update contrainer Props data:', this.data);

  },

  props: {
    data: {
      type: Object,
      required: true
    }
  },


  methods: {
    openSection(section) {

      console.log("selected menu ", section);
      this.selected_menu = section;
    },
  },
};
</script>
