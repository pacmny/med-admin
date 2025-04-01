<script setup lang="ts">
import { ref } from 'vue';
import type { Medication } from '../types';

const props = defineProps<{
  show: boolean;
  medication: Medication | null;
}>();

const emit = defineEmits<{
  (e: 'close'): void;
  (e: 'save', medication: Medication): void;
}>();

const selectedFrequency = ref('');
const selectedDosage = ref('1');
const selectedTimes = ref<string[]>([]);

const routeOptions = [
  'Prepour',
  'Administration',
  'PRN',
  'RN Admin',
  'Neb/INH',
  'Oral/Sublingual',
  'IVI Intravaginal',
  'SQ/IM/IV/ID',
  'NAS Intranasal',
  'TD Transdermal',
  'TOP Topical',
  'Urethral',
  'Rectally',
  'Optic',
  'Otic'
];

const dosageFormOptions = [
  'Applicator',
  'Blister',
  'Caplet',
  'Capsule',
  'Each',
  'Film',
  'Gram',
  'Gum',
  'Implant',
  'Insert',
  'Kit',
  'Lancet',
  'Lozenge',
  'Milliliter',
  'Packet',
  'Pad',
  'Patch',
  'Pen Needle',
  'Ring',
  'Sponge',
  'Stick',
  'Strip',
  'Suppository',
  'Swab',
  'Tablet',
  'Troche',
  'Unspecified',
  'Wafer'
];

const handleSave = () => {
  if (props.medication) {
    emit('save', props.medication);
  }
};

const addTime = () => {
  selectedTimes.value.push('');
};

const removeTime = (index: number) => {
  selectedTimes.value.splice(index, 1);
};
</script>

<template>
  <div v-if="show && medication" class="details-popup-overlay">
    <div class="details-popup">
      <h3>Make Selections</h3>
      
      <div class="frequency-dosage-row">
        <div class="frequency-group">
          <label for="frequency-select">Frequency</label>
          <select v-model="selectedFrequency" class="frequency-select" id="frequency-select">
            <option value="">Select frequency</option>
            <option>1 times daily</option>
            <option>2 times daily</option>
            <option>3 times daily</option>
            <option>4 times daily</option>
            <option>every other day</option>
            <option>at bedtime</option>
            <option>every hour</option>
            <option>every 2 hours</option>
            <option>every 3 hours</option>
            <option>every 4 hours</option>
            <option>every 6 hours</option>
            <option>every 8 hours</option>
            <option>every 12 hours</option>
            <option>every 24 hours</option>
          </select>
        </div>

        <div class="dosage-group">
          <label for="dosage-select">Dosage</label>
          <select v-model="selectedDosage" class="dosage-select" id="dosage-select">
            <option>1</option>
            <option>2</option>
            <option>3</option>
            <option>4</option>
            <option>5</option>
          </select>
        </div>
      </div>

      <div class="times-container">
        <div v-for="(time, index) in selectedTimes" :key="index" class="time-row">
          <input type="time" v-model="selectedTimes[index]" class="time-input">
          <div class="time-actions">
            <button class="time-button remove" @click="removeTime(index)">Remove</button>
            <button class="time-button change">Change</button>
          </div>
        </div>
      </div>

      <button class="add-time-button" @click="addTime">
        <span class="plus-icon">+</span>
        Add Additional Time
      </button>

      <div class="selected-times">
        <h4>Selected Change Times:</h4>
        <div class="times-list">
          <div v-for="time in selectedTimes" :key="time">{{ time }}</div>
        </div>
      </div>

      <div class="details-grid">
        <div class="details-box">
          <h4>Medication Information</h4>
          <div class="details-content">
            <div class="detail-row">
              <label>Route</label>
              <select v-model="medication.route" class="form-control">
                <option v-for="option in routeOptions" :key="option" :value="option">
                  {{ option }}
                </option>
              </select>
            </div>
            <div class="detail-row">
              <label>Form</label>
              <select v-model="medication.dosageForm" class="form-control">
                <option v-for="form in dosageFormOptions" :key="form" :value="form">
                  {{ form }}
                </option>
              </select>
            </div>
          </div>
        </div>

        <div class="details-box">
          <h4>Administration</h4>
          <div class="details-content">
            <div class="detail-row">
              <label>Frequency</label>
              <input type="text" v-model="medication.frequency" class="form-control" readonly>
            </div>
            <div class="detail-row">
              <label>Pills per Administration</label>
              <input type="number" v-model="medication.pillsPerAdministration" class="form-control">
            </div>
          </div>
        </div>
      </div>

      <div class="popup-actions">
        <button class="btn-cancel" @click="$emit('close')">Close</button>
        <button class="btn-save" @click="handleSave">Save Changes</button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.details-popup-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.details-popup {
  background-color: white;
  padding: 2rem;
  border-radius: 8px;
  width: 90%;
  max-width: 800px;
  max-height: 90vh;
  overflow-y: auto;
}

.frequency-dosage-row {
  display: flex;
  gap: 24px;
  margin-bottom: 20px;
}

.frequency-group,
.dosage-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.frequency-group {
  flex: 2;
}

.dosage-group {
  flex: 1;
}

.frequency-select,
.dosage-select {
  width: 100%;
  padding: 8px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
}

.times-container {
  margin-bottom: 20px;
}

.time-row {
  display: flex;
  align-items: center;
  margin-bottom: 12px;
  gap: 12px;
}

.time-input {
  padding: 8px;
  border: 1px solid #ddd;
  border-radius: 4px;
  width: 150px;
}

.time-actions {
  display: flex;
  gap: 8px;
}

.time-button {
  padding: 6px 12px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px;
}

.time-button.remove {
  background-color: #f8d7da;
  color: #721c24;
}

.time-button.change {
  background-color: #e2e3e5;
  color: #383d41;
}

.add-time-button {
  display: flex;
  align-items: center;
  gap: 8px;
  background: none;
  border: none;
  color: #0066cc;
  cursor: pointer;
  padding: 8px 0;
  font-size: 14px;
  margin-bottom: 20px;
}

.plus-icon {
  font-size: 18px;
  font-weight: bold;
}

.selected-times {
  margin-bottom: 24px;
}

.selected-times h4 {
  margin: 0 0 12px 0;
  color: #666;
}

.times-list {
  min-height: 60px;
  border: 1px solid #ddd;
  border-radius: 4px;
  padding: 12px;
}

.details-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1.5rem;
  margin: 1.5rem 0;
}

.details-box {
  border: 1px solid #ddd;
  border-radius: 4px;
  padding: 1rem;
}

.details-box h4 {
  margin: 0 0 1rem 0;
  color: #0c8687;
}

.detail-row {
  margin-bottom: 1rem;
}

.detail-row:last-child {
  margin-bottom: 0;
}

.detail-row label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
}

.form-control {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 0.9rem;
}

.popup-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 1.5rem;
}

.btn-save,
.btn-cancel {
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 0.9rem;
}

.btn-save {
  background-color: #0c8687;
  color: white;
}

.btn-cancel {
  background-color: #6c757d;
  color: white;
}
</style>