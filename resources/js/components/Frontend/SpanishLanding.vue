<template>
  <div class="spanish-landing-page">
    <!-- HERO SECTION -->
    <section class="sp-hero" :style="{ backgroundImage: `linear-gradient(135deg, rgba(10, 25, 47, 0.92) 0%, rgba(0, 30, 67, 0.85) 100%), url(${pageData.heroImage})` }">
      <div class="container sp-hero-container">
        <!-- Breadcrumbs -->
        <nav class="sp-breadcrumb" aria-label="Breadcrumb">
          <router-link to="/">Inicio</router-link>
          <span class="sep">/</span>
          <span class="current">{{ pageData.breadcrumb }}</span>
        </nav>

        <div class="sp-hero-grid">
          <div class="sp-hero-text">
            <div class="sp-hero-badge">
              <span class="sp-badge-dot"></span>
              <span>{{ pageData.badge }}</span>
            </div>
            <h1 class="sp-hero-title">{{ pageData.h1 }}</h1>
            <p class="sp-hero-sub">{{ pageData.subtitle }}</p>

            <div class="sp-hero-features">
              <div class="sp-feature-item" v-for="(feat, idx) in pageData.heroHighlights" :key="idx">
                <i class="fa-solid fa-check-circle"></i>
                <span>{{ feat }}</span>
              </div>
            </div>

            <div class="sp-hero-actions">
              <a :href="getWhatsAppUrl(pageData.whatsappPrefill)" target="_blank" rel="noopener" class="btn btn-gold btn-sp-hero">
                <i class="fa-brands fa-whatsapp"></i> Consultar por WhatsApp
              </a>
              <a href="#solicitar-presupuesto" class="btn btn-outline-light btn-sp-hero">
                <i class="fa-solid fa-paper-plane"></i> Pedir Presupuesto Gratis
              </a>
            </div>
          </div>

          <!-- Quick Quote Box -->
          <div class="sp-quick-quote-card" id="solicitar-presupuesto">
            <div class="sp-quote-head">
              <h3>Solicitar Presupuesto Rápido</h3>
              <p>Atención personalizada en español, urdu e inglés desde nuestra oficina en Barcelona</p>
            </div>

            <form @submit.prevent="submitEnquiry" class="sp-quote-form">
              <div class="sp-form-group">
                <label>Nombre Completo *</label>
                <input type="text" v-model="form.name" placeholder="Ej. Muhammad Ali" required>
              </div>

              <div class="sp-form-row">
                <div class="sp-form-group">
                  <label>Teléfono / WhatsApp *</label>
                  <input type="tel" v-model="form.phone" placeholder="+34 600 000 000" required>
                </div>
                <div class="sp-form-group">
                  <label>Correo Electrónico</label>
                  <input type="email" v-model="form.email" placeholder="correo@ejemplo.com">
                </div>
              </div>

              <div class="sp-form-row">
                <div class="sp-form-group">
                  <label>Servicio Solicitado</label>
                  <select v-model="form.service">
                    <option value="Umrah desde España">Paquete Umrah desde España</option>
                    <option value="Umrah desde Barcelona">Umrah con salida desde Barcelona</option>
                    <option value="Vuelos Internacionales">Vuelos Internacionales (Pakistán, Dubái...)</option>
                    <option value="Visados">Gestión de Visados (Arabia, EAU, UK...)</option>
                    <option value="Hoteles Meca y Medina">Hoteles en La Meca y Medina</option>
                    <option value="Umrah 2027">Reserva Anticipada Umrah 2027</option>
                  </select>
                </div>
                <div class="sp-form-group">
                  <label>Viajeros / Personas</label>
                  <select v-model="form.travelers">
                    <option value="1 persona">1 persona</option>
                    <option value="2 personas (Pareja)">2 personas (Pareja)</option>
                    <option value="3-4 personas (Familia)">3-4 personas (Familia)</option>
                    <option value="Grupo 5+ personas">Grupo (+5 personas)</option>
                  </select>
                </div>
              </div>

              <div class="sp-form-group">
                <label>Detalles / Fechas aproximadas</label>
                <textarea rows="2" v-model="form.message" placeholder="¿Cuándo desea viajar? ¿Aeropuerto de preferencia?"></textarea>
              </div>

              <div class="sp-quote-buttons">
                <button type="submit" class="btn btn-primary btn-block" :disabled="submitting">
                  <i class="fa-solid fa-envelope"></i> {{ submitting ? 'Enviando consulta...' : 'Enviar Solicitud Online' }}
                </button>
                <button type="button" @click="sendDirectWhatsApp" class="btn btn-gold btn-block" style="margin-top: 8px;">
                  <i class="fa-brands fa-whatsapp"></i> Enviar Directo a WhatsApp Barcelona
                </button>
              </div>

              <p v-if="submitSuccess" class="sp-form-success">
                ✓ ¡Gracias! Hemos recibido su solicitud. Un asesor de nuestra oficina de Badalona le contactará a la brevedad.
              </p>
              <p v-if="submitError" class="sp-form-error">
                Hubo un error al enviar su consulta. Por favor contáctenos directamente al WhatsApp +34 672 06 81 48.
              </p>
            </form>
          </div>
        </div>
      </div>
    </section>

    <!-- TRUST STRIP -->
    <section class="sp-trust-strip">
      <div class="container sp-trust-container">
        <div class="sp-trust-item">
          <i class="fa-solid fa-location-dot"></i>
          <div>
            <strong>Oficina Presencial en Barcelona</strong>
            <span>Av. Alfonso XIII 496, Badalona</span>
          </div>
        </div>
        <div class="sp-trust-item">
          <i class="fa-solid fa-kaaba"></i>
          <div>
            <strong>Especialistas Oficiales en Umrah</strong>
            <span>Hoteles 5★ y asistencia con Muallim</span>
          </div>
        </div>
        <div class="sp-trust-item">
          <i class="fa-solid fa-plane-departure"></i>
          <div>
            <strong>Vuelos Directos y con Conexión</strong>
            <span>Salidas desde Barcelona BCN y Madrid MAD</span>
          </div>
        </div>
        <div class="sp-trust-item">
          <i class="fa-solid fa-language"></i>
          <div>
            <strong>Atención Multilingüe 24/7</strong>
            <span>Español, Urdu, Punjabi e Inglés</span>
          </div>
        </div>
      </div>
    </section>

    <!-- DYNAMIC CORE CONTENT BY PAGE TYPE -->
    <section class="sp-content-section">
      <div class="container">
        <!-- Section Intro -->
        <div class="sp-section-head">
          <span class="eyebrow">{{ pageData.sectionEyebrow }}</span>
          <h2>{{ pageData.sectionTitle }}</h2>
          <p class="sp-lead">{{ pageData.sectionLead }}</p>
        </div>

        <!-- Cards Grid (if packages, visas, flights, hotels) -->
        <div class="grid grid-3 sp-items-grid" v-if="pageData.items && pageData.items.length">
          <div class="card sp-item-card" v-for="(item, i) in pageData.items" :key="i">
            <div class="card-arch sp-card-img-wrap">
              <span class="badge" v-if="item.badge">{{ item.badge }}</span>
              <img :src="item.image" :alt="item.title" loading="lazy">
            </div>
            <div class="card-body">
              <div class="sp-item-category" v-if="item.category">{{ item.category }}</div>
              <h3>{{ item.title }}</h3>
              <p class="desc">{{ item.description }}</p>

              <ul class="sp-item-perks" v-if="item.perks">
                <li v-for="(p, pi) in item.perks" :key="pi">
                  <i class="fa-solid fa-check"></i> {{ p }}
                </li>
              </ul>

              <div class="card-foot sp-card-foot">
                <div class="price" v-if="item.price">
                  <small>{{ item.pricePrefix || 'Desde' }}</small>
                  <b>{{ item.price }}</b>
                  <small v-if="item.priceUnit">{{ item.priceUnit }}</small>
                </div>
                <a :href="getWhatsAppUrl(item.whatsappMsg || ('Hola, solicito información sobre: ' + item.title))" target="_blank" rel="noopener" class="btn btn-gold btn-sm">
                  <i class="fa-brands fa-whatsapp"></i> Reservar
                </a>
              </div>
            </div>
          </div>
        </div>

        <!-- In-depth Guide Articles (Structured Content for SEO) -->
        <div class="sp-editorial-block">
          <div class="sp-editorial-grid">
            <div class="sp-editorial-main">
              <article class="sp-article-body">
                <div v-for="(block, bIdx) in pageData.editorialBlocks" :key="bIdx" class="sp-content-block">
                  <h3>{{ block.heading }}</h3>
                  <p v-for="(pText, pIdx) in block.paragraphs" :key="pIdx">{{ pText }}</p>
                  
                  <ul v-if="block.list" class="sp-rich-list">
                    <li v-for="(lItem, lIdx) in block.list" :key="lIdx">
                      <strong>{{ lItem.title }}:</strong> {{ lItem.desc }}
                    </li>
                  </ul>
                </div>
              </article>

              <!-- AIRLINE PARTNERS STRIP FOR FLIGHT/TRAVEL PAGES -->
              <div class="sp-airlines-box" v-if="pageData.showAirlines">
                <h4>Aerolíneas Asociadas con Salidas desde España</h4>
                <p>Reservamos sus billetes con las mejores aerolíneas mundiales, garantizando franquicia de equipaje de hasta 2 maletas de 23 kg o 40 kg para Pakistán y Oriente Medio:</p>
                <div class="sp-airline-logos-grid">
                  <div class="sp-airline-pill" v-for="air in ['Emirates', 'Saudia', 'Qatar Airways', 'PIA (Pakistan Int. Airlines)', 'Turkish Airlines', 'Etihad Airways', 'EgyptAir', 'Royal Jordanian']" :key="air">
                    <i class="fa-solid fa-plane"></i> {{ air }}
                  </div>
                </div>
              </div>

              <!-- FAQ ACCORDION -->
              <div class="sp-faq-section" v-if="pageData.faqs && pageData.faqs.length">
                <div class="sp-faq-header">
                  <span class="eyebrow">Preguntas Frecuentes</span>
                  <h3>Dudas Frecuentes sobre {{ pageData.shortTopic }}</h3>
                </div>

                <div class="sp-faq-list">
                  <div
                    class="sp-faq-item"
                    v-for="(faq, fIdx) in pageData.faqs"
                    :key="fIdx"
                    :class="{ open: openFaqIndex === fIdx }"
                  >
                    <button class="sp-faq-question" @click="toggleFaq(fIdx)" :aria-expanded="openFaqIndex === fIdx">
                      <span>{{ faq.question }}</span>
                      <i class="fa-solid" :class="openFaqIndex === fIdx ? 'fa-minus' : 'fa-plus'"></i>
                    </button>
                    <div class="sp-faq-answer" v-show="openFaqIndex === fIdx">
                      <p>{{ faq.answer }}</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- SIDEBAR -->
            <aside class="sp-editorial-sidebar">
              <!-- Office Card -->
              <div class="sp-sidebar-card sp-office-card">
                <div class="sp-office-badge">🇪🇸 Oficina Central España</div>
                <h4>Fly Planet Badalona (SanguGroup)</h4>
                <p class="sp-office-address">
                  <i class="fa-solid fa-location-dot"></i> Av. Alfonso XIII 496, 08913 Badalona, Barcelona, España
                </p>
                <div class="sp-office-phones">
                  <a href="tel:+34672068148" class="sp-phone-link">
                    <i class="fa-solid fa-phone"></i> +34 672 06 81 48
                  </a>
                  <a href="tel:+34631028871" class="sp-phone-link">
                    <i class="fa-solid fa-phone"></i> +34 631 02 88 71
                  </a>
                  <a href="tel:+34672068131" class="sp-phone-link">
                    <i class="fa-solid fa-phone"></i> +34 672 06 81 31
                  </a>
                </div>
                <div class="sp-office-hours">
                  <i class="fa-solid fa-clock"></i> Lunes a Sábado: 09:30 - 20:30 CET
                </div>
                <a :href="getWhatsAppUrl('Hola, quiero concertar una cita en su oficina de Badalona')" target="_blank" rel="noopener" class="btn btn-gold btn-block" style="margin-top: 14px;">
                  <i class="fa-brands fa-whatsapp"></i> Hablar con la Oficina de Barcelona
                </a>
              </div>

              <!-- Pakistan Office Card -->
              <div class="sp-sidebar-card sp-pk-card">
                <div class="sp-office-badge pk">🇵🇰 Oficina Pakistán</div>
                <h4>Sangu Travel & Tours Pakistán</h4>
                <p class="sp-office-address">
                  <i class="fa-solid fa-phone-volume"></i> +92 308 5092631
                </p>
                <p style="font-size: 13px; color: var(--ink-soft); margin-bottom: 12px;">
                  Soporte 24 horas para reservas, emisión de billetes aéreos de retorno y coordinación con peregrinos y familias.
                </p>
                <a href="https://wa.me/923085092631?text=Hola%2C+contacto+con+SanguGroup+Pakistan" target="_blank" rel="noopener" class="btn btn-ghost btn-block btn-sm">
                  <i class="fa-brands fa-whatsapp"></i> WhatsApp Pakistán
                </a>
              </div>

              <!-- Internal Links Navigation -->
              <div class="sp-sidebar-card sp-nav-card">
                <h4>Servicios Relacionados en España</h4>
                <ul class="sp-side-links">
                  <li><router-link to="/umrah-desde-espana">🕋 Umrah desde España</router-link></li>
                  <li><router-link to="/umrah-desde-barcelona">✈️ Umrah desde Barcelona</router-link></li>
                  <li><router-link to="/paquetes-umrah">📦 Paquetes Umrah 2026-2027</router-link></li>
                  <li><router-link to="/agencia-viajes-barcelona">🏢 Agencia de Viajes Barcelona</router-link></li>
                  <li><router-link to="/vuelos-internacionales">🛫 Vuelos Internacionales</router-link></li>
                  <li><router-link to="/visados">🛂 Gestión de Visados</router-link></li>
                  <li><router-link to="/hoteles-la-meca">🏨 Hoteles en La Meca y Medina</router-link></li>
                  <li><router-link to="/umrah-2027">✨ Umrah Temporada 2027</router-link></li>
                </ul>
              </div>
            </aside>
          </div>
        </div>
      </div>
    </section>

    <!-- BOTTOM CTA BANNER -->
    <section class="sp-cta-banner">
      <div class="container sp-cta-content">
        <span class="eyebrow" style="color: var(--gold);">Atención Inmediata</span>
        <h2>¿Preparando su Viaje o Peregrinación? Hable con un Asesor Hoy</h2>
        <p>Le ayudamos a comparar vuelos, seleccionar el mejor paquete de Umrah adaptado a su presupuesto o tramitar su visado sin complicaciones.</p>
        <div class="sp-cta-actions">
          <a :href="getWhatsAppUrl('Hola SanguGroup, quiero recibir presupuesto personalizado')" target="_blank" rel="noopener" class="btn btn-gold">
            <i class="fa-brands fa-whatsapp"></i> Chat WhatsApp Directo (+34 672 06 81 48)
          </a>
          <router-link to="/contact" class="btn btn-outline-light">
            <i class="fa-solid fa-map-location-dot"></i> Visitar Nuestra Oficina en Badalona
          </router-link>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import { spanishPagesDatabase } from './spanishData.js';

