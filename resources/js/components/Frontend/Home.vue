<template>
  <div class="homepage-redesign">
    <!-- 2. PREMIUM TRAVEL HERO SECTION -->
    <section class="hero-animated">
      <!-- Background slides -->
      <div
        v-for="(img, i) in heroImages"
        :key="i"
        class="hero-bg"
        :class="{ visible: i === heroIndex }"
        :style="{ backgroundImage: `url(${img})` }"
      ></div>

      <!-- Video slides -->
      <div
        v-for="(v, vi) in heroVideos"
        :key="'vid-' + vi"
        class="hero-bg hero-video"
        :class="{ visible: heroIndex === heroImages.length + vi }"
        :style="heroVideoFailed[vi] ? { backgroundImage: `url(${lastHeroImage})` } : {}"
      >
        <video
          v-if="!heroVideoFailed[vi]"
          :key="'play-' + vi + '-' + heroIndex"
          autoplay muted loop playsinline
          :src="v"
          :poster="lastHeroImage"
          preload="metadata"
          @error="onHeroVideoError(vi)"
        ></video>
      </div>

      <div class="hero-inner" :class="{ 'has-promo': promotions.length }">
        <div class="hero-content-wrap">
          <div class="hero-trust-tag">
            <span class="pulse-dot"></span>
            <span>{{ t('Agencia Oficial en Barcelona (Badalona) y Pakistán') }}</span>
          </div>

          <h1 class="hero-main-title">
            {{ t('Su Agencia de Viajes Internacional y Especialista en Umrah') }}
          </h1>

          <p class="hero-subtitle">
            {{ t('Billetes de avión al mejor precio a Pakistán, Dubái y todo el mundo. Paquetes de Umrah garantizados con salidas desde Barcelona y Madrid, hoteles 5★ frente al Haram y tramitación de visados.') }}
          </p>

          <div class="hero-cta-buttons">
            <router-link to="/umrah-desde-espana" class="btn btn-gold btn-hero">
              <i class="fa-solid fa-kaaba"></i> {{ t('Paquetes Umrah España') }}
            </router-link>
            <a href="https://wa.me/34672068148?text=Hola%20SanguGroup%2C%20deseo%20presupuesto%20de%20vuelos%20o%20Umrah" target="_blank" rel="noopener" class="btn btn-outline-light btn-hero">
              <i class="fa-brands fa-whatsapp"></i> {{ t('Consultar por WhatsApp') }}
            </a>
          </div>

          <!-- Trust Badges Strip -->
          <div class="hero-badges-row">
            <div class="h-badge"><i class="fa-solid fa-shield-halved"></i> 15+ Años de Experiencia</div>
            <div class="h-badge"><i class="fa-solid fa-users"></i> +10.000 Peregrinos Satisfechos</div>
            <div class="h-badge"><i class="fa-solid fa-headset"></i> Atención en Español, Urdu e Inglés</div>
          </div>
        </div>

        <!-- Promotions Ad Card (Right Side) -->
        <div class="hero-promo" v-if="promotions.length">
          <div class="hero-promo-head">
            <span class="hero-promo-eyebrow">🌟 Ofertas &amp; Novedades</span>
          </div>
          <transition name="promo-fade" mode="out-in">
            <div class="hero-promo-card" :key="currentHeroPromo.id || heroPromoIndex">
              <img v-if="currentHeroPromo.image_url" :src="currentHeroPromo.image_url" :alt="currentHeroPromo.title">
              <div v-else class="hero-promo-placeholder">🎯</div>
              <div class="hero-promo-body">
                <h3>{{ currentHeroPromo.title }}</h3>
                <p v-if="currentHeroPromo.description">{{ currentHeroPromo.description }}</p>
                <span v-if="currentHeroPromo.button_text" class="hero-promo-btn" @click="handleHeroPromoClick(currentHeroPromo)">
                  {{ currentHeroPromo.button_text }} →
                </span>
              </div>
            </div>
          </transition>
          <div class="hero-promo-dots" v-if="promotions.length > 1">
            <button
              v-for="(p, i) in promotions"
              :key="'hpd-' + i"
              class="hero-promo-dot"
              :class="{ active: i === heroPromoIndex }"
              @click="heroPromoIndex = i"
              :aria-label="`Promotion ${i + 1}`"
            ></button>
          </div>
        </div>
      </div>

      <!-- Slide Nav Dots -->
      <div class="hero-dots">
        <button
          v-for="(img, i) in heroImages"
          :key="i"
          class="hero-dot"
          :class="{ active: i === heroIndex }"
          @click="goToHero(i)"
          :aria-label="`Slide ${i + 1}`"
        ></button>
        <button
          v-for="(v, vi) in heroVideos"
          :key="'hd-v-' + vi"
          class="hero-dot"
          :class="{ active: heroIndex === heroImages.length + vi }"
          @click="goToHero(heroImages.length + vi)"
          :aria-label="`Video ${vi + 1}`"
        ></button>
      </div>
    </section>

    <!-- 3. FLIGHT BOOKING / ENQUIRY SEARCH (MULTITAB) -->
    <section class="travel-search-section">
      <div class="container">
        <div class="travel-search-card">
          <!-- Search Tabs -->
          <div class="travel-search-tabs">
            <button type="button" :class="{ active: searchTab === 'flights' }" @click="searchTab = 'flights'">
              <i class="fa-solid fa-plane"></i> <span>Vuelos Internacionales</span>
            </button>
            <button type="button" :class="{ active: searchTab === 'umrah' }" @click="searchTab = 'umrah'">
              <i class="fa-solid fa-kaaba"></i> <span>Paquetes Umrah</span>
            </button>
            <button type="button" :class="{ active: searchTab === 'visas' }" @click="searchTab = 'visas'">
              <i class="fa-solid fa-passport"></i> <span>Visados</span>
            </button>
            <button type="button" :class="{ active: searchTab === 'hotels' }" @click="searchTab = 'hotels'">
              <i class="fa-solid fa-hotel"></i> <span>Hoteles Meca / Mundo</span>
            </button>
          </div>

          <!-- TAB 1: FLIGHTS -->
          <form v-if="searchTab === 'flights'" @submit.prevent="submitFlightInquiry" class="search-form-body">
            <div class="search-fields-grid">
              <div class="form-group">
                <label>Origen (Ciudad / Aeropuerto)</label>
                <input type="text" v-model="flightForm.origin" placeholder="Ej. Barcelona (BCN) o Madrid (MAD)" required>
              </div>
              <div class="form-group">
                <label>Destino (Ciudad / Aeropuerto)</label>
                <input type="text" v-model="flightForm.destination" placeholder="Ej. Lahore, Islamabad, Dubái, Jeddah..." required>
              </div>
              <div class="form-group">
                <label>Fecha de Salida</label>
                <input type="date" v-model="flightForm.departureDate" required>
              </div>
              <div class="form-group">
                <label>Fecha de Regreso (opcional)</label>
                <input type="date" v-model="flightForm.returnDate">
              </div>
              <div class="form-group">
                <label>Pasajeros / Clase</label>
                <select v-model="flightForm.passengers">
                  <option value="1 Adulto (Turista)">1 Adulto (Turista)</option>
                  <option value="2 Adultos (Turista)">2 Adultos (Turista)</option>
                  <option value="Familia (2 Adultos + Niños)">Familia (2 Adultos + Niños)</option>
                  <option value="Grupo 5+ personas">Grupo (+5 personas)</option>
                  <option value="Clase Business">Clase Business</option>
                </select>
              </div>
              <div class="form-group" style="align-self: end;">
                <button type="submit" class="btn btn-gold btn-block btn-search-submit">
                  <i class="fa-brands fa-whatsapp"></i> Pedir Cotización Vuelo
                </button>
              </div>
            </div>
            <div class="search-form-footer">
              <span class="search-badge-note"><i class="fa-solid fa-check"></i> Franquicia de 40 kg o 2x23 kg para Pakistán</span>
              <span class="search-badge-note"><i class="fa-solid fa-check"></i> Emisión con Qatar, Emirates, Saudia, PIA, Turkish</span>
              <button type="button" @click="openQuickInquiryModal('Vuelos')" class="btn-link-inquiry">¿Prefiere enviar formulario online? Haga clic aquí</button>
            </div>
          </form>

          <!-- TAB 2: UMRAH -->
          <form v-else-if="searchTab === 'umrah'" @submit.prevent="submitUmrahInquiry" class="search-form-body">
            <div class="search-fields-grid">
              <div class="form-group">
                <label>Ciudad de Salida</label>
                <select v-model="umrahForm.departureCity">
                  <option value="Barcelona (El Prat BCN)">Barcelona (El Prat BCN)</option>
                  <option value="Madrid (Barajas MAD)">Madrid (Barajas MAD)</option>
                  <option value="Valencia / Málaga / Otra">Otra ciudad de España</option>
                  <option value="Reino Unido / Londres">Reino Unido (Londres / Manchester)</option>
                  <option value="Pakistán">Salida desde Pakistán</option>
                </select>
              </div>
              <div class="form-group">
                <label>Tipo de Paquete</label>
                <select v-model="umrahForm.packageType">
                  <option value="Económico (10 Días)">Económico (10 Días) — Desde 1.290€</option>
                  <option value="Confort 4 Estrellas (12 Días)">Confort 4 Estrellas (12 Días)</option>
                  <option value="VIP 5★ Clock Tower (14 Días)">VIP 5★ Clock Tower (Abraj Al-Bait)</option>
                  <option value="Salida Grupal con Muallim">Salida Grupal Mensual con Guía</option>
                  <option value="Especial Ramadán">Especial Mes de Ramadán</option>
                  <option value="Reserva Umrah 2027">Reserva Anticipada Temporada 2027</option>
                </select>
              </div>
              <div class="form-group">
                <label>Mes Deseado de Viaje</label>
                <select v-model="umrahForm.travelMonth">
                  <option value="Próxima salida disponible">Próxima salida disponible</option>
                  <option value="Noviembre - Diciembre">Noviembre - Diciembre</option>
                  <option value="Enero - Febrero">Enero - Febrero</option>
                  <option value="Ramadán">Mes Sagrado de Ramadán</option>
                  <option value="Semana Santa / Pascua">Semana Santa / Pascua</option>
                  <option value="Temporada 2027">Temporada 2027</option>
                </select>
              </div>
              <div class="form-group">
                <label>Número de Personas</label>
                <select v-model="umrahForm.travelers">
                  <option value="1 persona">1 persona</option>
                  <option value="2 personas (Pareja)">2 personas (Hab. Doble)</option>
                  <option value="3 personas">3 personas (Hab. Triple)</option>
                  <option value="4 personas (Familia)">4 personas (Hab. Cuádruple)</option>
                  <option value="Grupo de 5 o más">Grupo de 5 o más peregrinos</option>
                </select>
              </div>
              <div class="form-group" style="align-self: end; grid-column: span 2;">
                <button type="submit" class="btn btn-gold btn-block btn-search-submit">
                  <i class="fa-brands fa-whatsapp"></i> Recibir Presupuesto Umrah al Instante
                </button>
              </div>
            </div>
            <div class="search-form-footer">
              <span class="search-badge-note"><i class="fa-solid fa-check"></i> Visado oficial saudí y seguro médico incluidos</span>
              <span class="search-badge-note"><i class="fa-solid fa-check"></i> Hoteles verificados a pocos metros del Haram</span>
              <router-link to="/umrah-desde-espana" class="btn-link-inquiry">Ver todos los detalles de Umrah desde España →</router-link>
            </div>
          </form>

          <!-- TAB 3: VISAS -->
          <form v-else-if="searchTab === 'visas'" @submit.prevent="submitVisaInquiry" class="search-form-body">
            <div class="search-fields-grid">
              <div class="form-group">
                <label>Destino del Visado</label>
                <select v-model="visaForm.country">
                  <option value="Arabia Saudí (Turismo / Umrah)">Arabia Saudí (Turismo / Umrah e-Visa)</option>
                  <option value="Dubái / Emiratos Árabes">Dubái / Emiratos Árabes (30 o 60 días)</option>
                  <option value="Reino Unido (UK)">Reino Unido (Standard Visitor Visa)</option>
                  <option value="España / Schengen">Visado Schengen para España / Europa</option>
                  <option value="Turquía">Turquía (e-Visa de Turismo)</option>
                </select>
              </div>
              <div class="form-group">
                <label>Nacionalidad / Pasaporte</label>
                <input type="text" v-model="visaForm.nationality" placeholder="Ej. Española, Pakistaní con NIE..." required>
              </div>
              <div class="form-group">
                <label>Número de Solicitantes</label>
                <select v-model="visaForm.applicants">
                  <option value="1 persona">1 solicitante</option>
                  <option value="2 personas">2 solicitantes</option>
                  <option value="Familia 3-4 personas">Familia (3-4 solicitantes)</option>
                  <option value="Grupo 5+">Grupo (+5 solicitantes)</option>
                </select>
              </div>
              <div class="form-group" style="align-self: end;">
                <button type="submit" class="btn btn-gold btn-block btn-search-submit">
                  <i class="fa-brands fa-whatsapp"></i> Consultar Requisitos de Visado
                </button>
              </div>
            </div>
            <div class="search-form-footer">
              <span class="search-badge-note"><i class="fa-solid fa-bolt"></i> e-Visa saudí y Dubái en 24-48 horas</span>
              <span class="search-badge-note"><i class="fa-solid fa-shield"></i> Revisión previa de documentos en Badalona</span>
              <router-link to="/visados" class="btn-link-inquiry">Conocer todos los visados que tramitamos →</router-link>
            </div>
          </form>

          <!-- TAB 4: HOTELS -->
          <form v-else @submit.prevent="submitHotelInquiry" class="search-form-body">
            <div class="search-fields-grid">
              <div class="form-group">
                <label>Ciudad de Alojamiento</label>
                <select v-model="hotelForm.city">
                  <option value="La Meca (Makkah)">La Meca (Makkah) — Hoteles junto al Haram</option>
                  <option value="Medina (Madinah)">Medina (Madinah) — Frente a Masjid Nabawi</option>
                  <option value="Dubái (EAU)">Dubái (Downtown / Deira / Marina)</option>
                  <option value="Estambul (Turquía)">Estambul (Sultanahmet / Taksim)</option>
                  <option value="Otra ciudad">Otra ciudad mundial</option>
                </select>
              </div>
              <div class="form-group">
                <label>Categoría Deseada</label>
                <select v-model="hotelForm.category">
                  <option value="5 Estrellas Lujo / Clock Tower">5 Estrellas Lujo / Clock Tower (Vistas Kaaba)</option>
                  <option value="4 Estrellas Confort">4 Estrellas Confort a pie del Haram</option>
                  <option value="Económico Limpio">Económico con autobús continuo</option>
                </select>
              </div>
              <div class="form-group">
                <label>Fechas Estimadas</label>
                <input type="text" v-model="hotelForm.dates" placeholder="Ej. 10 al 20 de Diciembre">
              </div>
              <div class="form-group" style="align-self: end;">
                <button type="submit" class="btn btn-gold btn-block btn-search-submit">
                  <i class="fa-brands fa-whatsapp"></i> Cotizar Hotel Directo
                </button>
              </div>
            </div>
            <div class="search-form-footer">
              <span class="search-badge-note"><i class="fa-solid fa-check"></i> Convenios con Swissôtel, Pullman, Fairmont y Mövenpick</span>
              <router-link to="/hoteles-la-meca" class="btn-link-inquiry">Guía de hoteles en La Meca y Medina →</router-link>
            </div>
          </form>
        </div>
      </div>
    </section>

    <!-- 4. MAIN TRAVEL SERVICES (6 LUXURY CARDS) -->
    <section class="services-overview-section">
      <div class="container">
        <div class="section-head text-center">
          <span class="eyebrow">{{ t('Servicios Integrales de Viaje') }}</span>
          <h2 class="section-title">Soluciones Completas para Viajeros y Peregrinos</h2>
          <p class="section-intro">Desde la emisión de su billete de avión hasta el acompañamiento en los Santos Lugares, le garantizamos profesionalidad y cercanía en cada etapa.</p>
        </div>

        <div class="grid grid-3 services-cards-grid">
          <!-- Service 1 -->
          <div class="service-card-premium">
            <div class="srv-icon-box"><i class="fa-solid fa-plane-departure"></i></div>
            <h3>Billetes de Avión Internacionales</h3>
            <p>Vuelos baratos a Pakistán (Lahore, Islamabad, Karachi), Dubái, Arabia Saudí, Londres y todo el mundo. Franquicia de equipaje especial hasta 46 kg y reemisión de billetes sin complicaciones.</p>
            <router-link to="/contact" class="srv-link">Consultar Vuelos <i class="fa-solid fa-arrow-right"></i></router-link>
          </div>

          <!-- Service 2 -->
          <div class="service-card-premium featured">
            <div class="srv-badge">Especialidad Líder</div>
            <div class="srv-icon-box"><i class="fa-solid fa-kaaba"></i></div>
            <h3>Paquetes Umrah desde España</h3>
            <p>Organización completa de peregrinaciones a La Meca y Medina. Salidas directas desde Barcelona y Madrid, hoteles 5★ frente al Haram, tramitación de visado y guías bilingües en español y urdu.</p>
            <router-link to="/umrah-desde-espana" class="srv-link">Ver Paquetes Umrah <i class="fa-solid fa-arrow-right"></i></router-link>
          </div>

          <!-- Service 3 -->
          <div class="service-card-premium">
            <div class="srv-icon-box"><i class="fa-solid fa-passport"></i></div>
            <h3>Tramitación de Visados</h3>
            <p>Gestión rápida de visados turísticos y de negocios para Arabia Saudí (e-Visa), Emiratos Árabes (Dubái), Reino Unido y países Schengen. Asesoramiento documental y alta tasa de aprobación.</p>
            <router-link to="/visados" class="srv-link">Gestionar Visado <i class="fa-solid fa-arrow-right"></i></router-link>
          </div>

          <!-- Service 4 -->
          <div class="service-card-premium">
            <div class="srv-icon-box"><i class="fa-solid fa-hotel"></i></div>
            <h3>Hoteles en La Meca y Medina</h3>
            <p>Convenios directos con hoteles del complejo Abraj Al-Bait (Clock Tower) y primera línea de la Mezquita del Profeta en Medina. Habitaciones dobles, triples y cuádruples familiares.</p>
            <router-link to="/hoteles-la-meca" class="srv-link">Explorar Hoteles <i class="fa-solid fa-arrow-right"></i></router-link>
          </div>

          <!-- Service 5 -->
          <div class="service-card-premium">
            <div class="srv-icon-box"><i class="fa-solid fa-map-location-dot"></i></div>
            <h3>Paquetes Vacacionales y Tours</h3>
            <p>Circuitos y escapadas a Dubái, Estambul, Capadocia, Malasia y destinos europeos. Itinerarios a medida para parejas y familias con traslados y excursiones privadas.</p>
            <router-link to="/services" class="srv-link">Ver Destinos <i class="fa-solid fa-arrow-right"></i></router-link>
          </div>

          <!-- Service 6 -->
          <div class="service-card-premium">
            <div class="srv-icon-box"><i class="fa-solid fa-handshake-angle"></i></div>
            <h3>Asesoría y Consultoría Presencial</h3>
            <p>Atención cara a cara en nuestra oficina de Fly Planet Badalona (Av. Alfonso XIII 496). Le ayudamos a comparar fechas, escalas, equipaje y resolver cualquier duda sobre su viaje.</p>
            <router-link to="/agencia-viajes-barcelona" class="srv-link">Visitar Oficina <i class="fa-solid fa-arrow-right"></i></router-link>
          </div>
        </div>
      </div>
    </section>

    <!-- 5. FEATURED UMRAH PACKAGES (PREMIUM CARDS) -->
    <section class="featured-umrah-section bg-raised">
      <div class="container">
        <div class="section-head d-flex justify-between align-end">
          <div>
            <span class="eyebrow">Peregrinación Sagrada 2026 - 2027</span>
            <h2 class="section-title">Paquetes de Umrah Destacados con Salidas desde España</h2>
            <p class="section-intro">Viajes completos diseñados para la comodidad de su familia, con hoteles seleccionados a pocos metros del Haram.</p>
          </div>
          <router-link to="/paquetes-umrah" class="btn btn-ghost hide-mobile">Ver Todos los Paquetes →</router-link>
        </div>

        <div class="grid grid-3 umrah-cards-grid">
          <!-- Umrah Card 1 -->
          <div class="card umrah-card-featured">
            <div class="card-arch">
              <span class="badge">10 Días Confort</span>
              <img src="https://images.unsplash.com/photo-1565552645632-d725f8bfc19a?q=80&w=800&auto=format&fit=crop" alt="Paquete Umrah Económico desde España">
            </div>
            <div class="card-body">
              <div class="meta"><i class="fa-solid fa-plane"></i> Salida Barcelona / Madrid</div>
              <h3>Paquete Umrah Confort 10 Días</h3>
              <p class="desc">5 noches en La Meca (a 250m del Haram) y 4 noches en Medina. Incluye vuelos con equipaje (2x23kg), visado oficial saudí con seguro médico y autobús privado.</p>
              <ul class="card-feature-list">
                <li><i class="fa-solid fa-check"></i> Vuelos I/V con equipaje incluido</li>
                <li><i class="fa-solid fa-check"></i> Hotel en Meca y Medina junto al Haram</li>
                <li><i class="fa-solid fa-check"></i> Ziyarat guiado a lugares sagrados</li>
              </ul>
              <div class="card-foot">
                <div class="price">
                  1.450 €<br><small>por persona</small>
                </div>
                <a :href="getWhatsAppBookingLink('Paquete Umrah Confort 10 Días (1.450€)')" target="_blank" rel="noopener" class="btn btn-gold btn-sm">
                  <i class="fa-brands fa-whatsapp"></i> Reservar
                </a>
              </div>
            </div>
          </div>

          <!-- Umrah Card 2 -->
          <div class="card umrah-card-featured vip-card">
            <div class="card-arch">
              <span class="badge badge-gold">5★ Clock Tower VIP</span>
              <img src="https://images.unsplash.com/photo-1542051841857-5f90071e7989?q=80&w=800&auto=format&fit=crop" alt="Paquete Umrah 5 Estrellas Clock Tower">
            </div>
            <div class="card-body">
              <div class="meta"><i class="fa-solid fa-star" style="color:var(--gold);"></i> Alojamiento de Lujo</div>
              <h3>Umrah Premium 14 Días (Clock Tower)</h3>
              <p class="desc">Alojamiento en Abraj Al-Bait (Swissôtel / Pullman Zamzam) con acceso directo en ascensor a la explanada del Haram. Desayuno buffet internacional y traslados VIP.</p>
              <ul class="card-feature-list">
                <li><i class="fa-solid fa-check"></i> Vistas panorámicas hacia el Haram</li>
                <li><i class="fa-solid fa-check"></i> Vuelos con Saudia o Qatar Airways</li>
                <li><i class="fa-solid fa-check"></i> Hotel 5★ en Medina frente a Nabawi</li>
              </ul>
              <div class="card-foot">
                <div class="price">
                  2.190 €<br><small>por persona</small>
                </div>
                <a :href="getWhatsAppBookingLink('Umrah Premium 14 Días Clock Tower (2.190€)')" target="_blank" rel="noopener" class="btn btn-gold btn-sm">
                  <i class="fa-brands fa-whatsapp"></i> Reservar VIP
                </a>
              </div>
            </div>
          </div>

          <!-- Umrah Card 3 -->
          <div class="card umrah-card-featured">
            <div class="card-arch">
              <span class="badge">Salida Grupal Mensual</span>
              <img src="https://images.unsplash.com/photo-1519817650390-64a93db51149?q=80&w=800&auto=format&fit=crop" alt="Umrah Grupal Acompañado con Guía">
            </div>
            <div class="card-body">
              <div class="meta"><i class="fa-solid fa-users"></i> Grupo Acompañado</div>
              <h3>Umrah Grupal con Guía (Muallim)</h3>
              <p class="desc">Viaje organizado con coordinador desde Barcelona. Charlas preparatorias de los ritos del Ihram, grupo de apoyo en WhatsApp y visitas históricas guiadas.</p>
              <ul class="card-feature-list">
                <li><i class="fa-solid fa-check"></i> Acompañamiento espiritual continuo</li>
                <li><i class="fa-solid fa-check"></i> Kit de Ihram y agua Zamzam de regalo</li>
                <li><i class="fa-solid fa-check"></i> Ideal para familias y primera vez</li>
              </ul>
              <div class="card-foot">
                <div class="price">
                  1.580 €<br><small>por persona</small>
                </div>
                <a :href="getWhatsAppBookingLink('Umrah Grupal con Guía (1.580€)')" target="_blank" rel="noopener" class="btn btn-gold btn-sm">
                  <i class="fa-brands fa-whatsapp"></i> Unirse al Grupo
                </a>
              </div>
            </div>
          </div>
        </div>

        <div class="text-center" style="margin-top: 36px;">
          <router-link to="/umrah-desde-espana" class="btn btn-primary" style="margin-right: 12px;">
            Conocer Todo sobre Umrah desde España
          </router-link>
          <router-link to="/umrah-desde-barcelona" class="btn btn-gold">
            Salidas Específicas desde Barcelona (BCN)
          </router-link>
        </div>
      </div>
    </section>

    <!-- 6. FEATURED INTERNATIONAL DESTINATIONS -->
    <section class="destinations-section">
      <div class="container">
        <div class="section-head text-center">
          <span class="eyebrow">Conexiones Globales</span>
          <h2 class="section-title">Destinos Internacionales Populares</h2>
          <p class="section-intro">Emitimos billetes aéreos con las mejores tarifas para viajar a ver a su familia, disfrutar de vacaciones o realizar negocios.</p>
        </div>

        <div class="grid grid-3 destination-cards-grid">
          <div class="dest-card" v-for="dest in internationalDestinations" :key="dest.title">
            <div class="dest-card-bg" :style="{ backgroundImage: `url(${dest.image})` }"></div>
            <div class="dest-card-overlay"></div>
            <div class="dest-card-content">
              <span class="dest-tag">{{ dest.tag }}</span>
              <h3>{{ dest.title }}</h3>
              <p>{{ dest.desc }}</p>
              <div class="dest-card-bottom">
                <span class="dest-price">{{ dest.priceText }}</span>
                <a :href="getWhatsAppBookingLink('Vuelo a ' + dest.title)" target="_blank" rel="noopener" class="dest-btn">
                  Cotizar <i class="fa-solid fa-arrow-right"></i>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- 7. VISA SERVICES HIGHLIGHT -->
    <section class="visa-showcase-section bg-raised">
      <div class="container">
        <div class="section-head d-flex justify-between align-end">
          <div>
            <span class="eyebrow">Trámites Consulares</span>
            <h2 class="section-title">Servicio de Visados Internacionales</h2>
            <p class="section-intro">Evite errores en sus solicitudes consulares. Tramitamos su visado de forma rápida y con total garantía.</p>
          </div>
          <router-link to="/visados" class="btn btn-ghost hide-mobile">Ver Todos los Visados →</router-link>
        </div>

        <div class="grid grid-4 visa-cards-grid">
          <div class="card visa-card-modern" v-for="v in featuredVisas" :key="v.country">
            <div class="visa-card-head">
              <span class="visa-flag">{{ v.flag }}</span>
              <span class="visa-time-badge"><i class="fa-solid fa-bolt"></i> {{ v.time }}</span>
            </div>
            <div class="card-body">
              <h3>{{ v.country }}</h3>
              <div class="meta">{{ v.type }}</div>
              <p class="desc">{{ v.desc }}</p>
              <div class="card-foot">
                <div class="price">
                  {{ v.price }}<br><small>por solicitante</small>
                </div>
                <a :href="getWhatsAppBookingLink('Visado para ' + v.country)" target="_blank" rel="noopener" class="btn btn-gold btn-sm">
                  Solicitar
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- 8. AIRLINE PARTNER LOGOS MARQUEE -->
    <section class="airline-partners-section">
      <div class="container">
        <div class="section-head text-center">
          <span class="eyebrow">Compañías Aéreas Mundiales</span>
          <h2 class="section-title">Aerolíneas Oficiales con las que Emitimos sus Billetes</h2>
        </div>
      </div>

      <div class="airline-marquee">
        <div class="airline-track">
          <!-- Set 1 -->
          <div class="airline-logo" v-for="a in airlinePartners" :key="a.code">
            <img :src="a.logo" :alt="a.name" class="airline-img" loading="lazy">
          </div>
          <!-- Cloned for infinite seamless loop -->
          <div class="airline-logo" v-for="a in airlinePartners" :key="a.code + '-clone'">
            <img :src="a.logo" :alt="a.name" class="airline-img" loading="lazy">
          </div>
        </div>
      </div>
    </section>

    <!-- 9. WHY CHOOSE SANGUGROUP -->
    <section class="why-choose-section">
      <div class="container">
        <div class="section-head text-center">
          <span class="eyebrow" style="color: var(--gold);">Ventajas SanguGroup</span>
          <h2 class="section-title" style="color: #ffffff;">¿Por Qué Miles de Familias Confían en Nosotros?</h2>
          <p class="section-intro" style="color: rgba(255, 255, 255, 0.85);">
            Combinamos presencia física en Europa y Asia con la calidez de un trato cercano, asegurando que su dinero y su viaje estén siempre en buenas manos.
          </p>
        </div>

        <div class="grid grid-4 why-cards-grid">
          <div class="why-card">
            <div class="why-icon"><i class="fa-solid fa-building"></i></div>
            <h4>Oficina Presencial en Barcelona</h4>
            <p>Visítenos en Badalona (Av. Alfonso XIII 496). Sin intermediarios fantasmas ni teléfonos desatendidos: cara a cara con profesionales.</p>
          </div>
          <div class="why-card">
            <div class="why-icon"><i class="fa-solid fa-hand-holding-dollar"></i></div>
            <h4>Precios Transparentes</h4>
            <p>Tarifas claras en euros sin tasas ocultas de última hora. Mejor precio garantizado con franquicia de equipaje completa.</p>
          </div>
          <div class="why-card">
            <div class="why-icon"><i class="fa-solid fa-headset"></i></div>
            <h4>Atención Multilingüe 24/7</h4>
            <p>Le atendemos en su idioma materno: español, urdu, punjabi o inglés. Coordinación constante durante su viaje o peregrinación.</p>
          </div>
          <div class="why-card">
            <div class="why-icon"><i class="fa-solid fa-credit-card"></i></div>
            <h4>Facilidades de Pago Seguro</h4>
            <p>Pague a plazos con total flexibilidad mediante transferencia bancaria, Revolut o directamente en nuestras oficinas.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- 10. CUSTOMER TESTIMONIALS -->
    <section class="testimonials-section bg-raised">
      <div class="container">
        <div class="section-head text-center">
          <span class="eyebrow">Experiencias Reales</span>
          <h2 class="section-title">Lo que Opinan Nuestros Clientes y Peregrinos</h2>
          <p class="section-intro">La satisfacción y las bendiciones de nuestros viajeros son nuestra mayor recompensa.</p>
        </div>

        <div class="grid grid-3 testimonials-grid">
          <div class="testimonial-card-premium" v-for="t in verifiedTestimonials" :key="t.name">
            <div class="testi-stars">★★★★★</div>
            <p class="testi-text">"{{ t.comment }}"</p>
            <div class="testi-author-box">
              <div class="testi-avatar">{{ t.initial }}</div>
              <div>
                <strong>{{ t.name }}</strong>
                <span>{{ t.location }} · {{ t.service }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- 11. LATEST TRAVEL BLOGS -->
    <section class="latest-blogs-section" v-if="blogs.length">
      <div class="container">
        <div class="section-head d-flex justify-between align-end">
          <div>
            <span class="eyebrow">Guías y Consejos</span>
            <h2 class="section-title">Artículos de Viaje y Guías de Umrah</h2>
            <p class="section-intro">Información actualizada sobre requisitos de visado, consejos para la peregrinación y ofertas aéreas.</p>
          </div>
          <router-link to="/blog" class="btn btn-ghost hide-mobile">Ver Todo el Blog →</router-link>
        </div>

        <div class="grid grid-3 blog-cards-grid">
          <router-link
            v-for="b in blogs.slice(0, 3)"
            :key="b.id"
            :to="`/blog/${b.slug}`"
            class="blog-card-modern"
          >
            <div class="blog-thumb">
              <img :src="b.image_url || 'https://images.unsplash.com/photo-1519817650390-64a93db51149?q=80&w=800&auto=format&fit=crop'" :alt="b.title" loading="lazy">
            </div>
            <div class="blog-body">
              <span class="blog-tag">Guía de Viaje</span>
              <h3>{{ b.title }}</h3>
              <p>{{ (b.excerpt || b.content || '').substring(0, 110) }}...</p>
              <span class="blog-read-more">Leer artículo completo →</span>
            </div>
          </router-link>
        </div>
      </div>
    </section>

    <!-- 12. DUAL OFFICE CONTACT & WHATSAPP CTA -->
    <section class="contact-cta-section">
      <div class="container">
        <div class="contact-cta-grid">
          <!-- Left: Information & Offices -->
          <div class="contact-cta-info">
            <span class="eyebrow" style="color: var(--gold);">Oficinas Oficiales</span>
            <h2>¿Listo para Planificar su Viaje? Visítenos o Escríbanos</h2>
            <p>Nuestro equipo de asesores en Barcelona y Pakistán está disponible para buscarle la mejor opción al mejor precio.</p>

            <div class="office-location-card">
              <div class="off-badge">🇪🇸 Oficina España (Badalona / Barcelona)</div>
              <h4>Fly Planet Badalona (SanguGroup)</h4>
              <p><i class="fa-solid fa-location-dot"></i> Av. Alfonso XIII 496, 08913 Badalona, Barcelona, España</p>
              <p><i class="fa-solid fa-phone"></i> +34 672 06 81 48 · +34 631 02 88 71</p>
              <p><i class="fa-solid fa-clock"></i> Lunes a Sábado: 09:30 - 20:30 CET</p>
            </div>

            <div class="office-location-card" style="margin-top: 16px;">
              <div class="off-badge pk">🇵🇰 Oficina Pakistán (Soporte 24/7)</div>
              <h4>Sangu Travel & Tours</h4>
              <p><i class="fa-solid fa-phone-volume"></i> +92 308 5092631</p>
              <p><i class="fa-solid fa-envelope"></i> sangutravel@gmail.com</p>
            </div>
          </div>

          <!-- Right: Interactive Quick Inquiry Form -->
          <div class="contact-quick-form-card">
            <h3>Enviar Consulta Rápida</h3>
            <p>Le respondemos en menos de 1 hora con presupuesto detallado:</p>

            <form @submit.prevent="submitDirectInquiry">
              <div class="form-group">
                <label>Nombre y Apellidos *</label>
                <input type="text" v-model="inquiryForm.name" placeholder="Ej. Tariq Mehmood" required>
              </div>

              <div class="form-row">
                <div class="form-group">
                  <label>Teléfono / WhatsApp *</label>
                  <input type="tel" v-model="inquiryForm.phone" placeholder="+34 600 000 000" required>
                </div>
                <div class="form-group">
                  <label>Email (opcional)</label>
                  <input type="email" v-model="inquiryForm.email" placeholder="correo@ejemplo.com">
                </div>
              </div>

              <div class="form-group">
                <label>Tipo de Viaje o Servicio</label>
                <select v-model="inquiryForm.service">
                  <option value="Paquete Umrah desde España">Paquete Umrah desde España</option>
                  <option value="Billetes de Avión a Pakistán">Billetes de Avión a Pakistán</option>
                  <option value="Vuelos Internacionales">Otros Vuelos Internacionales</option>
                  <option value="Tramitación de Visados">Tramitación de Visados</option>
                  <option value="Reserva de Hoteles">Reserva de Hoteles en La Meca / Medina</option>
                  <option value="Otro Servicio">Otro servicio de viaje</option>
                </select>
              </div>

              <div class="form-group">
                <label>Detalles de su Consulta</label>
                <textarea rows="3" v-model="inquiryForm.message" placeholder="Indique fechas aproximadas, personas, aeropuertos o cualquier requerimiento..."></textarea>
              </div>

              <button type="submit" class="btn btn-gold btn-block" :disabled="inquirySubmitting">
                {{ inquirySubmitting ? 'Enviando consulta...' : 'Enviar Solicitud Ahora' }}
              </button>

              <div v-if="inquirySuccess" class="inquiry-alert success">
                ✓ ¡Consulta enviada con éxito! Nos pondremos en contacto con usted enseguida.
              </div>
              <div v-if="inquiryError" class="inquiry-alert error">
                Hubo un error al enviar. Por favor contáctenos por WhatsApp al +34 672 06 81 48.
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>

    <!-- Promotion Lightbox (if any) -->
    <transition name="lb-fade">
      <div v-if="promoLightboxOpen" class="lightbox-overlay" @click.self="promoLightboxOpen = false">
        <button class="lightbox-close" @click="promoLightboxOpen = false" aria-label="Cerrar">×</button>
        <img :src="promoLightboxImage" alt="Promoción" class="lightbox-image">
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import { t } from './locale.js';

const router = useRouter();

// HERO SLIDER ASSETS
const heroImages = [
  'https://images.unsplash.com/photo-1591604129939-f1efa4d9f7fa?q=80&w=1920&auto=format&fit=crop', // Makkah Haram
  'https://images.unsplash.com/photo-1542051841857-5f90071e7989?q=80&w=1920&auto=format&fit=crop', // Madinah Nabawi
  'https://images.unsplash.com/photo-1436491865332-7a61a109cc05?q=80&w=1920&auto=format&fit=crop', // Luxury Aircraft flight
  'https://images.unsplash.com/photo-1583422409516-2895a77efded?q=80&w=1920&auto=format&fit=crop', // Barcelona Sagrada Familia
  'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?q=80&w=1920&auto=format&fit=crop', // Dubai skyline
];

const heroVideos = [
  '/images/video/11992948-hd_1080_1920_60fps.mp4',
  '/images/video/7010424-uhd_3840_2160_25fps.mp4',
];

const lastHeroImage = heroImages[0];
const heroVideoFailed = ref(Array(heroVideos.length).fill(false));
const heroIndex = ref(0);
let heroTimer = null;

const onHeroVideoError = (vi) => {
  heroVideoFailed.value[vi] = true;
};

const goToHero = (i) => {
  heroIndex.value = i;
};

const nextHero = () => {
  const total = heroImages.length + heroVideos.length;
  heroIndex.value = (heroIndex.value + 1) % total;
};

const startHeroSlider = () => {
  stopHeroSlider();
  heroTimer = setInterval(() => {
    if (heroIndex.value >= heroImages.length) return;
    nextHero();
  }, 6000);
};

const stopHeroSlider = () => {
  if (heroTimer) clearInterval(heroTimer);
};

// SEARCH ENGINE TABS & FORMS
const searchTab = ref('flights');

const flightForm = ref({
  origin: 'Barcelona (BCN)',
  destination: '',
  departureDate: '',
  returnDate: '',
  passengers: '1 Adulto (Turista)',
});

const umrahForm = ref({
  departureCity: 'Barcelona (El Prat BCN)',
  packageType: 'Confort 4 Estrellas (12 Días)',
  travelMonth: 'Próxima salida disponible',
  travelers: '2 personas (Pareja)',
});

const visaForm = ref({
  country: 'Arabia Saudí (Turismo / Umrah)',
  nationality: '',
  applicants: '1 persona',
});

const hotelForm = ref({
  city: 'La Meca (Makkah)',
  category: '5 Estrellas Lujo / Clock Tower',
  dates: '',
});

// SUBMITTERS WITH WHATSAPP LINK GENERATION
const submitFlightInquiry = () => {
  const text = `*Consulta de Vuelo - SanguGroup Web*%0A` +
    `*Origen:* ${encodeURIComponent(flightForm.value.origin)}%0A` +
    `*Destino:* ${encodeURIComponent(flightForm.value.destination)}%0A` +
    `*Fecha Salida:* ${encodeURIComponent(flightForm.value.departureDate)}%0A` +
    `*Fecha Regreso:* ${encodeURIComponent(flightForm.value.returnDate || 'Sólo ida')}%0A` +
    `*Pasajeros:* ${encodeURIComponent(flightForm.value.passengers)}`;
  window.open(`https://wa.me/34672068148?text=${text}`, '_blank');
};

const submitUmrahInquiry = () => {
  const text = `*Solicitud de Presupuesto Umrah - SanguGroup Web*%0A` +
    `*Salida:* ${encodeURIComponent(umrahForm.value.departureCity)}%0A` +
    `*Paquete:* ${encodeURIComponent(umrahForm.value.packageType)}%0A` +
    `*Mes Deseado:* ${encodeURIComponent(umrahForm.value.travelMonth)}%0A` +
    `*Personas:* ${encodeURIComponent(umrahForm.value.travelers)}`;
  window.open(`https://wa.me/34672068148?text=${text}`, '_blank');
};

const submitVisaInquiry = () => {
  const text = `*Consulta de Visado - SanguGroup Web*%0A` +
    `*País Destino:* ${encodeURIComponent(visaForm.value.country)}%0A` +
    `*Nacionalidad:* ${encodeURIComponent(visaForm.value.nationality)}%0A` +
    `*Solicitantes:* ${encodeURIComponent(visaForm.value.applicants)}`;
  window.open(`https://wa.me/34672068148?text=${text}`, '_blank');
};

const submitHotelInquiry = () => {
  const text = `*Consulta de Hotel - SanguGroup Web*%0A` +
    `*Ciudad:* ${encodeURIComponent(hotelForm.value.city)}%0A` +
    `*Categoría:* ${encodeURIComponent(hotelForm.value.category)}%0A` +
    `*Fechas:* ${encodeURIComponent(hotelForm.value.dates || 'Por definir')}`;
  window.open(`https://wa.me/34672068148?text=${text}`, '_blank');
};

const getWhatsAppBookingLink = (serviceName) => {
  const text = encodeURIComponent(`Hola SanguGroup, deseo solicitar presupuesto e información sobre: ${serviceName}`);
  return `https://wa.me/34672068148?text=${text}`;
};

// DIRECT INQUIRY FORM (BOTTOM SECTION)
const inquiryForm = ref({
  name: '',
  phone: '',
  email: '',
  service: 'Paquete Umrah desde España',
  message: '',
});
const inquirySubmitting = ref(false);
const inquirySuccess = ref(false);
const inquiryError = ref(false);

const submitDirectInquiry = async () => {
  inquirySubmitting.value = true;
  inquirySuccess.value = false;
  inquiryError.value = false;

  try {
    await axios.post('/api/inquiry', {
      name: inquiryForm.value.name,
      phone: inquiryForm.value.phone,
      email: inquiryForm.value.email || null,
      subject: `[Home Web] Consulta: ${inquiryForm.value.service}`,
      message: `Servicio: ${inquiryForm.value.service}\nDetalles: ${inquiryForm.value.message || 'Sin mensaje adicional'}`,
    });
    inquirySuccess.value = true;
    inquiryForm.value.name = '';
    inquiryForm.value.phone = '';
    inquiryForm.value.message = '';
  } catch (err) {
    console.error('Inquiry submission error:', err);
    inquiryError.value = true;
  } finally {
    inquirySubmitting.value = false;
  }
};

// STATIC & DYNAMIC DATA
const promotions = ref([]);
const heroPromoIndex = ref(0);
const currentHeroPromo = computed(() => promotions.value[heroPromoIndex.value] || {});
const promoLightboxOpen = ref(false);
const promoLightboxImage = ref('');

const blogs = ref([]);

const internationalDestinations = [
  {
    tag: 'Ruta Estrella Pakistán',
    title: 'Pakistán (Lahore, Islamabad, Karachi)',
    desc: 'Vuelos regulares desde Barcelona y Madrid con franquicia de 40 kg o 2x23 kg. Conexiones cómodas con Qatar Airways y Emirates.',
    priceText: 'Tarifas Especiales Familia',
    image: 'https://images.unsplash.com/photo-1506012787146-f92b2d7d6d96?q=80&w=800&auto=format&fit=crop',
  },
  {
    tag: 'Peregrinación Sagrada',
    title: 'La Meca y Medina (Arabia Saudí)',
    desc: 'Vuelos para Umrah a Jeddah y Medina. Programas completos con hoteles 5★, traslados privados y visados oficiales.',
    priceText: 'Desde 1.290 € Paquete',
    image: 'https://images.unsplash.com/photo-1542051841857-5f90071e7989?q=80&w=800&auto=format&fit=crop',
  },
  {
    tag: 'Vacaciones y Conexión',
    title: 'Dubái y Abu Dhabi (EAU)',
    desc: 'Vuelos diarios con Emirates y Qatar. Tramitación de visados turísticos express en 24-48 horas y paquetes de hotel.',
    priceText: 'Desde 390 € Vuelo',
    image: 'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?q=80&w=800&auto=format&fit=crop',
  },
  {
    tag: 'Europa y Negocios',
    title: 'Reino Unido (Londres, Manchester)',
    desc: 'Vuelos diarios entre España y Reino Unido. Billetes de avión económicos para visitas familiares y turismo.',
    priceText: 'Desde 65 € Vuelo',
    image: 'https://images.unsplash.com/photo-1513635269975-59663e0ac1ad?q=80&w=800&auto=format&fit=crop',
  },
  {
    tag: 'Cultura y Escapadas',
    title: 'Estambul (Turquía)',
    desc: 'Vuelos directos con Turkish Airlines y Pegasus. Visitas a Santa Sofía, Mezquita Azul y cruceros por el Bósforo.',
    priceText: 'Desde 180 € Vuelo',
    image: 'https://images.unsplash.com/photo-1524231757912-21f4fe3a7200?q=80&w=800&auto=format&fit=crop',
  },
  {
    tag: 'Hub España',
    title: 'Barcelona y Madrid',
    desc: 'Nuestra sede en Badalona conecta Cataluña y toda España con las principales capitales del mundo.',
    priceText: 'Oficina Central BCN',
    image: 'https://images.unsplash.com/photo-1583422409516-2895a77efded?q=80&w=800&auto=format&fit=crop',
  },
];

const featuredVisas = [
  {
    country: 'Arabia Saudí',
    flag: '🇸🇦',
    time: '24-48 Horas',
    type: 'e-Visa Turismo / Umrah',
    desc: 'Visado de múltiples entradas con 1 año de validez. Válido para peregrinación de Umrah y visitas familiares en todo el país.',
    price: '165 €',
  },
  {
    country: 'Emiratos Árabes (Dubái)',
    flag: '🇦🇪',
    time: '48 Horas',
    type: 'Visado Turístico (30/60 Días)',
    desc: 'Visado electrónico para entrar por Dubái, Abu Dhabi o Sharjah. Sólo con copia de pasaporte y foto digital.',
    price: '120 €',
  },
  {
    country: 'Reino Unido (UK)',
    flag: '🇬🇧',
    time: 'Asesoría Integral',
    type: 'Standard Visitor Visa',
    desc: 'Preparación minuciosa de documentación económica, formulario oficial en inglés y cita biométrica consular.',
    price: 'Consultar',
  },
  {
    country: 'Turquía',
    flag: '🇹🇷',
    time: '24 Horas',
    type: 'e-Visa de Turismo',
    desc: 'Emisión electrónica inmediata para ciudadanos que cumplan los requisitos de visado turco.',
    price: '85 €',
  },
];

const airlinePartners = [
  { code: 'EK', name: 'Emirates', logo: '/images/airlines/EK.png' },
  { code: 'QR', name: 'Qatar Airways', logo: '/images/airlines/QR.png' },
  { code: 'SV', name: 'Saudia', logo: '/images/airlines/SV.png' },
  { code: 'PK', name: 'PIA', logo: '/images/airlines/PK.png' },
  { code: 'TK', name: 'Turkish Airlines', logo: '/images/airlines/TK.png' },
  { code: 'EY', name: 'Etihad Airways', logo: '/images/airlines/EY.png' },
  { code: 'BA', name: 'British Airways', logo: '/images/airlines/BA.png' },
  { code: 'LH', name: 'Lufthansa', logo: '/images/airlines/LH.png' },
  { code: 'AF', name: 'Air France', logo: '/images/airlines/AF.png' },
  { code: 'KL', name: 'KLM', logo: '/images/airlines/KL.png' },
  { code: 'FZ', name: 'flydubai', logo: '/images/airlines/FZ.png' },
  { code: 'G9', name: 'Air Arabia', logo: '/images/airlines/G9.png' },
  { code: 'GF', name: 'Gulf Air', logo: '/images/airlines/GF.png' },
  { code: 'WY', name: 'Oman Air', logo: '/images/airlines/WY.png' },
  { code: 'XY', name: 'flynas', logo: '/images/airlines/XY.png' },
  { code: 'W6', name: 'Wizz Air', logo: '/images/airlines/W6.png' },
];

const verifiedTestimonials = [
  {
    name: 'Muhammad Tariq',
    initial: 'M',
    location: 'Barcelona (Badalona)',
    service: 'Paquete Umrah Familiar',
    comment: 'Viajamos toda la familia a la Umrah con SanguGroup desde Barcelona. El hotel en La Meca estaba a menos de 3 minutos a pie del Haram. El trato en la oficina de Badalona fue de diez y todo lo acordado se cumplió al 100%.',
  },
  {
    name: 'Rashid Mahmood',
    initial: 'R',
    location: 'Madrid',
    service: 'Billetes de Avión a Lahore',
    comment: 'Siempre compro mis vuelos a Pakistán con ellos. Nos consiguieron franquicia de 46 kg con Qatar Airways a un precio mucho más bajo que en internet, y ante un cambio de horario me atendieron de inmediato por WhatsApp.',
  },
  {
    name: 'Fatima Zohra',
    initial: 'F',
    location: 'Valencia',
    service: 'Umrah 5 Estrellas Clock Tower',
    comment: 'Alojarnos en la Torre del Reloj con vistas a la Kaaba fue el sueño de nuestra vida. Las gestiones del visado y los traslados en autobús fueron impecables. Que Allah bendiga al equipo de SanguGroup.',
  },
];

const handleHeroPromoClick = (promo) => {
  if (promo.link_url) {
    if (promo.link_url.startsWith('http')) {
      window.open(promo.link_url, '_blank');
    } else {
      router.push(promo.link_url);
    }
  } else if (promo.image_url) {
    promoLightboxImage.value = promo.image_url;
    promoLightboxOpen.value = true;
  }
};

onMounted(async () => {
  startHeroSlider();

  try {
    const promoRes = await axios.get('/api/promotions');
    if (promoRes.data && Array.isArray(promoRes.data)) {
      promotions.value = promoRes.data.filter(p => p.is_active !== false);
    }
  } catch (e) {
    // Graceful fallback
  }

  try {
    const blogRes = await axios.get('/api/blogs');
    if (blogRes.data && Array.isArray(blogRes.data)) {
      blogs.value = blogRes.data.filter(b => b.is_active !== false);
    }
  } catch (e) {
    // Graceful fallback
  }
});

onUnmounted(() => {
  stopHeroSlider();
});
</script>

<style scoped>
.homepage-redesign {
  background-color: #fbfbfd;
  color: var(--ink);
}

/* 2. HERO ANIMATED */
.hero-animated {
  position: relative;
  min-height: 82vh;
  display: flex;
  align-items: center;
  background-color: #061120;
  overflow: hidden;
  padding: 60px 0 80px;
}

.hero-bg {
  position: absolute;
  inset: 0;
  background-size: cover;
  background-position: center;
  opacity: 0;
  transition: opacity 1.2s ease-in-out;
  z-index: 1;
}

.hero-bg.visible {
  opacity: 0.38;
}

.hero-bg video {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.hero-inner {
  position: relative;
  z-index: 5;
  width: 100%;
  max-width: var(--maxw);
  margin: 0 auto;
  padding: 0 28px;
  display: grid;
  grid-template-columns: 1fr;
  align-items: center;
}

.hero-inner.has-promo {
  grid-template-columns: 1.15fr 0.85fr;
  gap: 40px;
}

.hero-content-wrap {
  max-width: 680px;
}

.hero-trust-tag {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: rgba(197, 160, 89, 0.2);
  border: 1px solid rgba(197, 160, 89, 0.45);
  color: var(--gold);
  padding: 6px 14px;
  border-radius: 999px;
  font-size: 12.5px;
  font-weight: 700;
  letter-spacing: 0.05em;
  text-transform: uppercase;
  margin-bottom: 20px;
}

.pulse-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: var(--gold);
}

.hero-main-title {
  font-size: clamp(34px, 4.5vw, 56px);
  color: #ffffff;
  font-weight: 800;
  line-height: 1.14;
  margin-bottom: 18px;
  font-family: 'Fraunces', serif;
}

.hero-main-title em {
  font-style: normal;
  color: var(--gold);
}

.hero-subtitle {
  font-size: 17px;
  line-height: 1.65;
  color: rgba(255, 255, 255, 0.88);
  margin-bottom: 28px;
}

.hero-cta-buttons {
  display: flex;
  gap: 14px;
  flex-wrap: wrap;
  margin-bottom: 32px;
}

.btn-hero {
  padding: 14px 26px;
  font-size: 15px;
  font-weight: 700;
}

.hero-badges-row {
  display: flex;
  gap: 18px;
  flex-wrap: wrap;
  border-top: 1px solid rgba(255, 255, 255, 0.14);
  padding-top: 20px;
}

.h-badge {
  font-size: 13px;
  font-weight: 600;
  color: rgba(255, 255, 255, 0.9);
  display: flex;
  align-items: center;
  gap: 6px;
}

.h-badge i {
  color: var(--gold);
}

/* HERO PROMO CARD */
.hero-promo {
  background: rgba(255, 255, 255, 0.08);
  backdrop-filter: blur(16px);
  border: 1px solid rgba(255, 255, 255, 0.16);
  border-radius: 20px;
  padding: 22px;
  color: #ffffff;
}

.hero-promo-eyebrow {
  font-size: 12px;
  font-weight: 800;
  color: var(--gold);
  text-transform: uppercase;
  letter-spacing: 0.08em;
}

.hero-promo-card {
  margin-top: 12px;
  background: #ffffff;
  color: var(--ink);
  border-radius: 14px;
  overflow: hidden;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}

.hero-promo-card img {
  width: 100%;
  height: 180px;
  object-fit: cover;
}

.hero-promo-placeholder {
  height: 140px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 40px;
  background: #f1f5f9;
}

.hero-promo-body {
  padding: 16px;
}

.hero-promo-body h3 {
  font-size: 18px;
  color: var(--teal-dark);
  margin-bottom: 6px;
}

.hero-promo-body p {
  font-size: 13.5px;
  color: var(--ink-soft);
  margin-bottom: 12px;
}

.hero-promo-btn {
  display: inline-block;
  font-size: 13px;
  font-weight: 700;
  color: var(--teal);
  cursor: pointer;
}

.hero-promo-btn:hover {
  color: var(--gold-dark);
}

.hero-promo-dots {
  display: flex;
  gap: 6px;
  margin-top: 14px;
  justify-content: center;
}

.hero-promo-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.4);
  border: none;
  cursor: pointer;
}

