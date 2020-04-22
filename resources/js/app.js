require("./bootstrap");
import Vue from "vue";
import router from "./routes";

window.EventBus = new Vue();

// views
Vue.component("app", () => import("./views/App.vue"));

// components
Vue.component("smbr-rom-loader", () =>
    import("./components/SMBRRomLoader.vue")
);
Vue.component("smbr-select", () => import("./components/SMBRSelect.vue"));
Vue.component("smbr-checkbox", () => import("./components/SMBRCheckbox.vue"));
Vue.component("smbr-input", () => import("./components/SMBRInput.vue"));

var root = new Vue({
    el: "#root",
    data: {},
    methods: {},
    router: router
});