const route = useRoute();

// Current page data resolved from route path
const pageData = computed(() => {
  const currentPath = route.path.replace(/\/$/, '');
  return spanishPagesDatabase[currentPath] || spanishPagesDatabase['/umrah-desde-espana'];
});

// Form submission
const form = ref({
  name: '',
  phone: '',
  email: '',
  service: 'Umrah desde España',
  travelers: '2 personas (Pareja)',
  message: '',
});
const submitting = ref(false);
const submitSuccess = ref(false);
const submitError = ref(false);
const openFaqIndex = ref(0);

// Initialize service according to page
watch(() => pageData.value, (newVal) => {
  if (newVal && newVal.defaultService) {
    form.value.service = newVal.defaultService;
  }
}, { immediate: true });

const toggleFaq = (idx) => {
  openFaqIndex.value = openFaqIndex.value === idx ? null : idx;
};

const getWhatsAppUrl = (customText) => {
  const basePhone = '34672068148';
  const text = encodeURIComponent(customText || 'Hola SanguGroup, deseo solicitar información sobre viajes y Umrah desde España.');
  return `https://wa.me/${basePhone}?text=${text}`;
};

const sendDirectWhatsApp = () => {
  const basePhone = '34672068148';
  const msg = `*Nueva Solicitud de Presupuesto - SanguGroup Web*%0A` +
    `*Nombre:* ${encodeURIComponent(form.value.name || 'Cliente')}%0A` +
    `*Teléfono:* ${encodeURIComponent(form.value.phone || 'No especificado')}%0A` +
    `*Email:* ${encodeURIComponent(form.value.email || 'No especificado')}%0A` +
    `*Servicio:* ${encodeURIComponent(form.value.service)}%0A` +
    `*Personas:* ${encodeURIComponent(form.value.travelers)}%0A` +
    `*Detalles:* ${encodeURIComponent(form.value.message || 'Sin mensaje adicional')}`;
  window.open(`https://wa.me/${basePhone}?text=${msg}`, '_blank');
};

