<template>
  <div class="visa-apply-page">

    <!-- ───── HERO ───── -->
    <section class="apply-hero">
      <div class="container" style="position:relative;z-index:2;">
        <div class="breadcrumb-row">
          <router-link to="/">Home</router-link>
          <span class="sep">/</span>
          <router-link to="/visa">Visas</router-link>
          <span class="sep">/</span>
          <span v-if="visaData">{{ visaData.country }}</span>
          <span v-else>Apply</span>
          <span class="sep">/</span>
          <span class="current">Application</span>
        </div>
        <div class="hero-badge">
          <i class="fas fa-shield-alt"></i>
          <span>Fast &amp; Secure Visa Assistance</span>
        </div>
        <h1 class="hero-title">
          <span v-if="visaData">Apply for <em>{{ visaData.country }}</em> Visa</span>
          <span v-else>Visa <em>Application</em></span>
        </h1>
        <p class="hero-sub">
          Simple 2-step process · Secure Stripe payment · Expert visa processing
          <span v-if="visaData && estimatedFee > 0"> · {{ formatEUR(estimatedFee) }} per applicant</span>
        </p>
      </div>
    </section>

    <!-- ───── CONTENT ───── -->
    <div class="container page-body">

      <!-- Cancelled notice -->
      <div v-if="cancelledNotice" class="notice-bar">
        <i class="fas fa-info-circle"></i>
        <span>Your previous checkout was not completed. Review your details below and try again, or choose to pay later via bank transfer.</span>
        <button @click="cancelledNotice = false"><i class="fas fa-times"></i></button>
      </div>

      <!-- ─── PROGRESS BAR ─── -->
      <div v-if="!submitted" class="stepper">
        <div class="step-item" :class="{ active: currentStep === 1, done: currentStep > 1 }">
          <div class="step-circle">
            <i v-if="currentStep > 1" class="fas fa-check"></i>
            <span v-else>1</span>
          </div>
          <div class="step-info">
            <span class="step-label">Step 1 of 2</span>
            <span class="step-name">Application Details</span>
          </div>
        </div>
        <div class="step-connector" :class="{ filled: currentStep === 2 }"></div>
        <div class="step-item" :class="{ active: currentStep === 2 }">
          <div class="step-circle">2</div>
          <div class="step-info">
            <span class="step-label">Step 2 of 2</span>
            <span class="step-name">Review &amp; Payment</span>
          </div>
        </div>
      </div>

      <!-- ═══════════════════════════════════════════════
           STEP 1 — APPLICATION DETAILS
      ═══════════════════════════════════════════════ -->
      <div v-if="!submitted && currentStep === 1" class="form-card">
        <div class="form-card-head">
          <h2 class="form-card-title">Step 1 — Your Application Details</h2>
          <p class="form-card-hint">Only essential information is needed. This takes under 2 minutes.</p>
        </div>

        <form @submit.prevent="goToStep2" novalidate>
          <div class="form-grid">

            <!-- Full Name -->
            <div class="form-field full">
              <label class="field-label">
                Full Name <span class="req">*</span>
                <span class="field-hint">Exactly as shown on passport</span>
              </label>
              <div class="input-wrap" :class="{ error: errors.full_name }">
                <i class="fas fa-user input-icon"></i>
                <input
                  type="text"
                  v-model.trim="form.full_name"
                  placeholder="e.g. Muhammad Ahmad"
                  autocomplete="name"
                />
              </div>
              <span v-if="errors.full_name" class="field-error">{{ errors.full_name }}</span>
            </div>

            <!-- Email -->
            <div class="form-field">
              <label class="field-label">Email Address <span class="req">*</span></label>
              <div class="input-wrap" :class="{ error: errors.email }">
                <i class="fas fa-envelope input-icon"></i>
                <input
                  type="email"
                  v-model.trim="form.email"
                  placeholder="name@email.com"
                  autocomplete="email"
                />
              </div>
              <span v-if="errors.email" class="field-error">{{ errors.email }}</span>
            </div>

            <!-- WhatsApp / Phone -->
            <div class="form-field">
              <label class="field-label">
                WhatsApp / Phone <span class="req">*</span>
                <span class="field-hint">Include country code</span>
              </label>
              <div class="input-wrap" :class="{ error: errors.phone }">
                <i class="fab fa-whatsapp input-icon" style="color:#25D366;"></i>
                <input
                  type="tel"
                  v-model.trim="form.phone"
                  placeholder="+34 672 06 81 48"
                  autocomplete="tel"
                />
              </div>
              <span v-if="errors.phone" class="field-error">{{ errors.phone }}</span>
            </div>

            <!-- Nationality -->
            <div class="form-field">
              <label class="field-label">Your Nationality <span class="req">*</span></label>
              <div class="input-wrap" :class="{ error: errors.nationality }">
                <i class="fas fa-passport input-icon"></i>
                <select v-model="form.nationality">
                  <option value="" disabled>Select nationality</option>
                  <option v-for="n in nationalitiesList" :key="n" :value="n">{{ n }}</option>
                </select>
              </div>
              <span v-if="errors.nationality" class="field-error">{{ errors.nationality }}</span>
            </div>

            <!-- Destination Country -->
            <div class="form-field">
              <label class="field-label">Country You Want to Visit <span class="req">*</span></label>
              <div class="input-wrap" :class="{ error: errors.destination_country }">
                <i class="fas fa-plane-arrival input-icon"></i>
                <select v-model="form.destination_country" @change="onCountryChange">
                  <option value="" disabled>Select destination</option>
                  <option v-for="d in availableDestinations" :key="d" :value="d">{{ d }}</option>
                </select>
              </div>
              <span v-if="errors.destination_country" class="field-error">{{ errors.destination_country }}</span>
            </div>

            <!-- Visa Type -->
            <div class="form-field">
              <label class="field-label">
                Visa Type <span class="req">*</span>
                <span v-if="estimatedFee > 0" class="fee-badge">{{ formatEUR(estimatedFee) }} / person</span>
              </label>
              <div class="input-wrap" :class="{ error: errors.visa_type }">
                <i class="fas fa-id-card input-icon"></i>
                <select v-model="form.visa_type" @change="onVisaTypeChange">
                  <option value="" disabled>Select visa category</option>
                  <option v-for="t in availableVisaTypes" :key="t" :value="t">{{ t }}</option>
                </select>
              </div>
              <span v-if="errors.visa_type" class="field-error">{{ errors.visa_type }}</span>
            </div>

            <!-- Travel Date -->
            <div class="form-field">
              <label class="field-label">
                Expected Travel Date
                <span class="field-hint optional">Optional</span>
              </label>
              <div class="input-wrap">
                <i class="fas fa-calendar-alt input-icon"></i>
                <input type="date" v-model="form.travel_date" :min="todayDate" />
              </div>
            </div>

            <!-- Number of Applicants -->
            <div class="form-field">
              <label class="field-label">Number of Applicants</label>
              <div class="travellers-control">
                <button type="button" class="traveller-btn" :disabled="form.travellers <= 1" @click="form.travellers--">
                  <i class="fas fa-minus"></i>
                </button>
                <span class="traveller-count">{{ form.travellers }}</span>
                <button type="button" class="traveller-btn" :disabled="form.travellers >= 15" @click="form.travellers++">
                  <i class="fas fa-plus"></i>
                </button>
                <span class="traveller-total">Total: <strong>{{ formatEUR(estimatedFee * form.travellers) }}</strong></span>
              </div>
            </div>

            <!-- ─── DOCUMENT UPLOADS ─── -->
            <div class="form-field full upload-section-title">
              <h3 class="upload-heading">
                <i class="fas fa-cloud-upload-alt"></i> Document Uploads
                <span class="field-hint optional">Optional at this stage</span>
              </h3>
              <p class="upload-hint">You can upload documents now or send them later via WhatsApp. Accepted: JPG, PNG, PDF up to 5 MB.</p>
            </div>

            <!-- Applicant Photo -->
            <div class="form-field">
              <label class="field-label">
                <i class="fas fa-user-circle" style="color:var(--pri);margin-right:6px;"></i>
                Applicant Photo
                <span class="field-hint">Passport-style white background</span>
              </label>
              <div
                class="upload-drop-zone"
                :class="{ 'has-file': form.applicantPhoto, 'drag-over': dragging.photo }"
                @click="$refs.photoInput.click()"
                @dragover.prevent="dragging.photo = true"
                @dragleave="dragging.photo = false"
                @drop.prevent="handleDrop($event, 'photo')"
              >
                <!-- Preview -->
                <div v-if="photoPreview" class="upload-preview">
                  <img :src="photoPreview" alt="Applicant photo preview" />
                  <button type="button" class="remove-file" @click.stop="clearFile('photo')">
                    <i class="fas fa-times"></i>
                  </button>
                </div>
                <!-- Placeholder -->
                <div v-else class="upload-placeholder">
                  <div class="upload-icon"><i class="fas fa-portrait"></i></div>
                  <p>Click to upload or drag &amp; drop</p>
                  <small>JPG, PNG · Max 5MB</small>
                </div>
              </div>
              <input
                ref="photoInput"
                type="file"
                accept="image/*"
                hidden
                @change="handleFileSelect($event, 'photo')"
              />
              <div v-if="form.applicantPhoto" class="file-name-tag">
                <i class="fas fa-check-circle" style="color:var(--success);"></i>
                {{ form.applicantPhoto.name }}
              </div>
            </div>

            <!-- Passport Copy -->
            <div class="form-field">
              <label class="field-label">
                <i class="fas fa-passport" style="color:var(--pri);margin-right:6px;"></i>
                Passport Copy / Scan
                <span class="field-hint">Bio-data page clearly visible</span>
              </label>
              <div
                class="upload-drop-zone"
                :class="{ 'has-file': form.passportCopy, 'drag-over': dragging.passport }"
                @click="$refs.passportInput.click()"
                @dragover.prevent="dragging.passport = true"
                @dragleave="dragging.passport = false"
                @drop.prevent="handleDrop($event, 'passport')"
              >
                <!-- Preview -->
                <div v-if="passportPreview" class="upload-preview">
                  <img :src="passportPreview" alt="Passport scan preview" />
                  <button type="button" class="remove-file" @click.stop="clearFile('passport')">
                    <i class="fas fa-times"></i>
                  </button>
                </div>
                <!-- PDF icon (no preview for PDF) -->
                <div v-else-if="form.passportCopy && form.passportCopy.type === 'application/pdf'" class="upload-preview pdf-preview">
                  <i class="fas fa-file-pdf pdf-icon"></i>
                  <span>{{ form.passportCopy.name }}</span>
                  <button type="button" class="remove-file" @click.stop="clearFile('passport')">
                    <i class="fas fa-times"></i>
                  </button>
                </div>
                <!-- Placeholder -->
                <div v-else class="upload-placeholder">
                  <div class="upload-icon"><i class="fas fa-passport"></i></div>
                  <p>Click to upload or drag &amp; drop</p>
                  <small>JPG, PNG, PDF · Max 5MB</small>
                </div>
              </div>
              <input
                ref="passportInput"
                type="file"
                accept="image/*,application/pdf"
                hidden
                @change="handleFileSelect($event, 'passport')"
              />
              <div v-if="form.passportCopy" class="file-name-tag">
                <i class="fas fa-check-circle" style="color:var(--success);"></i>
                {{ form.passportCopy.name }}
              </div>
            </div>

          </div><!-- /form-grid -->

          <!-- Error summary -->
          <div v-if="hasErrors" class="error-summary">
            <i class="fas fa-exclamation-triangle"></i>
            Please fill in all required fields before continuing.
          </div>

          <!-- STEP 1 CTA -->
          <div class="step-actions">
            <button type="submit" class="btn-cta">
              Continue to Review &amp; Payment
              <i class="fas fa-arrow-right"></i>
            </button>
          </div>
        </form>
      </div>

      <!-- ═══════════════════════════════════════════════
           STEP 2 — REVIEW & PAYMENT
      ═══════════════════════════════════════════════ -->
      <div v-if="!submitted && currentStep === 2" class="form-card">
        <div class="form-card-head" style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:12px;">
          <div>
            <h2 class="form-card-title">Step 2 — Review &amp; Payment</h2>
            <p class="form-card-hint">Check your details carefully before choosing payment.</p>
          </div>
          <button type="button" class="btn-edit" @click="currentStep = 1">
            <i class="fas fa-pen"></i> Edit Details
          </button>
        </div>

        <!-- REVIEW TABLE -->
        <div class="review-card">
          <h4 class="review-heading"><i class="fas fa-check-double"></i> Applicant Details</h4>
          <div class="review-grid">
            <div class="review-row">
              <span>Applicant Name</span>
              <strong>{{ form.full_name }}</strong>
            </div>
            <div class="review-row">
              <span>Email Address</span>
              <strong>{{ form.email }}</strong>
            </div>
            <div class="review-row">
              <span>WhatsApp / Phone</span>
              <strong>{{ form.phone }}</strong>
            </div>
            <div class="review-row">
              <span>Nationality</span>
              <strong>{{ form.nationality }}</strong>
            </div>
            <div class="review-row">
              <span>Destination Country</span>
              <strong style="color:var(--pri);">{{ form.destination_country }}</strong>
            </div>
            <div class="review-row">
              <span>Visa Category</span>
              <strong>{{ form.visa_type }}</strong>
            </div>
            <div class="review-row">
              <span>Travel Date</span>
              <strong>{{ form.travel_date ? formatDate(form.travel_date) : 'Flexible / TBD' }}</strong>
            </div>
            <div class="review-row">
              <span>Applicants</span>
              <strong>{{ form.travellers }} person(s)</strong>
            </div>
            <div v-if="form.applicantPhoto || form.passportCopy" class="review-row">
              <span>Documents Attached</span>
              <strong style="color:var(--success);">
                <i class="fas fa-paperclip"></i>
                {{ [form.applicantPhoto ? 'Photo' : null, form.passportCopy ? 'Passport' : null].filter(Boolean).join(', ') }}
              </strong>
            </div>
          </div>
        </div>

        <!-- FEE BREAKDOWN -->
        <div class="fee-box">
          <h4 class="review-heading"><i class="fas fa-receipt"></i> Fee Breakdown</h4>
          <div class="fee-row">
            <span>Visa Service Fee (per applicant)</span>
            <b>{{ formatEUR(estimatedFee) }}</b>
          </div>
          <div class="fee-row">
            <span>Number of Applicants</span>
            <b>&times; {{ form.travellers }}</b>
          </div>
          <div class="fee-row total-row">
            <span>Total Due (EUR)</span>
            <b class="total-amount">{{ formatEUR(totalFee) }}</b>
          </div>
          <p class="fee-note">
            <i class="fas fa-shield-alt"></i>
            Transparent fee — no hidden charges or card surcharges.
          </p>
        </div>

        <!-- PAYMENT OPTIONS -->
        <div class="payment-options">
          <h4 class="payment-heading">Choose Payment Method</h4>

          <div class="pay-grid">
            <!-- PAY NOW -->
            <div class="pay-option pay-stripe">
              <div class="pay-option-top">
                <span class="pay-badge recommended">Recommended</span>
                <i class="fab fa-stripe pay-brand-icon"></i>
              </div>
              <h4 class="pay-title">Pay Now Online</h4>
              <p class="pay-desc">Instant verification. Secure card payment via Stripe Checkout. Fast-track processing.</p>
              <div class="card-icons">
                <i class="fab fa-cc-visa"></i>
                <i class="fab fa-cc-mastercard"></i>
                <i class="fab fa-cc-amex"></i>
                <span class="ssl-badge"><i class="fas fa-lock"></i> 256-bit SSL</span>
              </div>
              <button
                type="button"
                class="btn-cta btn-pay-now"
                :disabled="submitting"
                @click="submit('pay_now')"
              >
                <i v-if="submitting && selectedAction === 'pay_now'" class="fas fa-spinner fa-spin"></i>
                <i v-else class="fas fa-credit-card"></i>
                {{ submitting && selectedAction === 'pay_now' ? 'Preparing Checkout...' : 'Pay with Card (Stripe)' }}
              </button>
            </div>

            <!-- PAY LATER -->
            <div class="pay-option pay-later">
              <div class="pay-option-top">
                <span class="pay-badge pay-later-badge">Bank Transfer</span>
                <i class="fas fa-university pay-brand-icon" style="color:#64748b;"></i>
              </div>
              <h4 class="pay-title">Submit &amp; Pay Later</h4>
              <p class="pay-desc">Get a reference number now. Pay via Revolut / bank transfer before document processing begins.</p>
              <div class="pay-note-row">
                <i class="fas fa-clock" style="color:var(--warn);"></i>
                Application held for 48 hours pending payment.
              </div>
              <button
                type="button"
                class="btn-outline-dark btn-pay-later"
                :disabled="submitting"
                @click="submit('pay_later')"
              >
                <i v-if="submitting && selectedAction === 'pay_later'" class="fas fa-spinner fa-spin"></i>
                <i v-else class="fas fa-paper-plane"></i>
                {{ submitting && selectedAction === 'pay_later' ? 'Submitting...' : 'Submit & Pay Later' }}
              </button>
            </div>
          </div>

          <!-- Back & help -->
          <div class="step2-footer">
            <button type="button" class="btn-back" @click="currentStep = 1">
              <i class="fas fa-arrow-left"></i> Back to Step 1
            </button>
            <span class="help-text">
              Need help?
              <a href="https://wa.me/34672068148" target="_blank" rel="noopener" class="wa-link">
                <i class="fab fa-whatsapp"></i> +34 672 06 81 48
              </a>
            </span>
          </div>
        </div>
      </div>

      <!-- ═══════════════════════════════════════════════
           SUCCESS — PAY LATER CONFIRMATION
      ═══════════════════════════════════════════════ -->
      <div v-if="submitted" class="success-card">
        <div class="success-banner">
          <div class="success-icon-wrap">
            <i class="fas fa-check success-icon"></i>
          </div>
          <span class="success-badge">Application Saved</span>
          <h2 class="success-title">Application Submitted Successfully!</h2>
          <p class="success-sub">Thank you, {{ form.full_name }}. Your visa application is registered. Please complete payment to begin processing.</p>
        </div>

        <div class="success-body">
          <!-- Reference -->
          <div class="ref-box">
            <span class="ref-label">Your Application Reference</span>
            <div class="ref-number-row">
              <span class="ref-number">{{ bookingRef }}</span>
              <button class="copy-btn" @click="copyRef">
                <i :class="copied ? 'fas fa-check' : 'fas fa-copy'"></i>
                {{ copied ? 'Copied!' : 'Copy' }}
              </button>
            </div>
            <p class="ref-hint">Always quote this reference in transfers or WhatsApp messages.</p>
          </div>

          <!-- Bank Details -->
          <div v-if="paymentSettings && (paymentSettings.account_holder || paymentSettings.iban)" class="bank-box">
            <h4 class="bank-title"><i class="fas fa-university"></i> Bank Transfer / Revolut Details</h4>
            <div class="bank-grid">
              <div v-if="paymentSettings.account_holder" class="bank-row">
                <span>Account Holder</span><strong>{{ paymentSettings.account_holder }}</strong>
              </div>
              <div v-if="paymentSettings.iban" class="bank-row">
                <span>IBAN</span><strong class="mono">{{ paymentSettings.iban }}</strong>
              </div>
              <div v-if="paymentSettings.bic_swift" class="bank-row">
                <span>BIC / SWIFT</span><strong class="mono">{{ paymentSettings.bic_swift }}</strong>
              </div>
              <div v-if="paymentSettings.account_number" class="bank-row">
                <span>Account No.</span><strong class="mono">{{ paymentSettings.account_number }}</strong>
              </div>
              <div class="bank-row">
                <span>Amount</span><strong style="color:var(--pri);font-size:1.1em;">{{ formatEUR(totalFee) }}</strong>
              </div>
              <div class="bank-row">
                <span>Payment Reference</span>
                <strong class="mono" style="color:#dc2626;">{{ bookingRef }}</strong>
              </div>
            </div>
          </div>

          <!-- Next steps -->
          <div class="next-steps">
            <h5><i class="fas fa-list-check" style="color:var(--pri);"></i> What Happens Next?</h5>
            <ol>
              <li>A confirmation email was sent to <b>{{ form.email }}</b>.</li>
              <li>Make the bank transfer using the reference <b>{{ bookingRef }}</b>.</li>
              <li>Send proof of payment via WhatsApp to <b>+34 672 06 81 48</b> and we will begin processing immediately.</li>
            </ol>
          </div>

          <!-- Actions -->
          <div class="success-actions">
            <a :href="whatsAppUrl" target="_blank" rel="noopener" class="btn-wa">
              <i class="fab fa-whatsapp"></i> Send Proof via WhatsApp
            </a>
            <router-link to="/" class="btn-home">Return to Home</router-link>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import { formatEUR } from '../../utils/currency.js';

