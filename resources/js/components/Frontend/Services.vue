<template>
    <div class="bg-white selection:bg-primary/10">
        <!-- Hero Section -->
        <section class="page-hero">
            <div class="page-hero-bg">
                <img src="https://images.unsplash.com/photo-1554224155-1696413565d3?w=1920&q=80" alt="Travel services">
                <div class="page-hero-shade"></div>
            </div>
            <div class="page-hero-content text-center md:text-left">
                <nav class="page-breadcrumb justify-center md:justify-start">
                    <router-link to="/">Home</router-link>
                    <span class="sep"></span>
                    <span class="current">Services</span>
                </nav>
                <div class="page-hero-badge">
                    <i class="fas fa-concierge-bell"></i>
                    <span>Our Travel Services</span>
                </div>
                <h1 class="page-hero-title">Complete Travel <em>Services</em></h1>
                <p class="page-hero-sub mx-auto md:mx-0">
                    From flights and hotels to spiritual pilgrimages and visa processing — everything you need for a seamless journey, in one place.
                </p>
            </div>
        </section>

        <!-- Services -->
        <section class="py-24 lg:py-32">
            <div class="container mx-auto px-6 space-y-28 lg:space-y-36">
                <!-- 1. Travel Planning -->
                <div class="grid grid-cols-1 lg:grid-cols-2 items-center gap-16 lg:gap-24">
                    <div class="relative group order-2 lg:order-1">
                        <div class="relative h-[480px] rounded-[3rem] overflow-hidden shadow-premium group-hover:shadow-premium-lg transition-all duration-1000 border border-slate-100">
                            <img src="https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?w=800&q=80" alt="Travel planning" class="w-full h-full object-cover transition-transform duration-[3s] group-hover:scale-110">
                            <div class="absolute inset-0 bg-gradient-to-t from-primary-dark/70 via-transparent to-transparent"></div>
                            <div class="absolute bottom-10 left-10 w-16 h-16 rounded-2xl bg-white/15 backdrop-blur-2xl flex items-center justify-center text-white text-2xl border border-white/20">
                                <i class="fas fa-map-marked-alt"></i>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-8 order-1 lg:order-2">
                        <div class="space-y-5">
                            <div class="section-eyebrow"><i class="fas fa-plane-departure"></i> Service 01</div>
                            <h2 class="section-title">Travel <em>Planning</em></h2>
                        </div>
                        <p class="section-intro text-lg max-w-xl">
                            We handle every detail of your trip so you can focus on enjoying the journey. From custom itineraries to group travel coordination, our experts ensure a seamless experience.
                        </p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div v-for="item in ['Custom Itinerary Design', 'Private Transfers', 'Luxury Accommodations', 'Group Coordination']" :key="item" class="flex items-center gap-3 text-sm font-bold text-primary-dark bg-slate-50 p-5 rounded-2xl border border-slate-100 hover:border-secondary/30 transition-colors">
                                <i class="fas fa-check-circle text-secondary"></i> {{ item }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Visa Services -->
                <div class="grid grid-cols-1 lg:grid-cols-2 items-center gap-16 lg:gap-24">
                    <div class="space-y-8">
                        <div class="space-y-5">
                            <div class="section-eyebrow"><i class="fas fa-passport"></i> Service 02</div>
                            <h2 class="section-title">Visa <em>Services</em></h2>
                        </div>
                        <p class="section-intro text-lg max-w-xl">
                            Navigating complex visa requirements made easy. We process visa applications for over 40 countries with high approval rates and fast turnaround times.
                        </p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div v-for="item in ['Tourist Visa Processing', 'Business Visa Support', 'Expedited Processing', 'Document Verification']" :key="item" class="flex items-center gap-3 text-sm font-bold text-primary-dark bg-slate-50 p-5 rounded-2xl border border-slate-100 hover:border-secondary/30 transition-colors">
                                <i class="fas fa-check-circle text-secondary"></i> {{ item }}
                            </div>
                        </div>
                        <router-link to="/visa" class="btn-premium-secondary !px-10 !py-4 inline-flex">
                            Learn More <i class="fas fa-arrow-right ml-2 text-xs"></i>
                        </router-link>
                    </div>
                    <div class="relative group">
                        <div class="relative h-[480px] rounded-[3rem] overflow-hidden shadow-premium group-hover:shadow-premium-lg transition-all duration-1000 border border-slate-100">
                            <img src="https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?w=800&q=80" alt="Visa services" class="w-full h-full object-cover transition-transform duration-[3s] group-hover:scale-110">
                            <div class="absolute inset-0 bg-gradient-to-t from-primary-dark/70 via-transparent to-transparent"></div>
                            <div class="absolute bottom-10 left-10 w-16 h-16 rounded-2xl bg-white/15 backdrop-blur-2xl flex items-center justify-center text-white text-2xl border border-white/20">
                                <i class="fas fa-stamp"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Flight Booking -->
                <div class="grid grid-cols-1 lg:grid-cols-2 items-center gap-16 lg:gap-24">
                    <div class="relative group order-2 lg:order-1">
                        <div class="relative h-[480px] rounded-[3rem] overflow-hidden shadow-premium group-hover:shadow-premium-lg transition-all duration-1000 border border-slate-100">
                            <img src="https://images.unsplash.com/photo-1556388158-158ea5ccacbd?w=800&q=80" alt="Flight booking" class="w-full h-full object-cover transition-transform duration-[3s] group-hover:scale-110">
                            <div class="absolute inset-0 bg-gradient-to-t from-primary-dark/70 via-transparent to-transparent"></div>
                            <div class="absolute bottom-10 left-10 w-16 h-16 rounded-2xl bg-white/15 backdrop-blur-2xl flex items-center justify-center text-white text-2xl border border-white/20">
                                <i class="fas fa-plane"></i>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-8 order-1 lg:order-2">
                        <div class="space-y-5">
                            <div class="section-eyebrow"><i class="fas fa-ticket-alt"></i> Service 03</div>
                            <h2 class="section-title">Flight <em>Booking</em></h2>
                        </div>
                        <p class="section-intro text-lg max-w-xl">
                            Direct partnerships with major airlines worldwide. We find you the best routes, competitive prices, and premium cabin upgrades for any destination.
                        </p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div v-for="item in ['Best Price Guarantee', 'Business Class Upgrades', 'Group Bookings', '24/7 Flight Support']" :key="item" class="flex items-center gap-3 text-sm font-bold text-primary-dark bg-slate-50 p-5 rounded-2xl border border-slate-100 hover:border-secondary/30 transition-colors">
                                <i class="fas fa-check-circle text-secondary"></i> {{ item }}
                            </div>
                        </div>
                        <router-link to="/flights" class="btn-premium-primary !px-10 !py-4 inline-flex">
                            Search Flights <i class="fas fa-arrow-right ml-2 text-xs"></i>
                        </router-link>
                    </div>
                </div>

                <!-- 4. Umrah & Hajj -->
                <div class="grid grid-cols-1 lg:grid-cols-2 items-center gap-16 lg:gap-24">
                    <div class="space-y-8">
                        <div class="space-y-5">
                            <div class="section-eyebrow"><i class="fas fa-moon"></i> Service 04</div>
                            <h2 class="section-title">Umrah &amp; Hajj <em>Packages</em></h2>
                        </div>
                        <p class="section-intro text-lg max-w-xl">
                            Authorized Umrah operators with years of experience. We offer complete packages including 5-star hotels, guided tours, visa processing, and VIP ground transportation.
                        </p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div v-for="item in ['Premium Hotel Booking', 'MOFA Visa Processing', 'Airport Transfers', 'Personal Concierge']" :key="item" class="flex items-center gap-3 text-sm font-bold text-primary-dark bg-slate-50 p-5 rounded-2xl border border-slate-100 hover:border-secondary/30 transition-colors">
                                <i class="fas fa-check-circle text-secondary"></i> {{ item }}
                            </div>
                        </div>
                        <router-link to="/umrah" class="btn-premium-secondary !px-10 !py-4 inline-flex">
                            View Packages <i class="fas fa-arrow-right ml-2 text-xs"></i>
                        </router-link>
                    </div>
                    <div class="relative group">
                        <div class="relative h-[480px] rounded-[3rem] overflow-hidden shadow-premium group-hover:shadow-premium-lg transition-all duration-1000 border border-slate-100">
                            <img src="https://images.unsplash.com/photo-1591604129939-f1efa4d9f7fa?w=800&q=80" alt="Umrah and Hajj" class="w-full h-full object-cover transition-transform duration-[3s] group-hover:scale-110">
                            <div class="absolute inset-0 bg-gradient-to-t from-primary-dark/70 via-transparent to-transparent"></div>
                            <div class="absolute bottom-10 left-10 w-16 h-16 rounded-2xl bg-white/15 backdrop-blur-2xl flex items-center justify-center text-white text-2xl border border-white/20">
                                <i class="fas fa-kaaba"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="py-24 lg:py-32 px-6">
            <div class="container mx-auto">
                <div class="bg-primary-dark rounded-[4rem] p-16 lg:p-28 relative overflow-hidden group shadow-premium-lg border border-white/5">
                    <div class="absolute -top-40 -right-40 w-[700px] h-[700px] bg-secondary/10 rounded-full blur-[150px] group-hover:bg-secondary/20 transition-all duration-[3s]"></div>
                    <div class="absolute -bottom-40 -left-40 w-[700px] h-[700px] bg-primary/20 rounded-full blur-[150px] group-hover:bg-primary/30 transition-all duration-[3s]"></div>

                    <div class="relative z-10 text-center space-y-10">
                        <div class="space-y-5">
                            <div class="inline-flex items-center gap-3 px-5 py-2 bg-white/5 backdrop-blur-xl rounded-full border border-white/10 mx-auto">
                                <span class="w-2 h-2 rounded-full bg-secondary animate-pulse"></span>
                                <span class="text-[11px] font-black text-white uppercase tracking-[0.3em]">We're Here To Help</span>
                            </div>
                            <h2 class="text-4xl md:text-6xl font-display font-black text-white leading-tight">Contact <em class="text-secondary italic">Us</em></h2>
                        </div>
                        <p class="text-white/60 text-lg font-medium max-w-2xl mx-auto leading-relaxed">
                            Our travel specialists are available 24/7 to help plan your perfect trip and answer any questions.
                        </p>
                        <div class="flex flex-col sm:flex-row items-center justify-center gap-8 pt-4">
                            <router-link to="/contact" class="btn-premium-primary !px-14 !py-5 shadow-2xl shadow-primary/40 transition-all hover:scale-105 active:scale-95">
                                Get In Touch <i class="fas fa-arrow-right ml-2"></i>
                            </router-link>
                            <a href="tel:+923085093631" class="flex flex-col items-center text-white hover:text-secondary transition-colors">
                                <span class="text-[10px] font-black text-white/40 uppercase tracking-[0.3em] mb-1">Call Us Directly</span>
                                <span class="font-display font-black text-2xl tracking-tight">03085093631</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<script setup>
</script>
