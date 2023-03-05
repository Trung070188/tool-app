<template>
    <div class="main-content app-content"> <!-- container -->
        <div class="main-container container-fluid"> <!-- breadcrumb -->
            <div class="breadcrumb-header justify-content-between">
<!--                <div class="left-content"><span class="main-content-title mg-b-0 mg-b-lg-1">Campaign</span></div>-->
                <div class="justify-content-center mt-2">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item tx-15"><a href="/xadmin/dashboard/index">HOME</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Campaign</li>
                        <li class="breadcrumb-item active" aria-current="page">Danh sách</li>
                    </ol>
                </div>
            </div> <!-- /breadcrumb --> <!-- row -->

            <div class="row row-sm">

                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header pb-0">
                            <div class="d-flex justify-content-between">
                                <h4 class="card-title mg-b-0">Thống kê campaign</h4></div>
                        </div>
                        <div class="card-body">
                            <div class="card-header border-0 pt-6">
                                    <div class="d-flex align-items-center position-relative my-1">
                                        <input type="text" @keydown.enter="doFilter('keyword', filter.keyword, $event)" v-model="filter.keyword" placeholder="Tìm kiếm..." class="form-control col-lg-4"/>
                                    </div>
                                <div class="card-toolbar">
                                    <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base" style="position: absolute;top: 25px;right: 0px" v-if="campaignIds=='' ">

                                        <button type="button" style="margin-left: 10px" @click="advanceSearch" class="btn btn-light" v-if="isShowFilter">
                                            <i style="margin-left: 5px" class="fas fa-times"></i>
                                            Close Advanced Search
                                        </button>
                                        <button type="button" style="margin-left: 10px" @click="advanceSearch" class="btn btn-light" v-if="!isShowFilter">
                                            <i class="bi bi-funnel"></i>
                                            Advanced Search
                                        </button>
                                        <a href="/xadmin/campaigns/create" class="btn btn-primary" style="margin-left: 10px"><i class="fa fa-plus"/> Thêm</a>
                                    </div>
                                    <div class="d-flex justify-content-end align-items-center d-none"
                                         data-kt-customer-table-toolbar="selected" v-if="campaignIds!='' " style="position: absolute;top: 25px;right: 0px">
                                        <button   type="button" class="btn btn-danger"
                                                  data-kt-customer-table-select="delete_selected" @click="removeAllCampaign">Xóa campaign đã chọn
                                        </button>
                                    </div>
                                </div>
                                <form class="col-lg-12" v-if="!isShowFilter">
                                    <div class="row" style="position:relative;margin-top: 20px">
                                            <button type="button" class="btn btn-primary" @click="doFilter()">Search</button>
                                    </div>
                                </form>

                                <form class="col-lg-12" style="margin-top: 20px" v-if="isShowFilter">
                                    <div class="row">
                                        <div class="form-group col-lg-2">
                                            <label>OS </label>
                                            <select required class="form-control form-select"  v-model="filter.os">
                                                <option value="" disabled selected>Choose Os</option>
                                                <option value="0">All</option>
                                                <option value="ios">ios</option>
                                                <option value="android ">android</option>
                                            </select>

                                        </div>
                                        <div class="form-group col-lg-2">
                                            <label>Type campaign </label>
                                            <select required class="form-control form-select" v-model="filter.type">
                                                <option value="" disabled selected>Choose type campaign</option>
                                                <option value="0">All</option>
                                                <option value="cpi">cpi</option>
                                                <option value="rate">rate</option>
                                                <option value="map">maps</option>
                                                <option value="top_keyword"></option>
                                            </select>
                                        </div>
                                        <div class="form-group col-lg-2">
                                            <label>Customer </label>
                                            <select required class="form-control form-select" v-model="filter.customer_id">
                                                <option value="" disabled selected>Choose customer</option>
                                                <option v-for="customer in customers" :value="customer.id">{{customer.name}}</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-lg-4">
                                            <label>Creation date </label>
                                            <Daterangepicker v-model="filter.created" class="active"
                                                             placeholder="Creation date" readonly></Daterangepicker>
                                            <span v-if="filter.created!==''" class="svg-icon svg-icon-2 svg-icon-lg-1 me-0" @click="filterClear">
                                            <svg type="button" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" style="float: right;margin: -32px 3px 0px;">
                                            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" style="fill:red" />
                                                        <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" style="fill:red" />
                                            </svg>
                                            </span>
                                        </div>
                                        <div style="margin-top: 28px">
                                            <button type="button" class="btn btn-primary" @click="doFilter()">Search</button>
                                        </div>
                                    </div>
                                </form>

                            </div>

                            <!--                            <div class="row">-->
