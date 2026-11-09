<template>
    <div class="bg-white selection:bg-primary/10">
        <!-- Hero Section -->
        <section class="page-hero">
            <div class="page-hero-bg">
                <img src="https://images.unsplash.com/photo-1497366216548-37526070297c?w=1920&q=90" alt="Our Team" class="grayscale opacity-30">
                <div class="page-hero-shade"></div>
            </div>
            <div class="page-hero-content text-center md:text-left">
                <nav class="page-breadcrumb justify-center md:justify-start">
                    <router-link to="/">Home</router-link>
                    <span class="sep"></span>
                    <span class="current">Our Team</span>
                </nav>
                <div class="page-hero-badge">
                    <i class="fas fa-users"></i>
                    <span>Our Professional Team</span>
                </div>
                <h1 class="page-hero-title">Meet Our <em>Team</em></h1>
                <p class="page-hero-sub mx-auto md:mx-0">
                    Experienced travel consultants and destination specialists dedicated to making your journey perfect.
                </p>
            </div>
        </section>

        <!-- Team Grid -->
        <section class="py-24 lg:py-32">
            <div class="container mx-auto px-6">
                <div class="text-center max-w-2xl mx-auto mb-20 space-y-4">
                    <h6 class="section-eyebrow"><i class="fas fa-star"></i> Meet The Experts</h6>
                    <h2 class="section-title section-title-center">Our <em>Leadership</em></h2>
                    <p class="section-intro">The people behind every seamless journey and memorable experience.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                    <div v-for="member in team" :key="member.name" class="group bg-white rounded-[3rem] border border-slate-100 shadow-premium hover:shadow-premium-lg transition-all duration-700 overflow-hidden">
                        <div class="relative h-[420px] overflow-hidden">
                            <img :src="member.img" :alt="member.name" class="w-full h-full object-cover object-top transition-transform duration-1000 group-hover:scale-110">
                            <div class="absolute inset-0 bg-gradient-to-t from-accent/70 via-transparent to-transparent"></div>
                            <div class="absolute top-8 left-8">
                                <span class="px-5 py-2 bg-white/95 text-accent text-[11px] font-black uppercase tracking-widest rounded-2xl shadow-xl">{{ member.role }}</span>
                            </div>
                        </div>
                        <div class="p-10 text-center">
                            <h3 class="text-2xl font-display font-black text-accent mb-2">{{ member.name }}</h3>
                            <p class="text-secondary text-[11px] font-black uppercase tracking-[0.25em] mb-6">{{ member.role }}</p>
                            <p class="text-sm text-text-muted font-medium leading-relaxed max-w-sm mx-auto mb-8">{{ member.bio }}</p>
                            <div class="flex justify-center gap-3">
                                <a :href="member.linkedin" target="_blank" class="w-11 h-11 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-500 hover:bg-primary hover:text-white transition-all"><i class="fab fa-linkedin-in"></i></a>
                                <a :href="member.email" class="w-11 h-11 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-500 hover:bg-primary hover:text-white transition-all"><i class="fas fa-envelope"></i></a>
                            </div>
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
                            <h6 class="text-white font-black tracking-[0.4em] uppercase text-[11px]">Want To Join Our Team?</h6>
                            <h2 class="text-4xl md:text-6xl font-display font-black text-white leading-tight">Get In <em class="italic">Touch</em></h2>
                        </div>
                        <p class="text-white/80 text-lg font-medium max-w-2xl mx-auto leading-relaxed">
                            We're always looking for passionate travel professionals to join our growing team.
                        </p>
                        <div class="flex flex-col sm:flex-row items-center justify-center gap-8 pt-4">
                            <router-link to="/contact" class="btn-premium-primary !bg-accent !text-white !px-14 !py-5 !rounded-full">
                                Contact Us <i class="fas fa-arrow-right ml-2"></i>
                            </router-link>
                            <a href="tel:+923085093631" class="flex flex-col items-center text-white hover:text-accent transition-colors">
                                <span class="text-[10px] font-black text-white/60 uppercase tracking-[0.3em] mb-1">Call Us</span>
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
const team = [
    {
        name: 'Muhammad Farooq',
        role: 'CEO & Founder',
        img: '/images/team/farooq.png',
        bio: 'Leading Sangugroup with a vision to provide the most professional and spiritual travel experiences globally.',
        linkedin: 'https://www.linkedin.com/in/muhammad-farooq-sangu-60888b65',
        email: 'mailto:info@sangugroup.com'
    },
    {
        name: 'Sanat Ahmad',
        role: 'Lead Developer',
        img: '/images/team/sanat.png',
        bio: 'Crafting the digital experience behind Sangugroup\'s premium travel services and booking platforms.',
        linkedin: 'https://www.linkedin.com/',
        email: 'mailto:info@sangugroup.com'
    },
    {
        name: 'Operations Team',
        role: 'Travel Operations',
        img: 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?w=600&q=80',
        bio: 'Our operations specialists handle flights, hotels, visas, and ground support so your trip runs flawlessly.',
        linkedin: 'https://www.linkedin.com/',
        email: 'mailto:info@sangugroup.com'
    }
];
</script>