const submitEnquiry = async () => {
  submitting.value = true;
  submitSuccess.value = false;
  submitError.value = false;

  try {
    const payload = {
      name: form.value.name,
      phone: form.value.phone,
      email: form.value.email || null,
      subject: `[Web España] Solicitud: ${form.value.service} (${form.value.travelers})`,
      message: `Página de origen: ${route.path}\nServicio: ${form.value.service}\nViajeros: ${form.value.travelers}\nMensaje: ${form.value.message || 'Sin detalles adicionales'}`,
    };

    await axios.post('/api/inquiry', payload);
    submitSuccess.value = true;
    form.value.message = '';
  } catch (err) {
    console.error('Enquiry error:', err);
    submitError.value = true;
  } finally {
    submitting.value = false;
  }
};
</script>

<style scoped>
.spanish-landing-page {
  color: var(--ink);
  background-color: #fbfbfd;
}

/* HERO */
.sp-hero {
  position: relative;
  background-size: cover;
  background-position: center;
  color: #ffffff;
  padding: 60px 0 90px;
}

.sp-hero-container {
  position: relative;
  z-index: 2;
}

.sp-breadcrumb {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
  color: rgba(255, 255, 255, 0.7);
  margin-bottom: 24px;
}

.sp-breadcrumb a {
  color: rgba(255, 255, 255, 0.85);
  transition: color 0.2s;
}

