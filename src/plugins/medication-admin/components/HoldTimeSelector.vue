<template>
    <div class="hold-selector">
      <h2 class="hold-title">Hold Medication</h2>
  
      <!-- Radio buttons for "Hold All Times" vs. "Hold Specific Times" -->
      <div class="radio-option-group">
        <label class="radio-option">
          <input
            type="radio"
            v-model="holdType"
            value="all"
            name="holdType"
          />
          <span>Hold All Times</span>
        </label>
        <label class="radio-option">
          <input
            type="radio"
            v-model="holdType"
            value="specific"
            name="holdType"
          />
          <span>Hold Specific Times</span>
        </label>
      </div>
  
      <!-- If "Hold Specific Times," show time checkboxes -->
      <div v-if="holdType === 'specific'" class="times-selection-section">
        <div class="times-selection-header">Select times to hold:</div>
        <div class="times-selection-list">
          <label
            v-for="time in medicationTimes"
            :key="time"
            class="time-checkbox-label"
          >
            <input
              type="checkbox"
              :value="time"
              v-model="selectedTimes"
            />
            <span>{{ time }}</span>
          </label>
        </div>
      </div>
  
      <!-- Single Date input (instead of a date range) -->
      <Datepicker
        v-model="selectedDate"
        :enable-time-picker="false"
        placeholder="Select hold date"
        auto-apply
        text-input
        class="status-datepicker"
      />
  
      <!-- Reason input -->
      <input
        v-model="reasonValue"
        type="text"
        placeholder="Enter reason for hold"
        class="status-reason"
      />
  
      <!-- Submit / Cancel -->
      <div class="action-buttons">
        <button
          class="submit-button"
          :class="{ 'submit-button-enabled': isValid }"
          :disabled="!isValid"
          @click="handleSubmit"
        >
          Submit
        </button>
        <button
          class="cancel-button"
          @click="$emit('cancel')"
        >
          Cancel
        </button>
      </div>
    </div>
  </template>
  
  <script setup lang="ts">
  import { ref, computed } from 'vue';
  import Datepicker from '@vuepic/vue-datepicker';
  import '@vuepic/vue-datepicker/dist/main.css';
  
  /**
   * Props:
   *  - medicationTimes: array of time strings like ["09:18","12:05"].
   */
  const props = defineProps<{
    medicationTimes: string[];
  }>();
  
  /**
   * Emits:
   *  - "submit" => user clicked Submit with { dateRange, times, reason, holdType }.
   *  - "cancel" => user clicked Cancel.
   */
  const emit = defineEmits<{
    (e: 'submit', data: {
      dateRange: [Date, Date];
      times: string[] | null;
      reason: string;
      holdType: 'all' | 'specific';
    }): void;
    (e: 'cancel'): void;
  }>();
  
  // "all" => hold every time on that date, "specific" => hold only checkboxes
  const holdType = ref<'all' | 'specific'>('all');
  
  // Single date selection (no multi?day range)
  const selectedDate = ref<Date | null>(null);
  
  // Times user checked if holdType='specific'
  const selectedTimes = ref<string[]>([]);
  
  // Reason for hold
  const reasonValue = ref('');
  
  // The form is valid if we have a selected date and reason, plus times if "specific"
  const isValid = computed(() => {
    if (!selectedDate.value || !reasonValue.value.trim()) {
      return false;
    }
    if (holdType.value === 'specific' && selectedTimes.value.length === 0) {
      return false;
    }
    return true;
  });
  
  // On Submit
  function handleSubmit() {
    if (!isValid.value || !selectedDate.value) return;
  
    // We wrap selectedDate in an array for 'dateRange' 
    // so the parent can handle it consistently.
    const dateRange: [Date, Date] = [ selectedDate.value, selectedDate.value ];
  
    emit('submit', {
      dateRange,
      times: (holdType.value === 'specific') ? selectedTimes.value : null,
      reason: reasonValue.value.trim(),
      holdType: holdType.value
    });
  
    // Reset
    selectedDate.value = null;
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
  
  .hold-title {
    margin: 0;
    font-size: 1.2rem;
    color: #333;
  }
  
  /* Radio group styles */
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
  
  /* Times checkboxes if holdType='specific' */
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
  
  /* Datepicker override */
  .status-datepicker {
    width: 100%;
  }
  
  /* Reason input */
  .status-reason {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    margin-top: -4px;
  }
  .status-reason:focus {
    outline: none;
    border-color: #0C8687;
    box-shadow: 0 0 0 2px rgba(12, 134, 135, 0.1);
  }
  
  /* Buttons */
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
  
  /* Overriding vue-datepicker input with :deep */
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