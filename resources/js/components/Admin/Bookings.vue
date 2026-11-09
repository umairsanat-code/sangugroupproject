<template>
  <div class="admin-panel-card">
    
    <!-- TOP SUMMARY METRICS -->
    <div class="stats-grid mb-4">
      <div class="stat-card">
        <div class="stat-icon bg-blue-subtle text-primary"><i class="fas fa-passport"></i></div>
        <div>
          <div class="stat-val">{{ visaCount }}</div>
          <div class="stat-lbl">Visa Applications</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon bg-green-subtle text-success"><i class="fas fa-check-circle"></i></div>
        <div>
          <div class="stat-val">{{ paidCount }}</div>
          <div class="stat-lbl">Paid / Verified</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon bg-yellow-subtle text-warning"><i class="fas fa-clock"></i></div>
        <div>
          <div class="stat-val">{{ pendingCount }}</div>
          <div class="stat-lbl">Pending Review</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon bg-purple-subtle text-purple"><i class="fas fa-euro-sign"></i></div>
        <div>
          <div class="stat-val">{{ totalRevenueFormatted }}</div>
          <div class="stat-lbl">Total Volume</div>
        </div>
      </div>
    </div>

    <!-- FILTER & SEARCH BAR -->
    <div class="results-bar d-flex flex-wrap align-items-center gap-2 mb-3">
      <div class="search-wrap flex-grow-1" style="min-width: 240px; max-width: 380px;">
        <input
          type="text"
          v-model="searchQuery"
          placeholder="Search by name, ref, email, destination..."
          class="form-control"
          style="border: 1px solid var(--line); border-radius: 8px; padding: 9px 14px;"
        />
      </div>

      <select v-model="typeFilter" class="form-select filter-select">
        <option value="">All Services</option>
        <option value="visa">Visa Applications</option>
        <option value="umrah">Umrah Packages</option>
        <option value="hotel">Hotels</option>
      </select>

      <select v-model="statusFilter" class="form-select filter-select">
        <option value="">All App Statuses</option>
        <option value="Pending Review">Pending Review</option>
        <option value="Pending Payment">Pending Payment</option>
        <option value="Processing">Processing</option>
        <option value="Approved">Approved</option>
        <option value="Confirmed">Confirmed</option>
        <option value="Rejected">Rejected</option>
        <option value="Cancelled">Cancelled</option>
      </select>

      <select v-model="paymentStatusFilter" class="form-select filter-select">
        <option value="">All Payment Statuses</option>
        <option value="Pending">Pending Payment</option>
        <option value="Paid">Paid</option>
        <option value="Failed">Failed</option>
        <option value="Refunded">Refunded</option>
      </select>

      <!-- CSV Export Button -->
      <button class="btn btn-outline-primary ms-auto d-flex align-items-center gap-1" @click="exportCsv">
        <i class="fas fa-file-csv"></i> Export CSV
      </button>
      
      <!-- Refresh Button -->
      <button class="btn btn-outline-secondary d-flex align-items-center gap-1" @click="loadBookings">
        <i class="fas fa-sync-alt" :class="{ 'fa-spin': loading }"></i>
      </button>
    </div>

    <!-- TABLE -->
    <div class="table-responsive">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Reference</th>
            <th>Date</th>
            <th>Type</th>
            <th>Applicant / Customer</th>
            <th>Destination / Item</th>
            <th>Amount</th>
            <th>Payment Status</th>
            <th>App Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="b in filteredBookings" :key="b.booking_reference || b.id">
            <td class="mono fw-bold text-primary">
              {{ b.booking_reference }}
              <div v-if="b.stripe_payment_intent_id" class="small text-muted font-monospace" style="font-size: 10px;">
                <i class="fab fa-stripe text-info"></i> {{ b.stripe_payment_intent_id.substring(0, 14) }}...
              </div>
            </td>
            <td class="small text-muted text-nowrap">
              {{ formatDate(b.created_at || b.booking_date) }}
            </td>
            <td>
              <span class="badge" :class="typeBadgeClass(b.booking_type)">
                {{ b.booking_type || 'Booking' }}
              </span>
            </td>
            <td>
              <div class="fw-bold text-dark">{{ b.first_name || b.name || '—' }}</div>
              <div class="small text-muted">
                <span v-if="b.email">{{ b.email }}</span>
                <span v-if="b.phone"> · {{ b.phone }}</span>
              </div>
              <div v-if="b.nationality" class="small text-secondary">
                <i class="fas fa-flag text-muted me-1"></i>{{ b.nationality }}
              </div>
            </td>
            <td>
              <div class="fw-semibold text-dark">
                {{ b.destination_country ? b.destination_country + ' - ' + (b.visa_type || 'Visa') : (b.item_name || '—') }}
              </div>
              <div v-if="b.travel_date" class="small text-muted">
                <i class="fas fa-plane-departure text-muted me-1"></i>Travel: {{ formatDate(b.travel_date) }}
              </div>
            </td>
            <td class="fw-bold text-nowrap">
              {{ b.total_amount || b.total }} {{ b.currency || 'EUR' }}
            </td>
            <td>
              <span class="status-pill" :class="paymentStatusClass(b.payment_status)">
                <i v-if="isPaid(b.payment_status)" class="fas fa-check-circle me-1"></i>
                {{ b.payment_status || 'Pending' }}
              </span>
              <div v-if="b.payment_method" class="text-uppercase text-muted" style="font-size: 10px; font-weight: 700;">
                {{ b.payment_method }}
              </div>
            </td>
            <td>
              <span class="status-pill" :class="appStatusClass(b.booking_status || b.status)">
                {{ b.booking_status || b.status || 'Pending' }}
              </span>
            </td>
            <td>
              <div class="d-flex align-items-center gap-1">
                <button class="btn btn-sm btn-light border px-2 py-1" @click="openDetails(b)" title="View Details">
                  <i class="fas fa-eye text-primary"></i>
                </button>
                <a
                  v-if="b.phone"
                  :href="getWhatsAppLink(b)"
                  target="_blank"
                  rel="noopener"
                  class="btn btn-sm btn-light border px-2 py-1 text-success"
                  title="Chat on WhatsApp"
                >
                  <i class="fab fa-whatsapp"></i>
                </a>
              </div>
            </td>
          </tr>
          <tr v-if="filteredBookings.length === 0">
            <td colspan="9" class="text-center py-5 text-muted">
              <i class="fas fa-inbox fs-2 mb-2 d-block"></i>
              No applications or bookings match your filters
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- DETAILS & STATUS UPDATE MODAL -->
    <div v-if="selectedBooking" class="modal-backdrop-custom" @click.self="selectedBooking = null">
      <div class="modal-dialog-custom">
        <div class="modal-content shadow-lg border-0 rounded-4 overflow-hidden">
          
          <div class="modal-header bg-dark text-white p-4">
            <div>
              <div class="badge bg-gold-subtle text-gold text-uppercase fw-bold mb-1" style="font-size: 11px;">
                {{ selectedBooking.booking_type || 'Visa' }} Application
              </div>
              <h4 class="modal-title fw-bold mb-0">Reference: {{ selectedBooking.booking_reference }}</h4>
            </div>
            <button type="button" class="btn-close btn-close-white" @click="selectedBooking = null"></button>
          </div>

          <div class="modal-body p-4">
            <!-- APPLICANT SUMMARY -->
            <div class="card bg-light border-0 p-3 rounded-3 mb-4">
              <div class="row g-3">
                <div class="col-sm-6">
                  <span class="text-muted small d-block">Applicant Name</span>
                  <strong class="text-dark">{{ selectedBooking.first_name }} {{ selectedBooking.last_name || '' }}</strong>
                </div>
                <div class="col-sm-6">
                  <span class="text-muted small d-block">Email Address</span>
                  <a :href="'mailto:' + selectedBooking.email" class="text-primary fw-semibold">{{ selectedBooking.email }}</a>
                </div>
                <div class="col-sm-6">
                  <span class="text-muted small d-block">Phone / WhatsApp</span>
                  <a :href="getWhatsAppLink(selectedBooking)" target="_blank" class="text-success fw-semibold">
                    <i class="fab fa-whatsapp me-1"></i> {{ selectedBooking.phone }}
                  </a>
                </div>
                <div class="col-sm-6">
                  <span class="text-muted small d-block">Nationality</span>
                  <strong class="text-dark">{{ selectedBooking.nationality || '—' }}</strong>
                </div>
                <div class="col-sm-6">
                  <span class="text-muted small d-block">Destination Country</span>
                  <strong class="text-primary">{{ selectedBooking.destination_country || selectedBooking.item_name || '—' }}</strong>
                </div>
                <div class="col-sm-6">
                  <span class="text-muted small d-block">Visa Category</span>
                  <strong class="text-dark">{{ selectedBooking.visa_type || '—' }}</strong>
                </div>
                <div class="col-sm-6">
                  <span class="text-muted small d-block">Travel Date</span>
                  <strong class="text-dark">{{ formatDate(selectedBooking.travel_date) || 'Flexible / Undecided' }}</strong>
                </div>
                <div class="col-sm-6">
                  <span class="text-muted small d-block">Submission Date</span>
                  <span class="text-dark">{{ formatDate(selectedBooking.created_at) }}</span>
                </div>
              </div>
            </div>

            <!-- PAYMENT INFORMATION -->
            <div class="card border p-3 rounded-3 mb-4">
              <h6 class="fw-bold text-dark mb-3"><i class="fas fa-credit-card text-primary me-2"></i>Payment Details</h6>
              <div class="row g-3">
                <div class="col-sm-4">
                  <span class="text-muted small d-block">Total Amount</span>
                  <strong class="text-primary fs-5">{{ selectedBooking.total_amount }} {{ selectedBooking.currency || 'EUR' }}</strong>
                </div>
                <div class="col-sm-4">
                  <span class="text-muted small d-block">Payment Method</span>
                  <span class="badge bg-secondary-subtle text-secondary text-uppercase">{{ selectedBooking.payment_method || '—' }}</span>
                </div>
                <div class="col-sm-4">
                  <span class="text-muted small d-block">Payment Status</span>
                  <span class="status-pill" :class="paymentStatusClass(selectedBooking.payment_status)">{{ selectedBooking.payment_status }}</span>
                </div>
                <div class="col-12" v-if="selectedBooking.stripe_payment_intent_id || selectedBooking.stripe_session_id">
                  <span class="text-muted small d-block">Stripe Transaction Reference</span>
                  <div class="font-monospace small bg-light p-2 rounded border text-break">
                    {{ selectedBooking.stripe_payment_intent_id || selectedBooking.stripe_session_id }}
                  </div>
                </div>
              </div>
            </div>

            <!-- UPLOADED DOCUMENTS -->
            <div
              v-if="selectedBooking.additional_data?.applicant_photo || selectedBooking.additional_data?.passport_copy"
              class="card border p-3 rounded-3 mb-4"
            >
              <h6 class="fw-bold text-dark mb-3"><i class="fas fa-paperclip text-primary me-2"></i>Uploaded Documents</h6>
              <div class="d-flex flex-wrap gap-3">
                <a
                  v-if="selectedBooking.additional_data?.applicant_photo"
                  :href="'/storage/' + selectedBooking.additional_data.applicant_photo"
                  target="_blank"
                  class="btn btn-outline-primary btn-sm"
                >
                  <i class="fas fa-user-circle me-1"></i> View Applicant Photo
                </a>
                <a
                  v-if="selectedBooking.additional_data?.passport_copy"
                  :href="'/storage/' + selectedBooking.additional_data.passport_copy"
                  target="_blank"
                  class="btn btn-outline-secondary btn-sm"
                >
                  <i class="fas fa-passport me-1"></i> View Passport Copy
                </a>
              </div>
            </div>

            <!-- STATUS UPDATE FORM -->
            <div class="card border border-primary-subtle p-3 rounded-3 bg-light">
              <h6 class="fw-bold text-dark mb-3"><i class="fas fa-sliders-h text-primary me-2"></i>Update Application Status</h6>
              <div class="row g-3">
                <div class="col-sm-6">
                  <label class="form-label small fw-bold text-dark">Application Status</label>
                  <select v-model="editForm.booking_status" class="form-select">
                    <option value="Pending Payment">Pending Payment</option>
                    <option value="Pending Review">Pending Review</option>
                    <option value="Processing">Processing</option>
                    <option value="Approved">Approved</option>
                    <option value="Confirmed">Confirmed</option>
                    <option value="Rejected">Rejected</option>
                    <option value="Cancelled">Cancelled</option>
                  </select>
                </div>
                <div class="col-sm-6">
                  <label class="form-label small fw-bold text-dark">Payment Status</label>
                  <select v-model="editForm.payment_status" class="form-select">
                    <option value="Pending">Pending</option>
                    <option value="Paid">Paid</option>
                    <option value="Failed">Failed</option>
                    <option value="Refunded">Refunded</option>
                  </select>
                </div>
              </div>

              <div class="d-flex justify-content-end gap-2 mt-3 pt-2">
                <button type="button" class="btn btn-secondary px-3" @click="selectedBooking = null">Cancel</button>
                <button type="button" class="btn btn-primary px-4 fw-bold" :disabled="saving" @click="saveBookingStatus">
                  <i v-if="saving" class="fas fa-spinner fa-spin me-1"></i> Save Changes
                </button>
              </div>
            </div>

          </div>

          <div class="modal-footer bg-light p-3">
            <a :href="getWhatsAppLink(selectedBooking)" target="_blank" class="btn btn-outline-success">
              <i class="fab fa-whatsapp me-1"></i> Contact via WhatsApp
            </a>
            <button type="button" class="btn btn-dark" @click="selectedBooking = null">Close</button>
          </div>

        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

