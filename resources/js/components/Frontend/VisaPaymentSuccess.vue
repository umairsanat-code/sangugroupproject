<template>
  <div class="visa-success-page">
    <div class="container" style="max-width: 760px; padding: 60px 20px 100px;">
      
      <!-- LOADING STATE -->
      <div v-if="loading" class="card text-center p-5 shadow-sm border-0" style="border-radius: 20px;">
        <div class="spinner-border text-primary my-4" role="status" style="width: 3.5rem; height: 3.5rem;"></div>
        <h3 class="fw-bold text-dark">Verifying Your Visa Payment...</h3>
        <p class="text-muted">Please wait a moment while we securely confirm your payment with Stripe.</p>
      </div>

      <!-- SUCCESS CONFIRMATION -->
      <div v-else-if="booking" class="card border-0 shadow-lg overflow-hidden" style="border-radius: 24px;">
        <div class="success-banner text-center p-4 p-md-5 text-white position-relative" style="background: linear-gradient(135deg, #0d1b2a 0%, #1b263b 60%, #0077b6 100%);">
          <div class="badge-icon mb-3 mx-auto">
            <i class="fas fa-check-circle"></i>
          </div>
          <span class="badge bg-warning text-dark px-3 py-2 text-uppercase fw-bold mb-2" style="letter-spacing: 1px; font-size: 11px;">
            Payment Confirmed
          </span>
          <h1 class="h2 fw-bold mb-2">Visa Application Received!</h1>
          <p class="mb-0 text-white-50">Thank you, {{ booking.first_name }}. Your application and payment have been verified.</p>
        </div>

        <div class="card-body p-4 p-md-5 bg-white">
          <!-- REFERENCE BOX -->
          <div class="ref-card p-3 p-md-4 mb-4 rounded-3 text-center border" style="background-color: #f8faff; border-color: #dbeafe !important;">
            <div class="text-uppercase text-muted fw-bold small mb-1" style="letter-spacing: 1px;">Application Reference</div>
            <div class="d-flex align-items-center justify-center gap-2">
              <span class="h2 fw-bold text-primary mb-0 font-monospace">{{ booking.booking_reference }}</span>
              <button class="btn btn-sm btn-outline-primary rounded-pill px-3" @click="copyReference" :title="copied ? 'Copied!' : 'Copy Reference'">
                <i :class="copied ? 'fas fa-check text-success' : 'fas fa-copy'"></i>
                <span class="ms-1">{{ copied ? 'Copied' : 'Copy' }}</span>
              </button>
            </div>
            <small class="text-muted d-block mt-2">Quote this reference number in all inquiries regarding your application.</small>
          </div>

          <!-- APPLICATION DETAILS SUMMARY -->
          <h5 class="fw-bold text-dark mb-3">Application Summary</h5>
          <div class="row g-3 mb-4">
            <div class="col-sm-6">
              <div class="detail-box p-3 rounded border">
                <span class="text-muted small d-block">Destination Country</span>
                <strong class="text-dark fs-6">{{ booking.destination_country || '—' }}</strong>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="detail-box p-3 rounded border">
                <span class="text-muted small d-block">Visa Category</span>
                <strong class="text-dark fs-6">{{ booking.visa_type || '—' }}</strong>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="detail-box p-3 rounded border">
                <span class="text-muted small d-block">Applicant Name</span>
                <strong class="text-dark fs-6">{{ booking.first_name }}</strong>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="detail-box p-3 rounded border">
                <span class="text-muted small d-block">Travel Date</span>
                <strong class="text-dark fs-6">{{ booking.travel_date ? formatDate(booking.travel_date) : 'Flexible / TBD' }}</strong>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="detail-box p-3 rounded border">
                <span class="text-muted small d-block">Payment Status</span>
                <span class="badge bg-success-subtle text-success fw-bold px-2 py-1">
                  <i class="fas fa-check me-1"></i> {{ booking.payment_status }}
                </span>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="detail-box p-3 rounded border">
                <span class="text-muted small d-block">Total Paid</span>
                <strong class="text-primary fs-5">{{ booking.total_amount }} {{ booking.currency || 'EUR' }}</strong>
              </div>
            </div>
          </div>

          <!-- NEXT STEPS -->
          <div class="next-steps-card p-4 rounded-3 mb-4" style="background-color: #fcfcfd; border: 1px dashed #cbd5e1;">
            <h6 class="fw-bold text-dark mb-3"><i class="fas fa-list-check text-primary me-2"></i>What Happens Next?</h6>
            <ol class="mb-0 ps-3 text-secondary" style="line-height: 1.8;">
              <li>A payment receipt and confirmation email has been dispatched to <b>{{ booking.email }}</b>.</li>
              <li>Our dedicated visa operations officer is reviewing your submission and verifying visa requirements.</li>
              <li>We will contact you via WhatsApp / phone on <b>{{ booking.phone }}</b> within 2 to 4 business hours to collect any remaining documents.</li>
            </ol>
          </div>

          <!-- ACTION BUTTONS -->
          <div class="d-flex flex-column flex-sm-row gap-3 pt-2">
            <a :href="whatsAppUrl" target="_blank" rel="noopener" class="btn btn-success flex-grow-1 py-3 fw-bold rounded-pill text-white shadow-sm">
              <i class="fab fa-whatsapp fs-5 align-middle me-2"></i> Chat with Visa Team
            </a>
            <button @click="printReceipt" class="btn btn-outline-secondary py-3 px-4 fw-bold rounded-pill">
              <i class="fas fa-print me-2"></i> Print Receipt
            </button>
            <router-link to="/" class="btn btn-outline-dark py-3 px-4 fw-bold rounded-pill">
              Home
            </router-link>
          </div>
        </div>
      </div>

      <!-- ERROR / CANCELLED STATE -->
      <div v-else class="card border-0 shadow-sm p-5 text-center" style="border-radius: 20px;">
        <div class="text-warning display-4 mb-3"><i class="fas fa-exclamation-circle"></i></div>
        <h3 class="fw-bold text-dark">Application Status</h3>
        <p class="text-muted max-w-md mx-auto mb-4">
          {{ errorMessage || 'We could not verify the payment for this session. If you cancelled the payment, you can return to the application to try again.' }}
        </p>
        <div class="d-flex justify-content-center gap-3">
          <router-link to="/visa" class="btn btn-primary rounded-pill px-4 py-2">
            Explore Visas
          </router-link>
          <a :href="whatsAppUrl" target="_blank" rel="noopener" class="btn btn-outline-success rounded-pill px-4 py-2">
            <i class="fab fa-whatsapp me-1"></i> Contact Support
          </a>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const loading = ref(true);
