<template>
  <div class="admin-page" style="padding:24px;">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
      <h2 style="margin:0;">📣 Social Media Posting</h2>
      <div style="display:flex;gap:8px;">
        <button v-for="t in tabs" :key="t.key" @click="tab = t.key"
          :style="tabStyle(t.key)">{{ t.label }}</button>
      </div>
    </div>

    <!-- ================= CONNECTIONS ================= -->
    <section v-if="tab === 'connections'" style="margin-top:20px;">
      <div class="card" style="padding:20px;margin-bottom:16px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
        <div>
          <strong>Facebook Pages &amp; Instagram</strong>
          <p style="margin:6px 0 0;color:#667;font-size:14px;">
            Connect your Facebook account, choose a Page, and its linked Instagram Business account is detected automatically.
          </p>
        </div>
        <button class="btn btn-primary" @click="startConnect" :disabled="connecting">
          {{ connecting ? 'Opening Facebook…' : '🔗 Connect Facebook' }}
        </button>
      </div>

      <p v-if="!fbConfigured" class="alert alert-warning" style="padding:12px;border-radius:8px;background:#fff7e6;border:1px solid #f0c36d;">
        ⚠️ Facebook App credentials missing. Add <code>FACEBOOK_CLIENT_ID</code> and <code>FACEBOOK_CLIENT_SECRET</code> to your <code>.env</code>.
      </p>
      <p v-if="flashMessage" :style="flashStyle">{{ flashMessage }}</p>

      <table v-if="accounts.length" style="width:100%;border-collapse:collapse;" class="card">
        <thead>
          <tr style="text-align:left;">
            <th style="padding:12px;">Facebook Page</th>
            <th style="padding:12px;">Instagram</th>
            <th style="padding:12px;">Status</th>
            <th style="padding:12px;">Connected At</th>
            <th style="padding:12px;text-align:right;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="a in accounts" :key="a.id" style="border-top:1px solid #eee;">
            <td style="padding:12px;font-weight:600;">{{ a.page_name }}</td>
            <td style="padding:12px;">
              <template v-if="a.instagram">@{{ a.instagram.username }}</template>
              <span v-else style="color:#999;">Not linked</span>
            </td>
            <td style="padding:12px;">
              <span :class="statusClass(a.status, a.needs_reconnect)">
                {{ a.needs_reconnect ? 'Reconnect Required' : (a.status === 'connected' ? 'Connected' : a.status === 'discovered' ? 'Discovered' : 'Not Connected') }}
              </span>
            </td>
            <td style="padding:12px;">{{ a.connected_at || '—' }}</td>
            <td style="padding:12px;text-align:right;white-space:nowrap;">
              <button v-if="a.status !== 'connected' || a.needs_reconnect" class="btn btn-primary" style="padding:6px 14px;font-size:13px;"
                @click="connectPage(a)">Connect / Reconnect</button>
              <button v-else class="btn" style="padding:6px 14px;font-size:13px;background:#f2f2f2;"
                @click="disconnectPage(a)">Disconnect</button>
            </td>
          </tr>
        </tbody>
      </table>
      <p v-else style="color:#889;margin-top:16px;">No pages yet. Click “Connect Facebook” to fetch your Pages.</p>
    </section>

    <!-- ================= CREATE POST ================= -->
    <section v-else-if="tab === 'create'" style="margin-top:20px;max-width:720px;">
      <div class="card" style="padding:20px;">
        <label style="font-weight:600;">Caption / Message</label>
        <textarea v-model="form.caption" rows="4" maxlength="2200"
          placeholder="Write your post caption…"
          style="width:100%;margin-top:6px;padding:10px 12px;border:1px solid var(--line);border-radius:10px;font-family:inherit;"></textarea>
        <div style="font-size:12px;color:#99a;text-align:right;">{{ form.caption.length }}/2200</div>

        <label style="font-weight:600;display:block;margin-top:14px;">Media (images or one video)</label>
        <input type="file" multiple accept="image/jpeg,image/png,image/webp,video/mp4,video/quicktime"
          @change="onFilesSelected" style="margin-top:6px;" />
        <div v-if="previews.length" style="display:flex;gap:10px;margin-top:10px;flex-wrap:wrap;">
          <div v-for="(p, i) in previews" :key="i" style="position:relative;width:96px;height:96px;border-radius:10px;overflow:hidden;border:1px solid var(--line);">
            <img v-if="p.type === 'image'" :src="p.url" style="width:100%;height:100%;object-fit:cover;">
            <video v-else :src="p.url" style="width:100%;height:100%;object-fit:cover;"></video>
            <button @click="removeFile(i)" title="Remove"
              style="position:absolute;top:2px;right:2px;background:#dc2626;color:#fff;border:none;border-radius:50%;width:22px;height:22px;cursor:pointer;">×</button>
          </div>
        </div>

        <label style="font-weight:600;display:block;margin-top:14px;">Link / URL (optional)</label>
        <input type="url" v-model="form.link_url" placeholder="https://example.com"
          style="width:100%;margin-top:6px;padding:10px 12px;border:1px solid var(--line);border-radius:10px;" />

        <div style="display:flex;gap:18px;margin-top:18px;flex-wrap:wrap;">
          <label style="display:flex;align-items:center;gap:8px;font-weight:600;">
            <input type="checkbox" value="facebook" v-model="form.targets"> Facebook
            <span v-if="!facebookReady" style="font-weight:400;color:#b45309;font-size:12px;">(not connected)</span>
          </label>
          <label style="display:flex;align-items:center;gap:8px;font-weight:600;">
            <input type="checkbox" value="instagram" v-model="form.targets"> Instagram
            <span v-if="!instagramReady" style="font-weight:400;color:#b45309;font-size:12px;">(not connected)</span>
          </label>
        </div>

        <div style="display:flex;align-items:center;gap:10px;margin-top:18px;flex-wrap:wrap;">
          <button class="btn btn-primary" @click="submit(false)" :disabled="publishing">
            {{ publishing ? 'Publishing…' : '🚀 Publish Now' }}
          </button>
          <button class="btn" style="background:#eef;" @click="showSchedule = !showSchedule">🕒 Schedule Post</button>
        </div>

        <div v-if="showSchedule" style="margin-top:14px;display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
          <input type="datetime-local" v-model="form.scheduled_at"
            style="padding:8px 12px;border:1px solid var(--line);border-radius:10px;">
          <button class="btn btn-primary" @click="submit(true)" :disabled="publishing">
            {{ publishing ? 'Scheduling…' : 'Schedule' }}
          </button>
        </div>

        <div v-for="(res, platform) in lastResult" :key="platform" style="margin-top:14px;">
          <div :style="platformResultStyle(res)">
            <strong>{{ platform === 'facebook' ? 'Facebook' : 'Instagram' }}:</strong>
            <template v-if="res.status === 'published'">
              ✅ Published
              <a v-if="res.url" :href="res.url" target="_blank" style="margin-left:6px;">View post ↗</a>
            </template>
            <template v-else-if="res.status === 'failed'">
              ❌ Failed — {{ res.error }}
            </template>
            <template v-else>{{ res.status }}</template>
          </div>
        </div>
      </div>
    </section>

    <!-- ================= POST HISTORY ================= -->
    <section v-else style="margin-top:20px;">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
        <h3 style="margin:0;">Post History</h3>
        <button class="btn" style="background:#f2f2f2;padding:6px 14px;" @click="loadPosts">↻ Refresh</button>
      </div>

      <table v-if="posts.length" style="width:100%;border-collapse:collapse;" class="card">
        <thead>
          <tr style="text-align:left;">
            <th style="padding:12px;">Post</th>
            <th style="padding:12px;">Facebook</th>
            <th style="padding:12px;">Instagram</th>
            <th style="padding:12px;">Scheduled</th>
            <th style="padding:12px;">Published</th>
            <th style="padding:12px;"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="p in posts" :key="p.id" style="border-top:1px solid #eee;vertical-align:top;">
            <td style="padding:12px;max-width:340px;">
              <div style="display:flex;gap:10px;">
                <img v-if="p.media.length && p.media[0].type === 'image'" :src="p.media[0].url"
                  style="width:52px;height:52px;object-fit:cover;border-radius:8px;flex-shrink:0;">
                <video v-else-if="p.media.length" :src="p.media[0].url"
                  style="width:52px;height:52px;object-fit:cover;border-radius:8px;flex-shrink:0;"></video>
                <div>
                  <div style="font-size:14px;">{{ p.caption ? truncate(p.caption, 90) : '(no caption)' }}</div>
                  <a v-if="p.link_url" :href="p.link_url" target="_blank" style="font-size:12px;">{{ truncate(p.link_url, 40) }}</a>
                </div>
              </div>
            </td>
            <td style="padding:12px;">
              <div><span :class="pubClass(p.facebook_status)">{{ p.facebook_status }}</span></div>
              <a v-if="p.facebook_url" :href="p.facebook_url" target="_blank" style="font-size:12px;">view ↗</a>
              <div v-if="p.facebook_error" style="font-size:12px;color:#dc2626;max-width:220px;">{{ p.facebook_error }}</div>
            </td>
            <td style="padding:12px;">
              <div><span :class="pubClass(p.instagram_status)">{{ p.instagram_status }}</span></div>
              <a v-if="p.instagram_url" :href="p.instagram_url" target="_blank" style="font-size:12px;">view ↗</a>
              <div v-if="p.instagram_error" style="font-size:12px;color:#dc2626;max-width:220px;">{{ p.instagram_error }}</div>
            </td>
            <td style="padding:12px;white-space:nowrap;">{{ p.scheduled_at || '—' }}</td>
            <td style="padding:12px;white-space:nowrap;">{{ p.published_at || '—' }}</td>
            <td style="padding:12px;text-align:right;">
              <button v-if="p.scheduled_at && p.overall_status === 'pending'"
                class="btn btn-primary" style="padding:5px 12px;font-size:12px;" @click="publishScheduledNow(p)">Publish Now</button>
              <button class="btn" style="padding:5px 12px;font-size:12px;background:#fee2e2;color:#b91c1c;"
                @click="deletePost(p)">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
      <p v-else style="color:#889;">No posts published yet.</p>
    </section>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const tab = ref('connections');