const bookings = ref([]);
const loading = ref(false);
const saving = ref(false);

const searchQuery = ref('');
const typeFilter = ref('');
const statusFilter = ref('');
const paymentStatusFilter = ref('');

const selectedBooking = ref(null);
const editForm = ref({
  booking_status: '',
  payment_status: '',
});

const loadBookings = async () => {
  loading.value = true;
  try {
    const res = await axios.get('/api/bookings');
    bookings.value = Array.isArray(res.data) ? res.data : [];
  } catch(err) {
    console.error('Failed to load bookings:', err);
    bookings.value = JSON.parse(localStorage.getItem('sangu_bookings') || '[]');
  } finally {
    loading.value = false;
  }
};

onMounted(loadBookings);

// Metrics
const visaCount = computed(() => {
  return bookings.value.filter(b => (b.booking_type || b.type) === 'visa').length;
});

const paidCount = computed(() => {
  return bookings.value.filter(b => isPaid(b.payment_status)).length;
});

const pendingCount = computed(() => {
  return bookings.value.filter(b => {
    const s = (b.booking_status || b.status || '').toLowerCase();
    return s.includes('pending') || s === 'processing';
  }).length;
});

const totalRevenueFormatted = computed(() => {
  const sum = bookings.value
    .filter(b => isPaid(b.payment_status))
    .reduce((acc, b) => acc + (parseFloat(b.total_amount || b.total || 0)), 0);
  return '€' + sum.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
});

