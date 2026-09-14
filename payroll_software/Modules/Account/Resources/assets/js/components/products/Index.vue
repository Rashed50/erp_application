<template>
    <div>
        <!-- breadcrumb -->
        <div class="row">
            <div class="col-md-6">
                <h4>Products</h4>
            </div>
            <div class="col-md-6 text-right">
                <button type="button" class="btn btn-success" data-toggle="modal"  v-if="hasPermission('account_product_create')" data-target="#exampleModal">
                    <i class="fa fa-plus"></i> Add Product
                </button>
                &nbsp;
                <a href="/admin/accounting/product/units" type="button" class="btn btn-info">
                    <i class="fa fa-balance-scale"></i> Units
                </a>
            </div>
        </div>

        <div class="card mt-2">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Ref.</th>
                                    <th>Name</th>
                                    <!-- <th>Unit</th>
                                    <th>Price</th> -->
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, index) in products" :key="index">
                                    <td>{{ item.spi_auto_id  }}</td>
                                    <td>{{ item.spi_name_en }}</td>
                                    <!-- <td>{{ item.spi_unit }}</td>
                                    <td>{{ item.price ?? 0 }}</td> -->
                                    <td>
                                        <button class="btn btn-sm btn-success" data-toggle="modal"  v-if="hasPermission('account_product_edit')" data-target="#example2Modal" @click="edit(item)">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                        &nbsp;
                                        <button type="button" class="btn btn-sm btn-danger"  v-if="hasPermission('account_product_delete')" @click.prevent="delete_item(item, i)">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content ">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            Add Product
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="" @submit.prevent="save_item">
                            <div class="mb-2">
                                <label for="">Name:</label>
                                <input type="text" required class="form-control" v-model="new_item.name">
                            </div>
                            <div class="mb-2 d-none">
                                <label for="">Unit:</label>
                                <Multiselect v-model="new_item.unit_id"
                                    :options="units.map(d => ({label: d.name, value: d.id}))"
                                />
                            </div>
                            <div class="mb-2 d-none">
                                <label for="">Price:</label>
                                <input type="number" required class="form-control" v-model="new_item.price">
                            </div>
                            <div class="text-center mt-2">
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </form>
                    </div>
                    <!-- <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary">Save changes</button>
                    </div> -->
                </div>
            </div>
        </div>
        <div class="modal fade" id="example2Modal" tabindex="-1" aria-labelledby="example2ModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            Update Product
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="" @submit.prevent="update_item">
                            <div class="mb-2">
                                <label for="">Product Name:</label>
                                <input type="text" required class="form-control" v-model="edit_item.spi_name_en">
                            </div>
                            <div class="mb-2 d-none">
                                <label for="">Unit:</label>
                                <Multiselect v-model="edit_item.spi_unit"
                                    :options="units.map(d => ({label: d.name, value: d.id}))"
                                />
                            </div>
                            <div class="mb-2 d-none">
                                <label for="">Unit Rate:</label>
                                <input type="number" required class="form-control" v-model="edit_item.price" >
                            </div>
                            <div class="mt-2 text-center">
                                <button type="submit" class="btn btn-primary">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import axios from "axios";
import Multiselect from '@vueform/multiselect'
import "@vueform/multiselect/themes/default.css"
import {toast} from 'vue3-toastify';
import VueSelect from "vue-select";
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
    data() {
        return {
            products: [],
            units: [],
            new_item: {price: 0, unit_id: 1}, // default value both are hidden in the form
            edit_item: {},
        }
    },
    methods: {
        delete_item(cus, i) {


            if(confirm('Are you sure?')) {
                axios.delete("/admin/accounting/product/" + cus.id).then(() => {
                    this.products.splice(i, 1)
                })
                .catch((error) => {
                    alert(error)
                })
            }
        },
        edit(cus) {

            this.edit_item = JSON.parse(JSON.stringify(cus))
            this.edit_item.price = 0;
            // this.edit_item.unit_id = this.units.find(u => u.id == cus.unit_id).name
        },
        save_item() {
         debugger;   
           // if(!this.new_item.unit_id) return
            this.new_item.unit_id =  1; // default unit id
            this.new_item.price = this.new_item.price || 0;
            axios.post('/admin/accounting/product/add', {
                spi_name_en: this.new_item.name,
                spi_unit: this.new_item.unit_id,
                price: this.new_item.price

            }).then((response) => {
                this.products.push(response.data)
                this.new_item = {}
                $('button.close').click()
                toast.success('Product added successfully')
            })
            .catch((error) => {
                toast.error(error.response.data.message)
            })
        },
        update_item() {
            console.log(this.edit_item)

            axios.post('/admin/accounting/product/update', this.edit_item).then((response) => {
                // this.customers.push(response.data)
                this.products.forEach((cus, i) => {
                    if(cus.spi_auto_id == response.data.spi_auto_id)
                    {
                        this.products[i] = response.data
                    }
                })

                this.products[find] = response.data
                this.edit_item = {}
                $('button.close').click()
                toast.success('Product updated successfully')
            })
            .catch((error) => {
                // alert(error)
                toast.error(error.response.data.message)
            })
        },
    },
    mounted() {
        this.products = window.products
        this.units = window.units
    }
}
</script>
<style lang="">

</style>
