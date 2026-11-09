import { createRouter, createWebHistory } from 'vue-router';

// Frontend
const FrontendLayout = () => import('./components/Frontend/Layout.vue');
const Home = () => import('./components/Frontend/Home.vue');
const FrontendFlights = () => import('./components/Frontend/FlightSearch.vue');
const FrontendTicketBooking = () => import('./components/Frontend/TicketBooking.vue');
const FrontendUmrahPackages = () => import('./components/Frontend/UmrahPackages.vue');
const FrontendTeam = () => import('./components/Frontend/Team.vue');
const FrontendServices = () => import('./components/Frontend/Services.vue');
const FrontendContact = () => import('./components/Frontend/Contact.vue');
const UmrahDetails = () => import('./components/Frontend/UmrahDetails.vue');
const HotelSearch = () => import('./components/Frontend/HotelSearch.vue');
const HotelDetail = () => import('./components/Frontend/HotelDetail.vue');
const BookingSuccess = () => import('./components/Frontend/BookingSuccess.vue');
const AboutUs = () => import('./components/Frontend/AboutUs.vue');
const Gallery = () => import('./components/Frontend/Gallery.vue');
const Terms = () => import('./components/Frontend/Terms.vue');
const VisaInfo = () => import('./components/Frontend/VisaInfo.vue');
const VisaDetail = () => import('./components/Frontend/VisaDetail.vue');
const VisaApplication = () => import('./components/Frontend/VisaApplication.vue');
const VisaPaymentSuccess = () => import('./components/Frontend/VisaPaymentSuccess.vue');
const Esim = () => import('./components/Frontend/Esim.vue');
const BlogList = () => import('./components/Frontend/BlogList.vue');
const BlogDetail = () => import('./components/Frontend/BlogDetail.vue');
const SpanishLanding = () => import('./components/Frontend/SpanishLanding.vue');
const NotFound = () => import('./components/Frontend/NotFound.vue');

// Admin
const AdminLayout = () => import('./components/Admin/Layout.vue');
const AdminLogin = () => import('./components/Admin/Login.vue');
const AdminDashboard = () => import('./components/Admin/Dashboard.vue');
const AdminBookings = () => import('./components/Admin/Bookings.vue');
const AdminManageListings = () => import('./components/Admin/ManageListings.vue');
const AdminSocialMedia = () => import('./components/Admin/SocialMedia.vue');
const AdminPaymentSettings = () => import('./components/Admin/PaymentSettings.vue');
const AdminSocialLinks = () => import('./components/Admin/SocialLinks.vue');
const AdminChatbot = () => import('./components/Admin/Chatbot.vue');

const routes = [
    {
        path: '/',
        component: FrontendLayout,
        children: [
            { path: '', component: Home, name: 'home' },
            { path: 'about', component: AboutUs, name: 'frontend.about' },
            { path: 'flights', component: FrontendFlights, name: 'frontend.flights' },
            { path: 'book', component: FrontendTicketBooking, name: 'frontend.book' },
            { path: 'umrah', component: FrontendUmrahPackages, name: 'frontend.umrah' },
            { path: 'umrah/:id', component: UmrahDetails, name: 'frontend.umrah.details' },
            { path: 'hotels', component: HotelSearch, name: 'frontend.hotels' },
            { path: 'hotel-detail', component: HotelDetail, name: 'frontend.hotels.detail' },
            { path: 'esim', component: Esim, name: 'frontend.esim' },
            { path: 'blog', component: BlogList, name: 'frontend.blog' },
            { path: 'blog/:slug', component: BlogDetail, name: 'frontend.blog.detail' },
            { path: 'team', component: FrontendTeam, name: 'frontend.team' },
            { path: 'services', component: FrontendServices, name: 'frontend.services' },
            { path: 'contact', component: FrontendContact, name: 'frontend.contact' },
            { path: 'gallery', component: Gallery, name: 'frontend.gallery' },
            { path: 'terms', component: Terms, name: 'frontend.terms' },
            { path: 'visa', component: VisaInfo, name: 'frontend.visa' },
            { path: 'visa/:slug', component: VisaDetail, name: 'frontend.visa.detail' },
            { path: 'visa/:slug/apply', component: VisaApplication, name: 'frontend.visa.apply' },
            { path: 'visa-payment-success', component: VisaPaymentSuccess, name: 'frontend.visa.payment_success' },
            { path: 'booking-success/:id', component: BookingSuccess, name: 'frontend.booking_success' },
            // Dedicated Spanish SEO landing pages
            { path: 'umrah-desde-espana', component: SpanishLanding, name: 'frontend.es.umrah_espana' },
            { path: 'umrah-desde-barcelona', component: SpanishLanding, name: 'frontend.es.umrah_barcelona' },
            { path: 'paquetes-umrah', component: SpanishLanding, name: 'frontend.es.paquetes_umrah' },
            { path: 'agencia-viajes-barcelona', component: SpanishLanding, name: 'frontend.es.agencia_barcelona' },
            { path: 'vuelos-internacionales', component: SpanishLanding, name: 'frontend.es.vuelos_internacionales' },
            { path: 'visados', component: SpanishLanding, name: 'frontend.es.visados' },
            { path: 'hoteles-la-meca', component: SpanishLanding, name: 'frontend.es.hoteles_meca' },
            { path: 'umrah-2027', component: SpanishLanding, name: 'frontend.es.umrah_2027' },
            { path: ':pathMatch(.*)*', component: NotFound, name: 'notfound' },
        ]
    },
    { path: '/admin/login', component: AdminLogin, name: 'admin.login' },
    { 
        path: '/admin', 
        component: AdminLayout,
        meta: { requiresAuth: true },
        children: [
            { path: '', component: AdminDashboard, name: 'admin.dashboard' },
            { path: 'bookings', component: AdminBookings, name: 'admin.bookings' },
            { path: 'umrah-packages', component: AdminManageListings, name: 'admin.manage_listings' },
            { path: 'social-media', component: AdminSocialMedia, name: 'admin.social_media' },
            { path: 'payment-settings', component: AdminPaymentSettings, name: 'admin.payment_settings' },
            { path: 'social-links', component: AdminSocialLinks, name: 'admin.social_links' },
            { path: 'chatbot', component: AdminChatbot, name: 'admin.chatbot' },
            { path: ':pathMatch(.*)*', component: NotFound, name: 'notfound.admin' },
        ]
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
    scrollBehavior(to, from, savedPosition) {
        if (savedPosition) return savedPosition;
        return { top: 0, behavior: 'smooth' };
    }
});

router.beforeEach((to, from, next) => {
    const isAuthenticated = localStorage.getItem('isAuthenticated') === 'true'
        && !!localStorage.getItem('api_token');
    if (to.meta.requiresAuth && !isAuthenticated) {
        localStorage.removeItem('isAuthenticated');
        localStorage.removeItem('admin_logged_in');
        next('/admin/login');
    } else if (to.path === '/admin/login' && isAuthenticated) {
        next('/admin');
    } else {
        next();
    }
});

export default router;
