<template>
  <div>
    <section class="page-hero">
      <div class="page-hero-bg">
        <img src="https://images.unsplash.com/photo-1521737711867-e3b97375f902?w=1920&q=80&auto=format&fit=crop" alt="Contact Support">
        <div class="page-hero-shade"></div>
      </div>
      <div class="page-hero-content container">
        <div class="eyebrow" style="margin-bottom:12px;">Contact Us</div>
        <h1 class="page-hero-title">We're here to help, <em>every step</em></h1>
        <p class="page-hero-sub">Questions about a visa, an existing booking, or a group Umrah departure? Reach out — our team replies within a few hours.</p>
      </div>
    </section>

    <section>
      <div class="container contact-grid">
        <div class="form-card">
          <h2 style="font-size:20px;">Send us a message</h2>
          <form @submit.prevent="submitForm">
            <div class="form-grid" style="margin-top:16px;">
              <div class="form-group"><label>Full Name</label><input type="text" v-model="form.name" placeholder="Your name" required></div>
              <div class="form-group"><label>Mobile Number</label><input type="tel" v-model="form.phone" placeholder="03XX-XXXXXXX" required></div>
              <div class="form-group full"><label>Email Address</label><input type="email" v-model="form.email" placeholder="you@example.com" required></div>
              <div class="form-group full"><label>Subject</label>
                <select v-model="form.subject" required>
                  <option>Visa Enquiry</option>
                  <option>Umrah Package Enquiry</option>
                  <option>Hotel Booking</option>
                  <option>Existing Booking Support</option>
                  <option>Other</option>
                </select>
              </div>
              <div class="form-group full"><label>Message</label><textarea rows="5" v-model="form.message" placeholder="How can we help?" required></textarea></div>
            </div>
            <button type="submit" class="btn btn-primary" style="margin-top:18px;" :disabled="isSubmitting">{{ isSubmitting ? 'Sending...' : 'Send Message' }}</button>
          </form>
        </div>

        <div>
          <div class="contact-info-card">
            <div class="row"><span>📍</span><div><b>Fly Planet Badalona</b>Av Alfonso XIII 496, Badalona 08913, Spain</div></div>
            <div class="row"><span>☎</span><div><b>Phone</b>+923085092631<br>0034672068148<br>0034631028871<br>0034672068131</div></div>
            <div class="row"><span>📧</span><div><b>Email</b>sangutravel@gmail.com</div></div>
            <div class="row"><span>🕑</span><div><b>Office Hours</b>Monday – Saturday, 9:00 AM – 9:00 PM</div></div>
            <div class="row"><span>💬</span><div><b>WhatsApp Support</b>Available 24/7 for travellers currently abroad</div></div>
          </div>
          <div class="card" style="margin-top:20px;">
            <div style="height:220px;background:linear-gradient(160deg,#dfe8e6,#0E4B44);border-radius:var(--radius);display:flex;align-items:center;justify-content:center;color:#fff;font-family:'IBM Plex Mono',monospace;font-size:13px;text-align:center;">
              Map — Badalona, Spain
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';

const form = ref({
  name: '',
  phone: '',
  email: '',
  subject: 'Visa Enquiry',
  message: ''
});

const isSubmitting = ref(false);

const submitForm = async () => {
  if (isSubmitting.value) return;
  isSubmitting.value = true;
  
  try {
    const res = await axios.post('/api/inquiry', form.value);
    
    if(window.sanguToast) {
      window.sanguToast('Message sent — our team will reply shortly!');
    } else {
      alert('Message sent — our team will reply shortly!');
    }
    
    // Reset form
    form.value = { name: '', phone: '', email: '', subject: 'Visa Enquiry', message: '' };
  } catch (err) {
    console.error(err);
    if(window.sanguToast) {
      window.sanguToast('Error sending message. Please try again.', 'error');
    } else {
      alert('Error sending message. Please try again.');
    }
  } finally {
    isSubmitting.value = false;
  }
};
</script>
