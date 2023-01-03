<template>
    <div class="main-content app-content"> <!-- container -->
        <ActionBar label="Lưu lại" @action="save()" backUrl="/xadmin/customers/index"/>
        <div class="main-container container-fluid"> <!-- breadcrumb -->
            <div class="breadcrumb-header justify-content-between">
                <div class="left-content"><span class="main-content-title mg-b-0 mg-b-lg-1">Customer</span></div>
                <div class="justify-content-center mt-2">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item tx-15"><a href="/xadmin/dashboard/index">HOME</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Customer</li>
                    </ol>
                </div>
            </div> <!-- /breadcrumb --> <!-- row -->

            <div class="row row-sm">

                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header pb-0">
                            <div class="d-flex justify-content-between">
                                <h4 class="card-title mg-b-0">Customer Form</h4></div>

                        </div>
                        <div class="card-body">

                            <input v-model="entry.id" type="hidden" name="id">
                                                            <div class="form-group">
                                    <label>Name</label>
                                    <input id="f_name" v-model="entry.name" name="name"
                                           class="form-control"
                                           placeholder="name">
                                    <error-label for="f_name" :errors="errors.name"></error-label>
                                </div>
                                                            <div class="form-group">
                                    <label>Email</label>
                                    <input id="f_email" v-model="entry.email" name="name"
                                           class="form-control"
                                           placeholder="email">
                                    <error-label for="f_email" :errors="errors.email"></error-label>
                                </div>
                                                            <div class="form-group">
                                    <label>Phone</label>
                                    <input id="f_phone" v-model="entry.phone" name="name"
                                           class="form-control"
                                           placeholder="phone">
                                    <error-label for="f_phone" :errors="errors.phone"></error-label>
                                </div>
                                                            <div class="form-group">
                                    <label>Company</label>
                                    <input id="f_company" v-model="entry.company" name="name"
                                           class="form-control"
                                           placeholder="company">
                                    <error-label for="f_company" :errors="errors.company"></error-label>
                                </div>
                                                            <div class="form-group">
                                    <label>Description</label>
                                    <input id="f_description" v-model="entry.description" name="name"
                                           class="form-control"
                                           placeholder="description">
                                    <error-label for="f_description" :errors="errors.description"></error-label>
                                </div>
                                                    </div>
                    </div>
                </div> <!--/div--> <!--div-->

            </div> <!-- /row -->
        </div>


    </div> <!-- /main-content -->

</template>

<script>
    import {$post} from "../../utils";
    import ActionBar from '../../components/ActionBar';

    export default {
        name: "CustomersForm.vue",
        components: {ActionBar},
        data() {
            return {
                entry: $json.entry || {},
                isLoading: false,
                errors: {}
            }
        },
        methods: {
            async save() {
                this.isLoading = true;
                const res = await $post('/xadmin/customers/save', {entry: this.entry});
                this.isLoading = false;
                if (res.errors) {
                    this.errors = res.errors;
                    return;
                }
                if (res.code) {
                    toastr.error(res.message);
                } else {
                    this.errors = {};
                    toastr.success(res.message);

                    if (!this.entry.id) {
                        location.replace('/xadmin/customers/edit?id=' + res.id);
                    }
                }
            }
        }
    }
</script>

<style scoped>

</style>
