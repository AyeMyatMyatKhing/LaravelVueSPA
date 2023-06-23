<template>
  <div class="container my-5">
    <div class="row mb-3 justify-content-between">
      <!-- left side -->
      <div class="col-4">
        <!-- create and compare button -->
        <div class="mb-4">
          <button class="btn btn-primary me-2" @click="create">
          <i class="fa-solid fa-plus-circle me-1"></i> Create
          </button>
          <button class="btn btn-primary" @click="compareCard">
            <i class="fas fa-tags me-1"></i>Compare Product Price
          </button>
        </div>
        <!-- product detail -->
        <div v-if="showDetailCard">
          <div class="card">
            <div class="card-header fs-2 fw-bold">Detail</div>
            <div class="card-body bg-white fs-6 fw-semibold">
              <div class="mb-3">
                <span>Name : </span>{{ productDetail[0].name }}
              </div>
              <div class="mb-3">
                <span>Category : </span>{{ productDetail[0].category }}
              </div>
              <div class="mb-3">
                <span>Price : </span>{{ productDetail[0].price }}
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
                    <option value="">Select category</option>
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
        <!-- compare price -->
        <div v-else-if="showCompareCard"> 
          <div class="card">
          <div class="card-header fs-2 fw-bold">Compare Price</div>
          <div class="card-body bg-white fs-6 fw-semibold">
            <form @submit.prevent="compareProductPrice">
              <div class="mb-3">
                <label for="">Select Category</label>
                <select v-model="selectedCompareCategory" class="form-select">
                  <option value="">Select category</option>
                  <option
                    v-for="category in categories"
                    :key="category.id"
                    :value="category.id"
                  >
                    {{ category.label }}
                  </option>
                </select>
                <span class="text-danger" v-if="error.selectedCompareCategory">{{
                  error.selectedCompareCategory[0]
                }}</span>
              </div>
              <div class="mb-3">
                <label for="">First Product</label>
                <select v-model="selectedProduct1" class="form-select" :disabled="!selectedCompareCategory">
                  <option value="">Enter first product</option>
                  <option v-for="product in getCategoryProducts(selectedCompareCategory)" :key="product.id" :value="product">{{ product.name }}</option>
                </select>
                <span class="text-danger" v-if="error.selectedProduct1">{{
                  error.selectedProduct1[0]
                }}</span>
              </div>
              <div class="mb-3">
                <label for="">Second Product</label>
                <select v-model="selectedProduct2" class="form-select" :disabled="!selectedProduct1">
                  <option value="">Enter second product</option>
                  <option v-for="product in getFilteredCategoryProducts" :key="product.id" :value="product">{{ product.name }}</option>
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
      <!-- right side -->
      <div class="col-8">
        <form @submit.prevent="productList" class="row justify-content-start mb-4">
          <div class="col-5 d-flex">
            <label class="fs-6 fw-semibold mt-2 me-3">Name</label>
            <input
              type="text"
              v-model="search"
              placeholder="Enter search keyword"
              class="form-control bg-white"
            />
          </div>
          <div class="col-5 d-flex">
            <label class="fs-6 fw-semibold mt-2 me-3">Price</label>
            <select class="form-select bg-white" v-model="selectedRange">
              <option value="">All</option>
              <option v-for="range in priceRange" :key="range" :value="range">
                {{ range[0] }}~{{ range[1] }}
              </option>
            </select>
          </div>
          <div class="col-1">
            <button class="btn btn-primary" type="submit">
              <i class="fa-solid fa-magnifying-glass me-1"></i>
            </button>
          </div>
        </form>
        <div v-if="isSearch" class="col-6 border rounded mt-3 bg-light bg-gradient px-2 py-1 text-center mb-3">
          <p>You are searching with <span v-if="search">"{{ search }}" and</span> <span v-if="selectedRange">the price range between {{ selectedRange[0] }} and {{ selectedRange[1] }}.</span>
             The result data is {{ noOfSearchResult }}.</p>
          <button class="btn btn-primary btn-sm" @click="closeAlertMessage">Close</button>
        </div>
        <!-- product list -->
        <table class="table table-success table-striped">
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Price</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody v-if="paginateFilterData.length > 0">
            <tr v-for="product in paginateFilterData" :key="product.id">
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
          <tbody v-else>
            <tr class="text-center"><td colspan="4">No Data</td></tr>
          </tbody>
        </table>
        <pagination
          v-if="showPagination"
          :total="totalPages"
          v-model="currentPage"
          :per-page="itemsPerPage"
          :page-count="pageCount"
          @input="updateData"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import axios from "axios";
import { ref, reactive, onMounted, computed } from "vue";
import { getCurrentInstance } from 'vue'