.hero-promo-dot.active {
  background: var(--gold);
}

.hero-dots {
  position: absolute;
  bottom: 20px;
  left: 50%;
  transform: translateX(-50%);
  display: flex;
  gap: 8px;
  z-index: 10;
}

.hero-dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.4);
  border: none;
  cursor: pointer;
  transition: all 0.2s;
}

.hero-dot.active {
  width: 24px;
  border-radius: 999px;
  background: var(--gold);
}

/* 3. TRAVEL SEARCH SECTION */
.travel-search-section {
  position: relative;
  z-index: 20;
  margin-top: -46px;
  padding-bottom: 70px;
}

.travel-search-card {
  background: #ffffff;
  border-radius: 20px;
  box-shadow: 0 20px 60px rgba(10, 25, 47, 0.12);
  border: 1px solid #e2e8f0;
  overflow: hidden;
}

.travel-search-tabs {
  display: flex;
  background: #f8fafc;
  border-bottom: 1px solid #e2e8f0;
  overflow-x: auto;
}

.travel-search-tabs button {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  padding: 18px 20px;
  background: none;
  border: none;
  font-family: inherit;
  font-size: 15px;
  font-weight: 700;
  color: #64748b;
  cursor: pointer;
  transition: all 0.2s;
  white-space: nowrap;
  border-bottom: 3px solid transparent;
}

