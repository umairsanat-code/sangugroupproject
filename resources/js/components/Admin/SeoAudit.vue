<template>
  <div class="seo-manager">
    <div class="admin-panel-card mb-6">
      <h2>SEO Audit & Optimization Scanner</h2>
      <p class="text-slate-500 text-sm mb-6">Enter a page path (e.g. `/`, `/visas`, `/about-us`) to scan its live rendered HTML, identify missing SEO elements, and generate optimized tags.</p>
      
      <div class="form-grid" style="grid-template-columns: 1fr auto;">
        <div class="form-group mb-0">
          <input type="text" v-model="targetPath" placeholder="/visas/dubai" class="w-full" @keyup.enter="runScan">
        </div>
        <button class="btn-primary" @click="runScan" :disabled="scanning">
          <i class="fas" :class="scanning ? 'fa-spinner fa-spin' : 'fa-search'"></i> 
          {{ scanning ? 'Scanning...' : 'Scan Page' }}
        </button>
      </div>
    </div>

    <!-- Hidden Iframe for Scanning -->
    <iframe ref="scanFrame" :src="scanUrl" @load="onIframeLoad" style="width:0; height:0; border:none; position:absolute; visibility:hidden;"></iframe>

    <div v-if="results" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      
      <!-- Score & Issues -->
      <div class="lg:col-span-1">
        <div class="admin-panel-card h-full text-center">
          <h3 class="mb-4">SEO Score</h3>
          <div class="relative w-32 h-32 mx-auto mb-4">
            <svg class="w-full h-full" viewBox="0 0 36 36">
              <path class="text-slate-100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="currentColor" stroke-width="3" />
              <path :class="scoreColor" :stroke-dasharray="`${results.score}, 100`" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="currentColor" stroke-width="3" />
            </svg>
            <div class="absolute inset-0 flex flex-col items-center justify-center">
              <span class="text-3xl font-black">{{ results.score }}</span>
              <span class="text-xs text-slate-400">/ 100</span>
            </div>
          </div>
          
          <div class="text-left mt-6">
            <h4 class="font-bold border-b pb-2 mb-3">Audit Results</h4>
            <ul class="space-y-3 text-sm">
              <li v-for="(check, key) in results.checks" :key="key" class="flex items-start gap-2">
                <i class="fas mt-1" :class="check.passed ? 'fa-check-circle text-green-500' : (check.warning ? 'fa-exclamation-triangle text-amber-500' : 'fa-times-circle text-red-500')"></i>
                <div>
                  <div class="font-semibold">{{ check.label }}</div>
                  <div class="text-slate-500 text-xs">{{ check.msg }}</div>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Optimization Form -->
      <div class="lg:col-span-2">
        <div class="admin-panel-card">
          <div class="flex justify-between items-center mb-6">
            <h2>Optimize Tags for {{ targetPath }}</h2>
            <button class="btn-secondary text-sm" @click="autoOptimize" :disabled="generating">
              <i class="fas" :class="generating ? 'fa-spinner fa-spin' : 'fa-magic'"></i> Auto-Optimize
            </button>
          </div>

          <form @submit.prevent="saveSeo">
            <div class="form-group">
              <label class="flex justify-between">
                <span>H1 Heading</span>
                <span class="text-xs text-slate-400" :class="{'text-red-500': form.h1?.length > 60}">{{ form.h1?.length || 0 }} / 60</span>
              </label>
              <input type="text" v-model="form.h1" placeholder="SanguGroup Travel - Premium Umrah & Visa Services">
            </div>

            <div class="form-group">
              <label class="flex justify-between">
                <span>Page Title (Meta)</span>
                <span class="text-xs text-slate-400" :class="{'text-red-500': form.title?.length > 60}">{{ form.title?.length || 0 }} / 60</span>
              </label>
              <input type="text" v-model="form.title" placeholder="SanguGroup Travel...">
            </div>

            <div class="form-group">
              <label class="flex justify-between">
                <span>Meta Description</span>
                <span class="text-xs text-slate-400" :class="{'text-red-500': form.description?.length > 160}">{{ form.description?.length || 0 }} / 160</span>
              </label>
              <textarea v-model="form.description" rows="3" placeholder="Description for search engines..."></textarea>
            </div>

            <div class="form-group">
              <label>Canonical URL</label>
              <input type="text" v-model="form.canonical_url" placeholder="https://sangugroup.com/...">
            </div>

            <div class="form-group">
              <label>Schema.org Markup (JSON-LD)</label>
              <textarea v-model="form.schema_markup" rows="6" placeholder="{ '@context': 'https://schema.org', ... }" class="font-mono text-xs"></textarea>
            </div>

            <div class="flex gap-4 mt-6">
              <button type="submit" class="btn-primary" :disabled="saving">
                {{ saving ? 'Saving...' : 'Apply Fixes' }}
              </button>
              <span v-if="saveSuccess" class="text-green-600 font-medium self-center"><i class="fas fa-check"></i> Applied to Database!</span>
            </div>
          </form>
        </div>
      </div>
      
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import axios from 'axios';