const progress = getCurrentInstance().appContext.config.globalProperties.$Progress
let isEdit = false;
let isSearch = ref(false);
let showCreateCard = ref(true); 
let showDetailCard = ref(false);
let showCompareCard = ref(false);
let productsList = ref([]);
let productId = ref("");
let search = ref("");
const categories = [
  { id: "accessories", label: "Accessories" },
  { id: "stationery", label: "Stationery" },
  { id: "utensils", label: "Utensils" },
  { id: "shoe", label: "Shoe" },
  { id: "cloth", label: "Clothings" },
];
const noOfSearchResult = ref()
const selectedRange = ref('');
const minPrice = ref(0);
const maxPrice = ref(0);
const productDetail = ref();
const selectedCompareCategory = ref('');
const selectedProduct1 = ref('')
const selectedProduct2 = ref('')
const product = reactive({
  name: ref(""),
  category: ref(""),
  price: ref(""),
});
let successMessage = ref("");
let error = ref([]);
const itemsPerPage = 10;
const currentPage = ref(1);

onMounted(() => {
  productList();
});

// get price range for filtering product
const priceRange = computed(() => {
  const pairs = [];
  const range = maxPrice.value - minPrice.value;
  const step = Math.ceil(range / 5);

  let start = minPrice.value;
  for (let i = 0; i < 5; i++) {
    const end = Math.min(start + step, maxPrice.value);
    pairs.push([start, end]);
    start = end + 1;
  }
  
  return pairs;
});

// get product having same category for product comparison
const getCategoryProducts = (categoryId) => {
  if (!categoryId) {
    return [];
  } 
  const productOfSelectedCategory = productsList.value.filter((product) => product.category === categoryId);
  if(productOfSelectedCategory.length === 1) {
    Swal.fire({
      icon: 'info',
      text: "The selected category has only one product",
      showClass: {
        popup: 'animate__animated animate__fadeInDown'
      },
      hideClass: {
        popup: 'animate__animated animate__fadeOutUp'
      }
    });
    selectedCompareCategory.value = ''
  }
  return productOfSelectedCategory
};

// get options for second product
const getFilteredCategoryProducts = computed(() => {
  selectedProduct2.value = ''
  return getCategoryProducts(selectedCompareCategory.value).filter(
    product => product.id !== selectedProduct1.value.id
  );
})

const compareCard = () => {
  isEdit = false;
  showCreateCard.value= false;
  showDetailCard.value = false;
  showCompareCard.value = true;
  productId.value = "";
  product.name = "";
  product.price = "";
  product.category = "";
  error.value = ""
}

// compare product price
const compareProductPrice = () => {
  if (selectedCompareCategory.value === '') {
  error.value = {selectedCompareCategory : ['Please select category first.']}
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
    error.value = ''
  }
}

const filterData = () => {
  let filteredProducts  = productsList.value
  if (search.value !== '') {
    filteredProducts = filteredProducts.filter((product) =>
      product.name.toLowerCase().includes(search.value.toLowerCase())
    )
  }
  if(Array.isArray(selectedRange.value)) {
    filteredProducts = filteredProducts.filter((product) => 
      product.price >= selectedRange.value[0] && product.price <= selectedRange.value[1]
    )
  }
  return filteredProducts;
}

const paginateFilterData = computed(() => {
  const startIndex = (currentPage.value - 1) * itemsPerPage;
  const endIndex = startIndex + itemsPerPage;
  return filterData().slice(startIndex, endIndex);
})

const showPagination = computed(() => filterData().length > itemsPerPage);

const totalPages = computed(() => Math.ceil(filterData().length / itemsPerPage));

const pageCount = computed(() => Math.ceil(filterData().length / itemsPerPage));

const updateData = (page) => {
  currentPage.value = page;
};

// product list
const productList = () => {
  progress.start()
  if (Array.isArray(selectedRange.value) || search.value !== '') {
    isSearch = true
  }
  axios.get("/api/products", {
      params: {
        search: search.value,
        price: selectedRange.value,
      },
    })
    .then((res) => {
      productsList.value = res.data.product;
      const price = res.data.price;
      noOfSearchResult.value = productsList.value.length
      minPrice.value = Math.min(...price);
      maxPrice.value = Math.max(...price);
      progress.finish()
    })
    .catch(error => {
      progress.fail()
    });
};

const closeAlertMessage = () => {
  isSearch = false
  search.value = ''
  selectedRange.value = ''
  productList()
}

// product detail
const detail = (product_id) => {
  showDetailCard.value = true;
  selectedCompareCategory.value = ''
  selectedProduct1.value = ''
  selectedProduct2.value = ''
  productDetail.value = productsList.value.filter(product => product.id === product_id);
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
  selectedCompareCategory.value = ''
  selectedProduct1.value = ''
  selectedProduct2.value = ''
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
  selectedCompareCategory.value = ''
  selectedProduct1.value = ''
  selectedProduct2.value = ''
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