<template>
  <div class="flex flex-col items-center justify-center min-h-screen bg-gray-100 p-6">
    <div class="w-full max-w-4xl bg-white shadow-md rounded-lg p-6">
      <h2 class="text-xl font-semibold text-gray-800">Product Catalog</h2>
      <button @click="openCreateModal" class="mt-4 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition">
        Add New Product
      </button>

      <div v-if="loading" class="fixed inset-0 flex items-center justify-center bg-transparent z-50">
        <div class="loader"></div>
      </div>

      <div v-if="products.length > 0" class="mt-6">
        <table class="w-full border-collapse border border-gray-300">
          <thead class="bg-gray-200">
            <tr>
              <th class="p-3 border border-gray-300">ID</th>
              <th class="p-3 border border-gray-300">Name</th>
              <th class="p-3 border border-gray-300">Price</th>
              <th class="p-3 border border-gray-300">Stock</th>
              <th class="p-3 border border-gray-300">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="product in products" :key="product.id" class="text-center">
              <td class="p-3 border border-gray-300">{{ product.id }}</td>
              <td class="p-3 border border-gray-300">{{ product.name }}</td>
              <td class="p-3 border border-gray-300">{{ formatPrice(product.price) }}</td>
              <td class="p-3 border border-gray-300">{{ product.stock }}</td>
              <td class="p-3 border border-gray-300">
                <button @click="openEditModal(product)" class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 transition">
                  Edit
                </button>
                <button @click="deleteProduct(product.id)" class="ml-2 px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition">
                  Delete
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div v-else class="mt-6 text-gray-600">
        <p>No products available.</p>
      </div>
    </div>

    <div v-if="showModal" class="fixed inset-0 flex items-center justify-center bg-transparent">
      <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
        <h3 class="text-lg font-semibold">{{ isEditing ? 'Edit Product' : 'Add New Product' }}</h3>
        <form @submit.prevent="saveProduct" class="mt-4">
          <div class="mb-4">
            <label for="name" class="block font-medium">Name:</label>
            <input type="text" id="name" v-model="form.name" required class="w-full p-2 border border-gray-300 rounded">
          </div>
          <div class="mb-4">
            <label for="description" class="block font-medium">Description:</label>
            <textarea id="description" v-model="form.description" class="w-full p-2 border border-gray-300 rounded"></textarea>
          </div>
          <div class="mb-4">
            <label for="price" class="block font-medium">Price:</label>
            <input type="number" id="price" v-model.number="form.price" step="0.01" required class="w-full p-2 border border-gray-300 rounded">
          </div>
          <div class="mb-4">
            <label for="stock" class="block font-medium">Stock:</label>
            <input type="number" id="stock" v-model.number="form.stock" required class="w-full p-2 border border-gray-300 rounded">
          </div>
          <div class="flex justify-between mt-4">
            <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 transition">{{ isEditing ? 'Update' : 'Save' }}</button>
            <button type="button" @click="closeModal" class="px-4 py-2 bg-gray-400 text-white rounded hover:bg-gray-500 transition">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
  
  <script>
  import axios from 'axios';
  
  export default {
    data() {
      return {
        products: [],
        showModal: false,
        isEditing: false,
        currentProductId: null,
        loading: false,
        form: {
          name: '',
          description: '',
          price: null,
          stock: null,
        },
      };
    },
    mounted() {
      this.fetchProducts();
    },
    methods: {
      async fetchProducts() {
        this.loading = true;
        try {
          const response = await axios.get('/api/products');
          this.products = response.data.data;
        } catch (error) {
          console.error('Error fetching products:', error);
        }
        this.loading = false;
      },
      openCreateModal() {
        this.form = { name: '', description: '', price: null, stock: null };
        this.isEditing = false;
        this.currentProductId = null;
        this.showModal = true;
      },
      openEditModal(product) {
        this.form = { ...product };
        this.isEditing = true;
        this.currentProductId = product.id;
        this.showModal = true;
      },
      closeModal() {
        this.showModal = false;
      },
      async saveProduct() {
        this.loading = true;
        try {
          if (this.isEditing) {
            await axios.put(`/api/products/${this.currentProductId}`, this.form);
            this.$emit('notify', { type: 'success', message: 'Product updated successfully!' });
          } else {
            await axios.post('/api/products', this.form);
            this.$emit('notify', { type: 'success', message: 'Product created successfully!' });
          }
          this.closeModal();
          this.fetchProducts();
        } catch (error) {
          console.error('Error saving product:', error.response ? error.response.data : error);
          this.$emit('notify', { type: 'error', message: 'Failed to save product.' });
        }
        this.loading = false;
      },
      async deleteProduct(id) {
        if (confirm('Are you sure you want to delete this product?')) {
          this.loading = true;
          try {
            await axios.delete(`/api/products/${id}`);
            this.fetchProducts();
            this.$emit('notify', { type: 'success', message: 'Product deleted successfully!' });
          } catch (error) {
            console.error('Error deleting product:', error);
            this.$emit('notify', { type: 'error', message: 'Failed to delete product.' });
          }
          this.loading = false;
        }
      },
      formatPrice(price) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(price);
      },
    },
  };
  </script>
  
  <style scoped>
  /* Basic modal styling */
  .modal {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background-color: white;
    padding: 20px;
    border: 1px solid #ccc;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    z-index: 10;
  }
  
  .modal h3 {
    margin-top: 0;
  }
  
  .modal div {
    margin-bottom: 10px;
  }
  
  .modal label {
    display: block;
    margin-bottom: 5px;
  }
  
  .modal input[type="text"],
  .modal input[type="number"],
  .modal textarea {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    box-sizing: border-box;
  }
  
  .modal .actions button {
    margin-right: 10px;
  }
  
  table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
  }
  
  th, td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: left;
  }
  
  th {
    background-color: #f2f2f2;
  }
  
  button {
    padding: 8px 12px;
    margin-right: 5px;
    cursor: pointer;
  }

  .loader {
    border: 4px solid rgba(255, 255, 255, 0.3); /* Faint outer ring */
    border-radius: 50%;
    border-top: 4px solid #3498db; /* Visible spinning top */
    width: 50px;
    height: 50px;
    animation: spin 1s linear infinite;
  }

  @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }
  </style>