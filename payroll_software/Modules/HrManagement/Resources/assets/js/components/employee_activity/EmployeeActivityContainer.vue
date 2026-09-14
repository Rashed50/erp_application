<template>
    <button @click.prevent="openSection(1)" v-if="hasPermission('employee_job_status_change_activity')"
        :class="['btn ml-2 mt-2', selected_menu === 1 ? 'btn-primary' : 'btn-secondary']">
        <i class="fas fa-plus"></i> Add Activity
    </button>

    <button @click.prevent="openSection(2)" v-if="hasPermission('employee_salary_status_update')"
        :class="['btn ml-2 mt-2', selected_menu === 2 ? 'btn-primary' : 'btn-secondary']">
        <i class="fas fa-plus"></i> Update
    </button>
    <hr />

    <div v-if="selected_menu === 1" class="row">
        <AddEmployeeActivity :data="data"></AddEmployeeActivity>
    </div>

    <div v-if="selected_menu === 2" class="row">
        <UpdateEmployeeSalaryStatus></UpdateEmployeeSalaryStatus>
    </div>



</template>

<script>

import { inject } from 'vue';
import { useAuth } from '../../../../../../../resources/js/components/useAuth.js';
import AddEmployeeActivity from './AddEmployeeActivity.vue';
import UpdateEmployeeSalaryStatus from './UpdateEmployeeSalaryStatus.vue';

export default {
    components: {
        AddEmployeeActivity,
        UpdateEmployeeSalaryStatus,
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
            selected_menu: 1,
        };
    },
    methods: {
        openSection(section) {
            console.log("Selected menu changed to:", section);
            this.selected_menu = section;
        },
    },
}
</script>