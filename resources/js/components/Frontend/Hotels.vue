<template>
    <div class="bg-white selection:bg-primary/10">
        <!-- Hotels Hero Section -->
        <section class="relative py-28 md:py-40 bg-primary overflow-hidden">
            <!-- Background Image -->
            <div class="absolute inset-0 z-0">
                <img
                    src="/images/travivu_hotels_banner_1775155386699.png"
                    alt="Hotels Banner"
                    class="w-full h-full object-cover mix-blend-overlay opacity-20"
                >
                <div class="absolute inset-0 bg-gradient-to-r from-primary/90 to-transparent"></div>
            </div>

            <div class="container mx-auto px-6 relative z-10">
                <nav class="flex items-center gap-3 mb-8 text-white/70 text-[10px] font-black uppercase tracking-widest">
                    <router-link to="/" class="hover:text-secondary transition-colors">Home</router-link>
                    <span class="w-1 h-1 rounded-full bg-white/30"></span>
                    <span class="text-white">Hotels</span>
                </nav>
                <h1 class="text-5xl md:text-7xl font-display font-black text-white leading-tight">
                    Luxury <span class="text-secondary italic">Stay</span>
                </h1>
                <p class="text-white/80 text-lg font-medium max-w-xl mt-4">
                    Discover handpicked premium accommodations for your next unforgettable journey.
                </p>
            </div>
        </section>

        <!-- Search Bar Section -->
        <div class="container mx-auto px-6 -mt-20 relative z-20">
            <div class="bg-white p-4 rounded-[2.5rem] shadow-2xl border border-slate-100 transition-all hover:shadow-primary/5">
                <div class="bg-surface-soft p-8 rounded-[2rem]">
                    <form @submit.prevent="searchHotels" class="grid grid-cols-1 md:grid-cols-12 gap-6 items-end">
                        <div class="md:col-span-6">
                            <BaseInput label="Location" v-model="filters.location" placeholder="Where are you going?" />
                        </div>
                        <div class="md:col-span-3">
                            <BaseInput label="Guests" type="select" v-model="filters.guests">
                                <option>2 Adults, 1 Room</option>
                                <option>1 Adult, 1 Room</option>
                                <option>Family (4+)</option>
                            </BaseInput>
                        </div>
                        <div class="md:col-span-3">
                            <button type="submit" class="btn-premium-primary !h-[54px] w-full font-black text-[11px] uppercase tracking-widest">
                                Search Hotels <i class="fas fa-search ml-2"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Listing Section -->
        <section class="py-24 bg-white">
            <div class="container mx-auto px-6">
                <div class="flex flex-col lg:flex-row gap-12">
                    <!-- Sidebar Filters -->
                    <aside class="w-full lg:w-80 space-y-8">
                        <div class="p-8 bg-white border border-border rounded-[2rem] shadow-sm sticky top-32">
                            <div class="flex items-center justify-between mb-8 pb-4 border-b border-slate-50">
                                <h3 class="text-sm font-black uppercase tracking-widest text-text-dark">Filter By</h3>
                                <button class="text-[10px] font-bold text-text-muted hover:text-primary transition-colors">Clear</button>
                            </div>

                            <div class="space-y-10">
                                <!-- Categories -->
                                <div class="space-y-6">
                                    <h4 class="text-[11px] font-black uppercase tracking-[0.2em] text-text-muted">Categories</h4>
                                    <div class="space-y-4">
                                        <label v-for="cat in ['Resorts', 'Boutique', 'Villas', 'Appartments']" :key="cat" class="flex items-center justify-between text-xs font-bold text-text-dark cursor-pointer group">
                                            <div class="flex items-center gap-4">
                                                <input type="checkbox" class="w-5 h-5 rounded-lg border-border text-primary focus:ring-primary/20 transition-all">
                                                <span class="group-hover:text-primary transition-colors">{{ cat }}</span>
                                            </div>
                                            <span class="text-[10px] text-text-muted font-black">(12)</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Star Ratings -->
                                <div class="space-y-6 pt-4 border-t border-slate-50">
                                    <h4 class="text-[11px] font-black uppercase tracking-[0.2em] text-text-muted">Ratings</h4>
                                    <div class="space-y-4">
                                        <label v-for="star in [5, 4, 3]" :key="star" class="flex items-center gap-4 text-xs font-bold text-text-dark cursor-pointer group">
                                            <input type="checkbox" class="w-5 h-5 rounded-lg border-border text-primary focus:ring-primary/20 transition-all">
                                            <div class="flex items-center gap-1 text-secondary">
                                                <i v-for="i in 5" :key="i" :class="[i <= star ? 'fas fa-star' : 'far fa-star']" class="text-[10px]"></i>
                                                <span class="ml-2 text-text-muted group-hover:text-primary transition-colors">{{ star }} Stars</span>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </aside>

                    <!-- Hotel Cards -->
                    <main class="flex-grow space-y-10">
                        <div v-for="hotel in hotels" :key="hotel.id" class="group bg-white rounded-[3rem] border border-slate-100 overflow-hidden hover:shadow-premium-lg transition-all duration-700 flex flex-col xl:flex-row">
                            <!-- Hotel Image -->
                            <div class="relative xl:w-[450px] h-[400px] xl:h-auto overflow-hidden shrink-0">
                                <img :src="hotel.img" :alt="hotel.name" class="w-full h-full object-cover transition-transform duration-[3s] group-hover:scale-110">
                                <div class="absolute inset-0 bg-gradient-to-t from-primary-dark via-primary-dark/20 to-transparent"></div>
                                
                                <div class="absolute top-8 left-8">
                                    <div class="px-5 py-2 bg-white/10 backdrop-blur-2xl text-white text-[11px] font-black uppercase tracking-[0.2em] rounded-2xl border border-white/10">
                                        Verified Hotel
                                    </div>
                                </div>

                                <div class="absolute bottom-8 left-8">
                                    <div class="flex items-center gap-1.5 text-secondary text-[10px] mb-2">
                                        <i v-for="i in 5" :key="i" class="fas fa-star"></i>
                                    </div>
                                    <span class="text-white text-[10px] font-black uppercase tracking-widest">{{ hotel.type }}</span>
                                </div>
                            </div>

                            <!-- Hotel Details -->
                            <div class="flex-grow p-12 flex flex-col justify-between">
                                <div class="space-y-6">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-4xl font-display font-black text-primary-dark tracking-tighter group-hover:text-secondary transition-colors">{{ hotel.name }}</h3>
                                        <button class="w-12 h-12 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400 hover:bg-danger/10 hover:text-danger transition-all">
                                            <i class="far fa-heart"></i>
                                        </button>
                                    </div>

                                    <div class="flex flex-wrap items-center gap-6">
                                        <div class="flex items-center gap-2 text-[10px] font-black text-primary uppercase tracking-widest bg-primary/5 px-4 py-2 rounded-xl">
                                            <i class="fas fa-map-marker-alt"></i> {{ hotel.loc }}
                                        </div>
                                        <div class="flex items-center gap-2 text-[10px] font-black text-slate-400 uppercase tracking-widest border border-slate-100 px-4 py-2 rounded-xl">
                                            <i class="fas fa-wifi"></i> Free Wi-Fi
                                        </div>
                                        <div class="flex items-center gap-2 text-[10px] font-black text-slate-400 uppercase tracking-widest border border-slate-100 px-4 py-2 rounded-xl">
                                            <i class="fas fa-shield-alt"></i> Secure Booking
                                        </div>
                                    </div>

                                    <p class="text-slate-400 font-medium leading-relaxed text-sm">
                                        {{ hotel.desc }}
                                    </p>
                                </div>

                                <div class="pt-10 mt-10 border-t border-slate-50 flex items-center justify-between">
                                    <div class="space-y-1">
                                        <p class="text-[11px] font-black text-slate-400 uppercase tracking-[0.3em]">Price</p>
                                        <div class="text-2xl font-display font-black text-primary-dark tracking-tighter">
                                            Competitive
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Load More -->
                        <div class="text-center py-10">
                            <button class="btn-premium-outline !rounded-2xl !px-16 !h-16 font-black uppercase text-[11px] tracking-[0.3em] hover:border-primary">
                                View More Hotels
                            </button>
                        </div>
                    </main>
                </div>
            </div>
        </section>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import BaseInput from '../Base/BaseInput.vue';

