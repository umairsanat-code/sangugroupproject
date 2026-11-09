<template>
  <div>
    <section class="page-hero">
      <div class="page-hero-bg">
        <img src="https://images.pexels.com/photos/39356225/pexels-photo-39356225.jpeg?auto=compress&cs=tinysrgb&w=1920" alt="Visa Services Background" class="opacity-50 object-cover">
      </div>
      <div class="container relative z-10">
        <div class="eyebrow">Visa Services</div>
        <h1 style="font-size:clamp(28px,4vw,44px);">All visa destinations in one place</h1>
        <p style="max-width:560px;">Browse visit visas by country, compare processing time and pick the one that fits your travel date.</p>
      </div>
    </section>

    <div class="container">
      <div class="listing">
        <aside class="filters">
          <h4>Region</h4>
          <label class="filter-row"><input type="checkbox" checked> Middle East</label>
          <label class="filter-row"><input type="checkbox" checked> Europe</label>
          <label class="filter-row"><input type="checkbox" checked> Asia</label>
          <h4>Processing Time</h4>
          <label class="filter-row"><input type="checkbox" checked> 1-3 Days</label>
          <label class="filter-row"><input type="checkbox" checked> 4-7 Days</label>
          <label class="filter-row"><input type="checkbox" checked> 7+ Days</label>
          <h4>Entry Type</h4>
          <label class="filter-row"><input type="checkbox" checked> Single Entry</label>
          <label class="filter-row"><input type="checkbox" checked> Multiple Entry</label>
          <h4>Price Range</h4>
          <input type="range" min="50" max="2000" v-model="budget" style="width:100%;">
          <div class="form-hint">Up to {{ formatEUR(budget) }}</div>
        </aside>

        <div>
          <div class="results-bar">
            <span class="mono" style="font-size:13px;color:var(--ink-soft);">{{ filteredVisas.length }} visa destinations</span>
            <select v-model="sortBy">
              <option value="popular">Sort: Most Popular</option>
              <option value="price-low">Price: Low to High</option>
              <option value="price-high">Price: High to Low</option>
              <option value="fast">Fastest Processing</option>
            </select>
          </div>
          <div class="grid grid-3">
            <div class="card" v-for="v in filteredVisas" :key="v.slug">
              <div class="card-arch">
                <span v-if="v.popular" class="badge">Popular</span>
                <div v-if="!v.image_url" style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:52px;background:linear-gradient(160deg,#dfe8e6,#0E4B44);">{{ v.flag }}</div>
                <img v-else :src="v.image_url" :alt="v.country" style="width:100%;height:100%;object-fit:cover;">
              </div>
              <div class="card-body">
                <h3>{{ v.country }}</h3>
                <div class="meta">{{ v.title || v.type }} · {{ v.entry }} · Valid {{ v.validity }}</div>
                <p class="desc">{{ v.desc }}</p>
                <div class="card-foot">
                  <div class="price">{{ formatEUR(v.discounted_price || v.price) }}<br><small>per applicant</small></div>
                  <router-link :to="`/visa/${v.slug}`" class="btn btn-ghost" style="padding:10px 16px;">Details</router-link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import { formatEUR } from '../../utils/currency.js';

const visas = ref([]);
const sortBy = ref('popular');
const budget = ref(2000);

onMounted(async () => {
  try {
    const res = await axios.get('/api/visas');
    visas.value = res.data;
  } catch (err) {
    console.error("Error loading visas", err);
  }
});

const filteredVisas = computed(() => {
  let list = visas.value.filter(v => v.price <= budget.value);
  if (sortBy.value === 'price-low') list.sort((a,b) => a.price - b.price);
  if (sortBy.value === 'price-high') list.sort((a,b) => b.price - a.price);
  if (sortBy.value === 'popular') list.sort((a,b) => (b.popular ? 1 : 0) - (a.popular ? 1 : 0));
  if (sortBy.value === 'fast') list.sort((a,b) => parseInt(a.processing) - parseInt(b.processing));
  return list;
});
</script>
