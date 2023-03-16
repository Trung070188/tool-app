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
                        <li v-if="entry.id" class="breadcrumb-item active" aria-current="page">Sửa đối tác</li>
                        <li v-else class="breadcrumb-item active" aria-current="page">Thêm mới đối tác</li>
                    </ol>
                </div>
            </div> <!-- /breadcrumb --> <!-- row -->

            <div class="row row-sm">

                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header pb-0">
                            <div class="d-flex justify-content-between">
                                <h4 v-if="entry.id" class="card-title mg-b-0">Sửa đối tác</h4>
                                <h4 v-else class="card-title mg-b-0">Thêm mới đối tác</h4>
                            </div>

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
                            <div class="form-group" v-if="entry.id && entry.check_copy!==1">
                                <label>Re-generate</label>
                                <input class="form-control" style="cursor: pointer" v-model="entry.secret" readonly @click="copyTextToken">
                            </div>
                            <div class="form-group" v-if="entry.id && entry.check_copy==1">
                                <label>Re-generate</label>
                                <input class="form-control" style="cursor: pointer" v-model="token" readonly>
                            </div>
                            <button class="btn btn-primary" @click="createToken">Tạo mới Token</button>
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
            token:'',
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
        },
       async copyTextToken() {
            navigator.clipboard.writeText(this.entry.secret);
            const res = await $post('/xadmin/partners/checkCopy?id='+this.entry.id)
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
        },
       async createToken()
        {
            let randomString = '';
            const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

            for ( let i = 0; i < 32; i++ ) {
                randomString += characters.charAt(Math.floor(Math.random() * characters.length));
            }
            this.entry.secret=randomString;
            this.entry.check_copy=0;
            const res = await $post('/xadmin/partners/createToken?id='+this.entry.id + '&token='+this.entry.secret)
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
    },
    mounted() {
        console.log(this.entry.secret)
        this.token=this.entry.secret || '*****'
        this.token= this.token.slice(0, -15) + "**********";
    }
}
</script>

<style scoped>

</style>
