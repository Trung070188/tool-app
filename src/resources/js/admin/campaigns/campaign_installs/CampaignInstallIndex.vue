<template>
    <div class="main-content app-content"> <!-- container -->
        <div class="main-container container-fluid"> <!-- breadcrumb -->
            <div class="breadcrumb-header justify-content-between">
                <!--                <div class="left-content"><span class="main-content-title mg-b-0 mg-b-lg-1">CampaignInstall</span></div>-->
                <div class="justify-content-center mt-2">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item tx-15"><a href="/xadmin/dashboard/index">HOME</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Campaign Partner</li>
                        <li class="breadcrumb-item active" aria-current="page">Thống kê</li>
                    </ol>
                </div>
            </div> <!-- /breadcrumb --> <!-- row -->

            <div class="row row-sm">

                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header pb-0">
                            <div class="d-flex justify-content-between">
                                <h4 class="card-title mg-b-0">Thống kê</h4></div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                        <!--                                        <div class="form-group mx-sm-3 mb-2">-->
                                        <!--                                            <input @keydown.enter="doFilter('keyword', filter.keyword, $event)" v-model="filter.keyword"-->
                                        <!--                                                   type="text"-->
                                        <!--                                                   class="form-control" placeholder="tìm kiếm" >-->
                                        <!--                                        </div>-->
                                        <div class="form-group col-lg-3">
                                            <label>Campaign</label>
                                            <select class="js-example-responsive" v-model="filter.campaign" style="width: 100%" required>
                                                <option value="" disabled selected>Chọn campaign</option>
                                                <option value="0">All</option>
                                                <option v-for="campaign in campaigns" :value="campaign.id">{{campaign.id}}-{{campaign.name}}</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-lg-3">
                                            <label>Partner</label>
                                            <select class="js-example-responsive-a" style="width: 100%" v-model="filter.partner_name" required>
                                                <option value="" disabled selected>Chọn partner</option>
                                                <option value="0">All</option>
                                                <option v-for="partner in partners" :value="partner.id">{{partner.id}}-{{partner.name}}</option>
                                            </select>
<!--                                            <input @keydown.enter="doFilter('partner_name', filter.partner_name, $event)" v-model="filter.partner_name"-->
<!--                                                   type="text"-->
<!--                                                   class="form-control" placeholder="Partner" >-->
                                        </div>
                                        <div class="form-group col-lg-3">
                                            <label>Chọn thời gian thống kê</label>
                                            <Daterangepicker
                                                @update:modelValue="(value) => doFilter('created', value, $event)"
                                                v-model="filter.created" placeholder="Ngày tạo"></Daterangepicker>
                                        </div>

                                        <div class="form-group col-lg-3">
                                            <label></label>
                                            <button @click="filterClear()" type="button" style="margin-top: 30px"
                                                    class="btn btn-light">Xóa
                                            </button>
                                        </div>
                                </div>
                            <button class="btn btn-primary ml-1" @click="doFilter">Submit</button>

                            <hr>
                            <h3>Bảng dữ liệu</h3>
<!--                            <div style="margin-bottom:50px">-->
<!--                                <div style="float: left;display: inline-block">-->
<!--                                        <select class="form-select form-select-sm form-select-solid" v-model="limit" @change="changeLimit">-->
<!--                                            <option value="50">50</option>-->
<!--                                            <option value="100">100</option>-->
<!--                                            <option value="200">200</option>-->
<!--                                        </select>-->

<!--                                </div>-->
<!--                                <div style="display: inline-block;float: left;margin: 4px 4px">Record per page</div>-->
<!--                                <div style="float: right;display: inline-block">-->
<!--                                    <div style="float: left;margin: 2px 4px" >Search</div>-->
<!--                                    <input type="text"  @keydown.enter="doFilter('keyword', filter.keyword, $event)" v-model="filter.keyword">-->
<!--                                </div>-->
<!--                            </div>-->

                            <div class="table-responsive">
                                <table class="table mg-b-0 text-md-nowrap">
                                    <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Campaign</th>
                                        <th>Partner</th>
                                        <th>Total</th>
                                        <th>Price</th>
                                        <th>Click</th>
                                        <th>Send Postback</th>
                                        <th>Rate</th>
                                        <th>Chi phí share partner</th>
                                        <th>Chưa thanh toán</th>
                                        <th>Thanh toán</th>
                                        <!--                                    <th>Action</th>-->
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="entry in entries">
                                        <td v-text="entry.id"></td>
                                        <td>

                                            <template v-if="entry.campaign">
                                                <a :href="'/xadmin/campaign_installs/detail?id='+entry.id + '&created=' + this.filter.created">
                                                    {{entry.campaign_id}}-{{entry.campaign.name}}
                                                </a>
                                            </template>
                                        </td>
                                        <td>
                                            <template v-if="entry.partner"> {{entry.partner.name}}</template>
                                        </td>
                                        <td v-text="entry.campaign_install_count"></td>
                                        <td v-text="entry.price"></td>
                                        <td ></td>
                                        <td></td>
                                        <td></td>
                                        <td >{{entry.sharePrice}}</td>
                                        <td ></td>
                                        <td></td>

                                    </tr>
                                    <tr>
                                        <td ></td>
                                        <td ></td>
                                        <td >Tổng</td>
                                        <td >{{total}}</td>
                                        <td ></td>
                                        <td ></td>
                                        <td ></td>
                                        <td ></td>
                                        <td >{{totalPriceShare}}</td>
                                        <td ></td>
                                        <td></td>

                                    </tr>
                                    </tbody>
                                </table>