<!--                                <div class="col-xl-8">-->
<!--                                    <form class="form-inline">-->
<!--                                        <div class="form-group mx-sm-3 mb-2">-->
<!--                                            <input @keydown.enter="doFilter('keyword', filter.keyword, $event)" v-model="filter.keyword"-->
<!--                                                   type="text"-->
<!--                                                   class="form-control" placeholder="tìm kiếm" >-->
<!--                                        </div>-->
<!--                                        <div class="form-group mx-sm-3 mb-2">-->
<!--                                            <Daterangepicker-->
<!--                                                @update:modelValue="(value) => doFilter('created', value, $event)"-->
<!--                                                v-model="filter.created" placeholder="Ngày tạo"></Daterangepicker>-->
<!--                                        </div>-->

<!--&lt;!&ndash;                                        <div class="form-group mx-sm-3 mb-2">&ndash;&gt;-->
<!--&lt;!&ndash;                                            <button @click="filterClear()" type="button"&ndash;&gt;-->
<!--&lt;!&ndash;                                                    class="btn btn-light">Xóa&ndash;&gt;-->
<!--&lt;!&ndash;                                            </button>&ndash;&gt;-->
<!--&lt;!&ndash;                                        </div>&ndash;&gt;-->
<!--                                        <div class="form-group mx-sm-3 mb-2">-->
<!--                                            <input class="form-control">-->
<!--                                        </div>-->

<!--                                    </form>-->
<!--                                </div>-->
<!--                                <div class="col-xl-4 d-flex">-->
<!--                                    <div class="margin-left-auto mb-1">-->
<!--                                        <a href="/xadmin/campaigns/create" class="btn btn-primary">-->
<!--                                            <i class="fa fa-plus"/>-->
<!--                                            Thêm-->
<!--                                        </a>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                            </div>-->


                            <div class="table-responsive">
                                <table class="table mg-b-0 text-md-nowrap">
                                    <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Icon</th>
                                        <th>Campaign</th>
                                        <th>Os</th>
                                        <th>Customer</th>
                                        <th>Mã KH</th>
                                        <th>HT</th>
                                        <th>Giá bán</th>
                                        <th>Tổng lượt cài đặt</th>
                                        <th>Thành tiền</th>
                                        <th>Số lượng cài partner</th>
                                        <th>Giá share partner</th>
                                        <th>Chi phí share partner</th>
                                        <th>Số lượng fake</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="entry in entries">
                                        <td v-text="entry.id"></td>
                                        <td ><img v-if="entry.icon && entry.icon.length > 0" :src="entry.icon[0].url" style="width: 32px;height: 32px"></td>
                                        <td v-text="entry.name"></td>
                                        <td v-text="entry.os"></td>
                                        <td>
                                            <template v-if="entry.customer">
                                                {{entry.customer.name}}
                                            </template>
                                        </td>
                                        <td>
                                            <template v-if="entry.customer">
                                                {{entry.customer.id}}
                                            </template>
                                        </td>
                                        <td v-text="entry.type"></td>
                                        <td v-text="entry.price"></td>
                                        <td v-text="entry.total_install"></td>
                                        <td>{{(entry.price) * (entry.total_install)}}</td>
                                        <td>{{(entry.total_install)-(entry.total_fake)}}</td>
                                        <td></td>
                                        <td></td>
                                        <td v-text="entry.total_fake"></td>
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

</template>

<script>
    import {$get, $post, getTimeRangeAll} from "../../utils";
    import $router from '../../lib/SimpleRouter';
    import ActionBar from '../../components/ActionBar';
    import SwitchButton from "../../components/SwitchButton";


    let created = getTimeRangeAll();
    const $q = $router.getQuery();

    export default {
        name: "CampaignStatistical.vue",
        components: {SwitchButton, ActionBar},
        data() {
            let filter={
                keyword: $q.keyword || '',
                created: $q.created || created,
                os:$q.os || '',
                type:$q.type || '',
                customer_id:$q.customer_id || ''
            }
            let isShowFilter = false;
            for (var key in filter) {
                if (filter[key] != '') {
                    isShowFilter = true;
                }
            }
            return {
                campaign:'',
                allSelected:false,
                campaignIds:[],
                customers:[],
                isShowFilter:isShowFilter,
                entries: [],
                filter: filter,
                limit: $q.limit || 25,

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


            advanceSearch()
            {
                this.isShowFilter=!this.isShowFilter;
                for (var key in this.filter) {
                    this.filter[key] = '';
                }
                $router.setQuery({});
            },
            async OpenNextDay(entry)
            {
                const res= await $post('/xadmin/campaigns/openNextDay',{entry:entry})
                if (res.code) {
                    toastr.success(res.message);
                } else {
                    toastr.error(res.message);
                }

            },
            async load() {
                let query = $router.getQuery();
                const res = await $get('/customer/campaigns/dataStatistical', query);
                this.paginate = res.paginate;
                this.entries = res.data;
                this.customers=res.customers;
                this.from = (this.paginate.currentPage - 1) * (this.limit) + 1;
                this.to = (this.paginate.currentPage - 1) * (this.limit) + this.entries.length;

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