const route = useRoute();
const slug = route.params.slug || '';
const cancelledNotice = ref(route.query.cancelled === '1');

const currentStep = ref(1);
const submitting = ref(false);
const submitted = ref(false);
const selectedAction = ref('');
const bookingRef = ref('');
const copied = ref(false);
const paymentSettings = ref(null);
const visasList = ref([]);
const visaData = ref(null);

const photoInput = ref(null);
const passportInput = ref(null);

const dragging = ref({ photo: false, passport: false });

const todayDate = new Date().toISOString().split('T')[0];

const form = ref({
  full_name: '',
  email: '',
  phone: '',
  nationality: '',
  destination_country: '',
  visa_type: '',
  travel_date: '',
  travellers: 1,
  applicantPhoto: null,
  passportCopy: null,
});

const errors = ref({});
const hasErrors = computed(() => Object.keys(errors.value).length > 0);

// ── FILE HANDLING ──────────────────────────────────
const handleFileSelect = (event, field) => {
  const file = event.target.files[0];
  if (!file) return;
  if (file.size > 5 * 1024 * 1024) {
    alert('File is too large. Maximum allowed size is 5 MB.');
    return;
  }
  if (field === 'photo') form.value.applicantPhoto = file;
  if (field === 'passport') form.value.passportCopy = file;
};

