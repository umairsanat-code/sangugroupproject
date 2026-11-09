<template>
    <div class="min-h-screen bg-slate-50 selection:bg-secondary/30">
        <!-- Loading State -->
        <div v-if="loading" class="h-screen flex flex-col items-center justify-center space-y-8">
            <div class="w-20 h-20 border-2 border-slate-100 border-t-secondary rounded-full animate-spin"></div>
            <p class="text-[13px] font-black uppercase tracking-[0.3em] text-slate-400">
                Loading Destination Details...
            </p>
        </div>

        <!-- Destination Content -->
        <div v-else-if="dest">
            <!-- Immersive Header -->
            <section class="relative h-screen flex items-end overflow-hidden">
                <!-- Cinema Visual -->
                <div class="absolute inset-0 z-0">
                    <img :src="dest.image_path" class="w-full h-full object-cover scale-105 transition-transform duration-[10s] hover:scale-100" />
                    <div class="absolute inset-0 bg-gradient-to-t from-primary-dark/60 via-primary-dark/20 to-transparent"></div>
                </div>

                <div class="container mx-auto px-6 relative z-10 pb-40 animate-reveal">
                    <div class="flex items-center gap-6 mb-12">
                        <div class="px-6 py-2.5 bg-secondary text-primary-dark text-[10px] font-black uppercase tracking-[0.4em] rounded-2xl shadow-2xl shadow-secondary/20">
                            {{ dest.country }}
                        </div>
                        <div class="w-12 h-[1px] bg-white/20"></div>
                        <span class="text-white/40 text-[10px] font-black uppercase tracking-[0.5em]">
                            Global Travel Network
                        </span>
                    </div>

                    <h1 class="text-7xl md:text-[12rem] font-display font-black text-white leading-[0.85] tracking-tighter mb-12">
                        {{ dest.name?.split(' ')[0] }}<br />
                        <span class="text-secondary italic">{{ dest.name?.split(' ').slice(1).join(' ') || 'Experience' }}.</span>
                    </h1>

                    <p class="text-2xl text-white/50 max-w-3xl font-medium leading-relaxed mb-12">
                        {{ dest.description }}
                    </p>

                    <div class="flex items-center gap-12 pt-8 border-t border-white/10 max-w-xl">
                        <div class="space-y-1">
                            <p class="text-[10px] font-black text-secondary tracking-[0.3em] uppercase">Travel Quality</p>
                            <p class="text-white font-black text-sm uppercase tracking-widest">Premium Guaranteed</p>
                        </div>
                        <div class="w-[1px] h-12 bg-white/10"></div>
                        <div class="space-y-1">
                            <p class="text-[10px] font-black text-secondary tracking-[0.3em] uppercase">Support Status</p>
                            <p class="text-white font-black text-sm uppercase tracking-widest">24/7 Available</p>
                        </div>
                    </div>
                </div>

                <!-- Scroll Logic Hint -->
                <div class="absolute bottom-12 left-1/2 -translate-x-1/2 flex flex-col items-center gap-4 group cursor-pointer animate-reveal delay-300">
                    <div class="w-px h-16 bg-gradient-to-b from-white/0 via-secondary to-white/0 group-hover:h-24 transition-all duration-700"></div>
                    <span class="text-[10px] font-black text-white uppercase tracking-[0.4em] group-hover:text-secondary transition-colors">
                        Scroll To Explore
                    </span>
                </div>
            </section>

            <!-- Main Content Area -->
            <div class="container mx-auto px-6 py-32">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-24">
                    <!-- Left: Details & Itinerary -->
                    <div class="lg:col-span-8 space-y-24">
                        <div class="space-y-10">
                            <h2 class="text-5xl font-display font-black text-primary-dark">About the Journey.</h2>
                            <p class="text-xl text-slate-500 leading-relaxed font-medium">
                                Explore the breathtaking beauty of {{ dest.name }}. This curated experience offers a perfect blend of luxury, culture, and adventure, ensuring every moment is unforgettable. From high-end accommodations to exclusive local access, we handle every detail of your itinerary.
                            </p>
                        </div>

                        <!-- Itinerary -->
                        <div class="space-y-12">
                            <h3 class="text-3xl font-display font-black text-primary-dark uppercase tracking-tighter">Planned Itinerary.</h3>
                            <div class="space-y-8">
                                <div v-for="day in 3" :key="day" class="flex gap-10 group">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 rounded-3xl bg-secondary flex items-center justify-center text-primary-dark font-display font-black text-xl shadow-lg">0{{ day }}</div>
                                        <div v-if="day !== 3" class="w-[1px] flex-grow bg-slate-100 my-4"></div>
                                    </div>
                                    <div class="pb-12">
                                        <h4 class="text-2xl font-display font-black text-primary-dark mb-4">Day {{ day }}: {{ itineraryTitles[day-1] }}</h4>
                                        <p class="text-slate-400 font-medium leading-relaxed">Detailed exploration and immersive experiences throughout {{ dest.name }}, with curated logistics and premium hospitality arranged for the entire day.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Gallery -->
                        <div class="space-y-12">
                            <h3 class="text-3xl font-display font-black text-primary-dark uppercase tracking-tighter">Photo Gallery.</h3>
                            <div class="grid grid-cols-2 gap-8">
                                <div v-for="i in 4" :key="i" class="h-[300px] rounded-[3rem] overflow-hidden group shadow-premium">
                                    <img :src="`https://images.unsplash.com/photo-${galleryIds[i-1]}?w=800&q=80`" class="w-full h-full object-cover transition-transform duration-[2s] group-hover:scale-110">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Sidebar -->
                    <div class="lg:col-span-4 space-y-12">
                        <!-- Pricing Card -->
                        <div class="p-12 bg-white rounded-[4rem] border border-slate-100 shadow-premium-lg space-y-10 sticky top-12">
                            <div class="space-y-2">
                                <span class="text-[11px] font-black text-slate-400 uppercase tracking-[0.3em]">Price</span>
                                <div class="flex items-baseline gap-2">
                                    <span class="text-4xl font-display font-black text-primary-dark tracking-tighter">Competitive</span>
                                </div>
                            </div>

                            <div class="space-y-6">
                                <div v-for="feat in ['Luxury Transport', '5-Star Hotel', 'Private Guide', 'Full Insurance']" :key="feat" class="flex items-center gap-4 text-sm font-bold text-primary-dark">
                                    <span class="w-6 h-6 rounded-lg bg-secondary/20 text-secondary flex items-center justify-center text-[10px]"><i class="fas fa-check"></i></span>
                                    {{ feat }}
                                </div>
                            </div>

                            <router-link to="/contact" class="btn-premium-primary w-full !py-6 !rounded-[2rem] font-black text-[11px] shadow-primary/20">
                                REQUEST QUOTE <i class="fas fa-arrow-right ml-3"></i>
                            </router-link>

                            <p class="text-[11px] text-center text-slate-400 font-black uppercase tracking-widest">Authorized Travel Agent</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- More Destinations CTA -->
            <section class="py-32 bg-primary-dark relative overflow-hidden">
                <div class="container mx-auto px-6 text-center space-y-12 relative z-10">
                    <h2 class="text-6xl md:text-8xl font-display font-black text-white tracking-tighter">Next Adventure?</h2>
                </div>
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full bg-secondary/5 rounded-full blur-[120px]"></div>
            </section>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const dest = ref(null);
