<template>
    <div class="bg-white rounded-2xl border border-border overflow-hidden shadow-sm hover:shadow-md transition-shadow">
        <div class="px-8 py-6 border-b border-border flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h3 class="text-xl font-display font-bold text-text-dark">{{ title }}</h3>
                <p v-if="subtitle" class="text-xs text-text-muted font-bold uppercase tracking-widest mt-1">{{ subtitle }}</p>
            </div>
            
            <div class="flex items-center gap-4">
                <div class="relative group">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-text-muted group-focus-within:text-primary transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input 
                        type="text" 
                        v-model="searchQuery" 
                        placeholder="Search records..." 
                        class="h-11 pl-11 pr-4 rounded-full border border-border bg-surface-soft text-sm font-medium w-full md:w-72 focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/5 transition-all outline-none"
                    >
                </div>
                <slot name="actions"></slot>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-surface-soft border-b border-border">
                        <th v-for="col in columns" :key="col.key" :class="[col.class, 'px-8 py-4 text-[11px] font-black uppercase tracking-widest text-text-muted']">
                            {{ col.label }}
                        </th>
                        <th v-if="showActions" class="px-8 py-4 text-[11px] font-black uppercase tracking-widest text-text-muted text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <tr v-for="item in filteredData" :key="item.id" class="group hover:bg-slate-50 transition-colors">
                        <td v-for="col in columns" :key="col.key" :class="[col.class, 'px-8 py-4 text-sm font-bold text-text-dark']">
                            <slot :name="`cell(${col.key})`" :item="item">
                                {{ item[col.key] }}
                            </slot>
                        </td>
                        <td v-if="showActions" class="px-8 py-4 text-right">
                            <div class="flex justify-end items-center gap-2">
                                <slot name="row-actions" :item="item">
                                    <button 
                                        class="w-9 h-9 flex items-center justify-center rounded-full bg-white border border-border text-text-muted hover:bg-primary hover:text-white hover:border-primary transition-all shadow-sm"
                                        @click="$emit('edit', item)"
                                        title="Edit"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </button>
                                    <button 
                                        class="w-9 h-9 flex items-center justify-center rounded-full bg-white border border-border text-text-muted hover:bg-danger hover:text-white hover:border-danger transition-all shadow-sm"
                                        @click="$emit('delete', item.id)"
                                        title="Delete"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </slot>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="filteredData.length === 0">
                        <td :colspan="columns.length + (showActions ? 1 : 0)" class="text-center py-24 bg-white">
                            <div class="flex flex-col items-center">
                                <div class="w-20 h-20 rounded-full bg-slate-50 flex items-center justify-center text-slate-300 mb-6">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                    </svg>
                                </div>
                                <h4 class="text-text-dark font-display font-bold text-lg">No Results Found</h4>
                                <p class="text-text-muted text-sm font-medium mt-1">We couldn't find any entries matching your criteria.</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div v-if="filteredData.length > 0" class="px-8 py-6 border-t border-border flex flex-col sm:row justify-between items-center gap-4 bg-surface-soft/30">
            <div class="text-[11px] font-bold uppercase tracking-widest text-text-muted">
                Showing <span class="text-text-dark">{{ filteredData.length }}</span> of <span class="text-text-dark">{{ data.length }}</span> results
            </div>
            <div class="flex gap-2">
                <button class="h-9 px-4 rounded-lg border border-border bg-white text-[10px] font-black uppercase tracking-widest text-text-dark hover:bg-primary hover:text-white hover:border-primary disabled:opacity-30 disabled:pointer-events-none transition-all" disabled>
                    Prev
                </button>
                <button class="h-9 px-4 rounded-lg border border-border bg-white text-[10px] font-black uppercase tracking-widest text-text-dark hover:bg-primary hover:text-white hover:border-primary disabled:opacity-30 disabled:pointer-events-none transition-all" disabled>
                    Next
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    title: { type: String, required: true },
    subtitle: { type: String, default: '' },
    columns: { type: Array, required: true },
    data: { type: Array, required: true },
    showActions: { type: Boolean, default: true },
    searchFields: { type: Array, default: () => [] }
});

const emit = defineEmits(['edit', 'delete']);

const searchQuery = ref('');

const filteredData = computed(() => {
    if (!searchQuery.value) return props.data;
    
    const query = searchQuery.value.toLowerCase();
    const fields = props.searchFields.length > 0 
        ? props.searchFields 
        : props.columns.map(c => c.key);

    return props.data.filter(item => {
        return fields.some(field => {
            const value = item[field];
            return value && String(value).toLowerCase().includes(query);
        });
    });
});
</script>
