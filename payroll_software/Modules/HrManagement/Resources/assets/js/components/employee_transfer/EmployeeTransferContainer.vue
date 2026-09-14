<template>
    <button @click.prevent="openSection(1)" v-if="hasPermission('multiple-employee-transfer')"
        :class="['btn ml-2 mt-2', selected_menu === 1 ? 'btn-primary' : 'btn-secondary']">
        <i class="fas fa-plus"></i> Emp Transfer
    </button>

    <button @click.prevent="openSection(2)"
        :class="['btn ml-2 mt-2', selected_menu === 2 ? 'btn-primary' : 'btn-secondary']">
        <i class="fas fa-plus"></i> Emp Transfer Approval
    </button>
    <hr />

    <div v-if="selected_menu === 1" class="row">
        <EmployeeTransferComponent :data="data"></EmployeeTransferComponent>
    </div>

    <div v-if="selected_menu === 2" class="row">
        <EmployeeTransferApprovalComponent></EmployeeTransferApprovalComponent>
    </div>









</template>

<script>
import { inject } from 'vue';
import { useAuth } from '../../../../../../../resources/js/components/useAuth.js';
import EmployeeTransferApprovalComponent from './EmpTransferApprovalComponent.vue';
import EmployeeTransferComponent from './EmpTransferComponent.vue';

export default {
    components: {
        EmployeeTransferComponent,
        EmployeeTransferApprovalComponent,
    },
    props: {
        data: {
            type: Object,
            required: true
        }
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