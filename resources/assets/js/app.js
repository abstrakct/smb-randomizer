require("./bootstrap");
import Vue from "vue";
import router from "./routes";

window.EventBus = new Vue();

// views
Vue.component("app", require("./views/App.vue"));

// components
Vue.component("smbr-rom-loader", require("./components/SMBRRomLoader.vue"));

var root = new Vue({
  el: "#root",
  data: {},
  methods: {},
  router: router
});
