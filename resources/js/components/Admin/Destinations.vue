<template>
    <div class="space-y-6">
        <div class="flex justify-between items-end">
            <div>
                <h1 class="text-3xl font-black text-slate-900 tracking-tight">Global Destinations</h1>
                <p class="text-sm text-slate-500 font-medium mt-1">Manage geographical focal points and SEO-optimized travel content.</p>
            </div>
            <button class="btn-premium-primary gap-2" @click="openModal()">
                <span>+</span> Map New Destination
            </button>
        </div>

        <DataTable 
            title="Geographical Registry"
            subtitle="Strategic travel hubs across the SanguGroup network"
            :columns="columns"
            :data="destinations"
            :searchFields="['name', 'country', 'slug']"
            @edit="openModal"
            @delete="deleteDestination"
        >
            <template #cell(name)="{ item }">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl overflow-hidden bg-slate-100 border border-slate-100 shrink-0">
                        <img v-if="item.image_path" :src="item.image_path" class="w-full h-full object-cover">
                        <div v-else class="w-full h-full flex items-center justify-center text-slate-300"><i class="fas fa-globe"></i></div>
                    </div>
                    <div>
                        <div class="font-bold text-slate-900">{{ item.name }}</div>
                        <div class="text-[10px] font-black uppercase tracking-widest text-slate-400 mt-0.5">/{{ item.slug }}</div>
                    </div>
                </div>
            </template>

            <template #cell(is_featured)="{ item }">
                <span :class="item.is_featured ? 'badge-warning' : 'badge-info'" class="badge">
                    {{ item.is_featured ? 'Featured' : 'Standard' }}
                </span>
            </template>

            <template #cell(country)="{ item }">
                <span class="font-bold text-slate-700 uppercase tracking-tighter text-xs">{{ item.country }}</span>
            </template>
        </DataTable>

        <!-- Upsert Modal -->
        <Modal 
            :show="showModal" 
            :title="form.id ? 'Refine Destination' : 'Chart New Territory'"
            :subtitle="form.id ? `Editing locale: ${form.name}` : 'Document a new destination for the global travel map'"
            size="lg"
            @close="showModal = false"
        >
            <form @submit.prevent="saveDestination" class="space-y-8">
                <div class="grid grid-cols-2 gap-8">
                    <div class="space-y-6">
                        <BaseInput 
                            v-model="form.name" 
                            label="Destination Name" 
                            placeholder="e.g. Santorini" 
                            @input="generateSlug"
                            required 
                        />
                        <BaseInput 
                            v-model="form.slug" 
                            label="URL Identifier (Slug)" 
                            placeholder="e.g. santorini-greece" 
                            required 
                        />
                        <BaseInput 
                            v-model="form.country" 
                            label="Primary Country" 
                            placeholder="e.g. Greece" 
                            required 
                        />
                    </div>
                    
                    <div class="space-y-6">
                        <BaseInput 
                            v-model="form.image_path" 
                            label="Asset Path (Image URL)" 
                            placeholder="/images/dest/santorini.jpg" 
                        />
                        <div class="flex items-center gap-4 p-4 bg-slate-50 rounded-2xl border border-slate-100">
                            <input 
                                type="checkbox" 
                                id="is_featured" 
                                v-model="form.is_featured"
                                class="w-5 h-5 rounded-lg border-slate-200 text-secondary focus:ring-secondary/20 transition-all cursor-pointer"
                            >
                            <label for="is_featured" class="text-[10px] font-black uppercase tracking-widest text-slate-600 cursor-pointer">
                                Mark as Featured Destination
                            </label>
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-100">
                    <BaseInput 
                        v-model="form.description" 
                        type="textarea"
                        label="Destination SEO Narrative" 
                        placeholder="Craft a compelling story for this location..." 
                    />
                </div>

                <div class="flex justify-end gap-3 pt-6 border-t border-slate-100">
                    <button type="button" class="btn-premium-outline px-8" @click="showModal = false">Cancel</button>
                    <button type="submit" class="btn-premium-primary px-12">
                        {{ form.id ? 'Authorize Updates' : 'Commit Destination' }}
                    </button>
                </div>
            </form>
        </Modal>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import DataTable from '../Base/DataTable.vue';
import Modal from '../Base/Modal.vue';
import BaseInput from '../Base/BaseInput.vue';

const destinations = ref([]);
const showModal = ref(false);
const form = ref({ 
    id: null, 
    name: '', 
    slug: '', 
    country: '', 
    description: '', 
    image_path: '',
    is_featured: false
});

const columns = [
    { key: 'name', label: 'Identity' },
    { key: 'country', label: 'Nation' },
    { key: 'is_featured', label: 'Status' }
];

const fetchDestinations = async () => {
    try {
        const res = await axios.get('/api/admin/destinations');
        destinations.value = res.data;
    } catch (e) {
        console.error("API Failure:", e);
    }
}

const deleteDestination = async (id) => {
    if(!confirm('Permanently remove this destination from the global map?')) return;
    try {
        await axios.delete(`/api/admin/destinations/${id}`);
        fetchDestinations();
    } catch (e) {
        console.error("Deletion Failure:", e);
    }
}

const generateSlug = () => {
    if(!form.value.id) {
        form.value.slug = form.value.name
            .toLowerCase()
            .replace(/[^\w ]+/g, '')
            .replace(/ +/g, '-');
    }
}

const openModal = (dest = null) => {
    if (dest) {
        form.value = { ...dest, is_featured: !!dest.is_featured };
    } else {
        form.value = { 
            id: null, 
            name: '', 
            slug: '', 
            country: '', 
            description: '', 
            image_path: '',
            is_featured: false
        };
    }
    showModal.value = true;
}

const saveDestination = async () => {
    try {
        if (form.value.id) {
            await axios.put(`/api/admin/destinations/${form.value.id}`, form.value);
        } else {
            await axios.post('/api/admin/destinations', form.value);
        }
        showModal.value = false;
        fetchDestinations();
    } catch (e) {
        console.error("Save Failure:", e);
    }
}

onMounted(() => {
    fetchDestinations();
});
</script>
