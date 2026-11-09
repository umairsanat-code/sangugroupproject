<template>
  <div class="site-wrapper">
    <!-- TOP UTILITY BAR -->
    <div class="site-topbar">
      <div class="container topbar-inner">
        <div class="topbar-left">
          <span class="topbar-item address-item">
            <i class="fa-solid fa-location-dot"></i>
            <strong>Barcelona:</strong> Fly Planet Badalona, Av. Alfonso XIII 496, 08913 Badalona
          </span>
          <span class="topbar-item hide-mobile">
            <i class="fa-regular fa-clock"></i> {{ t('Lun–Sáb: 09:30 – 20:30 CET') }}
          </span>
        </div>
        <div class="topbar-right">
          <a href="tel:+34672068148" class="topbar-item phone-link">
            <span class="flag-icon">🇪🇸</span> +34 672 06 81 48
          </a>
          <a href="tel:+923085092631" class="topbar-item phone-link hide-mobile">
            <span class="flag-icon">🇵🇰</span> +92 308 5092631
          </a>
          <a href="https://wa.me/34672068148?text=Hola%20SanguGroup%2C%20deseo%20informaci%C3%B3n%20sobre%20vuelos%20y%20Umrah" target="_blank" rel="noopener" class="topbar-wa-pill">
            <i class="fa-brands fa-whatsapp"></i> {{ t('WhatsApp Directo') }}
          </a>
          <div class="topbar-lang" aria-label="Select language">
            <button type="button" :class="{ active: locale === 'es' }" :aria-pressed="locale === 'es'" title="Español" @click="setLocale('es')">ES</button>
            <span>|</span>
            <button type="button" :class="{ active: locale === 'en' }" :aria-pressed="locale === 'en'" title="English" @click="setLocale('en')">EN</button>
          </div>
        </div>
      </div>
    </div>

    <!-- MAIN HEADER -->
    <header class="site-header" :class="{ 'scrolled': isScrolled }">
      <div class="header-inner">
        <router-link to="/" class="logo" style="display:flex;align-items:center;">
          <img src="/images/logo.png" alt="SanguGroup - Agencia de Viajes y Umrah" class="header-logo-img">
        </router-link>

        <!-- Desktop Navigation with Dropdowns -->
        <nav class="nav-main" :class="{ 'open': isMobileMenuOpen }">
          <router-link to="/" active-class="active" exact @click="closeMobileMenu">{{ t('Inicio') }}</router-link>
          

          <!-- Umrah Dropdown -->
          <div class="nav-dropdown-item" @mouseenter="openDropdown = 'umrah'" @mouseleave="openDropdown = null">
            <router-link to="/umrah" active-class="active" class="dropdown-trigger" @click="closeMobileMenu">
              {{ t('Umrah') }} <i class="fa-solid fa-chevron-down nav-arrow"></i>
            </router-link>
            <div class="nav-dropdown-menu" :class="{ 'show': openDropdown === 'umrah' }">
              <router-link to="/umrah" class="dropdown-link" @click="closeMobileMenu">
                <i class="fa-solid fa-kaaba"></i>
                <div>
                  <strong>{{ t('Todos los Paquetes Umrah') }}</strong>
                  <span>{{ t('Catálogo general y salidas') }}</span>
                </div>
              </router-link>
              <router-link to="/umrah-desde-espana" class="dropdown-link" @click="closeMobileMenu">
                <i class="fa-solid fa-flag"></i>
                <div>
                  <strong>{{ t('Umrah desde España') }}</strong>
                  <span>{{ t('Salidas desde Madrid y Barcelona') }}</span>
                </div>
              </router-link>
              <router-link to="/umrah-desde-barcelona" class="dropdown-link" @click="closeMobileMenu">
                <i class="fa-solid fa-plane-departure"></i>
                <div>
                  <strong>Umrah desde Barcelona (El Prat)</strong>
                  <span>Asistencia presencial en Badalona</span>
                </div>
              </router-link>
              <router-link to="/paquetes-umrah" class="dropdown-link" @click="closeMobileMenu">
                <i class="fa-solid fa-box-open"></i>
                <div>
                  <strong>Paquetes Umrah 2026 - 2027</strong>
                  <span>Económicos, Familiares y 5★</span>
                </div>
              </router-link>
              <router-link :to="{ path: '/umrah', query: { group: 1 } }" class="dropdown-link" @click="closeMobileMenu">
                <i class="fa-solid fa-users"></i>
                <div>
                  <strong>Salidas Grupales con Guía (Muallim)</strong>
                  <span>Grupos acompañados cada mes</span>
                </div>
              </router-link>
              <router-link to="/hoteles-la-meca" class="dropdown-link" @click="closeMobileMenu">
                <i class="fa-solid fa-hotel"></i>
                <div>
                  <strong>Hoteles en La Meca y Medina</strong>
                  <span>Clock Tower y hoteles junto al Haram</span>
                </div>
              </router-link>
              <router-link to="/umrah-2027" class="dropdown-link" @click="closeMobileMenu">
                <i class="fa-solid fa-calendar-check"></i>
                <div>
                  <strong>Umrah Temporada 2027</strong>
                  <span>Reserva anticipada con descuento</span>
                </div>
              </router-link>
            </div>
          </div>

          <!-- Visados Dropdown -->
          <div class="nav-dropdown-item" @mouseenter="openDropdown = 'visa'" @mouseleave="openDropdown = null">
            <router-link to="/visa" active-class="active" class="dropdown-trigger" @click="closeMobileMenu">
              {{ t('Visados') }} <i class="fa-solid fa-chevron-down nav-arrow"></i>
            </router-link>
            <div class="nav-dropdown-menu" :class="{ 'show': openDropdown === 'visa' }">
              <router-link to="/visa" class="dropdown-link" @click="closeMobileMenu">
                <i class="fa-solid fa-passport"></i>
                <div>
                  <strong>Todos los Visados</strong>
                  <span>Requisitos y solicitudes</span>
                </div>
              </router-link>
              <router-link to="/visados" class="dropdown-link" @click="closeMobileMenu">
                <i class="fa-solid fa-stamp"></i>
                <div>
                  <strong>Gestión de Visados en España</strong>
                  <span>Servicio express en Badalona</span>
                </div>
              </router-link>
              <router-link to="/visa/saudi-visit-visa" class="dropdown-link" @click="closeMobileMenu">
                <i class="fa-solid fa-check-double"></i>
                <div>
                  <strong>Visado Arabia Saudí (Turismo / Umrah)</strong>
                  <span>e-Visa múltiple válida por 1 año</span>
                </div>
              </router-link>
              <router-link to="/visa/uae-visit-visa" class="dropdown-link" @click="closeMobileMenu">
                <i class="fa-solid fa-building"></i>
                <div>
                  <strong>Visado Dubái / EAU</strong>
                  <span>Estancias de 30 o 60 días</span>
                </div>
              </router-link>
              <router-link to="/visa/uk-visit-visa" class="dropdown-link" @click="closeMobileMenu">
                <i class="fa-solid fa-crown"></i>
                <div>
                  <strong>Visado Reino Unido (UK)</strong>
                  <span>Asesoría documental completa</span>
                </div>
              </router-link>
            </div>
          </div>

          <router-link to="/hotels" active-class="active" @click="closeMobileMenu">{{ t('Hoteles') }}</router-link>
          
          <router-link to="/agencia-viajes-barcelona" active-class="active" @click="closeMobileMenu">
            <span class="nav-pill-badge">BCN</span> {{ t('Agencia Barcelona') }}
          </router-link>
          
          <router-link to="/gallery" active-class="active" @click="closeMobileMenu">{{ t('Galería') }}</router-link>
          <router-link to="/esim" active-class="active" @click="closeMobileMenu">eSIM</router-link>
          <router-link to="/blog" active-class="active" @click="closeMobileMenu">{{ t('Blog') }}</router-link>
          <router-link to="/contact" active-class="active" @click="closeMobileMenu">{{ t('Contacto') }}</router-link>
        </nav>

        <!-- CTA & Mobile Trigger -->
        <div class="header-cta">
          <a href="https://wa.me/34672068148?text=Hola%2C%20quiero%20pedir%20presupuesto%20de%20viaje" target="_blank" rel="noopener" class="btn btn-gold btn-header-cta hide-mobile">
            <i class="fa-brands fa-whatsapp"></i> {{ t('Presupuesto Rápido') }}
          </a>
          <a href="tel:+34672068148" class="icon-btn" title="Llamar a la oficina de Barcelona" aria-label="Llamar">
            <i class="fa-solid fa-phone"></i>
          </a>
          <button class="nav-toggle" aria-label="Abrir menú" @click="isMobileMenuOpen = !isMobileMenuOpen">
            <span></span><span></span><span></span>
          </button>
        </div>
      </div>
    </header>

    <!-- CONTENT -->
    <main>
      <router-view v-slot="{ Component, route }">
        <component :is="Component" :key="route.path" />
      </router-view>
    </main>

    <!-- PROFESSIONAL FOOTER -->
    <footer class="site-footer">
      <div class="container">
        <!-- Main 4-Column Grid -->
        <div class="footer-grid">
          <!-- Col 1: Identity & Trust -->
          <div class="footer-col-brand">
            <router-link to="/" class="logo footer-logo-wrap">
              <img src="/images/logo.png" alt="SanguGroup" class="footer-logo-img">
            </router-link>
            <p class="footer-about-text">
              Agencia de viajes internacional líder en España y Pakistán. Especialistas oficiales en billetes de avión a todo el mundo, paquetes completos de Umrah garantizados y tramitación ágil de visados. Con oficinas físicas en Badalona (Barcelona) y Pakistán.
            </p>
            <div class="footer-accreditation">
              <div class="acc-badge"><i class="fa-solid fa-certificate"></i> Agencia Oficial</div>
              <div class="acc-badge"><i class="fa-solid fa-shield-halved"></i> Pago Seguro</div>
              <div class="acc-badge"><i class="fa-solid fa-award"></i> 15+ Años Exp.</div>
            </div>
            <div class="social-row">
              <a v-for="p in socialPlatforms" :key="p.key" :href="p.url" target="_blank" rel="noopener" :aria-label="p.label" v-html="p.icon"></a>
            </div>
          </div>

          <!-- Col 2: Servicios Principales -->
          <div>
            <h4>{{ t('Servicios Principales') }}</h4>
            <ul>
              <li><router-link to="/vuelos-internacionales">Vuelos España a Pakistán</router-link></li>
              <li><router-link to="/umrah">{{ t('Paquetes Umrah') }}</router-link></li>
              <li><router-link :to="{ path: '/umrah', query: { group: 1 } }">Salidas Grupales con Muallim</router-link></li>
              <li><router-link to="/visa">Visados de Visita y Turismo</router-link></li>
              <li><router-link to="/hotels">{{ t('Reserva de Hoteles') }}</router-link></li>
              <li><router-link to="/esim">eSIM de Viaje Internacional</router-link></li>
              <li><router-link to="/gallery">{{ t('Galería de Peregrinos') }}</router-link></li>
            </ul>
          </div>

          <!-- Col 3: España & Umrah Especializado -->
          <div>
            <h4>{{ t('España & Umrah') }}</h4>
            <ul>
              <li><router-link to="/umrah-desde-espana">{{ t('Umrah desde España') }}</router-link></li>
              <li><router-link to="/umrah-desde-barcelona">Umrah desde Barcelona (BCN)</router-link></li>
              <li><router-link to="/paquetes-umrah">Catálogo Paquetes Umrah</router-link></li>
              <li><router-link to="/agencia-viajes-barcelona">{{ t('Agencia de Viajes Badalona') }}</router-link></li>
              <li><router-link to="/hoteles-la-meca">Hoteles en La Meca y Medina</router-link></li>
              <li><router-link to="/visados">Tramitación de Visados</router-link></li>
              <li><router-link to="/umrah-2027">Reserva Anticipada Umrah 2027</router-link></li>
              <li><router-link to="/blog">Blog &amp; Consejos de Viaje</router-link></li>
            </ul>
          </div>

          <!-- Col 4: Oficinas y Contacto -->
          <div>
            <h4>{{ t('Oficinas & Contacto') }}</h4>
            <div class="footer-office-card">
              <div class="office-title"><span class="flag-icon">🇪🇸</span> {{ t('Oficina Barcelona (Badalona)') }}</div>
              <p class="office-p">Av. Alfonso XIII 496, 08913 Badalona, Barcelona, España</p>
              <p class="office-phones">
                <a href="tel:+34672068148">+34 672 06 81 48</a> · 
                <a href="tel:+34631028871">+34 631 02 88 71</a>
              </p>
            </div>

            <div class="footer-office-card" style="margin-top: 14px;">
              <div class="office-title"><span class="flag-icon">🇵🇰</span> {{ t('Oficina Pakistán (24/7)') }}</div>
              <p class="office-phones">
                <a href="tel:+923085092631">+92 308 5092631</a>
              </p>
            </div>

            <p style="margin-top: 14px; font-size: 13px;">
              <i class="fa-solid fa-envelope" style="color: var(--gold); margin-right: 6px;"></i> sangutravel@gmail.com<br>
              <i class="fa-solid fa-clock" style="color: var(--gold); margin-right: 6px;"></i> {{ t('Lunes a Sábado, 9:30h – 20:30h CET') }}
            </p>
          </div>
        </div>

        <!-- Footer Bottom Bar -->
        <div class="footer-bottom">
          <div class="footer-bottom-copy">
            <span>© 2026 SanguGroup (Fly Planet Badalona). {{ t('Todos los derechos reservados.') }}</span>
            <span class="footer-sep">·</span>
            <router-link to="/terms">{{ t('Términos y Condiciones') }}</router-link>
            <span class="footer-sep">·</span>
            <router-link to="/about">{{ t('Sobre Nosotros') }}</router-link>
            <span class="footer-sep">·</span>
            <router-link to="/contact">Contacto</router-link>
          </div>
          <div class="footer-payment-badges">
            <span class="pay-badge"><i class="fa-brands fa-cc-visa"></i> Visa</span>
            <span class="pay-badge"><i class="fa-brands fa-cc-mastercard"></i> Mastercard</span>
            <span class="pay-badge"><i class="fa-solid fa-building-columns"></i> Revolut / Transferencia</span>
          </div>
        </div>
      </div>
    </footer>

    <!-- FLOATING WHATSAPP BUTTON (LEFT SIDE) -->
    <div class="floating-whatsapp-wrap">
      <transition name="wa-pop">
        <div v-if="isWaSelectorOpen" class="wa-selector-card" @click.stop>
          <div class="wa-card-header">
            <div class="wa-header-avatar">
              <i class="fa-brands fa-whatsapp"></i>
            </div>
            <div>
              <strong>Atención Directa WhatsApp</strong>
              <span><i class="status-dot"></i> Agentes disponibles en línea</span>
            </div>
            <button class="wa-close-btn" @click="isWaSelectorOpen = false" aria-label="Cerrar">×</button>
          </div>

          <div class="wa-card-body">
            <p class="wa-lead-text">Seleccione la oficina con la que desea comunicarse:</p>

            <a :href="buildWhatsAppLink('34672068148', selectedWaOption)" target="_blank" rel="noopener" class="wa-channel-item" @click="isWaSelectorOpen = false">
              <div class="wa-channel-icon">🇪🇸</div>
              <div class="wa-channel-info">
                <strong>Oficina España (Badalona / Barcelona)</strong>
                <span>+34 672 06 81 48 · Atención en Español, Urdu e Inglés</span>
              </div>
              <i class="fa-solid fa-arrow-right"></i>
            </a>

            <a :href="buildWhatsAppLink('923085092631', selectedWaOption)" target="_blank" rel="noopener" class="wa-channel-item" @click="isWaSelectorOpen = false">
              <div class="wa-channel-icon">🇵🇰</div>
              <div class="wa-channel-info">
                <strong>Oficina Pakistán</strong>
                <span>+92 308 5092631 · Soporte para Billetes y Peregrinos</span>
              </div>
              <i class="fa-solid fa-arrow-right"></i>
            </a>

            <div class="wa-quick-options">
              <span class="wa-opt-label">Mensaje rápido:</span>
              <div class="wa-opt-chips">
                <button type="button" :class="{ active: selectedWaOption === 'umrah' }" @click="selectedWaOption = 'umrah'">🕋 Paquetes Umrah</button>
                <button type="button" :class="{ active: selectedWaOption === 'vuelos' }" @click="selectedWaOption = 'vuelos'">✈️ Vuelos Baratos</button>
                <button type="button" :class="{ active: selectedWaOption === 'visado' }" @click="selectedWaOption = 'visado'">🛂 Tramitar Visado</button>
                <button type="button" :class="{ active: selectedWaOption === 'hotel' }" @click="selectedWaOption = 'hotel'">🏨 Hoteles La Meca</button>
              </div>
            </div>
          </div>
        </div>
      </transition>

      <button
        type="button"
        class="floating-whatsapp-btn"
        @click="isWaSelectorOpen = !isWaSelectorOpen"
        aria-label="Abrir chat de WhatsApp"
      >
        <i class="fa-brands fa-whatsapp"></i>
        <span class="wa-pulse-dot"></span>
        <span class="wa-floating-label hide-mobile">WhatsApp</span>
      </button>
    </div>

    <!-- Floating AI chatbot (right side) -->
    <ChatWidget />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import ChatWidget from './ChatWidget.vue';
