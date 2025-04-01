<script setup lang="ts">
import { ref } from 'vue';
import { Plus, Minus } from 'lucide-vue-next';

interface Props {
  label: string;
  decrementTitle: string;
  initialValue?: number;
}

const props = withDefaults(defineProps<Props>(), {
  initialValue: 0
});

const count = ref(props.initialValue);
</script>

<template>
  <div class="flex flex-col items-center gap-4 bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-xl font-semibold text-gray-800">{{ label }}</h2>
    <div class="flex items-center gap-4">
      <button
        :title="decrementTitle"
        @click="count--"
        class="p-2 rounded-full bg-red-100 hover:bg-red-200 transition-colors"
        aria-label="Decrease"
      >
        <Minus class="w-5 h-5 text-red-600" />
      </button>
      <span class="text-3xl font-bold min-w-[3ch] text-center">{{ count }}</span>
      <button
        @click="count++"
        class="p-2 rounded-full bg-green-100 hover:bg-green-200 transition-colors"
        aria-label="Increase"
      >
        <Plus class="w-5 h-5 text-green-600" />
      </button>
    </div>
  </div>
</template>

<style scoped>
.flex {
  display: flex;
}

.flex-col {
  flex-direction: column;
}

.items-center {
  align-items: center;
}

.gap-4 {
  gap: 1rem;
}

.bg-white {
  background-color: white;
}

.p-6 {
  padding: 1.5rem;
}

.rounded-lg {
  border-radius: 0.5rem;
}

.shadow-md {
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

.text-xl {
  font-size: 1.25rem;
  line-height: 1.75rem;
}

.font-semibold {
  font-weight: 600;
}

.text-gray-800 {
  color: #1f2937;
}

.p-2 {
  padding: 0.5rem;
}

.rounded-full {
  border-radius: 9999px;
}

.bg-red-100 {
  background-color: #fee2e2;
}

.hover\:bg-red-200:hover {
  background-color: #fecaca;
}

.bg-green-100 {
  background-color: #dcfce7;
}

.hover\:bg-green-200:hover {
  background-color: #bbf7d0;
}

.transition-colors {
  transition-property: background-color, border-color, color, fill, stroke;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 150ms;
}

.text-red-600 {
  color: #dc2626;
}

.text-green-600 {
  color: #16a34a;
}

.text-3xl {
  font-size: 1.875rem;
  line-height: 2.25rem;
}

.font-bold {
  font-weight: 700;
}

.min-w-\[3ch\] {
  min-width: 3ch;
}

.text-center {
  text-align: center;
}

.w-5 {
  width: 1.25rem;
}

.h-5 {
  height: 1.25rem;
}
</style>