const filters = ref({
    location: '',
    guests: '2 Adults, 1 Room'
});
const baseUrl = ref('');
// Vite automatically handles base URL
if (typeof import.meta !== 'undefined' && import.meta.env) {
    baseUrl.value = import.meta.env.BASE_URL || '';
}
const searchHotels = () => {
    console.log("Searching for hotels with filters:", filters.value);
}

const hotels = [
    {
        id: 1,
        name: 'Burj Al Arab Luxury Suite',
        loc: 'Dubai, UAE',
        price: '$850',
        type: '5 Stars Resort',
        img: 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800&q=80',
        desc: 'Experience the ultimate in luxury at the world\'s most iconic hotel with private beach access and unparalleled service.'
    },
    {
        id: 2,
        name: 'The Ritz Paris - Classic',
        loc: 'Paris, France',
        price: '$920',
        type: 'Historic Luxury',
        img: 'https://images.unsplash.com/photo-1582719508461-905c673771fd?w=800&q=80',
        desc: 'A legend of elegance overlooking Place Vendôme with exquisite dining and a world-class wellness center.'
    },
    {
        id: 3,
        name: 'Santorini Blue Horizon',
        loc: 'Santorini, Greece',
        price: '$450',
        type: 'Boutique Villa',
        img: 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?w=800&q=80',
        desc: 'Stunning sunset views from your private infinity pool overlooking the Aegean Sea and the caldera.'
    }
];
</script>
