<template>
    <div class="space-y-6">
        <div class="flex justify-between items-end">
            <div>
                <h1 class="text-3xl font-black text-slate-900 tracking-tight">Client Ledger</h1>
                <p class="text-sm text-slate-500 font-medium mt-1">Manage your global customer database and travel profiles.</p>
            </div>
            <button class="btn-premium-primary gap-2" @click="openModal()">
                <span>+</span> Add New Client
            </button>
        </div>

        <DataTable 
            title="Customer Directory"
            subtitle="Real-time synchronization with SanguGroup CRM"
            :columns="columns"
            :data="customers"
            :searchFields="['first_name', 'last_name', 'email', 'phone', 'passport_number']"
            @edit="openModal"
            @delete="deleteCustomer"
        >
            <template #cell(first_name)="{ item }">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center font-bold text-slate-400 text-xs">
                        {{ item.first_name[0] }}{{ item.last_name[0] }}
                    </div>
                    <span class="font-bold text-slate-900">{{ item.first_name }} {{ item.last_name }}</span>
                </div>
            </template>
            
            <template #cell(email)="{ item }">
                <a :href="`mailto:${item.email}`" class="text-accent hover:underline font-medium">{{ item.email }}</a>
            </template>

            <template #cell(passport_number)="{ item }">
                <code class="px-2 py-1 bg-slate-50 rounded border border-slate-100 text-[10px] font-black uppercase text-slate-500">
                    {{ item.passport_number || 'N/A' }}
                </code>
            </template>
        </DataTable>

        <!-- Upsert Modal -->
        <Modal 
            :show="showModal" 
            :title="form.id ? 'Refine Profile' : 'Register New Client'"
            :subtitle="form.id ? `Editing profile ID: ${form.id}` : 'Complete the form to register a new traveler'"
            @close="showModal = false"
        >
            <form @submit.prevent="saveCustomer" class="space-y-6">
                <div class="grid grid-cols-2 gap-6">
                    <BaseInput 
                        v-model="form.first_name" 
                        label="First Name" 
                        placeholder="e.g. John" 
                        required 
                    />
                    <BaseInput 
                        v-model="form.last_name" 
                        label="Last Name" 
                        placeholder="e.g. Doe" 
                        required 
                    />
                </div>
                
                <div class="grid grid-cols-2 gap-6">
                    <BaseInput 
                        v-model="form.email" 
                        type="email" 
                        label="Email Address" 
                        placeholder="j.doe@example.com" 
                    />
                    <BaseInput 
                        v-model="form.phone" 
                        label="Primary Phone" 
                        placeholder="+880 1XXX-XXXXXX" 
                    />
                </div>

                <div class="pt-4 border-t border-slate-100">
                    <div class="grid grid-cols-2 gap-6">
                        <BaseInput 
                            v-model="form.passport_number" 
                            label="Passport Number" 
                            placeholder="E.g. A1234567" 
                            helpText="Required for international flight bookings"
                        />
                        <BaseInput 
                            v-model="form.passport_expiry" 
                            type="date" 
                            label="Passport Expiry" 
                        />
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-6 border-t border-slate-100">
                    <button type="button" class="btn-premium-outline" @click="showModal = false">Cancel</button>
                    <button type="submit" class="btn-premium-primary px-10">
                        {{ form.id ? 'Update Record' : 'Commit Registration' }}
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

const customers = ref([]);
const showModal = ref(false);
const form = ref({ 
    id: null, 
    first_name: '', 
    last_name: '', 
    email: '', 
    phone: '',
    passport_number: '',
    passport_expiry: ''
});

const columns = [
    { key: 'first_name', label: 'Identity' },
    { key: 'email', label: 'Communication' },
    { key: 'phone', label: 'Contact' },
    { key: 'passport_number', label: 'Passport' }
];

const fetchCustomers = async () => {
    try {
        const res = await axios.get('/api/admin/customers');
        customers.value = res.data;
    } catch (e) {
        console.error("API Failure:", e);
    }
}

const deleteCustomer = async (id) => {
    if(!confirm('Are you sure you want to permanently delete this record?')) return;
    try {
        await axios.delete(`/api/admin/customers/${id}`);
        fetchCustomers();
    } catch (e) {
        console.error("Deletion Failure:", e);
    }
}

const openModal = (customer = null) => {
    if (customer) {
        form.value = { ...customer };
    } else {
        form.value = { 
            id: null, 
            first_name: '', 
            last_name: '', 
            email: '', 
            phone: '',
            passport_number: '',
            passport_expiry: ''
        };
    }
    showModal.value = true;
}

const saveCustomer = async () => {
    try {
        if (form.value.id) {
            await axios.put(`/api/admin/customers/${form.value.id}`, form.value);
        } else {
            await axios.post('/api/admin/customers', form.value);
        }
        showModal.value = false;
        fetchCustomers();
    } catch (e) {
        console.error("Save Failure:", e);
    }
}

onMounted(() => {
    fetchCustomers();
});
</script>