// Filter logic
const filteredBookings = computed(() => {
  const q = searchQuery.value.toLowerCase().trim();
  const t = typeFilter.value.toLowerCase();
  const s = statusFilter.value.toLowerCase();
  const ps = paymentStatusFilter.value.toLowerCase();

  return bookings.value.filter(b => {
    const bType = (b.booking_type || b.type || '').toLowerCase();
    const bStatus = (b.booking_status || b.status || '').toLowerCase();
    const bPayment = (b.payment_status || '').toLowerCase();

    if (t && bType !== t) return false;
    if (s && bStatus !== s) return false;
    if (ps && bPayment !== ps) return false;

    if (q) {
      const ref = (b.booking_reference || b.ref || '').toLowerCase();
      const name = (b.first_name || b.name || '').toLowerCase();
      const email = (b.email || '').toLowerCase();
      const phone = (b.phone || '').toLowerCase();
      const dest = (b.destination_country || b.item_name || '').toLowerCase();

      if (!ref.includes(q) && !name.includes(q) && !email.includes(q) && !phone.includes(q) && !dest.includes(q)) {
        return false;
      }
    }
    return true;
  });
});

// Modal Actions
const openDetails = (b) => {
  selectedBooking.value = b;
  editForm.value.booking_status = b.booking_status || b.status || 'Pending Review';
  editForm.value.payment_status = b.payment_status || 'Pending';
};

