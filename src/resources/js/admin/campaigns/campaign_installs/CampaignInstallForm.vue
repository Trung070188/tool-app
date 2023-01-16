<template>
    <div class="main-content app-content"> <!-- container -->
        <ActionBar label="Lưu lại" @action="save()" backUrl="/xadmin/campaign_installs/index"/>
        <div class="main-container container-fluid"> <!-- breadcrumb -->
            <div class="breadcrumb-header justify-content-between">
                <div class="left-content"><span class="main-content-title mg-b-0 mg-b-lg-1">CampaignInstall</span></div>
                <div class="justify-content-center mt-2">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item tx-15"><a href="/xadmin/dashboard/index">HOME</a></li>
                        <li class="breadcrumb-item active" aria-current="page">CampaignInstall</li>
                    </ol>
                </div>
            </div> <!-- /breadcrumb --> <!-- row -->

            <div class="row row-sm">

                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header pb-0">
                            <div class="d-flex justify-content-between">
                                <h4 class="card-title mg-b-0">CampaignInstall Form</h4></div>

                        </div>
                        <div class="card-body">

                            <input v-model="entry.id" type="hidden" name="id">
                                                            <div class="form-group">
                                    <label>Campaign Id</label>
                                    <input id="f_campaign_id" v-model="entry.campaign_id" name="name"
                                           class="form-control"
                                           placeholder="campaign_id">
                                    <error-label for="f_campaign_id" :errors="errors.campaign_id"></error-label>
                                </div>
                                                            <div class="form-group">
                                    <label>Partner Campaign Id</label>
                                    <input id="f_partner_campaign_id" v-model="entry.partner_campaign_id" name="name"
                                           class="form-control"
                                           placeholder="partner_campaign_id">
                                    <error-label for="f_partner_campaign_id" :errors="errors.partner_campaign_id"></error-label>
                                </div>
                                                            <div class="form-group">
                                    <label>Partner Id</label>
                                    <input id="f_partner_id" v-model="entry.partner_id" name="name"
                                           class="form-control"
                                           placeholder="partner_id">
                                    <error-label for="f_partner_id" :errors="errors.partner_id"></error-label>
                                </div>
                                                            <div class="form-group">
                                    <label>Installed At</label>
                                    <input id="f_installed_at" v-model="entry.installed_at" name="name"
                                           class="form-control"
                                           placeholder="installed_at">
                                    <error-label for="f_installed_at" :errors="errors.installed_at"></error-label>
                                </div>
                                                            <div class="form-group">
                                    <label>Device Id</label>
                                    <input id="f_device_id" v-model="entry.device_id" name="name"
                                           class="form-control"
                                           placeholder="device_id">
                                    <error-label for="f_device_id" :errors="errors.device_id"></error-label>
                                </div>
                                                            <div class="form-group">
                                    <label>Ip</label>
                                    <input id="f_ip" v-model="entry.ip" name="name"
                                           class="form-control"
                                           placeholder="ip">
                                    <error-label for="f_ip" :errors="errors.ip"></error-label>
                                </div>
                                                            <div class="form-group">
                                    <label>Os</label>
                                    <input id="f_os" v-model="entry.os" name="name"
                                           class="form-control"
                                           placeholder="os">
                                    <error-label for="f_os" :errors="errors.os"></error-label>
                                </div>
                                                    </div>
                    </div>
                </div> <!--/div--> <!--div-->

            </div> <!-- /row -->
        </div>


    </div> <!-- /main-content -->

</template>

<script>
    import {$post} from "../../../utils";
    import ActionBar from '../../../components/ActionBar';

    export default {
        name: "CampaignInstallsForm.vue",
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
                const res = await $post('/xadmin/campaign_installs/save', {entry: this.entry});
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
                        location.replace('/xadmin/campaign_installs/edit?id=' + res.id);
                    }
                }
            }
        }
    }
</script>

<style scoped>

</style>