const tabs = [
  { key: 'connections', label: 'Connections' },
  { key: 'create', label: 'Create Post' },
  { key: 'history', label: 'Post History' },
];

const accounts = ref([]);
const posts = ref([]);
const fbConfigured = ref(true);
const connecting = ref(false);
const publishing = ref(false);
const showSchedule = ref(false);
const flashMessage = ref('');
const flashType = ref('success');
const lastResult = ref({});

const form = ref({ caption: '', link_url: '', targets: ['facebook'], scheduled_at: '' });
const files = ref([]);
const previews = ref([]);

const facebookReady = computed(() => accounts.value.some(a => a.status === 'connected' && !a.needs_reconnect));
const instagramReady = computed(() => accounts.value.some(a => a.isInstagramConnected?.() || (a.instagram && a.status === 'connected' && !a.needs_reconnect)));

function tabStyle(key) {
  const active = tab.value === key;
  return {
    padding: '8px 18px',
    borderRadius: '999px',
    border: active ? 'none' : '1px solid var(--line)',
    background: active ? 'var(--gold, #b8860b)' : '#fff',
    color: active ? '#fff' : '#333',
    fontWeight: 600,
    cursor: 'pointer',
  };
}

function statusClass(status, needs) {
  if (needs) return 'badge badge-warn';
  return status === 'connected' ? 'badge badge-ok' : 'badge';
}
function pubClass(s) {
  return s === 'published' ? 'badge badge-ok'
    : s === 'failed' ? 'badge badge-bad'
    : s === 'skipped' ? 'badge' : 'badge badge-warn';
}

