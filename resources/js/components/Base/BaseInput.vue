<template>
    <div class="space-y-2">
        <label v-if="label" class="block text-[11px] font-bold uppercase tracking-wider text-text-dark ml-1">
            {{ label }}
        </label>
        <div class="relative group">
            <template v-if="type === 'textarea'">
                <textarea 
                    :value="modelValue" 
                    @input="$emit('update:modelValue', $event.target.value)"
                    :placeholder="placeholder"
                    rows="4"
                    class="w-full px-5 py-3.5 rounded-xl border border-border bg-white text-text-dark placeholder:text-text-muted focus:border-primary focus:ring-4 focus:ring-primary/5 transition-all outline-none resize-none"
                    v-bind="$attrs"
                ></textarea>
            </template>
            <template v-else-if="type === 'select'">
                <select 
                    :value="modelValue" 
                    @change="$emit('update:modelValue', $event.target.value)"
                    class="w-full h-[54px] px-5 rounded-xl border border-border bg-white text-text-dark placeholder:text-text-muted focus:border-primary focus:ring-4 focus:ring-primary/5 transition-all outline-none appearance-none bg-[url('data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20fill%3D%22none%22%20viewBox%3D%220%200%2020%2020%22%3E%3Cpath%20stroke%3D%22%231A2B48%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%20stroke-width%3D%221.5%22%20d%3D%22m6%208%204%204%204-4%22%2F%3E%3C%2Fsvg%3E')] bg-[length:1.25rem_1.25rem] bg-[right_1rem_center] bg-no-repeat"
                    v-bind="$attrs"
                >
                    <slot></slot>
                </select>
            </template>
            <template v-else>
                <input 
                    :type="type" 
                    :value="modelValue" 
                    @input="$emit('update:modelValue', $event.target.value)"
                    :placeholder="placeholder"
                    class="w-full h-[54px] px-5 rounded-xl border border-border bg-white text-text-dark placeholder:text-text-muted focus:border-primary focus:ring-4 focus:ring-primary/5 transition-all outline-none"
                    v-bind="$attrs"
                >
            </template>
        </div>
        <p v-if="helpText" class="text-[10px] font-medium text-slate-400 ml-1 italic">{{ helpText }}</p>
    </div>
</template>

<script setup>
const props = defineProps({
    modelValue: [String, Number, Boolean],
    label: String,
    type: { type: String, default: 'text' },
    placeholder: String,
    helpText: String
});

const emit = defineEmits(['update:modelValue']);
</script>
