<template>
  <div class="chatbot-admin">
    <!-- Stats -->
    <div class="stat-cards">
      <div class="cb-stat"><span>Conversations</span><strong>{{ stats.conversations }}</strong></div>
      <div class="cb-stat"><span>Messages</span><strong>{{ stats.messages }}</strong></div>
      <div class="cb-stat"><span>Total Leads</span><strong>{{ stats.leads }}</strong></div>
      <div class="cb-stat cb-stat-accent"><span>New Leads</span><strong>{{ stats.new_leads }}</strong></div>
    </div>

    <!-- Tabs -->
    <div class="cb-tabs">
      <button :class="{ active: tab === 'settings' }" @click="tab = 'settings'">⚙️ Settings</button>
      <button :class="{ active: tab === 'conversations' }" @click="switchTab('conversations')">💬 Conversations</button>
      <button :class="{ active: tab === 'leads' }" @click="switchTab('leads')">
        🎯 Leads
        <span v-if="stats.new_leads" class="cb-pill">{{ stats.new_leads }}</span>
      </button>
    </div>

    <!-- ===================== SETTINGS ===================== -->
    <div v-if="tab === 'settings'" class="admin-panel-card">
      <h2>AI Chatbot Settings</h2>
      <p class="cb-hint">Configure the public chat assistant. The API key is stored encrypted and is never exposed to the website or browser.</p>

      <div v-if="flash" class="flash" :class="flashType">{{ flash }}</div>

      <form class="cb-form" @submit.prevent="saveSettings">
        <div class="cb-row-inline">
          <label class="cb-switch">
            <input type="checkbox" v-model="form.enabled">
            <span class="cb-slider"></span>
            <b>Chatbot enabled</b>
          </label>
          <label class="cb-switch">
            <input type="checkbox" v-model="form.lead_capture_enabled">
            <span class="cb-slider"></span>
            <b>Collect customer leads</b>
          </label>
        </div>

        <div class="cb-grid">
          <div class="cb-field">
            <label>OpenAI API Key</label>
            <input
              type="password"
              v-model="form.api_key"
              autocomplete="new-password"
              :placeholder="settings.has_api_key ? '•••••••••••• (leave blank to keep current key)' : 'sk-...'"
            >
            <small v-if="settings.has_api_key" class="cb-ok">✓ A key is currently saved</small>
            <small v-else class="cb-warn">No key saved. The assistant will use the server .env key if present.</small>
            <label v-if="settings.has_api_key" class="cb-check">
              <input type="checkbox" v-model="form.clear_api_key"> Remove the saved key
            </label>
          </div>

          <div class="cb-field">
            <label>Model</label>
            <input type="text" v-model="form.model" placeholder="gpt-4o-mini">
            <small>e.g. gpt-4o-mini, gpt-4o, gpt-4-turbo</small>
          </div>

          <div class="cb-field">
            <label>Temperature (0 – 2)</label>
            <input type="number" min="0" max="2" step="0.1" v-model.number="form.temperature">
          </div>

          <div class="cb-field">
            <label>Max reply tokens</label>
            <input type="number" min="16" max="4096" step="1" v-model.number="form.max_tokens">
          </div>
        </div>

        <div class="cb-field">
          <label>Welcome message</label>
          <textarea rows="2" v-model="form.welcome_message" placeholder="Assalamu Alaikum! How can I help you today?"></textarea>
        </div>

        <div class="cb-field">
          <label>System instructions</label>
          <textarea rows="6" v-model="form.system_prompt" placeholder="You are a helpful travel assistant for SanguGroup..."></textarea>
          <small>The services, FAQs and contact details below are automatically appended to these instructions.</small>
        </div>

        <div class="cb-field">
          <label>Suggested questions</label>
          <div v-for="(q, i) in form.suggested_questions" :key="'q' + i" class="cb-repeat">
            <input type="text" v-model="form.suggested_questions[i]" placeholder="e.g. What visa services do you offer?">
            <button type="button" class="cb-del" @click="form.suggested_questions.splice(i, 1)">✕</button>
          </div>
          <button type="button" class="cb-add" @click="form.suggested_questions.push('')">+ Add question</button>
        </div>

        <div class="cb-field">
          <label>Company services</label>
          <div v-for="(s, i) in form.services" :key="'s' + i" class="cb-repeat cb-repeat-block">
            <input type="text" v-model="s.name" placeholder="Service name (e.g. Umrah Packages)">
            <input type="text" v-model="s.description" placeholder="Short description">
            <button type="button" class="cb-del" @click="form.services.splice(i, 1)">✕</button>
          </div>
          <button type="button" class="cb-add" @click="form.services.push({ name: '', description: '' })">+ Add service</button>
        </div>

        <div class="cb-field">
          <label>FAQs</label>
          <div v-for="(f, i) in form.faqs" :key="'f' + i" class="cb-repeat cb-repeat-block">
            <input type="text" v-model="f.question" placeholder="Question">
            <textarea rows="2" v-model="f.answer" placeholder="Answer"></textarea>
            <button type="button" class="cb-del" @click="form.faqs.splice(i, 1)">✕</button>
          </div>
          <button type="button" class="cb-add" @click="form.faqs.push({ question: '', answer: '' })">+ Add FAQ</button>
        </div>

        <div class="cb-actions">
          <button type="submit" class="btn btn-primary" :disabled="saving">
            {{ saving ? 'Saving…' : 'Save Settings' }}
          </button>
        </div>
      </form>
    </div>

    <!-- ===================== CONVERSATIONS ===================== -->
    <div v-else-if="tab === 'conversations'" class="admin-panel-card">
      <h2>Conversations</h2>
      <div class="cb-filters">
        <select v-model="convFilters.status" @change="loadConversations(1)">
          <option value="">All statuses</option>
          <option value="open">Open</option>
          <option value="closed">Closed</option>
        </select>
        <input type="text" v-model="convFilters.search" placeholder="Search messages…" @keyup.enter="loadConversations(1)">
        <button class="btn btn-ghost" @click="loadConversations(1)">Search</button>
      </div>

      <div class="cb-table-wrap">
        <table class="admin-table">
          <thead>
            <tr><th>ID</th><th>Visitor</th><th>Page</th><th>Messages</th><th>Lead</th><th>Last activity</th><th></th></tr>
          </thead>
          <tbody>
            <tr v-for="c in conversations" :key="c.id">
              <td>#{{ c.id }}</td>
              <td>{{ c.visitor_ip || '—' }}</td>
              <td class="cb-ellipsis" :title="c.page_url">{{ c.page_url || '—' }}</td>
              <td>{{ c.messages_count }}</td>
              <td><span v-if="c.lead" class="cb-badge">Yes</span><span v-else>—</span></td>
              <td>{{ formatDate(c.last_activity_at || c.updated_at) }}</td>
              <td class="cb-actions-cell">
                <button class="cb-link" @click="viewConversation(c.id)">View</button>
                <button class="cb-link cb-danger" @click="deleteConversation(c.id)">Delete</button>
              </td>
            </tr>
            <tr v-if="!conversations.length"><td colspan="7" class="cb-empty">No conversations yet.</td></tr>
          </tbody>
        </table>
      </div>
      <Pager :meta="convMeta" @page="loadConversations" />
    </div>

    <!-- ===================== LEADS ===================== -->
    <div v-else class="admin-panel-card">
      <h2>Customer Leads</h2>
      <div class="cb-filters">
        <select v-model="leadFilters.status" @change="loadLeads(1)">
          <option value="">All statuses</option>
          <option v-for="s in statuses" :key="s" :value="s">{{ s }}</option>
        </select>
        <input type="text" v-model="leadFilters.search" placeholder="Search name / phone / email…" @keyup.enter="loadLeads(1)">
        <button class="btn btn-ghost" @click="loadLeads(1)">Search</button>
      </div>

      <div class="cb-table-wrap">
        <table class="admin-table">
          <thead>
            <tr><th>Name</th><th>Contact</th><th>Service</th><th>Status</th><th>Consent</th><th>Date</th><th></th></tr>
          </thead>
          <tbody>
            <tr v-for="l in leads" :key="l.id">
              <td><b>{{ l.name || '—' }}</b></td>
              <td>{{ l.phone || l.email || '—' }}</td>
              <td>{{ l.service_interest || '—' }}</td>
              <td><span class="cb-status" :class="'cb-status-' + l.status">{{ l.status }}</span></td>
              <td>{{ l.consent ? '✓' : '—' }}</td>
              <td>{{ formatDate(l.created_at) }}</td>
              <td class="cb-actions-cell">
                <button class="cb-link" @click="openLead(l)">View</button>
                <button class="cb-link cb-danger" @click="deleteLead(l.id)">Delete</button>
              </td>
            </tr>
            <tr v-if="!leads.length"><td colspan="7" class="cb-empty">No leads captured yet.</td></tr>
          </tbody>
        </table>
      </div>
      <Pager :meta="leadMeta" @page="loadLeads" />
    </div>

    <!-- Conversation detail -->
    <div v-if="activeConversation" class="cb-modal" @click.self="activeConversation = null">
      <div class="cb-modal-box">
        <div class="cb-modal-head">
          <h3>Conversation #{{ activeConversation.id }}</h3>
          <button class="cb-del" @click="activeConversation = null">✕</button>
        </div>
        <div class="cb-transcript">
          <div v-for="m in activeConversation.messages" :key="m.id" class="cb-msg" :class="m.role">
            <div class="cb-msg-role">{{ m.role }}</div>
            <div class="cb-msg-text">{{ m.content }}</div>
            <div class="cb-msg-time">{{ formatDate(m.created_at) }}</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Lead detail -->
    <div v-if="activeLead" class="cb-modal" @click.self="activeLead = null">
      <div class="cb-modal-box cb-modal-sm">
        <div class="cb-modal-head">
          <h3>Lead #{{ activeLead.id }}</h3>
          <button class="cb-del" @click="activeLead = null">✕</button>
        </div>
        <div class="cb-lead-grid">
          <div><b>Name</b><span>{{ activeLead.name || '—' }}</span></div>
          <div><b>Phone</b><span>{{ activeLead.phone || '—' }}</span></div>
          <div><b>Email</b><span>{{ activeLead.email || '—' }}</span></div>
          <div><b>Service</b><span>{{ activeLead.service_interest || '—' }}</span></div>
          <div class="full"><b>Message</b><span>{{ activeLead.message || '—' }}</span></div>
          <div class="full"><b>Consent</b><span>{{ activeLead.consent ? 'Given ✓' : 'Not given' }}</span></div>
        </div>
        <div class="cb-field">
          <label>Status</label>
          <select v-model="leadEdit.status">
            <option v-for="s in statuses" :key="s" :value="s">{{ s }}</option>
          </select>
        </div>
        <div class="cb-field">
          <label>Internal notes</label>
          <textarea rows="3" v-model="leadEdit.notes" placeholder="Add follow-up notes..."></textarea>
        </div>
        <div class="cb-actions">
          <button class="btn btn-primary" :disabled="saving" @click="saveLead">Save</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, h } from 'vue';