const flashStyle = computed(() => ({
  marginTop: '14px',
  padding: '12px',
  borderRadius: '8px',
  background: flashType.value === 'error' ? '#fee2e2' : '#dcfce7',
  color: flashType.value === 'error' ? '#b91c1c' : '#166534',
}));

function platformResultStyle(res) {
  return {
    padding: '10px 14px',
    borderRadius: '8px',
    fontSize: '14px',
    background: res.status === 'published' ? '#dcfce7' : res.status === 'failed' ? '#fee2e2' : '#fef9c3',
    color: res.status === 'published' ? '#166534' : res.status === 'failed' ? '#b91c1c' : '#854d0e',
  };
}

async function loadAccounts() {
  try {
    const res = await axios.get('/api/admin/social/accounts');
    fbConfigured.value = res.data.configured;
    accounts.value = res.data.accounts;
  } catch (e) {
    console.error(e);
  }
}

async function loadPosts() {
  try {
    const res = await axios.get('/api/admin/social/posts');
    posts.value = res.data;
  } catch (e) {
    console.error(e);
  }
}

async function startConnect() {
  connecting.value = true;
  try {
    const res = await axios.get('/api/admin/social/oauth-url');
    window.location.href = res.data.url;
  } catch (e) {
    flash(e.response?.data?.message || 'Could not start Facebook connection.', 'error');
    connecting.value = false;
  }
}

