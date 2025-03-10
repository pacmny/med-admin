<script setup lang="ts">
import { ref, onMounted } from 'vue';
import 'flatpickr/dist/flatpickr.css';
import flatpickr from 'flatpickr';
import ExpandableDetails from './ExpandableDetails.vue';
import type { Medication, MedicationStatus, MedicationAdminProps } from '../types';

const props = withDefaults(defineProps<MedicationAdminProps>(), {
  medications: () => []
});

const emit = defineEmits<{
  (e: 'statusChange', medication: Medication, status: string): void;
  (e: 'medicationTaken', medication: Medication, time: string, action: string): void;
  (e: 'signatureSubmit', signature: string, medications: Medication[], time: string): void;
  (e: 'tabsChange', medication: Medication, tabs: number): void;
}>();

const dateList = ref<Date[]>([]);
const selectedTimeElement = ref<HTMLElement | null>(null);
const selectedTime = ref<string>('');
const selectedAction = ref<string>('');
const medicationStatus = ref<MedicationStatus>({});

const showSelectDropdown = ref(false);
const selectedMedication = ref<Medication | null>(null);
const selectedFrequency = ref('2 times daily');
const selectedDosage = ref('1');
const selectedTimes = ref<string[]>([]);

const statusOptions = [
  { value: 'active', label: 'Active', color: '#d4edda' },
  { value: 'discontinue', label: 'Discontinue', color: '#f8d7da' },
  { value: 'hold', label: 'Hold', color: '#fff3cd' },
  { value: 'new', label: 'New', color: '#869ccd' },
  { value: 'pending', label: 'Pending', color: '#bf86cd' },
  { value: 'change', label: 'Change', color: '#bf8d05' },
  { value: 'completed', label: 'Completed', color: '#00f445' }
];

const toggleSelectDropdown = (medication: Medication) => {
  selectedMedication.value = medication;
  selectedTimes.value = [];
  showSelectDropdown.value = true;
};

const addTime = () => {
  selectedTimes.value.push('');
};

const removeTime = (index: number) => {
  selectedTimes.value.splice(index, 1);
};

const handleSave = () => {
  if (selectedMedication.value) {
    selectedMedication.value.frequency = selectedFrequency.value;
    selectedMedication.value.dosage = selectedDosage.value;
    selectedMedication.value.times = [...selectedTimes.value];
    selectedMedication.value.administrationTimes = selectedTimes.value.join(', ');
  }
  showSelectDropdown.value = false;
};

const handleCancel = () => {
  showSelectDropdown.value = false;
};

onMounted(() => {
  initializeDateRangePicker();
  populateMedicationTable();
});

const formatDateToYYYYMMDD = (date: Date): string => {
  return date.getFullYear() + '-' +
         String(date.getMonth() + 1).padStart(2, '0') + '-' +
         String(date.getDate()).padStart(2, '0');
};

const initializeDateRangePicker = () => {
  const dateRangePicker = document.getElementById('date-range-picker');
  if (dateRangePicker) {
    flatpickr(dateRangePicker, {
      mode: "range",
      dateFormat: "l, M d, Y",
      maxDate: new Date().fp_incr(365),
      onChange: (selectedDates) => {
        if (selectedDates.length === 2) {
          updateDateRange(selectedDates[0], selectedDates[1]);
        }
      }
    });
  }
};

const updateDateRange = (startDate: Date, endDate: Date) => {
  if (!startDate || !endDate) {
    alert("Please select both a start and an end date.");
    return;
  }

  dateList.value = [];
  let currentDate = new Date(startDate);
  while (currentDate <= endDate) {
    dateList.value.push(new Date(currentDate));
    currentDate.setDate(currentDate.getDate() + 1);
  }

  populateMedicationTable();
};

const populateMedicationTable = () => {
  dateList.value.forEach(date => {
    const dateStr = formatDateToYYYYMMDD(date);
    if (!medicationStatus.value[dateStr]) {
      medicationStatus.value[dateStr] = {};
      props.medications.forEach((med, index) => {
        med.times.forEach(time => {
          if (!medicationStatus.value[dateStr][time]) {
            medicationStatus.value[dateStr][time] = {};
          }
          medicationStatus.value[dateStr][time][index] = 'pending';
        });
      });
    }
  });
};