const axios = window.axios;

const tab = ref('settings');
const saving = ref(false);
const flash = ref('');
const flashType = ref('success');

const statuses = ['new', 'contacted', 'qualified', 'won', 'lost'];

const stats = reactive({ conversations: 0, messages: 0, leads: 0, new_leads: 0 });

const settings = reactive({ has_api_key: false });
const form = reactive({
  enabled: true,
  lead_capture_enabled: true,
  api_key: '',
  clear_api_key: false,
  model: 'gpt-4o-mini',
  temperature: 0.4,
  max_tokens: 500,
  welcome_message: '',
  system_prompt: '',
  suggested_questions: [],
  services: [],
  faqs: [],
});

const conversations = ref([]);
const convMeta = ref({});
const convFilters = reactive({ status: '', search: '' });

const leads = ref([]);
const leadMeta = ref({});
const leadFilters = reactive({ status: '', search: '' });

const activeConversation = ref(null);
const activeLead = ref(null);
const leadEdit = reactive({ status: 'new', notes: '' });

const Pager = {
  props: { meta: Object },
  emits: ['page'],
  setup(props, { emit }) {
    return () => {
      const m = props.meta || {};
      if (!m.last_page || m.last_page <= 1) return null;
      return h('div', { class: 'cb-pager' }, [
        h('button', { class: 'btn btn-ghost', disabled: m.current_page <= 1, onClick: () => emit('page', m.current_page - 1) }, '‹ Prev'),
        h('span', {}, `Page ${m.current_page} of ${m.last_page}`),
        h('button', { class: 'btn btn-ghost', disabled: m.current_page >= m.last_page, onClick: () => emit('page', m.current_page + 1) }, 'Next ›'),
      ]);
    };
  },
};