import { locale, setLocale, t } from './locale.js';

const isMobileMenuOpen = ref(false);
const isScrolled = ref(false);
const openDropdown = ref(null);
const isWaSelectorOpen = ref(false);
const selectedWaOption = ref('umrah');
const socialLinks = ref({});

const handleScroll = () => {
  isScrolled.value = window.scrollY > 40;
};

const closeMobileMenu = () => {
  isMobileMenuOpen.value = false;
  openDropdown.value = null;
};

const buildWhatsAppLink = (phone, topic) => {
  const topics = {
    umrah: 'Hola SanguGroup, deseo solicitar presupuesto e información sobre los paquetes de Umrah desde España.',
    vuelos: 'Hola SanguGroup, deseo cotizar un billete de avión internacional (fechas y destino).',
    visado: 'Hola SanguGroup, necesito información para tramitar un visado.',
    hotel: 'Hola SanguGroup, deseo reservar hotel en La Meca / Medina.',
  };
  const text = encodeURIComponent(topics[topic] || 'Hola SanguGroup, deseo información sobre sus servicios de viaje.');
  return `https://wa.me/${phone}?text=${text}`;
};

const icons = {
  facebook: '<svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M13.5 21v-7h2.4l.4-3h-2.8V9.1c0-.9.3-1.6 1.6-1.6h1.3V4.8c-.6-.1-1.4-.1-2.2-.1-2.2 0-3.7 1.3-3.7 3.8V11H8v3h2.5v7h3z"/></svg>',
  instagram: '<svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M12 8.4a3.6 3.6 0 1 0 0 7.2 3.6 3.6 0 0 0 0-7.2zm0 5.9a2.3 2.3 0 1 1 0-4.6 2.3 2.3 0 0 1 0 4.6zM16.9 8.1a.9.9 0 1 1-1.8 0 .9.9 0 0 1 1.8 0zM20 8.3c0-.9-.2-1.6-.7-2.2a3 3 0 0 0-2.2-.7c-.8 0-1.1-.1-3.1-.1s-2.3 0-3.1.1a3 3 0 0 0-2.2.7 3 3 0 0 0-.7 2.2c0 .8-.1 1.1-.1 3.1s0 2.3.1 3.1a3 3 0 0 0 .7 2.2c.6.5 1.3.7 2.2.7.8 0 1.1.1 3.1.1s2.3 0 3.1-.1a3 3 0 0 0 2.2-.7c.5-.6.7-1.3.7-2.2 0-.8.1-1.1.1-3.1s0-2.3-.1-3.1zM18.6 14.5a5 5 0 0 1-.3 1.7 3 3 0 0 1-1.7 1.7 5 5 0 0 1-1.7.3c-.9 0-1.2.1-2.9.1s-2 0-2.9-.1a5 5 0 0 1-1.7-.3 3 3 0 0 1-1.7-1.7 5 5 0 0 1-.3-1.7c0-.9-.1-1.2-.1-2.9s0-2 .1-2.9a5 5 0 0 1 .3-1.7 3 3 0 0 1 1.7-1.7 5 5 0 0 1 1.7-.3c.9 0 1.2-.1 2.9-.1s2 0 2.9.1a5 5 0 0 1 1.7.3 3 3 0 0 1 1.7 1.7 5 5 0 0 1 .3 1.7c0 .9.1 1.2.1 2.9s0 2-.1 2.9z"/></svg>',
  whatsapp: '<svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M12 3a9 9 0 0 0-7.8 13.5L3 21l4.6-1.2A9 9 0 1 0 12 3zm5.4 12.6c-.2.7-1.3 1.3-1.9 1.4-.5.1-1.1.1-1.8-.1-.4-.1-.9-.3-1.5-.5-2.6-1.1-4.3-3.8-4.4-4-.1-.1-1.1-1.4-1.1-2.7s.7-1.9.9-2.2c.2-.3.5-.3.7-.3h.5c.2 0 .4 0 .6.4s.8 1.9.8 2c.1.1.1.3 0 .5s-.1.3-.3.5l-.4.5c-.1.1-.3.3-.1.6.2.3.8 1.3 1.7 2.1 1.2 1.1 2.2 1.4 2.5 1.5.3.1.5.1.6-.1l1-1.2c.2-.3.4-.2.7-.1l2 .9c.3.2.5.3.6.4.1.2.1.7-.1 1.3z"/></svg>',
  youtube: '<svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M21.6 7.2a2.5 2.5 0 0 0-1.8-1.8C18.2 5 12 5 12 5s-6.2 0-7.8.4A2.5 2.5 0 0 0 2.4 7.2 26.5 26.5 0 0 0 2 12a26.5 26.5 0 0 0 .4 4.8 2.5 2.5 0 0 0 1.8 1.8c1.6.4 7.8.4 7.8.4s6.2 0 7.8-.4a2.5 2.5 0 0 0 1.8-1.8A26.5 26.5 0 0 0 22 12a26.5 26.5 0 0 0-.4-4.8zM10 15.2V8.8l5.2 3.2L10 15.2z"/></svg>',
  tiktok: '<svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M16.5 3h-2.6v12.6a3 3 0 1 1-2.6-3v-2.7a5.7 5.7 0 1 0 5.2 5.6V3z"/></svg>',
  telegram: '<svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M21.5 4.5 18.9 19c-.2 1-.8 1.2-1.6.7l-4.5-3.3-2.2 2.1c-.2.2-.4.4-.9.4l.3-4.7L18.2 7c.4-.3-.1-.5-.6-.2L7.4 12.9l-4.4-1.4c-1-.3-1-.9.2-1.4L20.4 3.3c.8-.3 1.5.2 1.1 1.2z"/></svg>',
  twitter: '<svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M17.5 3h3.1l-6.8 7.8L21.8 21h-6.3l-4.9-6.4L5 21H1.9l7.3-8.3L1.6 3h6.4l4.4 5.9L17.5 3zm-1.1 16.2h1.7L7.5 4.7H5.7l10.7 14.5z"/></svg>'
};