.travel-search-tabs button:hover {
  color: var(--teal-dark);
}

.travel-search-tabs button.active {
  color: var(--teal-dark);
  background: #ffffff;
  border-bottom-color: var(--gold);
}

.travel-search-tabs button i {
  color: var(--gold);
  font-size: 17px;
}

.search-form-body {
  padding: 28px 32px;
}

.search-fields-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 18px;
}

.search-fields-grid .form-group label {
  font-size: 12px;
  font-weight: 700;
  color: var(--ink-soft);
  text-transform: uppercase;
  letter-spacing: 0.05em;
  margin-bottom: 6px;
  display: block;
}

.search-fields-grid .form-group input,
.search-fields-grid .form-group select {
  width: 100%;
  padding: 12px 14px;
  border-radius: 10px;
  border: 1px solid #cbd5e1;
  background: #f8fafc;
  font-family: inherit;
  font-size: 14px;
  font-weight: 600;
  color: var(--ink);
  transition: all 0.2s;
}

.search-fields-grid .form-group input:focus,
.search-fields-grid .form-group select:focus {
  background: #ffffff;
  border-color: var(--gold);
  box-shadow: 0 0 0 3px rgba(197, 160, 89, 0.15);
  outline: none;
}

.btn-search-submit {
  padding: 14px 18px;
  font-size: 14px;
  font-weight: 700;
}

