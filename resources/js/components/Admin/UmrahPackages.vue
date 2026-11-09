<template>
    <div class="space-y-6">
        <div class="flex justify-between items-end">
            <div>
                <h1 class="text-3xl font-black text-slate-900 tracking-tight">Umrah Programs</h1>
                <p class="text-sm text-slate-500 font-medium mt-1">Orchestrate spiritual journeys with precision-crafted packages.</p>
            </div>
            <button class="btn-premium-primary gap-2" @click="openModal()">
                <span>+</span> Launch New Program
            </button>
        </div>

        <DataTable 
            title="Active Inventory"
            subtitle="Spiritual packages currently available for booking"
            :columns="columns"
            :data="packages"
            :searchFields="['title', 'description', 'hotel_details']"
            @edit="openModal"
            @delete="deletePackage"
        >
            <template #cell(title)="{ item }">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600 shadow-sm border border-amber-100">
                        <i class="fas fa-mosque"></i>
                    </div>
                    <div>
                        <div class="font-bold text-slate-900">{{ item.title }}</div>
                        <div class="text-[10px] font-black uppercase tracking-widest text-slate-400 mt-0.5">ID: #{{ item.id }}</div>
                    </div>
                </div>
            </template>

            <template #cell(duration_days)="{ item }">
                <div class="flex items-center gap-2">
                    <span class="text-slate-400">📅</span>
                    <span class="font-bold text-slate-700">{{ item.duration_days }} Days</span>
                </div>
            </template>

            <template #cell(price)="{ item }">
                <span class="font-black text-slate-900">${{ parseFloat(item.price).toLocaleString() }}</span>
            </template>

            <template #cell(visa_included)="{ item }">
                <span :class="item.visa_included ? 'badge-success' : 'badge-danger'" class="badge">
                    {{ item.visa_included ? 'Visa Included' : 'Visa Excluded' }}
                </span>
            </template>
        </DataTable>

        <!-- Upsert Modal -->
        <Modal 
            :show="showModal" 
            :title="form.id ? 'Modify Program' : 'Initialize Program'"
            :subtitle="form.id ? `Adjusting parameters for: ${form.title}` : 'Define the components of a new spiritual journey'"
            size="lg"
            @close="showModal = false"
        >
            <form @submit.prevent="savePackage" class="space-y-8">
                <div class="grid grid-cols-2 gap-8">
                    <div class="space-y-6">
                        <BaseInput 
                            v-model="form.title" 
                            label="Program Title" 
                            placeholder="e.g. Premium Ramadan Suite" 
                            required 
                        />
                        <div class="grid grid-cols-2 gap-6">
                            <BaseInput 
                                v-model="form.duration_days" 
                                type="number"
                                label="Duration (Days)" 
                                placeholder="14" 
                                required 
                            />
                            <BaseInput 
                                v-model="form.price" 
                                type="number"
                                step="0.01"
                                label="Base Price (USD)" 
                                placeholder="2450.00" 
                                required 
                            />
                        </div>
                        <div class="flex items-center gap-4 p-4 bg-slate-50 rounded-2xl border border-slate-100">
                            <input 
                                type="checkbox" 
                                id="visa_included" 
                                v-model="form.visa_included"
                                class="w-5 h-5 rounded-lg border-slate-200 text-secondary focus:ring-secondary/20 transition-all cursor-pointer"
                            >
                            <label for="visa_included" class="text-[10px] font-black uppercase tracking-widest text-slate-600 cursor-pointer">
                                Include Visa Processing
                            </label>
                        </div>
                    </div>
                    
                    <div class="space-y-6">
                        <BaseInput 
                            v-model="form.hotel_details" 
                            type="textarea"
                            label="Accommodation Specs" 
                            placeholder="Detail hotel names and star ratings..." 
                        />
                        <BaseInput 
                            v-model="form.transport_details" 
                            type="textarea"
                            label="Logistics & Transport" 
                            placeholder="Detail air and ground transportation..." 
                        />
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-100">
                    <BaseInput 
                        v-model="form.description" 
                        type="textarea"
                        label="Program Overview (Marketing)" 
                        placeholder="Provide an enticing description for travelers..." 
                    />
                </div>

                <div class="flex justify-end gap-3 pt-6 border-t border-slate-100">
                    <button type="button" class="btn-premium-outline px-8" @click="showModal = false">Discard</button>
                    <button type="submit" class="btn-premium-primary px-12">
                        {{ form.id ? 'Save Changes' : 'Publish Program' }}
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

const packages = ref([]);
const showModal = ref(false);
const form = ref({ 
    id: null, 
    title: '', 
    duration_days: '', 
    price: '', 
    hotel_details: '',
    transport_details: '',
    visa_included: false,
    description: ''
});

const columns = [
    { key: 'title', label: 'Package' },
    { key: 'duration_days', label: 'Duration' },
    { key: 'price', label: 'Price' },
    { key: 'visa_included', label: 'Visa' }
];

const fetchPackages = async () => {
    try {
        const res = await axios.get('/api/admin/umrah-packages');
        packages.value = res.data;
    } catch (e) {
        console.error("API Failure:", e);
    }
}

const deletePackage = async (id) => {
    if(!confirm('This action cannot be undone. Permanent deletion?')) return;
    try {
        await axios.delete(`/api/admin/umrah-packages/${id}`);
        fetchPackages();
    } catch (e) {
        console.error("Deletion Failure:", e);
    }
}

const openModal = (pkg = null) => {
    if (pkg) {
        form.value = { ...pkg, visa_included: !!pkg.visa_included };
    } else {
        form.value = { 
            id: null, 
            title: '', 
            duration_days: '', 
            price: '', 
            hotel_details: '',
            transport_details: '',
            visa_included: false,
            description: ''
        };
    }
    showModal.value = true;
}

const savePackage = async () => {
    try {
        if (form.value.id) {
            await axios.put(`/api/admin/umrah-packages/${form.value.id}`, form.value);
        } else {
            await axios.post('/api/admin/umrah-packages', form.value);
        }
        showModal.value = false;
        fetchPackages();
    } catch (e) {
        console.error("Save Failure:", e);
    }
}

onMounted(() => {
    fetchPackages();
});
</script>
