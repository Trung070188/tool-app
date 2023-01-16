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
                                <div class="col-xl-8">
                                    <form class="form-inline">
                                        <!--                                        <div class="form-group mx-sm-3 mb-2">-->
                                        <!--                                            <input @keydown.enter="doFilter('keyword', filter.keyword, $event)" v-model="filter.keyword"-->
                                        <!--                                                   type="text"-->
                                        <!--                                                   class="form-control" placeholder="tìm kiếm" >-->
                                        <!--                                        </div>-->
                                        <div class="form-group mx-sm-3 mb-2">
                                            <input @keydown.enter="doFilter('campaign', filter.campaign, $event)" v-model="filter.campaign"
                                                   type="text"
                                                   class="form-control" placeholder="Campaign" >
                                        </div>
                                        <div class="form-group mx-sm-3 mb-2">
                                            <input @keydown.enter="doFilter('partner_name', filter.partner_name, $event)" v-model="filter.partner_name"
                                                   type="text"
                                                   class="form-control" placeholder="Partner" >
                                        </div>
                                        <div class="form-group mx-sm-3 mb-2">
                                            <Daterangepicker
                                                @update:modelValue="(value) => doFilter('created', value, $event)"
                                                v-model="filter.created" placeholder="Ngày tạo"></Daterangepicker>
                                        </div>

                                        <div class="form-group mx-sm-3 mb-2">
                                            <button @click="filterClear()" type="button"
                                                    class="btn btn-light">Xóa
                                            </button>
                                        </div>

                                    </form>
                                </div>
                                <!--                                <div class="col-xl-4 d-flex">-->
                                <!--                                    <div class="margin-left-auto mb-1">-->
                                <!--                                        <a href="/xadmin/campaign_installs/create" class="btn btn-primary">-->
                                <!--                                            <i class="fa fa-plus"/>-->
                                <!--                                            Thêm-->
                                <!--                                        </a>-->
                                <!--                                    </div>-->
                                <!--                                </div>-->
                            </div>


                            <div class="table-responsive">
                                <table class="table mg-b-0 text-md-nowrap">
                                    <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Campaign</th>
                                        <th>Partner</th>
                                        <th>Device Id</th>
                                        <th>Ip</th>
                                        <th>Os</th>
                                        <!--                                    <th>Action</th>-->
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="entry in entries">
                                        <td v-text="entry.id"></td>
                                        <td v-text="entry.campaign"></td>
                                        <td v-text="entry.partner_name"></td>
                                        <td v-text="entry.device_id"></td>
                                        <td v-text="entry.ip"></td>
                                        <td v-text="entry.os"></td>
                                    </tr>
                                    </tbody>
                                </table>
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
import {$get} from "../../utils";
const $q = $router.getQuery();

export default {
  name: "CustomerDashboardIndex",
  components: {RichtextEditor, SwitchButton},
    data() {
        return {
            entries: [],
            dataList: [],
            testButton: false,
            filter: {
                // type: $q.type || 1,
                // quarter: $q.quarter || parseInt(moment().subtract(1, 'Q').format('Q')),
                // year: $q.year || moment().year(),
                // pageSize: $q.pageSize || 10,
                // currentPage: $q.currentPage || 1
            },
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
        async load() {
            let query = $router.getQuery();
            const res = await $get('/customer/dashboard/data', query);
            this.paginate = res.paginate;
            this.entries = res.data;
            console.log(this.entries);
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
