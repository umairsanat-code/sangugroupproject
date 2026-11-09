<template>
    <div class="space-y-6">
        <div class="flex justify-between items-end">
            <div>
                <h1 class="text-3xl font-black text-slate-900 tracking-tight">Flight Inventory</h1>
                <p class="text-sm text-slate-500 font-medium mt-1">Manage global flight schedules and inventory capacity.</p>
            </div>
            <button class="btn-premium-primary gap-2" @click="openModal()">
                <span>+</span> Register New Flight
            </button>
        </div>

        <DataTable 
            title="Real-time Flight Status"
            subtitle="Synchronized with global aviation databases"
            :columns="columns"
            :data="flights"
            :searchFields="['flight_number', 'departure_city', 'arrival_city']"
            @edit="openModal"
            @delete="deleteFlight"
        >
            <template #cell(flight_number)="{ item }">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 shadow-sm border border-blue-100">
                        <i class="fas fa-plane"></i>
                    </div>
                    <div>
                        <div class="font-bold text-slate-900">{{ item.flight_number }}</div>
                        <div class="text-[10px] font-black uppercase tracking-widest text-slate-400 mt-0.5">{{ item.airline?.name || 'Unknown Carrier' }}</div>
                    </div>
                </div>
            </template>

            <template #cell(route)="{ item }">
                <div class="flex items-center gap-3 font-bold text-slate-700">
                    <span class="px-2 py-1 bg-slate-100 rounded text-[10px] tracking-tighter uppercase">{{ item.departure_city }}</span>
                    <span class="text-slate-300">→</span>
                    <span class="px-2 py-1 bg-slate-100 rounded text-[10px] tracking-tighter uppercase">{{ item.arrival_city }}</span>
                </div>
            </template>

            <template #cell(departure_time)="{ item }">
                <div class="text-xs font-medium">
                    <div class="text-slate-900">{{ formatDate(item.departure_time) }}</div>
                    <div class="text-slate-400 font-bold uppercase tracking-widest mt-0.5">{{ formatTime(item.departure_time) }}</div>
                </div>
            </template>

            <template #cell(status)="{ item }">
                <span class="badge badge-success">Scheduled</span>
            </template>
        </DataTable>

        <!-- Upsert Modal -->
        <Modal 
            :show="showModal" 
            :title="form.id ? 'Refine Flight Data' : 'Authorize New Flight'"
            :subtitle="form.id ? `Managing Flight #${form.flight_number}` : 'Inject a new flight route into the inventory system'"
            size="lg"
            @close="showModal = false"
        >
            <form @submit.prevent="saveFlight" class="space-y-8">
                <div class="grid grid-cols-2 gap-8">
                    <div class="space-y-6">
                        <div class="grid grid-cols-2 gap-6">
                            <BaseInput 
                                v-model="form.flight_number" 
                                label="Flight Number" 
                                placeholder="e.g. EK202" 
                                required 
                            />
                            <BaseInput 
                                v-model="form.airline_id" 
                                type="select"
                                label="Operating Carrier" 
                                required 
                            >
                                <option value="" disabled>Select Carrier</option>
                                <option v-for="a in airlines" :key="a.id" :value="a.id">{{ a.name }}</option>
                            </BaseInput>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-6 pt-4 border-t border-slate-100">
                            <BaseInput 
                                v-model="form.departure_city" 
                                label="Departure City" 
                                placeholder="E.g. Dhaka (DAC)" 
                                required 
                            />
                            <BaseInput 
                                v-model="form.arrival_city" 
                                label="Arrival City" 
                                placeholder="E.g. Jeddah (JED)" 
                                required 
                            />
                        </div>
                    </div>
                    
                    <div class="space-y-6">
                        <BaseInput 
                            v-model="form.departure_time" 
                            type="datetime-local"
                            label="Departure Schedule" 
                            required 
                        />
                        <BaseInput 
                            v-model="form.arrival_time" 
                            type="datetime-local"
                            label="Arrival Schedule" 
                            required 
                        />
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-6 border-t border-slate-100">
                    <button type="button" class="btn-premium-outline px-8" @click="showModal = false">Discard</button>
                    <button type="submit" class="btn-premium-primary px-12">
                        {{ form.id ? 'Update Schedule' : 'Commit Flight' }}
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

const flights = ref([]);
const airlines = ref([]);
const showModal = ref(false);
const form = ref({ 
    id: null, 
    flight_number: '', 
    airline_id: '', 
    departure_city: '', 
    arrival_city: '',
    departure_time: '',
    arrival_time: ''
});

const columns = [
    { key: 'flight_number', label: 'Flight Identifier' },
    { key: 'route', label: 'Flight Path' },
    { key: 'departure_time', label: 'Schedule' },
    { key: 'status', label: 'Operational Status' }
];

const fetchFlights = async () => {
    try {
        const res = await axios.get('/api/admin/flights');
        flights.value = res.data;
    } catch (e) {
        console.error("API Failure:", e);
    }
}

const fetchAirlines = async () => {
    try {
        const res = await axios.get('/api/admin/airlines'); // Assuming this exists or returns []
        airlines.value = res.data;
    } catch (e) {
        console.error("Airlines API Failure:", e);
    }
}

const deleteFlight = async (id) => {
    if(!confirm('Permanent deletion of flight record?')) return;
    try {
        await axios.delete(`/api/admin/flights/${id}`);
        fetchFlights();
    } catch (e) {
        console.error("Deletion Failure:", e);
    }
}

const openModal = (flight = null) => {
    if (flight) {
        form.value = { 
            ...flight,
            departure_time: flight.departure_time ? flight.departure_time.slice(0, 16) : '',
            arrival_time: flight.arrival_time ? flight.arrival_time.slice(0, 16) : ''
        };
    } else {
        form.value = { 
            id: null, 
            flight_number: '', 
            airline_id: '', 
            departure_city: '', 
            arrival_city: '',
            departure_time: '',
            arrival_time: ''
        };
    }
    showModal.value = true;
}

const saveFlight = async () => {
    try {
        if (form.value.id) {
            await axios.put(`/api/admin/flights/${form.value.id}`, form.value);
        } else {
            await axios.post('/api/admin/flights', form.value);
        }
        showModal.value = false;
        fetchFlights();
    } catch (e) {
        console.error("Save Failure:", e);
    }
}

const formatDate = (dateStr) => {
    return new Date(dateStr).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
}

const formatTime = (dateStr) => {
    return new Date(dateStr).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
}

onMounted(() => {
    fetchFlights();
    fetchAirlines();
});
</script>