const booking = ref(null);
const errorMessage = ref('');
const copied = ref(false);

const sessionId = route.query.session_id || '';
const refQuery = route.query.ref || '';

const whatsAppUrl = computed(() => {
  const refNum = booking.value ? booking.value.booking_reference : (refQuery || '');
  const name = booking.value ? booking.value.first_name : '';
  const country = booking.value ? booking.value.destination_country : '';
  const text = encodeURIComponent(`Hola SanguGroup, he completado mi solicitud de visado ${country} (Ref: ${refNum}, Nombre: ${name}). Deseo coordinar el trámite.`);
  return `https://wa.me/34672068148?text=${text}`;
});

const formatDate = (dateStr) => {
  if (!dateStr) return '';
  try {
    const d = new Date(dateStr);
    return d.toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
  } catch (e) {
    return dateStr;
  }
};

const copyReference = () => {
  if (!booking.value?.booking_reference) return;
  navigator.clipboard.writeText(booking.value.booking_reference).then(() => {
    copied.value = true;
    setTimeout(() => { copied.value = false; }, 2500);
  });
};

const printReceipt = () => {
  window.print();
};

const verifyPayment = async () => {
  loading.value = true;
  try {
    const res = await axios.get('/api/visa/verify-payment', {
      params: {
        session_id: sessionId,
        ref: refQuery
      }
    });

    if (res.data && res.data.booking) {
      booking.value = res.data.booking;
    } else {
      errorMessage.value = 'Could not find booking record.';
    }
  } catch (err) {
    console.error('Payment verification error:', err);
    errorMessage.value = err.response?.data?.message || 'Verification could not be completed.';
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  verifyPayment();
});
</script>

<style scoped>
.visa-success-page {
  min-height: 80vh;
  background-color: #f8fafc;
}
.badge-icon {
  width: 72px;
  height: 72px;
  background: rgba(255, 255, 255, 0.15);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 38px;
  color: #38bdf8;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
}
.detail-box {
  background: #ffffff;
  border-color: #e2e8f0 !important;
  transition: all 0.2s ease;
}
.detail-box:hover {
  border-color: #cbd5e1 !important;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
}
@media print {
  .btn, .next-steps-card, .badge-icon {
    display: none !important;
  }
  .visa-success-page {
    background: #fff !important;
  }
}
</style>