const handleDrop = (event, field) => {
  dragging.value[field === 'photo' ? 'photo' : 'passport'] = false;
  const file = event.dataTransfer.files[0];
  if (!file) return;
  if (file.size > 5 * 1024 * 1024) {
    alert('File is too large. Maximum allowed size is 5 MB.');
    return;
  }
  if (field === 'photo') form.value.applicantPhoto = file;
  if (field === 'passport') form.value.passportCopy = file;
};

const clearFile = (field) => {
  if (field === 'photo') {
    form.value.applicantPhoto = null;
    if (photoInput.value) photoInput.value.value = '';
  } else {
    form.value.passportCopy = null;
    if (passportInput.value) passportInput.value.value = '';
  }
};

const photoPreview = computed(() => {
  if (!form.value.applicantPhoto || form.value.applicantPhoto.type === 'application/pdf') return '';
  return URL.createObjectURL(form.value.applicantPhoto);
});

const passportPreview = computed(() => {
  if (!form.value.passportCopy || form.value.passportCopy.type === 'application/pdf') return '';
  return URL.createObjectURL(form.value.passportCopy);
});

// ── NATIONALITIES ──────────────────────────────────
const nationalitiesList = [
  'Spain', 'Pakistan', 'United Kingdom', 'Saudi Arabia', 'United Arab Emirates',
  'France', 'Germany', 'Italy', 'Netherlands', 'Belgium', 'Portugal',
  'United States', 'Canada', 'Australia', 'Qatar', 'Kuwait', 'Bahrain',
  'Oman', 'Morocco', 'Algeria', 'Egypt', 'Turkey', 'India', 'Bangladesh', 'Other'
];

