<template>
    <div class="admin-media">
        <div class="flex items-center justify-between mb-10">
            <div>
                <h1 class="text-3xl font-black text-slate-900">Media Sanctuary</h1>
                <p class="text-sm text-slate-400 font-bold uppercase tracking-widest mt-2">Manage visual assets, galleries, and high-fidelity catalogs</p>
            </div>
            <div class="flex gap-4">
                <button class="px-6 py-4 bg-slate-50 border border-slate-200 text-[10px] font-black uppercase tracking-widest rounded-2xl flex items-center gap-3 hover:bg-slate-100 transition-all">
                    <span>📁</span> New Folder
                </button>
                <button class="px-8 py-4 bg-primary text-white text-[10px] font-black uppercase tracking-widest rounded-2xl shadow-lg shadow-primary/20 hover:scale-105 transition-transform">
                    ↑ Upload Assets
                </button>
            </div>
        </div>

        <!-- Grid Layout -->
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
            <div v-for="i in 12" :key="i" class="premium-card p-0 aspect-square group relative overflow-hidden cursor-pointer">
                <img :src="`https://images.unsplash.com/photo-${1500000000000 + (i * 1000000)}?w=400&q=80`" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                <div class="absolute inset-0 bg-primary/60 opacity-0 group-hover:opacity-100 transition-all flex flex-col items-center justify-center gap-4">
                    <button class="px-4 py-2 bg-white text-primary text-[10px] font-black uppercase tracking-widest rounded-lg shadow-xl hover:bg-secondary hover:text-white transition-all">Select</button>
                    <button class="text-white/50 hover:text-red-400 transition-colors text-xs font-black uppercase tracking-widest">Remove</button>
                </div>
                <div class="absolute bottom-3 left-3 right-3 truncate">
                    <span class="px-2 py-1 bg-black/50 backdrop-blur-md text-[10px] font-black text-white/80 uppercase tracking-widest rounded rounded-bl-xl shadow-lg">Image_{{ i }}.jpg</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';

// Mock media data for the demo
const mediaItems = ref([]);
</script>
