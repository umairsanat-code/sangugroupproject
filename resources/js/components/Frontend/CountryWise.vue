<template>
    <div class="min-h-screen bg-slate-50 selection:bg-secondary/30">
        <!-- Destinations Hero -->
        <div class="relative h-[550px] flex items-center justify-center overflow-hidden">
            <!-- Background Visuals -->
            <div class="absolute inset-0 z-0 scale-105">
                <img src="https://images.unsplash.com/photo-1488646953014-85cb44e25828?w=1920&q=90" alt="World Map" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-b from-primary-dark/40 via-primary/40 to-slate-50"></div>
            </div>
            
            <div class="container mx-auto px-6 relative z-10 text-center animate-reveal">
                <div class="inline-flex items-center gap-4 px-6 py-3 bg-white/10 backdrop-blur-2xl rounded-2xl border border-white/10 mb-10">
                    <i class="fas fa-globe text-secondary"></i>
                    <span class="text-white text-[11px] font-black uppercase tracking-[0.4em]">Global Travel Network</span>
                </div>
                <h1 class="text-7xl md:text-[9rem] font-display font-black text-white mb-8 tracking-tighter leading-[0.85]">
                    Top <br>
                    <span class="text-secondary tracking-tight italic">Destinations.</span>
                </h1>
                <p class="text-white/50 text-xl font-medium max-w-2xl mx-auto mb-12">Handpicked locations across the world's most exclusive destinations.</p>
                <div class="flex items-center justify-center gap-6 text-white/40 font-black uppercase tracking-[0.3em] text-[11px]">
                    <router-link to="/" class="hover:text-secondary transition-colors">Home</router-link>
                    <span class="w-2 h-[1px] bg-white/20"></span>
                    <span class="text-white">Discover More</span>
                </div>
            </div>
        </div>

        <!-- Search & Filter Area -->
        <div class="container mx-auto px-6 -mt-24 relative z-20">
            <div class="glass-panel p-4 rounded-[3rem] shadow-premium-lg border border-white/10 animate-fade-in-up">
                <div class="bg-slate-900/40 rounded-[2.5rem] p-8">
                    <form @submit.prevent="handleSearch" class="grid grid-cols-1 md:grid-cols-12 gap-8 items-end">
                        <div class="md:col-span-4 space-y-3">
                            <label class="text-[10px] font-black text-white/30 uppercase tracking-[0.2em] ml-2 font-display">Destination Country</label>
                            <select v-model="filters.country" class="input-premium-dark appearance-none">
                                <option value="">All Countries</option>
                                <option>United Arab Emirates</option>
                                <option>Saudi Arabia</option>
                                <option>France</option>
                                <option>Turkey</option>
                            </select>
                        </div>
                        <div class="md:col-span-5 space-y-3">
                            <label class="text-[10px] font-black text-white/30 uppercase tracking-[0.2em] ml-2 font-display">City or Region</label>
                            <input type="text" v-model="filters.search" class="input-premium-dark" placeholder="Enter city or landmark">
                        </div>
                        <div class="md:col-span-3 pt-2">
                            <button type="submit" class="btn-premium-secondary w-full h-[64px] gap-3 font-black text-[11px] shadow-secondary/20 transition-all active:scale-95">
                                Search Now <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Main Results Section -->
        <section class="section-container relative z-10">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12">
                <div v-for="dest in filteredDestinations" :key="dest.id" class="group relative h-[600px] rounded-[4rem] overflow-hidden shadow-premium-sm hover:shadow-premium-lg transition-all duration-[1s] border border-slate-100 flex flex-col">
                    <div class="relative h-2/3 overflow-hidden">
                        <img :src="dest.image" :alt="dest.name" class="w-full h-full object-cover transition-transform duration-[3s] group-hover:scale-110">
                        <div class="absolute inset-0 bg-gradient-to-t from-primary-dark/95 via-primary-dark/20 to-transparent opacity-60"></div>
                        <div class="absolute top-8 left-8 flex flex-col gap-2">
                            <span class="px-5 py-2 bg-white/10 backdrop-blur-2xl text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-2xl border border-white/10">{{ dest.duration }}</span>
                            <span class="px-5 py-2 bg-secondary text-primary-dark text-[10px] font-black uppercase tracking-[0.2em] rounded-2xl shadow-xl">{{ dest.country }}</span>
                        </div>
                    </div>
                    
                    <div class="p-12 flex-grow bg-white flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-1 text-secondary text-xs mb-4">
                                <i v-for="n in 5" :key="n" class="fas fa-star"></i>
                            </div>
                            <h3 class="text-4xl font-display font-black text-primary-dark tracking-tighter group-hover:text-secondary transition-colors mb-4">{{ dest.name }}</h3>
                            <p class="text-sm text-slate-400 font-medium leading-relaxed line-clamp-2">{{ dest.description }}</p>
                        </div>
                        
                        <div class="flex items-center justify-between pt-8 border-t border-slate-50 mt-8">
                            <div class="flex flex-col">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] mb-1">Price</span>
                                <div class="text-2xl font-display font-black text-primary-dark tracking-tighter">Competitive</div>
                            </div>
                            <router-link to="/contact" class="w-14 h-14 rounded-full bg-slate-50 flex items-center justify-center text-primary-dark hover:bg-secondary hover:text-white transition-all">
                                <i class="fas fa-arrow-right"></i>
                            </router-link>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Dynamic CTA -->
        <section class="py-32 px-6">
            <div class="container mx-auto">
                <div class="bg-primary-dark rounded-[4rem] p-20 md:p-32 relative overflow-hidden group shadow-premium-lg border border-white/5">
                    <div class="absolute -top-40 -right-40 w-[600px] h-[600px] bg-secondary/10 rounded-full blur-[120px] group-hover:bg-secondary/20 transition-all duration-[2s]"></div>

                    <div class="relative z-10 text-center space-y-12">
                        <div class="space-y-4">
                            <h6 class="text-secondary font-black tracking-[0.4em] uppercase text-[11px]">Need Help?</h6>
                            <h2 class="text-6xl md:text-8xl font-display font-black text-white leading-tight tracking-tighter">Plan Your Journey.</h2>
                        </div>
                        <p class="text-white/40 text-xl font-medium max-w-2xl mx-auto leading-relaxed">Our travel experts can curate specialized itineraries for any destination or spiritual journey.</p>

                        <div class="flex flex-col sm:flex-row items-center justify-center gap-8 pt-8">
                            <router-link to="/contact" class="btn-premium-primary !px-16 !py-6 !rounded-[2rem] font-black text-[11px]">Request Itinerary <i class="fas fa-arrow-right ml-3"></i></router-link>
                            <span class="text-white/30 font-black uppercase tracking-[0.3em] text-[11px]">Active Support: 24/7/365</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const filters = ref({
    country: '',
    search: ''
});

