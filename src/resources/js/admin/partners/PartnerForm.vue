<template>
    <div class="main-content app-content"> <!-- container -->
        <ActionBar label="Lưu lại" @action="save()" backUrl="/xadmin/partners/index"/>
        <div class="main-container container-fluid"> <!-- breadcrumb -->
            <div class="breadcrumb-header justify-content-between">
<!--                <div class="left-content"><span class="main-content-title mg-b-0 mg-b-lg-1">Partner</span></div>-->
                <div class="justify-content-center mt-2">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item tx-15"><a href="/xadmin/dashboard/index">HOME</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Đối tác</li>
                        <li class="breadcrumb-item active" aria-current="page">Thêm mới đối tác</li>
                    </ol>
                </div>
            </div> <!-- /breadcrumb --> <!-- row -->

            <div class="row row-sm">

                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header pb-0">
                            <div class="d-flex justify-content-between">
                                <h4 class="card-title mg-b-0">Thêm mới đối tác</h4></div>

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
                                    <label>Ip</label>
                                    <input id="f_ip" v-model="entry.ip" name="name"
                                           class="form-control"
                                           placeholder="ip">
                                    <error-label for="f_ip" :errors="errors.ip"></error-label>
                                </div>
                                                            <div class="form-group">
                                    <label>Note</label>
                                    <input id="f_note" v-model="entry.note" name="name"
                                           class="form-control"
                                           placeholder="note">
                                    <error-label for="f_note" :errors="errors.note"></error-label>
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
        name: "PartnersForm.vue",
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
                const res = await $post('/xadmin/partners/save', {entry: this.entry});
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
                        location.replace('/xadmin/partners/edit?id=' + res.id);
                    }
                }
            }
        }
    }
</script>

<style scoped>

</style>
