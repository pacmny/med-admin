<script setup lang="ts">
import { ref, computed, watch, onMounted } from 'vue';
import 'flatpickr/dist/flatpickr.css';
import flatpickr from 'flatpickr';
import ExpandableDetails from './ExpandableDetails.vue';
import AddMedicationForm from './AddMedicationForm.vue';
import type { Medication } from '../types';

const medications = ref<Medication[]>([]);
const sortBy = ref<string>('');

const handleSort = (type: string) => {
  sortBy.value = type;
};

const routeCategories = [
  'PRN',
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

const groupedMedications = computed(() => {
  let sortedMeds = [...medications.value];
  const groups: Record<string, Medication[]> = {};

  // If sorting by time is selected
  if (sortBy.value === 'time') {
    // Get all unique times
    const allTimes = new Set<string>();
    sortedMeds.forEach(med => {
      if (!med.prn) { // Skip PRN medications for time sorting
        med.times.forEach(timeStatus => {
          const [hours, minutes] = timeStatus.time.split(':');
          const hour = parseInt(hours);
          const ampm = hour >= 12 ? 'PM' : 'AM';
          const displayHour = hour % 12 || 12;
          const displayTime = `${displayHour}:${minutes} ${ampm}`;
          allTimes.add(displayTime);
        });
      }
    });

    // Sort times chronologically
    const sortedTimes = Array.from(allTimes).sort((a, b) => {
      const timeA = new Date(`1970/01/01 ${a}`);
      const timeB = new Date(`1970/01/01 ${b}`);
      return timeA.getTime() - timeB.getTime();
    });

    // Create groups for each time and duplicate medications for each time they appear
    sortedTimes.forEach(time => {
      groups[time] = [];
      sortedMeds.forEach(med => {
        if (!med.prn) { // Skip PRN medications
          med.times.forEach(t => {
            const [hours, minutes] = t.time.split(':');
            const hour = parseInt(hours);
            const ampm = hour >= 12 ? 'PM' : 'AM';
            const displayHour = hour % 12 || 12;
            const displayTime = `${displayHour}:${minutes} ${ampm}`;
            
            if (displayTime === time) {
              const medForTime = {
                ...med,
                times: [t]
              };
              groups[time].push(medForTime);
            }
          });
        }
      });
    });
  } 
  // If sorting by PRN is selected
  else if (sortBy.value === 'prn') {
    const prnMeds = sortedMeds.filter(med => med.prn);
    if (prnMeds.length > 0) {
      groups['PRN Medications'] = prnMeds;
    }
  }
  // If sorting by route is selected
  else if (sortBy.value === 'route') {
    routeCategories.forEach(route => {
      const medsForRoute = sortedMeds.filter(med => med.route === route);
      if (medsForRoute.length > 0) {
        groups[route] = medsForRoute;
      }
    });
    
    const uncategorizedMeds = sortedMeds.filter(med => !med.route || !routeCategories.includes(med.route));
    if (uncategorizedMeds.length > 0) {
      groups['Uncategorized'] = uncategorizedMeds;
    }
  }
  // Handle other sorting options
  else {
    switch (sortBy.value) {
      case 'medication':
        sortedMeds.sort((a, b) => a.name.localeCompare(b.name));
        break;
      case 'diagnosis':
        sortedMeds.sort((a, b) => (a.diagnosis || '').localeCompare(b.diagnosis || ''));
        break;
    }
    groups['All Medications'] = sortedMeds;
  }

  // Remove empty groups
  Object.keys(groups).forEach(key => {
    if (groups[key].length === 0) {
      delete groups[key];
    }
  });

  return groups;
});

const loadMedications = () => {
  const savedMedications = localStorage.getItem('medications');
  if (savedMedications) {
    medications.value = JSON.parse(savedMedications);
  }
};

watch(medications, (newMeds) => {
  localStorage.setItem('medications', JSON.stringify(newMeds));
}, { deep: true });

const props = withDefaults(defineProps<{
  medications?: Medication[];
}>(), {
  medications: () => []
});

watch(() => props.medications, (newMeds) => {
  if (!localStorage.getItem('medications')) {
    medications.value = [...newMeds];
  }
}, { immediate: true });

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
const medicationStatus = ref<Record<string, any>>({});

const showSelectDropdown = ref(false);
const showAddForm = ref(false);
const selectedMedication = ref<Medication | null>(null);
const selectedFrequency = ref('');
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

const getTimesCountFromFrequency = (frequency: string): number => {
  if (!frequency) return 0;
  
  const match = frequency.match(/(\d+)/);
  if (match) {
    return parseInt(match[1], 10);
  }
  
  switch (frequency) {
    case 'daily':
    case 'at bedtime':
    case 'every 24 hours':
      return 1;
    case 'every 12 hours':
      return 2;
    case 'every 8 hours':
      return 3;
    case 'every 6 hours':
      return 4;
    case 'every 4 hours':
      return 6;
    case 'every 3 hours':
      return 8;
    case 'every 2 hours':
      return 12;
    case 'every hour':
      return 24;
    default:
      return 1;
  }
};

watch(selectedFrequency, (newFrequency) => {
  if (!newFrequency || (selectedMedication.value && selectedMedication.value.prn)) {
    selectedTimes.value = [];
    return;
  }
  
  const timesCount = getTimesCountFromFrequency(newFrequency);
  selectedTimes.value = Array(timesCount).fill('');
});

const toggleSelectDropdown = (medication: Medication) => {
  selectedMedication.value = medication;
  selectedTimes.value = [];
  selectedFrequency.value = '';
  showSelectDropdown.value = true;
};

const handleSave = () => {
  if (selectedMedication.value) {
    selectedMedication.value.frequency = selectedFrequency.value;
    selectedMedication.value.dosage = selectedDosage.value;
    
    if (!selectedMedication.value.prn) {
      selectedMedication.value.times = selectedTimes.value.map(time => ({ time, completed: false }));
      selectedMedication.value.administrationTimes = selectedTimes.value.join(', ');
    } else {
      selectedMedication.value.times = [];
      selectedMedication.value.administrationTimes = 'As needed';
    }
  }
  showSelectDropdown.value = false;
};

const handleCancel = () => {
  showSelectDropdown.value = false;
};

const handleNewMedication = (medication: Partial<Medication>) => {
  const newMedication: Medication = {
    name: medication.medicationDetails || '',
    times: [],
    tabsAvailable: medication.tabsAvailable || 0,
    frequency: medication.frequency || '',
    dosage: medication.dosage || '',
    administrationTimes: '',
    route: medication.route || '',
    dosageForm: medication.dosageForm || '',
    diagnosis: medication.diagnosis || '',
    prn: medication.prn || false,
    startDate: medication.startDate,
    endDate: medication.endDate,
    pharmacy: medication.pharmacy || '',
    pharmacyNpi: medication.pharmacyNpi || '',
    pharmacyAddress: medication.pharmacyAddress || '',
    pharmacyPhone: medication.pharmacyPhone || '',
    pharmacyDea: medication.pharmacyDea || '',
    prescriberInfo: medication.prescriberInfo || '',
    prescriberDeaNpi: medication.prescriberDeaNpi || '',
    rxNumber: medication.rxNumber || '',
    refills: medication.refills || 0,
    refillReminderDate: medication.refillReminderDate,
    expirationDate: medication.expirationDate || '',
    instructions: medication.instructions || ''
  };

  medications.value.push(newMedication);
  showAddForm.value = false;
  
  if (!newMedication.prn) {
    selectedMedication.value = newMedication;
    selectedFrequency.value = newMedication.frequency;
    selectedDosage.value = '1';
    showSelectDropdown.value = true;
  }
  
  populateMedicationTable();
};

const handleMedicationUpdate = (updatedMedication: Medication) => {
  const index = medications.value.findIndex(med => med.name === updatedMedication.name);
  if (index !== -1) {
    medications.value[index] = updatedMedication;
  }
};

const addTime = () => {
  selectedTimes.value.push('');
};

const removeTime = (index: number) => {
  selectedTimes.value.splice(index, 1);
};

onMounted(() => {
  loadMedications();
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
      medications.value.forEach((med, index) => {
        if (!med.prn) {
          med.times.forEach(timeStatus => {
            if (!medicationStatus.value[dateStr][timeStatus.time]) {
              medicationStatus.value[dateStr][timeStatus.time] = {};
            }
            medicationStatus.value[dateStr][timeStatus.time][index] = 'pending';
          });
        }
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
  
  emit('statusChange', medications.value[medIndex], status);
};

const handleTabsChange = (medication: Medication, newValue: number) => {
  medication.tabsAvailable = newValue;
  emit('tabsChange', medication, newValue);
};
</script>

<template>
  <div id="app">
    <h2>Administration</h2>

    <div class="table-container">
      <div class="date-range-selector">
        <label for="date-range-picker">Select Date Range:</label>
        <input type="text" id="date-range-picker" placeholder="Select date range">
        <button class="add-manually-btn" @click="showAddForm = true">
          Add Manually
        </button>
      </div>

      <div class="sort-controls">
        <button 
          class="sort-button" 
          :class="{ active: sortBy === 'medication' }"
          @click="handleSort('medication')"
        >
          Sort by Medication
        </button>
        <button 
          class="sort-button" 
          :class="{ active: sortBy === 'time' }"
          @click="handleSort('time')"
        >
          Sort by Time
        </button>
        <button 
          class="sort-button" 
          :class="{ active: sortBy === 'diagnosis' }"
          @click="handleSort('diagnosis')"
        >
          Sort by Diagnosis
        </button>
        <button 
          class="sort-button" 
          :class="{ active: sortBy === 'route' }"
          @click="handleSort('route')"
        >
          Sort by Route
        </button>
        <button 
          class="sort-button" 
          :class="{ active: sortBy === 'prn' }"
          @click="handleSort('prn')"
        >
          Sort by PRN
        </button>
      </div>

      <template v-for="(medications, category) in groupedMedications" :key="category">
        <div v-if="medications.length > 0" class="category-section">
          <h3 class="category-header">{{ category }}</h3>
          <table class="schedule-table">
            <thead>
              <tr>
                <th>Medication Details</th>
                <th>Status</th>
                <th>Tabs Available</th>
                <th>Frequency</th>
                <th>Dosage</th>
                <th>Select Time and Dosage</th>
                <th>Administration Times</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(med, medIndex) in medications" :key="medIndex" class="medication-row active-row">
                <td>
                  <ExpandableDetails 
                    :medication="med"
                    @update="handleMedicationUpdate"
                  >
                    <template #preview>
                      {{ med.name }}
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
                <td>{{ med.frequency || 'Not set' }}</td>
                <td>{{ med.dosage || 'Not set' }}</td>
                <td class="select-time-dosage">
                  <button class="select-button" @click="toggleSelectDropdown(med)">Select</button>
                </td>
                <td>
                  <div class="administration-times">
                    <div v-if="med.prn" class="prn-indicator">As needed</div>
                    <template v-else>
                      <div v-for="time in med.times" :key="time.time" class="time-entry">
                        {{ time.time }}
                      </div>
                    </template>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </template>
    </div>

    <AddMedicationForm
      :show="showAddForm"
      @close="showAddForm = false"
      @save="handleNewMedication"
    />

    <div v-if="showSelectDropdown" class="select-dropdown-overlay">
      <div class="select-dropdown">
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

        <template v-if="!selectedMedication?.prn">
          <div class="times-container">
            <div v-for="(time, index) in selectedTimes" :key="index" class="time-row">
              <input type="time" v-model="selectedTimes[index]" class="time-input">
              <div class="time-actions">
                <button class="time-button remove" @click="removeTime(index)">Remove</button>
                <button class="time-button change">Change</button>
              </div>
            </div>
          </div>

          <div class="selected-times">
            <h4>Selected Times:</h4>
            <div class="times-list">
              <div v-for="time in selectedTimes" :key="time">{{ time }}</div>
            </div>
          </div>
        </template>
        <div v-else class="prn-notice">
          <p>This medication is marked as PRN (As Needed). No specific administration times are required.</p>
        </div>

        <div class="button-group">
          <button class="cancel-button" @click="handleCancel">Cancel</button>
          <button 
            class="save-button" 
            @click="handleSave"
            :disabled="!selectedFrequency || (!selectedMedication?.prn && selectedTimes.some(time => !time))"
          >
            Save
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.table-container {
  margin: 20px auto;
  width: 90%;
  overflow-x: auto;
}

.date-range-selector {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 1rem;
}

.sort-controls {
  display: flex;
  gap: 1rem;
  margin: 1rem 0;
  padding: 0.5rem;
  background-color: #f8f9fa;
  border-radius: 8px;
}

.sort-button {
  padding: 0.5rem 1rem;
  border: 1px solid #dee2e6;
  border-radius: 4px;
  background-color: white;
  color: #495057;
  cursor: pointer;
  font-size: 0.9rem;
  transition: all 0.2s ease;
}

.sort-button:hover {
  background-color: #e9ecef;
}

.sort-button.active {
  background-color: #0c8687;
  color: white;
  border-color: #0c8687;
}

.schedule-table {
  width: 100%;
  border-collapse: collapse;
  background-color: white;
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.schedule-table th,
.schedule-table td {
  border: 1px solid #ddd;
  padding: 12px;
  text-align: center;
}

.tabs-available {
  background-color: #d4edda;
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
}

.select-time-dosage {
  background-color: #d4edda;
  padding: 8px;
}

.select-button {
  background-color: white;
  border: 1px solid #ddd;
  border-radius: 4px;
  padding: 8px 24px;
  font-size: 14px;
  cursor: pointer;
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
  gap: 12px;
  margin-bottom: 12px;
}

.time-input {
  flex: 1;
  padding: 8px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
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

.save-button:disabled {
  background-color: #cccccc;
  cursor: not-allowed;
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

.add-manually-btn {
  background-color: #0c8687;
  color: white;
  border: none;
  border-radius: 4px;
  padding: 8px 16px;
  font-size: 14px;
  cursor: pointer;
  transition: background-color 0.2s;
  margin-left: auto;
}

.add-manually-btn:hover {
  background-color: #0a7273;
}

.medication-row.active-row { background-color: #d4edda; }
.medication-row.discontinued-row { background-color: #f8d7da; }
.medication-row.hold-row { background-color: #fff3cd; }
.medication-row.new-row { background-color: #869ccd; }
.medication-row.pending-row { background-color: #bf86cd; }
.medication-row.change-row { background-color: #bf8d05; }
.medication-row.completed-row { background-color: #00f445; }

.administration-times {
  display: flex;
  flex-direction: column;
  gap: 8px;
  padding: 8px;
}

.time-entry {
  padding: 4px 8px;
  background-color: #f8f9fa;
  border-radius: 4px;
  font-size: 14px;
}

.category-section {
  margin-bottom: 2rem;
}

.category-header {
  background-color: #0c8687;
  color: white;
  padding: 0.75rem 1rem;
  margin: 0;
  font-size: 1.2rem;
  border-radius: 4px 4px 0 0;
}

.schedule-table {
  margin-top: 0;
  border-radius: 0 0 4px 4px;
}

.prn-notice {
  background-color: #fff3cd;
  border: 1px solid #ffeeba;
  border-radius: 4px;
  padding: 1rem;
  margin: 1rem 0;
  color: #856404;
  text-align: center;
}

.prn-indicator {
  font-style: italic;
  color: #666;
}
</style>