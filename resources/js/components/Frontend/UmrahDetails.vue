<template>
  <div v-if="u">
    <section class="detail-hero">
      <div class="container">
        <div class="breadcrumb"><router-link to="/">Home</router-link> / <router-link to="/umrah">Umrah</router-link> / <span>{{ u.title }}</span></div>
        <h1 style="color:#fff;font-size:clamp(26px,4vw,38px);">{{ u.title }}</h1>
        <p style="color:rgba(255,255,255,.78);margin:0;">{{ u.nights }} · {{ u.city }} <span v-if="u.group">· Group Departure</span></p>
      </div>
    </section>

    <div class="container">
      <div class="detail-grid">
        <div>
          <div class="detail-gallery">
            <img v-if="u.image_url" :src="u.image_url" :alt="u.title" style="width:100%;height:400px;object-fit:cover;border-radius:24px;">
            <img v-else src="https://images.pexels.com/photos/35000011/pexels-photo-35000011.jpeg?auto=compress&cs=tinysrgb&w=1200" alt="">
          </div>

          <div class="detail-tabs">
            <button :class="{ active: activeTab === 'overview' }" @click="activeTab = 'overview'">Overview</button>
            <button :class="{ active: activeTab === 'includes' }" @click="activeTab = 'includes'">What's Included</button>
            <button :class="{ active: activeTab === 'itinerary' }" @click="activeTab = 'itinerary'">Itinerary</button>
          </div>

          <div class="detail-panel" :class="{ active: activeTab === 'overview' }">
            <p style="font-size:15.5px;">{{ u.desc }}</p>
            <ul class="info-list">
              <li><b>Duration</b>{{ u.nights }}</li>
              <li><b>Cities</b>{{ u.city }}</li>
              <li><b>Hotel Tier</b>{{ u.hotel }}</li>
              <li><b>Group Type</b>{{ u.group ? 'Guided Group' : 'Individual' }}</li>
              <li><b>Rating</b>★ {{ u.rating }}</li>
              <li><b>Price</b>PKR {{ u.price.toLocaleString("en-PK") }}</li>
            </ul>
          </div>
          
          <div class="detail-panel" :class="{ active: activeTab === 'includes' }">
            <ul style="font-size:14.5px;color:var(--ink-soft);line-height:2;">
              <li v-for="inc in u.includes" :key="inc">✓ {{ inc }}</li>
            </ul>
          </div>
          
          <div class="detail-panel" :class="{ active: activeTab === 'itinerary' }">
            <div style="margin-bottom:16px;"><h3 style="font-size:16px;">Day 1-2 — Arrival in Madinah</h3><p style="font-size:14.5px;">Arrival, hotel check-in, and orientation. Free time for Ziyarat and prayers at Masjid an-Nabawi.</p></div>
            <div style="margin-bottom:16px;"><h3 style="font-size:16px;">Day 3-4 — Madinah Ziyarat Tour</h3><p style="font-size:14.5px;">Guided tour of Quba Mosque, Mount Uhud, and the Seven Mosques.</p></div>
            <div style="margin-bottom:16px;"><h3 style="font-size:16px;">Transfer to Makkah</h3><p style="font-size:14.5px;">Private/shared coach transfer to Makkah with a stop at Bir Ali for Ihram.</p></div>
            <div><h3 style="font-size:16px;">Remaining Nights — Makkah</h3><p style="font-size:14.5px;">Umrah rituals, free time for worship, and an optional Makkah Ziyarat tour before departure.</p></div>
          </div>
        </div>

        <div class="booking-card">
          <div class="eyebrow">Package Price</div>
          <div class="price">PKR {{ u.price.toLocaleString("en-PK") }}</div>
          <div class="divider"></div>
          <div class="summary-row"><span>Duration</span><span>{{ u.nights }}</span></div>
          <div class="summary-row"><span>Hotel</span><span>{{ u.hotel }}</span></div>
          <div class="summary-row"><span>Rating</span><span>★ {{ u.rating }}</span></div>
          <div class="divider"></div>
          <router-link :to="`/book?type=umrah&slug=${u.slug}`" class="btn btn-primary btn-block">Book This Package</router-link>
          <p class="form-hint" style="text-align:center;margin-top:12px;" v-if="u.group">Group departure seats are limited</p>
        </div>
      </div>
    </div>
  </div>
  <div v-else class="container" style="padding: 100px 0; text-align: center;">
      <h2>Package not found.</h2>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const u = ref(null);
const activeTab = ref('overview');

onMounted(async () => {
  const slug = route.params.id || 'umrah-economy-10';
  try {
    if (slug.startsWith('group-')) {
        const id = slug.replace('group-', '');
        const res = await axios.get('/api/umrah-groups/' + id);
        const g = res.data;
        u.value = {
            id: 'group-' + g.id,
            slug: 'group-' + g.id,
            title: g.name,
            group: true,
            is_real_group: true,
            price: g.price,
            city: 'Makkah & Madinah',
            rating: 5,
            nights: (g.departure_date || 'TBD') + ' to ' + (g.return_date || 'TBD'),
            desc: g.group_details,
            image_url: g.image_url,
            hotel: g.hotel,
        };
    } else {
        const res = await axios.get('/api/umrah-packages/' + slug);
        u.value = res.data;
    }
  } catch(err) {
    console.error(err);
  }
});
</script>