const saveBookingStatus = async () => {
  if (!selectedBooking.value) return;
  saving.value = true;
  const refId = selectedBooking.value.booking_reference || selectedBooking.value.id;

  try {
    await axios.put('/api/bookings/' + refId + '/status', {
      booking_status: editForm.value.booking_status,
      payment_status: editForm.value.payment_status,
    });

    selectedBooking.value.booking_status = editForm.value.booking_status;
    selectedBooking.value.status = editForm.value.booking_status;
    selectedBooking.value.payment_status = editForm.value.payment_status;

    await loadBookings();
    selectedBooking.value = null;
  } catch (err) {
    console.error('Failed to update status:', err);
    alert('Failed to update status. Please try again.');
  } finally {
    saving.value = false;
  }
};

const exportCsv = () => {
  const params = new URLSearchParams();
  if (typeFilter.value) params.append('type', typeFilter.value);
  if (statusFilter.value) params.append('status', statusFilter.value);
  if (paymentStatusFilter.value) params.append('payment_status', paymentStatusFilter.value);
  if (searchQuery.value) params.append('search', searchQuery.value);

  window.open('/api/bookings/export-csv?' + params.toString(), '_blank');
};

const isPaid = (status) => {
  return String(status || '').toLowerCase() === 'paid';
};