const socialPlatforms = computed(() => {
  const order = ['facebook', 'instagram', 'whatsapp', 'youtube', 'tiktok', 'telegram', 'twitter'];
  const labels = { facebook: 'Facebook', instagram: 'Instagram', whatsapp: 'WhatsApp', youtube: 'YouTube', tiktok: 'TikTok', telegram: 'Telegram', twitter: 'X (Twitter)' };
  return order
    .filter(key => socialLinks.value[key])
    .map(key => ({ key, label: labels[key], url: socialLinks.value[key], icon: icons[key] }));
});

onMounted(async () => {
  document.documentElement.lang = locale.value;
  window.addEventListener('scroll', handleScroll, { passive: true });
  try {
    const res = await window.axios.get('/api/social-links');
    socialLinks.value = res.data || {};
  } catch (e) {
    console.error(e);
  }
});

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll);
});
</script>

<style scoped>
.site-wrapper {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

/* TOP UTILITY BAR */
.site-topbar {
  background-color: #061120;
  color: #94a3b8;
  font-size: 12.5px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.08);
  padding: 8px 0;
  position: relative;
  z-index: 210;
}

.topbar-inner {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
}

.topbar-left, .topbar-right {
  display: flex;
  align-items: center;
  gap: 18px;
}

.topbar-item {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  color: #cbd5e1;
}

