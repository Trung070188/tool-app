<template>
    <div class="main-content app-content"> <!-- container -->
        <ActionBar label="Lưu lại" @action="save()" backUrl="/xadmin/customers/index"/>
        <div class="main-container container-fluid"> <!-- breadcrumb -->
            <div class="breadcrumb-header justify-content-between">
                <div class="left-content"><span class="main-content-title mg-b-0 mg-b-lg-1">Khách hàng</span></div>
                <div class="justify-content-center mt-2">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item tx-15"><a href="/xadmin/dashboard/index">Trang chủ</a></li>
                        <li v-if="entry.id" class="breadcrumb-item active" aria-current="page">Sửa khách hàng</li>
                        <li v-else class="breadcrumb-item active" aria-current="page">Thêm mới khách hàng</li>
                    </ol>
                </div>
            </div> <!-- /breadcrumb --> <!-- row -->

            <div class="row row-sm">

                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header pb-0">
                            <div class="d-flex justify-content-between">
                                <h4 v-if="entry.id" class="card-title mg-b-0">Sửa khách hàng</h4>
                                <h4 v-else class="card-title mg-b-0">Thêm mới khách hàng</h4>
                            </div>

                        </div>
                        <div class="card-body">
                            <div class="row">
                                <input v-model="entry.id" type="hidden" name="id">
                                <div class="form-group col-lg-6">
                                    <label>Tên khách hàng</label>
                                    <input id="f_name" v-model="entry.name" name="name"
                                           class="form-control"
                                           placeholder="Tên khách hàng">
                                    <error-label for="f_name" :errors="errors.name"></error-label>
                                </div>
<!--                                <div class="form-group col-lg-6">-->
<!--                                    <label>Email</label>-->
<!--                                    <input id="f_email" v-model="entry.email" name="name"-->
<!--                                           class="form-control"-->
<!--                                           placeholder="email">-->
<!--                                    <error-label for="f_email" :errors="errors.email"></error-label>-->
<!--                                </div>-->
                                <div class="form-group col-lg-6">
                                    <label>Số điện thoại</label>
                                    <input id="f_phone" v-model="entry.phone" name="name"
                                           class="form-control"
                                           placeholder="Số điện thoại">
                                    <error-label for="f_phone" :errors="errors.phone"></error-label>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label>Công ty</label>
                                    <input id="f_company" v-model="entry.company" name="name"
                                           class="form-control"
                                           placeholder="Công ty">
                                    <error-label for="f_company" :errors="errors.company"></error-label>
                                </div>
                                <div class="form-group col-lg-6" >
                                    <label>Tên đăng nhập</label>
                                    <input  v-model="entry.username" name="name" type="text"
                                            class="form-control"
                                            placeholder="Tên đăng nhập">
                                    <error-label for="f_description" :errors="errors.username"></error-label>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label>Địa chỉ</label>
                                    <input id="f_description" v-model="entry.description" name="name"
                                           class="form-control"
                                           placeholder="Địa chỉ">
                                    <error-label for="f_description" :errors="errors.description"></error-label>
                                </div>
                                <div class="form-group col-lg-6" >
                                    <label>Mật khẩu</label>
                                    <input  v-model="entry.show_pass" name="name" type="text"
                                            class="form-control"
                                            placeholder="Mật khẩu">
                                    <error-label for="f_description" :errors="errors.show_pass"></error-label>
                                </div>

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
                entry:$json.entry ||{
                    'name':'',
                    'email':'',
                    'phone':'',
                    'company':'',
                    'show_pass':'',
                    'description':''
                },
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