.sp-breadcrumb a:hover {
  color: var(--gold);
}

.sp-breadcrumb .sep {
  opacity: 0.5;
}

.sp-breadcrumb .current {
  color: var(--gold);
  font-weight: 600;
}

.sp-hero-grid {
  display: grid;
  grid-template-columns: 1.15fr 0.85fr;
  gap: 48px;
  align-items: center;
}

.sp-hero-badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: rgba(197, 160, 89, 0.18);
  border: 1px solid rgba(197, 160, 89, 0.5);
  color: var(--gold);
  padding: 6px 14px;
  border-radius: 999px;
  font-size: 12.5px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  margin-bottom: 18px;
}

.sp-badge-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background-color: var(--gold);
}

.sp-hero-title {
  font-size: clamp(32px, 4.2vw, 48px);
  font-weight: 800;
  line-height: 1.15;
  color: #ffffff;
  margin-bottom: 16px;
  font-family: 'Fraunces', serif;
}

.sp-hero-sub {
  font-size: 17px;
  line-height: 1.6;
  color: rgba(255, 255, 255, 0.88);
  margin-bottom: 24px;
  max-width: 580px;
}

.sp-hero-features {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px 18px;
  margin-bottom: 30px;
}

.sp-feature-item {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 14px;
  font-weight: 600;
  color: rgba(255, 255, 255, 0.95);
}