.topbar-item i {
  color: var(--gold);
}

.phone-link {
  font-weight: 700;
  color: #f1f5f9;
  transition: color 0.2s;
}

.phone-link:hover {
  color: var(--gold);
}

.topbar-wa-pill {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: #25d366;
  color: #ffffff;
  font-weight: 700;
  font-size: 11.5px;
  padding: 4px 10px;
  border-radius: 999px;
  transition: transform 0.2s, background-color 0.2s;
}

.topbar-wa-pill:hover {
  background: #1eb957;
  transform: scale(1.03);
}

.topbar-lang {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 11.5px;
  font-weight: 700;
  color: #64748b;
}

.topbar-lang .active {
  color: var(--gold);
}

.topbar-lang button {
  padding: 0;
  border: 0;
  background: transparent;
  color: inherit;
  font: inherit;
  cursor: pointer;
}

.topbar-lang button:hover,
.topbar-lang button:focus-visible {
  color: #fff;
}

.flag-icon {
  font-size: 14px;
}

/* HEADER */
.site-header {
  position: sticky;
  top: 0;
  z-index: 200;
  background: rgba(10, 25, 47, 0.96);
  backdrop-filter: blur(12px);
  border-bottom: 1px solid rgba(255, 255, 255, 0.08);
  transition: background-color 0.3s ease, box-shadow 0.3s ease;
}

