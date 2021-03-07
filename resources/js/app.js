import Vue from "vue";
import store from "./store";
import App from './components/App';

Vue.component(App.name, App);
Vue.config.productionTip = false;

const app = new Vue({
    el: "#app",
    store,
});
