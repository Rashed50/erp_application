<template>

    <button @click.prevent="openSection(1)" v-if="hasPermission('monthly_salary_status_unpaid_list')"
        :class="['btn ml-2 mt-2', selected_menu === 1 ? 'btn-primary' : 'btn-secondary']">
        <i class="fas fa-plus"></i> Salary Pending
    </button>

    <button @click.prevent="openSection(2)" v-if="hasPermission('monthly_salary_status_paid_list')"
        :class="['btn ml-2 mt-2', selected_menu === 2 ? 'btn-primary' : 'btn-secondary']">
        <i class="fas fa-plus"></i> Salary Paid
    </button>

    <hr>


    <div v-if="selected_menu === 1" class="row">
        <PendingSalaryComponent :sponsor_list="this.data.sponsors" :project_list="this.data.projects">
        </PendingSalaryComponent>
    </div>
    <div v-if="selected_menu === 2" class="row">
        <PaidSalaryComponent :sponsor_list="this.data.sponsors" :project_list="this.data.projects">
        </PaidSalaryComponent>
    </div>

</template>

<script>

import { inject } from 'vue';
import axios from 'axios';
import { toast } from 'vue3-toastify';

import { useAuth } from '../../../../../../resources/js/components/useAuth.js';
import PendingSalaryComponent from './PendingSalaryComponent.vue';
import PaidSalaryComponent from './PaidSalaryComponent.vue';


export default {
    components: {
        PendingSalaryComponent,
        PaidSalaryComponent

    },

    props: {
        data: {
            type: Object,
            required: true,
            default: () => ({}),
        },
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
    methods: {
        openSection(section) {
            console.log("selected menu ", section);
            this.selected_menu = section;
        },
    },
}
</script>
