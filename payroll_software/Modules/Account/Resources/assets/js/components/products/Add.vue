<template>
    <div>
        <!-- breadcrumb -->
        <div class="row">
            <div class="col-md-6">
                <h4>Products</h4>
            </div>
            <div class="col-md-6 text-right">
                <a :href="ProductList()" class="btn btn-success">List Products</a>
            </div>
        </div>

        <!-- body -->
        <div class="card mt-2">
            <div class="card-header">
                <h4>Add Product</h4>
            </div>

            <div class="card-body">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Enter name">
                </div>


                <div class="form-group">
                    <label for="unit">Unit</label>
                    <input type="text" class="form-control" id="unit" name="unit" placeholder="Enter unit">
                </div>

                <div class="form-group">
                    <label for="price">Price</label>
                    <input type="text" class="form-control" id="price" name="price" placeholder="Enter price">
                </div>
            </div>


            <div class="card-footer">
                <button class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</template>
<script>
import {ProductList} from "../../routes";
import {defineComponent} from "vue";

export default defineComponent({
    name: "AccountModulesProductsAdd",
    methods: {
        ProductList() {
            return ProductList
        }
    }
})

</script>
<style lang="">

</style>