const setFlash = (msg, type = 'success') => {
  flash.value = msg;
  flashType.value = type;
  setTimeout(() => { flash.value = ''; }, 4000);
};

const loadStats = async () => {
  try {
    const { data } = await axios.get('/api/admin/chatbot/stats');
    Object.assign(stats, data);
  } catch (e) { /* ignore */ }
};

const loadSettings = async () => {
  try {
    const { data } = await axios.get('/api/admin/chatbot/settings');
    settings.has_api_key = !!data.has_api_key;
    form.enabled = !!data.enabled;
    form.lead_capture_enabled = !!data.lead_capture_enabled;
    form.model = data.model || 'gpt-4o-mini';
    form.temperature = data.temperature ?? 0.4;
    form.max_tokens = data.max_tokens ?? 500;
    form.welcome_message = data.welcome_message || '';
    form.system_prompt = data.system_prompt || '';
    form.suggested_questions = Array.isArray(data.suggested_questions) ? [...data.suggested_questions] : [];
    form.services = Array.isArray(data.services) ? data.services.map((s) => ({ name: s.name || '', description: s.description || '' })) : [];
    form.faqs = Array.isArray(data.faqs) ? data.faqs.map((f) => ({ question: f.question || '', answer: f.answer || '' })) : [];
    form.api_key = '';
    form.clear_api_key = false;
  } catch (e) {
    setFlash('Could not load chatbot settings.', 'error');
  }
};

