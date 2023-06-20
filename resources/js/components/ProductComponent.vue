<template>
  <div class="container my-5">
    <div class="row mb-3 justify-content-between">
      <!-- create button -->
      <div class="col-4">
        <button class="btn btn-primary me-2" @click="create">
          <i class="fa-solid fa-plus-circle me-1"></i> Create
        </button>
        <button class="btn btn-primary" @click="showCreateCard = false; showDetailCard = false; showCompareCard = true">
          <i class="fas fa-tags me-1"></i>Compare Product Price
        </button>
      </div>
      <!-- search product -->
      <div class="col-8">
        <form @submit.prevent="productList" class="row justify-content-start">
          <div class="col-5">
            <input
              type="text"
              v-model="search"
              placeholder="Search Name"
              class="form-control bg-white"
            />
          </div>
          <div class="col-5">
            <select class="form-select bg-white" v-model="selectedRange">
              <option v-for="range in priceRange" :key="range" :value="range">
                {{ range[0] }}~{{ range[1] }}
              </option>
            </select>
          </div>
          <div class="col-1">
            <button class="btn btn-primary w-" type="submit">
              <i class="fa-solid fa-magnifying-glass me-1"></i>
            </button>
          </div>
        </form>
      </div>
    </div>
    <div class="row">
      <div class="col-4">
        <!-- product detail -->
        <div v-if="showDetailCard">
          <div class="card">
            <div class="card-header fs-2 fw-bold">Detail</div>
            <div class="card-body bg-white fs-6 fw-semibold">
              <div class="mb-3">
                <span>Name : </span>{{ productDetail.name }}
              </div>
              <div class="mb-3">
                <span>Category : </span>{{ productDetail.category }}
              </div>
              <div class="mb-3">
                <span>Price : </span>{{ productDetail.price }}
              </div>
            </div>
          </div>
        </div>
        <!-- create and update -->
        <div v-else-if="showCreateCard">
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
                  <label for="">Category</label>
                  <select v-model="product.category" class="form-select">
                    <option
                      v-for="category in categories"
                      :key="category.id"
                      :value="category.id"
                    >
                      {{ category.label }}
                    </option>
                  </select>
                  <span class="text-danger" v-if="error.category">{{
                    error.category[0]
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
        <div v-else-if="showCompareCard"> 
          <div class="card">
          <div class="card-header fs-2 fw-bold">Compare Price</div>
          <div class="card-body bg-white fs-6 fw-semibold">
            <form @submit.prevent="compareProductPrice">
              <div class="mb-3">
                <label for="">Select Category</label>
                <select v-model="selectedCompareCategory" class="form-select">
                  <option
                    v-for="category in categories"
                    :key="category.id"
                    :value="category.id"
                  >
                    {{ category.label }}
                  </option>
                </select>
                <span class="text-danger" v-if="error.selectedCategory">{{
                  error.selectedCategory[0]
                }}</span>
              </div>
              <div class="mb-3">
                <label for="">First Product</label>
                <select v-model="selectedProduct1" class="form-select">
                  <option v-for="product in getCategoryProducts(selectedCompareCategory)" :key="product.id" :value="product">{{ product.name }}</option>
                </select>
                <span class="text-danger" v-if="error.selectedProduct1">{{
                  error.selectedProduct1[0]
                }}</span>
              </div>
              <div class="mb-3">
                <label for="">Second Product</label>
                <select v-model="selectedProduct2" class="form-select">
                  <option v-for="product in getCategoryProducts(selectedCompareCategory)" :key="product.id" :value="product">{{ product.name }}</option>
                </select>
                <span class="text-danger" v-if="error.selectedProduct2">{{
                  error.selectedProduct2[0]
                }}</span>
              </div>
              <button class="btn btn-primary" type="submit">
                <i class="fas fa-tags me-1"></i>Compare
              </button>
            </form>
          </div>
        </div>
        </div>
      </div>
      <!-- product list -->
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
                  class="btn btn-primary btn-sm me-2"
                  @click="detail(product.id)"
                >
                  <i class="fas fa-info-circle me-1"></i>Detail
                </button>
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
import { ref, reactive, onMounted, computed } from "vue";

let isEdit = false;
let showCreateCard = ref(true); 
let showDetailCard = ref(false);
let showCompareCard = ref(false);
let products = ref([]);
let productId = ref("");
let search = ref("");
const categories = [
  { id: "accessories", label: "Accessories" },
  { id: "stationery", label: "Stationery" },
  { id: "utensils", label: "Utensils" },
  { id: "shoe", label: "Shoe" },
  { id: "cloth", label: "Clothings" },
];
const selectedCompareCategory = ref('');
const categoryProduct = ref([]);
const selectedRange = ref([]);
const minPrice = ref(0);
const maxPrice = ref(0);
const productDetail = ref([]);
const selectedProduct1 = ref('')
const selectedProduct2 = ref('')
const product = reactive({
  name: ref(""),
  category: ref(""),
  price: ref(""),
});
let successMessage = ref("");
let error = ref([]);

onMounted(() => {
  productList();
});

// get price range
const priceRange = computed(() => {
  const range = [];
  const pairs = [];
  const step = (maxPrice.value - minPrice.value) / 5;
  for (let i = 0; i < 6; i++) {
    const value = minPrice.value + step * i;
    range.push(Math.round(value));
  }

  for (let i = 0; i < range.length - 1; i++) {
    const pair = [range[i], range[i + 1]];
    pairs.push(pair);
  }
  return pairs;
});

// get product having same category
const getCategoryProducts = (categoryId) => {
  if (categoryId) {
    return categoryProduct.value.filter((product) => product.category === categoryId);
  } else {
    return [];
  }
};

// compare product price
const compareProductPrice = () => {
  if (selectedCompareCategory.value === '') {
    error.value.selectedCategory = ['Please select category first.']
  }
  if (selectedProduct1.value === '') {
    error.value.selectedProduct1 = ['Please select first product.'];
  }
  if (selectedProduct2.value === '') {
    error.value.selectedProduct2 = ['Please select second product.'];
  }
  else {
    const price1 = selectedProduct1.value.price;
    const price2 = selectedProduct2.value.price;
    let priceDiff = price1 - price2;
    let title = '';
    let message = '';

    if (priceDiff > 0) {
      title = `${selectedProduct1.value.name} is more expensive.`;
      message = `The price difference is ${priceDiff} kyats.`;
    } else if (priceDiff < 0) {
      title = `${selectedProduct2.value.name} is more expensive.`;
      message = `The price difference is ${Math.abs(priceDiff)} kyats.`;
    } else {
      title = 'Both products have the same price.';
      message = 'There is no price difference.';
    }
    Swal.fire({
      icon: 'info',
      title: title,
      text: message,
      showClass: {
        popup: 'animate__animated animate__fadeInDown'
      },
      hideClass: {
        popup: 'animate__animated animate__fadeOutUp'
      }
    });
    selectedCompareCategory.value = ''
    selectedProduct1.value = ''
    selectedProduct2.value = ''
  }
}

// product list
const productList = async (page = 1) => {
  await axios
    .get("/api/products", {
      params: {
        page: page,
        search: search.value,
        price: selectedRange.value,
      },
    })
    .then((res) => {
      products.value = res.data.product;
      categoryProduct.value = res.data.categoryProduct
      const price = res.data.price;
      minPrice.value = Math.min(...price);
      maxPrice.value = Math.max(...price);
    });
};

// product detail
const detail = (product_id) => {
  showDetailCard.value = true;
  axios.get(`/api/products/${product_id}`).then((res) => {
    productDetail.value = res.data;
  });
};

const create = () => {
  isEdit = false;
  showCreateCard.value= true;
  showDetailCard.value = false;
  showCompareCard.value = false;
  productId.value = "";
  product.name = "";
  product.price = "";
  product.category = "";
  error.value = "";
};

// store product
const store = async () => {
  await axios
    .post("api/products", product)
    .then((res) => {
      successMessage.value = res.data.message;
      product.name = "";
      product.price = "";
      product.category = "";
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
        console.log(error.value)
      }
    });
};

const edit = (selectedProduct) => {
  isEdit = true;
  showCreateCard.value= true;
  showDetailCard.value = false;
  showCompareCard.value = false;
  productId.value = selectedProduct.id;
  product.name = selectedProduct.name;
  product.price = selectedProduct.price;
  product.category = selectedProduct.category;
};

// update product
const update = () => {
  axios
    .put(`/api/products/${productId.value}`, product)
    .then((res) => {
      successMessage.value = res.data.message;
      product.name = "";
      product.price = "";
      product.category = "";
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

// delete product
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
        product.name = "";
        product.price = "";
        product.category = "";
        productList();
      });
    }
  });
};
</script>