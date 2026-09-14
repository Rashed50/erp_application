<template>
    <button @click.prevent="openSection(1)"
        :class="['btn ml-2 mt-2', selected_menu === 1 ? 'btn-primary' : 'btn-secondary']">
        <i class="fas fa-search"></i>Renewal Expense search
    </button>

    <button @click.prevent="openSection(2)"
        :class="['btn ml-2 mt-2', selected_menu === 2 ? 'btn-primary' : 'btn-secondary']">
        <i class="fas fa-gear"></i> Advance setting
    </button>
    <button @click.prevent="openSection(3)" v-if="hasPermission('employee-contribution')"
        :class="['btn ml-2 mt-2', selected_menu === 3 ? 'btn-primary' : 'btn-secondary']">
        <i class="fas fa-plus"></i> Iqama Renewal
    </button>
    <button @click.prevent="openSection(4)" v-if="hasPermission('employee-anualsfee')"
        :class="['btn ml-2 mt-2', selected_menu === 4 ? 'btn-primary' : 'btn-secondary']">
        <i class="fas fa-plus"></i> Cash Recevied
    </button>
    <button @click.prevent="openSection(5)" v-if="hasPermission('cash_received_searh')"
        :class="['btn ml-2 mt-2', selected_menu === 5 ? 'btn-primary' : 'btn-secondary']">
        <i class="fas fa-search"></i> Cash Recevied Search
    </button>

    <hr>

    <div v-if="selected_menu === 1" class="row">
        <iqamaExpenceSearchComponent :data="data"></iqamaExpenceSearchComponent>
    </div>


    <div v-if="selected_menu === 2" class="row">
        <IqamaAdvanceSettingComponent></IqamaAdvanceSettingComponent>
    </div>
    <div v-if="selected_menu === 3" class="row">
        <NewIqamaRenewalComponent></NewIqamaRenewalComponent>
    </div>
    <div v-if="selected_menu === 4" class="row">
        <CashAddEmployee>
        </CashAddEmployee>
    </div>
    <div v-if="selected_menu === 5" class="row">
        <CashReceivedEmployee>
        </CashReceivedEmployee>
    </div>
</template>

<script>
import { inject } from 'vue';
import { useAuth } from '../../../../../../../resources/js/components/useAuth.js';
import IqamaAdvanceSettingComponent from './IqamaAdvanceSetting.vue';
import iqamaExpenceSearchComponent from './IqamaExpenceSearch.vue';
import NewIqamaRenewalComponent from './NewIqamaRenewalComponent.vue';
import CashAddEmployee from './CashReceivedInsertComponent.vue/';
import CashReceivedEmployee from './CashReceivedSearchComponent.vue/';
export default {
    components: {
        iqamaExpenceSearchComponent,
        IqamaAdvanceSettingComponent,
        NewIqamaRenewalComponent,
        CashAddEmployee,
        CashReceivedEmployee,
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
