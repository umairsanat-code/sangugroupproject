<template>
  <div v-if="v">
    <section class="detail-hero" :style="heroStyle">
      <div class="container" style="position:relative;z-index:2;">
        <div class="breadcrumb">
          <router-link to="/">Home</router-link> / <router-link to="/visa">Visas</router-link> / <span>{{ v.country }}</span>
        </div>
        <div style="display:flex;align-items:center;gap:16px;">
          <span v-if="!v.image_url" style="font-size:44px;">{{ v.flag }}</span>
          <div>
            <h1 style="color:#fff;font-size:clamp(32px,5vw,48px);margin-bottom:8px;text-shadow:0 4px 12px rgba(0,0,0,0.6);">{{ v.country }} Visa</h1>
            <p style="color:rgba(255,255,255,.9);margin:0;font-size:18px;text-shadow:0 4px 12px rgba(0,0,0,0.6);">{{ v.title || v.type }} · Processed in {{ v.processing }}</p>
          </div>
        </div>
      </div>
    </section>

    <div class="container" style="padding: 40px 0;">
      <div class="detail-grid">
        <!-- Main Content Column -->
        <div class="visa-content-sections">
          
          <div class="section-block" id="overview">
            <h2>Visa Overview</h2>
            <p>{{ v.desc }}</p>
            <ul class="info-list" style="margin-top:16px;">
              <li><b>Visa Type</b>{{ v.type }}</li>
              <li><b>Stay Duration</b>{{ v.duration }}</li>
              <li><b>Validity</b>{{ v.validity }}</li>
              <li><b>Entry Type</b>{{ v.entry }}</li>
            </ul>
          </div>

          <div class="section-block" id="requirements" v-if="v.requirements && v.requirements.length">
            <h2>Visa Requirements</h2>
            <ul class="check-list">
              <li v-for="(req, idx) in v.requirements" :key="idx">{{ req }}</li>
            </ul>
          </div>

          <div class="section-block" id="documents" v-if="v.documents_required && v.documents_required.length">
            <h2>Required Documents</h2>
            <ul class="check-list">
              <li v-for="(doc, idx) in v.documents_required" :key="idx">{{ doc }}</li>
            </ul>
          </div>

          <div class="section-block" id="processing">
            <h2>Processing Time &amp; Fee</h2>
            <div class="fee-box">
              <div class="fee-row">
                <span>Standard Processing Time</span>
                <b>{{ v.processing }}</b>
              </div>
              <div class="fee-row total">
                <span>Total Visa Fee (per applicant)</span>
                <b>{{ formatEUR(v.discounted_price || v.price) }}</b>
              </div>
            </div>
          </div>

          <div class="section-block" id="important" v-if="v.important_info">
            <h2>Important Information</h2>
            <div class="important-box">
              <p>{{ v.important_info }}</p>
            </div>
          </div>
        </div>

        <!-- Sticky Sidebar / Application Form -->
        <div class="booking-sidebar">
          <div class="booking-card sticky" id="apply">
            <h3 style="margin-top:0;">Apply Now</h3>
            <p class="form-hint" style="margin-bottom:20px;">Complete our quick multi-step application form, then pay securely via Revolut bank transfer.</p>
            
            <div>
              <div v-if="v.documents_required && v.documents_required.length" style="margin-bottom:16px;">
                <h4 style="margin-bottom:8px;font-size:15px;">Required Documents</h4>
                <ul class="check-list" style="font-size:13px;">
                  <li v-for="(doc, idx) in v.documents_required" :key="idx">{{ doc }}</li>
                </ul>
              </div>

              <div class="divider"></div>
              <div class="summary-row" style="margin-bottom:8px;">
                <span>Total per applicant</span>
                <span>{{ formatEUR(v.price) }}</span>
              </div>
              <div v-if="v.savings > 0" class="summary-row" style="margin-bottom:8px;color:var(--gold);">
                <span>Discount</span>
                <span>-{{ formatEUR(v.price - v.discounted_price) }}</span>
              </div>
              <div class="summary-row total" style="margin-bottom:20px;">
                <span>Final Total (per applicant)</span>
                <span>{{ formatEUR(v.discounted_price || v.price) }}</span>
              </div>
              
              <router-link :to="'/visa/' + slug + '/apply'" class="btn btn-primary btn-block">
                Start Application →
              </router-link>
              <p class="form-hint" style="margin-top:12px;">Photo, passport copy, and visa service terms are collected in the application form.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div v-else class="container" style="padding: 100px 0; text-align: center;">
      <h2>Loading Visa Details...</h2>
  </div>
</template>

<script setup>
import { formatEUR } from '../../utils/currency.js';
import { ref, computed, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const v = ref(null);
const slug = route.params.slug;

const heroStyle = computed(() => {
  if (v.value && v.value.image_url) {
    return { backgroundImage: `url(${v.value.image_url})` };
  }
  return { backgroundImage: `url(https://images.unsplash.com/photo-1436491865332-7a61a109cc05?q=80&w=2000&auto=format&fit=crop)` };
});

onMounted(async () => {
  try {
    const res = await axios.get('/api/visas/' + slug);
    v.value = res.data;
  } catch (err) {
    console.error("Visa not found", err);
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


.visa-content-sections {
  display: flex;
  flex-direction: column;
  gap: 40px;
}
.section-block h2 {
  font-size: 24px;
  color: var(--teal-dark);
  margin-bottom: 16px;
  padding-bottom: 8px;
  border-bottom: 2px solid var(--line);
}
.check-list {
  list-style: none;
  padding: 0;
}
.check-list li {
  position: relative;
  padding-left: 28px;
  margin-bottom: 12px;
  line-height: 1.6;
  color: var(--ink);
}
.check-list li::before {
  content: "✓";
  position: absolute;
  left: 0;
  top: 0;
  color: var(--gold);
  font-weight: bold;
}

.fee-box {
  background: var(--bg-body);
  border: 1px solid var(--line);
  border-radius: 12px;
  padding: 20px;
}
.fee-row {
  display: flex;
  justify-content: space-between;
  padding: 12px 0;
  border-bottom: 1px dashed var(--line);
}
.fee-row:last-child {
  border-bottom: none;
}
.fee-row.total {
  font-size: 18px;
  color: var(--teal-dark);
  padding-top: 16px;
  margin-top: 4px;
}

.important-box {
  background: rgba(231, 76, 60, 0.08);
  border-left: 4px solid #e74c3c;
  padding: 16px 20px;
  border-radius: 4px;
  color: #c0392b;
}

.sticky {
  position: sticky;
  top: 100px;
}
</style>
