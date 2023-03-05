<template>
    <div class="main-content app-content"> <!-- container -->
        <div class="main-container container-fluid"> <!-- breadcrumb -->
            <div class="breadcrumb-header justify-content-between">
                <!--                <div class="left-content"><span class="main-content-title mg-b-0 mg-b-lg-1">CampaignInstall</span></div>-->
                <div class="justify-content-center mt-2">
                    <ol class="breadcrumb">
<!--                        <li class="breadcrumb-item tx-15"><a href="/xadmin/dashboard/index">HOME</a></li>-->
<!--                        <li class="breadcrumb-item active" aria-current="page">Campaign Partner</li>-->
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
                                    <label>Tìm kiếm</label>
                                    <input type="text" @keydown.enter="doFilter('keyword', filter.keyword, $event)" v-model="filter.keyword" placeholder="Tìm kiếm..." class="form-control"/>

                                </div>
                                <div class="form-group col-lg-3">
                                    <label>OS</label>
                                    <select class="form-select form-control" v-model="filter.os" required>
                                        <option value="" disabled selected>Chọn os</option>
                                        <option value="0">ALL</option>
                                        <option value="android">Android</option>
                                        <option value="ios">iOS</option>
                                    </select>
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
                            <div class="table-responsive">
                                <table class="table mg-b-0 text-md-nowrap">
                                    <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Icon</th>
                                        <th>Tên ứng dụng</th>
                                        <th>Hệ điều hành</th>
                                        <th>Type</th>
                                        <th>Trạng thái</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="entry in entries">
                                        <td v-text="entry.id"></td>
                                        <td ><img v-if="entry.icon && entry.icon.length > 0" :src="entry.icon[0].url" style="width: 32px;height: 32px"></td>
                                        <td v-text="entry.name"></td>
                                        <td v-text="entry.os"></td>
                                        <td v-text="entry.type"></td>
                                        <td><switch-button></switch-button></td>
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
            entries: [],
            dataList: [],
            testButton: false,
            filter: {
                keyword: $q.keyword || '',
                os:$q.os || '',
            },
            limit: $q.limit || 25,

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
        // doFilter(field, value, event) {
        //     if (event) {
        //         event.preventDefault();
        //     }
        //
        //     const params = {page: 1};
        //     params[field] = value;
        //     $router.updateQuery(params)
        // },
        doFilter() {
            $router.setQuery(this.filter)
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
    select:required:invalid {
        color: #adadad;
    }

    option[value=""][disabled] {
        display: none;
    }

    option {
        color: black;
    }
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
