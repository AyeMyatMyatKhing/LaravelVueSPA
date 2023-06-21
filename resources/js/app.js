import './bootstrap'
import { createApp } from 'vue'
// import { Bootstrap5Pagination } from 'laravel-vue-pagination'
import Swal from 'sweetalert2'
import Paginate from "vuejs-paginate-next";
import VueProgressBar from '@aacassandra/vue3-progressbar';
import ProductComponent from './components/ProductComponent.vue';

const app = createApp({});

app.component('pagination' , Paginate)
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
window.Swal = Swal

const options = {
  color: "#0B5ED7",
  failedColor: "#874b4b",
  thickness: "5px",
  transition: {
    speed: "0.3s",
    opacity: "0.6s",
    termination: 300,
  },
  autoRevert: true,
  location: "top",
  inverse: false,
};

app.use(VueProgressBar, options);

app.mount('#app');