.search-form-footer {
  margin-top: 18px;
  border-top: 1px solid #f1f5f9;
  padding-top: 14px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 12px;
  font-size: 13px;
}

.search-badge-note {
  color: #047857;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  gap: 6px;
}

.btn-link-inquiry {
  background: none;
  border: none;
  color: var(--teal);
  font-weight: 700;
  font-size: 13px;
  cursor: pointer;
  text-decoration: underline;
}

/* 4. SERVICES OVERVIEW */
.services-overview-section {
  padding: 70px 0;
}

.service-card-premium {
  background: #ffffff;
  border-radius: 18px;
  padding: 32px 28px;
  border: 1px solid #e2e8f0;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.03);
  transition: transform 0.25s ease, box-shadow 0.25s ease;
  display: flex;
  flex-direction: column;
  position: relative;
}

.service-card-premium:hover {
  transform: translateY(-6px);
  box-shadow: 0 20px 40px rgba(10, 25, 47, 0.1);
  border-color: rgba(197, 160, 89, 0.4);
}

.service-card-premium.featured {
  border: 2px solid var(--gold);
  background: linear-gradient(180deg, #ffffff 0%, #fefcf8 100%);
}

.srv-badge {
  position: absolute;
  top: 14px;
  right: 18px;
  background: var(--gold);
  color: #061120;
  font-size: 10.5px;
  font-weight: 800;
  padding: 4px 8px;
  border-radius: 999px;
  text-transform: uppercase;
}

.srv-icon-box {
  width: 54px;
  height: 54px;
  border-radius: 14px;
  background: rgba(10, 25, 47, 0.05);
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--gold);
  font-size: 24px;
  margin-bottom: 20px;
}

