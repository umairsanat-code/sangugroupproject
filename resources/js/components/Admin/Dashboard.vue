<template>
  <div>
    <div class="stat-cards">
      <div class="stat-card">
        <div class="label">Total Bookings</div>
        <div class="value">{{ bookings.length }}</div>
        <div class="delta up">↑ Live from bookings</div>
      </div>
      <div class="stat-card">
        <div class="label">Pending Review</div>
        <div class="value">{{ pending }}</div>
        <div class="delta" style="color:#a1720f;">Needs action</div>
      </div>
      <div class="stat-card">
        <div class="label">Confirmed</div>
        <div class="value">{{ confirmed }}</div>
        <div class="delta up">↑ Ready to process</div>
      </div>
      <div class="stat-card">
        <div class="label">Est. Revenue</div>
        <div class="value">€ {{ revenue.toLocaleString('de-DE') }}</div>
        <div class="delta up">↑ Across all bookings</div>
      </div>
    </div>

    <div class="admin-panel-card">
      <h2>Recent Bookings</h2>
      <table class="admin-table">
        <thead>
          <tr>
            <th>Reference</th>
            <th>Type</th>
            <th>Customer</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Date</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="b in recentBookings" :key="b.booking_reference || b.ref">
            <td class="mono">{{ b.booking_reference || b.ref }}</td>
            <td style="text-transform:capitalize;">{{ b.booking_type || b.type }}</td>
            <td>{{ b.first_name || b.name || '-' }}</td>
            <td>{{ b.total_amount ? '€ ' + parseFloat(b.total_amount).toLocaleString('de-DE') : b.total }}</td>
            <td><span class="status-pill" :class="statusClass(b.booking_status || b.status)">{{ b.booking_status || b.status }}</span></td>
            <td>{{ b.created_at ? b.created_at.slice(0,10) : b.date }}</td>
          </tr>
          <tr v-if="recentBookings.length === 0">
            <td colspan="6" class="empty-state">No bookings yet</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="grid grid-3">
      <div class="admin-panel-card">
        <h2 style="font-size:14px;color:var(--ink-soft);">Visa Bookings</h2>
        <div class="value" style="font-family:'Fraunces',serif;font-size:26px;">{{ visaCount }}</div>
      </div>
      <div class="admin-panel-card">
        <h2 style="font-size:14px;color:var(--ink-soft);">Umrah Bookings</h2>
        <div class="value" style="font-family:'Fraunces',serif;font-size:26px;">{{ umrahCount }}</div>
      </div>
      <div class="admin-panel-card">
        <h2 style="font-size:14px;color:var(--ink-soft);">Hotel Bookings</h2>
        <div class="value" style="font-family:'Fraunces',serif;font-size:26px;">{{ hotelCount }}</div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

const bookings = ref([]);

onMounted(async () => {
  try {
    const res = await axios.get('/api/bookings');
    bookings.value = res.data;
  } catch(err) {
    bookings.value = JSON.parse(localStorage.getItem('sangu_bookings') || '[]');
  }
});

const pending = computed(() => bookings.value.filter(b => (b.booking_status || b.status) === 'Pending Review').length);
const confirmed = computed(() => bookings.value.filter(b => (b.booking_status || b.status) === 'Confirmed').length);
const revenue = computed(() => bookings.value.filter(b => (b.booking_status || b.status) !== 'Cancelled').reduce((sum, b) => {
  const amt = b.total_amount ? parseFloat(b.total_amount) : parseInt((b.total || '0').replace(/[^\d]/g, ''));
  return sum + (amt || 0);
}, 0));

const visaCount = computed(() => bookings.value.filter(b => (b.booking_type || b.type) === 'visa').length);
const umrahCount = computed(() => bookings.value.filter(b => (b.booking_type || b.type) === 'umrah').length);
const hotelCount = computed(() => bookings.value.filter(b => (b.booking_type || b.type) === 'hotel').length);

const recentBookings = computed(() => bookings.value.slice(0, 6));

const statusClass = (status) => {
  if (status === 'Confirmed') return 'success';
  if (status === 'Pending Review') return 'warning';
  if (status === 'Cancelled') return 'danger';
  return '';
};
</script>