const handleStatusChange = (event: Event, medIndex: number) => {
  const select = event.target as HTMLSelectElement;
  const status = select.value;
  const row = select.closest('tr');
  
  if (row) {
    row.classList.remove('active-row', 'discontinued-row', 'hold-row', 'new-row', 'pending-row');
    row.classList.add(`${status}-row`);
  }
  
  emit('statusChange', props.medications[medIndex], status);
};

const handleTabsChange = (medication: Medication, newValue: number) => {
  medication.tabsAvailable = newValue;
  emit('tabsChange', medication, newValue);
};

const showPopup = (time: string, element: HTMLElement) => {
  const popup = document.getElementById('popup');
  if (popup) {
    popup.style.display = 'flex';
    const timeElement = document.getElementById('time');
    if (timeElement) {
      timeElement.textContent = time;
    }
    selectedTimeElement.value = element;
    selectedTime.value = time;
    selectedAction.value = '';
    
    const currentDate = new Date();
    const selectedDateElement = document.getElementById('selected-date');
    if (selectedDateElement) {
      selectedDateElement.textContent = currentDate.toDateString();
    }
    
    const reasonContainer = document.getElementById('reason-container');
    const reasonInput = document.getElementById('reason-input') as HTMLTextAreaElement;
    if (reasonContainer && reasonInput) {
      reasonContainer.style.display = 'none';
      reasonInput.value = '';
    }
  }
};

const selectOption = (action: string) => {
  selectedAction.value = action;
  const reasonContainer = document.getElementById('reason-container');
  if (reasonContainer) {
    reasonContainer.style.display = action === 'take-later' || action === 'refused' ? 'block' : 'none';
    if (action === 'taken') {
      applyAction();
    }
  }
};

const applyAction = () => {
  if (!selectedTimeElement.value || !selectedTime.value || !selectedAction.value) return;

  const currentDate = formatDateToYYYYMMDD(new Date());
  const timestamp = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
  const reasonInput = document.getElementById('reason-input') as HTMLTextAreaElement;
  const reason = reasonInput ? reasonInput.value : '';

  if (medicationStatus.value[currentDate] && 
      medicationStatus.value[currentDate][selectedTime.value]) {
    const medIndex = selectedTimeElement.value.getAttribute('data-med-index');
    if (medIndex !== null) {
      medicationStatus.value[currentDate][selectedTime.value][medIndex] = selectedAction.value;
      if (reason) {
        medicationStatus.value[currentDate][selectedTime.value][`${medIndex}_reason`] = reason;
      }
      
      emit('medicationTaken', props.medications[parseInt(medIndex)], selectedTime.value, selectedAction.value);
    }
  }

  closePopup();
  checkForSignature(currentDate, selectedTime.value);
};

const closePopup = () => {
  const popup = document.getElementById('popup');
  if (popup) {
    popup.style.display = 'none';
  }
};

const checkForSignature = (date: string, time: string) => {
  const medsForTime = medicationStatus.value[date][time];
  const allDone = Object.entries(medsForTime).every(([key, status]) => {
    return !key.includes('_reason') && (status === 'taken' || status === 'refused');
  });

  if (allDone) {
    showSignaturePopup(date, time);
  }
};

const showSignaturePopup = (date: string, time: string) => {
  const popup = document.getElementById('signaturePopup');
  if (popup) {
    popup.style.display = 'flex';
    const timeElement = document.getElementById('signature-time');
    const dateElement = document.getElementById('signature-date');
    if (timeElement) timeElement.textContent = time;
    if (dateElement) dateElement.textContent = new Date(date).toDateString();

    const medicationListDiv = document.getElementById('medication-list');
    if (medicationListDiv) {
      medicationListDiv.innerHTML = '';
      const medsForTime = medicationStatus.value[date][time];
      
      Object.entries(medsForTime).forEach(([key, status]) => {
        if (!key.includes('_reason')) {
          const med = props.medications[parseInt(key)];
          const reason = medsForTime[`${key}_reason`] || '';
          const medDiv = document.createElement('div');
          medDiv.textContent = `${med.name} - Status: ${status}${reason ? ' - Reason: ' + reason : ''}`;
          medicationListDiv.appendChild(medDiv);
        }
      });
    }
  }
};

