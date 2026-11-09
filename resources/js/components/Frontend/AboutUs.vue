<template>
  <div>
    <section class="page-hero">
      <div class="page-hero-bg">
        <img src="https://images.unsplash.com/photo-1497366216548-37526070297c?w=1920&q=80&auto=format&fit=crop" alt="Our Team">
        <div class="page-hero-shade"></div>
      </div>
      <div class="page-hero-content container">
        <div class="eyebrow" style="color:var(--gold);margin-bottom:12px;">About SanguGroup</div>
        <h1 class="page-hero-title">Built by travellers,<br>for travellers</h1>
        <p class="page-hero-sub">SanguGroup started with a simple frustration: visa and Umrah booking shouldn't mean endless phone calls and vague promises. So we built a platform where you can see prices upfront, track your application, and speak to a real person whenever you need to.</p>
      </div>
    </section>

    <section>
      <div class="container">
        <div class="grid grid-4">
          <div class="card"><div class="card-body"><h3 style="font-size:30px;color:var(--teal);">2016</h3><p class="desc">Founded as a small visa consultancy.</p></div></div>
          <div class="card"><div class="card-body"><h3 style="font-size:30px;color:var(--teal);">40,000+</h3><p class="desc">Pilgrims guided through Umrah since inception.</p></div></div>
          <div class="card"><div class="card-body"><h3 style="font-size:30px;color:var(--teal);">65+</h3><p class="desc">Visa destinations processed monthly.</p></div></div>
          <div class="card"><div class="card-body"><h3 style="font-size:30px;color:var(--teal);">4.8/5</h3><p class="desc">Average rating across 6,000+ reviews.</p></div></div>
        </div>
      </div>
    </section>

    <section class="bg-raised">
      <div class="container" style="display:grid;grid-template-columns:1fr 1fr;gap:50px;align-items:center;">
        <div class="hero-arch" style="height:360px;">
          <img src="https://images.unsplash.com/photo-1591604129939-f1efa4d9f7fa?q=80&w=1000&auto=format&fit=crop" alt="Our team">
        </div>
        <div>
          <div class="eyebrow">Our Promise</div>
          <h2 style="font-size:clamp(24px,3vw,34px);">Transparent pricing, real support</h2>
          <p>No hidden charges, no "call for price" — every visa, Umrah package and hotel on our site is priced upfront. If your visa application is rejected by the embassy, our processing fee is refunded per our terms. Our support desk in Spain is staffed daily, and our WhatsApp line stays open around the clock for travellers already on the road.</p>
          <router-link to="/contact" class="btn btn-primary" style="margin-top:8px;">Talk to Our Team</router-link>
        </div>
      </div>
    </section>

    <section>
      <div class="container">
        <div class="section-head"><div><div class="eyebrow">Why Travellers Choose Us</div><h2>What sets SanguGroup apart</h2></div></div>
        <div class="grid grid-3">
          <div class="card"><div class="card-body"><h3>IATA Accredited</h3><p class="desc">Book flights and packages through a licensed, registered travel agency.</p></div></div>
          <div class="card"><div class="card-body"><h3>Dedicated Muallim</h3><p class="desc">Every group Umrah departure travels with an experienced, Urdu-speaking Muallim.</p></div></div>
          <div class="card"><div class="card-body"><h3>Real-Time Tracking</h3><p class="desc">Track your visa application status online instead of calling for updates.</p></div></div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
</script>