.site-header.scrolled {
  background: rgba(6, 17, 32, 0.98);
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.35);
}

.header-inner {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 12px 28px;
  max-width: var(--maxw);
  margin: 0 auto;
}

.header-logo-img {
  height: 48px;
  width: auto;
  max-width: 190px;
  object-fit: contain;
  background: #ffffff;
  padding: 6px 12px;
  border-radius: 10px;
}

.nav-main {
  display: flex;
  align-items: center;
  gap: 22px;
}

.nav-main a {
  font-size: 14px;
  font-weight: 600;
  color: #e2e8f0;
  position: relative;
  padding: 8px 0;
  transition: color 0.2s;
  display: inline-flex;
  align-items: center;
  gap: 5px;
}

.nav-main a:hover,
.nav-main a.active {
  color: var(--gold);
}

.nav-main a.active::after {
  content: "";
  position: absolute;
  left: 0;
  right: 0;
  bottom: 0px;
  height: 2px;
  background: var(--gold);
  border-radius: 2px;
}

.nav-pill-badge {
  background: #ef4444;
  color: #ffffff;
  font-size: 9.5px;
  font-weight: 800;
  padding: 2px 6px;
  border-radius: 999px;
  letter-spacing: 0.05em;
  text-transform: uppercase;
}

/* DROPDOWNS */
.nav-dropdown-item {
  position: relative;
}

