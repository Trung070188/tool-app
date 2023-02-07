<template>
    <div class="main-content app-content"> <!-- container -->
        <ActionBar label="Lưu lại" @action="save()" backUrl="/xadmin/campaigns/index"/>
        <div class="main-container container-fluid"> <!-- breadcrumb -->
            <div class="breadcrumb-header justify-content-between">
<!--                <div class="left-content"><span class="main-content-title mg-b-0 mg-b-lg-1">Campaign</span></div>-->
                <div class="justify-content-center mt-2">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item tx-15"><a href="/xadmin/dashboard/index">HOME</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Campaign</li>
                        <li class="breadcrumb-item active" aria-current="page">Sửa campaign</li>
                    </ol>
                </div>
            </div> <!-- /breadcrumb --> <!-- row -->

            <div class="row row-sm">

                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header pb-0">
                            <div class="d-flex justify-content-between">
                                <h4 class="card-title mg-b-0">Thêm mới campaign</h4></div>

                        </div>
                        <div class="card-body">
                            <div class="row">
                                <input v-model="entry.id" type="hidden" name="id">

                                <div class="form-group col-lg-6">
                                    <label>Store url</label>
                                    <input  v-model="entry.store_url" name="name"
                                            class="form-control"
                                            placeholder="Store url">
                                    <error-label for="f_status" :errors="errors.store_url"></error-label>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label>Name</label>
                                    <input id="f_name" v-model="entry.name" name="name"
                                           class="form-control"
                                           placeholder="name">
                                    <error-label for="f_name" :errors="errors.name"></error-label>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label>Type</label>
                                    <br>
                                    <input  v-model="entry.type" type="radio" value="cpi"/>
                                    <label style="margin-right: 20px;margin-left: 5px">CPI</label>
                                    <input  v-model="entry.type" type="radio" value="rate"/>
                                    <label style="margin-right: 20px;margin-left: 5px">Rate</label>
                                    <input  v-model="entry.type" type="radio" value="map"/>
                                    <label style="margin-right: 20px;margin-left: 5px">Map</label>
                                    <input  v-model="entry.type" type="radio" value="top_keyword"/>
                                    <label style="margin-right: 20px;margin-left: 5px">Top keyword</label>
                                    <error-label for="f_type" :errors="errors.type"></error-label>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label>Icon</label>
                                    <q-file-manager-input v-model="entry.icon"
                                                          placeholder="icon"></q-file-manager-input>
                                    <error-label for="f_icon" :errors="errors.icon"></error-label>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label>Price</label>
                                    <input id="f_price" v-model="entry.price" name="name"
                                           class="form-control"
                                           placeholder="price">
                                    <error-label for="f_price" :errors="errors.price"></error-label>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label>Os</label>
                                    <br>
                                    <input  id="f_os" v-model="entry.os" type="radio" value="ios"/>
                                    <label style="margin-right: 20px;margin-left: 5px">ios</label>
                                    <input  id="f_os1" v-model="entry.os" type="radio" value="android"/>
                                    <label style="margin-right: 20px;margin-left: 5px">android</label>
                                    <input  id="f_os2" v-model="entry.os" type="radio" value="all"/>
                                    <label style="margin-right: 20px;margin-left: 5px">all</label>
                                    <error-label for="f_os" :errors="errors.os"></error-label>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label>Tên khách hàng</label>
                                    <!--                                    <input id="f_customer_id" v-model="entry.customer_id" name="name"-->
                                    <!--                                           class="form-control"-->
                                    <!--                                           placeholder="Tên khách hàng">-->
                                    <select class="form-control form-select" v-model="entry.customer_id">
                                        <option v-for="customer in customers" :value="customer.id">{{customer.name}}</option>
                                    </select>
                                    <error-label for="f_customer_id" :errors="errors.customer_id"></error-label>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label>Package Id</label>
                                    <input id="f_package_id" v-model="entry.package_id" name="name"
                                           class="form-control"
                                           placeholder="package_id">
                                    <error-label for="f_package_id" :errors="errors.package_id"></error-label>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label>Status</label>
                                    <div>
                                        <switch-button v-model="entry.status" class="form-control"></switch-button>

                                    </div>
                                    <error-label for="f_status" :errors="errors.status"></error-label>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label>Daily fake install</label>
                                    <input  v-model="entry.daily_fake_install" name="name"
                                            class="form-control"
                                            placeholder="Daily fake install">
                                    <error-label for="f_status" :errors="errors.daily_fake_install"></error-label>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label>Open next day</label>
                                    <div>
                                        <switch-button class="form-control" v-model="entry.open_next_day"></switch-button>

                                    </div>
                                    <error-label for="f_status" :errors="errors.open_next_day"></error-label>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label>Total install</label>
                                    <input  v-model="entry.total_install" name="name"
                                            class="form-control"
                                            placeholder="Total install">
                                    <error-label for="f_status" :errors="errors.total_install"></error-label>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label>Is fake on</label>
                                    <div>
                                        <switch-button  v-model="entry.is_fake_on"></switch-button>

                                    </div>
                                    <error-label for="f_status" :errors="errors.is_fake_on"></error-label>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label>Auto on at</label>
                                    <Datepicker  v-model="entry.auto_on_at" name="name"
                                                 class="form-control"
                                                 placeholder="Auto on at"></Datepicker>
                                    <error-label for="f_status" :errors="errors.auto_on_at"></error-label>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label>Auto off at</label>
                                    <Datepicker  v-model="entry.auto_off_at" name="name"
                                                 class="form-control"
                                                 placeholder="Auto off at"></Datepicker>
                                    <error-label for="f_status" :errors="errors.auto_off_at"></error-label>
                                </div>
                                <div class="form-group col-lg-12">
                                    <label>Note</label>
                                    <RichtextEditor  v-model="entry.note"></RichtextEditor>
                                    <error-label for="f_status" :errors="errors.note"></error-label>
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
    import Uploader from "../../components/Uploader";
    import FileManagerInput from "../../components/FileManagerInput";
    import SwitchButton from "../../components/SwitchButton";
    import Datepicker from "../../components/Datepicker";
    import RichtextEditor from "../../components/RichtextEditor";
    import QFileManagerInput from "../../components/QFileManagerInput";

    export default {
        name: "CampaignDetail.vue",
        components: {QFileManagerInput, RichtextEditor, Datepicker, SwitchButton, FileManagerInput, Uploader, ActionBar},
        data() {
            console.log($json.customer);
            return {
                customers:$json.customer || [],
                entry: $json.entry || {},
                isLoading: false,
                errors: {}
            }
        },
        methods: {
            async save() {
                this.isLoading = true;
                const res = await $post('/xadmin/campaigns/save', {entry: this.entry});
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
                        location.replace('/xadmin/campaigns/edit?id=' + res.id);
                    }
                }
            }
        }
    }
</script>

<style scoped>
    input[type="radio"] {
        width: 15px;
        height: 15px;
    }
</style>
