<template>
  <div v-if="blog">
    <section class="detail-hero" :style="heroStyle">
      <div class="container" style="position:relative;z-index:2;">
        <div class="breadcrumb">
          <router-link to="/">Home</router-link> / <router-link to="/blog">Blog</router-link> / <span>{{ blog.title }}</span>
        </div>
        <div style="max-width:700px;">
          <span v-if="blog.tag" class="tag-badge">{{ blog.tag }}</span>
          <h1 style="color:#fff;font-size:clamp(28px,4vw,42px);margin:12px 0 16px;text-shadow:0 4px 12px rgba(0,0,0,0.6);">{{ blog.title }}</h1>
          <div class="hero-meta">
            <span v-if="blog.author">By {{ blog.author }}</span>
            <span v-if="blog.published_at">{{ formatDate(blog.published_at) }}</span>
          </div>
        </div>
      </div>
    </section>

    <section style="padding:40px 0 80px;">
      <div class="container">
        <div class="blog-layout">
          <article class="blog-article">
            <p v-if="blog.excerpt" class="blog-excerpt">{{ blog.excerpt }}</p>
            <div class="blog-content" v-html="blog.content"></div>
          </article>

          <aside class="blog-sidebar">
            <div class="sidebar-card">
              <h3>Latest Articles</h3>
              <div v-for="b in relatedBlogs" :key="b.id" class="related-item">
                <router-link :to="`/blog/${b.slug}`">
                  <h4>{{ b.title }}</h4>
                  <span class="related-date">{{ formatDate(b.published_at) }}</span>
                </router-link>
              </div>
              <div v-if="relatedBlogs.length === 0" style="color:var(--ink-soft);font-size:13px;">No other articles yet.</div>
            </div>
          </aside>
        </div>
      </div>
    </section>
  </div>
  <div v-else class="container" style="padding: 100px 0; text-align: center;">
    <h2>Loading article...</h2>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const blog = ref(null);
const relatedBlogs = ref([]);

const heroStyle = computed(() => {
  if (blog.value && blog.value.image_url) {
    return { backgroundImage: `url(${blog.value.image_url})` };
  }
  return { backgroundImage: `url(https://images.unsplash.com/photo-1504711434969-e33886168d9c?q=80&w=2000&auto=format&fit=crop)` };
});

const formatDate = (d) => {
  if (!d) return '';
  return new Date(d).toLocaleDateString('en-GB', { day: 'numeric', month: 'long', year: 'numeric' });
};

const setMeta = (attr, key, content) => {
  if (!content) return;
  let el = document.head.querySelector(`meta[${attr}="${key}"]`);
  if (!el) {
    el = document.createElement('meta');
    el.setAttribute(attr, key);
    document.head.appendChild(el);
  }
  el.setAttribute('content', content);
};

const setCanonical = (url) => {
  if (!url) return;
  let el = document.head.querySelector('link[rel="canonical"]');
  if (!el) {
    el = document.createElement('link');
    el.setAttribute('rel', 'canonical');
    document.head.appendChild(el);
  }
  el.setAttribute('href', url);
};

const applySeo = (b) => {
  const brand = 'Sangu Travel & Tours';
  const title = `${b.title} | ${brand} Blog`;
  const description = b.excerpt || '';
  document.title = title;
  setMeta('name', 'description', description);
  setMeta('property', 'og:title', title);
  setMeta('property', 'og:description', description);
  setMeta('property', 'og:type', 'article');
  if (b.image_url) setMeta('property', 'og:image', b.image_url);
  setMeta('name', 'twitter:title', title);
  setMeta('name', 'twitter:description', description);
  const canonicalBase = window.location.origin.replace('://www.', '://');
  setCanonical(`${canonicalBase}/blog/${b.slug}`);
};

onMounted(async () => {
  const slug = route.params.slug;
  try {
    const res = await axios.get('/api/blogs/' + slug);
    blog.value = res.data;
    applySeo(blog.value);

    const allRes = await axios.get('/api/blogs');
    relatedBlogs.value = allRes.data.filter(b => b.id !== blog.value.id).slice(0, 5);
  } catch (err) {
    console.error('Blog not found', err);
  }
});
</script>

<style scoped>
.detail-hero {
  position: relative;
  padding: 80px 0;
  background-size: cover;
  background-position: center;
  min-height: 45vh;
  display: flex;
  align-items: flex-end;
}
.tag-badge {
  display: inline-block;
  background: var(--gold);
  color: var(--teal-dark);
  border-radius: 20px;
  padding: 4px 14px;
  font-size: 12px;
  font-weight: 700;
  letter-spacing: 0.5px;
}
.hero-meta {
  display: flex;
  gap: 16px;
  font-size: 14px;
  color: rgba(255,255,255,.85);
  text-shadow: 0 2px 8px rgba(0,0,0,0.4);
}

.blog-layout {
  display: grid;
  grid-template-columns: 1fr 320px;
  gap: 48px;
  align-items: start;
}
.blog-article { min-width: 0; }
.blog-excerpt {
  font-size: 18px;
  line-height: 1.7;
  color: var(--ink-soft);
  margin-bottom: 32px;
  padding-bottom: 24px;
  border-bottom: 1px solid var(--line);
}
.blog-content {
  font-size: 16px;
  line-height: 1.8;
  color: var(--ink);
}
.blog-content :deep(h2) {
  font-size: 24px;
  color: var(--teal-dark);
  margin: 32px 0 16px;
  padding-bottom: 8px;
  border-bottom: 2px solid var(--line);
}
.blog-content :deep(h3) {
  font-size: 20px;
  color: var(--teal-dark);
  margin: 24px 0 12px;
}
.blog-content :deep(ul),
.blog-content :deep(ol) {
  padding-left: 24px;
  margin: 12px 0;
}
.blog-content :deep(li) {
  margin-bottom: 8px;
  line-height: 1.7;
}
.blog-content :deep(img) {
  max-width: 100%;
  border-radius: 12px;
  margin: 20px 0;
}
.blog-content :deep(blockquote) {
  border-left: 4px solid var(--gold);
  padding: 16px 20px;
  background: var(--bg-raised);
  border-radius: 4px;
  margin: 20px 0;
  font-style: italic;
  color: var(--ink-soft);
}

.blog-sidebar { position: sticky; top: 100px; }
.sidebar-card {
  background: #fff;
  border: 1px solid var(--line);
  border-radius: 16px;
  padding: 24px;
}
.sidebar-card h3 {
  font-size: 18px;
  color: var(--teal-dark);
  margin-bottom: 16px;
  padding-bottom: 8px;
  border-bottom: 2px solid var(--line);
}
.related-item {
  padding: 12px 0;
  border-bottom: 1px solid var(--line);
}
.related-item:last-child { border-bottom: none; }
.related-item a {
  text-decoration: none;
  color: inherit;
}
.related-item h4 {
  font-size: 14px;
  color: var(--ink-dark);
  margin: 0 0 4px;
  line-height: 1.4;
  transition: color 0.2s;
}
.related-item a:hover h4 { color: var(--teal); }
.related-date { font-size: 12px; color: var(--ink-soft); }

@media (max-width: 768px) {
  .blog-layout { grid-template-columns: 1fr; }
  .blog-sidebar { position: static; }
}
</style>
