<template>
    <div class="main-content app-content">
        <ActionBar type="detail" @remove="save()"
                   backUrl="/xadmin/users/index"
                   title="Chi tiết người dùng"/>
        <div class="main-container container-fluid">
            <div class="breadcrumb-header justify-content-between">
                <div class="left-content"><span class="main-content-title mg-b-0 mg-b-lg-1">User Detail</span></div>
                <div class="justify-content-center mt-2">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item tx-15"><a href="/xadmin/dashboard/index">HOME</a></li>
                        <li class="breadcrumb-item active" aria-current="page">User Detail</li>
                    </ol>
                </div>
<!--                <div class="row">-->
<!--                    <div class="col-lg-12">-->
<!--                        <div class="card card-custom card-stretch gutter-b">-->
<!--                            <div class="card-body d-flex flex-column">-->
<!--                                <input v-model="entry.id" type="hidden" name="id">-->
<!--                                <div class="row mb-7">-->
<!--                                    <label class="col-lg-2 col-4 fw-semibold text-muted">Tên</label>-->
<!--                                    <div class="col-lg-10 col-8">-->
<!--                                        <span class="fw-bold fs-6 text-gray-800" v-text="entry.name"></span>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                                <div class="row mb-7">-->
<!--                                    <label class="col-lg-2 col-4 fw-semibold text-muted">Email</label>-->
<!--                                    <div class="col-lg-10 col-8">-->
<!--                                        <span class="fw-bold fs-6 text-gray-800" v-text="entry.email"></span>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                                <div class="row mb-7">-->
<!--                                    <label class="col-lg-2 col-4 fw-semibold text-muted">Chức danh</label>-->
<!--                                    <div class="col-lg-10 col-8">-->
<!--                                        <span class="fw-bold fs-6 text-gray-800" v-text="entry.position"></span>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                                <div class="row mb-7">-->
<!--                                    <label class="col-lg-2 col-4 fw-semibold text-muted">Đơn vị</label>-->
<!--                                    <div class="col-lg-10 col-8">-->
<!--                                        <span class="fw-bold fs-6 text-gray-800" v-text="entry.unit_name"></span>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                   -->
<!--                </div>-->

            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="card card-custom card-stretch gutter-b">
                        <div class="card-body d-flex flex-column">
                            <div class="row">
                                <div class="col-lg-12">
                                    <input v-model="entry.id" type="hidden" name="id">
                                    <div class="form-group">
                                        <label>Code</label>
                                        <input id="f_code" v-model="entry.code" name="name"
                                               class="form-control"
                                               placeholder="code">
                                        <error-label for="f_code" :errors="errors.code"></error-label>
                                    </div>
                                    <div class="form-group">
                                        <label>Username</label>
                                        <input id="f_username" v-model="entry.username" name="name"
                                               class="form-control"
                                               placeholder="username">
                                        <error-label for="f_username" :errors="errors.username"></error-label>
                                    </div>
                                    <div class="form-group">
                                        <label>Name</label>
                                        <input id="f_name" v-model="entry.name" name="name"
                                               class="form-control"
                                               placeholder="name">
                                        <error-label for="f_name" :errors="errors.name"></error-label>
                                    </div>
                                    <div class="form-group">
                                        <label>Birthday</label>
                                        <input id="f_birthday" v-model="entry.birthday" name="name"
                                               class="form-control"
                                               placeholder="birthday">
                                        <error-label for="f_birthday" :errors="errors.birthday"></error-label>
                                    </div>
                                    <div class="form-group">
                                        <label>Phone</label>
                                        <input id="f_phone" v-model="entry.phone" name="name"
                                               class="form-control"
                                               placeholder="phone">
                                        <error-label for="f_phone" :errors="errors.phone"></error-label>
                                    </div>
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input id="f_email" v-model="entry.email" name="name"
                                               class="form-control"
                                               placeholder="email">
                                        <error-label for="f_email" :errors="errors.email"></error-label>
                                    </div>
                                    <div class="form-group">
                                        <label>Address</label>
                                        <input id="f_address" v-model="entry.address" name="name"
                                               class="form-control"
                                               placeholder="address">
                                        <error-label for="f_address" :errors="errors.address"></error-label>
                                    </div>
                                    <div class="form-group">
                                        <label>Status</label>
                                        <input id="f_status" v-model="entry.status" name="name"
                                               class="form-control"
                                               placeholder="status">
                                        <error-label for="f_status" :errors="errors.status"></error-label>
                                    </div>
                                    <div class="form-group">
                                        <label>Type</label>
                                        <input id="f_type" v-model="entry.type" name="name"
                                               class="form-control"
                                               placeholder="type">
                                        <error-label for="f_type" :errors="errors.type"></error-label>
                                    </div>
                                    <div class="form-group">
                                        <label>Created By</label>
                                        <input id="f_created_by" v-model="entry.created_by" name="name"
                                               class="form-control"
                                               placeholder="created_by">
                                        <error-label for="f_created_by" :errors="errors.created_by"></error-label>
                                    </div>
                                    <div class="form-group">
                                        <label>Updated By</label>
                                        <input id="f_updated_by" v-model="entry.updated_by" name="name"
                                               class="form-control"
                                               placeholder="updated_by">
                                        <error-label for="f_updated_by" :errors="errors.updated_by"></error-label>
                                    </div>

                                    <div class="form-group">
                                        <label>Role</label>
                                        <select v-model="role" class="form-control form-select">
                                            <option v-for="role in roles" :value="role.id">{{role.name}}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>

</template>

<script>
import ActionBar from "../../components/ActionBar";
// import AutoComplete from "../../components/AutoComplete";
import {$post} from "../../utils";
import swal from "sweetalert";
import $router from "../../lib/SimpleRouter";

export default {
  name: "UserDetail",
  components: {ActionBar},
  data() {
    return {
        role:$json.role,
        roles:$json.roles,
      entry: $json.entry || {},
      isLoading: false,
      errors: {}
    }
  },

  methods: {
    async remove(entry) {
      swal({
        title: "Cảnh báo",
        text: "Bạn có chắc bạn muốn xóa mục này?",
        icon: "warning",
        buttons: {
          cancel: 'Hủy bỏ',
          confirm: 'Xóa',
        },
        dangerMode: true,
      }).then((isConfirm) => {
        if(isConfirm) {
          const res = $post('/xadmin/users/remove', {id: entry.id});
          if (res.code) {
            toastr.error(res.message);
          } else {
            toastr.success('Đã xóa');
          }

          $router.updateQuery({page: this.paginate.currentPage, _: Date.now()});
        }
      });
    },
      async save() {
          this.isLoading = true;
          const res = await $post('/xadmin/users/save', {entry: this.entry}, false);
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
                  location.replace('/xadmin/users/edit?id=' + res.id);
              }
          }
      }
  }
}
</script>

<style scoped>

</style>