const targetPath = ref('/');
const scanUrl = ref('');
const scanFrame = ref(null);

const scanning = ref(false);
const generating = ref(false);
const saving = ref(false);
const saveSuccess = ref(false);

const results = ref(null);
const extractedText = ref('');

const form = ref({
  h1: '',
  title: '',
  description: '',
  canonical_url: '',
  schema_markup: ''
});

const scoreColor = computed(() => {
  if (!results.value) return 'text-slate-200';
  const s = results.value.score;
  if (s >= 80) return 'text-green-500';
  if (s >= 50) return 'text-amber-500';
  return 'text-red-500';
});

const runScan = () => {
  if (!targetPath.value) return;
  
  // Clean path
  let path = targetPath.value.trim();
  if (!path.startsWith('/')) path = '/' + path;
  targetPath.value = path;
  
  scanning.value = true;
  results.value = null;
  saveSuccess.value = false;
  
  // Set iframe src to trigger load with cache-busting timestamp
  const sep = path.includes('?') ? '&' : '?';
  scanUrl.value = window.location.origin + path + sep + 't=' + Date.now();
  
  // The actual analysis is now triggered by the @load event on the iframe
};

const onIframeLoad = () => {
  if (!scanning.value) return; // Only process if we are actively scanning
  
  let retries = 0;
  const maxRetries = 10; // Max 5 seconds total (10 * 500ms)

  const checkVueMounted = () => {
    try {
      const doc = scanFrame.value.contentWindow.document;
      const appDiv = doc.querySelector('#app');
      
      if (appDiv && appDiv.children.length > 0) {
        // Vue has mounted content inside #app
        setTimeout(analyzeIframe, 500);
      } else {
        retries++;
        if (retries >= maxRetries) {
          // Timeout reached, just scan whatever is there (maybe it's a 404 or static page)
          analyzeIframe();
        } else {
          // Still empty, keep polling
          setTimeout(checkVueMounted, 500);
        }
      }
    } catch (e) {
      // Ignore cross-origin or access errors during load
    }
  };
  
  checkVueMounted();
};

const analyzeIframe = async () => {
  try {
    const doc = scanFrame.value.contentWindow.document;
    
    // Extract data
    const title = doc.title || '';
    const metaDesc = doc.querySelector('meta[name="description"]')?.content || '';
    const h1s = doc.querySelectorAll('h1');
    const h1Count = h1s.length;
    const schemas = doc.querySelectorAll('script[type="application/ld+json"]');
    const canonical = doc.querySelector('link[rel="canonical"]')?.href || '';
    const bodyText = doc.body.innerText || '';
    const wordCount = bodyText.split(/\s+/).filter(w => w.length > 0).length;
    
    extractedText.value = bodyText;

    // Checks logic
    let score = 100;
    const checks = {
      title: { label: 'Page Title', passed: false, warning: false, msg: '' },
      desc: { label: 'Meta Description', passed: false, warning: false, msg: '' },
      h1: { label: 'H1 Heading', passed: false, warning: false, msg: '' },
      schema: { label: 'Schema Markup', passed: false, warning: false, msg: '' },
      canon: { label: 'Canonical URL', passed: false, warning: false, msg: '' },
      content: { label: 'Content Quality', passed: false, warning: false, msg: '' }
    };

    // Title
    if (!title) {
      score -= 20; checks.title.msg = 'Missing title tag.';
    } else if (title.length < 15) {
      score -= 5; checks.title.warning = true; checks.title.msg = 'Title is quite short (' + title.length + ' chars).';
    } else if (title.length > 65) {
      score -= 5; checks.title.warning = true; checks.title.msg = 'Title is too long (' + title.length + ' chars).';
    } else {
      checks.title.passed = true; checks.title.msg = 'Optimal length (' + title.length + ' chars).';
    }

    // Meta Desc
    if (!metaDesc || metaDesc.trim() === 'SanguGroup - Premium travel and spiritual services. Book flights, luxury hotels, and personalized Umrah packages worldwide.') {
      score -= 20; checks.desc.msg = 'Using default/missing meta description.';
    } else if (metaDesc.length < 50 || metaDesc.length > 165) {
      score -= 10; checks.desc.warning = true; checks.desc.msg = 'Suboptimal length (' + metaDesc.length + ' chars).';
    } else {
      checks.desc.passed = true; checks.desc.msg = 'Optimal length (' + metaDesc.length + ' chars).';
    }

    // H1
    if (h1Count === 0) {
      score -= 15; checks.h1.msg = 'Missing H1 heading.';
    } else if (h1Count > 1) {
      score -= 5; checks.h1.warning = true; checks.h1.msg = 'Multiple H1 tags found (' + h1Count + ').';
    } else {
      checks.h1.passed = true; checks.h1.msg = 'One H1 heading found.';
    }

    // Schema
    if (schemas.length === 0) {
      score -= 10; checks.schema.msg = 'No structured data / schema detected.';
    } else {
      checks.schema.passed = true; checks.schema.msg = schemas.length + ' schema block(s) detected.';
    }

    // Canonical
    if (!canonical) {
      score -= 10; checks.canon.msg = 'No canonical tag.';
    } else {
      checks.canon.passed = true; checks.canon.msg = 'Canonical tag present.';
    }

    // Content
    if (wordCount < 100) {
      score -= 20; checks.content.msg = 'Thin content (~' + wordCount + ' words).';
    } else if (wordCount < 300) {
      score -= 5; checks.content.warning = true; checks.content.msg = 'Slightly thin content (~' + wordCount + ' words).';
    } else {
      checks.content.passed = true; checks.content.msg = 'Good content length (~' + wordCount + ' words).';
    }

    results.value = { score: Math.max(0, score), checks };
    
    // Load existing DB SEO
    loadSeo();

  } catch (e) {
    console.error("Iframe scan failed", e);
    alert("Could not scan page. Is the URL correct?");
  }
  
  scanning.value = false;
};