const submitSignature = () => {
  const signatureInput = document.getElementById('signature-input') as HTMLInputElement;
  if (!signatureInput || !signatureInput.value) {
    alert('Please provide your signature.');
    return;
  }

  const timestamp = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
  console.log(`Signature: ${signatureInput.value}, Timestamp: ${timestamp}`);
  
  const currentDate = formatDateToYYYYMMDD(new Date());
  const medsForTime = medicationStatus.value[currentDate][selectedTime.value];
  const medicationsForSignature = Object.keys(medsForTime)
    .filter(key => !key.includes('_reason'))
    .map(key => props.medications[parseInt(key)]);
  
  emit('signatureSubmit', signatureInput.value, medicationsForSignature, selectedTime.value);

  const popup = document.getElementById('signaturePopup');
  if (popup) {
    popup.style.display = 'none';
  }
};
</script>

<template>
  <div id="app">
    <h2>Administration</h2>

    <div class="table-container">
      <div class="date-range-selector">
        <label for="date-range-picker">Select Date Range:</label>
        <input type="text" id="date-range-picker" placeholder="Select date range">
      </div>

      <table id="administrationTable" class="schedule-table">
        <thead>
          <tr>
            <th>Medication Details</th>
            <th>Status</th>
            <th>Tabs Available</th>
            <th>Frequency</th>
            <th>Dosage</th>
            <th>Select Time and Dosage</th>
            <th>{{ new Date().toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' }) }}</th>
            <th>Complete</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(med, medIndex) in medications" :key="medIndex" class="medication-row active-row">
            <td>
              <ExpandableDetails>
                <template #preview>
                  {{ med.name }}
                </template>
                <template #details>
                  <div class="details-grid">
                    <div class="details-box">
                      <h3>Medication Information</h3>
                      <div class="details-content">
                        <div class="detail-row">
                          <span class="detail-label">NDC Number:</span>
                          <span class="detail-value">70700010917</span>
                        </div>
                        <div class="detail-row">
                          <span class="detail-label">Dosage:</span>
                          <span class="detail-value">{{ med.dosage }}</span>
                        </div>
                        <div class="detail-row">
                          <span class="detail-label">Route:</span>
                          <span class="detail-value">Topical</span>
                        </div>
                        <div class="detail-row">
                          <span class="detail-label">Number of Tablets/Quanity:</span>
                          <span class="detail-value">{{ med.tabsAvailable }} units</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </template>
              </ExpandableDetails>
            </td>
            <td>
              <select 
                class="status-dropdown" 
                @change="(e) => handleStatusChange(e, medIndex)"
              >
                <option 
                  v-for="option in statusOptions" 
                  :key="option.value"
                  :value="option.value"
                  :style="{ backgroundColor: option.color }"
                >
                  {{ option.label }}
                </option>
              </select>
            </td>
            <td class="tabs-available">
              <div class="tabs-counter">
                <input 
                  type="number" 
                  v-model="med.tabsAvailable" 
                  @change="handleTabsChange(med, $event.target.value)"
                  class="tabs-input"
                >
              </div>
            </td>
            <td>{{ med.frequency }}</td>
            <td>{{ med.dosage }}</td>
            <td class="select-time-dosage">
              <button class="select-button" @click="toggleSelectDropdown(med)">Select</button>
            </td>
            <td>{{ med.administrationTimes }}</td>
            <td>
              <input type="checkbox" v-model="med.complete">
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Popup for medication administration -->
    <div id="popup" class="popup">
      <div class="popup-content">
        <h3>Choose Action for <span id="time"></span></h3>
        <p>Date: <span id="selected-date"></span></p>
        <div class="button-row">
          <button type="button" class="taken" @click="selectOption('taken')">Taken</button>
          <button type="button" class="take-later" @click="selectOption('take-later')">Take Later</button>
          <button type="button" class="refused" @click="selectOption('refused')">Refused</button>
        </div>
        <div id="reason-container" style="display: none; margin-top: 10px;">
          <label for="reason-input">Reason:</label>
          <textarea id="reason-input" rows="3" style="width: 100%;"></textarea>
          <button type="button" @click="applyAction">Submit</button>
        </div>
      </div>
    </div>

    <!-- Signature Popup -->
    <div id="signaturePopup" class="popup">
      <div class="popup-content">
        <h3>Signature Required for <span id="signature-time"></span></h3>
        <p>Date: <span id="signature-date"></span></p>
        <div id="medication-list"></div>
        <label for="signature-input">Signature:</label>
        <input type="text" id="signature-input">
        <button type="button" @click="submitSignature">Submit</button>
      </div>
    </div>

    <!-- Add the select dropdown -->
    <div v-if="showSelectDropdown" class="select-dropdown-overlay">
      <div class="select-dropdown">
        <h3>Make Selections</h3>
        
        <div class="frequency-dosage-row">
          <div class="frequency-group">
            <label for="frequency-select">Frequency</label>
            <select v-model="selectedFrequency" class="frequency-select" id="frequency-select">
              <option>daily</option>
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
              <option>before every meal</option>
              <option>after every meal</option>
              <option>1 hour after every meal</option>
              <option>every morning</option>
              <option>once a week</option>
              <option>as directed</option>
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
              <option>6</option>
              <option>7</option>
              <option>8</option>
              <option>9</option>
              <option>10</option>
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

        <div class="button-group">
          <button class="cancel-button" @click="handleCancel">Cancel</button>
          <button class="save-button" @click="handleSave">Save</button>
        </div>
      </div>
    </div>
  </div>
</template>

<style>
.tabs-available {
  background-color: #d4edda !important;
  padding: 8px;
}

.tabs-counter {
  display: flex;
  justify-content: center;
  align-items: center;
}

.tabs-input {
  width: 60px;
  padding: 4px;
  text-align: center;
  border: 1px solid #ddd;
  border-radius: 4px;
  background-color: white;
}

.select-time-dosage {
  background-color: #d4edda !important;
  padding: 8px;
}

.select-button {
  background-color: white;
  border: 1px solid #ddd;
  border-radius: 4px;
  padding: 8px 24px;
  font-size: 14px;
  cursor: pointer;
  width: 100%;
  text-align: center;
  transition: background-color 0.2s;
}

.select-button:hover {
  background-color: #f8f9fa;
}

.select-dropdown-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.select-dropdown {
  background-color: white;
  padding: 24px;
  border-radius: 8px;
  width: 500px;
  max-width: 90%;
}

.select-dropdown h3 {
  margin: 0 0 24px 0;
  text-align: center;
  font-size: 20px;
}

.select-row {
  margin-bottom: 20px;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.select-row label {
  font-weight: 500;
  color: #333;
  font-size: 14px;
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

.button-group {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
}

.save-button,
.cancel-button {
  padding: 8px 24px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px;
}

.save-button {
  background-color: #28a745;
  color: white;
}

.cancel-button {
  background-color: #6c757d;
  color: white;
}

.status-dropdown {
  width: 100%;
  padding: 8px;
  border: 1px solid #ddd;
  border-radius: 4px;
  cursor: pointer;
}

.status-dropdown option {
  padding: 8px;
}

.status-dropdown option[value="active"] {
  background-color: #d4edda;
}

.status-dropdown option[value="discontinue"] {
  background-color: #f8d7da;
}

.status-dropdown option[value="hold"] {
  background-color: #fff3cd;
}

.status-dropdown option[value="new"] {
  background-color: #869ccd;
}

.status-dropdown option[value="pending"] {
  background-color: #bf86cd;
}

.status-dropdown option[value="change"] {
  background-color: #bf8d05;
}

.status-dropdown option[value="completed"] {
  background-color: #00f445;
}

/* Status row colors */
.medication-row.active-row {
  background-color: #d4edda;
}

.medication-row.discontinue-row {
  background-color: #f8d7da;
}

.medication-row.hold-row {
  background-color: #fff3cd;
}

.medication-row.new-row {
  background-color: #869ccd;
}

.medication-row.pending-row {
  background-color: #bf86cd;
}

.medication-row.change-row {
  background-color: #bf8d05;
}

.medication-row.completed-row {
  background-color: #00f445;
}

.date-range-selector {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 1rem;
}
</style>