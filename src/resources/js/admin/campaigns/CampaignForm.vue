<template>
    <div class="main-content app-content"> <!-- container -->
        <ActionBar label="Lưu lại" @action="save()" @clone="clone()" :check="check" backUrl="/xadmin/campaigns/index"/>
        <div class="main-container container-fluid"> <!-- breadcrumb -->
            <div class="breadcrumb-header justify-content-between">
<!--                <div class="left-content"><span class="main-content-title mg-b-0 mg-b-lg-1">Campaign</span></div>-->
                <div class="justify-content-center mt-2">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item tx-15"><a href="/xadmin/dashboard/index">HOME</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Campaign</li>
                        <li class="breadcrumb-item active" aria-current="page">Thêm mới</li>
                    </ol>
                </div>
            </div> <!-- /breadcrumb --> <!-- row -->

            <div class="row row-sm">

                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header pb-0">
                            <div class="d-flex justify-content-between">
                                <h4 v-if="entry.id" class="card-title mg-b-0">Sửa campaign</h4>
                                <h4 v-else class="card-title mg-b-0">Thêm mới campaign</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <input v-model="entry.id" type="hidden" name="id">

                                <div class="form-group col-lg-6">
                                    <label>Store url</label>
                                    <input  v-model="entry.store_url" name="name"
                                            @update:modelValue="value => onUrlStoreChange(value)"
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
                                    <QFileManagerInput v-model="entry.icon"
                                                          input-id="campaign_icon"
                                                        placeholder="icon"/>
                                    <error-label for="f_icon" :errors="errors.icon"></error-label>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label>Price</label>
                                    <input id="f_price" v-model="format" name="name"
                                           class="form-control"
                                           placeholder="price" @input="formatPrice(format)">
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
                                    <select class="js-example-responsive" style="width: 100%" v-model="entry.customer_id">
                                        <option v-for="customer in customers" :value="customer.id">{{customer.id}}-{{customer.name}}</option>
                                    </select>
<!--                                    <select class="form-control form-select" v-model="entry.customer_id">-->
<!--                                        <option value="">Chọn khách hàng</option>-->
<!--                                        <option v-for="customer in customers" :value="customer.id">{{customer.name}}</option>-->
<!--                                    </select>-->
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
                                    <label>Bật fake số</label>
                                    <div>
                                        <switch-button  v-model="entry.is_fake_on"></switch-button>

                                    </div>

                                    <label>Hourly fake install</label>
                                    <input :disabled="!entry.is_fake_on"  v-model="entry.hourly_fake_install" name="name"
                                            class="form-control"
                                            placeholder="Hourly fake install">
                                    <error-label for="f_status" :errors="errors.hourly_fake_install"></error-label>
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
                                    <div style="margin-top:10px">
                                        <label>Total daily install</label>
                                        <input  v-model="entry.daily_install" name="name"
                                                class="form-control"
                                                placeholder="Total install">
                                        <error-label for="f_status" :errors="errors.daily_install"></error-label>
                                    </div>
                                </div>


                                <div class="form-group col-lg-6">
                                    <label>Tự động bật</label>
                                    <div>
                                        <switch-button  v-model="entry.auto_on_status"></switch-button>
                                    </div>
                                        <label>Tự động bật lúc</label>
                                        <Datepicker :timepicker="true"  v-model="entry.auto_on_at" name="name"
                                                    class="form-control"
                                                    placeholder="Auto on at"></Datepicker>
                                        <error-label for="f_status" :errors="errors.auto_on_at"></error-label>

                                </div>

                                <div class="form-group col-lg-6">
                                    <label>Tự động tắt</label>
                                    <div>
                                        <switch-button  v-model="entry.auto_off_status"></switch-button>

                                    </div>
                                        <label>Tự động tắt lúc</label>
                                        <Datepicker :timepicker="true"   v-model="entry.auto_off_at" name="name"
                                                    class="form-control"
                                                    placeholder="Auto off at"></Datepicker>
                                        <error-label for="f_status" :errors="errors.auto_off_at"></error-label>

                                </div>
                                <div class="form-group col-lg-12">
                                    <label>Note</label>
                                    <RichtextEditor  v-model="entry.note"></RichtextEditor>
                                    <error-label for="f_status" :errors="errors.note"></error-label>
                                </div>

                                <div class="form-group col-lg-12" v-if="eventLogs.length">
                                    <label>Sự kiện gần đây</label>
                                    <ul style="height: 300px; overflow-y: auto">
                                        <li v-for="log in eventLogs" >
                                            <em v-text="log.title"></em>
                                        </li>
                                    </ul>
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
    import {format} from "../../../../public/assets/plugins/echart/echart";
    export default {
        name: "CampaignsForm.vue",
        components: {
            QFileManagerInput, RichtextEditor, Datepicker, SwitchButton, FileManagerInput, Uploader, ActionBar},
        data() {
            let format;
            if($json.entry.id)
            {
                format=$json.entry.price

            }
            else {
                format=''
            }

            return {
                check: 0,
                format: format,
                customers: $json.customer || [],
                entry: $json.entry || {
                    customer_id: '',
                    price: ''
                },
                isLoading: false,
                errors: {},
                eventLogs: $json.eventLogs,
            }
        },
        mounted() {
            if(this.entry.id)
            {
                this.check = 1;
            }
            const vm = this;
            $(".js-example-responsive").select2({
            }).on("change", function(e) {
                vm.entry.customer_id = $(this).val();
            });
        },
        methods: {
            formatPrice(price)
            {
                this.entry.price = this.format.replace(/[^0-9.-]+/g, '');
                if (this.entry.price === '') {
                    this.formatBooking = '0';
                }
                else {
                    this.format = parseFloat(this.entry.price).toLocaleString('en-US', {
                        style: 'currency',
                        currency: 'VND'
                    });
                }
            },
            async onUrlStoreChange(value) {
                const res = await $post('/xadmin/campaigns/getAppIcon', {
                    url: value
                });

                if (res.code === 200) {
                    this.entry.icon = [{
                        is_image:true,
                        id: res.data.id,
                        url: res.data.icon
                    }];
                    this.entry.package_id = res.data.package_id;
                    this.entry.name = res.data.name;
                    this.entry.os = res.data.os;
                } else {
                    toastr.error('Không lấy được icon');
                }
            },
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
            },
            async clone() {
                this.isLoading = true;
                const res = await $post('/xadmin/campaigns/clone', {entry: this.entry});
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
                    location.replace('/xadmin/campaigns/edit?id=' +res.id);
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
