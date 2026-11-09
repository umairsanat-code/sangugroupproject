<template>
  <div class="admin-panel-card">
    <h2>Payment Settings (Revolut)</h2>
    <p class="text-slate-500 text-sm mb-6">Configure the Revolut account details shown to clients on the payment screen. All fields are optional — only the ones you fill in will be displayed.</p>

    <div v-if="flash" class="flash" :class="flashType">{{ flash }}</div>

    <form @submit.prevent="save" class="settings-form">
      <div class="field-group">
        <h3 class="section-title">Account Details</h3>
        <div class="form-grid">
          <div class="form-group"><label>Account Holder</label><input type="text" v-model="form.account_holder" placeholder="e.g. SanguGroup Travel"></div>
          <div class="form-group"><label>Currency</label><select v-model="form.currency"><option value="EUR">EUR</option><option value="GBP">GBP</option><option value="PKR">PKR</option><option value="USD">USD</option></select></div>
          <div class="form-group"><label>Account Number</label><input type="text" v-model="form.account_number" placeholder="Revolut account number"></div>
          <div class="form-group"><label>Sort Code</label><input type="text" v-model="form.sort_code" placeholder="e.g. 12-34-56"></div>
          <div class="form-group"><label>IBAN</label><input type="text" v-model="form.iban" placeholder="PK36REVO0000000000000000000" class="mono"></div>
          <div class="form-group"><label>BIC / SWIFT</label><input type="text" v-model="form.bic_swift" placeholder="e.g. REVOGB2L"></div>
        </div>
      </div>

      <div class="field-group">
        <h3 class="section-title">Optional Extras</h3>
        <div class="form-grid">
          <div class="form-group"><label>Revolut.me / Payment Link</label><input type="url" v-model="form.payment_link" placeholder="https://revolut.me/..."></div>
        </div>
        <div class="form-group"><label>Instructions shown to client</label><textarea rows="3" v-model="form.instructions" placeholder="e.g. Please quote your booking reference with the payment so we can match it instantly."></textarea></div>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn-primary" :disabled="saving">
          <i class="fas" :class="saving ? 'fa-spinner fa-spin' : 'fa-save'"></i> {{ saving ? 'Saving...' : 'Save Payment Settings' }}
        </button>
      </div>
    </form>

    <div class="preview-box" v-if="form.account_holder || form.iban">
      <h3 class="section-title" style="margin-bottom:12px;">Preview — What clients will see</h3>
      <div class="preview-inner">
        <div v-if="form.account_holder"><b>Account Holder:</b> {{ form.account_holder }}</div>
        <div v-if="form.account_number"><b>Account Number:</b> {{ form.account_number }}</div>
        <div v-if="form.sort_code"><b>Sort Code:</b> {{ form.sort_code }}</div>
        <div v-if="form.iban"><b>IBAN:</b> <span class="mono">{{ form.iban }}</span></div>
        <div v-if="form.bic_swift"><b>BIC / SWIFT:</b> {{ form.bic_swift }}</div>
        <div><b>Currency:</b> {{ form.currency }}</div>
        <div v-if="form.payment_link" style="margin-top:8px;"><b>Payment Link:</b> <a :href="form.payment_link" target="_blank">{{ form.payment_link }}</a></div>
        <div v-if="form.instructions" style="margin-top:8px;font-style:italic;">{{ form.instructions }}</div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const form = ref({
  account_holder: '',
  account_number: '',
  sort_code: '',
  iban: '',
  bic_swift: '',
  currency: 'EUR',
  payment_link: '',
  instructions: ''
});

const saving = ref(false);
const flash = ref('');
const flashType = ref('success');

onMounted(async () => {
  try {
    const res = await axios.get('/api/admin/payment-settings');
    if (res.data) {
      form.value = {
        account_holder: res.data.account_holder || '',
        account_number: res.data.account_number || '',
        sort_code: res.data.sort_code || '',
        iban: res.data.iban || '',
        bic_swift: res.data.bic_swift || '',
        currency: res.data.currency || 'EUR',
        payment_link: res.data.payment_link || '',
        instructions: res.data.instructions || ''
      };
    }
  } catch (err) {
    console.error('Failed to load payment settings', err);
  }
});

const save = async () => {
  saving.value = true;
  flash.value = '';
  try {
    await axios.post('/api/admin/payment-settings', form.value);
    flash.value = 'Payment settings saved successfully.';
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
.form-group input, .form-group select, .form-group textarea { width: 100%; padding: 11px 13px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; background: #fff; }
.form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: #147964; box-shadow: 0 0 0 3px rgba(20,121,100,.12); }
.mono { font-family: 'Courier New', monospace; letter-spacing: .03em; }
.form-actions { margin-top: 8px; }
.btn-primary { background: #147964; border: none; color: #fff; padding: 12px 24px; border-radius: 8px; font-size: 14px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; }
.btn-primary:hover { background: #0e4b44; }
.btn-primary:disabled { opacity:.6; cursor: not-allowed; }
.flash { padding: 12px 16px; border-radius: 8px; font-size: 14px; margin-bottom: 18px; }
.flash.success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
.flash.error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
.preview-box { margin-top: 32px; padding: 20px; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 10px; }
.preview-inner { font-size: 14px; color: #334155; display: flex; flex-direction: column; gap: 6px; }
.preview-inner b { color: #0f172a; }
.preview-inner a { color: #147964; word-break: break-all; }
@media (max-width: 640px) { .form-grid { grid-template-columns: 1fr; } }
</style>