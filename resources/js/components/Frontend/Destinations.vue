<template>
    <div class="bg-white selection:bg-primary/10">
        <!-- Hero Section -->
        <section class="page-hero">
            <div class="page-hero-bg">
                <img src="https://images.unsplash.com/photo-1488646953014-85cb44e25828?w=1920&q=90" alt="Destinations">
                <div class="page-hero-shade"></div>
            </div>
            <div class="page-hero-content text-center md:text-left">
                <nav class="page-breadcrumb justify-center md:justify-start">
                    <router-link to="/">Home</router-link>
                    <span class="sep"></span>
                    <span class="current">Destinations</span>
                </nav>
                <div class="page-hero-badge">
                    <i class="fas fa-globe-asia"></i>
                    <span>Global Travel Network</span>
                </div>
                <h1 class="page-hero-title">Explore <em>Destinations</em></h1>
                <p class="page-hero-sub mx-auto md:mx-0">
                    Discover handpicked destinations across the globe — from iconic cities to serene escapes, we bring the world to you.
                </p>
            </div>
        </section>

        <!-- Featured Destinations -->
        <section class="py-24 lg:py-32">
            <div class="container mx-auto px-6">
                <div class="flex flex-col md:flex-row md:items-end justify-between mb-16 gap-6">
                    <div class="space-y-3">
                        <h6 class="section-eyebrow"><i class="fas fa-star"></i> Featured Selection</h6>
                        <h2 class="section-title">Top <em>Destinations</em></h2>
                    </div>
                    <router-link to="/contact" class="btn-premium-outline !px-8 !py-3.5">
                        Plan Your Trip <i class="fas fa-arrow-right ml-2 text-xs"></i>
                    </router-link>
                </div>

                <div v-if="loading" class="flex flex-col items-center justify-center p-32 space-y-8 bg-slate-50 rounded-[3rem] border border-slate-100">
                    <div class="w-20 h-20 border-2 border-slate-100 border-t-secondary rounded-full animate-spin"></div>
                    <p class="text-xs font-black uppercase tracking-[0.4em] text-slate-400 animate-pulse">Loading Destinations...</p>
                </div>

                <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                    <div v-for="dest in featuredDestinations" :key="dest.id" class="group relative h-[520px] rounded-[3rem] overflow-hidden shadow-premium hover:shadow-premium-lg transition-all duration-700 border border-slate-100">
                        <img :src="dest.image_path" :alt="dest.name" class="w-full h-full object-cover transition-transform duration-[3s] group-hover:scale-110">
                        <div class="absolute inset-0 bg-gradient-to-t from-accent/95 via-accent/25 to-transparent"></div>

                        <div class="absolute top-8 left-8">
                            <span class="px-4 py-2 bg-white/10 backdrop-blur-xl text-white text-[11px] font-black uppercase tracking-widest rounded-full border border-white/20">Special Offer</span>
                        </div>

                        <div class="absolute bottom-10 left-10 right-10 space-y-5">
                            <div class="text-xs font-black text-secondary uppercase tracking-[0.2em] flex items-center gap-3">
                                <span class="w-8 h-[2px] bg-secondary"></span> {{ dest.country }}
                            </div>
                            <h3 class="text-4xl font-display font-black text-white leading-tight">{{ dest.name }}</h3>
                            <p class="text-sm text-white/70 font-medium leading-relaxed line-clamp-2 opacity-0 group-hover:opacity-100 translate-y-3 group-hover:translate-y-0 transition-all duration-700">{{ dest.description }}</p>
                            <router-link :to="{ name: 'frontend.destinations.show', params: { slug: dest.slug }}" class="inline-flex items-center gap-3 text-xs font-black text-white uppercase tracking-[0.2em] pt-3 hover:text-secondary transition-colors">
                                View Details
                                <span class="w-10 h-10 rounded-full bg-white/10 border border-white/20 flex items-center justify-center group-hover:bg-secondary group-hover:text-accent transition-all"><i class="fas fa-arrow-right text-xs"></i></span>
                            </router-link>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- All Destinations -->
        <section class="py-24 lg:py-32 bg-surface-soft">
            <div class="container mx-auto px-6">
                <div class="text-center max-w-2xl mx-auto mb-20 space-y-4">
                    <h6 class="section-eyebrow"><i class="fas fa-th-large"></i> Destination Archive</h6>
                    <h2 class="section-title section-title-center">All <em>Destinations</em></h2>
                    <p class="section-intro">Browse our full collection and find the destination that speaks to you.</p>
                </div>

                <div v-if="loading" class="flex flex-col items-center justify-center p-32 space-y-8 bg-white rounded-[3rem] border border-slate-100">
                    <div class="w-20 h-20 border-2 border-slate-100 border-t-secondary rounded-full animate-spin"></div>
                    <p class="text-xs font-black uppercase tracking-[0.4em] text-slate-400 animate-pulse">Loading Destinations...</p>
                </div>

                <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div v-for="dest in destinations" :key="dest.id" class="group bg-white rounded-[3rem] border border-slate-100 shadow-premium hover:shadow-premium-lg transition-all duration-700 overflow-hidden flex flex-col">
                        <div class="relative h-64 overflow-hidden shrink-0">
                            <img :src="dest.image_path" :alt="dest.name" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                            <div class="absolute inset-0 bg-gradient-to-t from-accent/60 to-transparent"></div>
                            <div class="absolute bottom-5 left-5">
                                <span class="px-3 py-1.5 bg-accent text-white text-[10px] font-black uppercase tracking-widest rounded-xl shadow-lg">{{ dest.country }}</span>
                            </div>
                        </div>
                        <div class="p-8 flex-grow flex flex-col">
                            <h4 class="text-xl font-display font-black text-accent mb-3 group-hover:text-primary transition-colors">{{ dest.name }}</h4>
                            <p class="text-sm text-text-muted font-medium leading-relaxed line-clamp-2 mb-8">{{ dest.description }}</p>
                            <router-link :to="{ name: 'frontend.destinations.show', params: { slug: dest.slug }}" class="mt-auto inline-flex items-center gap-3 text-[11px] font-black uppercase tracking-widest text-text-muted group-hover:text-primary transition-colors">
                                Explore Details <i class="fas fa-arrow-right text-xs"></i>
                            </router-link>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="py-24 lg:py-32 px-6">
            <div class="container mx-auto">
                <div class="bg-secondary rounded-[4rem] p-16 lg:p-24 relative overflow-hidden group shadow-premium-lg">
                    <div class="absolute -top-40 -right-40 w-[600px] h-[600px] bg-white/10 rounded-full blur-[120px] group-hover:bg-white/20 transition-all duration-[2s]"></div>
                    <div class="relative z-10 text-center space-y-8">
                        <div class="space-y-4">
                            <h6 class="text-white font-black tracking-[0.4em] uppercase text-[11px]">Travel Support</h6>
                            <h2 class="text-4xl md:text-6xl font-display font-black text-white leading-tight">Plan Your <em class="italic">Trip</em></h2>
                        </div>
                        <p class="text-white/80 text-lg font-medium max-w-2xl mx-auto leading-relaxed">
                            Our travel experts can curate specialized itineraries for any destination on the map.
                        </p>
                        <div class="flex flex-col sm:flex-row items-center justify-center gap-8 pt-4">
                            <router-link to="/contact" class="btn-premium-primary !bg-accent !text-white !px-14 !py-5 !rounded-full">
                                Request Itinerary <i class="fas fa-arrow-right ml-2"></i>
                            </router-link>
                            <a href="tel:+923085093631" class="flex flex-col items-center text-white hover:text-accent transition-colors">
                                <span class="text-[10px] font-black text-white/60 uppercase tracking-[0.3em] mb-1">Direct Support</span>
                                <span class="font-display font-black text-3xl tracking-tight">03085093631</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';

