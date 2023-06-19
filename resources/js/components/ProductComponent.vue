<template>
  <div class="container my-5">
    <div class="row mb-3 justify-content-end">
      <div class="col-4">
        <button class="btn btn-primary" @click="create">
          <i class="fa-solid fa-plus-circle me-1"></i> Create
        </button>
      </div>
      <div class="col-4">
        <form @submit.prevent="productList">
          <div class="input-group">
            <input
              type="text"
              v-model="search"
              aria-describedby="basic-addon2"
              placeholder="Search"
              class="form-control bg-white"
            />
            <button class="btn btn-primary" id="basic-addon2" type="submit">
              <i class="fa-solid fa-magnifying-glass"></i>
            </button>
          </div>
        </form>
      </div>
    </div>
    <div class="row">
      <!-- create and update -->
      <div class="col-4">
        <div class="card">
          <div class="card-header fs-2 fw-bold">
            {{ isEdit ? "Edit" : "Create" }}
          </div>
          <div class="card-body bg-white fs-6 fw-semibold">
            <form @submit.prevent="isEdit ? update() : store()">
              <div class="mb-3">
                <label for="">Name</label>
                <input
                  type="text"
                  v-model="product.name"
                  class="form-control"
                  name="name"
                />
                <span class="text-danger" v-if="error.name">{{
                  error.name[0]
                }}</span>
              </div>
              <div class="mb-3">
                <label for="">Price</label>
                <input
                  type="text"
                  v-model="product.price"
                  name="price"
                  class="form-control"
                />
                <span class="text-danger" v-if="error.price">{{
                  error.price[0]
                }}</span>
              </div>
              <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-floppy-disk me-1"></i
                >{{ !isEdit ? "Save" : "Update" }}
              </button>
            </form>
          </div>
        </div>
      </div>
      <!-- table -->
      <div class="col-8">
        <table class="table table-success table-striped">
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Price</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="product in products.data" :key="product.id">
              <td>{{ product.id }}</td>
              <td>{{ product.name }}</td>
              <td>{{ product.price }}</td>
              <td>
                <button
                  class="btn btn-success btn-sm me-2"
                  @click="edit(product)"
                >
                  <i class="fa-solid fa-pen-to-square me-1"></i>Edit
                </button>
                <button
                  class="btn btn-danger btn-sm"
                  @click="destroy(product.id)"
                >
                  <i class="fa-solid fa-trash-can me-1"></i>Delete
                </button>
              </td>
            </tr>
          </tbody>
        </table>
        <pagination :data="products" @pagination-change-page="productList" />
      </div>
    </div>
  </div>
</template>

<script setup>
import axios from "axios";
import { ref, reactive, onMounted } from "vue";

let isEdit = false;
let products = ref({});
let productId = ref("");
let search = ref("");
const product = reactive({
  name: ref(""),
  price: ref(""),
});
let successMessage = ref("");
let error = ref([]);

onMounted(() => {
  productList();
});

const productList = async (page = 1) => {
  await axios
    .get(`/api/products/?page=${page}&search=${search.value}`)
    .then((res) => {
      products.value = res.data;
    });
};

const create = () => {
  isEdit = false;
  productId.value = "";
  product.name = "";
  product.price = "";
  error.value = "";
};

const store = async () => {
  await axios
    .post("api/products", product)
    .then((res) => {
      successMessage.value = res.data.message;
      product.name = "";
      product.price = "";
      productList();
      error.value = "";
      Toast.fire({
        icon: "success",
        title: "Product created successfully.",
      });
    })
    .catch((err) => {
      if (err.response.status === 422) {
        error.value = err.response.data.errors;
      }
    });
};

const edit = (selectedProduct) => {
  isEdit = true;
  productId.value = selectedProduct.id;
  product.name = selectedProduct.name;
  product.price = selectedProduct.price;
};

const update = () => {
  axios
    .put(`/api/products/${productId.value}`, product)
    .then((res) => {
      successMessage.value = res.data.message;
      product.name = "";
      product.price = "";
      productList();
      error.value = "";
      Toast.fire({
        icon: "success",
        title: "Product updated successfully.",
      });
    })
    .catch((err) => {
      if (err.response.status === 422) {
        error.value = err.response.data.errors;
      }
    });
};

const destroy = (id) => {
  Swal.fire({
    title: "Are you sure?",
    text: "You won't be able to revert this!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Yes, delete it!",
  }).then((result) => {
    if (result.isConfirmed) {
      axios.delete(`/api/products/${id}`).then((res) => {
        Swal.fire(
          "Deleted!",
          "Product has been deleted successfully.",
          "success"
        );
        productList();
      });
    }
  });
};
</script>