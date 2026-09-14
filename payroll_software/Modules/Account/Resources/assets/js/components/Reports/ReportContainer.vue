<template>
    <button @click.prevent="openSection(1)" v-if="hasPermission('salary_bonus_add')"
        :class="['btn ml-2 mt-2', selected_menu === 1 ? 'btn-primary' : 'btn-secondary']">
        GeneralLedger
    </button>


    <button @click.prevent="openSection(2)"
        :class="['btn ml-2 mt-2', selected_menu === 3 ? 'btn-primary' : 'btn-secondary']">
        Sales
    </button>
    <button @click.prevent="openSection(3)"
        :class="['btn ml-2 mt-2', selected_menu === 2 ? 'btn-primary' : 'btn-secondary']">
        TrialBalance
    </button>
    <button @click.prevent="openSection(4)"
        :class="['btn ml-2 mt-2', selected_menu === 4 ? 'btn-primary' : 'btn-secondary']">
        All Report
    </button>
    <hr />

    <div v-if="selected_menu === 1" class="row">
        <GeneralLedgerReport></GeneralLedgerReport>
    </div>

    <div v-if="selected_menu === 2" class="row">
        <SalesReport></SalesReport>
    </div>
    <div v-if="selected_menu === 3" class="row">
        <TrialBalanceReport></TrialBalanceReport>
    </div>
    <div v-if="selected_menu === 4" class="row">
        <AllReportComponent></AllReportComponent>
    </div>









</template>

<script>

import { inject } from 'vue';
import { useAuth } from '../../../../../../../resources/js/components/useAuth.js';
import GeneralLedgerReport from './GeneralLedgerReport.vue';
import SalesReport from './SalesReport.vue';
import TrialBalanceReport from './TrialBalanceReport.vue';
import AllReportComponent from './AllReportComponent.vue';

export default {
    components: {
        GeneralLedgerReport,
        SalesReport,
        TrialBalanceReport,
        AllReportComponent
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