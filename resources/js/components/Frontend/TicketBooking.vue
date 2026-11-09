<template>
  <div>
    <section class="page-hero" style="padding-bottom:0;">
      <div class="container">
        <div class="eyebrow">Checkout</div>
        <h1 style="font-size:clamp(26px,3.6vw,38px);">Complete your booking</h1>
      </div>
    </section>

    <div class="container" style="padding:40px 28px 90px;">
      <div class="booking-steps">
        <div class="b-step" :class="{ active: step === 1, done: step > 1 }">Traveller Details</div>
        <div class="b-step" :class="{ active: step === 2, done: step > 2 }">Passport Info</div>
        <div class="b-step" :class="{ active: step === 3, done: step > 3 }">Payment</div>
        <div class="b-step" :class="{ active: step === 4 }">Confirmation</div>
      </div>

      <div class="detail-grid" style="padding:0;align-items:start;">
        <div>
          <!-- STEP 1: Traveller Details -->
          <div class="form-card" v-show="step === 1">
            <h2 style="font-size:22px;">Traveller Details</h2>
            <p style="font-size:14px;">Tell us who is travelling. You can add more travellers for group bookings.</p>
            <div class="form-grid" style="margin-top:20px;">
              <div class="form-group"><label>Full Name (as on Passport)</label><input type="text" v-model="form.fullName" placeholder="e.g. Ayesha Khan" required></div>
              <div class="form-group"><label>Date of Birth</label><input type="date" v-model="form.dob" required></div>
              <div class="form-group"><label>Gender</label><select v-model="form.gender"><option>Male</option><option>Female</option></select></div>
              <div class="form-group"><label>CNIC Number</label><input type="text" v-model="form.cnic" placeholder="XXXXX-XXXXXXX-X"></div>
              <div class="form-group"><label>Mobile Number</label><input type="tel" v-model="form.phone" placeholder="03XX-XXXXXXX" required></div>
              <div class="form-group"><label>Email Address</label><input type="email" v-model="form.email" placeholder="you@example.com" required></div>
              <div class="form-group full"><label>Number of Travellers</label><select v-model="form.travellerCount"><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5+</option></select></div>
            </div>
            <div style="display:flex;justify-content:flex-end;margin-top:26px;">
              <button class="btn btn-primary" @click="goStep(2)">Continue to Passport Info →</button>
            </div>
          </div>

          <!-- STEP 2: Passport Info -->
          <div class="form-card" v-show="step === 2">
            <h2 style="font-size:22px;">Passport &amp; Travel Details</h2>
            <p style="font-size:14px;">Enter passport details exactly as printed. This is used for visa/Umrah processing.</p>
            <div class="form-grid" style="margin-top:20px;">
              <div class="form-group"><label>Passport Number</label><input type="text" v-model="form.passportNo" placeholder="AB1234567" required></div>
              <div class="form-group"><label>Passport Issue Date</label><input type="date" v-model="form.passportIssue"></div>
              <div class="form-group"><label>Passport Expiry Date</label><input type="date" v-model="form.passportExpiry" required></div>
              <div class="form-group"><label>Nationality</label><input type="text" v-model="form.nationality"></div>
              <div class="form-group"><label>Departure City</label><select v-model="form.depCity"><option>Lahore</option><option>Karachi</option><option>Islamabad</option></select></div>
              <div class="form-group"><label>Preferred Travel Date</label><input type="date" v-model="form.travelDate"></div>
              <div class="form-group full"><label>Upload Passport Copy (optional for demo)</label><input type="file"></div>
            </div>
            <div style="display:flex;justify-content:space-between;margin-top:26px;">
              <button class="btn btn-ghost" @click="goStep(1)">← Back</button>
              <button class="btn btn-primary" @click="goStep(3)">Continue to Payment →</button>
            </div>
          </div>

          <!-- STEP 3: Payment -->
          <div class="form-card" v-show="step === 3">
            <h2 style="font-size:22px;">Revolut Payment</h2>
            <p style="font-size:14px;">Please transfer the total amount to our Revolut account below, then confirm your booking. Quote your booking reference with the transfer so we can match it instantly.</p>

            <div class="pay-panel" style="margin-top:18px;">
              <ul class="info-list">
                <li v-if="payment.account_holder"><b>Account Holder</b>{{ payment.account_holder }}</li>
                <li v-if="payment.account_number"><b>Account Number</b>{{ payment.account_number }}</li>
                <li v-if="payment.sort_code"><b>Sort Code</b>{{ payment.sort_code }}</li>
                <li v-if="payment.iban"><b>IBAN</b><span class="mono">{{ payment.iban }}</span></li>
                <li v-if="payment.bic_swift"><b>BIC / SWIFT</b>{{ payment.bic_swift }}</li>
                <li><b>Currency</b>{{ (payment.currency || 'EUR').toUpperCase() }}</li>
                <li><b>Amount Due</b><b style="color:var(--teal);">{{ totalPriceStr }}</b></li>
              </ul>
              <p v-if="payment.instructions" class="form-hint" style="margin-top:10px;">{{ payment.instructions }}</p>
              <a v-if="payment.payment_link" :href="payment.payment_link" target="_blank" rel="noopener" class="btn btn-primary mt" style="margin-top:14px;text-decoration:none;">Open Revolut & Transfer</a>
            </div>

            <div style="display:flex;justify-content:space-between;margin-top:26px;">
              <button class="btn btn-ghost" @click="goStep(2)">← Back</button>
              <button class="btn btn-primary" @click="confirmBooking">Confirm Booking →</button>
            </div>
          </div>

          <!-- STEP 4: Confirmation -->
          <div class="form-card" v-show="step === 4" style="text-align:center;">
            <div style="font-size:52px;margin-bottom:10px;">✅</div>
            <h2 style="font-size:24px;">Booking Confirmed!</h2>
            <p>Your booking reference is <b class="mono" style="color:var(--teal);">{{ bookingRef }}</b>. Please complete your Revolut payment as shown in the previous step — a confirmation with your ticket details has been sent to your email and WhatsApp.</p>
            <div style="display:flex;gap:14px;justify-content:center;margin-top:20px;">
              <router-link to="/" class="btn btn-ghost">Back to Home</router-link>
              <router-link to="/contact" class="btn btn-primary">Contact Support</router-link>
            </div>
          </div>
        </div>

        <!-- SUMMARY SIDEBAR -->
        <div class="booking-card">
          <div class="eyebrow">Order Summary</div>
          <h3 style="font-size:18px;">{{ itemTitle }}</h3>
          <p class="form-hint">{{ itemMeta }}</p>
          <div class="divider"></div>
          <div class="summary-row"><span>Base Price</span><span>{{ basePriceStr }} × {{ form.travellerCount }}</span></div>
          <div class="summary-row"><span>Travellers</span><span>{{ form.travellerCount }}</span></div>
          <div class="summary-row"><span>Service Fee</span><span>PKR 1,500</span></div>
          <div class="summary-row total"><span>Total Payable</span><span>{{ totalPriceStr }}</span></div>
          <p class="form-hint" style="margin-top:14px;">Prices include documentation handling. Airfare taxes may vary at time of ticketing.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { formatEUR } from '../../utils/currency.js';
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const router = useRouter();

