<template>
<!--
     <button @click.prevent="openSection(1)"
        :class="['btn ml-2 mt-2', selected_menu === 1 ? 'btn-primary' : 'btn-secondary']">
        <i class="fas fa-plus"></i> Add New
    </button>

    <button @click.prevent="openSection(2)"
        :class="['btn ml-2 mt-2', selected_menu === 2 ? 'btn-primary' : 'btn-secondary']">
        <i class="fas fa-plus"></i> Add Salary
    </button> -->


    <div v-if="selected_menu === 1" class="row">
        <AddEmployeeComponent :data="data"></AddEmployeeComponent>
    </div>

</template>

<script>

import { inject } from 'vue';
import axios from 'axios';
import { toast } from 'vue3-toastify';
import { useAuth } from "../../../../../../../resources/js/components/useAuth.js";

import AddEmployeeComponent from './AddEmployeeComponent.vue';


export default {
    components: {
        AddEmployeeComponent,
      
    },
    props: {
        data: {
            type: Object,
            required: true
        }
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
