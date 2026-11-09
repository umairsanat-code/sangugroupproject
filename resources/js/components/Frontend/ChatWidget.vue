<template>
  <div v-if="ready && config.enabled" class="sangu-chat">
    <!-- Launcher -->
    <button
      v-show="!open"
      class="sangu-chat-launcher"
      type="button"
      aria-label="Open chat assistant"
      @click="toggle"
    >
      <svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z" />
      </svg>
      <span class="sangu-chat-launcher-dot"></span>
    </button>

    <!-- Panel -->
    <transition name="sangu-pop">
      <section v-show="open" class="sangu-chat-panel" role="dialog" aria-label="Chat assistant">
        <header class="sangu-chat-header">
          <div class="sangu-chat-identity">
            <div class="sangu-chat-avatar">
              <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 2a7 7 0 0 0-7 7c0 2.4 1.2 4.5 3 5.7V19a1 1 0 0 0 1.6.8L12 18.5l2.4 1.3A1 1 0 0 0 16 19v-4.3c1.8-1.2 3-3.3 3-5.7a7 7 0 0 0-7-7z" />
              </svg>
            </div>
            <div>
              <strong>{{ config.title || 'AI Assistant' }}</strong>
              <span class="sangu-chat-status"><i></i> Online</span>
            </div>
          </div>
          <button class="sangu-chat-close" type="button" aria-label="Close chat" @click="toggle">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M6 6l12 12M18 6L6 18" /></svg>
          </button>
        </header>

        <div ref="scrollArea" class="sangu-chat-body">
          <div
            v-for="(msg, i) in messages"
            :key="i"
            class="sangu-chat-row"
            :class="msg.role === 'user' ? 'is-user' : 'is-bot'"
          >
            <div class="sangu-chat-bubble" :class="{ 'is-error': msg.error }" v-html="format(msg.content)"></div>
          </div>

          <div v-if="loading" class="sangu-chat-row is-bot">
            <div class="sangu-chat-bubble sangu-chat-typing" aria-label="Assistant is typing">
              <span></span><span></span><span></span>
            </div>
          </div>

          <div v-if="!loading && showSuggestions" class="sangu-chat-suggestions">
            <button
              v-for="(q, i) in config.suggested_questions"
              :key="i"
              type="button"
              class="sangu-chat-chip"
              @click="send(q)"
            >{{ q }}</button>
          </div>
        </div>

        <form class="sangu-chat-input" @submit.prevent="send()">
          <input
            ref="inputEl"
            v-model="input"
            type="text"
            :disabled="loading"
            maxlength="2000"
            placeholder="Type your message…"
            autocomplete="off"
          >
          <button type="submit" :disabled="loading || !input.trim()" aria-label="Send message">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z" /></svg>
          </button>
        </form>
        <div class="sangu-chat-footer">Powered by SanguGroup AI</div>
      </section>
    </transition>
  </div>
</template>

<script setup>
import { ref, computed, nextTick, onMounted } from 'vue';

const STORAGE_KEY = 'sangu_chat_token';

const ready = ref(false);
const open = ref(false);
const loaded = ref(false);
const loading = ref(false);
const input = ref('');
const messages = ref([]);
const config = ref({ enabled: false, title: 'AI Assistant', welcome_message: '', suggested_questions: [] });
const token = ref(localStorage.getItem(STORAGE_KEY) || null);
const scrollArea = ref(null);
const inputEl = ref(null);

const showSuggestions = computed(() =>
  (config.value.suggested_questions?.length > 0) && messages.value.length <= 1 && !loading.value
);

onMounted(async () => {
  try {
    const { data } = await window.axios.get('/api/chat/config');
    config.value = { ...config.value, ...data };
  } catch (e) {
    config.value.enabled = false;
  } finally {
    ready.value = true;
  }
});

const toggle = () => {
  open.value = !open.value;
  if (open.value) {
    ensureLoaded();
    nextTick(() => inputEl.value?.focus());
  }
};

