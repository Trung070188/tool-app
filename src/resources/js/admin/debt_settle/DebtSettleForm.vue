<template>
    <div class="main-content app-content"> <!-- container -->
        <ActionBar label="Lưu lại" @action="save()" backUrl="/xadmin/debt_settle/index"/>
        <div class="main-container container-fluid"> <!-- breadcrumb -->
            <div class="breadcrumb-header justify-content-between">
                <div class="left-content"><span class="main-content-title mg-b-0 mg-b-lg-1">DebtSettle</span></div>
                <div class="justify-content-center mt-2">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item tx-15"><a href="/xadmin/dashboard/index">HOME</a></li>
                        <li class="breadcrumb-item active" aria-current="page">DebtSettle</li>
                    </ol>
                </div>
            </div> <!-- /breadcrumb --> <!-- row -->

            <div class="row row-sm">

                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header pb-0">
                            <div class="d-flex justify-content-between">
                                <h4 class="card-title mg-b-0">DebtSettle Form</h4></div>
                        </div>
                        <div class="card-body">
                            <input v-model="entry.id" type="hidden" name="id">
                            <div class="form-group">
                                <label>Customer</label>
                                <select class="js-example-responsive" style="width: 100%" v-model="entry.customer_id">
                                    <option v-for="customer in entries" :value="customer.id">{{customer.id}}-{{customer.name}}</option>
                                </select>
                                <error-label for="f_customer_id" :errors="errors.customer_id"></error-label>
                            </div>
                            <div class="form-group">
                                <label>Pay Booking</label>
                                <input id="inputNumber" v-model="formatBooking" name="name"
                                       class="form-control"
                                       placeholder="pay_booking" @input="formatValueBooking(formatBooking)">
                                <error-label for="f_pay_booking" :errors="errors.pay_booking"></error-label>
                            </div>
                            <div class="form-group">
                                <label>Pay Debt</label>
                                <input id="f_pay_debt" v-model="formatDebt" name="name"
                                       class="form-control"
                                       placeholder="pay_debt" @input="formatValueDebt(formatDebt)">
                                <error-label for="f_pay_debt" :errors="errors.pay_debt"></error-label>
                            </div>
                            <div class="form-group col-lg-12">
                                <label>Note</label>
                                <br>
                                <textarea class="form-control" v-model="entry.note"></textarea>
                                <!--                                    <RichtextEditor  v-model="entry.note"></RichtextEditor>-->
                                <error-label for="f_status" :errors="errors.note"></error-label>
                            </div>
                        </div>
                    </div>
                </div> <!--/div--> <!--div-->

            </div> <!-- /row -->
        </div>


    </div> <!-- /main-content -->

</template>

<script>
import {$get, $post} from "../../utils";
import ActionBar from '../../components/ActionBar';
import $router from "../../lib/SimpleRouter";
import RichtextEditor from "../../components/RichtextEditor";


export default {
    name: "DebtSettleForm.vue",
    components: {ActionBar,RichtextEditor},
    data() {
        let entry
        let formatBooking
        let formatDebt
        if($json.entry)
        {
           entry =$json.entry
            formatBooking=entry.pay_booking || 0
            formatDebt=entry.pay_debt || 0
        }
        else {
            entry={
                pay_booking:0,
                pay_debt:0
            }
            formatBooking=0
            formatDebt=0


        }
        return {
            formatBooking:formatBooking,
            formatDebt:formatDebt,
            entries:[],
            entry: entry,
            isLoading: false,
            errors: {}
        }
    },
    mounted() {
        const vm = this;
        $(".js-example-responsive").select2({
        }).on("change", function(e) {
            vm.entry.customer_id = $(this).val();
        });
        $router.on('/', this.load).init();
    },
    methods: {
        formatValueBooking() {
            {


                this.entry.pay_booking = this.formatBooking.replace(/[^0-9.-]+/g, '') || 0;
                if (this.entry.pay_booking === '') {
                    this.formatBooking = '0';
                }
                else {
                    this.formatBooking = parseFloat(this.entry.pay_booking).toLocaleString('en-US', {
                        style: 'currency',
                        currency: 'VND'
                    });
                }
            }

        },
        formatValueDebt() {
            {
                this.entry.pay_debt = this.formatDebt.replace(/[^0-9.-]+/g, '') || 0;
                if (this.entry.pay_debt === '') {
                    this.formatDebt = '0';
                }
                else {
                    this.formatDebt = parseFloat(this.entry.pay_debt).toLocaleString('en-US', {
                        style: 'currency',
                        currency: 'VND'
                    });
                }
            }

        },

        async load() {
            let query = $router.getQuery();
            const res = await $get('/xadmin/debt_settle/dataCreate', query);
            this.entries = res.customers;
        },
        async save() {
            this.isLoading = true;
            const res = await $post('/xadmin/debt_settle/save', {entry: this.entry});
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
                    location.replace('/xadmin/debt_settle/edit?id=' + res.id);
                }
            }
        }
    }
}
</script>

<style scoped>

</style>
