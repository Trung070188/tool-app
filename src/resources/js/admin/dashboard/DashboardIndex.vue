<template>
<!--    <div class="main-content app-content"> &lt;!&ndash; container &ndash;&gt;-->
<!--        <div class="main-container container-fluid"> &lt;!&ndash; breadcrumb &ndash;&gt;-->
<!--            <div class="breadcrumb-header justify-content-between">-->
<!--                <div class="left-content"><span class="main-content-title mg-b-0 mg-b-lg-1">DASHBOARD</span></div>-->
<!--                <div class="justify-content-center mt-2">-->
<!--                    <ol class="breadcrumb">-->
<!--                        <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">TRANG CHỦ</a></li>-->
<!--                        <li class="breadcrumb-item tx-15 active" aria-current="page">DASHBOARD</li>-->
<!--                    </ol>-->
<!--                </div>-->
<!--            </div>-->
<!--            <div class="row">-->
<!--                <div class="col-12 col-sm-12">-->
<!--                    <div class="card" style="width: 90%">-->

<!--                        <div class="card-body row">-->

<!--                            <div class="col-xl-12 col-md-12 col-xs-12">-->
<!--                                <switch-button v-model="testButton"/>-->
<!--                                <RichtextEditor : v-model="testContent"/>-->
<!--                                <button type="button" @click="clickMe()" class="btn btn-primary">ClickMe</button>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->

<!--        </div>-->
<!--    </div>-->
</template>

<script>

import $router from "../../lib/SimpleRouter";
import moment from "moment/moment";
import SwitchButton from "../../components/SwitchButton";
import RichtextEditor from "../../components/RichtextEditor";
const $q = $router.getQuery();

export default {
  name: "DashboardIndex",
  components: {RichtextEditor, SwitchButton},
    data() {
        return {
            entries: [],
            dataList: [],
            testButton: false,
            testContent: '<h1>hello World</h1>',
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
        //$router.on('/', this.load).init();
    },
    methods: {
        clickMe() {
            this.testButton = !this.testButton;
            console.log(this.testButton)
        },
        async load() {
            let query = $router.getQuery();

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
