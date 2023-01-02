<template>
    <div class="main-content app-content"> <!-- container -->
        <div class="main-container container-fluid"> <!-- breadcrumb -->
            <div class="breadcrumb-header justify-content-between">
                <div class="left-content"><span class="main-content-title mg-b-0 mg-b-lg-1">DASHBOARD</span></div>
                <div class="justify-content-center mt-2">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">TRANG CHỦ</a></li>
                        <li class="breadcrumb-item tx-15 active" aria-current="page">DASHBOARD</li>
                    </ol>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-sm-12">
                    <div class="card" style="width: 90%">

                        <div class="card-body row">
                            <div class="col-xl-7 col-md-7 col-xs-12 float-left">
                                <div class="font-weight-bold my-5 font-large">
                                    PostPay là hệ sinh thái thanh toán được phát triển bởi Tổng công ty Bưu Điện Việt
                                    Nam,
                                    được Ngân hàng Nhà nước cấp phép số 13/GP-NHNN ngày 12/04/2021
                                </div>
                                <div class="d-flex mt-5">
                                    <div>
                                        <img src="/assets/img/qr-taiapp.svg">
                                    </div>
                                    <div>
                                        <div class="mt-2 font-large mb-5">Cùng trải nghiệm PostPay<br>
                                            Ứng dụng thuần Việt dành cho người Việt
                                        </div>
                                        <ul>
                                            <li style="padding-right: 2px">
                                                <a href="https://postpay.vn/tai-app/ios" target="_blank">
                                                    <img class="btn-download-app" src="/assets/img/btn-app-store.svg">
                                                </a>

                                            </li>
                                            <li>
                                                <a href="https://play.google.com/store/apps/details?id=com.vnpost.postpay">
                                                    <img class="btn-download-app" src="/assets/img/btn-google-play.svg">
                                                </a>

                                            </li>
                                        </ul>
                                    </div>

                                </div>
                            </div>
                            <div class="col-xl-5 col-md-5 col-xs-12 float-right">
                                <img src=/assets/img/image-in-banner.png>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-3">
                <div class="m-auto box-search">
                    <div class="card-header pb-0">
                        <div class="d-flex justify-content-between">
                            <h4 class="card-title mg-b-0">
                                XUẤT BÁO CÁO THEO KỲ</h4></div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-xl-12">
                            <form class="form-inline">
                                <div class="row mx-sm-3 mb-2">
                                    <label class="mt-1">Tiêu chí lọc</label>
                                    <div>
                                        <select v-model="filter.type"
                                                @change="doFilter('type', filter.type)"
                                                class="form-select form-control"
                                                style="width: 142px">
                                            <option :value="1">Quý</option>
                                            <option :value="2">Năm</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mx-sm-3 mb-2">
                                    <label>Tính đến năm</label>
                                    <SelectYear @update:modelValue="value => doFilter('filterYear', value)"
                                                v-model="filter.year"></SelectYear>
                                </div>
                                <div class="row mx-sm-3 mb-2"
                                     v-bind:class="{'d-none': filter.type == 2 }">
                                    <label>Tính đến quý</label>
                                    <SelectQuarter :year="filter.year"
                                                   @selectOneQuarter="(quarter)=> clickChoseQuarter(quarter)"
                                                   v-model="filter.quarter"></SelectQuarter>
                                </div>
                                <div class="btn-export">
                                    <button type="button" @click="onExport()" class="btn btn-primary mb-2">Xuất excel</button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>

<script>

import QFileManagerInput from "../../components/QFileManagerInput";
import QImage from "../../components/QImage";
import SelectQuarter from "../../components/SelectQuarter";
import SelectYear from "../../components/SelectYear";
import config_select from "../connection_units/config_select";
import $router from "../../lib/SimpleRouter";
import {$post, getQuarterMonths, getTimeRangeAll, getYearMonths} from "../../utils";
import moment from "moment/moment";
let created = getTimeRangeAll();
const $q = $router.getQuery();
const TRANS_STATUS = {
    FAILED: 0,
    SUCCESS: 1,
    IN_PROGRESS: 2,
    PENDING: 3
}
export default {
  name: "DashboardIndex",
  components: {SelectYear, SelectQuarter, QImage, QFileManagerInput},
    data() {
        return {
            entries: [],
            dataList: [],
            filter: {
                type: $q.type || 1,
                quarter: $q.quarter || parseInt(moment().subtract(1, 'Q').format('Q')),
                year: $q.year || moment().year(),
                pageSize: $q.pageSize || 10,
                currentPage: $q.currentPage || 1
            },
            paginate: {
                currentPage: 1,
                lastPage: 1
            }
        }
    },
    mounted() {
        $router.on('/', this.load).init();
    },
    methods: {
        async load() {
            let query = $router.getQuery();
            // const res = await $get('/xadmin/wallets/data', query);
            // this.paginate = res.paginate;
            // this.entries = res.data;
        },
        async remove(entry) {
            if (!confirm('Xóa bản ghi: ' + entry.id)) {
                return;
            }
            const res = await $post('/xadmin/connection_units/remove', {id: entry.id});
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
        doFilter(field, value, event) {
            if (event) {
                event.preventDefault();
            }

            const params = {page: 1};
            params[field] = value;
            $router.updateQuery(params)
        },
        async toggleStatus(entry) {
            const res = await $post('/xadmin/connection_units/toggleStatus', {
                id: entry.id,
                status: entry.status
            });

            if (res.code === 200) {
                toastr.success(res.message);
            } else {
                toastr.error(res.message);
            }
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
        async onExport() {
            let query = $router.getQuery();
            const year = query.year || moment().year();
            let months = getYearMonths(year);
            const type = query.type || '1';
            const quarter = query.quarter || parseInt(moment().subtract(1, 'Q').format('Q'));
            const {SUCCESS, FAILED, PENDING} = TRANS_STATUS;
            if (type === '1') {
                months = getQuarterMonths(quarter);
            }


            location.href  = '/xadmin/dashboard/export?' + (new URLSearchParams({
                type: type,
                year: year,
                quarter: quarter,
                months: months
            }));

            // const res = await $post('/xadmin/dashboard/export?' , {
            //     type: type,
            //     year: year,
            //     quarter: quarter,
            //     months: months
            // });
        }
    }
}
</script>

<style scoped>
.font-large{
    font-size: 18px;
}
.btn-download-app {
    width: 170px;
}
.box-search {
    width: 90%;
    min-height: 150px;
    border: 1px solid #000;
    border-radius: 5px
}
.btn-export{
    margin-left: auto;
    order: 2;
    padding-right: 10px;
}
ul{
    margin-top: 58px !important;
    margin-left: 0 !important;
    padding-left: 0 !important;
    margin-bottom: 10px;
    display: block;
    list-style-type: disc;
    margin-block-start: 1em;
    margin-block-end: 1em;
    margin-inline-start: 0px;
    margin-inline-end: 0px;
    padding-inline-start: 40px;
}
ul li {
    list-style: none;
    display: inline-block;
}

</style>