const defaultDestinations = [
  'Saudi Arabia', 'United Arab Emirates', 'United Kingdom', 'Schengen (EU)',
  'Turkey', 'Malaysia', 'Qatar', 'Spain', 'United States', 'Canada', 'Egypt', 'Oman'
];

const availableDestinations = computed(() => {
  const fromDB = visasList.value.map(v => v.country).filter(Boolean);
  return Array.from(new Set([...fromDB, ...defaultDestinations])).sort();
});

const availableVisaTypes = computed(() => {
  if (!form.value.destination_country) return ['Tourist Visa', 'Visit Visa', 'Business Visa', 'e-Visa'];
  const matches = visasList.value.filter(v =>
    v.country && v.country.toLowerCase() === form.value.destination_country.toLowerCase()
  );
  if (matches.length) {
    const types = Array.from(new Set(matches.map(m => m.type).filter(Boolean)));
    if (!types.includes('Tourist Visa')) types.push('Tourist Visa');
    if (!types.includes('Visit Visa')) types.push('Visit Visa');
    return types;
  }
  return ['Tourist Visa', 'Visit Visa', 'Business Visa', 'Family Visit', 'e-Visa', 'Transit Visa'];
});

const estimatedFee = computed(() => {
  if (!form.value.destination_country) return 120;
  const match = visasList.value.find(v =>
    v.country && v.country.toLowerCase() === form.value.destination_country.toLowerCase() &&
    (!form.value.visa_type || (v.type && v.type.toLowerCase() === form.value.visa_type.toLowerCase()))
  );
  if (match) {
    const raw = match.discounted_price || match.price;
    const num = parseFloat(raw);
    if (!isNaN(num) && num > 0) {
      return num > 1000 ? Math.round(num / 300) : num;
    }
  }
  return 120;
});

const totalFee = computed(() => estimatedFee.value * (form.value.travellers || 1));

const whatsAppUrl = computed(() => {
  const ref = bookingRef.value;
  const country = form.value.destination_country;
  const name = form.value.full_name;
  const text = encodeURIComponent(
    `Hola SanguGroup, he completado mi solicitud de visado ${country} (Ref: ${ref}, Nombre: ${name}). Adjunto el comprobante de pago.`
  );
  return `https://wa.me/34672068148?text=${text}`;
});

