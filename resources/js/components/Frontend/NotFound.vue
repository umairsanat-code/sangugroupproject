<template>
  <div>
    <section class="page-hero" style="min-height:72vh;">
      <div class="page-hero-bg">
        <img
          src="https://images.unsplash.com/photo-1488646953014-85cb44e25828?w=1920&q=70&auto=format&fit=crop"
          alt="Page not found"
        >
        <div class="page-hero-shade" style="background:linear-gradient(to right, rgba(10,55,51,0.8) 0%, rgba(10,55,51,0.55) 60%, rgba(10,55,51,0.35) 100%);"></div>
      </div>
      <div class="page-hero-content container" style="text-align:center;">
        <nav class="page-breadcrumb" style="justify-content:center;">
          <router-link to="/">Home</router-link>
          <span class="sep"></span>
          <span class="current">404</span>
        </nav>
        <div class="page-hero-badge" style="margin:0 auto;">
          <i class="fa-solid fa-compass"></i>
          <span>Lost in transit?</span>
        </div>
        <h1 class="page-hero-title" style="font-size:96px;line-height:1;">4<em>0</em>4</h1>
        <p class="page-hero-sub" style="max-width:560px;margin:0 auto 32px;">
          The page you're looking for doesn't exist or has been moved. Let's get you back on track.
        </p>
        <div style="display:flex;gap:14px;justify-content:center;flex-wrap:wrap;">
          <router-link to="/" class="btn btn-gold">Back to Home</router-link>
          <router-link to="/contact" class="btn btn-outline-light">Contact Support</router-link>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
</script>