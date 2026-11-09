<template>
    <div class="bg-white selection:bg-primary/10">
        <!-- Flight Search Hero Section -->
        <section class="page-hero">
            <div class="page-hero-bg">
                <img
                    src="/images/travivu_flights_banner_1775155094158.png"
                    alt="Flight Banner"
                >
                <div class="page-hero-shade"></div>
            </div>

            <div class="page-hero-content">
                <nav class="page-breadcrumb">
                    <router-link to="/">Home</router-link>
                    <span class="sep"></span>
                    <span class="current">Flights</span>
                </nav>
                <h1 class="page-hero-title">Search <em>Flights</em></h1>
                <p class="page-hero-sub">
                    Find the best deals on flights worldwide. Your next destination is just a click away.
                </p>
            </div>
        </section>

        <!-- Search Flights -->
        <div class="container mx-auto px-6 -mt-20 relative z-20">
            <div class="bg-white p-4 rounded-[2.5rem] shadow-2xl border border-slate-100 transition-all hover:shadow-primary/5">
                <div class="bg-surface-soft p-8 rounded-[2rem]">
                    <form @submit.prevent="fetchFlights" class="grid grid-cols-1 md:grid-cols-12 gap-6 items-end">
                        <div class="md:col-span-3">
                            <BaseInput label="From" v-model="searchDate.from" placeholder="Origin City" />
                        </div>
                        <div class="md:col-span-3">
                            <BaseInput label="To" v-model="searchDate.to" placeholder="Destination City" />
                        </div>
                        <div class="md:col-span-3">
                            <BaseInput label="Departure" type="date" v-model="searchDate.date" />
                        </div>
                        <div class="md:col-span-3">
                            <button type="submit" class="btn-premium-primary !h-[54px] w-full font-black text-xs uppercase tracking-widest">
                                Search Flights <i class="fas fa-search ml-2"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <section class="py-24 bg-white">
            <div class="container mx-auto px-6">
                <div class="flex flex-col lg:flex-row gap-12">
                    <!-- Filters Sidebar -->
                    <aside class="w-full lg:w-80 space-y-8">
                        <div class="p-8 bg-white border border-border rounded-[2rem] shadow-sm sticky top-32">
                            <div class="flex items-center justify-between mb-8 pb-4 border-b border-slate-50">
                                <h3 class="text-sm font-black uppercase tracking-widest text-text-dark">Filters</h3>
                                <button @click="resetFilters" class="text-[10px] font-bold text-text-muted hover:text-primary transition-colors">Reset All</button>
                            </div>

                            <div class="space-y-10">

                                <!-- Stops Filter -->
                                <div class="space-y-6 pt-4 border-t border-slate-50">
                                    <h4 class="text-[11px] font-black uppercase tracking-[0.2em] text-text-muted">Stops</h4>
                                    <div class="space-y-4">
                                        <label v-for="stop in ['Non-stop', '1 Stop', '2+ Stops']" :key="stop" class="flex items-center gap-4 text-xs font-bold text-text-dark cursor-pointer group">
                                            <input type="checkbox" class="w-5 h-5 rounded-lg border-border text-primary focus:ring-primary/20 transition-all">
                                            <span class="group-hover:text-primary transition-colors">{{ stop }}</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Airlines Filter -->
                                <div class="space-y-6 pt-4 border-t border-slate-50">
                                    <h4 class="text-[11px] font-black uppercase tracking-[0.2em] text-text-muted">Airlines</h4>
                                    <div class="space-y-4">
                                        <label v-for="airline in ['Emirates', 'Qatar Airways', 'PIA', 'Saudia']" :key="airline" class="flex items-center gap-4 text-xs font-bold text-text-dark cursor-pointer group">
                                            <input type="checkbox" class="w-5 h-5 rounded-lg border-border text-primary focus:ring-primary/20 transition-all">
                                            <span class="group-hover:text-primary transition-colors">{{ airline }}</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </aside>

                        <!-- Results Area -->
                        <main class="flex-grow space-y-10">
                            <!-- Search Toolbar -->
                            <div class="flex flex-col sm:flex-row items-center justify-between gap-6 px-8 py-5 bg-white rounded-3xl border border-slate-100 shadow-premium-sm">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-primary/5 flex items-center justify-center text-primary">
                                        <i class="fas fa-plane text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest">Search Results</p>
                                        <p class="text-xs font-black text-primary-dark uppercase">{{ flights.length }} Flights Found</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-6">
                                    <span class="text-[11px] font-black uppercase text-slate-400 tracking-[0.2em]">Sort By:</span>
                                    <select class="text-xs font-black bg-slate-50 border-none rounded-xl px-5 py-2.5 focus:ring-secondary/20 text-primary-dark cursor-pointer uppercase tracking-widest">
                                        <option>Best Match</option>
                                        <option>Price: Low to High</option>
                                        <option>Time: Earliest</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Results Feed -->
                            <div v-if="loading" class="flex flex-col items-center justify-center py-40 gap-8 bg-white border border-slate-100 rounded-[3rem] shadow-premium-sm">
                                <div class="w-20 h-20 border-2 border-slate-100 border-t-secondary rounded-full animate-spin"></div>
                                <p class="text-xs font-black uppercase tracking-[0.4em] text-slate-400 animate-pulse">Searching for the best flights...</p>
                            </div>

                            <div v-else-if="flights.length === 0" class="text-center py-40 bg-white border border-slate-100 rounded-[3rem] shadow-premium-sm">
                                <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-8 text-slate-200 transform -rotate-12">
                                    <i class="fas fa-plane-slash text-4xl"></i>
                                </div>
                                <h3 class="text-4xl font-display font-black text-primary-dark mb-4">No Flights Found</h3>
                                <p class="text-slate-400 font-medium max-w-sm mx-auto mb-10 leading-relaxed">We couldn't find any flights matching your search criteria at this time.</p>
                                <button @click="resetFilters" class="btn-premium-outline !rounded-2xl !px-12">Search Again</button>
                            </div>

                            <div v-else class="space-y-8">
                                <div v-for="flight in flights" :key="flight.id" class="group relative bg-white rounded-[3rem] border border-slate-100 overflow-hidden hover:shadow-premium-lg hover:-translate-y-1 transition-all duration-700">
                                    <div class="absolute top-0 left-0 w-2 h-full bg-primary group-hover:bg-secondary transition-colors"></div>

                                    <div class="flex flex-col xl:flex-row">
                                        <!-- Flight Details -->
                                        <div class="flex-grow p-10">
                                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-10 mb-12">
                                                <div class="flex items-center gap-6">
                                                    <div class="w-16 h-16 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center text-primary text-3xl group-hover:bg-primary group-hover:text-white transition-all transform group-hover:rotate-12">
                                                        <i class="fas fa-plane-arrival"></i>
                                                    </div>
                                                    <div>
                                                        <h4 class="text-2xl font-display font-black text-primary-dark tracking-tighter">{{ flight.airline?.name || 'SanguGlobal Airways' }}</h4>
                                                        <div class="flex items-center gap-4 mt-2">
                                                            <span class="px-3 py-1 bg-slate-100 rounded-lg text-[11px] font-black text-primary-dark uppercase tracking-widest">Flight: {{ flight.flight_number }}</span>
                                                            <span class="w-1 h-1 rounded-full bg-slate-200"></span>
                                                            <span class="text-[11px] font-black text-slate-400 uppercase tracking-widest">Economy Class</span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="flex items-center gap-3 bg-secondary/10 px-5 py-2.5 rounded-2xl border border-secondary/10">
                                                    <span class="w-2 h-2 rounded-full bg-secondary animate-pulse"></span>
                                                    <span class="text-[11px] font-black text-secondary uppercase tracking-[0.15em]">Available Now</span>
                                                </div>
                                            </div>

                                            <!-- Route -->
                                            <div class="grid grid-cols-1 md:grid-cols-7 items-center gap-8 text-center relative">
                                                <div class="md:col-span-2 space-y-2">
                                                    <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest">Departure</p>
                                                    <div class="text-4xl font-display font-black text-primary-dark tracking-tighter">{{ formatTime(flight.departure_time) }}</div>
                                                    <div class="text-xs font-black text-primary uppercase tracking-tight">{{ flight.departure_city || 'KWI' }}</div>
                                                </div>

                                                <div class="md:col-span-3 px-10 relative">
                                                    <div class="text-[11px] font-black text-primary-dark uppercase tracking-[0.25em] mb-6 bg-slate-50 py-2 px-4 rounded-xl w-fit mx-auto border border-slate-100">
                                                        {{ flight.duration || '06H 30M' }}
                                                    </div>
                                                    <div class="relative h-[2px] bg-slate-100 flex items-center justify-between">
                                                        <div class="w-3 h-3 rounded-full bg-white border-4 border-primary"></div>
                                                        <div class="absolute left-1/2 -translate-x-1/2 text-primary/20 text-2xl transform group-hover:translate-x-20 transition-transform duration-[4s]">
                                                            <i class="fas fa-plane"></i>
                                                        </div>
                                                        <div class="w-3 h-3 rounded-full bg-white border-4 border-secondary"></div>
                                                    </div>
                                                    <div class="text-[11px] font-black text-slate-300 uppercase tracking-[0.35em] mt-6">
                                                        Best Rate Guaranteed
                                                    </div>
                                                </div>

                                                <div class="md:col-span-2 space-y-2">
                                                    <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest">Arrival</p>
                                                    <div class="text-4xl font-display font-black text-primary-dark tracking-tighter">{{ formatTime(flight.arrival_time) }}</div>
                                                    <div class="text-xs font-black text-secondary uppercase tracking-tight">{{ flight.arrival_city || 'DXB' }}</div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="w-full xl:w-80 p-12 bg-slate-50 xl:border-l border-slate-100 flex flex-col justify-center items-center gap-10 group-hover:bg-white transition-colors">
                                            <div class="text-center space-y-2">
                                                <span class="text-[11px] font-black text-slate-400 uppercase tracking-[0.3em] block">Rates</span>
                                                <div class="text-3xl font-display font-black text-primary-dark tracking-tighter">
                                                    Competitive
                                                </div>
                                            </div>
                                            <button @click="bookFlight(flight)" class="group/btn relative overflow-hidden btn-premium-primary w-full !h-16 !rounded-2xl shadow-premium-sm">
                                                <span class="relative z-10 flex items-center justify-center gap-3">
                                                    Book This Flight
                                                    <i class="fas fa-arrow-right text-xs group-hover/btn:translate-x-1 transition-transform"></i>
                                                </span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </main>
                </div>
            </div>
        </section>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import axios from 'axios';
