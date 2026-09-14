<!-- resources/js/components/daily-expense/DailyExpenseManager.vue -->
<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <!-- Tab Navigation -->
                <ul class="nav nav-tabs mb-3">
                    <li class="nav-item">
                        <a class="nav-link" :class="{ active: activeTab === 'form' }" href="#" @click.prevent="activeTab = 'form'">
                            <i class="fa fa-plus"></i> Add Expense
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" :class="{ active: activeTab === 'list' }" href="#" @click.prevent="activeTab = 'list'">
                            <i class="fa fa-list"></i> Expense List
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <DailyExpenseComponent
                    v-show="activeTab === 'form'"
                    :expense-types="data.expense_types"
                    :projects="data.projects"
                    :cr_accounts="data.cr_accounts"
                    @saved="onExpenseSaved" />

                <DailyExpenseListComponent
                    v-show="activeTab === 'list'"
                    :expense-types="data.expense_types"
                    :projects="data.projects"
                    ref="expenseList" />
            </div>
        </div>
    </div>
</template>

<script>

import { inject } from 'vue';
import axios from 'axios';
import { toast } from 'vue3-toastify';
import { useAuth } from "../../../../../../../resources/js/components/useAuth.js";

import DailyExpenseComponent from '../Purchase/DailyExpenseComponent.vue';
import DailyExpenseListComponent from '../Purchase/DailyExpenseListComponent.vue';


export default {
    // name: 'DailyExpenseManager',
    components: {
        DailyExpenseComponent,
        DailyExpenseListComponent,
    },
    props: {
        data: {
            type: Object,
            required: true
        },

    },
    data() {
        return {
            activeTab: 'form'
        }
    },
    mounted() {

        console.log('Expense Types:', this.expenseTypes);
        console.log('Projects data :', this.projects);
    },
    methods: {
        onExpenseSaved() {
            this.activeTab = 'list';
            if (this.$refs.expenseList) {
              //  this.$refs.expenseList.searchRecords();
            }
        }
    }
}


</script>

<style scoped>
.nav-tabs .nav-link {
    cursor: pointer;
}
</style>