const loadSeo = async () => {
  try {
    const res = await axios.post('/api/admin/seo/load', { path: targetPath.value });
    if (res.data && res.data.title) {
      form.value = {
        h1: res.data.h1 || '',
        title: res.data.title || '',
        description: res.data.description || '',
        canonical_url: res.data.canonical_url || '',
        schema_markup: res.data.schema_markup ? (typeof res.data.schema_markup === 'string' ? res.data.schema_markup : JSON.stringify(res.data.schema_markup, null, 2)) : ''
      };
    } else {
      // Pre-fill with current frame data if no DB record
      const doc = scanFrame.value.contentWindow.document;
      // Filter out default fallback descriptions
      let detectedDesc = doc.querySelector('meta[name="description"]')?.content || '';
      if (detectedDesc.includes('SanguGroup - Premium travel')) detectedDesc = '';
      const h1El = doc.querySelector('h1');
      
      form.value = {
        h1: h1El ? h1El.textContent.trim() : '',
        title: doc.title || '',
        description: detectedDesc,
        canonical_url: doc.querySelector('link[rel="canonical"]')?.href || (window.location.origin + targetPath.value),
        schema_markup: ''
      };
    }
  } catch (e) {
    console.error("Failed to load SEO", e);
  }
};

const autoOptimize = async () => {
  generating.value = true;
  try {
    const res = await axios.post('/api/admin/seo/generate', {
      path: targetPath.value,
      content: extractedText.value
    });
    
    form.value.h1 = res.data.h1 || '';
    form.value.title = res.data.title;
    form.value.description = res.data.description;
    form.value.canonical_url = res.data.canonical_url;
    form.value.schema_markup = res.data.schema_markup;
    
  } catch (e) {
    console.error("Auto optimize failed", e);
    alert("Failed to generate optimized tags.");
  }
  generating.value = false;
};

const saveSeo = async () => {
  saving.value = true;
  saveSuccess.value = false;
  try {
    await axios.post('/api/admin/seo/save', {
      path: targetPath.value,
      ...form.value
    });
    saveSuccess.value = true;
    
    // Automatically rescan to reflect the newly saved changes in the score!
    setTimeout(() => {
      saveSuccess.value = false;
      runScan();
    }, 1500);
    
  } catch (e) {
    console.error("Failed to save SEO", e);
    alert("Error saving SEO configuration.");
  }
  saving.value = false;
};
</script>

<style scoped>
.seo-manager input, .seo-manager textarea {
  width: 100%;
  padding: 12px 16px;
  border: 1px solid var(--line);
  border-radius: 8px;
  font-family: inherit;
  font-size: 14px;
}
.seo-manager label {
  display: block;
  font-weight: 700;
  margin-bottom: 6px;
  font-size: 13px;
  color: var(--primary-dark);
}
</style>
