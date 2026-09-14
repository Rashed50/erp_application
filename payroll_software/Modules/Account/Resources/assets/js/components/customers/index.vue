<template lang="">
    <div>
        <!-- breadcrumb -->
        <div class="row">
            <div class="col-md-6">
                <h4>Customers</h4>
            </div>
            <div class="col-md-6 text-right">
                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#exampleModal">Add Customer</button>
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
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>VAT No.</th>
                                    <th>Address</th>
                                    <th>
                                        Constract Details
                                    </th>
                                    <th>Balance</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(customer, i) in customers.filter(cus => deleted_customers.indexOf(cus.customer_id) === -1)" :key="i">
                                    <td>{{customer.customer_id}}</td>
                                    <td>{{customer.customer_name}}</td>
                                    <td>{{customer.customer_phone}}</td>
                                    <td>{{customer.customer_email}}</td>
                                    <td>{{customer.vat_no}}</td>
                                    <td>
                                        {{customer.customer_address ?? ''}}
                                        <br>
                                        {{customer.country ?? ''}}
                                    </td>
                                    <td>
                                        {{customer.contact_person ?? ''}}
                                        <br>
                                        {{customer.contact_person_phone ?? ''}}
                                         <br>
                                        {{customer.contact_person_email ?? ''}}
                                     </td>
                                    <td>{{customer.current_balance}}</td>

                                    <td>{{customer.active_status == 1 ? 'Active' : 'Inactive'}}</td>

                                    <td>
                                        <button type="button" class="btn btn-sm btn-success"  v-if="hasPermission('accounts_customer_edit')"  data-toggle="modal" data-target="#example2Modal" @click="edit_customer(customer)">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                        &nbsp;
                                        <button type="button" class="btn btn-sm btn-danger"  v-if="hasPermission('accounts_customer_delete')"  @click.prevent="delete_cus(customer, i)">
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
                            Add Customer
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="" @submit.prevent="save_customer">
                            <div class="mb-2">
                                <label for="">Name:</label>
                                <input type="text" required class="form-control" v-model="new_cus.customer_name">
                            </div>
                            <div class="mb-2">
                                <label for="">Phone:</label>
                                <input type="text" required class="form-control" v-model="new_cus.customer_phone">
                            </div>
                            <div class="mb-2">
                                <label for="">Email:</label>
                                <input type="email" required class="form-control" v-model="new_cus.customer_email">
                            </div>
                            <div class="mb-2">
                                <label for="">VAT No:</label>
                                <input type="text" required class="form-control" v-model="new_cus.vat_no">
                            </div>
                            <div class="mb-2">
                                <label for="">Opening Balance:</label>
                                <input type="number" required class="form-control" step="1" v-model="new_cus.current_balance">
                            </div>


                            <div class="mb-2">
                                <label for="">Billing Address:</label>
                                <input type="text" required class="form-control" v-model="new_cus.cus_billing_address">
                            </div>
                            <div class="mb-2">
                                <label for="">Postal Code:</label>
                                <input type="text" required class="form-control" v-model="new_cus.cus_billing_zip">
                            </div>
                             <div class="mb-2">
                                <label for="">City:</label>
                                <input type="text" required class="form-control" v-model="new_cus.cus_billing_city">
                            </div>
                            <div class="mb-2">
                                <label for="">Country:</label>
                                <input type="text" required class="form-control" v-model="new_cus.country">
                            </div>
                            <div class="mb-2">
                                <label for="">Contact Person Name:</label>
                                <input type="text" required class="form-control" v-model="new_cus.contact_person">
                            </div>
                            <div class="mb-2">
                                <label for="">Phone:</label>
                                <input type="text" required class="form-control" v-model="new_cus.contact_person_phone">
                            </div>
                            <div class="mb-2">
                                <label for="">Email:</label>
                                <input type="email" required class="form-control" v-model="new_cus.contact_person_email">
                            </div>

                            <div class="alert alert-danger" v-if="new_cus_error">
                                {{ new_cus_error }}
                            </div>
                            <div class="text-center mt-2">
                                <button type="submit" v-if="hasPermission('accounts_customer_create')"  class="btn btn-primary">Save</button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
        <div class="modal fade" id="example2Modal" tabindex="-1" aria-labelledby="example2ModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            Edit Customer
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="" @submit.prevent="update_customer">

                            <div class="mb-2">
                                <label for="">Name:</label>
                                <input type="text" required class="form-control" v-model="edit_cus.customer_name">
                            </div>
                            <div class="mb-2">
                                <label for="">Phone:</label>
                                <input type="text" required class="form-control" v-model="edit_cus.customer_phone">
                            </div>
                            <div class="mb-2">
                                <label for="">Email:</label>
                                <input type="email" required class="form-control" v-model="edit_cus.customer_email">
                            </div>
                            <div class="mb-2">
                                <label for="">VAT No:</label>
                                <input type="text" required class="form-control" v-model="edit_cus.vat_no">
                            </div>
                            <div class="mb-2">
                                <label for="">Opening Balance:</label>
                                <input type="number" required class="form-control" step="1" v-model="edit_cus.current_balance">
                            </div>



                            <div class="mb-2">
                                <label for="">Billing Address:</label>
                                <input type="text" required class="form-control" v-model="edit_cus.cus_billing_address">
                            </div>
                            <div class="mb-2">
                                <label for="">Postal Code:</label>
                                <input type="text" required class="form-control" v-model="edit_cus.cus_billing_zip">
                            </div>
                             <div class="mb-2">
                                <label for="">City:</label>
                                <input type="text" required class="form-control" v-model="edit_cus.cus_billing_city">
                            </div>
                            <div class="mb-2">
                                <label for="">Country:</label>
                                <input type="text" required class="form-control" v-model="edit_cus.country">
                            </div>
                            <div class="mb-2">
                                <label for="">Contact Person Name:</label>
                                <input type="text" required class="form-control" v-model="edit_cus.contact_person">
                            </div>
                            <div class="mb-2">
                                <label for="">Phone:</label>
                                <input type="text" required class="form-control" v-model="edit_cus.contact_person_phone">
                            </div>
                            <div class="mb-2">
                                <label for="">Email:</label>
                                <input type="email" required class="form-control" v-model="edit_cus.contact_person_email">
                            </div>
                            <div class="mt-2 text-center">
                                <button type="submit"  v-if="hasPermission('accounts_customer_edit')"  class="btn btn-primary">Update</button>
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
    </div>
