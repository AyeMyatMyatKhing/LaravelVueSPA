import './bootstrap'
import { createApp } from 'vue'
import { Bootstrap5Pagination } from 'laravel-vue-pagination'
import Swal from 'sweetalert2'
import VueProgressBar from "@aacassandra/vue3-progressbar";

const app = createApp({});

import ProductComponent from './components/ProductComponent.vue';

app.component('pagination', Bootstrap5Pagination);
app.component('product-component' , ProductComponent);

window.Toast = Swal.mixin({
    toast: true,
    position: "top-end",
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
      toast.addEventListener("mouseenter", Swal.stopTimer);
      toast.addEventListener("mouseleave", Swal.resumeTimer);
    },
});

const options = {
  color: "#bffaf3",
  failedColor: "#874b4b",
  thickness: "5px",
  transition: {
    speed: "0.2s",
    opacity: "0.6s",
    termination: 300,
  },
  autoRevert: true,
  location: "left",
  inverse: false,
};

app.use(VueProgressBar, options);

window.Swal = Swal

app.mount('#app');