const onCountryChange = () => {
  if (availableVisaTypes.value.length > 0) {
    form.value.visa_type = availableVisaTypes.value[0];
  }
};
const onVisaTypeChange = () => {}; // fee recalculates via computed

const formatDate = (dateStr) => {
  if (!dateStr) return '';
  try {
    return new Date(dateStr).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
  } catch (e) { return dateStr; }
};

const copyRef = () => {
  if (!bookingRef.value) return;
  navigator.clipboard.writeText(bookingRef.value).then(() => {
    copied.value = true;
    setTimeout(() => { copied.value = false; }, 2500);
  });
};

// ── VALIDATION ─────────────────────────────────────
const validateStep1 = () => {
  const e = {};
  if (!form.value.full_name) e.full_name = 'Full name is required';
  if (!form.value.email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.value.email)) {
    e.email = 'A valid email address is required';
  }
  if (!form.value.phone || form.value.phone.length < 6) {
    e.phone = 'Phone / WhatsApp number is required';
  }
  if (!form.value.nationality) e.nationality = 'Nationality is required';
  if (!form.value.destination_country) e.destination_country = 'Destination country is required';
  if (!form.value.visa_type) e.visa_type = 'Visa type is required';
  errors.value = e;
  return Object.keys(e).length === 0;
};

const goToStep2 = () => {
  if (!validateStep1()) {
    window.scrollTo({ top: 300, behavior: 'smooth' });
    return;
  }
  currentStep.value = 2;
  window.scrollTo({ top: 180, behavior: 'smooth' });
};

// ── SUBMISSION ─────────────────────────────────────
const submit = async (action) => {
  if (submitting.value) return;
  if (!validateStep1()) { currentStep.value = 1; return; }

  submitting.value = true;
  selectedAction.value = action;

  try {
    // Build payload - use FormData to support file uploads if documents are attached
    let payload;
    const hasFiles = form.value.applicantPhoto || form.value.passportCopy;

    if (hasFiles) {
      payload = new FormData();
      payload.append('full_name', form.value.full_name);
      payload.append('email', form.value.email);
      payload.append('phone', form.value.phone);
      payload.append('nationality', form.value.nationality);
      payload.append('destination_country', form.value.destination_country);
      payload.append('visa_type', form.value.visa_type);
      payload.append('travel_date', form.value.travel_date || '');
      payload.append('travellers', form.value.travellers);
      payload.append('visa_id', visaData.value?.id || '');
      payload.append('visa_slug', slug || '');
      payload.append('pay_action', action);
      if (form.value.applicantPhoto) payload.append('applicant_photo', form.value.applicantPhoto);
      if (form.value.passportCopy) payload.append('passport_copy', form.value.passportCopy);
    } else {
      payload = {
        full_name: form.value.full_name,
        email: form.value.email,
        phone: form.value.phone,
        nationality: form.value.nationality,
        destination_country: form.value.destination_country,
        visa_type: form.value.visa_type,
        travel_date: form.value.travel_date || null,
        travellers: form.value.travellers,
        visa_id: visaData.value?.id || null,
        visa_slug: slug || null,
        pay_action: action,
      };
    }

    const config = hasFiles ? { headers: { 'Content-Type': 'multipart/form-data' } } : {};
    const res = await axios.post('/api/visa/apply', payload, config);

    if (action === 'pay_now' && res.data.checkout_url) {
      window.location.href = res.data.checkout_url;
      return;
    }

    bookingRef.value = res.data.booking_reference;
    paymentSettings.value = res.data.payment_settings || null;
    submitted.value = true;
    window.scrollTo({ top: 100, behavior: 'smooth' });

  } catch (err) {
    console.error('Submission error:', err);
    const msg = err.response?.data?.message || err.response?.data?.error || 'Failed to submit. Please try again.';
    alert(msg);
  } finally {
    submitting.value = false;
  }
};

// ── ON MOUNT ───────────────────────────────────────
onMounted(async () => {
  try {
    const payRes = await axios.get('/api/payment-settings');
    paymentSettings.value = payRes.data;
  } catch (e) {}

  try {
    const visasRes = await axios.get('/api/visas');
    visasList.value = visasRes.data || [];
  } catch (e) {}

  if (slug) {
    try {
      const r = await axios.get('/api/visas/' + slug);
      if (r.data) {
        visaData.value = r.data;
        form.value.destination_country = r.data.country || '';
        form.value.visa_type = r.data.type || '';
      }
    } catch (e) {}
  }

  if (!form.value.destination_country && availableDestinations.value.length > 0) {
    form.value.destination_country = 'Saudi Arabia';
    onCountryChange();
  }
});
</script>

<style scoped>
/* ─── DESIGN TOKENS (local) ─── */
.visa-apply-page {
  --pri: #0059E3;
  --pri-dark: #0045B3;
  --gold: #C5A059;
  --success: #22c55e;
  --warn: #f59e0b;
  --danger: #ef4444;
  --ink: #1A2B48;
  --ink-soft: #69717D;
  --muted: #94A3B8;
  --line: #E8ECF0;
  --bg: #F7F9FC;
  --white: #ffffff;
  --radius: 16px;
  --shadow: 0 4px 24px rgba(26,43,72,0.07);
  --shadow-lg: 0 10px 40px rgba(26,43,72,0.10);
  background-color: var(--bg);
  min-height: 100vh;
  padding-bottom: 80px;
  font-family: var(--font-body, 'Inter', sans-serif);
}