async function connectPage(a) {
  try {
    await axios.post(`/api/admin/social/accounts/${a.id}/connect`);
    flash(`"${a.page_name}" connected successfully.`);
    await loadAccounts();
  } catch (e) {
    flash('Failed to connect page.', 'error');
  }
}

async function disconnectPage(a) {
  try {
    await axios.post(`/api/admin/social/accounts/${a.id}/disconnect`);
    flash(`"${a.page_name}" disconnected.`);
    await loadAccounts();
  } catch (e) {
    flash('Failed to disconnect page.', 'error');
  }
}

function onFilesSelected(e) {
  files.value = Array.from(e.target.files || []);
  previews.value = files.value.map(f => ({ type: f.type.startsWith('video') ? 'video' : 'image', url: URL.createObjectURL(f) }));
}

function removeFile(i) {
  files.value.splice(i, 1);
  previews.value.splice(i, 1);
}

async function submit(scheduled) {
  if (!form.value.caption.trim() && !files.value.length && !form.value.link_url.trim()) {
    alert('Add a caption, media or link first.');
    return;
  }
  if (!form.value.targets.length) {
    alert('Select at least one platform.');
    return;
  }

  publishing.value = true;
  lastResult.value = {};

  try {
    const fd = new FormData();
    fd.append('caption', form.value.caption);
    if (form.value.link_url.trim()) fd.append('link_url', form.value.link_url);
    form.value.targets.forEach(t => fd.append('targets[]', t));
    if (scheduled && form.value.scheduled_at) fd.append('scheduled_at', form.value.scheduled_at);
    files.value.forEach(f => fd.append('media[]', f));

    const res = await axios.post('/api/admin/social/posts', fd, {
      headers: { 'Content-Type': 'multipart/form-data' },
    });

    if (res.data.scheduled) {
      flash(res.data.message || 'Post scheduled.');
    } else {
      const p = res.data.post;
      lastResult.value = {
        facebook: { status: p.facebook_status, error: p.facebook_error, url: p.facebook_url },
        instagram: { status: p.instagram_status, error: p.instagram_error, url: p.instagram_url },
      };
    }

    resetForm();
    await loadPosts();
  } catch (e) {
    flash(e.response?.data?.message || e.response?.data?.error || 'Publishing failed.', 'error');
  } finally {
    publishing.value = false;
  }
}

async function publishScheduledNow(p) {
  try {
    await axios.post(`/api/admin/social/posts/${p.id}/publish-scheduled`);
    await loadPosts();
  } catch (e) {
    flash('Could not publish scheduled post.', 'error');
  }
}

async function deletePost(p) {
  if (!confirm('Delete this post from history?')) return;
  try {
    await axios.delete(`/api/admin/social/posts/${p.id}`);
    await loadPosts();
  } catch (e) {
    flash('Delete failed.', 'error');
  }
}

function resetForm() {
  form.value = { caption: '', link_url: '', targets: ['facebook'], scheduled_at: '' };
  files.value = [];
  previews.value = [];
  showSchedule.value = false;
}

function flash(msg, type = 'success') {
  flashMessage.value = msg;
  flashType.value = type;
  setTimeout(() => (flashMessage.value = ''), 8000);
}

function truncate(str, n) {
  return str.length > n ? str.slice(0, n) + '…' : str;
}

onMounted(async () => {
  await loadAccounts();

  const oauth = route.query.fb_oauth;
  if (oauth === 'success') {
    flash('Facebook connected. Now select the Page you want to use below.');
  } else if (oauth === 'error') {
    flash(route.query.message || 'Facebook connection failed.', 'error');
  }

  await loadPosts();
});
</script>

<style scoped>
.badge {
  display: inline-block;
  padding: 3px 10px;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 700;
  background: #eee;
  color: #555;
  text-transform: capitalize;
}
.badge-ok { background: #dcfce7; color: #166534; }
.badge-bad { background: #fee2e2; color: #b91c1c; }
.badge-warn { background: #fef3c7; color: #92400e; }
.card { background: #fff; border: 1px solid var(--line, #e5e7eb); border-radius: 12px; overflow: hidden; }
.btn { border: none; border-radius: 8px; cursor: pointer; font-family: inherit; font-weight: 600; }
.btn-primary { background: var(--gold, #b8860b); color: #fff; padding: 10px 18px; }
.btn-primary:disabled { opacity: .6; cursor: not-allowed; }
.alert code { background: #fff3cd; padding: 2px 6px; border-radius: 4px; }
</style>