.service-card-premium h3 {
  font-size: 20px;
  color: var(--teal-dark);
  margin-bottom: 12px;
}

.service-card-premium p {
  font-size: 14px;
  color: var(--ink-soft);
  line-height: 1.6;
  margin-bottom: 22px;
  flex: 1;
}

.srv-link {
  font-size: 14px;
  font-weight: 700;
  color: var(--teal);
  display: inline-flex;
  align-items: center;
  gap: 6px;
  transition: transform 0.2s;
}

.srv-link:hover {
  color: var(--gold-dark);
  transform: translateX(4px);
}

/* 5. FEATURED UMRAH SECTION */
.featured-umrah-section {
  padding: 80px 0;
}

.umrah-card-featured {
  border-radius: 18px;
  background: #ffffff;
  overflow: hidden;
  border: 1px solid #e2e8f0;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
  transition: transform 0.25s ease, box-shadow 0.25s ease;
}

.umrah-card-featured:hover {
  transform: translateY(-8px);
  box-shadow: 0 24px 50px rgba(10, 25, 47, 0.12);
}

.umrah-card-featured.vip-card {
  border: 2px solid var(--gold);
}

.badge-gold {
  background: var(--gold) !important;
  color: #061120 !important;
}

.card-feature-list {
  list-style: none;
  padding: 0;
  margin: 12px 0 18px;
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.card-feature-list li {
  font-size: 13px;
  color: var(--ink-soft);
  display: flex;
  align-items: center;
  gap: 8px;
}

.card-feature-list li i {
  color: #10b981;
  font-size: 12px;
}

/* 6. DESTINATIONS SECTION */
.destinations-section {
  padding: 80px 0;
}

.dest-card {
  position: relative;
  height: 320px;
  border-radius: 18px;
  overflow: hidden;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
  display: flex;
  flex-direction: column;
  justify-content: flex-end;
  padding: 24px;
}

.dest-card-bg {
  position: absolute;
  inset: 0;
  background-size: cover;
  background-position: center;
  transition: transform 0.5s ease;
}

.dest-card:hover .dest-card-bg {
  transform: scale(1.08);
}

.dest-card-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(180deg, rgba(6, 17, 32, 0.1) 0%, rgba(6, 17, 32, 0.88) 100%);
}