const loading = ref(true);

const itineraryTitles = ['Arrival & Exploration', 'Cultural Immersion', 'Adventure & Leisure'];
const galleryIds = [
    '1570077188670-e3a8d69ac5ff',
    '1512453979798-5ea266f8880c',
    '1531310197839-ccf54634509e',
    '1493976040374-85c8e12f0c0e'
];

const fetchDestination = async () => {
    loading.value = true;
    try {
        const slug = route.params.slug;
        const response = await axios.get(`/api/destinations/${slug}`);
        dest.value = response.data;
    } catch (error) {
        console.error("Error fetching destination:", error);
        // Robust Demo fallback matching the expected structure
        const demoData = {
            'santorini': { name: 'Santorini Sanctuary', country: 'Greece', image_path: 'https://images.unsplash.com/photo-1570077188670-e3a8d69ac5ff?w=1920&q=80', description: 'A magical island in the heart of the Aegean Sea, known for its whitewashed houses and blue-domed churches.' },
            'swiss-alps': { name: 'Swiss Alps Resort', country: 'Switzerland', image_path: 'https://images.unsplash.com/photo-1531310197839-ccf54634509e?w=1920&q=80', description: 'Breathtaking mountain peaks, pristine lakes, and world-class luxury resorts in the heart of Europe.' },
            'dubai': { name: 'Dubai Horizon', country: 'UAE', image_path: 'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=1920&q=80', description: 'Ultra-modern structural engineering and luxury desert experiences in the golden city.' },
            'kyoto': { name: 'Kyoto Core', country: 'Japan', image_path: 'https://images.unsplash.com/photo-1493976040374-85c8e12f0c0e?w=1920&q=80', description: 'Experience the perfect blend of ancient tradition and modern innovation in the heart of Japan.' },
            'amalfi-coast': { name: 'Amalfi Coast', country: 'Italy', image_path: 'https://images.unsplash.com/photo-1633321088355-d0f81134ca3b?w=1920&q=80', description: 'Stunning coastal landscapes, colorful villages, and Mediterranean luxury at its finest.' },
            'maldives': { name: 'Maldives Paradise', country: 'South Asia', image_path: 'https://images.unsplash.com/photo-1514282401047-d79a71a590e8?w=1920&q=80', description: 'Crystal clear waters, private villas, and unparalleled serene luxury in the Indian Ocean.' },
            'petra-jordan': { name: 'Petra Historical', country: 'Jordan', image_path: 'https://images.unsplash.com/photo-1579606035123-5e92150824b2?w=1920&q=80', description: 'One of the world\'s most famous archaeological sites, where ancient history comes alive.' },
            'bora-bora': { name: 'Bora Bora', country: 'French Polynesia', image_path: 'https://images.unsplash.com/photo-1532408840539-d3dc041910d7?w=1920&q=80', description: 'The pearl of the Pacific, offering world-class overwater bungalows and turquoise lagoons.' }
        };
        dest.value = demoData[route.params.slug] || demoData['santorini'];
    } finally {
        loading.value = false;
        window.scrollTo(0, 0);
    }
}

onMounted(() => {
    fetchDestination();
});
</script>

<style scoped>
@keyframes reveal {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-reveal {
    animation: reveal 1.2s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
.delay-300 { animation-delay: 300ms; }
</style>
