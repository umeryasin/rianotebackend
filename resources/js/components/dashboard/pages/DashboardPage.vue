<template>
  <div class="vue-dashboard-page">
    <div class="row content-area p-4">
      <div class="col-md-12">
        <div class="db-section-1 pt-3 pb-3">
          <div class="row">
            <div class="col-md-6 text-left">
              <p>
                <b>Welcome to your {{ this.$appConfig.app_name }} User
                  Dashboard</b>
              </p>
            </div>
            <div class="col-md-6 text-right">
              <button class="btn btn-warning" v-on:click="submitNewNote">
                Start using your {{ this.$appConfig.app_name }} now
                <i class="fas fa-arrow-alt-circle-right"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-12" v-if="userInfo != null">
        <div class="row text-white db-section-2 pb-2">
          <div class="col-md-12 p-4">
            <h6>Basic Information</h6>
          </div>
          <div class="col-md-12 pt-2 pb-2 pl-4 pr-4">
            <div class="row">
              <div class="col-md-6 text-left">
                <i class="fas fa-envelope"></i>
                <span>Email: </span>
              </div>
              <div class="col-md-6 text-right">
                <span>{{ userInfo.email }}</span>
              </div>
            </div>
          </div>
          <div class="col-md-12 pt-2 pb-2 pl-4 pr-4">
            <div class="row">
              <div class="col-md-6 text-left">
                <i class="fas fa-pen-nib"></i>
                <span>Signature: </span>
              </div>
              <div class="col-md-6 text-right">
                <span>{{ userInfo.signature }}</span>
              </div>
            </div>
          </div>
          <div class="col-md-12 pt-2 pb-2 pl-4 pr-4">
            <div class="row">
              <div class="col-md-6 text-left">
                <i class="fas fa-file-invoice-dollar"></i>
                <span>Billing Information: </span>
              </div>
              <div class="col-md-6 text-right">
                <button class="btn btn-warning" style="font-size: 14px">
                  Request to change signature
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-12">
        <div class="row white-bg mt-5">
          <div class="col-md-12">
            <h6 class="p-4 border-bottom">
              Active Subscriptions assigned to your account
            </h6>
          </div>
          <div class="col-md-12">
            <div class="table-responsive mt-4">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>Subscription type</th>
                    <th>State</th>
                    <th>Started from</th>
                    <th>valid until</th>
                    <th>Purchased by</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody v-if="sub_scriptions_list && sub_scriptions_list != null">
                  <tr v-for="sub in sub_scriptions_list" :key="sub.id">
                    <td>{{ sub.subscription.name }}</td>
                    <td>
                      <button class="btn btn-success btn-14px">{{ sub.status }}</button>
                    </td>
                    <td>{{ sub.start }}</td>
                    <td>{{ sub.end }}</td>
                    <td>
                      <button class="btn btn-info btn-14px disabled">You</button>
                    </td>
                    <td>
                      <button class="btn btn-danger btn-14px disabled">
                        Unassigned me
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  name: "DashboardPage",
  data() {
    return {
      notes_list: null,
      sub_scriptions_list: null,
    };
  },
  mounted() {
    this.$emit("updateNav", this.$route.name);
    document.title = "Dashboard";
    this.getNotesList();
    if (this.$route.query.status == "success") {
      this.verifyPayment();
      this.$toastr.s("Your Subscription has been activated", "Payment Successfully");
    } else if (this.$route.query.status == "cancel") {
      this.$toastr.e("Payment Failed Go to Subscriptions and Pay again", "Payment Failed");
    }
    this.subscriptions();
  },
  methods: {
    getNotesList() {
      const { token } = JSON.parse(localStorage.getItem("loginInfo"));
      let headers = {
        Accept: "application/json",
        "Content-Type": "application/json",
        Authorization: `Bearer ${token}`,
      };
      this.axios
        .get(process.env.MIX_API_URL + "/api/notes", { headers: headers })
        .then((response) => {
          this.notes_list = response.data.data.notes;
        })
        .catch((error) => {
          console.log(error);
        });
    },
    verifyPayment() {
      const { token } = JSON.parse(localStorage.getItem("loginInfo"));
      let url = process.env.MIX_API_URL + "/api/verify-payment";
      let headers = {
        "Accept": "application/json",
        "Content-Type": "application/json",
        Authorization: `Bearer ${token}`,
      };
      this.axios.post(url, { subscription_code: this.$route.query.ref }, { headers: headers }).then((response) => {
        //
      }).catch((error) => {
        console.log(error);
      });
    },
    submitNewNote() {
      if (this.notes_list != null && this.notes_list.length > 0) {
        this.$router.push({
          name: "make_note",
          params: { type: this.notes_list[0].id, section: "MyPhrase" },
        });
      } else {
        this.$toastr.e("No notes available for you", "Error!");
      }
    },
    subscriptions() {
      const { token } = JSON.parse(localStorage.getItem("loginInfo"));
      let url = process.env.MIX_API_URL + "/api/user/active-subscriptions";
      let headers = {
        "Accept": "application/json",
        "Content-Type": "application/json",
        Authorization: `Bearer ${token}`,
      };
      this.axios.get(url, { headers: headers }).then((response) => {
        this.sub_scriptions_list = response.data.data.subscriptions;
      }).catch((error) => {
        console.log(error);
      });
    },
  },
  computed: {
    userInfo() {
      if (this.$store.state.login.user != null) {
        return this.$store.state.login.user;
      } else {
        this.$store.dispatch("login/userInformation").then(() => {
          return this.$store.state.login.user;
        });
      }
    },
  },
};
</script>