<template>
  <div class="admin-panel-card">
    <h2>Social Links (Footer)</h2>
    <p class="text-slate-500 text-sm mb-6">Configure the social media links shown in the website footer. Leave a field empty to hide that icon.</p>

    <div v-if="flash" class="flash" :class="flashType">{{ flash }}</div>

    <form @submit.prevent="save" class="settings-form">
      <div class="field-group">
        <h3 class="section-title">Follow Us</h3>
        <div class="form-grid">
          <div class="form-group">
            <label><b>f</b> Facebook</label>
            <input type="url" v-model="form.facebook" placeholder="https://facebook.com/yourpage">
          </div>
          <div class="form-group">
            <label><b>ig</b> Instagram</label>
            <input type="url" v-model="form.instagram" placeholder="https://instagram.com/yourhandle">
          </div>
          <div class="form-group">
            <label><b>wa</b> WhatsApp</label>
            <input type="url" v-model="form.whatsapp" placeholder="https://wa.me/923085093631">
          </div>
          <div class="form-group">
            <label><b>yt</b> YouTube</label>
            <input type="url" v-model="form.youtube" placeholder="https://youtube.com/@yourchannel">
          </div>
          <div class="form-group">
            <label><b>tt</b> TikTok</label>
            <input type="url" v-model="form.tiktok" placeholder="https://tiktok.com/@yourhandle">
          </div>
          <div class="form-group">
            <label><b>tg</b> Telegram</label>
            <input type="url" v-model="form.telegram" placeholder="https://t.me/yourchannel">
          </div>
          <div class="form-group">
            <label><b>𝕏</b> X / Twitter</label>
            <input type="url" v-model="form.twitter" placeholder="https://x.com/yourhandle">
          </div>
        </div>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn-primary" :disabled="saving">
          <i class="fas" :class="saving ? 'fa-spinner fa-spin' : 'fa-save'"></i> {{ saving ? 'Saving...' : 'Save Social Links' }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const form = ref({
  facebook: '',
  instagram: '',
  whatsapp: '',
  youtube: '',
  tiktok: '',
  telegram: '',
  twitter: ''
});

const saving = ref(false);
const flash = ref('');
const flashType = ref('success');

onMounted(async () => {
  try {
    const res = await axios.get('/api/admin/social-links');
    if (res.data) {
      form.value = {
        facebook: res.data.facebook || '',
        instagram: res.data.instagram || '',
        whatsapp: res.data.whatsapp || '',
        youtube: res.data.youtube || '',
        tiktok: res.data.tiktok || '',
        telegram: res.data.telegram || '',
        twitter: res.data.twitter || ''
      };
    }
  } catch (err) {
    console.error('Failed to load social links', err);
  }
});

const save = async () => {
  saving.value = true;
  flash.value = '';
  try {
    await axios.post('/api/admin/social-links', form.value);
    flash.value = 'Social links saved successfully.';
    flashType.value = 'success';
  } catch (err) {
    console.error('Save failed', err);
    flash.value = 'Failed to save. Please check the details and try again.';
    flashType.value = 'error';
  } finally {
    saving.value = false;
  }
};
</script>

<style scoped>
.settings-form { display: flex; flex-direction: column; gap: 28px; }
.field-group { display: flex; flex-direction: column; gap: 14px; }
.section-title { font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; color: #147964; margin: 0; padding-bottom: 8px; border-bottom: 2px solid #e2e8f0; }
.form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; }
.form-group { margin: 0; }
.form-group label { display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px; }
.form-group label b { color: #147964; }
.form-group input { width: 100%; padding: 11px 13px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; background: #fff; }
.form-group input:focus { outline: none; border-color: #147964; box-shadow: 0 0 0 3px rgba(20,121,100,.12); }
.form-actions { margin-top: 8px; }
.btn-primary { background: #147964; border: none; color: #fff; padding: 12px 24px; border-radius: 8px; font-size: 14px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; }
.btn-primary:hover { background: #0e4b44; }
.btn-primary:disabled { opacity:.6; cursor: not-allowed; }
.flash { padding: 12px 16px; border-radius: 8px; font-size: 14px; margin-bottom: 18px; }
.flash.success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
.flash.error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
.text-slate-500 { color: #64748b; }
.text-sm { font-size: 14px; }
.mb-6 { margin-bottom: 24px; }
@media (max-width: 640px) { .form-grid { grid-template-columns: 1fr; } }
</style>