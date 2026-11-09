import { computed, ref } from 'vue';

const storageKey = 'sangugroup-locale';
const supported = new Set(['en', 'es']);
const detectLocale = () => {
  try {
    const saved = localStorage.getItem(storageKey);
    if (supported.has(saved)) return saved;
  } catch (_) {}
  return (navigator.languages || [navigator.language || 'en'])
    .some(language => /^es(?:-|$)/i.test(language)) ? 'es' : 'en';
};

export const locale = ref(typeof navigator === 'undefined' ? 'en' : detectLocale());
export const isSpanish = computed(() => locale.value === 'es');

export function setLocale(language) {
  if (!supported.has(language)) return;
  locale.value = language;
  try { localStorage.setItem(storageKey, language); } catch (_) {}
  if (typeof document !== 'undefined') document.documentElement.lang = language;
}

const copy = {
  'Home': 'Inicio', 'Flights': 'Vuelos', 'Umrah': 'Umrah', 'All Umrah Packages': 'Todos los Paquetes Umrah',
  'General catalogue and departures': 'Catálogo general y salidas', 'Umrah from Spain': 'Umrah desde España',
  'Departures from Madrid and Barcelona': 'Salidas desde Madrid y Barcelona', 'Umrah from Barcelona (El Prat)': 'Umrah desde Barcelona (El Prat)',
  'In-person assistance in Badalona': 'Asistencia presencial en Badalona', 'Umrah Packages 2026 - 2027': 'Paquetes Umrah 2026 - 2027',
  'Budget, Family and 5★': 'Económicos, Familiares y 5★', 'Guided Group Departures (Muallim)': 'Salidas Grupales con Guía (Muallim)',
  'Accompanied groups every month': 'Grupos acompañados cada mes', 'Hotels in Mecca and Medina': 'Hoteles en La Meca y Medina',
  'Clock Tower and hotels by the Haram': 'Clock Tower y hoteles junto al Haram', 'Umrah Season 2027': 'Umrah Temporada 2027',
  'Early booking discount': 'Reserva anticipada con descuento', 'Visas': 'Visados', 'All Visas': 'Todos los Visados',
  'Requirements and applications': 'Requisitos y solicitudes', 'Visa processing in Spain': 'Gestión de Visados en España',
  'Express service in Badalona': 'Servicio express en Badalona', 'Saudi Arabia Visa (Tourism / Umrah)': 'Visado Arabia Saudí (Turismo / Umrah)',
  'Multiple-entry e-Visa valid for 1 year': 'e-Visa múltiple válida por 1 año', 'Dubai / UAE Visa': 'Visado Dubái / EAU',
  'Stays of 30 or 60 days': 'Estancias de 30 o 60 días', 'United Kingdom (UK) Visa': 'Visado Reino Unido (UK)',
  'Complete document guidance': 'Asesoría documental completa', 'Hotels': 'Hoteles', 'Barcelona Travel Agency': 'Agencia Barcelona',
  'Gallery': 'Galería', 'Blog': 'Blog', 'Contact': 'Contacto', 'Quick Quote': 'Presupuesto Rápido',
  'International Travel Agency and Umrah Specialist': 'Su Agencia de Viajes Internacional y Especialista en Umrah',
  'Your International Travel Agency and Umrah Specialist': 'Su Agencia de Viajes Internacional y Especialista en Umrah',
  'International Flights': 'Vuelos Internacionales', 'Umrah Packages': 'Paquetes Umrah', 'Hotels Mecca / World': 'Hoteles Meca / Mundo',
  'Quick offers & news': 'Ofertas & Novedades', 'Ask on WhatsApp': 'Consultar por WhatsApp',
  'Get a Free Quote': 'Pedir Presupuesto Gratis', 'Official Agency in Barcelona (Badalona) and Pakistan': 'Agencia Oficial en Barcelona (Badalona) y Pakistán',
  '15+ Years of Experience': '15+ Años de Experiencia', '10,000+ Satisfied Pilgrims': '+10.000 Peregrinos Satisfechos',
  'Support in Spanish, Urdu and English': 'Atención en Español, Urdu e Inglés', 'About Us': 'Sobre Nosotros', 'Terms and Conditions': 'Términos y Condiciones',
  'Your International Travel Agency and Umrah Specialist': 'Su Agencia de Viajes Internacional y Especialista en Umrah',
  'Official Agency in Barcelona (Badalona) and Pakistan': 'Agencia Oficial en Barcelona (Badalona) y Pakistán',
  'Best-price flights to Pakistan, Dubai and worldwide. Guaranteed Umrah packages departing from Barcelona and Madrid, 5★ hotels facing the Haram and visa processing.': 'Billetes de avión al mejor precio a Pakistán, Dubái y todo el mundo. Paquetes de Umrah garantizados con salidas desde Barcelona y Madrid, hoteles 5★ frente al Haram y tramitación de visados.',
  'Spain Umrah Packages': 'Paquetes Umrah España', 'Ask us on WhatsApp': 'Consultar por WhatsApp',
  'Integrated Travel Services': 'Servicios Integrales de Viaje', 'Umrah Packages from Spain': 'Paquetes Umrah desde España',
  'View Umrah Packages': 'Ver Paquetes Umrah', 'International travel agency': 'Agencia de viajes internacional',
  'Mon–Sat: 09:30 – 20:30 CET': 'Lun–Sáb: 09:30 – 20:30 CET', 'Direct WhatsApp': 'WhatsApp Directo',
  'Visas': 'Visados', 'eSIM': 'eSIM', 'Contact': 'Contacto', 'Travel Gallery': 'Galería',
  'Main Services': 'Servicios Principales', 'International Flights': 'Vuelos Internacionales',
  'Flights from Spain to Pakistan': 'Vuelos España a Pakistán', 'Umrah Packages': 'Paquetes Umrah',
  'Guided Group Departures': 'Salidas Grupales con Muallim', 'Visit and Tourist Visas': 'Visados de Visita y Turismo',
  'Hotel Reservations': 'Reserva de Hoteles', 'International Travel eSIM': 'eSIM de Viaje Internacional',
  'Pilgrim Gallery': 'Galería de Peregrinos', 'Spain & Umrah': 'España & Umrah',
  'Umrah from Barcelona (BCN)': 'Umrah desde Barcelona (BCN)', 'Umrah Packages Catalogue': 'Catálogo Paquetes Umrah',
  'Badalona Travel Agency': 'Agencia de Viajes Badalona', 'Hotels in Mecca and Medina': 'Hoteles en La Meca y Medina',
  'Visa Processing': 'Tramitación de Visados', 'Early Bird Umrah 2027 Booking': 'Reserva Anticipada Umrah 2027',
  'Travel Blog & Tips': 'Blog & Consejos de Viaje', 'Offices & Contact': 'Oficinas & Contacto',
  'Barcelona Office (Badalona)': 'Oficina Barcelona (Badalona)', 'Pakistan Office (24/7)': 'Oficina Pakistán (24/7)',
  'Monday to Saturday, 9:30 AM – 8:30 PM CET': 'Lunes a Sábado, 9:30h – 20:30h CET',
  'All rights reserved.': 'Todos los derechos reservados.', 'About Us': 'Sobre Nosotros',
  'Terms and Conditions': 'Términos y Condiciones', 'Safe Payment': 'Pago Seguro', 'Official Agency': 'Agencia Oficial',
  '15+ Years Exp.': '15+ Años Exp.', 'Atención Directa WhatsApp': 'Atención Directa WhatsApp',
  'Agents available online': 'Agentes disponibles en línea', 'Select the office you would like to contact:': 'Seleccione la oficina con la que desea comunicarse:',
  'Spain Office (Badalona / Barcelona)': 'Oficina España (Badalona / Barcelona)',
  'Support for Tickets and Pilgrims': 'Soporte para Billetes y Peregrinos', 'Quick message:': 'Mensaje rápido:',
  'Umrah Packages': 'Paquetes Umrah', 'Cheap Flights': 'Vuelos Baratos', 'Process Visa': 'Tramitar Visado',
  'Mecca Hotels': 'Hoteles La Meca', 'Open WhatsApp chat': 'Abrir chat de WhatsApp',
};

const spanishToEnglish = Object.fromEntries(Object.entries(copy).map(([english, spanish]) => [spanish, english]));

export function t(spanish) {
  return locale.value === 'es' ? spanish : (spanishToEnglish[spanish] || spanish);
}
