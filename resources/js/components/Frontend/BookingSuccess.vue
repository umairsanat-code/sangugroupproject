<template>
    <div class="min-h-screen bg-slate-50 selection:bg-secondary/30 flex items-center justify-center p-6">
        <div class="max-w-3xl w-full">
            <div v-if="loading" class="text-center space-y-8 animate-reveal">
                <div class="w-24 h-24 border-2 border-slate-100 border-t-secondary rounded-full animate-spin mx-auto"></div>
                <p class="text-[13px] font-black uppercase tracking-[0.3em] text-slate-400">Loading your booking...</p>
            </div>

            <div v-else-if="booking" class="space-y-10 animate-reveal">
                <!-- Success Header -->
                <div class="bg-primary-dark rounded-[4rem] p-16 text-center border border-white/5 shadow-premium-lg relative overflow-hidden group">
                    <div class="absolute -top-20 -right-20 w-60 h-60 bg-secondary/10 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-[2s]"></div>
                    
                    <div class="relative z-10 space-y-8">
                        <div class="w-24 h-24 bg-secondary rounded-[2rem] flex items-center justify-center text-primary-dark text-4xl mx-auto shadow-2xl shadow-secondary/20 transform rotate-12 group-hover:rotate-0 transition-transform duration-700">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="space-y-3">
                            <h1 class="text-5xl md:text-7xl font-display font-black text-white tracking-tighter leading-none">
                                Booking <span class="text-secondary italic">Confirmed.</span>
                            </h1>
                            <p class="text-white/40 text-[11px] font-black uppercase tracking-[0.4em]">Booking Status: Confirmed</p>
                        </div>
                    </div>
                </div>

                <!-- Booking Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    <div class="bg-white p-12 rounded-[3.5rem] border border-slate-100 shadow-premium-sm space-y-10">
                        <div class="flex items-center gap-6">
                            <div class="w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center text-primary text-xl"><i class="fas fa-file-alt"></i></div>
                            <div class="space-y-1">
                                <h4 class="text-xl font-display font-black text-primary-dark">Booking Reference</h4>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ booking.booking_reference }}</p>
                            </div>
                        </div>
                        <div class="space-y-6">
                            <div class="flex justify-between items-center text-xs font-bold">
                                <span class="text-slate-400 uppercase tracking-widest text-[10px] font-black">Name</span>
                                <span class="text-primary-dark">{{ booking.first_name }} {{ booking.last_name }}</span>
                            </div>
                            <div class="flex justify-between items-center text-xs font-bold">
                                <span class="text-slate-400 uppercase tracking-widest text-[10px] font-black">Service</span>
                                <span class="text-primary-dark">{{ booking.item_name }}</span>
                            </div>
                            <div class="flex justify-between items-center text-xs font-bold">
                                <span class="text-slate-400 uppercase tracking-widest text-[10px] font-black">Payment</span>
                                <span class="text-primary-dark capitalize">{{ booking.payment_method }} Gateway</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-12 rounded-[3.5rem] border border-slate-100 shadow-premium-sm flex flex-col justify-between">
                        <div class="space-y-4">
                            <h4 class="text-xl font-display font-black text-primary-dark">What's Next</h4>
                            <p class="text-sm text-slate-400 font-medium leading-relaxed">
                                A confirmation email has been sent to {{ booking.email }}. Our team will contact you with further details within 24 hours.
                            </p>
                        </div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-lg font-black text-secondary">$</span>
                            <div class="text-6xl font-display font-black text-primary-dark tracking-tighter">{{ booking.total_amount }}</div>
                        </div>
                    </div>
                </div>

                <!-- Global Actions -->
                <div class="flex flex-col sm:flex-row gap-6 pt-6">
                    <router-link to="/" class="btn-premium-primary flex-grow !h-[80px] !rounded-[2.5rem] flex items-center justify-center font-black text-[11px] uppercase tracking-[0.4em]">
                        Return to Home <i class="fas fa-arrow-right ml-3"></i>
                    </router-link>
                    <button @click="window.print()" class="btn-premium-outline !h-[80px] !px-12 !rounded-[2.5rem] font-black text-[11px] uppercase tracking-[0.4em] border-slate-200 hover:bg-slate-900 hover:text-white">
                        Print Receipt
                    </button>
                </div>
            </div>

            <!-- Error State -->
            <div v-else class="text-center space-y-10 animate-reveal">
                <div class="text-8xl text-slate-200 mb-8"><i class="fas fa-search"></i></div>
                <h2 class="text-5xl font-display font-black text-primary-dark tracking-tighter">Booking Not Found</h2>
                <p class="text-lg text-slate-400 font-medium max-w-md mx-auto">We could not find the requested booking. The link may have expired or the booking ID is incorrect.</p>
                <router-link to="/" class="btn-premium-primary inline-flex !px-16">Return to Home <i class="fas fa-arrow-right ml-2"></i></router-link>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const booking = ref(null);
const loading = ref(true);

const fetchBooking = async () => {
    loading.value = true;
    try {
        const id = route.params.id;
        const session_id = route.query.session_id;

        if (session_id) {
            await axios.post('/api/bookings/payment-success', {
                reference: id,
                session_id: session_id
            });
        }

        const response = await axios.get(`/api/bookings/${id}`);
        booking.value = response.data;
    } catch (error) {
        console.error("Link/Payment Verification Failure:", error);
    } finally {
        setTimeout(() => {
            loading.value = false;
        }, 1200);
    }
}

onMounted(() => {
    fetchBooking();
});
</script>

<style scoped>
@keyframes reveal {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-reveal {
    animation: reveal 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
.delay-100 { animation-delay: 150ms; }
</style>
