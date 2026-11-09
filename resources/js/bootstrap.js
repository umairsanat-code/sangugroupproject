import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.withCredentials = true;

const apiToken = localStorage.getItem('api_token');
if (apiToken) {
  window.axios.defaults.headers.common['Authorization'] = `Bearer ${apiToken}`;
}

window.axios.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401 && !window.location.pathname.startsWith('/admin/login')) {
      localStorage.removeItem('isAuthenticated');
      localStorage.removeItem('admin_logged_in');
      localStorage.removeItem('api_token');
      delete window.axios.defaults.headers.common['Authorization'];
      if (window.location.pathname.startsWith('/admin')) {
        window.location.href = '/admin/login';
      }
    }
    return Promise.reject(error);
  }
);