.sp-feature-item i {
  color: var(--gold);
  font-size: 16px;
}

.sp-hero-actions {
  display: flex;
  gap: 14px;
  flex-wrap: wrap;
}

.btn-sp-hero {
  padding: 14px 24px;
  font-weight: 700;
  font-size: 14.5px;
}

/* QUICK QUOTE CARD */
.sp-quick-quote-card {
  background: #ffffff;
  border-radius: 20px;
  padding: 30px 28px;
  box-shadow: 0 20px 50px rgba(0, 0, 0, 0.35);
  color: var(--ink);
  border: 1px solid rgba(255, 255, 255, 0.8);
}

.sp-quote-head h3 {
  font-size: 21px;
  font-weight: 700;
  color: var(--teal-dark);
  margin-bottom: 6px;
  font-family: 'Plus Jakarta Sans', sans-serif;
}

.sp-quote-head p {
  font-size: 13px;
  color: var(--ink-soft);
  margin-bottom: 18px;
}

.sp-form-group {
  margin-bottom: 14px;
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.sp-form-group label {
  font-size: 12px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: var(--ink-soft);
}

.sp-form-group input,
.sp-form-group select,
.sp-form-group textarea {
  width: 100%;
  padding: 11px 14px;
  border-radius: 10px;
  border: 1px solid #d1d5db;
  font-size: 14px;
  font-family: inherit;
  background-color: #f9fafb;
  transition: all 0.2s;
  color: var(--ink);
}

.sp-form-group input:focus,
.sp-form-group select:focus,
.sp-form-group textarea:focus {
  background-color: #ffffff;
  border-color: var(--gold);
  outline: none;
  box-shadow: 0 0 0 3px rgba(197, 160, 89, 0.15);
}

.sp-form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}

