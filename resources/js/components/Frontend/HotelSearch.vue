<template>
  <div>
    <section class="page-hero">
      <div class="page-hero-bg">
        <img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?w=1920&q=80&auto=format&fit=crop" alt="Hotel">
        <div class="page-hero-shade"></div>
      </div>
      <div class="page-hero-content container">
        <div class="eyebrow">Hotel Bookings</div>
        <h1 style="font-size:clamp(28px,4vw,44px);">Stay close to the Haramain</h1>
        <p style="max-width:560px;">Hand-picked hotels in Makkah and Madinah, sorted by walking distance from the Haram.</p>
      </div>
    </section>

    <div class="container">
      <div class="listing">
        <aside class="filters">
          <h4>City</h4>
          <label class="filter-row"><input type="checkbox" checked> Makkah</label>
          <label class="filter-row"><input type="checkbox" checked> Madinah</label>
          <h4>Star Rating</h4>
          <label class="filter-row"><input type="checkbox" checked> 5-Star</label>
          <label class="filter-row"><input type="checkbox" checked> 4-Star</label>
          <label class="filter-row"><input type="checkbox" checked> 3-Star</label>
          <h4>Distance from Haram</h4>
          <label class="filter-row"><input type="checkbox" checked> Under 200m</label>
          <label class="filter-row"><input type="checkbox" checked> 200m - 500m</label>
          <label class="filter-row"><input type="checkbox" checked> 500m+</label>
        </aside>

        <div>
          <div class="results-bar">
            <span class="mono" style="font-size:13px;color:var(--ink-soft);">{{ filteredHotels.length }} hotels found</span>
            <select v-model="sortBy">
              <option value="popular">Sort: Recommended</option>
              <option value="price-low">Price: Low to High</option>
              <option value="price-high">Price: High to Low</option>
              <option value="rating">Highest Rated</option>
            </select>
          </div>
          <div class="grid grid-2">
            <div class="card" style="flex-direction:row;" v-for="h in filteredHotels" :key="h.slug">
              <div class="card-arch" style="width:180px;height:auto;margin:14px;border-radius:16px;flex:none;">
                <img v-if="h.image_url" :src="h.image_url" :alt="h.name" style="width:100%;height:100%;object-fit:cover;">
                <img v-else src="https://images.unsplash.com/photo-1445019980597-93fa8acb246c?q=80&w=600&auto=format&fit=crop" :alt="h.name">
              </div>
              <div class="card-body">
                <div class="meta">{{ '★'.repeat(h.stars) }} · {{ h.city }}</div>
                <h3>{{ h.name }}</h3>
                <div class="meta">📍 {{ h.distance }}</div>
                <p class="desc">{{ h.desc }}</p>
                <div class="card-foot">
                  <div class="price">PKR {{ parseFloat(h.price).toLocaleString("en-PK") }}<br><small>per night</small></div>
                  <router-link :to="`/hotel-detail?slug=${h.slug}`" class="btn btn-ghost" style="padding:10px 16px;">Details</router-link>
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

const hotels = ref([]);
const sortBy = ref('popular');

onMounted(async () => {
  try {
    const res = await axios.get('/api/hotels');
    hotels.value = res.data;
  } catch(err) {
    console.error(err);
  }
});

const filteredHotels = computed(() => {
  let list = [...hotels.value];
  if (sortBy.value === 'price-low') list.sort((a,b) => a.price - b.price);
  if (sortBy.value === 'price-high') list.sort((a,b) => b.price - a.price);
  if (sortBy.value === 'rating') list.sort((a,b) => b.rating - a.rating);
  return list;
});
</script>