const destinations = ref([]);
const loading = ref(true);

const fetchDestinations = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/api/destinations');
        destinations.value = response.data;
        if (destinations.value.length === 0) {
            destinations.value = [
                { id: 1, name: 'Santorini', slug: 'santorini', country: 'Greece', image_path: 'https://images.unsplash.com/photo-1570077188670-e3a8d69ac5ff?w=800&q=80', description: 'Beautiful blue-domed villages and stunning sunset views over the Aegean Sea.', is_featured: true },
                { id: 2, name: 'Dubai', slug: 'dubai', country: 'UAE', image_path: 'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=800&q=80', description: 'Ultra-modern architecture, luxury shopping, and unforgettable desert adventures.', is_featured: true },
                { id: 3, name: 'Swiss Alps', slug: 'swiss-alps', country: 'Switzerland', image_path: 'https://images.unsplash.com/photo-1531310197839-ccf54634509e?w=800&q=80', description: 'High-altitude retreats, scenic rail journeys, and breathtaking mountain landscapes.', is_featured: true },
                { id: 4, name: 'Kyoto', slug: 'kyoto', country: 'Japan', image_path: 'https://images.unsplash.com/photo-1493976040374-85c8e12f0c0e?w=800&q=80', description: 'Ancient temples, traditional tea ceremonies, and serene cherry blossom parks.', is_featured: false },
                { id: 5, name: 'Amalfi Coast', slug: 'amalfi-coast', country: 'Italy', image_path: 'https://images.unsplash.com/photo-1633321088355-d0f81134ca3b?w=800&q=80', description: 'Dramatic coastal cliffs, charming villages, and world-class Mediterranean dining.', is_featured: false },
                { id: 6, name: 'Maldives', slug: 'maldives', country: 'South Asia', image_path: 'https://images.unsplash.com/photo-1514282401047-d79a71a590e8?w=800&q=80', description: 'Crystal-clear lagoons, over-water villas, and untouched tropical beaches.', is_featured: false },
                { id: 7, name: 'Petra', slug: 'petra-jordan', country: 'Jordan', image_path: 'https://images.unsplash.com/photo-1579606035123-5e92150824b2?w=800&q=80', description: 'An ancient city carved into rose-colored rock and one of the world wonders.', is_featured: false },
                { id: 8, name: 'Bora Bora', slug: 'bora-bora', country: 'French Polynesia', image_path: 'https://images.unsplash.com/photo-1532408840539-d3dc041910d7?w=800&q=80', description: 'Turquoise lagoons, over-water bungalows, and the ultimate island escape.', is_featured: false },
            ];
        }
    } catch (error) {
        console.error("Destinations Error:", error);
    } finally {
        setTimeout(() => {
            loading.value = false;
        }, 800);
    }
}

const featuredDestinations = computed(() => {
    return destinations.value.filter(d => d.is_featured).slice(0, 3);
});

onMounted(() => {
    fetchDestinations();
});
</script>
