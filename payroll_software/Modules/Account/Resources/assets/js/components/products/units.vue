<template>
    <div>
        <!-- breadcrumb -->
        <div class="row">
            <div class="col-md-6">
                <h4>Product Units</h4>
            </div>
            <div class="col-md-6 text-right">
                <button type="button" @click="name = ''"
                    data-toggle="modal" data-target="#exampleModal" class="btn btn-success"><i class="fa fa-plus"></i> Add Unit</button>
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
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, index) in units" :key="index">
                                    <td>{{ item.id }}</td>
                                    <td>{{ item.name }}</td>
                                    <td style="width: 130px;">
                                        <button class="btn btn-primary btn-sm" type="button" data-toggle="modal" data-target="#example2Modal"
                                            @click="edit_id = item.id, name = item.name"
                                        >
                                            <i class="fa fa-edit"></i>
                                        </button>
                                        &nbsp;
                                        <button @click="delete_item(item.id, index)" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i> </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content ">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            Add Unit
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="" @submit.prevent="save">
                            <div class="mb-2">
                                <label for="">Name:</label>
                                <input type="text" required class="form-control" v-model="name">
                            </div>
                            <div class=" mt-2">
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="example2Modal" tabindex="-1" aria-labelledby="example2ModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            Edit Unit
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="" @submit.prevent="update">
                            <div class="mb-2">
                                <label for="">Name:</label>
                                <input type="text" required class="form-control" v-model="name">
                            </div>
                            <div class="mt-2 ">
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

export default {
    data() {
        return {
            units: [],
            name: '',
            edit_id: ''
        }
    },
    methods: {
        save() {
            axios.post("/api/admin/accounting/unit", {name: this.name}).then((response) => {
                this.units.push(response.data)
                this.name = ''
                $('.close').click()
            })
            .catch((error) => {
                alert(error)
            })
        },
        update() {
            if(!this.edit_id) return
            axios.put("/api/admin/accounting/unit/" + this.edit_id, {name: this.name}).then((response) => {
                let find = this.units.find((unit) => {
                    return unit.id == this.edit_id
                })
                find.name = this.name
                this.edit_id = ''
                $('.close').click()
            })
            .catch((error) => {
                alert(error)
            })
        },
        delete_item(id, i) {
            if(!confirm('Are you sure you want to delete this unit?')) return
            axios.delete("/api/admin/accounting/unit/" + id).then((response) => {
                this.units.splice(i, 1)
            })
            .catch((error) => {
                alert(error)
            })
        },
    },
    mounted() {
        this.units = window.units
    }
}
</script>
<style lang="">

</style>
