<template>
    <div class="main-content app-content"> <!-- container -->
        <div class="main-container container-fluid"> <!-- breadcrumb -->
            <div class="breadcrumb-header justify-content-between">
                <div class="justify-content-center mt-2">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item tx-15"><a href="/xadmin/dashboard/index">Campaign</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Thống kê</li>
                        <li class="breadcrumb-item active" aria-current="page">Thống kê chi tiết</li>
                    </ol>
                </div>
            </div> <!-- /breadcrumb --> <!-- row -->

            <div class="row row-sm">

                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header pb-0">
                            <div class="d-flex justify-content-between">
                                <h4 class="card-title mg-b-0">Thống kê chi tiết : {{entry.name}}</h4></div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-lg-3">
                                    <label>Timeline</label>
                                    <select class="form-select form-control" v-model="filter.timeline">
                                        <option value="00">00</option>
                                        <option value="01">01</option>
                                        <option value="02">02</option>
                                        <option value="03">03</option>
                                        <option value="04">04</option>
                                        <option value="05">05</option>
                                        <option value="06">06</option>
                                        <option value="07">07</option>
                                        <option value="08">08</option>
                                        <option value="09">09</option>
                                        <option value="10">10</option>
                                        <option value="11">11</option>
                                        <option value="12">12</option>
                                        <option value="13">13</option>
                                        <option value="14">14</option>
                                        <option value="15">15</option>
                                        <option value="16">16</option>
                                        <option value="17">17</option>
                                        <option value="18">18</option>
                                        <option value="19">19</option>
                                        <option value="20">20</option>
                                        <option value="21">21</option>
                                        <option value="22">22</option>
                                        <option value="23">23</option>
                                    </select>
                                </div>
                                <div class="form-group col-lg-3">
                                    <label>Thời gian tùy chọn</label>
                                    <Daterangepicker
                                        @update:modelValue="(value) => doFilter('created', value, $event)"
                                        v-model="filter.created" placeholder="Ngày tạo"></Daterangepicker>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <button class="btn btn-primary" @click="doFilter()">Tìm kiếm</button>
                            </div>
                            <div class="table-responsive">
                                <table class="table mg-b-0 text-md-nowrap">
                                    <thead>
                                    <tr>
                                        <th>Thời gian</th>
                                        <th>Số lượt cài đặt</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="entry in entries">
                                        <td>{{entry.date}}</td>
                                        <td>{{entry.count}}</td>
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
import {$get, $post, getTimeNow} from "../../utils";
import $router from '../../lib/SimpleRouter';
import ActionBar from '../../components/ActionBar';


let created = getTimeNow();
const $q = $router.getQuery();

export default {
    name: "CampaignStatisticalDetail.vue",
    components: {ActionBar},
    data() {
        return {
            entry:$json.entry || [],
            entries: [],
            filter: {
                created: $q.created || created,
                timeline:$q.timeline || '00',
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
        addZeroToIndex(A) {
        let result = [];
        for (let i=0; i<A.length; i++) {
            if (A[i].date < 10) {
                // Thêm 0 đằng trước index
                let z = "0" + String(A[i].date);
                result.push({date: z, count: A[i].count});
            } else {
                result.push({date: A[i].date, count: A[i].count});
            }
        }
        return result;
},
        async load() {
            this.doFilter();
            let query = $router.getQuery();
            const res = await $get('/xadmin/campaigns/dataDetail?id='+this.entry.id, query);
            // this.paginate = res.paginate;
            this.entries =this.addZeroToIndex(res.data);
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

</style>
