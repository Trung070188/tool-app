<template>
    <div class="main-content app-content"> <!-- container -->
        <ActionBar label="Lưu lại" @action="save()" backUrl="/xadmin/campaign_partners/index"/>
        <div class="main-container container-fluid"> <!-- breadcrumb -->
            <div class="breadcrumb-header justify-content-between">
<!--                <div class="left-content"><span class="main-content-title mg-b-0 mg-b-lg-1">CampaignPartner</span></div>-->
                <div class="justify-content-center mt-2">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item tx-15"><a href="/xadmin/dashboard/index">HOME</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Partner Campaign</li>
                        <li class="breadcrumb-item active" aria-current="page">Thêm mới campaign partner</li>
                    </ol>
                </div>
            </div> <!-- /breadcrumb --> <!-- row -->

            <div class="row row-sm">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header pb-0">
                            <div class="d-flex justify-content-between">
                                <h4 class="card-title mg-b-0">Thêm mới campaign partner</h4></div>

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
                                    <error-label for="f_ partner_campaign_id" ></error-label>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label>Campaign</label>
                                    <select class="form-select form-control" v-model="entry.campaign_id" @change="load()">
                                        <option v-for="campaign in campaigns" :value="campaign.id">{{campaign.name}}</option>
                                    </select>
                                    <error-label for="f_ partner_campaign_id" ></error-label>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label>Partner</label>
                                    <select class="form-select form-control" v-model="entry.partner_id">
                                        <option v-for="partner in partners" :value="partner.id">{{partner.name}}</option>
                                    </select>
                                    <error-label for="f_campaign_id" ></error-label>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label>Price</label>
                                    <input v-model="entry.price"  name="name"
                                           class="form-control"
                                           placeholder="price">
                                    <error-label for="f_partner_id"></error-label>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label>Url Partner</label>
                                    <input  name="name"
                                            class="form-control"
                                            placeholder="url partner">
                                    <error-label for="f_partner_id"></error-label>
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
                partners:[],
                dataFilterCampaign:{
                },
                campaignId:'',
                campaigns:[],
                entry: $json.entry || {},
                isLoading: false,
                errors: {}
            }
        },
        mounted() {
            $router.on('/', this.load).init();
        },
        methods: {
            async save() {
                console.log(this.dataFilterCampaign);
                this.isLoading = true;
               var seft=this;
                const res = await $post('/xadmin/campaign_partners/save', {entry:seft.entry});
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
            async load() {
                let query = $router.getQuery();
                const res = await $get('/xadmin/campaign_partners/dataEdit?campaign_id='+this.campaignId, query);
                this.paginate = res.paginate;
                this.campaigns=res.campaigns;
                this.partners=res.partners;
                if(res.data_filter_campaign!=null)
                {
                    this.dataFilterCampaign=res.data_filter_campaign;

                }
            },

        }
    }
</script>

<style scoped>

</style>