const formatDate = (dateStr) => {
  if (!dateStr) return '';
  try {
    const d = new Date(dateStr);
    return d.toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
  } catch (e) {
    return dateStr;
  }
};

const getWhatsAppLink = (b) => {
  if (!b || !b.phone) return '#';
  const cleanPhone = b.phone.replace(/[^\d+]/g, '');
  const ref = b.booking_reference || b.ref || '';
  const text = encodeURIComponent(`Hola ${b.first_name || ''}, le contactamos de SanguGroup respecto a su solicitud ${ref}.`);
  return `https://wa.me/${cleanPhone.replace('+', '')}?text=${text}`;
};

const typeBadgeClass = (type) => {
  const t = String(type || '').toLowerCase();
  if (t === 'visa') return 'bg-info-subtle text-info';
  if (t === 'umrah') return 'bg-warning-subtle text-warning';
  if (t === 'hotel') return 'bg-success-subtle text-success';
  return 'bg-secondary-subtle text-secondary';
};

const paymentStatusClass = (status) => {
  const s = String(status || '').toLowerCase();
  if (s === 'paid') return 'success';
  if (s === 'pending') return 'warning';
  if (s === 'failed') return 'danger';
  if (s === 'refunded') return 'danger';
  return '';
};

const appStatusClass = (status) => {
  const s = String(status || '').toLowerCase();
  if (s.includes('approved') || s.includes('confirmed')) return 'success';
  if (s.includes('processing')) return 'info';
  if (s.includes('pending')) return 'warning';
  if (s.includes('rejected') || s.includes('cancelled')) return 'danger';
  return '';
};
</script>

<style scoped>
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 16px;
}
.stat-card {
  background: #ffffff;
  border: 1px solid var(--line, #e2e8f0);
  border-radius: 12px;
  padding: 16px 20px;
  display: flex;
  align-items: center;
  gap: 16px;
}
.stat-icon {
  width: 46px;
  height: 46px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
}
.bg-blue-subtle { background-color: #e0f2fe; }
.bg-green-subtle { background-color: #dcfce7; }
.bg-yellow-subtle { background-color: #fef9c3; }
.bg-purple-subtle { background-color: #f3e8ff; }
.text-purple { color: #9333ea; }
.stat-val {
  font-size: 22px;
  font-weight: 800;
  color: #0f172a;
  line-height: 1.1;
}
.stat-lbl {
  font-size: 12px;
  font-weight: 600;
  color: #64748b;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}
.filter-select {
  border: 1px solid var(--line, #e2e8f0);
  border-radius: 8px;
  padding: 8px 12px;
  font-size: 13px;
  max-width: 190px;
}
.bg-gold-subtle {
  background-color: rgba(197, 160, 89, 0.2);
}
.text-gold {
  color: #e5b869;
}
.modal-backdrop-custom {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(15, 23, 42, 0.65);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1050;
  padding: 20px;
}
.modal-dialog-custom {
  max-width: 680px;
  width: 100%;
  max-height: 90vh;
  overflow-y: auto;
}
</style>