const step = ref(1);
const form = ref({
  fullName: '', dob: '', gender: 'Male', cnic: '', phone: '', email: '', travellerCount: '1',
  passportNo: '', passportIssue: '', passportExpiry: '', nationality: 'Pakistani', depCity: 'Lahore', travelDate: ''
});
const payMethod = ref('revolut');
const bookingRef = ref('');
const payment = ref({});

const type = ref('visa');
const slug = ref('');
const item = ref(null);
const itemTitle = ref('Custom Enquiry');
const itemMeta = ref('');
const basePrice = ref(150);

onMounted(async () => {
  type.value = route.query.type || 'visa';
  slug.value = route.query.slug;

  try {
    if (type.value === 'visa') {
      const res = await axios.get('/api/visas/' + slug.value);
      item.value = res.data;
      itemTitle.value = item.value.country + ' Visa';
      itemMeta.value = item.value.type + ' · ' + item.value.duration;
      basePrice.value = parseFloat(item.value.price);
    } else if (type.value === 'umrah') {
      const res = await axios.get('/api/umrah-packages/' + slug.value);
      item.value = res.data;
      itemTitle.value = item.value.title;
      itemMeta.value = item.value.nights + ' · ' + item.value.city;
      basePrice.value = parseFloat(item.value.price);
    } else if (type.value === 'hotel') {
      const res = await axios.get('/api/hotels/' + slug.value);
      item.value = res.data;
      itemTitle.value = item.value.name;
      itemMeta.value = item.value.city + ' · per night';
      basePrice.value = parseFloat(item.value.price);
    }
  } catch(err) {
    console.error('Could not load item data', err);
  }

  try {
    const payRes = await axios.get('/api/payment-settings');
    payment.value = payRes.data || {};
  } catch(err) {
    console.error('Could not load payment settings', err);
  }
});

const basePriceStr = computed(() => 'PKR ' + basePrice.value.toLocaleString("en-PK"));
const totalPrice = computed(() => (basePrice.value * parseInt(form.value.travellerCount)) + 15);
const totalPriceStr = computed(() => 'PKR ' + totalPrice.value.toLocaleString("en-PK"));

const goStep = (n) => {
  if (n === 2) {
    if (!form.value.fullName || !form.value.dob || !form.value.phone || !form.value.email) {
      if(window.sanguToast) window.sanguToast('Please fill in all required traveller details');
      else alert('Please fill in all required traveller details');
      return;
    }
  }
  if (n === 3) {
    if (!form.value.passportNo || !form.value.passportExpiry) {
      if(window.sanguToast) window.sanguToast('Please enter your passport number and expiry date');
      else alert('Please enter your passport number and expiry date');
      return;
    }
  }
  step.value = n;
  window.scrollTo({top:0, behavior:'smooth'});
};

const confirmBooking = async () => {
  try {
    const res = await axios.post('/api/bookings', {
      type: type.value,
      title: itemTitle.value,
      name: form.value.fullName,
      email: form.value.email,
      phone: form.value.phone,
      travellers: parseInt(form.value.travellerCount),
      total: totalPriceStr.value,
    });
    bookingRef.value = res.data.booking_reference;
    step.value = 4;
    window.scrollTo({top:0, behavior:'smooth'});
  } catch(err) {
    console.error('Booking failed', err);
    alert('Booking failed. Please try again.');
  }
};
</script>