.dest-card-content {
  position: relative;
  z-index: 2;
  color: #ffffff;
}

.dest-tag {
  display: inline-block;
  font-size: 11px;
  font-weight: 800;
  text-transform: uppercase;
  color: var(--gold);
  letter-spacing: 0.05em;
  margin-bottom: 6px;
}

.dest-card-content h3 {
  font-size: 21px;
  color: #ffffff;
  margin-bottom: 6px;
}

.dest-card-content p {
  font-size: 12.5px;
  color: rgba(255, 255, 255, 0.85);
  margin-bottom: 14px;
  line-height: 1.4;
}

.dest-card-bottom {
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-top: 1px solid rgba(255, 255, 255, 0.2);
  padding-top: 10px;
}

.dest-price {
  font-size: 14px;
  font-weight: 700;
  color: var(--gold);
}

.dest-btn {
  font-size: 13px;
  font-weight: 700;
  color: #ffffff;
  display: inline-flex;
  align-items: center;
  gap: 5px;
}

.dest-btn:hover {
  color: var(--gold);
}

/* 7. VISA SHOWCASE */
.visa-showcase-section {
  padding: 80px 0;
}

.visa-card-modern {
  border-radius: 16px;
  background: #ffffff;
  border: 1px solid #e2e8f0;
  padding: 16px;
  display: flex;
  flex-direction: column;
}

