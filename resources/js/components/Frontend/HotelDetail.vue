<template>
  <div v-if="h">
    <section class="detail-hero">
      <div class="container">
        <div class="breadcrumb"><router-link to="/">Home</router-link> / <router-link to="/hotels">Hotels</router-link> / <span>{{ h.name }}</span></div>
        <h1 style="color:#fff;font-size:clamp(26px,4vw,38px);">{{ h.name }}</h1>
        <p style="color:rgba(255,255,255,.78);margin:0;">{{ '★'.repeat(h.stars) }} · {{ h.city }} · {{ h.distance }}</p>
      </div>
    </section>

    <div class="container">
      <div class="detail-grid">
        <div>
          <div class="detail-gallery">
            <img v-if="h.image_url" :src="h.image_url" :alt="h.name" style="width:100%;height:400px;object-fit:cover;border-radius:24px;">
            <img v-else src="https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?q=80&w=1200&auto=format&fit=crop" alt="">
          </div>

          <div class="detail-tabs">
            <button :class="{ active: activeTab === 'overview' }" @click="activeTab = 'overview'">Overview</button>
            <button :class="{ active: activeTab === 'amenities' }" @click="activeTab = 'amenities'">Amenities</button>
            <button :class="{ active: activeTab === 'policy' }" @click="activeTab = 'policy'">Policies</button>
          </div>

          <div class="detail-panel" :class="{ active: activeTab === 'overview' }">
            <p style="font-size:15.5px;">{{ h.desc }}</p>
            <ul class="info-list">
              <li><b>City</b>{{ h.city }}</li>
              <li><b>Star Rating</b>{{ '★'.repeat(h.stars) }}</li>
              <li><b>Distance</b>{{ h.distance }}</li>
              <li><b>Rating</b>★ {{ h.rating }}</li>
              <li><b>Price / Night</b>PKR {{ h.price.toLocaleString("en-PK") }}</li>
            </ul>
          </div>
          
          <div class="detail-panel" :class="{ active: activeTab === 'amenities' }">
            <ul style="font-size:14.5px;color:var(--ink-soft);line-height:2;">
              <li v-for="am in h.amenities" :key="am">✓ {{ am }}</li>
            </ul>
          </div>
          
          <div class="detail-panel" :class="{ active: activeTab === 'policy' }">
            <div style="margin-bottom:16px;"><h3 style="font-size:16px;">Check-in / Check-out</h3><p style="font-size:14.5px;">Check-in from 2:00 PM, check-out by 12:00 PM. Early check-in subject to availability.</p></div>
            <div><h3 style="font-size:16px;">Cancellation</h3><p style="font-size:14.5px;">Free cancellation up to 7 days before check-in. Later cancellations are charged one night's stay.</p></div>
          </div>
        </div>

        <div class="booking-card">
          <div class="eyebrow">Per Night</div>
          <div class="price">PKR {{ h.price.toLocaleString("en-PK") }}</div>
          <div class="divider"></div>
          <div class="summary-row"><span>City</span><span>{{ h.city }}</span></div>
          <div class="summary-row"><span>Distance</span><span>{{ h.distance }}</span></div>
          <div class="summary-row"><span>Rating</span><span>★ {{ h.rating }}</span></div>
          <div class="divider"></div>
          <router-link :to="`/book?type=hotel&slug=${h.slug}`" class="btn btn-primary btn-block">Book This Hotel</router-link>
          <p class="form-hint" style="text-align:center;margin-top:12px;">Free cancellation available</p>
        </div>
      </div>
    </div>
  </div>
  <div v-else class="container" style="padding: 100px 0; text-align: center;">
      <h2>Hotel not found.</h2>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const h = ref(null);
const activeTab = ref('overview');

onMounted(async () => {
  const slug = route.query.slug || 'swissotel-al-maqam-makkah';
  try {
    const res = await axios.get('/api/hotels/' + slug);
    h.value = res.data;
  } catch(err) {
    console.error(err);
  }
});
</script>