<!--                                <div style="float: left;display: inline-block;margin-top: 10px" v-text=" 'Showing '+from +' to '+ to +' of '+ entries.length + ' entries' " v-if="entries.length > 0"></div>-->
                                <div class="float-right" style="margin-top:10px; ">
<!--                                    <Paginate :value="paginate" :pagechange="onPageChange"></Paginate>-->
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
import {$get, $post, getTimeNow} from "../../../utils";
    import $router from '../../../lib/SimpleRouter';
    import ActionBar from '../../../components/ActionBar';


    let created = getTimeNow();
    const $q = $router.getQuery();

    export default {
        name: "Campaign_installsIndex.vue",
        components: {ActionBar},
        data() {
            return {
                total: 0,
                totalPriceShare: 0,
                campaigns: [],
                partners: [],
                entries: [],
                filter: {
                    keyword: $q.keyword || '',
                    campaign: $q.campaign || '',
                    partner_name: $q.partner_name || '',
                    created: $q.created || created,
                },
                // limit: $q.limit || 25,
                // from: 0,
                // to: 0,
                // paginate: {
                //     currentPage: 1,
                //     lastPage: 1
                // }
            }
        },
        mounted() {
            const vm = this;
            $(".js-example-responsive").select2({
                placeholder: "All"
            }).on("change", function(e) {
                vm.filter.campaign = $(this).val();
            });
            $(".js-example-responsive-a").select2({
                placeholder: "All"
            }).on("change", function(e) {
                vm.filter.partner_name = $(this).val();
            });
            $router.on('/', this.load).init();
        },
        methods: {
            async load() {
                let query = $router.getQuery();
                this.doFilter();
                const res = await $get('/xadmin/campaign_installs/data', query);
                // this.paginate = res.paginate;
                this.entries = res.data;
                this.total=this.entries.reduce((accumulator, currentValue)=>{
                    return accumulator + parseInt(currentValue['campaign_install_count']);
                },0);
                this.totalPriceShare = 0;
                for (let item of this.entries) {
                    if (item.price) {
                        let sharePrice = 0;
                        sharePrice = (item.price) * (item.campaign_install_count)
                        if (sharePrice) {
                            this.totalPriceShare += sharePrice;
                        }
                        item.sharePrice = parseFloat(sharePrice).toLocaleString('en-US', {
                            style: 'currency',
                            currency: 'VND'
                        })
                        item.price = parseFloat(item.price).toLocaleString('en-US', {
                            style: 'currency',
                            currency: 'VND'
                        });
                    }
                }
                this.totalPriceShare = parseFloat(this.totalPriceShare).toLocaleString('en-US', {
                    style: 'currency',
                    currency: 'VND'
                });
                this.campaigns = res.campaigns;
                this.partners = res.partners;
                this.from = (this.paginate.currentPage - 1) * (this.limit) + 1;
                this.to = (this.paginate.currentPage - 1) * (this.limit) + this.entries.length;

            },
            async remove(entry) {
                if (!confirm('Xóa bản ghi: ' + entry.id)) {
                    return;
                }

                const res = await $post('/xadmin/campaign_installs/remove', {id: entry.id});

                if (res.code) {
                    toastr.error(res.message);
                } else {
                    toastr.success(res.message);
                }

                $router.updateQuery({page: this.paginate.currentPage, _: Date.now()});
            },
            filterClear() {
                for (var key in this.filter) {
                    this.filter[key] = '';
                }

                $router.setQuery({});
            },
            doFilter() {
                $router.setQuery(this.filter)
            },
            changeLimit() {
                let params = $router.getQuery();
                params['page'] = 1;
                params['limit'] = this.limit;
                $router.setQuery(params)
            },
            async toggleStatus(entry) {
                const res = await $post('/xadmin/campaign_installs/toggleStatus', {
                    id: entry.id,
                    status: entry.status
                });

                if (res.code === 200) {
                    toastr.success(res.message);
                } else {
                    toastr.error(res.message);
                }
            },
            onPageChange(page) {
                $router.updateQuery({page: page})
            }
        }
    }
</script>

<style scoped>
    select:required:invalid {
        color: #adadad;
    }

    option[value=""][disabled] {
        display: none;
    }

    option {
        color: black;
    }

</style>