const saveSettings = async () => {
  saving.value = true;
  flash.value = '';
  try {
    const payload = {
      enabled: form.enabled,
      lead_capture_enabled: form.lead_capture_enabled,
      model: form.model,
      temperature: form.temperature,
      max_tokens: form.max_tokens,
      welcome_message: form.welcome_message,
      system_prompt: form.system_prompt,
      suggested_questions: form.suggested_questions,
      services: form.services,
      faqs: form.faqs,
      clear_api_key: form.clear_api_key,
    };
    if (form.api_key && form.api_key.trim() !== '') payload.api_key = form.api_key.trim();

    const { data } = await axios.post('/api/admin/chatbot/settings', payload);
    settings.has_api_key = !!data.settings?.has_api_key;
    form.api_key = '';
    form.clear_api_key = false;
    setFlash('Chatbot settings saved.', 'success');
  } catch (err) {
    setFlash(err?.response?.data?.message || 'Failed to save settings.', 'error');
  } finally {
    saving.value = false;
  }
};

const loadConversations = async (page = 1) => {
  try {
    const { data } = await axios.get('/api/admin/chatbot/conversations', {
      params: { page, status: convFilters.status || undefined, search: convFilters.search || undefined },
    });
    conversations.value = data.data || [];
    convMeta.value = data;
  } catch (e) {
    setFlash('Could not load conversations.', 'error');
  }
};

const viewConversation = async (id) => {
  try {
    const { data } = await axios.get(`/api/admin/chatbot/conversations/${id}`);
    activeConversation.value = data;
  } catch (e) {
    setFlash('Could not load the conversation.', 'error');
  }
};

const deleteConversation = async (id) => {
  if (!confirm('Delete this conversation and all its messages?')) return;
  try {
    await axios.delete(`/api/admin/chatbot/conversations/${id}`);
    loadConversations(convMeta.value.current_page || 1);
    loadStats();
  } catch (e) {
    setFlash('Could not delete the conversation.', 'error');
  }
};

const loadLeads = async (page = 1) => {
  try {
    const { data } = await axios.get('/api/admin/chatbot/leads', {
      params: { page, status: leadFilters.status || undefined, search: leadFilters.search || undefined },
    });
    leads.value = data.data || [];
    leadMeta.value = data;
  } catch (e) {
    setFlash('Could not load leads.', 'error');
  }
};

const openLead = (lead) => {
  activeLead.value = lead;
  leadEdit.status = lead.status || 'new';
  leadEdit.notes = lead.notes || '';
};

const saveLead = async () => {
  saving.value = true;
  try {
    const { data } = await axios.patch(`/api/admin/chatbot/leads/${activeLead.value.id}`, {
      status: leadEdit.status,
      notes: leadEdit.notes,
    });
    setFlash('Lead updated.', 'success');
    activeLead.value = data.lead;
    loadLeads(leadMeta.value.current_page || 1);
    loadStats();
  } catch (e) {
    setFlash('Could not update the lead.', 'error');
  } finally {
    saving.value = false;
  }
};

