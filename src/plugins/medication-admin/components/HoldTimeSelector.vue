<template>
    <div class="hold-selector">
      <div class="radio-option-group">
        <label class="radio-option">
          <input
            type="radio"
            v-model="holdType"
            value="all"
            name="holdType" />
          <span>Hold All Times</span>
        </label>
        <label class="radio-option">
          <input
            type="radio"
            v-model="holdType"
            value="specific"
            name="holdType" />
          <span>Hold Specific Times</span>
        </label>
      </div>
      <div v-if="holdType === 'specific'" class="times-selection-section">
        <div class="times-selection-header">Select times to hold:</div>
        <div class="times-selection-list">
          <label v-for="time in medicationTimes" :key="time" class="time-checkbox-label">
            <input
              type="checkbox"
              :value="time"
              v-model="selectedTimes" />
            <span>{{ time }}</span>
          </label>
        </div>
      </div>
      <Datepicker
        v-model="dateValue"
        :enable-time-picker="false"
        placeholder="Select hold date range"
        range
        auto-apply
        text-input
        class="status-datepicker" />
      <input
        v-model="reasonValue"
        type="text"
        placeholder="Enter reason for hold"
        class="status-reason" />
      <div class="action-buttons">
        <button
          class="submit-button"
          :class="{ 'submit-button-enabled': isValid }"
          :disabled="!isValid"
          @click="handleSubmit">
          Submit
        </button>
        <button class="cancel-button" @click="$emit('cancel')">
          Cancel
        </button>
      </div>
    </div>
  </template>
  <script setup lang="ts">
  import { ref, computed } from 'vue';
  import Datepicker from '@vuepic/vue-datepicker';
  import '@vuepic/vue-datepicker/dist/main.css';
  const props = defineProps<{
    medicationTimes: string[];
  }>();
  const emit = defineEmits<{
    (e: 'submit', data: {
      dateRange: [Date, Date];
      times: string[] | null;
      reason: string;
      holdType: 'all' | 'specific';
    }): void;
    (e: 'cancel'): void;
  }>();
  const holdType = ref<'all' | 'specific'>('all');
  const dateValue = ref<[Date, Date] | null>(null);
  const selectedTimes = ref<string[]>([]);
  const reasonValue = ref('');
  const isValid = computed(() => {
    if (!dateValue.value || !dateValue.value[0] || !dateValue.value[1] || !reasonValue.value.trim()) {
      return false;
    }
    if (holdType.value === 'specific' && selectedTimes.value.length === 0) {
      return false;
    }
    return true;
  });
  function handleSubmit() {
    if (!isValid.value || !dateValue.value) return;
    emit('submit', {
      dateRange: dateValue.value,
      times: holdType.value === 'specific' ? selectedTimes.value : null,
      reason: reasonValue.value.trim(),
      holdType: holdType.value
    });
    // Reset form
    dateValue.value = null;
    selectedTimes.value = [];
    reasonValue.value = '';
    holdType.value = 'all';
  }
  </script>
  <style scoped>
  .hold-selector {
    background: white;
    border-radius: 8px;
    padding: 16px;
    display: flex;
    flex-direction: column;
    gap: 16px;
  }
  .radio-option-group {
    display: flex;
    flex-direction: column;
    gap: 10px;
    padding: 12px;
    background-color: #F8F9FA;
    border-radius: 8px;
  }
  .radio-option {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    font-size: 16px;
    color: #333;
  }
  .radio-option input[type="radio"] {
    width: 18px;
    height: 18px;
    accent-color: #0C8687;
  }
  .times-selection-section {
    padding: 12px;
    background-color: #F8F9FA;
    border-radius: 8px;
  }
  .times-selection-header {
    margin: 0 0 12px 0;
    font-size: 16px;
    font-weight: 500;
    color: #333;
  }
  .times-selection-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
    max-height: 200px;
    overflow-y: auto;
  }
  .time-checkbox-label {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    font-size: 16px;
    color: #333;
  }
  .time-checkbox-label input[type="checkbox"] {
    width: 18px;
    height: 18px;
    accent-color: #0C8687;
  }
  .status-datepicker {
    width: 100%;
  }
  .status-reason {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
  }
  .status-reason:focus {
    outline: none;
    border-color: #0C8687;
    box-shadow: 0 0 0 2px rgba(12, 134, 135, 0.1);
  }
  .action-buttons {
    display: flex;
    justify-content: flex-end;
    gap: 8px;
  }
  .submit-button,
  .cancel-button {
    padding: 8px 16px;
    border: none;
    border-radius: 4px;
    font-weight: 500;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.2s ease;
  }
  .submit-button {
    background-color: #ccc;
    color: white;
    cursor: not-allowed;
  }
  .submit-button-enabled {
    background-color: #0C8687;
    cursor: pointer;
  }
  .submit-button-enabled:hover {
    background-color: #0A7273;
  }
  .cancel-button {
    background-color: #F8F9FA;
    color: #333;
  }
  .cancel-button:hover {
    background-color: #E9ECEF;
  }
  :deep(.dp__input) {
    padding: 8px 12px !important;
    border: 1px solid #ddd !important;
    border-radius: 4px !important;
    font-size: 14px !important;
    color: #212529 !important;
    background-color: #fff !important;
    height: 38px !important;
  }
  :deep(.dp__input:focus) {
    outline: none !important;
    border-color: #0C8687 !important;
    box-shadow: 0 0 0 2px rgba(12, 134, 135, 0.1) !important;
  }
  </style>