const ensureLoaded = async () => {
  if (loaded.value) return;
  loaded.value = true;

  if (config.value.welcome_message) {
    messages.value.push({ role: 'assistant', content: config.value.welcome_message });
  }

  if (token.value) {
    try {
      const { data } = await window.axios.get('/api/chat/history', { params: { token: token.value } });
      if (data.messages && data.messages.length) {
        messages.value = data.messages.map((m) => ({ role: m.role, content: m.content }));
      }
    } catch (e) {
      /* keep the local welcome message on failure */
    }
  }
  scrollToBottom();
};

const send = async (preset) => {
  const content = (preset ?? input.value).trim();
  if (!content || loading.value) return;

  messages.value.push({ role: 'user', content });
  input.value = '';
  loading.value = true;
  scrollToBottom();

  try {
    const { data } = await window.axios.post('/api/chat/message', {
      message: content,
      conversation_token: token.value,
      page_url: window.location.pathname,
    });

    if (data.conversation_token) {
      token.value = data.conversation_token;
      localStorage.setItem(STORAGE_KEY, data.conversation_token);
    }

    messages.value.push({
      role: 'assistant',
      content: data.reply || 'Sorry, I could not generate a response.',
      error: !!data.error,
    });
  } catch (err) {
    const friendly = err?.response?.data?.message
      || 'Sorry, we could not send your message right now. Please check your connection and try again.';
    messages.value.push({ role: 'assistant', content: friendly, error: true });
  } finally {
    loading.value = false;
    scrollToBottom();
  }
};

const scrollToBottom = () => {
  nextTick(() => {
    if (scrollArea.value) scrollArea.value.scrollTop = scrollArea.value.scrollHeight;
  });
};

// Escape HTML, then allow the model's newlines to render as line breaks.
const format = (text) => {
  const safe = String(text ?? '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;');
  return safe.replace(/\n/g, '<br>');
};
</script>

<style scoped>
.sangu-chat { position: fixed; right: 20px; bottom: 20px; z-index: 99999; font-family: 'Plus Jakarta Sans', 'Inter', system-ui, sans-serif; }

