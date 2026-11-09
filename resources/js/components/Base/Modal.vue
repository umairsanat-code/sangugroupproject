<template>
    <transition name="modal">
        <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <!-- Backdrop -->
            <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" @click="$emit('close')"></div>
            
            <!-- Modal Content -->
            <div 
                class="relative bg-white rounded-[--radius-premium] shadow-2xl w-full flex flex-col max-h-[90vh] overflow-hidden"
                :class="sizeClass"
            >
                <!-- Header -->
                <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <div>
                        <h3 class="text-xl font-bold text-slate-900">{{ title }}</h3>
                        <p v-if="subtitle" class="text-xs text-slate-500 font-bold uppercase tracking-widest mt-1">{{ subtitle }}</p>
                    </div>
                    <button 
                        @click="$emit('close')" 
                        class="p-2 hover:bg-slate-200 rounded-xl transition-colors text-slate-400 hover:text-slate-600"
                    >
                        ✕
                    </button>
                </div>

                <!-- Body -->
                <div class="p-8 overflow-y-auto custom-scrollbar flex-1">
                    <slot></slot>
                </div>

                <!-- Footer -->
                <div v-if="$slots.footer" class="p-6 border-t border-slate-100 bg-slate-50/50 flex justify-end gap-3">
                    <slot name="footer"></slot>
                </div>
            </div>
        </div>
    </transition>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    show: { type: Boolean, required: true },
    title: { type: String, required: true },
    subtitle: { type: String, default: '' },
    size: { type: String, default: 'md' }
});

const emit = defineEmits(['close']);

const sizeClass = computed(() => {
    switch (props.size) {
        case 'sm': return 'max-w-md';
        case 'lg': return 'max-w-4xl';
        case 'xl': return 'max-w-6xl';
        case 'full': return 'max-w-full m-4 h-[calc(100vh-2rem)]';
        default: return 'max-w-2xl';
    }
});
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #e2e8f0;
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #cbd5e1;
}
</style>