.dropdown-trigger {
  cursor: pointer;
}

.nav-arrow {
  font-size: 10px;
  margin-left: 2px;
  transition: transform 0.2s;
}

.nav-dropdown-item:hover .nav-arrow {
  transform: rotate(180deg);
}

.nav-dropdown-menu {
  position: absolute;
  top: 100%;
  left: -20px;
  width: 320px;
  background: #ffffff;
  border-radius: 16px;
  padding: 12px;
  box-shadow: 0 20px 45px rgba(0, 0, 0, 0.25);
  border: 1px solid #e2e8f0;
  display: none;
  flex-direction: column;
  gap: 4px;
  z-index: 300;
}

.nav-dropdown-menu.show,
.nav-dropdown-item:hover .nav-dropdown-menu {
  display: flex;
}

.dropdown-link {
  display: flex !important;
  align-items: flex-start !important;
  gap: 12px !important;
  padding: 10px 12px !important;
  border-radius: 10px;
  color: var(--ink) !important;
  transition: background-color 0.2s !important;
}

.dropdown-link:hover {
  background-color: #f1f5f9;
  color: var(--teal-dark) !important;
}

.dropdown-link.active::after {
  display: none !important;
}

.dropdown-link i {
  color: var(--gold);
  font-size: 18px;
  margin-top: 3px;
}

