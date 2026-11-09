<template>
  <div>
    <!-- Hero -->
    <section class="gallery-hero">
      <div class="gallery-hero-bg"></div>
      <div class="container gallery-hero-content">
        <nav class="gallery-breadcrumb">
          <router-link to="/">Home</router-link>
          <span>·</span>
          <span>Gallery</span>
        </nav>
        <h1>Our <em>Gallery</em></h1>
        <p class="lead">Explore moments with Sangu Travel &amp; Tours</p>
      </div>
    </section>

    <!-- Gallery Grid -->
    <section class="gallery-main">
      <div class="container">
        <div v-if="loading" class="gallery-loading">Loading gallery...</div>

        <template v-else-if="items.length">
          <div class="gallery-filters">
            <button
              v-for="cat in categories"
              :key="cat.key"
              class="gallery-filter-btn"
              :class="{ active: activeCategory === cat.key }"
              @click="activeCategory = cat.key"
            >{{ cat.label }}</button>
          </div>

          <div class="gallery-grid">
            <div
              v-for="(img, i) in filteredItems"
              :key="img.id"
              class="gallery-item"
              @click="openLightbox(i)"
            >
              <img :src="img.image_url" :alt="img.title || 'Gallery image'">
              <div class="gallery-item-overlay">
                <span v-if="img.category" class="tag">{{ img.category }}</span>
                <h4>{{ img.title || 'Gallery' }}</h4>
              </div>
            </div>
          </div>

          <div v-if="filteredItems.length === 0" class="gallery-empty">
            No images in this category yet.
          </div>
        </template>

        <div v-else class="gallery-empty">
          Gallery coming soon. Check back for photos from our tours and Umrah journeys.
        </div>
      </div>
    </section>

    <!-- Lightbox -->
    <transition name="lb-fade">
      <div v-if="lbOpen" class="lightbox-overlay" @click.self="closeLightbox">
        <button class="lightbox-close" @click="closeLightbox" aria-label="Close">×</button>
        <button v-if="filteredItems.length > 1" class="lightbox-nav lightbox-prev" @click="lbPrev" aria-label="Previous">‹</button>
        <button v-if="filteredItems.length > 1" class="lightbox-nav lightbox-next" @click="lbNext" aria-label="Next">›</button>
        <div class="lightbox-content">
          <img :src="filteredItems[lbIdx]?.image_url" :alt="filteredItems[lbIdx]?.title" class="lightbox-image">
          <div class="lightbox-caption">
            <h3>{{ filteredItems[lbIdx]?.title }}</h3>
            <p>{{ lbIdx + 1 }} / {{ filteredItems.length }}</p>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import axios from 'axios';

const items = ref([]);
const loading = ref(true);
const activeCategory = ref('all');
const lbOpen = ref(false);
const lbIdx = ref(0);

const categories = computed(() => {
  const cats = new Set(items.value.map(i => i.category).filter(Boolean));
  const list = [{ key: 'all', label: 'All' }];
  cats.forEach(c => list.push({ key: c.toLowerCase(), label: c }));
  return list;
});

const filteredItems = computed(() => {
  if (activeCategory.value === 'all') return items.value;
  return items.value.filter(i => (i.category || '').toLowerCase() === activeCategory.value);
});

const openLightbox = (index) => {
  lbIdx.value = index;
  lbOpen.value = true;
  document.body.style.overflow = 'hidden';
};

const closeLightbox = () => {
  lbOpen.value = false;
  document.body.style.overflow = '';
};

const lbPrev = () => {
  lbIdx.value = (lbIdx.value - 1 + filteredItems.value.length) % filteredItems.value.length;
};

const lbNext = () => {
  lbIdx.value = (lbIdx.value + 1) % filteredItems.value.length;
};

const onKeydown = (e) => {
  if (!lbOpen.value) return;
  if (e.key === 'Escape') closeLightbox();
  if (e.key === 'ArrowLeft') lbPrev();
  if (e.key === 'ArrowRight') lbNext();
};

onMounted(async () => {
  window.addEventListener('keydown', onKeydown);
  try {
    const res = await axios.get('/api/gallery');
    items.value = res.data;
  } catch (err) {
    console.error('Failed to load gallery', err);
  } finally {
    loading.value = false;
  }
});

onUnmounted(() => {
  window.removeEventListener('keydown', onKeydown);
  document.body.style.overflow = '';
});
</script>

