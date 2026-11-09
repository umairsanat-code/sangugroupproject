<template>
  <div class="admin-body" v-if="isAuthenticated">
    <div class="admin-shell">
      <aside class="admin-sidebar">
        <div class="logo" style="display:flex;align-items:center;justify-content:center;padding:24px 20px;border-bottom:1px solid rgba(255,255,255,0.05);margin-bottom:16px;">
          <img src="/images/logo.png" alt="SanguTravel Logo" style="height:48px;width:auto;max-width:100%;object-fit:contain;background:#fff;padding:8px;border-radius:8px;">
        </div>
        <div class="admin-nav">
          <router-link to="/admin" exact-active-class="active">📊 Dashboard</router-link>
          <!-- Using /admin/customers as a proxy for bookings for now, or create new route -->
          <router-link to="/admin/bookings" active-class="active">🧾 Bookings</router-link>
          <router-link to="/admin/umrah-packages" active-class="active">🗂 Manage Listings</router-link>
          <router-link to="/admin/social-media" active-class="active">📣 Social Media</router-link>
          <router-link to="/admin/payment-settings" active-class="active">💳 Payment Settings</router-link>
          <router-link to="/admin/social-links" active-class="active">🔗 Social Links</router-link>
          <router-link to="/admin/chatbot" active-class="active">🤖 AI Chatbot</router-link>
          <div class="section-label">Account</div>
          <a href="#" @click.prevent="openScrapeGuard">🛡 ScrapeGuard</a>
          <a href="#" @click.prevent="logout">🚪 Log Out</a>
          <router-link to="/" target="_blank">🌐 View Website</router-link>
        </div>
      </aside>

      <main class="admin-main">
        <div class="admin-topbar">
          <h1 style="text-transform: capitalize">{{ routeName }}</h1>
          <div class="admin-user"><div class="avatar">A</div> Admin User</div>
        </div>

        <router-view></router-view>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';

const router = useRouter();
const route = useRoute();
const isAuthenticated = ref(false);

const routeName = computed(() => {
  const path = route.path.split('/').pop() || 'Dashboard';
  return path === 'admin' ? 'Dashboard' : path.replace('-', ' ');
});

onMounted(() => {
  if (localStorage.getItem('isAuthenticated') !== 'true' || !localStorage.getItem('api_token')) {
    router.push('/admin/login');
  } else {
    isAuthenticated.value = true;
  }
});

const logout = async () => {
  try {
    await window.axios.post('/api/logout');
  } catch (e) { /* ignore */ }
  localStorage.removeItem('admin_logged_in');
  localStorage.removeItem('isAuthenticated');
  localStorage.removeItem('api_token');
  delete window.axios.defaults.headers.common['Authorization'];
  router.push('/admin/login');
};

const openScrapeGuard = () => {
  const token = localStorage.getItem('api_token') || '';
  window.open('/scrapeguard?token=' + encodeURIComponent(token), '_blank');
};
</script>