.dropdown-link strong {
  display: block;
  font-size: 13.5px;
  color: var(--teal-dark);
}

.dropdown-link span {
  display: block;
  font-size: 11.5px;
  color: var(--ink-soft);
}

/* HEADER CTA */
.header-cta {
  display: flex;
  align-items: center;
  gap: 12px;
}

.btn-header-cta {
  padding: 10px 18px;
  font-size: 13px;
  font-weight: 700;
}

.icon-btn {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--gold);
  transition: all 0.2s;
}

.icon-btn:hover {
  background: var(--gold);
  color: #061120;
}

.nav-toggle {
  display: none;
  background: none;
  border: none;
  cursor: pointer;
  padding: 8px;
}

.nav-toggle span {
  display: block;
  width: 24px;
  height: 2.5px;
  background: #ffffff;
  margin: 5px 0;
  border-radius: 2px;
}

/* FOOTER */
.site-footer {
  background: #061120;
  color: #94a3b8;
  padding: 70px 0 25px;
  border-top: 1px solid rgba(255, 255, 255, 0.08);
}

.footer-grid {
  display: grid;
  grid-template-columns: 1.4fr 1fr 1fr 1.2fr;
  gap: 40px;
  margin-bottom: 50px;
}

.footer-logo-wrap {
  display: inline-block;
  margin-bottom: 16px;
  background: #ffffff;
  padding: 8px 14px;
  border-radius: 10px;
}

.footer-logo-img {
  height: 44px;
  width: auto;
  max-width: 180px;
  object-fit: contain;
}

.footer-about-text {
  font-size: 13.5px;
  line-height: 1.65;
  color: #cbd5e1;
  margin-bottom: 20px;
}

.footer-accreditation {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
  margin-bottom: 20px;
}

.acc-badge {
  font-size: 11px;
  font-weight: 700;
  background: rgba(255, 255, 255, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.12);
  padding: 4px 8px;
  border-radius: 6px;
  color: var(--gold);
  display: flex;
  align-items: center;
  gap: 5px;
}

.footer-grid h4 {
  color: #ffffff;
  font-size: 15px;
  font-family: 'Plus Jakarta Sans', sans-serif;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  margin-bottom: 18px;
  font-weight: 700;
}

.footer-grid ul {
  list-style: none;
  margin: 0;
  padding: 0;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.footer-grid ul a {
  font-size: 13.5px;
  color: #94a3b8;
  transition: color 0.2s;
}

.footer-grid ul a:hover {
  color: var(--gold);
}

.footer-office-card {
  background: rgba(255, 255, 255, 0.04);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 12px;
  padding: 14px;
}

.office-title {
  color: #ffffff;
  font-weight: 700;
  font-size: 13px;
  margin-bottom: 6px;
  display: flex;
  align-items: center;
  gap: 6px;
}

.office-p {
  font-size: 12.5px;
  color: #cbd5e1;
  margin: 0 0 6px;
  line-height: 1.4;
}

.office-phones a {
  font-size: 13px;
  font-weight: 700;
  color: var(--gold);
}

.footer-bottom {
  border-top: 1px solid rgba(255, 255, 255, 0.1);
  padding-top: 24px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 16px;
  font-size: 12.5px;
}

.footer-bottom-copy {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
}

.footer-bottom-copy a:hover {
  color: var(--gold);
}

.footer-sep {
  opacity: 0.4;
}

.footer-payment-badges {
  display: flex;
  gap: 12px;
  font-size: 12px;
  font-weight: 600;
  color: #cbd5e1;
}

.pay-badge {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  background: rgba(255, 255, 255, 0.06);
  padding: 4px 8px;
  border-radius: 6px;
}

/* FLOATING WHATSAPP BUTTON (LEFT) */
.floating-whatsapp-wrap {
  position: fixed;
  left: 24px;
  bottom: 24px;
  z-index: 9999;
}

.floating-whatsapp-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  background: #25d366;
  color: #ffffff;
  border: none;
  padding: 12px 18px;
  border-radius: 999px;
  font-family: inherit;
  font-size: 14px;
  font-weight: 700;
  cursor: pointer;
  box-shadow: 0 10px 25px rgba(37, 211, 102, 0.4);
  transition: transform 0.25s ease, box-shadow 0.25s ease;
  position: relative;
}

.floating-whatsapp-btn:hover {
  transform: translateY(-3px) scale(1.03);
  box-shadow: 0 16px 35px rgba(37, 211, 102, 0.55);
}

.floating-whatsapp-btn i {
  font-size: 24px;
}

.wa-pulse-dot {
  position: absolute;
  top: 4px;
  right: 4px;
  width: 10px;
  height: 10px;
  border-radius: 50%;
  background-color: #ffffff;
  animation: pulse 1.5s infinite;
}