.sp-form-success {
  margin-top: 12px;
  padding: 10px;
  background: #ecfdf5;
  border: 1px solid #a7f3d0;
  color: #065f46;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
}

.sp-form-error {
  margin-top: 12px;
  padding: 10px;
  background: #fef2f2;
  border: 1px solid #fecaca;
  color: #991b1b;
  border-radius: 8px;
  font-size: 13px;
}

/* TRUST STRIP */
.sp-trust-strip {
  background: #ffffff;
  border-bottom: 1px solid #e5e7eb;
  padding: 24px 0;
}

.sp-trust-container {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 20px;
}

.sp-trust-item {
  display: flex;
  align-items: center;
  gap: 14px;
}

.sp-trust-item i {
  font-size: 26px;
  color: var(--gold);
  flex-shrink: 0;
}

.sp-trust-item strong {
  display: block;
  font-size: 14px;
  color: var(--teal-dark);
  font-weight: 700;
}

.sp-trust-item span {
  font-size: 12.5px;
  color: var(--ink-soft);
}

/* CORE CONTENT */
.sp-content-section {
  padding: 70px 0;
}

.sp-section-head {
  text-align: center;
  max-width: 820px;
  margin: 0 auto 48px;
}

.sp-section-head h2 {
  font-size: clamp(26px, 3.2vw, 38px);
  color: var(--teal-dark);
  margin: 10px 0 14px;
  font-family: 'Fraunces', serif;
}

