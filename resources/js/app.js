import Vue from "vue";
import store from "./store";
import App from './components/App';
import VTooltip from 'v-tooltip'

// Font Awesome
import './font-awesome';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

Vue.component(App.name, App);
Vue.component('fa-icon', FontAwesomeIcon);
Vue.use(VTooltip);
Vue.config.productionTip = false;

const app = new Vue({
    el: "#app",
    store,
});