import BaseInput from '../Base/BaseInput.vue';

const router = useRouter();
const route = useRoute();

const flights = ref([]);
const loading = ref(true);
const searchDate = ref({
    from: route.query.from || '',
    to: route.query.to || '',
    date: route.query.date || '',
});
const baseUrl = ref('');
// Vite automatically handles base URL
if (typeof import.meta !== 'undefined' && import.meta.env) {
    baseUrl.value = import.meta.env.BASE_URL || '';
}
const fetchFlights = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/api/flights/search', {
            params: searchDate.value,
            timeout: 8000
        });
        flights.value = response.data;
    } catch (error) {
        console.error("Flight Search Error:", error);
        // Add dummy data if API fails for demo
        flights.value = [
            { id: 1, flight_number: 'EK202', price: '850', departure_city: 'NYC', arrival_city: 'DXB', departure_time: '2026-04-10 23:00', arrival_time: '2026-04-11 20:30', duration: '14h 30m', airline: { name: 'Emirates' } },
            { id: 2, flight_number: 'QR704', price: '920', departure_city: 'NYC', arrival_city: 'DOH', departure_time: '2026-04-10 21:00', arrival_time: '2026-04-11 16:15', duration: '12h 15m', airline: { name: 'Qatar Airways' } }
        ];
    } finally {
        setTimeout(() => {
            loading.value = false;
        }, 1200);
    }
}

const formatTime = (datetime) => {
    if (!datetime) return '00:00';
    return new Date(datetime).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: false });
}

const bookFlight = (flight) => {
    router.push({ path: '/book', query: { flight_id: flight.id } });
}

const resetFilters = () => {
    searchDate.value = { from: '', to: '', date: '' };
    fetchFlights();
}

onMounted(() => {
    fetchFlights();
});
</script>

<style scoped>
@keyframes reveal {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