@keyframes pulse {
  0% { transform: scale(0.9); opacity: 0.9; }
  50% { transform: scale(1.4); opacity: 0.3; }
  100% { transform: scale(0.9); opacity: 0.9; }
}

.wa-selector-card {
  position: absolute;
  left: 0;
  bottom: 65px;
  width: 360px;
  max-width: calc(100vw - 40px);
  background: #ffffff;
  border-radius: 18px;
  overflow: hidden;
  box-shadow: 0 20px 50px rgba(0, 0, 0, 0.25);
  border: 1px solid #e2e8f0;
}

.wa-card-header {
  background: linear-gradient(135deg, #095039 0%, #128c7e 100%);
  color: #ffffff;
  padding: 16px;
  display: flex;
  align-items: center;
  gap: 12px;
  position: relative;
}

.wa-header-avatar {
  width: 38px;
  height: 38px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 22px;
}

.wa-card-header strong {
  display: block;
  font-size: 14.5px;
}

.wa-card-header span {
  font-size: 11.5px;
  opacity: 0.9;
  display: flex;
  align-items: center;
  gap: 5px;
}

.status-dot {
  width: 7px;
  height: 7px;
  background: #4ade80;
  border-radius: 50%;
  display: inline-block;
}

.wa-close-btn {
  position: absolute;
  right: 12px;
  top: 12px;
  background: none;
  border: none;
  color: #ffffff;
  font-size: 20px;
  cursor: pointer;
  opacity: 0.75;
}

.wa-close-btn:hover {
  opacity: 1;
}

.wa-card-body {
  padding: 16px;
}

.wa-lead-text {
  font-size: 12.5px;
  color: #64748b;
  margin-bottom: 12px;
}

.wa-channel-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px;
  border-radius: 12px;
  border: 1px solid #e2e8f0;
  background: #f8fafc;
  margin-bottom: 10px;
  transition: all 0.2s;
  color: var(--ink);
}

.wa-channel-item:hover {
  background: #ecfdf5;
  border-color: #a7f3d0;
  transform: translateX(3px);
}

.wa-channel-icon {
  font-size: 22px;
}

.wa-channel-info {
  flex: 1;
}

.wa-channel-info strong {
  display: block;
  font-size: 13.5px;
  color: var(--teal-dark);
}

.wa-channel-info span {
  font-size: 11px;
  color: #64748b;
  display: block;
}

.wa-channel-item i {
  color: #10b981;
  font-size: 14px;
}

.wa-quick-options {
  margin-top: 14px;
  border-top: 1px dashed #e2e8f0;
  padding-top: 10px;
}

.wa-opt-label {
  font-size: 11px;
  font-weight: 700;
  color: #64748b;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  display: block;
  margin-bottom: 6px;
}

.wa-opt-chips {
  display: flex;
  gap: 6px;
  flex-wrap: wrap;
}

.wa-opt-chips button {
  background: #f1f5f9;
  border: 1px solid #cbd5e1;
  padding: 4px 8px;
  border-radius: 6px;
  font-size: 11px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.15s;
}

.wa-opt-chips button.active,
.wa-opt-chips button:hover {
  background: #065f46;
  color: #ffffff;
  border-color: #065f46;
}

.wa-pop-enter-active, .wa-pop-leave-active {
  transition: opacity 0.2s ease, transform 0.2s ease;
}

.wa-pop-enter-from, .wa-pop-leave-to {
  opacity: 0;
  transform: translateY(12px) scale(0.96);
}

/* RESPONSIVE */
@media (max-width: 1024px) {
  .footer-grid {
    grid-template-columns: 1fr 1fr;
  }
}

@media (max-width: 900px) {
  .nav-main {
    position: fixed;
    top: 65px;
    left: 0;
    right: 0;
    bottom: 0;
    background: #0a192f;
    flex-direction: column;
    align-items: flex-start;
    padding: 24px;
    gap: 16px;
    transform: translateX(100%);
    transition: transform 0.3s ease;
    overflow-y: auto;
    z-index: 250;
  }

  .nav-main.open {
    transform: translateX(0);
  }

  .nav-dropdown-menu {
    position: static;
    display: flex;
    width: 100%;
    box-shadow: none;
    border: none;
    background: rgba(255, 255, 255, 0.05);
    padding: 8px;
    margin-top: 6px;
  }

  .dropdown-link {
    color: #e2e8f0 !important;
  }

  .dropdown-link strong {
    color: #ffffff !important;
  }

  .dropdown-link span {
    color: #94a3b8 !important;
  }

  .nav-toggle {
    display: block;
  }
}

@media (max-width: 640px) {
  .hide-mobile {
    display: none !important;
  }
  .footer-grid {
    grid-template-columns: 1fr;
  }
  .topbar-left {
    font-size: 11px;
  }
}
</style>
