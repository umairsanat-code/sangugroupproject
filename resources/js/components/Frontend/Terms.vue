<template>
  <div>
    <!-- Hero -->
    <section class="page-hero">
      <div class="page-hero-bg">
        <img
          src="https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?w=1920&q=70&auto=format&fit=crop"
          alt="Terms & Conditions"
          style="filter: grayscale(40%);"
        >
        <div class="page-hero-shade" style="background:linear-gradient(to right, rgba(10,55,51,0.75) 0%, rgba(10,55,51,0.55) 60%, rgba(10,55,51,0.35) 100%);"></div>
      </div>
      <div class="page-hero-content container">
        <nav class="page-breadcrumb">
          <router-link to="/">Home</router-link>
          <span class="sep"></span>
          <span class="current">Terms &amp; Conditions</span>
        </nav>
        <div class="page-hero-badge">
          <i class="fa-solid fa-file-shield"></i>
          <span>Legal &amp; Policy</span>
        </div>
        <h1 class="page-hero-title">Terms &amp; <em>Conditions</em></h1>
        <p class="page-hero-sub">Please read these terms carefully before using our services. By booking with SanguGroup you agree to the terms set out below.</p>
      </div>
    </section>

    <!-- Content -->
    <section style="padding: 64px 0 100px;">
      <div class="container legal" style="max-width: 820px;">
        <p style="font-size:13px;color:var(--ink-soft);font-family:'IBM Plex Mono',monospace;margin-bottom:32px;">
          Last updated: September 2026
        </p>

        <h2>1. Introduction</h2>
        <p>These Terms and Conditions ("Terms") govern your use of the SanguGroup website and the purchase of any travel services, visa packages, Umrah packages, or hotel bookings offered by SanguGroup ("we", "us", "our"). By accessing our website or placing a booking, you confirm that you have read, understood, and agreed to these Terms.</p>

        <h2>2. Booking &amp; Payment</h2>
        <p>All bookings are subject to availability and confirmation. A booking is only confirmed once you have received a written confirmation email from SanguGroup and the required deposit or full payment has been received. Prices displayed are in Pakistani Rupees (PKR) unless otherwise stated and are subject to change without notice until a booking is confirmed.</p>

        <h2>3. Visa Services</h2>
        <p>SanguGroup acts as an authorised visa processing agent. We facilitate the submission of your application to the relevant embassy or consulate. We do not guarantee visa approval, as this decision rests solely with the issuing authority. Our processing fee is non-refundable in the event of a visa refusal, except where our refund policy explicitly states otherwise.</p>
        <p>Applicants are responsible for providing accurate, complete, and truthful documentation. SanguGroup accepts no liability for delays or refusals arising from incomplete or inaccurate applications.</p>

        <h2>4. Umrah &amp; Travel Packages</h2>
        <p>Umrah packages include services as specified in the package description at the time of booking. Any services not explicitly mentioned are not included. SanguGroup reserves the right to make minor changes to itineraries, hotels, or travel dates due to operational requirements, and will notify affected customers as soon as practicable.</p>

        <h2>5. Cancellations &amp; Refunds</h2>
        <p>Cancellation charges vary by package and how far in advance the cancellation is made. Please refer to the specific package terms provided at the time of booking. Certain components of packages (e.g., airline tickets, hotel deposits) may be non-refundable as per the supplier's policy. Refunds, where applicable, are processed within 14-21 working days.</p>

        <h2>6. Travel Documents &amp; Responsibility</h2>
        <p>It is the traveller's sole responsibility to hold a valid passport (with at least 6 months' validity), any required visas, and all other necessary travel documentation. SanguGroup accepts no responsibility for losses arising from failure to comply with entry requirements or customs regulations.</p>

        <h2>7. Liability Limitation</h2>
        <p>SanguGroup's liability is limited to the total amount paid by the customer for the relevant service. We are not liable for indirect, consequential, or special damages, including but not limited to loss of enjoyment, missed connections, or costs arising from force majeure events such as natural disasters, acts of war, or government-imposed restrictions.</p>

        <h2>8. Privacy</h2>
        <p>Your personal information is collected and used in accordance with our Privacy Policy. By using our services, you consent to the collection and processing of your data as described therein. We do not sell or share your personal data with third parties except as required to fulfil your booking.</p>

        <h2>9. Governing Law</h2>
        <p>These Terms are governed by the laws of Pakistan. Any disputes arising under or in connection with these Terms shall be subject to the exclusive jurisdiction of the courts of Lahore, Pakistan.</p>

        <h2>10. Contact</h2>
        <p>If you have any questions about these Terms, please contact us at <a href="mailto:sangutravel@gmail.com" style="color:var(--teal);font-weight:600;">sangutravel@gmail.com</a>.</p>

        <div class="divider" style="margin:40px 0;"></div>

        <div style="display:flex;gap:14px;flex-wrap:wrap;">
          <router-link to="/contact" class="btn btn-primary">Contact Us</router-link>
          <router-link to="/" class="btn btn-ghost">Back to Home</router-link>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
</script>