.visa-card-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 14px;
}

.visa-flag {
  font-size: 32px;
}

.visa-time-badge {
  font-size: 11px;
  font-weight: 700;
  background: #ecfdf5;
  color: #065f46;
  padding: 4px 8px;
  border-radius: 999px;
  border: 1px solid #a7f3d0;
}

/* 8. AIRLINE PARTNERS MARQUEE */
.airline-partners-section {
  padding: 60px 0;
  background: #ffffff;
  border-top: 1px solid #e2e8f0;
  border-bottom: 1px solid #e2e8f0;
}

.airline-marquee {
  overflow: hidden;
  white-space: nowrap;
  position: relative;
  width: 100%;
}

.airline-track {
  display: inline-flex;
  align-items: center;
  gap: 48px;
  animation: marquee 32s linear infinite;
}

.airline-logo {
  flex-shrink: 0;
  height: 48px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.airline-img {
  height: 40px;
  width: auto;
  object-fit: contain;
  filter: grayscale(10%) contrast(90%);
  opacity: 0.85;
  transition: all 0.2s;
}

.airline-img:hover {
  filter: none;
  opacity: 1;
  transform: scale(1.05);
}

@keyframes marquee {
  0% { transform: translateX(0); }
  100% { transform: translateX(-50%); }
}

/* 9. WHY CHOOSE */
.why-choose-section {
  background: linear-gradient(135deg, #0A192F 0%, #001E43 100%);
  color: #ffffff;
  padding: 90px 0;
}

.why-card {
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 16px;
  padding: 28px 24px;
  transition: transform 0.2s;
}

.why-card:hover {
  transform: translateY(-5px);
  background: rgba(255, 255, 255, 0.08);
}

.why-icon {
  width: 50px;
  height: 50px;
  border-radius: 12px;
  background: rgba(197, 160, 89, 0.15);
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--gold);
  font-size: 22px;
  margin-bottom: 18px;
}

.why-card h4 {
  font-size: 18px;
  color: #ffffff;
  margin-bottom: 10px;
}

.why-card p {
  font-size: 13.5px;
  color: rgba(255, 255, 255, 0.75);
  line-height: 1.6;
  margin: 0;
}

/* 10. TESTIMONIALS */
.testimonials-section {
  padding: 80px 0;
}

.testimonial-card-premium {
  background: #ffffff;
  border-radius: 18px;
  padding: 30px;
  border: 1px solid #e2e8f0;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.03);
  display: flex;
  flex-direction: column;
}

.testi-stars {
  color: #f59e0b;
  font-size: 16px;
  margin-bottom: 14px;
  letter-spacing: 2px;
}

.testi-text {
  font-size: 14.5px;
  font-style: italic;
  line-height: 1.65;
  color: #374151;
  flex: 1;
  margin-bottom: 20px;
}

.testi-author-box {
  display: flex;
  align-items: center;
  gap: 12px;
  border-top: 1px solid #f1f5f9;
  padding-top: 14px;
}

.testi-avatar {
  width: 42px;
  height: 42px;
  border-radius: 50%;
  background: var(--gold);
  color: #061120;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 800;
  font-size: 16px;
}

.testi-author-box strong {
  display: block;
  font-size: 14px;
  color: var(--teal-dark);
}

.testi-author-box span {
  display: block;
  font-size: 12px;
  color: var(--ink-soft);
}

/* 11. BLOGS */
.latest-blogs-section {
  padding: 80px 0;
}

.blog-card-modern {
  background: #ffffff;
  border-radius: 16px;
  overflow: hidden;
  border: 1px solid #e2e8f0;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.03);
  display: flex;
  flex-direction: column;
  transition: transform 0.2s;
}

.blog-card-modern:hover {
  transform: translateY(-6px);
}

.blog-thumb {
  height: 200px;
  overflow: hidden;
}

.blog-thumb img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.4s;
}