.sp-lead {
  font-size: 16.5px;
  color: var(--ink-soft);
  line-height: 1.6;
}

.sp-items-grid {
  margin-bottom: 60px;
}

.sp-item-card {
  border-radius: 16px;
  background: #ffffff;
  border: 1px solid #e5e7eb;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
  transition: transform 0.25s ease, box-shadow 0.25s ease;
}

.sp-item-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 18px 40px rgba(10, 25, 47, 0.12);
}

.sp-card-img-wrap {
  height: 200px;
  position: relative;
  overflow: hidden;
}

.sp-card-img-wrap img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.4s ease;
}

.sp-item-card:hover .sp-card-img-wrap img {
  transform: scale(1.05);
}

.sp-item-category {
  font-size: 11.5px;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  font-weight: 800;
  color: var(--gold-dark);
  margin-bottom: 6px;
}

.sp-item-perks {
  list-style: none;
  padding: 0;
  margin: 14px 0 20px;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.sp-item-perks li {
  font-size: 13.5px;
  color: var(--ink-soft);
  display: flex;
  align-items: center;
  gap: 8px;
}

.sp-item-perks li i {
  color: #10b981;
  font-size: 12px;
}

.sp-card-foot {
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-top: 1px solid #f1f5f9;
  padding-top: 14px;
}

.sp-card-foot .price small {
  display: block;
  font-size: 11px;
  color: var(--ink-soft);
}

.sp-card-foot .price b {
  font-size: 22px;
  color: var(--teal-dark);
}

/* EDITORIAL GRID */
.sp-editorial-grid {
  display: grid;
  grid-template-columns: 1fr 360px;
  gap: 48px;
  align-items: start;
}

.sp-editorial-main {
  background: #ffffff;
  border-radius: 18px;
  border: 1px solid #e5e7eb;
  padding: 40px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
}

.sp-content-block {
  margin-bottom: 34px;
}

.sp-content-block h3 {
  font-size: 24px;
  color: var(--teal-dark);
  margin-bottom: 14px;
  font-family: 'Fraunces', serif;
}

.sp-content-block p {
  font-size: 15.5px;
  line-height: 1.7;
  color: #374151;
  margin-bottom: 14px;
}

.sp-rich-list {
  list-style: none;
  padding: 0;
  margin: 18px 0;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.sp-rich-list li {
  font-size: 15px;
  line-height: 1.6;
  background: #f8fafc;
  padding: 12px 16px;
  border-radius: 10px;
  border-left: 3px solid var(--gold);
}

.sp-airlines-box {
  background: linear-gradient(135deg, #0A192F 0%, #001E43 100%);
  color: #ffffff;
  border-radius: 16px;
  padding: 30px;
  margin: 40px 0;
}

.sp-airlines-box h4 {
  font-size: 20px;
  color: var(--gold);
  margin-bottom: 10px;
}

.sp-airlines-box p {
  color: rgba(255, 255, 255, 0.85);
  font-size: 14px;
  margin-bottom: 20px;
}

.sp-airline-logos-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
  gap: 10px;
}

.sp-airline-pill {
  background: rgba(255, 255, 255, 0.1);
  padding: 8px 12px;
  border-radius: 8px;
  font-size: 12.5px;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 8px;
  border: 1px solid rgba(255, 255, 255, 0.15);
}

.sp-airline-pill i {
  color: var(--gold);
}

/* FAQ SECTION */
.sp-faq-section {
  margin-top: 48px;
  border-top: 1px solid #e5e7eb;
  padding-top: 36px;
}

.sp-faq-header {
  margin-bottom: 24px;
}

.sp-faq-header h3 {
  font-size: 24px;
  color: var(--teal-dark);
}

.sp-faq-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.sp-faq-item {
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  overflow: hidden;
  background: #ffffff;
  transition: all 0.2s;
}

.sp-faq-item.open {
  border-color: var(--gold);
  box-shadow: 0 4px 14px rgba(197, 160, 89, 0.08);
}

.sp-faq-question {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 16px 20px;
  background: none;
  border: none;
  text-align: left;
  font-size: 15.5px;
  font-weight: 700;
  color: var(--ink);
  cursor: pointer;
  gap: 16px;
}

.sp-faq-question i {
  color: var(--gold);
  font-size: 14px;
}

.sp-faq-answer {
  padding: 0 20px 18px;
  font-size: 14.5px;
  line-height: 1.65;
  color: #4b5563;
  border-top: 1px dashed #f1f5f9;
  padding-top: 12px;
}

/* SIDEBAR CARDS */
.sp-sidebar-card {
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 16px;
  padding: 24px;
  margin-bottom: 24px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
}

.sp-office-badge {
  display: inline-block;
  font-size: 11px;
  font-weight: 800;
  text-transform: uppercase;
  padding: 4px 10px;
  border-radius: 999px;
  background: rgba(197, 160, 89, 0.15);
  color: var(--gold-dark);
  margin-bottom: 12px;
}

.sp-office-badge.pk {
  background: rgba(14, 75, 68, 0.12);
  color: #0E4B44;
}

.sp-office-card h4,
.sp-pk-card h4,
.sp-nav-card h4 {
  font-size: 17px;
  color: var(--teal-dark);
  margin-bottom: 10px;
}

.sp-office-address {
  font-size: 13.5px;
  color: var(--ink-soft);
  margin-bottom: 16px;
  line-height: 1.5;
  display: flex;
  align-items: flex-start;
  gap: 8px;
}

.sp-office-address i {
  color: var(--gold);
  margin-top: 3px;
}

.sp-office-phones {
  display: flex;
  flex-direction: column;
  gap: 8px;
  margin-bottom: 16px;
}

.sp-phone-link {
  font-size: 14px;
  font-weight: 700;
  color: var(--teal-dark);
  display: flex;
  align-items: center;
  gap: 8px;
  transition: color 0.2s;
}

.sp-phone-link:hover {
  color: var(--gold);
}

.sp-office-hours {
  font-size: 12.5px;
  color: #6b7280;
  display: flex;
  align-items: center;
  gap: 8px;
  border-top: 1px solid #f1f5f9;
  padding-top: 12px;
}

.sp-side-links {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.sp-side-links a {
  display: block;
  font-size: 14px;
  font-weight: 600;
  color: var(--ink-soft);
  padding: 8px 12px;
  border-radius: 8px;
  background: #f8fafc;
  transition: all 0.2s;
}

.sp-side-links a:hover,
.sp-side-links a.router-link-active {
  background: rgba(197, 160, 89, 0.15);
  color: var(--teal-dark);
  transform: translateX(4px);
}

/* CTA BANNER */
.sp-cta-banner {
  background: linear-gradient(135deg, #0A192F 0%, #001E43 100%);
  color: #ffffff;
  padding: 70px 0;
  text-align: center;
}

.sp-cta-content {
  max-width: 780px;
  margin: 0 auto;
}

.sp-cta-content h2 {
  font-size: clamp(26px, 3.5vw, 40px);
  color: #ffffff;
  margin: 12px 0 16px;
  font-family: 'Fraunces', serif;
}

.sp-cta-content p {
  color: rgba(255, 255, 255, 0.85);
  font-size: 16.5px;
  line-height: 1.6;
  margin-bottom: 28px;
}

.sp-cta-actions {
  display: flex;
  gap: 14px;
  justify-content: center;
  flex-wrap: wrap;
}

/* RESPONSIVE */
@media (max-width: 1024px) {
  .sp-hero-grid {
    grid-template-columns: 1fr;
  }
  .sp-trust-container {
    grid-template-columns: 1fr 1fr;
  }
  .sp-editorial-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 640px) {
  .sp-trust-container {
    grid-template-columns: 1fr;
  }
  .sp-hero-features {
    grid-template-columns: 1fr;
  }
  .sp-form-row {
    grid-template-columns: 1fr;
  }
}
</style>
