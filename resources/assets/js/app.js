require("./bootstrap");
import Vue from "vue";
import router from "./routes";

import App from "./views/App.vue";

window.EventBus = new Vue();

var app = new Vue({
  el: "#root",
  data: {},
  methods: {},
  router: router,
  components: {
    App
  }
});
