<template>
    <div class="space-y-6">
        <div class="flex justify-between items-end">
            <div>
                <h1 class="text-3xl font-black text-slate-900 tracking-tight">Luxury Hotels</h1>
                <p class="text-sm text-slate-500 font-medium mt-1">Manage premium property partnerships and spatial inventory.</p>
            </div>
            <button class="btn-premium-primary gap-2" @click="openModal()">
                <span>+</span> Onboard Property
            </button>
        </div>

        <DataTable 
            title="Global Property Ledger"
            subtitle="Curated collection of high-end accommodations"
            :columns="columns"
            :data="hotels"
            :searchFields="['name', 'city', 'location']"
            @edit="openModal"
            @delete="deleteHotel"
        >
            <template #cell(name)="{ item }">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center font-bold text-indigo-600 shadow-sm border border-indigo-100 uppercase">
                        {{ item.name[0] }}
                    </div>
                    <div>
                        <div class="font-bold text-slate-900">{{ item.name }}</div>
                        <div class="text-[10px] font-black uppercase tracking-widest text-slate-400 mt-0.5">{{ item.location || 'Prime Location' }}</div>
                    </div>
                </div>
            </template>

            <template #cell(stars)="{ item }">
                <div class="flex gap-0.5 text-secondary text-[10px]">
                    <span v-for="n in item.stars" :key="n">★</span>
                    <span v-for="n in (5 - item.stars)" :key="n" class="text-slate-200">★</span>
                </div>
            </template>

            <template #cell(city)="{ item }">
                <span class="font-bold text-slate-700 uppercase tracking-tighter text-xs">{{ item.city }}</span>
            </template>

            <template #cell(vendor)="{ item }">
                <div class="text-xs font-medium text-slate-500">
                    {{ item.vendor?.name || 'In-House' }}
                </div>
            </template>
        </DataTable>

        <!-- Upsert Modal -->
        <Modal 
            :show="showModal" 
            :title="form.id ? 'Refine Property Specs' : 'Initialize Property Profile'"
            :subtitle="form.id ? `Editing: ${form.name}` : 'Document a new luxury accommodation for the inventory'"
            @close="showModal = false"
        >
            <form @submit.prevent="saveHotel" class="space-y-8">
                <div class="space-y-6">
                    <BaseInput 
                        v-model="form.name" 
                        label="Property Name" 
                        placeholder="e.g. Burj Al Arab Jumeirah" 
                        required 
                    />
                    
                    <div class="grid grid-cols-2 gap-6">
                        <BaseInput 
                            v-model="form.city" 
                            label="Target City" 
                            placeholder="e.g. Dubai" 
                            required 
                        />
                        <BaseInput 
                            v-model="form.stars" 
                            type="select"
                            label="Star Rating" 
                            required 
                        >
                            <option value="1">1 Star</option>
                            <option value="2">2 Stars</option>
                            <option value="3">3 Stars</option>
                            <option value="4">4 Stars</option>
                            <option value="5">5 Stars</option>
                        </BaseInput>
                    </div>

                    <BaseInput 
                        v-model="form.location" 
                        label="Precise Location / Address" 
                        placeholder="E.g. Jumeirah St - Umm Suqeim 3..." 
                    />

                    <BaseInput 
                        v-model="form.vendor_id" 
                        type="select"
                        label="Property Vendor / Manager" 
                    >
                        <option value="">No Vendor (In-House)</option>
                        <option v-for="v in vendors" :key="v.id" :value="v.id">{{ v.name }}</option>
                    </BaseInput>
                </div>

                <div class="flex justify-end gap-3 pt-6 border-t border-slate-100">
                    <button type="button" class="btn-premium-outline px-8" @click="showModal = false">Cancel</button>
                    <button type="submit" class="btn-premium-primary px-12">
                        {{ form.id ? 'Authorize Updates' : 'Commit Property' }}
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

const hotels = ref([]);
const vendors = ref([]);
const showModal = ref(false);
const form = ref({ 
    id: null, 
    name: '', 
    city: '', 
    location: '', 
    stars: 5,
    vendor_id: ''
});

const columns = [
    { key: 'name', label: 'Property Brand' },
    { key: 'city', label: 'Geography' },
    { key: 'stars', label: 'Elite Class' },
    { key: 'vendor', label: 'Management' }
];

const fetchHotels = async () => {
    try {
        const res = await axios.get('/api/admin/hotels');
        hotels.value = res.data;
    } catch (e) {
        console.error("API Failure:", e);
    }
}

const fetchVendors = async () => {
    try {
        const res = await axios.get('/api/admin/vendors');
        vendors.value = res.data;
    } catch (e) {
        console.error("Vendors API Failure:", e);
    }
}

const deleteHotel = async (id) => {
    if(!confirm('This will purge the property from global inventory. Confirm?')) return;
    try {
        await axios.delete(`/api/admin/hotels/${id}`);
        fetchHotels();
    } catch (e) {
        console.error("Deletion Failure:", e);
    }
}

const openModal = (hotel = null) => {
    if (hotel) {
        form.value = { ...hotel };
    } else {
        form.value = { 
            id: null, 
            name: '', 
            city: '', 
            location: '', 
            stars: 5,
            vendor_id: ''
        };
    }
    showModal.value = true;
}

const saveHotel = async () => {
    try {
        if (form.value.id) {
            await axios.put(`/api/admin/hotels/${form.value.id}`, form.value);
        } else {
            await axios.post('/api/admin/hotels', form.value);
        }
        showModal.value = false;
        fetchHotels();
    } catch (e) {
        console.error("Save Failure:", e);
    }
}

onMounted(() => {
    fetchHotels();
    fetchVendors();
});
</script>