const deleteLead = async (id) => {
  if (!confirm('Delete this lead?')) return;
  try {
    await axios.delete(`/api/admin/chatbot/leads/${id}`);
    loadLeads(leadMeta.value.current_page || 1);
    loadStats();
  } catch (e) {
    setFlash('Could not delete the lead.', 'error');
  }
};

const switchTab = (name) => {
  tab.value = name;
  if (name === 'conversations' && !conversations.value.length) loadConversations(1);
  if (name === 'leads' && !leads.value.length) loadLeads(1);
};

const formatDate = (value) => {
  if (!value) return '—';
  const d = new Date(value);
  return isNaN(d.getTime()) ? '—' : d.toLocaleString();
};

onMounted(() => {
  loadStats();
  loadSettings();
});
</script>

<style scoped>
.cb-hint { color: #64748b; font-size: 13.5px; margin: -6px 0 18px; }
.cb-form { display: flex; flex-direction: column; gap: 20px; }
.cb-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; }
.cb-field { display: flex; flex-direction: column; gap: 6px; }
.cb-field label { font-weight: 700; font-size: 13px; color: #334155; }
.cb-field input, .cb-field select, .cb-field textarea {
  width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; background: #fff; font-family: inherit;
}
.cb-field input:focus, .cb-field select:focus, .cb-field textarea:focus { outline: none; border-color: var(--teal, #0E4B44); box-shadow: 0 0 0 3px rgba(14,75,68,.12); }
.cb-field small { font-size: 12px; color: #64748b; }
.cb-ok { color: #166534 !important; font-weight: 600; }
.cb-warn { color: #b45309 !important; font-weight: 600; }
.cb-row-inline { display: flex; gap: 32px; flex-wrap: wrap; }

.cb-switch { display: inline-flex; align-items: center; gap: 10px; cursor: pointer; font-size: 14px; color: #334155; }
.cb-switch input { display: none; }
.cb-slider { width: 42px; height: 24px; background: #cbd5e1; border-radius: 999px; position: relative; transition: .2s; flex: none; }
.cb-slider::after { content: ''; position: absolute; top: 3px; left: 3px; width: 18px; height: 18px; background: #fff; border-radius: 50%; transition: .2s; }
.cb-switch input:checked + .cb-slider { background: var(--teal, #0E4B44); }
.cb-switch input:checked + .cb-slider::after { transform: translateX(18px); }
.cb-check { display: flex; align-items: center; gap: 8px; font-size: 13px; color: #b91c1c; margin-top: 4px; }

.cb-repeat { display: flex; gap: 8px; align-items: flex-start; margin-bottom: 8px; }
.cb-repeat-block { flex-wrap: wrap; }
.cb-repeat-block input { flex: 1 1 40%; }
.cb-repeat-block textarea { flex: 1 1 100%; }
.cb-repeat input, .cb-repeat textarea { padding: 9px 11px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13.5px; font-family: inherit; }
.cb-del { flex: none; background: #fee2e2; color: #b91c1c; border: none; border-radius: 8px; width: 38px; height: 38px; cursor: pointer; font-weight: 700; }
.cb-add { align-self: flex-start; background: transparent; border: 1px dashed var(--teal, #0E4B44); color: var(--teal, #0E4B44); padding: 8px 14px; border-radius: 8px; cursor: pointer; font-weight: 700; font-size: 13px; }

.cb-actions { display: flex; gap: 12px; }

.cb-tabs { display: flex; gap: 8px; margin-bottom: 20px; flex-wrap: wrap; }
.cb-tabs button { border: 1px solid var(--line, #DCD5C4); background: #fff; color: #334155; padding: 10px 18px; border-radius: 10px; cursor: pointer; font-weight: 700; font-size: 14px; }
.cb-tabs button.active { background: var(--teal, #0E4B44); color: #fff; border-color: var(--teal, #0E4B44); }
.cb-pill { background: var(--gold, #C69A3E); color: var(--teal-dark, #0A3733); border-radius: 999px; font-size: 11px; padding: 1px 8px; margin-left: 6px; }

.cb-filters { display: flex; gap: 10px; margin-bottom: 16px; flex-wrap: wrap; }
.cb-filters select, .cb-filters input { padding: 9px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; }
.cb-table-wrap { overflow-x: auto; }
.cb-ellipsis { max-width: 220px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.cb-actions-cell { white-space: nowrap; }
.cb-link { background: none; border: none; color: var(--teal, #0E4B44); font-weight: 700; cursor: pointer; padding: 4px 6px; font-size: 13px; }
.cb-link.cb-danger { color: #b91c1c; }
.cb-empty { text-align: center; color: #94a3b8; padding: 24px; }
.cb-badge { background: #dcfce7; color: #166534; border-radius: 999px; font-size: 11px; font-weight: 700; padding: 2px 10px; }

.cb-stat { background: #fff; border: 1px solid var(--line, #DCD5C4); border-radius: 16px; padding: 20px; display: flex; flex-direction: column; gap: 6px; }
.cb-stat span { font-size: 12px; text-transform: uppercase; letter-spacing: .05em; color: #64748b; font-weight: 700; }
.cb-stat strong { font-size: 30px; color: var(--teal-dark, #0A3733); }
.cb-stat-accent { background: var(--gold, #C69A3E); border-color: var(--gold, #C69A3E); }
.cb-stat-accent span { color: var(--teal-dark, #0A3733); }
.cb-stat-accent strong { color: var(--teal-dark, #0A3733); }

.cb-status { border-radius: 999px; font-size: 11.5px; font-weight: 700; padding: 3px 11px; text-transform: capitalize; background: #e2e8f0; color: #334155; }
.cb-status-new { background: #dbeafe; color: #1d4ed8; }
.cb-status-contacted { background: #fef3c7; color: #b45309; }
.cb-status-qualified { background: #e0e7ff; color: #4338ca; }
.cb-status-won { background: #dcfce7; color: #166534; }
.cb-status-lost { background: #fee2e2; color: #991b1b; }

.cb-pager { display: flex; align-items: center; gap: 14px; margin-top: 16px; justify-content: flex-end; font-size: 13px; color: #64748b; }

.cb-modal { position: fixed; inset: 0; background: rgba(10,55,51,.55); z-index: 1000; display: flex; align-items: center; justify-content: center; padding: 20px; }
.cb-modal-box { background: #fff; border-radius: 16px; width: 640px; max-width: 100%; max-height: 85vh; display: flex; flex-direction: column; overflow: hidden; }
.cb-modal-sm { width: 460px; }
.cb-modal-head { display: flex; justify-content: space-between; align-items: center; padding: 18px 22px; border-bottom: 1px solid var(--line, #DCD5C4); }
.cb-modal-head h3 { margin: 0; font-size: 16px; }
.cb-modal-box > .cb-field, .cb-modal-box > .cb-lead-grid, .cb-modal-box > .cb-actions { padding: 0 22px; }
.cb-modal-box > .cb-actions { padding-bottom: 22px; margin-top: 14px; }
.cb-modal-sm .cb-field:first-of-type { margin-top: 18px; }

.cb-transcript { padding: 18px 22px; overflow-y: auto; display: flex; flex-direction: column; gap: 12px; }
.cb-msg { border-radius: 12px; padding: 10px 14px; max-width: 86%; }
.cb-msg.user { align-self: flex-end; background: var(--teal, #0E4B44); color: #fff; }
.cb-msg.assistant { align-self: flex-start; background: #f1f5f9; color: #14332c; }
.cb-msg-role { font-size: 10.5px; text-transform: uppercase; letter-spacing: .06em; opacity: .7; font-weight: 800; margin-bottom: 3px; }
.cb-msg-text { font-size: 14px; line-height: 1.5; white-space: pre-wrap; }
.cb-msg-time { font-size: 10.5px; opacity: .6; margin-top: 4px; }

.cb-lead-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 18px; }
.cb-lead-grid .full { grid-column: 1 / -1; }
.cb-lead-grid > div { display: flex; flex-direction: column; gap: 3px; }
.cb-lead-grid b { font-size: 12px; color: #64748b; text-transform: uppercase; letter-spacing: .04em; }
.cb-lead-grid span { font-size: 14px; color: #14332c; }

.flash { padding: 12px 16px; border-radius: 8px; font-size: 14px; margin-bottom: 18px; }
.flash.success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
.flash.error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }

@media (max-width: 640px) {
  .cb-grid { grid-template-columns: 1fr; }
  .cb-lead-grid { grid-template-columns: 1fr; }
}
</style>
