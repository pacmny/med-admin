<template>
  <div class="hold-selector">
    <!-- Title changes based on statusOption -->
    <h2 class="hold-title">
      {{ 
        statusOption === 'new'
          ? 'New Medication'
          : statusOption === 'discontinue'
            ? 'Discontinue Medication'
            : 'Hold Medication' 
      }}
    </h2>

    <!-- "All Times" or "Specific Times" -->
    <div class="radio-option-group">
      <label class="radio-option">
        <input
          type="radio"
          v-model="timeSelection"
          value="all"
          name="timeSelection"
        />
        <span>
          {{ 
            statusOption === 'new'
              ? 'New All Times'
              : statusOption === 'discontinue'
                ? 'Discontinue All Times'
                : 'Hold All Times'
          }}
        </span>
      </label>
      <label class="radio-option">
        <input
          type="radio"
          v-model="timeSelection"
          value="specific"
          name="timeSelection"
        />
        <span>
          {{ 
            statusOption === 'new'
              ? 'New Specific Times'
              : statusOption === 'discontinue'
                ? 'Discontinue Specific Times'
                : 'Hold Specific Times'
          }}
        </span>
      </label>
    </div>

    <!-- If user picks "Specific Times," show checkboxes -->
    <div v-if="timeSelection === 'specific'" class="times-selection-section">
      <div class="times-selection-header">
        {{ 
          statusOption === 'new'
            ? 'Select times to mark as New:'
            : statusOption === 'discontinue'
              ? 'Select times to discontinue:'
              : 'Select times to hold:' 
        }}
      </div>
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

    <!-- Single Date input -->
    <Datepicker
      v-model="selectedDate"
      placeholder="Select date"
      auto-apply
      text-input
      class="status-datepicker"
      multi-dates
      :format="'yyyy-mm-dd'"
      :show-time="false"
    />

    <!-- Reason input -->
    <input
      v-model="reasonValue"
      type="text"
      :placeholder="reasonPlaceholder"
      class="status-reason"
    />

    <!-- Nurse Signature input -->
    <input
      v-model="nurseSignature"
      type="text"
      placeholder="Enter Nurse Signature"
      class="nurse-signature"
    />

    <!-- Submit/Cancel -->
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
import { ref, computed, defineProps, defineEmits } from 'vue'
import Datepicker from '@vuepic/vue-datepicker'
import '@vuepic/vue-datepicker/dist/main.css'

/**
 * Props:
 *  - statusOption: 'hold' | 'new' | 'discontinue'
 *  - medicationTimes: e.g. ["09:30","12:05"] 
 */
const props = defineProps<{
  statusOption: 'hold' | 'new' | 'discontinue';
  medicationTimes: string[];
}>()

/**
 * Emits:
 *  - "submit" => user clicked Submit with 
 *                { dateRange, times, reason, holdType, statusOption }.
 *  - "cancel" => user clicked Cancel.
 */
const emit = defineEmits<{
  (e: 'submit', data: {
    dateRange: [Date, Date];
    times: string[] | null;
    reason: string;
    holdType: 'all' | 'specific';
    statusOption: 'hold' | 'new' | 'discontinue';
  }): void;
  (e: 'cancel'): void;
}>()

// "all" => all times on that date, "specific" => only selected
const timeSelection = ref<'all' | 'specific'>('all')

// Single date
//const selectedDate = ref<Date | null>(null)
//multiple dates 
const selectedDate = ref<Date[]>([]); // Use an array to hold multiple dates
// Times user checked if timeSelection='specific'
const selectedTimes = ref<string[]>([])

// Reason
const reasonValue = ref('')

// Nurse signature
const nurseSignature = ref('')

// Dynamic placeholder text
const reasonPlaceholder = computed(() => {
  if (props.statusOption === 'new') {
    return 'Enter reason for new'
  } else if (props.statusOption === 'discontinue') {
    return 'Enter reason for discontinue'
  }
  return 'Enter reason for hold'
})

// Form is valid if we have a date, reason, nurse signature, and times (if "specific")
const isValid = computed(() => {
  if (!selectedDate.value || !reasonValue.value.trim() || !nurseSignature.value.trim()) {
    return false
  }
  if (timeSelection.value === 'specific' && selectedTimes.value.length === 0) {
    return false
  }
  return true
})

function handleSubmit() {
  if (!isValid.value || !selectedDate.value) return

  // Single day => wrap in [start,end] for the parent
  const dateRange: [Date, Date] = [ selectedDate.value[0], selectedDate.value[1] ]

  emit('submit', {
    dateRange,
    times: (timeSelection.value === 'specific') ? selectedTimes.value : null,
    reason: reasonValue.value.trim(),
    holdType: timeSelection.value,
    statusOption: props.statusOption,
    nurseSignature: nurseSignature.value.trim()
  })

  // Reset
  selectedDate.value = [];//null
  selectedTimes.value = []
  reasonValue.value = ''
  nurseSignature.value = ''
  timeSelection.value = 'all'
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

/* Times checkboxes if 'specific' */
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

/* Reason + Nurse signature */
.status-reason,
.nurse-signature {
  width: 100%;
  padding: 8px 12px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
  margin-top: -4px;
}
.status-reason:focus,
.nurse-signature:focus {
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
  color: #fff;
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