const destinations = ref([
    {
        id: 1,
        name: 'Dubai Luxury Experience',
        country: 'United Arab Emirates',
        duration: '5 Days',
        price: '1,450',
        image: 'https://images.unsplash.com/photo-1548013146-72479768bada?w=800&q=80',
        description: 'Experience the absolute pinnacle of luxury in Dubai with desert safaris and premium city tours.'
    },
    {
        id: 2,
        name: 'Paris Romantic Escape',
        country: 'France',
        duration: '7 Days',
        price: '2,100',
        image: 'https://images.unsplash.com/photo-1502602898657-3e91760cbb34?w=800&q=80',
        description: 'A romantic journey through the streets of Paris and the Eiffel Tower area.'
    },
    {
        id: 3,
        name: 'Istanbul Heritage Tour',
        country: 'Turkey',
        duration: '6 Days',
        price: '1,150',
        image: 'https://images.unsplash.com/photo-1524231757912-21f4fe3a7200?w=800&q=80',
        description: 'Explore the rich historical legacy and vibrant culture of the Ottoman capital.'
    },
    {
        id: 4,
        name: 'London Royal Experience',
        country: 'United Kingdom',
        duration: '5 Days',
        price: '1,850',
        image: 'https://images.unsplash.com/photo-1513635269975-59663e0ac1ad?w=800&q=80',
        description: 'High-tier urban exploration of the British capital with exclusive access.'
    },
    {
        id: 5,
        name: 'Mecca Spiritual Journey',
        country: 'Saudi Arabia',
        duration: '14 Days',
        price: '2,450',
        image: 'https://images.unsplash.com/photo-1591604129939-f1efa4d9f7fa?w=800&q=80',
        description: 'Premium spiritual experience with Clock Tower accommodations and dedicated assistance.'
    },
    {
        id: 6,
        name: 'Maldives Private Resort',
        country: 'Maldives',
        duration: '5 Days',
        price: '3,200',
        image: 'https://images.unsplash.com/photo-1514282401047-d79a71a590e8?w=800&q=80',
        description: 'Ultra-exclusive over-water villas with private transfers and oceanic serenity.'
    }
]);

const filteredDestinations = computed(() => {
    return destinations.value.filter(dest => {
        const matchCountry = !filters.value.country || dest.country === filters.value.country;
        const matchSearch = !filters.value.search || 
            dest.name.toLowerCase().includes(filters.value.search.toLowerCase()) ||
            dest.description.toLowerCase().includes(filters.value.search.toLowerCase());
        return matchCountry && matchSearch;
    });
});

const handleSearch = () => {
    // Logic already handled by computed property
};
</script>

<style scoped>
@keyframes reveal {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-reveal {
    animation: reveal 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
.animate-fade-in-up {
    animation: reveal 0.7s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
</style>
