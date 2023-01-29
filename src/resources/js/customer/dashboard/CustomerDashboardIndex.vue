<template>
    <div class="main-content app-content"> <!-- container -->
        <div class="main-container container-fluid"> <!-- breadcrumb -->
            <div class="breadcrumb-header justify-content-between">
                <!--                <div class="left-content"><span class="main-content-title mg-b-0 mg-b-lg-1">CampaignInstall</span></div>-->
                <div class="justify-content-center mt-2">
                    <ol class="breadcrumb">
<!--                        <li class="breadcrumb-item tx-15"><a href="/xadmin/dashboard/index">HOME</a></li>-->
<!--                        <li class="breadcrumb-item active" aria-current="page">Campaign Partner</li>-->
                        <li class="breadcrumb-item active" aria-current="page">Thông kê</li>
                    </ol>
                </div>
            </div> <!-- /breadcrumb --> <!-- row -->

            <div class="row row-sm">

                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header pb-0">
                            <div class="d-flex justify-content-between">
                                <h4 class="card-title mg-b-0">Thông kê</h4></div>
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
                                    <select class="form-control form-select" v-model="filter.campaign">
                                        {{filter.campaign}}
                                        <option v-for="campaign in campaigns" :value="campaign.name">{{campaign.name}}</option>
                                    </select>
                                </div>
                                <div class="form-group col-lg-3">
                                    <label>Partner</label>
                                    <select class="form-select form-control" v-model="filter.partner_name">
                                        <option v-for="partner in partners" :value="partner.name">{{partner.name}}</option>
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
                            <div style="margin-bottom:50px">
                                <div style="float: left;display: inline-block">
                                    <select class="form-select form-select-sm form-select-solid" v-model="limit" @change="changeLimit">
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                        <option value="200">200</option>
                                    </select>

                                </div>
                                <div style="display: inline-block;float: left;margin: 4px 4px">Record per page</div>
                                <div style="float: right;display: inline-block">
                                    <div style="float: left;margin: 2px 4px">Search</div>
                                    <input type="text">
                                    <button>Print</button>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table mg-b-0 text-md-nowrap">
                                    <thead>
                                    <tr>
                                        <th>Campaign</th>
                                        <th>Partner</th>
                                        <th>Total</th>
                                        <th>Click</th>
                                        <th>Send Postback</th>
                                        <th>Rate</th>
                                        <th></th>
                                        <th>Chưa thanh toán</th>
                                        <th>Thanh toán</th>
                                        <!--                                    <th>Action</th>-->
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="entry in entries">
                                        <td v-text="entry.campaign"></td>
                                        <td v-text="entry.partner_name"></td>
                                        <td></td>
                                        <td></td>
                                        <td ></td>
                                        <td></td>
                                        <td></td>
                                        <td ></td>
                                        <td ></td>

                                    </tr>
                                    </tbody>
                                </table>
                                <div style="float: left;display: inline-block;margin-top: 10px" v-text=" 'Showing '+from +' to '+ to +' of '+ entries.length + ' entries' " v-if="entries.length > 0"></div>
                                <div class="float-right" style="margin-top:10px; ">
                                    <Paginate :value="paginate" :pagechange="onPageChange"></Paginate>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> <!--/div--> <!--div-->

            </div> <!-- /row -->
        </div>


    </div> <!-- /main-content -->
 <!-- /main-content -->
</template>

<script>

import $router from "../../lib/SimpleRouter";
import moment from "moment/moment";
import SwitchButton from "../../components/SwitchButton";
import RichtextEditor from "../../components/RichtextEditor";
import {$get, getTimeRangeAll} from "../../utils";
const $q = $router.getQuery();
let created = getTimeRangeAll();

export default {
  name: "CustomerDashboardIndex",
  components: {RichtextEditor, SwitchButton},
    data() {
        return {
            campaigns:[],
            partners:[],
            entries: [],
            dataList: [],
            testButton: false,
            filter: {
                campaign:$q.campaign || '',
                partner_name:$q.partner_name || '',
                created: $q.created || created,
                // type: $q.type || 1,
                // quarter: $q.quarter || parseInt(moment().subtract(1, 'Q').format('Q')),
                // year: $q.year || moment().year(),
                // pageSize: $q.pageSize || 10,
                // currentPage: $q.currentPage || 1
            },
            limit: $q.limit || 50,

            paginate: {
                currentPage: 1,
                lastPage: 1
            }
        }
    },
    mounted() {
        $router.on('/', this.load).init();
        console.log('Loaded')
    },
    methods: {
        onPageChange(page) {
            $router.updateQuery({page: page})
        },
        changeLimit() {
            let params = $router.getQuery();
            params['page'] = 1;
            params['limit'] = this.limit;
            $router.setQuery(params)
        },
        async load() {
            let query = $router.getQuery();
            const res = await $get('/customer/dashboard/data', query);
            this.paginate = res.paginate;
            this.entries = res.data;
            this.campaigns=res.campaigns;
            this.partners=res.partners;
            this.from = (this.paginate.currentPage - 1) * (this.limit) + 1;
            this.to = (this.paginate.currentPage - 1) * (this.limit) + this.entries.length;
        },
        clickMe() {
            this.testButton = !this.testButton;
            console.log(this.testButton)
        },
        // async load() {
        //     let query = $router.getQuery();
        //
        // },
        filterClear() {
            for (var key in this.filter) {
                this.filter[key] = '';
            }
            $router.setQuery({});
        },
        doFilter(field, value, event) {
            if (event) {
                event.preventDefault();
            }

            const params = {page: 1};
            params[field] = value;
            $router.updateQuery(params)
        },
        clickChose(year) {
            this.doFilter('year', year)
        },
        clickChoseQuarter(quarter) {
            this.doFilter('quarter', quarter)
        },
        async changePageSize(size) {
            $router.updateQuery({pageSize: this.filter.pageSize})
        },
    }
}
</script>

<style scoped>
/*.font-large{*/
/*    font-size: 18px;*/
/*}*/
/*.btn-download-app {*/
/*    width: 170px;*/
/*}*/
/*.box-search {*/
/*    width: 90%;*/
/*    min-height: 150px;*/
/*    border: 1px solid #000;*/
/*    border-radius: 5px*/
/*}*/
/*.btn-export{*/
/*    margin-left: auto;*/
/*    order: 2;*/
/*    padding-right: 10px;*/
/*}*/
/*ul{*/
/*    margin-top: 58px !important;*/
/*    margin-left: 0 !important;*/
/*    padding-left: 0 !important;*/
/*    margin-bottom: 10px;*/
/*    display: block;*/
/*    list-style-type: disc;*/
/*    margin-block-start: 1em;*/
/*    margin-block-end: 1em;*/
/*    margin-inline-start: 0px;*/
/*    margin-inline-end: 0px;*/
/*    padding-inline-start: 40px;*/
/*}*/
/*ul li {*/
/*    list-style: none;*/
/*    display: inline-block;*/
/*}*/

</style>
