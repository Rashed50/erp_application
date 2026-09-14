<template>
  <div>

        <div class="mb-3">

            <button v-if="this.hasPermission('partial_salary_add')"   @click="menuClick('add')"
                :class="['btn mt-2', activeSection === 'add' ? 'btn-primary' : 'btn-secondary']"      >
                <i class="fas fa-plus"></i> Add New Record
            </button>
            <button v-if="this.hasPermission('partial_salary_search')" @click="menuClick('search')" :class="['btn ml-2 mt-2', activeSection === 'search' ? 'btn-primary' : 'btn-secondary']"    >
                <i class="fas fa-search"></i> Search Records
            </button>
            <button v-if="this.hasPermission('partial_salary_bulk_upload')" @click="menuClick('bulk')" :class="['btn ml-2 mt-2', activeSection === 'bulk' ? 'btn-primary' : 'btn-secondary']"    >
                <i class="fas fa-file-excel"></i> Bulk Import
            </button>
        </div>

        <div v-if="activeSection === 'add' && !editingRecord">
             <PartialSalaryInsertForm
                :projects="projects"
                :authUserId="1"
                :months="months"
                :years="years"
                @saved="onSaved"
             ></PartialSalaryInsertForm>
        </div>

        <div v-if="activeSection === 'search'">
            <PartialSalarySearchForm
                :projects="projects"
                :authUserId="1"
                :months="months"
                :years="years"
                @edit="onEdit"
           ></PartialSalarySearchForm>
        </div>
        <div v-if="editingRecord" class="mt-4">

            <PartialSalaryInsertForm
                :record="editingRecord"
                :projects="projects"
                :authUserId="1"
                :isEdit="true"
                :months="months"
                :years="years"
                @saved="onEditSaved"
                @cancel="cancelEdit"
            ></PartialSalaryInsertForm>
        </div>

        <div v-if="activeSection === 'bulk'">
            <PartialSalaryExcelUpload
                :projects="projects"
                :months="months"
                :years="years"
                @import-completed="onImportCompleted"
            />
        </div>


  </div>


</template>

<script>
import { inject } from 'vue';
import axios from 'axios';
import { toast } from 'vue3-toastify';
import { useAuth } from "../../../../../../../resources/js/components/useAuth.js";

import PartialSalaryInsertForm from './PartialSalaryInsertForm.vue';
import PartialSalarySearchForm from './PartialSalarySearchForm.vue';
import PartialSalaryExcelUpload from './PartialSalaryExcelUpload.vue';


export default {
  components: {
    PartialSalaryInsertForm,
    PartialSalarySearchForm,
    PartialSalaryExcelUpload,
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

        activeSection: '',
        editingRecord: null,
        months: {
                1: 'January',
                2: 'February',
                3: 'March',
                4: 'April',
                5: 'May',
                6: 'June',
                7: 'July',
                8: 'August',
                9: 'September',
                10: 'October',
                11: 'November',
                12: 'December'
            },
        years: Array.from({ length: 2 }, (_, i) => new Date().getFullYear() - i)
    };
  },

  props: {
    projects: {
      type: Object,
      required: true
    }
  },

  methods: {

     menuClick(section) {

            if(section === 'add' && !this.hasPermission('partial_salary_add')) {
                this.showNotification('You do not have permission to add records', 'error');
                return;
            }
            else if(section === 'add' ) {
                 this.activeSection = section;
                return;
            }
            else if(section === 'search' && !this.hasPermission('partial_salary_search')) {

                this.showNotification('You do not have permission to view records', 'error');
                return;
            }
            else if(section === 'search'  ) {

               this.editingRecord = null;
               this.activeSection = section;
                return;
            }
             else if(section === 'bulk' && !this.hasPermission('partial_salary_bulk_upload')) {

                this.showNotification('You do not have permission to bulk upload records', 'error');
                return;
            }
            else if(section === 'bulk' ) {

               this.editingRecord = null;
               this.activeSection = section;
               return;
            }

        },

        onSaved() {

                // You can use a toast notification library or simple alert
                toast.success('Record saved successfully');
            },

        onEdit(record) {
            // called when edit button is clicked in search component
            this.editingRecord = record;
            this.activeSection = 'add';
            this.isEdit = true;
        },

        onEditSaved() {
            // called when the form emits 'saved' after editing a record
            this.editingRecord = null;
            this.activeSection = 'search';
            toast.success('Record updated successfully');
        },

        cancelEdit() {
            this.editingRecord = null;
            this.activeSection = 'search';
        },
        onImportCompleted() {
            this.activeSection = 'search';
            toast.success('Bulk import completed successfully', 'success');
        },

        showNotification(message, type = 'info') {
            // If you have a toast library installed
            if (this.$toast) {
                this.$toast[type](message);
            } else {
                // Fallback to alert
                alert(message);
            }
        }
  },
};
</script>


<!-- <template>
    <div>

        <div class="mb-3">

            <button
                @click="activeSection = 'add'"
                :class="['btn', activeSection === 'add' ? 'btn-primary' : 'btn-secondary']"
            >
                <i class="fas fa-plus"></i> Add New Record
            </button>
            <button
                @click="activeSection = 'search'"
                :class="['btn ml-2', activeSection === 'search' ? 'btn-primary' : 'btn-secondary']"
            >
                <i class="fas fa-search"></i> Search Records
            </button>
        </div>

        <div >
            <PartialSalaryHistoryForm
                :projects="projects"

                @saved="onSaved"
            />
        </div>

        <div v-if="activeSection === 'search'">
            <PartialSalaryHistorySearch
                :projects="projects"
                :employees="employees"
                :months="months"
                :years="years"
                :auth-user-id="authUserId"
                @edit="onEdit"
            />
        </div>

        <div v-if="editingRecord" class="mt-4">
            <div class="card">
                <div class="card-header">
                    <h4>Edit Record</h4>
                </div>
                <div class="card-body">
                    <PartialSalaryHistoryForm
                        :record="editingRecord"
                        :projects="projects"
                        :employees="employees"
                        :months="months"
                        :years="years"
                        :auth-user-id="authUserId"
                        :is-edit="true"
                        @saved="onEditSaved"
                        @cancel="cancelEdit"
                    />
                </div>
            </div>
        </div>
    </div>
</template>

<script>
// Import child components locally
import PartialSalaryHistoryForm from './PartialSalaryHistoryForm.vue';
import PartialSalaryHistorySearch from './PartialSalaryHistorySearch.vue';


export default {
    name: 'PartialSalaryHistoryManager',

    components: {
        PartialSalaryHistoryForm,
        PartialSalaryHistorySearch
    },

    props: {
        projects: {
            type: Array,
            required: true
        },
    },

    data() {
        return {
            activeSection: 'add',
            editingRecord: null
        }
    },

    methods: {
        onSaved() {
            this.activeSection = 'search';
            // You can use a toast notification library or simple alert
            this.showNotification('Record saved successfully', 'success');
        },

        onEdit(record) {
            this.editingRecord = record;
            this.activeSection = 'add';
        },

        onEditSaved() {
            this.editingRecord = null;
            this.activeSection = 'search';
            this.showNotification('Record updated successfully', 'success');
        },

        cancelEdit() {
            this.editingRecord = null;
            this.activeSection = 'search';
        },

        showNotification(message, type = 'info') {
            // If you have a toast library installed
            if (this.$toast) {
                this.$toast[type](message);
            } else {
                // Fallback to alert
                alert(message);
            }
        }
    }
}
</script> -->