</template>
<script>
import axios from 'axios'
import { toast } from 'vue3-toastify';

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
    props: {
        save: {
            type: String,
            required: true
        },
        update: {
            type: String,
            required: true
        },
        delete: {
            type: String,
            required: true
        },
        customers: {
            type: Array,
            required: true
        },
    },
    data() {
        return {
            deleted_customers: [],
            new_cus: {
                customer_name: '',
                customer_phone: '',
                customer_email: '',
                vat_no: '',
                current_balance:0,
                contact_person:'',
                contact_person_phone:'',
                contact_person_email:'',
                cus_billing_address:'',
                cus_billing_zip:'',
                cus_billing_city:'',
                country:'',

            },
            edit_cus: {},
        }
    },
    methods: {
        delete_cus(cus, i) {
            if(confirm('Are you sure?')) {
                axios.post(this.delete, {customer_id: cus.customer_id}).then(() => {
                    this.deleted_customers.push(cus.customer_id)
                })
            }
        },
        edit_customer(cus) {

            this.edit_cus.customer_id = cus.customer_id
            this.edit_cus.customer_name = cus.customer_name
            this.edit_cus.customer_email = cus.customer_email
            this.edit_cus.customer_phone = cus.customer_phone
            this.edit_cus.vat_no = cus.vat_no
            this.edit_cus.current_balance = cus.current_balance
            this.edit_cus.cus_billing_address = cus.customer_address
            this.edit_cus.cus_billing_zip = cus.customer_address
            this.edit_cus.cus_billing_city = cus.customer_address
            this.edit_cus.country = cus.country
            this.edit_cus.contact_person = cus.contact_person
            this.edit_cus.contact_person_phone = cus.contact_person_phone
            this.edit_cus.contact_person_email = cus.contact_person_email

        },
        save_customer() {
            axios.post(this.save, this.new_cus).then((response) => {
                // Push the actual customer data from the response
                if (response.data.success && response.data.data) {
                    this.customers.push(response.data.data);
                }
                // Reset the form
                this.new_cus = {
                    customer_name: '',
                    customer_phone: '',
                    customer_email: '',
                    vat_no: '',
                    current_balance: 0,
                    contact_person: '',
                    contact_person_phone: '',
                    contact_person_email: '',
                    cus_billing_address: '',
                    cus_billing_zip: '',
                    cus_billing_city: '',
                    country: '',
                };
                $('button.close').click()
                toast.success('Customer added successfully')
            })
            .catch((error) => {
                toast.error(error.response.data.message)
            })
        },
        update_customer() {
            axios.post(this.update, this.edit_cus).then((response) => {
                // this.customers.push(response.data)
                let find = this.customers.find((cus) => {
                    return cus.cus_auto_id == this.edit_cus.cus_auto_id
                })
                find.cus_name = this.edit_cus.cus_name
                find.cus_phone = this.edit_cus.cus_phone
                find.cus_email = this.edit_cus.cus_email
                find.cus_tax_number = this.edit_cus.cus_tax_number
                find.cus_org_name = this.edit_cus.cus_org_name
                find.cus_billing_address = this.edit_cus.cus_billing_address
                find.cus_billing_city = this.edit_cus.cus_billing_city
                find.cus_billing_state = this.edit_cus.cus_billing_state
                find.cus_billing_zip = this.edit_cus.cus_billing_zip
                find.cus_billing_country = this.edit_cus.cus_billing_country

                this.edit_cus = {}
                toast.success('Customer updated successfully')
                $('button.close').click()
            })
            .catch((error) => {
                toast.error(error.response.data.message)
            })
        },
    },
    mounted() {
        // this.get_customers();
    }
}
</script>
<style scoped>
form .mb-2 {
    display: flex;
}
form .mb-2 label {
    width: 200px;
    padding-right: 10px;
    text-align: right;
}
</style>
