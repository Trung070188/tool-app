<template>
    <div class="main-content app-content"> <!-- container -->
        <ActionBar label="Lưu lại" @action="save()" :check="check" @clone="clone()" backUrl="/xadmin/campaign_partners/index"/>
        <div class="main-container container-fluid"> <!-- breadcrumb -->
            <div class="breadcrumb-header justify-content-between">
<!--                <div class="left-content"><span class="main-content-title mg-b-0 mg-b-lg-1">CampaignPartner</span></div>-->
                <div class="justify-content-center mt-2">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item tx-15"><a href="/xadmin/dashboard/index">HOME</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Partner Campaign</li>
                        <li v-if="entry.id" class="breadcrumb-item active" aria-current="page">Sửa campaign partner</li>
                        <li v-else class="breadcrumb-item active" aria-current="page">Thêm mới campaign partner</li>
                    </ol>
                </div>
            </div> <!-- /breadcrumb --> <!-- row -->

            <div class="row row-sm">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header pb-0">
                            <div class="d-flex justify-content-between">
                                <h4 v-if="entry.id" class="card-title mg-b-0">Sửa campaign partner</h4>
                                <h4 v-else class="card-title mg-b-0">Thêm mới campaign partner</h4>
                            </div>

                        </div>
                        <div class="card-body">

                            <div class="row">
                                <input v-model="entry.id" type="hidden" name="id">
                                <div class="form-group col-lg-6">
                                    <label> Name</label>
                                    <input  v-model="entry.name" name="name"
                                            class="form-control"
                                            placeholder="name">
                                    <error-label for="f_ partner_campaign_id" :errors="errors.name"></error-label>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label>Os</label>
                                    <!--                                <input   name="name" v-model="entry.os"-->
                                    <!--                                         class="form-control"-->
                                    <!--                                         placeholder=" partner_campaign_id">-->
                                    <select   name="name" v-model="entry.os"
                                              class="form-control form-select">
                                        <option value="android">Android</option>
                                        <option value="ios">Ios</option>
                                    </select>
                                    <error-label :errors="errors.os" ></error-label>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label>Campaign</label>
                                    <select class="js-example-responsive" style="width: 100%" v-model="entry.campaign_id">
                                        <option v-for="campaign in campaigns" :value="campaign.id">{{campaign.id}}-{{campaign.name}}</option>
                                    </select>
<!--                                    <select class="form-select form-control" v-model="entry.campaign_id">-->
<!--                                        <option v-for="campaign in campaigns" :value="campaign.id">{{campaign.name}}</option>-->
<!--                                    </select>-->
                                    <error-label :errors="errors.campaign_id" ></error-label>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label>Partner</label>
                                    <select class="js-example-responsive-abc" style="width: 100%" v-model="entry.partner_id">
                                        <option v-for="partner in partners" :value="partner.id">{{partner.id}}-{{partner.name}}</option>
                                    </select>

                                    <error-label :errors="errors.partner_id" ></error-label>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label>Price</label>
                                    <input v-model="entry.price"  name="name"
                                           class="form-control"
                                           placeholder="price">
                                    <error-label :errors="errors.price"></error-label>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label>Url Partner</label>
                                    <input  name="name"
                                            class="form-control"
                                            placeholder="url partner">
                                    <error-label ></error-label>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label>Status</label>
                                    <div>
                                        <switch-button v-model="entry.status"></switch-button>

                                    </div>
                                    <error-label for="f_partner_id"></error-label>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label>Oper next day</label>
                                    <div>
                                        <switch-button v-model="entry.open_next_day"></switch-button>

                                    </div>
                                    <error-label for="f_partner_id"></error-label>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label>Share data</label>
                                    <div>
                                        <switch-button v-model="entry.share_data"></switch-button>

                                    </div>
                                    <error-label for="f_partner_id"></error-label>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label>Total install</label>
                                    <input  v-model="entry.total_install" name="name"
                                            class="form-control"
                                            placeholder="Total install">
                                    <error-label for="f_status" :errors="errors.total_install"></error-label>
                                    <div style="margin-top:10px">
                                        <label>Total daily install</label>
                                        <input  v-model="entry.daily_install" name="name"
                                                class="form-control"
                                                placeholder="Total install">
                                        <error-label for="f_status" :errors="errors.daily_install"></error-label>
                                    </div>
                                </div>
                                <div class="form-group col-lg-12">
                                    <label>Note</label>
                                    <richtext-editor v-model="entry.note"></richtext-editor>
                                    <error-label for="f_partner_id"></error-label>
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
    import {$get, $post} from "../../../utils";
    import ActionBar from '../../../components/ActionBar';
    import $router from "../../../lib/SimpleRouter";
    import SwitchButton from "../../../components/SwitchButton";
    import RichtextEditor from "../../../components/RichtextEditor";

    export default {
        name: "PartnerCampaignForm.vue",
        components: {RichtextEditor, SwitchButton, ActionBar},
        data() {
            return {
                check: 0,
                campaigns: $json.campaigns,
                partners: $json.partners,
                dataFilterCampaign: {},
                campaignId: '',
                entry: $json.entry || {},
                isLoading: false,
                errors: {}
            }
        },
        mounted() {
            if(this.entry.id)
            {
                this.check = 1
            }
            const vm = this;
            $(".js-example-responsive").select2({
            }).on("change", function(e) {
                vm.entry.campaign_id = $(this).val();
            });
            $(".js-example-responsive-abc").select2({
            }).on("change", function(e) {
                vm.entry.partner_id = $(this).val();
            });
        },
        methods: {
            async save() {
                this.isLoading = true;
                const res = await $post('/xadmin/campaign_partners/save', {entry:this.entry});
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
                        location.replace('/xadmin/campaign_partners/edit?id=' + res.id);
                    }
                }
            },

            async clone() {
                this.isLoading = true;
                const res = await $post('/xadmin/campaign_partners/clone', {entry: this.entry});
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
                    location.replace('/xadmin/campaign_partners/edit?id=' +res.id);
                    if (!this.entry.id) {
                        location.replace('/xadmin/campaign_partners/edit?id=' + res.id);
                    }
                }
            }
        }
    }
</script>

<style scoped>

</style>