.sangu-chat-launcher {
  position: relative;
  width: 60px; height: 60px; border-radius: 50%; border: none; cursor: pointer;
  background: linear-gradient(135deg, #0E4B44 0%, #16715d 100%);
  color: #fff; display: flex; align-items: center; justify-content: center;
  box-shadow: 0 12px 30px rgba(14, 75, 68, .35);
  transition: transform .2s ease, box-shadow .2s ease;
}
.sangu-chat-launcher:hover { transform: translateY(-2px) scale(1.04); box-shadow: 0 16px 36px rgba(14, 75, 68, .45); }
.sangu-chat-launcher-dot {
  position: absolute; top: 6px; right: 6px; width: 12px; height: 12px; border-radius: 50%;
  background: #ff4d4f; border: 2px solid #fff; animation: sangu-pulse 2s infinite;
}
@keyframes sangu-pulse { 0% { box-shadow: 0 0 0 0 rgba(255,77,79,.6); } 70% { box-shadow: 0 0 0 8px rgba(255,77,79,0); } 100% { box-shadow: 0 0 0 0 rgba(255,77,79,0); } }

.sangu-chat-panel {
  position: absolute; right: 0; bottom: 74px;
  width: 370px; max-width: calc(100vw - 40px); height: 560px; max-height: calc(100vh - 120px);
  background: #fff; border-radius: 18px; overflow: hidden;
  display: flex; flex-direction: column;
  box-shadow: 0 24px 60px rgba(10, 55, 51, .28);
  border: 1px solid #e5e7eb;
}

.sangu-chat-header {
  display: flex; align-items: center; justify-content: space-between;
  padding: 14px 16px; color: #fff;
  background: linear-gradient(135deg, #0E4B44 0%, #16715d 100%);
}
.sangu-chat-identity { display: flex; align-items: center; gap: 11px; }
.sangu-chat-avatar {
  width: 38px; height: 38px; border-radius: 50%; background: rgba(255,255,255,.18);
  display: flex; align-items: center; justify-content: center;
}
.sangu-chat-identity strong { display: block; font-size: 15px; font-weight: 700; line-height: 1.2; }
.sangu-chat-status { display: inline-flex; align-items: center; gap: 5px; font-size: 11px; opacity: .9; }
.sangu-chat-status i { width: 7px; height: 7px; border-radius: 50%; background: #7CFC9B; display: inline-block; }
.sangu-chat-close { background: transparent; border: none; color: #fff; cursor: pointer; opacity: .85; padding: 4px; border-radius: 8px; }
.sangu-chat-close:hover { opacity: 1; background: rgba(255,255,255,.12); }

.sangu-chat-body { flex: 1; overflow-y: auto; padding: 16px; background: #f6f8f8; display: flex; flex-direction: column; gap: 10px; }
.sangu-chat-row { display: flex; }
.sangu-chat-row.is-user { justify-content: flex-end; }
.sangu-chat-row.is-bot { justify-content: flex-start; }
.sangu-chat-bubble {
  max-width: 82%; padding: 10px 13px; border-radius: 14px; font-size: 14px; line-height: 1.5;
  color: #14332c; background: #fff; border: 1px solid #e6ebea;
  box-shadow: 0 1px 2px rgba(0,0,0,.04); word-wrap: break-word; overflow-wrap: anywhere;
}
.is-user .sangu-chat-bubble { background: linear-gradient(135deg, #0E4B44 0%, #16715d 100%); color: #fff; border-color: transparent; border-bottom-right-radius: 4px; }
.is-bot .sangu-chat-bubble { border-bottom-left-radius: 4px; }
.sangu-chat-bubble.is-error { background: #fff5f5; border-color: #fecaca; color: #991b1b; }

.sangu-chat-typing { display: inline-flex; gap: 5px; align-items: center; padding: 13px; }
.sangu-chat-typing span { width: 7px; height: 7px; border-radius: 50%; background: #9aa8a5; animation: sangu-bounce 1.3s infinite ease-in-out; }
.sangu-chat-typing span:nth-child(2) { animation-delay: .15s; }
.sangu-chat-typing span:nth-child(3) { animation-delay: .3s; }
@keyframes sangu-bounce { 0%, 60%, 100% { transform: translateY(0); opacity: .5; } 30% { transform: translateY(-5px); opacity: 1; } }

.sangu-chat-suggestions { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 4px; }
.sangu-chat-chip {
  border: 1px solid #cfe0da; background: #fff; color: #0E4B44; font-size: 12.5px; font-weight: 600;
  padding: 8px 12px; border-radius: 999px; cursor: pointer; transition: all .15s ease; text-align: left;
}
.sangu-chat-chip:hover { background: #0E4B44; color: #fff; border-color: #0E4B44; }

.sangu-chat-input { display: flex; gap: 8px; padding: 12px; background: #fff; border-top: 1px solid #eef1f1; }
.sangu-chat-input input {
  flex: 1; border: 1px solid #d7e0dd; border-radius: 999px; padding: 11px 15px; font-size: 14px; outline: none; color: #14332c;
}
.sangu-chat-input input:focus { border-color: #16715d; box-shadow: 0 0 0 3px rgba(22,113,93,.14); }
.sangu-chat-input button {
  width: 44px; height: 44px; flex: none; border-radius: 50%; border: none; cursor: pointer;
  background: linear-gradient(135deg, #0E4B44 0%, #16715d 100%); color: #fff; display: flex; align-items: center; justify-content: center;
}
.sangu-chat-input button:disabled { opacity: .5; cursor: not-allowed; }
.sangu-chat-footer { text-align: center; font-size: 10.5px; color: #93a3a0; padding: 0 0 8px; background: #fff; }

.sangu-pop-enter-active, .sangu-pop-leave-active { transition: opacity .18s ease, transform .18s ease; }
.sangu-pop-enter-from, .sangu-pop-leave-to { opacity: 0; transform: translateY(12px) scale(.98); }

@media (max-width: 640px) {
  .sangu-chat { right: 16px; bottom: 16px; }
  .sangu-chat-panel {
    position: fixed; right: 0; left: 0; bottom: 0; top: 0;
    width: 100%; max-width: 100%; height: 100%; max-height: 100%; border-radius: 0;
  }
}
</style>