<style scoped>
.gallery-hero {
  position: relative;
  padding: 100px 0 80px;
  color: #fff;
  overflow: hidden;
}
.gallery-hero-bg {
  position: absolute;
  inset: 0;
  background-image: url('https://images.unsplash.com/photo-1591604129939-f1efa4d9f7fa?q=80&w=2000&auto=format&fit=crop');
  background-size: cover;
  background-position: center;
  z-index: 1;
}
.gallery-hero-content {
  position: relative;
  z-index: 3;
  text-align: center;
}
.gallery-breadcrumb {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  font-family: 'IBM Plex Mono', monospace;
  font-size: 11px;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: rgba(255,255,255,0.6);
  margin-bottom: 20px;
}
.gallery-breadcrumb a { color: rgba(255,255,255,0.6); transition: color 0.2s; }
.gallery-breadcrumb a:hover { color: var(--gold); }
.gallery-hero h1 {
  font-family: 'Fraunces', serif;
  font-size: clamp(36px, 5vw, 56px);
  color: #fff;
  margin-bottom: 12px;
  text-shadow: 0 4px 12px rgba(0,0,0,0.6);
}
.gallery-hero h1 em { font-style: normal; color: var(--gold); }
.gallery-hero .lead {
  font-size: 17px;
  color: rgba(255,255,255,0.82);
  max-width: 520px;
  margin: 0 auto;
  text-shadow: 0 4px 12px rgba(0,0,0,0.6);
}

.gallery-main { padding: 64px 0 96px; }
.gallery-loading, .gallery-empty {
  text-align: center;
  padding: 60px 20px;
  color: var(--ink-soft);
  font-size: 16px;
}

.gallery-filters {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 10px;
  margin-bottom: 48px;
}
.gallery-filter-btn {
  padding: 12px 24px;
  border-radius: 999px;
  border: 1px solid var(--line);
  background: #fff;
  color: var(--ink-soft);
  font-size: 13px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s ease;
  font-family: 'Plus Jakarta Sans', sans-serif;
}
.gallery-filter-btn:hover {
  border-color: var(--teal);
  color: var(--teal);
}
.gallery-filter-btn.active {
  background: var(--teal);
  border-color: var(--teal);
  color: #fff;
  box-shadow: 0 4px 16px rgba(14,75,68,0.25);
}

.gallery-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 24px;
}
.gallery-item {
  position: relative;
  border-radius: 20px;
  overflow: hidden;
  cursor: pointer;
  aspect-ratio: 4/3;
  box-shadow: 0 2px 16px rgba(0,0,0,0.06);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.gallery-item:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 32px rgba(0,0,0,0.12);
}
.gallery-item img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s ease;
}
.gallery-item:hover img { transform: scale(1.06); }
.gallery-item-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(to top, rgba(14,75,68,0.88) 0%, transparent 55%);
  display: flex;
  flex-direction: column;
  justify-content: flex-end;
  padding: 20px;
  opacity: 0;
  transition: opacity 0.3s ease;
}
.gallery-item:hover .gallery-item-overlay { opacity: 1; }
.gallery-item-overlay .tag {
  display: inline-block;
  background: var(--gold);
  color: var(--teal-dark);
  border-radius: 20px;
  padding: 3px 12px;
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  width: fit-content;
  margin-bottom: 8px;
}
.gallery-item-overlay h4 {
  color: #fff;
  font-size: 16px;
  margin: 0;
}

@media (max-width: 992px) {
  .gallery-grid { grid-template-columns: repeat(2, 1fr); gap: 18px; }
}
@media (max-width: 560px) {
  .gallery-grid { grid-template-columns: 1fr; }
  .gallery-filters { gap: 8px; }
  .gallery-filter-btn { padding: 10px 18px; font-size: 12px; }
}

/* Lightbox */
.lightbox-overlay {
  position: fixed;
  inset: 0;
  z-index: 9999;
  background: rgba(10, 55, 51, 0.94);
  backdrop-filter: blur(8px);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 24px;
}
.lightbox-close {
  position: absolute;
  top: 20px;
  right: 24px;
  width: 48px;
  height: 48px;
  border-radius: 50%;
  border: none;
  background: rgba(255,255,255,0.12);
  color: #fff;
  font-size: 28px;
  cursor: pointer;
  line-height: 1;
  transition: background 0.2s;
  z-index: 10;
}
.lightbox-close:hover { background: rgba(255,255,255,0.22); }
.lightbox-nav {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  width: 52px;
  height: 52px;
  border-radius: 50%;
  border: none;
  background: rgba(255,255,255,0.12);
  color: #fff;
  font-size: 28px;
  cursor: pointer;
  transition: background 0.2s;
  line-height: 1;
  z-index: 10;
}
.lightbox-nav:hover { background: rgba(255,255,255,0.22); }
.lightbox-prev { left: 20px; }
.lightbox-next { right: 20px; }
.lightbox-content {
  max-width: 900px;
  width: 100%;
  text-align: center;
}
.lightbox-image {
  max-height: 75vh;
  width: auto;
  max-width: 100%;
  margin: 0 auto;
  border-radius: 12px;
  object-fit: contain;
}
.lightbox-caption h3 {
  color: #fff;
  font-size: 20px;
  margin-top: 16px;
}
.lightbox-caption p {
  color: rgba(255,255,255,0.55);
  font-size: 14px;
}
.lb-fade-enter-active, .lb-fade-leave-active { transition: opacity 0.35s ease; }
.lb-fade-enter-from, .lb-fade-leave-to { opacity: 0; }
</style>