/* ─── HERO ─── */
.apply-hero {
  background: linear-gradient(135deg, #061120 0%, #0d2a52 60%, #0059E3 100%);
  padding: 72px 0 56px;
  color: #fff;
}
.breadcrumb-row {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 12px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: rgba(255,255,255,.55);
  margin-bottom: 20px;
}
.breadcrumb-row a { color: rgba(255,255,255,.55); text-decoration: none; }
.breadcrumb-row a:hover { color: #fff; }
.breadcrumb-row .sep { color: rgba(255,255,255,.25); }
.breadcrumb-row .current { color: rgba(255,255,255,.85); }

.hero-badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: rgba(255,255,255,0.1);
  border: 1px solid rgba(255,255,255,0.2);
  backdrop-filter: blur(8px);
  border-radius: 999px;
  padding: 6px 16px;
  font-size: 11px;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.12em;
  color: rgba(255,255,255,0.9);
  margin-bottom: 20px;
}
.hero-badge i { color: #C5A059; }

.hero-title {
  font-size: clamp(28px, 4.5vw, 46px);
  font-weight: 800;
  color: #fff;
  margin: 0 0 12px;
  letter-spacing: -0.02em;
  line-height: 1.1;
}
.hero-title em { color: #C5A059; font-style: normal; }

.hero-sub {
  color: rgba(255,255,255,0.7);
  font-size: 15px;
  margin: 0;
  max-width: 580px;
}

/* ─── BODY ─── */
.page-body {
  max-width: 820px;
  margin: -28px auto 0;
  position: relative;
  z-index: 10;
  padding: 0 20px;
}

/* ─── NOTICE ─── */
.notice-bar {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  background: #fef3c7;
  border: 1px solid #fcd34d;
  border-radius: 10px;
  padding: 14px 16px;
  font-size: 13.5px;
  color: #92400e;
  margin-bottom: 20px;
}
.notice-bar i { flex-shrink: 0; margin-top: 2px; }
.notice-bar button { background: none; border: none; cursor: pointer; color: #92400e; margin-left: auto; padding: 0; }

/* ─── STEPPER ─── */
.stepper {
  display: flex;
  align-items: center;
  background: var(--white);
  border-radius: 14px;
  box-shadow: var(--shadow);
  padding: 20px 28px;
  margin-bottom: 20px;
}
.step-item {
  display: flex;
  align-items: center;
  gap: 12px;
}
.step-circle {
  width: 44px; height: 44px;
  border-radius: 50%;
  background: #EEF2FF;
  color: #94a3b8;
  display: flex; align-items: center; justify-content: center;
  font-size: 16px; font-weight: 800;
  transition: all 0.3s;
  flex-shrink: 0;
}
.step-item.active .step-circle {
  background: var(--pri);
  color: #fff;
  box-shadow: 0 4px 14px rgba(0,89,227,0.3);
}
.step-item.done .step-circle {
  background: var(--success);
  color: #fff;
}
.step-info { display: flex; flex-direction: column; }
.step-label {
  font-size: 11px; font-weight: 700; text-transform: uppercase;
  letter-spacing: 0.08em; color: var(--muted);
}
.step-name {
  font-size: 14px; font-weight: 700; color: var(--ink);
}
.step-item.active .step-name { color: var(--pri); }
.step-connector {
  flex: 1;
  height: 3px;
  background: var(--line);
  margin: 0 20px;
  border-radius: 999px;
  transition: background 0.4s;
}
.step-connector.filled { background: var(--success); }

@media (max-width: 540px) {
  .step-info { display: none; }
  .stepper { padding: 14px 16px; }
}

/* ─── FORM CARD ─── */
.form-card {
  background: var(--white);
  border-radius: var(--radius);
  box-shadow: var(--shadow-lg);
  overflow: hidden;
  margin-bottom: 24px;
  border: 1px solid var(--line);
}
.form-card-head {
  padding: 28px 32px 0;
}
.form-card-title {
  font-size: 22px; font-weight: 800; color: var(--ink); margin: 0 0 4px;
}
.form-card-hint {
  font-size: 14px; color: var(--ink-soft); margin: 0 0 20px;
}

/* ─── FORM GRID ─── */
.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
  padding: 24px 32px;
}
@media (max-width: 600px) {
  .form-grid { grid-template-columns: 1fr; padding: 16px 20px; }
  .form-card-head { padding: 20px 20px 0; }
}
.form-field.full { grid-column: 1 / -1; }

.field-label {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13px; font-weight: 700; color: var(--ink);
  margin-bottom: 8px;
}
.req { color: var(--danger); font-size: 15px; }
.field-hint {
  font-size: 11px; font-weight: 500;
  color: var(--muted);
  background: #F1F5F9;
  padding: 2px 8px;
  border-radius: 999px;
}
.field-hint.optional { background: #ECFDF5; color: #15803d; }

.input-wrap {
  display: flex;
  align-items: center;
  border: 1.5px solid var(--line);
  border-radius: 10px;
  overflow: hidden;
  background: var(--white);
  transition: border-color 0.2s, box-shadow 0.2s;
}
.input-wrap:focus-within {
  border-color: var(--pri);
  box-shadow: 0 0 0 3px rgba(0,89,227,0.10);
}
.input-wrap.error {
  border-color: var(--danger);
  box-shadow: 0 0 0 3px rgba(239,68,68,0.10);
}
.input-icon {
  padding: 0 12px;
  color: var(--muted);
  font-size: 15px;
  flex-shrink: 0;
}
.input-wrap input,
.input-wrap select {
  flex: 1;
  border: none;
  outline: none;
  padding: 11px 14px 11px 0;
  font-size: 14px;
  color: var(--ink);
  background: transparent;
  font-family: inherit;
  min-width: 0;
}
.input-wrap select { cursor: pointer; }

.field-error {
  font-size: 12px; color: var(--danger); font-weight: 600;
  display: block; margin-top: 5px;
}

/* ─── TRAVELLERS CONTROL ─── */
.travellers-control {
  display: flex;
  align-items: center;
  gap: 12px;
}
.traveller-btn {
  width: 36px; height: 36px;
  border-radius: 50%;
  border: 1.5px solid var(--line);
  background: var(--white);
  color: var(--ink);
  display: flex; align-items: center; justify-content: center;
  cursor: pointer;
  transition: all 0.2s;
}
.traveller-btn:hover:not(:disabled) { border-color: var(--pri); color: var(--pri); }
.traveller-btn:disabled { opacity: 0.35; cursor: not-allowed; }
.traveller-count {
  font-size: 22px; font-weight: 800; color: var(--ink);
  min-width: 32px; text-align: center;
  font-family: var(--font-display, 'Outfit', sans-serif);
}
.traveller-total {
  font-size: 13px; color: var(--ink-soft);
}
.traveller-total strong { color: var(--pri); }

/* ─── UPLOAD SECTION ─── */
.upload-section-title { margin-top: 4px; }
.upload-heading {
  font-size: 16px; font-weight: 700; color: var(--ink);
  display: flex; align-items: center; gap: 8px; margin: 0 0 4px;
}
.upload-heading i { color: var(--pri); }
.upload-hint { font-size: 13px; color: var(--muted); margin: 0; }

.fee-badge {
  font-size: 11px; font-weight: 700;
  background: rgba(0,89,227,0.08);
  color: var(--pri);
  padding: 2px 10px;
  border-radius: 999px;
}

.upload-drop-zone {
  border: 2px dashed var(--line);
  border-radius: 12px;
  padding: 24px 16px;
  text-align: center;
  cursor: pointer;
  background: #FAFBFF;
  transition: all 0.25s;
  min-height: 130px;
  display: flex; align-items: center; justify-content: center;
  position: relative;
  overflow: hidden;
}
.upload-drop-zone:hover,
.upload-drop-zone.drag-over {
  border-color: var(--pri);
  background: rgba(0,89,227,0.04);
}
.upload-drop-zone.has-file {
  border-color: var(--success);
  background: rgba(34,197,94,0.04);
}
.upload-placeholder { text-align: center; }
.upload-icon {
  width: 50px; height: 50px;
  background: rgba(0,89,227,0.08);
  border-radius: 12px;
  display: flex; align-items: center; justify-content: center;
  font-size: 22px; color: var(--pri);
  margin: 0 auto 10px;
}
.upload-placeholder p { font-size: 13.5px; font-weight: 600; color: var(--ink); margin: 0 0 4px; }
.upload-placeholder small { font-size: 11px; color: var(--muted); }

.upload-preview {
  position: relative;
  width: 100%; height: 100%;
  min-height: 100px;
  display: flex; align-items: center; justify-content: center;
}
.upload-preview img {
  max-height: 110px;
  max-width: 100%;
  border-radius: 8px;
  object-fit: contain;
}
.pdf-preview {
  flex-direction: column; gap: 8px;
}
.pdf-icon { font-size: 42px; color: #ef4444; }

.remove-file {
  position: absolute;
  top: -6px; right: -6px;
  width: 24px; height: 24px;
  border-radius: 50%;
  background: var(--danger);
  border: none;
  color: #fff;
  font-size: 11px;
  display: flex; align-items: center; justify-content: center;
  cursor: pointer;
  z-index: 5;
}
.file-name-tag {
  font-size: 12px; color: var(--success); font-weight: 600;
  margin-top: 6px; display: flex; align-items: center; gap: 5px;
  word-break: break-all;
}

/* ─── ACTIONS ─── */
.error-summary {
  margin: 0 32px 16px;
  background: rgba(239,68,68,0.08);
  border: 1px solid rgba(239,68,68,0.3);
  border-radius: 8px;
  padding: 12px 16px;
  font-size: 13px;
  font-weight: 600;
  color: var(--danger);
  display: flex; align-items: center; gap: 8px;
}
.step-actions {
  padding: 0 32px 32px;
  display: flex;
  justify-content: flex-end;
  border-top: 1px solid var(--line);
  padding-top: 24px;
  margin-top: 4px;
}
@media (max-width: 600px) {
  .step-actions { padding: 20px; }
  .error-summary { margin: 0 20px 16px; }
}

.btn-cta {
  display: inline-flex; align-items: center; gap: 8px;
  background: linear-gradient(135deg, var(--pri) 0%, var(--pri-dark) 100%);
  color: #fff; border: none;
  padding: 13px 28px; border-radius: 999px;
  font-size: 15px; font-weight: 700;
  cursor: pointer; transition: all 0.25s;
  font-family: inherit;
  box-shadow: 0 6px 20px rgba(0,89,227,0.25);
}
.btn-cta:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 10px 28px rgba(0,89,227,0.35);
}
.btn-cta:disabled { opacity: 0.6; cursor: not-allowed; }

.btn-edit {
  display: inline-flex; align-items: center; gap: 6px;
  background: none; border: 1.5px solid var(--line);
  padding: 8px 16px; border-radius: 999px;
  font-size: 13px; font-weight: 700; color: var(--ink-soft);
  cursor: pointer; transition: all 0.2s;
}
.btn-edit:hover { border-color: var(--pri); color: var(--pri); }

/* ─── REVIEW CARD ─── */
.review-card {
  background: #F7F9FC;
  border: 1px solid var(--line);
  border-radius: 12px;
  padding: 20px 24px;
  margin: 20px 32px;
}
@media (max-width: 600px) { .review-card { margin: 16px 20px; } }

.review-heading {
  font-size: 15px; font-weight: 700; color: var(--ink);
  margin: 0 0 14px;
  display: flex; align-items: center; gap: 8px;
}
.review-heading i { color: var(--pri); }

.review-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 10px;
}
@media (max-width: 540px) { .review-grid { grid-template-columns: 1fr; } }

.review-row {
  display: flex; flex-direction: column; gap: 2px;
  font-size: 13px;
}
.review-row span { color: var(--muted); font-size: 11.5px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; }
.review-row strong { color: var(--ink); font-weight: 700; }

/* ─── FEE BOX ─── */
.fee-box {
  background: var(--white);
  border: 1px solid var(--line);
  border-radius: 12px;
  padding: 20px 24px;
  margin: 0 32px 20px;
}
@media (max-width: 600px) { .fee-box { margin: 0 20px 20px; } }

.fee-row {
  display: flex; justify-content: space-between; align-items: center;
  padding: 10px 0;
  border-bottom: 1px dashed var(--line);
  font-size: 14px; color: var(--ink-soft);
}
.fee-row:last-of-type { border-bottom: none; }
.fee-row b { color: var(--ink); font-weight: 700; }
.total-row {
  font-size: 17px; font-weight: 700; color: var(--ink);
  padding-top: 14px; border-bottom: none;
}
.total-amount { color: var(--pri); font-size: 24px; }
.fee-note {
  font-size: 12px; color: var(--muted); margin: 8px 0 0;
  display: flex; align-items: center; gap: 6px;
}
.fee-note i { color: var(--success); }

/* ─── PAYMENT OPTIONS ─── */
.payment-options { padding: 0 32px 32px; }
@media (max-width: 600px) { .payment-options { padding: 0 20px 24px; } }

.payment-heading {
  font-size: 16px; font-weight: 700; color: var(--ink);
  margin: 0 0 16px;
}
.pay-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
  margin-bottom: 24px;
}
@media (max-width: 580px) { .pay-grid { grid-template-columns: 1fr; } }

.pay-option {
  border-radius: 14px;
  border: 1.5px solid var(--line);
  padding: 22px;
  display: flex; flex-direction: column; gap: 10px;
  transition: all 0.25s;
}
.pay-option:hover { box-shadow: 0 6px 24px rgba(0,0,0,0.06); transform: translateY(-2px); }

.pay-stripe { background: linear-gradient(180deg, #EFF6FF 0%, #fff 100%); border-color: #BFDBFE; }
.pay-later { background: var(--white); }

.pay-option-top { display: flex; justify-content: space-between; align-items: center; }
.pay-badge {
  font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em;
  padding: 3px 10px; border-radius: 999px;
}
.recommended { background: var(--pri); color: #fff; }
.pay-later-badge { background: #F1F5F9; color: #64748b; }
.pay-brand-icon { font-size: 28px; color: #6772e5; }

.pay-title { font-size: 16px; font-weight: 800; color: var(--ink); margin: 0; }
.pay-desc { font-size: 13px; color: var(--ink-soft); margin: 0; line-height: 1.6; }

.card-icons {
  display: flex; align-items: center; gap: 8px;
  font-size: 20px;
}
.card-icons .fa-cc-visa { color: #1a1f71; }
.card-icons .fa-cc-mastercard { color: #eb001b; }
.card-icons .fa-cc-amex { color: #2e77bc; }
.ssl-badge {
  font-size: 11px; font-weight: 700; color: var(--success);
  background: rgba(34,197,94,0.1);
  padding: 3px 8px; border-radius: 999px;
  display: inline-flex; align-items: center; gap: 4px;
  margin-left: 4px;
}
.pay-note-row {
  font-size: 12.5px; color: var(--ink-soft);
  display: flex; align-items: center; gap: 6px;
}

.btn-pay-now { width: 100%; justify-content: center; padding: 13px 20px; }
.btn-pay-later {
  width: 100%; display: flex; align-items: center; justify-content: center;
  gap: 8px;
  border: 2px solid var(--ink);
  border-radius: 999px;
  padding: 12px 20px;
  font-size: 14px; font-weight: 700; color: var(--ink);
  background: none; cursor: pointer;
  font-family: inherit;
  transition: all 0.2s;
}
.btn-pay-later:hover:not(:disabled) { background: var(--ink); color: #fff; }
.btn-pay-later:disabled { opacity: 0.6; cursor: not-allowed; }

.step2-footer {
  display: flex; justify-content: space-between; align-items: center;
  flex-wrap: wrap; gap: 10px;
  padding-top: 20px;
  border-top: 1px solid var(--line);
}
.btn-back {
  background: none; border: none; cursor: pointer;
  font-size: 14px; font-weight: 700; color: var(--muted);
  display: flex; align-items: center; gap: 6px;
  transition: color 0.2s; font-family: inherit;
}
.btn-back:hover { color: var(--ink); }
.help-text { font-size: 13px; color: var(--muted); display: flex; align-items: center; gap: 6px; }
.wa-link { color: #25D366; font-weight: 700; text-decoration: none; display: flex; align-items: center; gap: 4px; }
.wa-link:hover { text-decoration: underline; }

/* ─── SUCCESS CARD ─── */
.success-card {
  background: var(--white);
  border-radius: var(--radius);
  box-shadow: var(--shadow-lg);
  overflow: hidden;
  border: 1px solid var(--line);
}
.success-banner {
  background: linear-gradient(135deg, #061120 0%, #0d2a52 60%, #0059E3 100%);
  padding: 48px 32px 40px;
  text-align: center;
  color: #fff;
}
.success-icon-wrap {
  width: 72px; height: 72px;
  border-radius: 50%;
  background: rgba(255,255,255,0.15);
  display: flex; align-items: center; justify-content: center;
  font-size: 36px; color: #38bdf8;
  margin: 0 auto 16px;
}
.success-badge {
  display: inline-block;
  background: rgba(197,160,89,0.9);
  color: #fff; font-size: 11px; font-weight: 800;
  text-transform: uppercase; letter-spacing: 0.1em;
  padding: 4px 14px; border-radius: 999px;
  margin-bottom: 16px;
}
.success-title { font-size: 26px; font-weight: 800; color: #fff; margin: 0 0 10px; }
.success-sub { color: rgba(255,255,255,0.7); font-size: 15px; margin: 0; }

.success-body { padding: 32px; }
@media (max-width: 600px) { .success-body { padding: 20px; } }

/* reference */
.ref-box {
  background: #EFF6FF;
  border: 1px solid #BFDBFE;
  border-radius: 12px;
  padding: 20px 24px;
  text-align: center;
  margin-bottom: 20px;
}
.ref-label { font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em; color: var(--muted); display: block; margin-bottom: 8px; }
.ref-number-row { display: flex; align-items: center; justify-content: center; gap: 10px; }
.ref-number { font-size: 28px; font-weight: 900; color: var(--pri); font-family: monospace; letter-spacing: 2px; }
.copy-btn {
  display: flex; align-items: center; gap: 5px;
  background: none; border: 1.5px solid var(--pri);
  color: var(--pri); border-radius: 999px;
  padding: 5px 12px; font-size: 12px; font-weight: 700;
  cursor: pointer; transition: all 0.2s; font-family: inherit;
}
.copy-btn:hover { background: var(--pri); color: #fff; }
.ref-hint { font-size: 12px; color: var(--muted); margin: 8px 0 0; }

/* bank */
.bank-box {
  border: 1px solid var(--line);
  border-radius: 12px;
  padding: 20px 24px;
  margin-bottom: 20px;
}
.bank-title {
  font-size: 15px; font-weight: 700; color: var(--ink);
  margin: 0 0 14px;
  display: flex; align-items: center; gap: 8px;
}
.bank-title i { color: var(--pri); }
.bank-grid { display: flex; flex-direction: column; gap: 8px; }
.bank-row {
  display: flex; justify-content: space-between; align-items: center;
  font-size: 13.5px; padding: 8px 0;
  border-bottom: 1px dashed var(--line);
}
.bank-row:last-child { border-bottom: none; }
.bank-row span { color: var(--muted); }
.bank-row strong { color: var(--ink); }
.mono { font-family: monospace; letter-spacing: 0.5px; }

/* next steps */
.next-steps {
  background: rgba(0,89,227,0.05);
  border: 1px dashed rgba(0,89,227,0.2);
  border-radius: 10px;
  padding: 18px 20px;
  margin-bottom: 24px;
}
.next-steps h5 {
  font-size: 14px; font-weight: 700; color: var(--ink);
  margin: 0 0 10px;
  display: flex; align-items: center; gap: 7px;
}
.next-steps ol {
  margin: 0; padding-left: 20px;
  color: var(--ink-soft);
  font-size: 13.5px;
  line-height: 1.8;
}

/* actions */
.success-actions {
  display: flex; flex-wrap: wrap; gap: 12px;
}
.btn-wa {
  display: inline-flex; align-items: center; gap: 8px;
  background: #25D366; color: #fff;
  padding: 13px 24px; border-radius: 999px;
  font-size: 14px; font-weight: 700; text-decoration: none;
  transition: all 0.2s;
  box-shadow: 0 4px 14px rgba(37,211,102,0.3);
}
.btn-wa:hover { background: #1db855; transform: translateY(-1px); }
.btn-home {
  display: inline-flex; align-items: center; gap: 8px;
  border: 1.5px solid var(--line); background: none; color: var(--ink);
  padding: 12px 20px; border-radius: 999px;
  font-size: 14px; font-weight: 700; text-decoration: none;
  transition: all 0.2s;
}
.btn-home:hover { border-color: var(--ink); background: var(--ink); color: #fff; }
</style>