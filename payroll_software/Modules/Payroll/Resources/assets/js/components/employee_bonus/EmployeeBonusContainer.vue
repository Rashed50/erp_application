<template>
    <button @click.prevent="openSection(1)" v-if="hasPermission('salary_bonus_add')"
        :class="['btn ml-2 mt-2', selected_menu === 1 ? 'btn-primary' : 'btn-secondary']">
        <i class="fas fa-plus"></i> New Entry
    </button>


    <button @click.prevent="openSection(3)"
        :class="['btn ml-2 mt-2', selected_menu === 3 ? 'btn-primary' : 'btn-secondary']">
        <i class="fas fa-search"></i> Search
    </button>
    <button @click.prevent="openSection(2)"
        :class="['btn ml-2 mt-2', selected_menu === 2 ? 'btn-primary' : 'btn-secondary']">
        <i class="fas fa-file"></i> Report
    </button>
    <hr />

    <div v-if="selected_menu === 1" class="row">
        <NewEntryComponent></NewEntryComponent>
    </div>

    <div v-if="selected_menu === 2" class="row">
        <ReportComponent></ReportComponent>
    </div>
    <div v-if="selected_menu === 3" class="row">
        <SearchBonusComponent></SearchBonusComponent>
    </div>









</template>

<script>

import { inject } from 'vue';
import { useAuth } from '../../../../../../../resources/js/components/useAuth.js';
import NewEntryComponent from './NewEntryComponent.vue';
import ReportComponent from './ReportComponent.vue';
import SearchBonusComponent from './SearchBonusComponent.vue';

export default {
    components: {
        NewEntryComponent,
        ReportComponent,
        SearchBonusComponent
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