<template>
  <div>
    <section class="page-hero">
      <div class="page-hero-bg">
        <img src="https://images.pexels.com/photos/35000011/pexels-photo-35000011.jpeg?auto=compress&cs=tinysrgb&w=1920" alt="Kaaba">
        <div class="page-hero-shade"></div>
      </div>
      <div class="page-hero-content container">
        <div class="eyebrow">Umrah Packages</div>
        <h1 style="font-size:clamp(28px,4vw,44px);">{{ isGroup ? 'Umrah Group Departures' : 'Umrah packages for every budget' }}</h1>
        <p style="max-width:560px;">{{ isGroup ? 'Travel together with a guided group — fixed dates, dedicated Muallim, shared coach transfers.' : 'Compare economy, premium and group Umrah packages — airfare, visa, hotel and transfers included.' }}</p>
      </div>
    </section>

    <div class="container">
      <div class="listing">
        <aside class="filters">
          <h4>Package Type</h4>
          <label class="filter-row"><input type="checkbox" v-model="filterIndividual"> Individual</label>
          <label class="filter-row"><input type="checkbox" v-model="filterGroup"> Group Departure</label>
          <h4>Duration</h4>
          <label class="filter-row"><input type="checkbox" checked> 7-10 Nights</label>
          <label class="filter-row"><input type="checkbox" checked> 11-15 Nights</label>
          <h4>Hotel Rating</h4>
          <label class="filter-row"><input type="checkbox" checked> 3-Star</label>
          <label class="filter-row"><input type="checkbox" checked> 4-Star</label>
          <label class="filter-row"><input type="checkbox" checked> 5-Star</label>
          <h4>Budget (per person)</h4>
          <input type="range" min="100" max="5000" v-model="budget" style="width:100%;">
          <div class="form-hint">Up to {{ formatEUR(budget) }}</div>
        </aside>

        <div>
          <div class="results-bar">
            <span class="mono" style="font-size:13px;color:var(--ink-soft);">{{ filteredUmrahs.length }} packages</span>
            <select v-model="sortBy">
              <option value="popular">Sort: Recommended</option>
              <option value="price-low">Price: Low to High</option>
              <option value="price-high">Price: High to Low</option>
              <option value="rating">Highest Rated</option>
            </select>
          </div>
          <div class="grid grid-3">
            <div class="card" v-for="u in filteredUmrahs" :key="u.slug">
              <div class="card-arch"><span class="badge">{{ u.group ? 'Group Departure' : (u.duration_type || u.nights) }}</span>
                <img v-if="u.image_url" :src="u.image_url" :alt="u.title" style="width:100%;height:100%;object-fit:cover;">
                <img v-else src="https://images.unsplash.com/photo-1519817650390-64a93db51149?q=80&w=800&auto=format&fit=crop" :alt="u.title">
              </div>
              <div class="card-body">
                <h3>{{ u.title }}</h3>
                <div class="meta">{{ u.nights }} · {{ u.city }} · ★ {{ u.rating }}</div>
                <p class="desc">{{ u.desc }}</p>
                <div class="card-foot">
                  <div class="price">
                    <template v-if="u.savings > 0">
                      <span style="text-decoration:line-through;color:var(--ink-soft);font-size:14px;margin-right:8px;">{{ formatEUR(u.price) }}</span>
                      <span style="color:var(--gold);font-size:12px;font-weight:bold;margin-right:8px;">You Save</span>
                    </template>
                    {{ formatEUR(u.discounted_price || u.price) }}<br><small>per person</small>
                  </div>
                  <router-link :to="`/umrah/${u.slug}`" class="btn btn-ghost" style="padding:10px 16px;">Details</router-link>
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
import { ref, computed, onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import { formatEUR } from '../../utils/currency.js';

const route = useRoute();
const umrahs = ref([]);
const sortBy = ref('popular');
const budget = ref(5000);

const isGroup = computed(() => route.query.group === '1');
const filterIndividual = ref(!isGroup.value);
const filterGroup = ref(true);

onMounted(async () => {
  try {
    const [pkgsRes, groupsRes] = await Promise.all([
      axios.get('/api/umrah-packages'),
      axios.get('/api/umrah-groups')
    ]);
    
    const pkgs = pkgsRes.data;
    
    const groups = groupsRes.data.map(g => ({
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
    }));

    umrahs.value = [...pkgs, ...groups];
  } catch(err) {
    console.error(err);
  }
});

watch(isGroup, (newVal) => {
  if(newVal) {
    filterIndividual.value = false;
    filterGroup.value = true;
  } else {
    filterIndividual.value = true;
    filterGroup.value = true;
  }
});

const filteredUmrahs = computed(() => {
  let list = umrahs.value.filter(u => {
    if (u.price > budget.value) return false;
    if (u.group && !filterGroup.value) return false;
    if (!u.group && !filterIndividual.value) return false;
    return true;
  });

  if (sortBy.value === 'price-low') list.sort((a,b) => a.price - b.price);
  if (sortBy.value === 'price-high') list.sort((a,b) => b.price - a.price);
  if (sortBy.value === 'rating') list.sort((a,b) => b.rating - a.rating);
  // 'popular' could be default order
  return list;
});
</script>
