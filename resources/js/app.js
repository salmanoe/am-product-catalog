import './bootstrap';

import { createApp } from 'vue';
import ProductList from '/resources/js/components/ProductList.vue';

const app = createApp({});
app.component('product-list', ProductList);
app.mount('#app');
