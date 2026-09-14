<template lang="">
    <div>
        <!-- breadcrumb -->
        <div class="row">
            <div class="col-md-6">
                <h4>New Supplier Create</h4>
            </div>
            <div class="col-md-6 text-right">

            </div>
        </div>
        <!-- body -->
        <div class="card mt-2">
            <form class="card-body" @submit.prevent="submit">
                <div class="d-flex flex-wrap">
                    <div class="col-md-6 form-group">
                        <label>Branch Office: <span class="req_star">*</span> </label>
                        <Multiselect
                            required
                            v-model="selectedBranch"
                            :options="branchOptions"
                            placeholder="Select item"
                            :searchable="true"/>
                    </div>

                    <div class="col-md-6 form-group">
                        <label>Supplier Name: <span class="req_star">*</span> </label>
                        <input class="form-control form-control-lg" placeholder="Name " required v-model="name" />
                    </div>

                    <div class="col-md-6 form-group">
                        <label>Vat Number: <span class="req_star">*</span> </label>
                        <input class="form-control form-control-lg" placeholder="Vat Number" required v-model="vat_number" />
                    </div>

                    <div class="col-md-6 form-group">
                        <label>Email: <span class="req_star">*</span> </label>
                        <input class="form-control form-control-lg" type="email" placeholder="Email" required v-model="email" />
                    </div>

                    <div class="col-md-6 form-group">
                        <label>Phone Number: <span class="req_star">*</span> </label>
                        <input class="form-control form-control-lg" placeholder="Phone Number Here" required v-model="supplier_phone" />
                    </div>

                    <div class="col-md-6 form-group d-none">
                        <label>Active Status <span class="req_star">*</span> </label>
                        <div class="ms-4">
                            <input class="form-check-input" type="checkbox" v-model="active_status" />
                            <label class="col-sm-6 control-label" style="text-align:left;">Active</label><br>
                        </div>
                    </div>

                     <div class="col-md-6 form-group">
                        <label>Initial Balance: <span class="req_star">*</span> </label>
                        <input type="number" step ="1" class="form-control form-control-lg" placeholder="Vat Number"  required v-model="initial_balance" />
                    </div>

                    <div class="col-md-6 form-group">
                        <label>Contact Address: <span class="req_star">*</span> </label>
                        <textarea class="form-control" rows="3" v-model="address"></textarea>
                    </div>

                </div>

                <button class="btn btn-success btn-sm mt-2 ms-2" type="submit" v-if="hasPermission('accounts_supplier_create')"  @click="force_save = 1" :disabled="uploading">
                    <i v-if="uploading" class="fa fa-spinner fa-spin"></i>
                    <i v-else class="fa fa-check"></i>
                    &nbsp;
                    Save
                </button>

                <a class="btn btn-danger btn-sm ms-2 mt-2" @click="CancelButton">
                    <i class="fa fa-times me-2" aria-hidden="true"></i> Cancel
                </a>
            </form>
        </div>
    </div>
</template>

<script>
    import axios from 'axios';
    import { toast } from 'vue3-toastify';
    import Multiselect from '@vueform/multiselect'
    import "@vueform/multiselect/themes/default.css"
    import {InventorySuppliersStore, InventorySuppliersList} from "../../routes";



    // role and permission composable
    import { inject } from 'vue';
    import {useAuth} from "../../../../../../../resources/js/components/useAuth.js";
    const auth = inject('auth')

    export default {
        setup() {
            const { hasPermission } = useAuth();

            return {
                hasPermission,
            };
        },
        components: {
            Multiselect,
        },

        props: {
            data: {
                type: Object,
                required: true
            }
        },

        data() {
            return {
                selectedBranch: null,
                name: null,
                email: null,
                vat_number: null,
                supplier_phone:null,
                address: null,
                active_status: true,
                initial_balance:0
            }
        },

        computed: {
            branchOptions() {
                return [{ label: 'Select item', value: null }, ...this.data.branchs.map(d => ({ label: d.branch_name_en, value: d.braoff_auto_id }))];
            },
        },

        mounted() {
        },

        methods: {
            async submit() {
                console.log("Active =", this.active_status)
                this.uploading = true;
                 try {
                    const response = await axios.post(InventorySuppliersStore, {
                        branch_office_id: this.selectedBranch,
                        isupp_name: this.name,
                        isupp_email: this.email,
                        supplier_phone:this.supplier_phone,
                        isupp_vat_number: this.vat_number,
                        isupp_contact_address: this.address,
                        active_status: this.active_status,
                        initial_balance:this.initial_balance,
                    });

                    if (response.data.success) {
                        toast.success(response.data.message);

                        setTimeout(() => {
                            window.location = InventorySuppliersList
                        }, 1500);
                    } else {
                        toast.error('Failed to create Inventory Supplier.');
                    }
                } catch (error) {
                    toast.error('System exception occurred: '  + error.response?.data?.message || error.message);
                } finally {
                    this.uploading = false;
                }
            },


            CancelButton() {
                window.location = InventorySuppliersList;
            }
        }
    }
</script>
