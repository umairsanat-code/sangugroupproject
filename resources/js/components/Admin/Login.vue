<template>
  <div class="login-shell">
    <div class="login-card">
      <div class="logo" style="color:var(--teal-dark);margin-bottom:6px;"><span class="mark"></span> SanguTravel</div>
      <p style="margin-bottom:24px;">Sign in to the admin dashboard</p>
      
      <form @submit.prevent="doLogin">
        <div class="form-group" style="margin-bottom:14px;">
          <label>Email Address</label>
          <input type="email" v-model="email" required>
        </div>
        <div class="form-group" style="margin-bottom:8px;">
          <label>Password</label>
          <input type="password" v-model="password" required>
        </div>
        <p class="form-hint" style="color:var(--brick);min-height:16px;">{{ errMsg }}</p>
        <button type="submit" class="btn btn-primary btn-block" style="margin-top:10px;" :disabled="loading">
          {{ loading ? 'Signing In…' : 'Sign In' }}
        </button>
      </form>
      
      <p class="form-hint" style="text-align:center;margin-top:16px;">Demo credentials are pre-filled — just click Sign In.</p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();
const email = ref('admin@sangutravel.pk');
const password = ref('admin123');
const errMsg = ref('');
const loading = ref(false);

const doLogin = async () => {
  loading.value = true;
  errMsg.value = '';
  try {
    const res = await axios.post('/api/login', {
      email: email.value,
      password: password.value,
    });

    localStorage.setItem('admin_logged_in', 'true');
    localStorage.setItem('isAuthenticated', 'true');
    localStorage.setItem('api_token', res.data.token);
    window.axios.defaults.headers.common['Authorization'] = `Bearer ${res.data.token}`;
    router.push('/admin');
  } catch (e) {
    errMsg.value = e.response?.data?.message || 'Invalid email or password.';
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
/* Need to make sure body takes up full height for login-shell centering. 
   The main style.css provides .login-shell which implies flex centering. */
.login-shell {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--bg);
}
</style>
