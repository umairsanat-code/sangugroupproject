<template>
  <div>
    <section class="detail-hero" style="background-image:url('https://images.unsplash.com/photo-1504711434969-e33886168d9c?q=80&w=2000&auto=format&fit=crop');">
      <div class="container" style="position:relative;z-index:2;">
        <div class="breadcrumb">
          <router-link to="/">Home</router-link> / <span>Blog</span>
        </div>
        <h1 style="color:#fff;font-size:clamp(32px,5vw,48px);margin-bottom:8px;text-shadow:0 4px 12px rgba(0,0,0,0.6);">From the Journal</h1>
        <p style="color:rgba(255,255,255,.9);margin:0;font-size:18px;text-shadow:0 4px 12px rgba(0,0,0,0.6);">Guides, tips and stories for your next trip</p>
      </div>
    </section>

    <section style="padding:60px 0;">
      <div class="container">
        <div v-if="loading" style="text-align:center;padding:60px 0;color:var(--ink-soft);">Loading articles...</div>
        <div v-else-if="blogs.length === 0" style="text-align:center;padding:60px 0;color:var(--ink-soft);">
          <p>No articles published yet. Check back soon!</p>
        </div>
        <div v-else>
          <!-- Featured article -->
          <router-link v-if="blogs[0]" :to="`/blog/${blogs[0].slug}`" class="blog-featured">
            <div class="blog-featured-img">
              <img :src="blogs[0].image_url || 'https://images.unsplash.com/photo-1504711434969-e33886168d9c?q=80&w=800&auto=format&fit=crop'" :alt="blogs[0].title" width="800" height="500" fetchpriority="high">
            </div>
            <div class="blog-featured-body">
              <span class="tag" v-if="blogs[0].tag">{{ blogs[0].tag }}</span>
              <h2>{{ blogs[0].title }}</h2>
              <p>{{ blogs[0].excerpt }}</p>
              <div class="blog-meta">
                <span v-if="blogs[0].author">By {{ blogs[0].author }}</span>
                <span v-if="blogs[0].published_at">{{ formatDate(blogs[0].published_at) }}</span>
              </div>
              <span class="btn btn-ghost" style="margin-top:16px;padding:10px 20px;">Read Article →</span>
            </div>
          </router-link>

          <!-- Grid of articles -->
          <div class="grid grid-3" style="margin-top:40px;">
            <router-link
              v-for="blog in blogs.slice(1)"
              :key="blog.id"
              :to="`/blog/${blog.slug}`"
              class="guide-card"
            >
              <div class="guide-thumb">
                <img :src="blog.image_url || 'https://images.unsplash.com/photo-1504711434969-e33886168d9c?q=80&w=800&auto=format&fit=crop'" :alt="blog.title" width="400" height="250" loading="lazy">
              </div>
              <div class="guide-body">
                <span class="tag" v-if="blog.tag">{{ blog.tag }}</span>
                <h3>{{ blog.title }}</h3>
                <p>{{ blog.excerpt }}</p>
                <div class="blog-meta" style="margin-top:auto;">
                  <span v-if="blog.author">{{ blog.author }}</span>
                  <span v-if="blog.published_at">{{ formatDate(blog.published_at) }}</span>
                </div>
              </div>
            </router-link>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const blogs = ref([]);
const loading = ref(true);

const formatDate = (d) => {
  if (!d) return '';
  return new Date(d).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
};

onMounted(async () => {
  document.title = 'Travel Blog | Sangu Travel & Tours';
  try {
    const res = await axios.get('/api/blogs');
    blogs.value = res.data;
  } catch (err) {
    console.error('Failed to load blogs', err);
  } finally {
    loading.value = false;
  }
});
</script>

<style scoped>
.detail-hero {
  position: relative;
  padding: 80px 0;
  background-size: cover;
  background-position: center;
  min-height: 40vh;
  display: flex;
  align-items: center;
}
.blog-featured {
  display: grid;
  grid-template-columns: 1.2fr 1fr;
  gap: 40px;
  background: #fff;
  border: 1px solid var(--line);
  border-radius: 20px;
  overflow: hidden;
  text-decoration: none;
  color: inherit;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.blog-featured:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 32px rgba(0,0,0,0.1);
}
.blog-featured-img {
  height: 100%;
  min-height: 300px;
  overflow: hidden;
}
.blog-featured-img img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.4s ease;
}
.blog-featured:hover .blog-featured-img img { transform: scale(1.05); }
.blog-featured-body {
  padding: 32px;
  display: flex;
  flex-direction: column;
  justify-content: center;
}
.blog-featured-body .tag {
  display: inline-block;
  background: var(--bg-raised);
  color: var(--teal-dark);
  border-radius: 20px;
  padding: 4px 14px;
  font-size: 12px;
  font-weight: 600;
  letter-spacing: 0.5px;
  width: fit-content;
  margin-bottom: 12px;
}
.blog-featured-body h2 {
  font-size: 28px;
  line-height: 1.3;
  color: var(--ink-dark);
  margin-bottom: 12px;
}
.blog-featured-body p {
  font-size: 15px;
  color: var(--ink-soft);
  line-height: 1.7;
}
.blog-meta {
  display: flex;
  gap: 16px;
  font-size: 13px;
  color: var(--ink-soft);
  margin-top: 12px;
}
.guide-card {
  background: #fff;
  border: 1px solid var(--line);
  border-radius: 20px;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  text-decoration: none;
  color: inherit;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.guide-card:hover { transform: translateY(-4px); box-shadow: 0 8px 28px rgba(0,0,0,0.09); }
.guide-thumb { height: 180px; overflow: hidden; }
.guide-thumb img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s ease; }
.guide-card:hover .guide-thumb img { transform: scale(1.05); }
.guide-body { padding: 20px; display: flex; flex-direction: column; gap: 10px; flex: 1; }
.guide-body .tag { display: inline-block; background: var(--bg-raised); color: var(--teal-dark); border-radius: 20px; padding: 3px 10px; font-size: 11px; font-weight: 600; letter-spacing: 0.5px; width: fit-content; }
.guide-body h3 { font-size: 16px; line-height: 1.4; color: var(--ink-dark); }
.guide-body p { font-size: 13px; color: var(--ink-soft); line-height: 1.6; }

@media (max-width: 768px) {
  .blog-featured { grid-template-columns: 1fr; }
  .blog-featured-img { min-height: 200px; }
}
</style>