.blog-card-modern:hover .blog-thumb img {
  transform: scale(1.05);
}

.blog-body {
  padding: 22px;
  display: flex;
  flex-direction: column;
  flex: 1;
}

.blog-tag {
  font-size: 11px;
  font-weight: 800;
  text-transform: uppercase;
  color: var(--gold-dark);
  margin-bottom: 8px;
}

.blog-body h3 {
  font-size: 18px;
  color: var(--teal-dark);
  margin-bottom: 10px;
}

.blog-body p {
  font-size: 13.5px;
  color: var(--ink-soft);
  line-height: 1.5;
  margin-bottom: 16px;
  flex: 1;
}

.blog-read-more {
  font-size: 13px;
  font-weight: 700;
  color: var(--teal);
}

/* 12. CONTACT CTA */
.contact-cta-section {
  background: #061120;
  color: #ffffff;
  padding: 80px 0;
}

.contact-cta-grid {
  display: grid;
  grid-template-columns: 1.1fr 0.9fr;
  gap: 48px;
  align-items: center;
}

.contact-cta-info h2 {
  font-size: clamp(28px, 3.5vw, 42px);
  color: #ffffff;
  margin: 12px 0 16px;
  font-family: 'Fraunces', serif;
}

.contact-cta-info p {
  color: rgba(255, 255, 255, 0.85);
  font-size: 16px;
  margin-bottom: 28px;
}

.office-location-card {
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.12);
  border-radius: 14px;
  padding: 18px;
}

.off-badge {
  display: inline-block;
  font-size: 11px;
  font-weight: 800;
  text-transform: uppercase;
  color: var(--gold);
  margin-bottom: 6px;
}

.off-badge.pk {
  color: #4ade80;
}

.office-location-card h4 {
  font-size: 16px;
  color: #ffffff;
  margin-bottom: 8px;
}

.office-location-card p {
  font-size: 13.5px;
  color: #cbd5e1;
  margin: 0 0 6px;
  display: flex;
  align-items: center;
  gap: 8px;
}

.office-location-card p i {
  color: var(--gold);
}

.contact-quick-form-card {
  background: #ffffff;
  color: var(--ink);
  border-radius: 20px;
  padding: 32px;
  box-shadow: 0 20px 50px rgba(0, 0, 0, 0.35);
}

.contact-quick-form-card h3 {
  font-size: 22px;
  color: var(--teal-dark);
  margin-bottom: 6px;
}

.contact-quick-form-card p {
  font-size: 13.5px;
  color: var(--ink-soft);
  margin-bottom: 18px;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}

.contact-quick-form-card .form-group {
  margin-bottom: 14px;
}

.contact-quick-form-card label {
  font-size: 12px;
  font-weight: 700;
  color: var(--ink-soft);
  text-transform: uppercase;
  letter-spacing: 0.05em;
  display: block;
  margin-bottom: 5px;
}

.contact-quick-form-card input,
.contact-quick-form-card select,
.contact-quick-form-card textarea {
  width: 100%;
  padding: 11px 14px;
  border-radius: 10px;
  border: 1px solid #d1d5db;
  background: #f9fafb;
  font-family: inherit;
  font-size: 14px;
  color: var(--ink);
}

.contact-quick-form-card input:focus,
.contact-quick-form-card select:focus,
.contact-quick-form-card textarea:focus {
  background: #ffffff;
  border-color: var(--gold);
  outline: none;
  box-shadow: 0 0 0 3px rgba(197, 160, 89, 0.15);
}

.inquiry-alert {
  margin-top: 14px;
  padding: 10px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
}

.inquiry-alert.success {
  background: #ecfdf5;
  color: #065f46;
  border: 1px solid #a7f3d0;
}

.inquiry-alert.error {
  background: #fef2f2;
  color: #991b1b;
  border: 1px solid #fecaca;
}

/* RESPONSIVE */
@media (max-width: 1024px) {
  .hero-inner.has-promo {
    grid-template-columns: 1fr;
  }
  .contact-cta-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 900px) {
  .services-cards-grid,
  .umrah-cards-grid,
  .destination-cards-grid,
  .why-cards-grid,
  .testimonials-grid,
  .blog-cards-grid {
    grid-template-columns: 1fr;
  }
  .visa-cards-grid {
    grid-template-columns: 1fr 1fr;
  }
}

@media (max-width: 640px) {
  .search-fields-grid {
    grid-template-columns: 1fr;
  }
  .visa-cards-grid {
    grid-template-columns: 1fr;
  }
  .form-row {
    grid-template-columns: 1fr;
  }
}
</style>
