<template>
    <div class="main-content app-content"> <!-- container -->
        <div class="main-container container-fluid"> <!-- breadcrumb -->
            <div class="breadcrumb-header justify-content-between">
<!--                <div class="left-content"><span class="main-content-title mg-b-0 mg-b-lg-1">Khách hàng</span></div>-->
                <div class="justify-content-center mt-2">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item tx-15"><a href="/xadmin/dashboard/index">Trang chủ</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Khách hàng</li>
                        <li class="breadcrumb-item active" aria-current="page">Danh sách khách hàng</li>
                    </ol>
                </div>
            </div> <!-- /breadcrumb --> <!-- row -->

            <div class="row row-sm">

                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header pb-0">
                            <div class="d-flex justify-content-between">
                                <h4 class="card-title mg-b-0">Danh sách khách hàng</h4></div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-xl-8">
                                    <form class="form-inline">
                                        <div class="form-group mx-sm-3 mb-2">
                                            <input @keydown.enter="doFilter('keyword', filter.keyword, $event)" v-model="filter.keyword"
                                                   type="text"
                                                   class="form-control" placeholder="tìm kiếm" >
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
                                <div class="col-xl-4 d-flex">
                                    <div class="margin-left-auto mb-1">
                                        <a href="/xadmin/customers/create" class="btn btn-primary">
                                            <i class="fa fa-plus"/>
                                            Thêm
                                        </a>
                                    </div>
                                </div>
                            </div>


                            <div class="table-responsive">
                                <table class="table mg-b-0 text-md-nowrap">
                                    <thead>
                                    <tr>
                                        <th>ID</th>
                                                                                    <th>Name</th>
                                                                                    <th>Email</th>
                                                                                    <th>Phone</th>
                                                                                    <th>Company</th>
                                                                                    <th>Description</th>
                                                                                <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="entry in entries">
                                        <td>
                                            <a class="edit-link" :href="'/xadmin/customers/edit?id='+entry.id"
                                               v-text="entry.id"></a>
                                        </td>
                                                                                    <td>{{entry.id}} - {{entry.name}}</td>
                                                                                    <td v-text="entry.email"></td>
                                                                                    <td v-text="entry.phone"></td>
                                                                                    <td v-text="entry.company"></td>
                                                                                    <td v-text="entry.description"></td>

                                        <td class="">
                                            <a :href="'/xadmin/customers/edit?id='+entry.id" class="btn "><i
                                                    class="fa fa-edit"></i></a>
                                            <a @click="remove(entry)" href="javascript:;" class="btn "><i
                                                    class="fa fa-trash"></i></a>
                                        </td>
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


    let created = getTimeRangeAll();
    const $q = $router.getQuery();

    export default {
        name: "CustomersIndex.vue",
        components: {ActionBar},
        data() {
            return {
                entries: [],
                filter: {
                    keyword: $q.keyword || '',
                    created: $q.created || created,
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
                const res = await $get('/xadmin/customers/data', query);
                this.paginate = res.paginate;
                this.entries = res.data;
            },
            async remove(entry) {
                if (!confirm('Xóa bản ghi: ' + entry.id)) {
                    return;
                }

                const res = await $post('/xadmin/customers/remove', {id: entry.id});

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
                $router.setQuery(params)
            },
            async toggleStatus(entry) {
                const res = await $post('/xadmin/customers/toggleStatus', {
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

</style>
