<script setup lang="ts">
import { ref, computed, watch, onMounted, defineProps, withDefaults, defineEmits } from 'vue';
import { useRouter } from 'vue-router';
import 'flatpickr/dist/flatpickr.css';
import flatpickr from 'flatpickr';
import ExpandableDetails from './ExpandableDetails.vue';
import AddMedicationForm from './AddMedicationForm.vue';
import HoldTimeSelector from './HoldTimeSelector.vue';
import type { Medication } from '../types';

const router = useRouter();
const medications = ref<Medication[]>([]);
const sortBy = ref<string>('');
const currentDate = ref(new Date());
const showSelectDropdown = ref(false);
const showAddForm = ref(false);
const selectedMedication = ref<Medication | null>(null);
const selectedFrequency = ref('');
const selectedDosage = ref('1');
const selectedTimes = ref<string[]>([]);
const dateList = ref<Date[]>([]);
const selectedTimeElement = ref<HTMLElement | null>(null);
const selectedTime = ref<string>('');
const selectedAction = ref<string>('');
const medicationStatus = ref<Record<string, any>>({});
const selectedStatus = ref<string | null>(null);
const showHoldSelector = ref(false);
const selectedMedicationForHold = ref<Medication | null>(null);
const showTimeModal = ref(false);
const selectedMedicationForTime = ref<Medication | null>(null);
const timeInputs = ref<string[]>([]);

const frequencyOptions = [
  '1 times daily',
  '2 times daily',
  '3 times daily',
  '4 times daily',
  'every other day',
  'at bedtime',
  'every hour',
  'every 2 hours',
  'every 3 hours',
  'every 4 hours',
  'every 6 hours',
  'every 8 hours',
  'every 12 hours',
  'every 24 hours',
  'monday, wednesday, friday, sunday',
  'tuesday, thursday, saturday'
];

const formatDate = (date: Date) => {
  return date.toLocaleDateString('en-US', {
    weekday: 'short',
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  });
};

const statusOptions = [
  { value: 'active', label: 'Active', color: '#d4edda' },
  { value: 'discontinue', label: 'Discontinue', color: '#f8d7da' },
  { value: 'hold', label: 'Hold', color: '#fff3cd' },
  { value: 'new', label: 'New', color: '#869ccd' },
  { value: 'pending', label: 'Pending', color: '#bf86cd' },
  { value: 'change', label: 'Change', color: '#bf8d05' },
  { value: 'completed', label: 'Completed', color: '#00f445' },
  { value: 'partial', label: 'Partial', color: '#ff69b4' }
];

const handleSort = (type: string) => {
  sortBy.value = type;
};

const handleStatusFilter = (status: string | null) => {
  selectedStatus.value = selectedStatus.value === status ? null : status;
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

// Group meds according to selected sort
const groupedMedications = computed(() => {
  let sortedMeds = [...medications.value];
  
  if (selectedStatus.value) {
    sortedMeds = sortedMeds.filter(med => med.status === selectedStatus.value);
  }
  
  const groups: Record<string, Medication[]> = {};

  if (sortBy.value === 'time') {
    const allTimes = new Set<string>();
    sortedMeds.forEach(med => {
      if (!med.prn) {
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

    const sortedTimes = Array.from(allTimes).sort((a, b) => {
      const timeA = new Date(`1970/01/01 ${a}`);
      const timeB = new Date(`1970/01/01 ${b}`);
      return timeA.getTime() - timeB.getTime();
    });

    sortedTimes.forEach(time => {
      groups[time] = [];
      sortedMeds.forEach(med => {
        if (!med.prn) {
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
  else if (sortBy.value === 'prn') {
    const prnMeds = sortedMeds.filter(med => med.prn);
    if (prnMeds.length > 0) {
      groups['PRN Medications'] = prnMeds;
    }
  }
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
  else {
    // Default sorts: medication or diagnosis
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

// Local storage load
const loadMedications = () => {
  const savedMedications = localStorage.getItem('medications');
  if (savedMedications) {
    medications.value = JSON.parse(savedMedications);
  }
};

watch(medications, (newMeds) => {
  localStorage.setItem('medications', JSON.stringify(newMeds));
}, { deep: true });

// Define props and watch for initial load
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

// Define emits
const emit = defineEmits<{
  (e: 'statusChange', medication: Medication, status: string): void;
  (e: 'medicationTaken', medication: Medication, time: string, action: string): void;
  (e: 'signatureSubmit', signature: string, medications: Medication[], time: string): void;
  (e: 'tabsChange', medication: Medication, tabs: number): void;
}>();

// Frequency utility
const getTimesCountFromFrequency = (frequency: string): number => {
  if (!frequency) return 0;
  
  // Handle "X times daily" format
  const dailyMatch = frequency.match(/(\d+)\s*times?\s*daily/);
  if (dailyMatch) {
    return parseInt(dailyMatch[1], 10);
  }
  
  // Handle "every X hours" format
  const hoursMatch = frequency.match(/every\s*(\d+)\s*hours?/);
  if (hoursMatch) {
    const hours = parseInt(hoursMatch[1], 10);
    return Math.floor(24 / hours); // Calculate slots based on 24-hour day
  }
  
  // Handle special cases
  switch (frequency) {
    case 'every hour':
      return 24;
    case 'daily':
    case 'at bedtime':
    case 'every 24 hours':
    case 'every other day':
      return 1;
    case 'monday, wednesday, friday, sunday':
      return 4;
    case 'tuesday, thursday, saturday':
      return 3;
    default:
      return 1;
  }
};

// Watch the selected frequency to update times
watch(selectedFrequency, (newFrequency) => {
  if (!newFrequency || (selectedMedicationForTime.value && selectedMedicationForTime.value.prn)) {
    timeInputs.value = [];
    return;
  }
  
  const timesCount = getTimesCountFromFrequency(newFrequency);
  timeInputs.value = Array(timesCount).fill('');
});

// Modal toggling
const toggleSelectDropdown = (medication: Medication) => {
  selectedMedicationForTime.value = medication;
  showTimeModal.value = true;
};

const handleSave = () => {
  if (selectedMedicationForTime.value) {
    selectedMedicationForTime.value.frequency = selectedFrequency.value;
    selectedMedicationForTime.value.dosage = selectedDosage.value;
    
    if (!selectedMedicationForTime.value.prn) {
      selectedMedicationForTime.value.times = timeInputs.value
        .filter(time => time) // Filter out empty times
        .map(time => ({ time, completed: false }));
      selectedMedicationForTime.value.administrationTimes = timeInputs.value.join(', ');
    } else {
      selectedMedicationForTime.value.times = [];
      selectedMedicationForTime.value.administrationTimes = 'As needed';
    }
  }
  showTimeModal.value = false;
  selectedMedicationForTime.value = null;
  timeInputs.value = [];
};

const handleCancel = () => {
  showTimeModal.value = false;
  selectedMedicationForTime.value = null;
  timeInputs.value = [];
};

// Handle new medication
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
    selectedMedicationForTime.value = newMedication;
    selectedFrequency.value = newMedication.frequency;
    selectedDosage.value = '1';
    showTimeModal.value = true;
  }
  
  populateMedicationTable();
};

const handleMedicationUpdate = (updatedMedication: Medication) => {
  const index = medications.value.findIndex(med => med.name === updatedMedication.name);
  if (index !== -1) {
    medications.value[index] = updatedMedication;
  }
};

const formatDateToYYYYMMDD = (date: Date): string => {
  return date.getFullYear() + '-' +
         String(date.getMonth() + 1).padStart(2, '0') + '-' +
         String(date.getDate()).padStart(2, '0');
};

// Date range picker
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
  let currentDateVal = new Date(startDate);
  while (currentDateVal <= endDate) {
    dateList.value.push(new Date(currentDateVal));
    currentDateVal.setDate(currentDateVal.getDate() + 1);
  }

  populateMedicationTable();
};

// Populate medication status by date/time
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
  
  if (status === 'hold') {
    selectedMedicationForHold.value = medications.value[medIndex];
    showHoldSelector.value = true;
    return;
  }
  
  if (row) {
    row.classList.remove('active-row', 'discontinued-row', 'hold-row', 'new-row', 'pending-row', 'change-row', 'completed-row', 'partial-row');
    row.classList.add(`${status}-row`);
  }
  
  emit('statusChange', medications.value[medIndex], status);
};

const handleHoldSubmit = (data: { 
  dateRange: [Date, Date]; 
  times: string[] | null; 
  reason: string;
  holdType: 'all' | 'specific';
}) => {
  if (!selectedMedicationForHold.value) return;

  const medication = selectedMedicationForHold.value;
  
  const index = medications.value.findIndex(med => med === medication);
  if (index !== -1) {
    const row = document.querySelector(`tr[data-med-index="${index}"]`);
    if (row) {
      row.classList.remove('active-row', 'discontinued-row', 'hold-row', 'new-row', 'pending-row', 'change-row', 'completed-row', 'partial-row');
      row.classList.add('hold-row');
      
      const select = row.querySelector('.status-dropdown') as HTMLSelectElement;
      if (select) {
        select.value = 'hold';
      }
    }
    
    medication.holdInfo = {
      dateRange: data.dateRange,
      times: data.times,
      reason: data.reason,
      type: data.holdType
    };
    
    emit('statusChange', medication, 'hold');
  }
  
  showHoldSelector.value = false;
  selectedMedicationForHold.value = null;
};

const handleTabsChange = (medication: Medication, newValue: number) => {
  medication.tabsAvailable = newValue;
  emit('tabsChange', medication, newValue);
};

// Row styling
const getRowStatusClass = (medication: Medication) => {
  if (medication.holdInfo) return 'hold-row';
  return 'active-row';
};

onMounted(() => {
  loadMedications();
  initializeDateRangePicker();
  populateMedicationTable();
});
</script>

<template>
  <div id="app">
    <h2>Administration</h2>

    <div class="table-container">
      <!-- Status Filter -->
      <div class="status-filter">
        <h3>Filter by Status:</h3>
        <div class="status-buttons">
          <button 
            class="status-button"
            :class="{ active: selectedStatus === null }"
            @click="handleStatusFilter(null)"
          >
            Show All
          </button>
          <button 
            v-for="option in statusOptions"
            :key="option.value"
            class="status-button"
            :class="{ active: selectedStatus === option.value }"
            :style="{ backgroundColor: option.color }"
            @click="handleStatusFilter(option.value)"
          >
            {{ option.label }}
          </button>
        </div>
      </div>

      <!-- Date Range and Add Form -->
      <div class="date-range-selector">
        <label for="date-range-picker">Select Date Range:</label>
        <input type="text" id="date-range-picker" placeholder="Select date range">
        <button class="add-manually-btn" @click="showAddForm = true">
          Add Manually
        </button>
      </div>

      <!-- Sorting Controls -->
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

      <!-- Grouped Medications -->
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
                <th>Administration Times ({{ formatDate(currentDate) }})</th>
              </tr>
            </thead>
            <tbody>
              <tr 
                v-for="(med, medIndex) in medications" 
                :key="medIndex" 
                class="medication-row active-row"
                :data-med-index="medIndex"
              >
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
                <td class="tabs-available" :class="getRowStatusClass(med)">
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
                <td class="select-time-dosage" :class="getRowStatusClass(med)">
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

    <!-- Add Medication Form -->
    <AddMedicationForm
      :show="showAddForm"
      @close="showAddForm = false"
      @save="handleNewMedication"
    />

    <!-- Hold Time Selector Modal -->
    <div v-if="showHoldSelector" class="modal-overlay">
      <div class="modal-content">
        <HoldTimeSelector
          v-if="selectedMedicationForHold"
          :medication-times="selectedMedicationForHold.times.map(t => t.time)"
          @submit="handleHoldSubmit"
          @cancel="showHoldSelector = false"
        />
      </div>
    </div>

    <!-- Time and Dosage Modal -->
    <div v-if="showTimeModal" class="modal-overlay">
      <div class="modal-content">
        <h3>Select Time and Dosage</h3>
        <div class="form-group">
          <label>Frequency:</label>
          <select v-model="selectedFrequency" class="form-select">
            <option value="">Select frequency</option>
            <option v-for="option in frequencyOptions" :key="option" :value="option">
              {{ option }}
            </option>
          </select>
        </div>
        <div class="form-group">
          <label>Dosage:</label>
          <input type="number" v-model="selectedDosage" min="1" step="1">
        </div>
        <div v-if="timeInputs.length > 0" class="form-group">
          <label>Administration Times:</label>
          <div v-for="(_, index) in timeInputs" :key="index" class="time-input-row">
            <input 
              type="time" 
              v-model="timeInputs[index]"
              class="time-input"
              required
            >
          </div>
        </div>
        <div class="form-actions">
          <button @click="handleSave" class="btn-save">Save</button>
          <button @click="handleCancel" class="btn-cancel">Cancel</button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.status-filter {
  margin-bottom: 1.5rem;
}

.status-filter h3 {
  margin-bottom: 0.5rem;
  color: #333;
  font-size: 1rem;
}

.status-buttons {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.status-button {
  padding: 0.5rem 1rem;
  border: 1px solid #dee2e6;
  border-radius: 4px;
  cursor: pointer;
  font-size: 0.9rem;
  transition: all 0.2s ease;
  color: #333;
}

.status-button:hover {
  filter: brightness(0.95);
}

.status-button.active {
  border-color: #0c8687;
  box-shadow: 0 0 0 2px rgba(12, 134, 135, 0.2);
}

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

.modal-overlay {
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

.modal-content {
  background: white;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  width: 90%;
  max-width: 500px;
  padding: 2rem;
}

.form-group {
  margin-bottom: 1rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
}

.form-group select,
.form-group input {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 1rem;
}

.time-input-row {
  margin-bottom: 0.5rem;
}

.time-input {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 1rem;
}

.form-actions {
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
  transition: all 0.2s ease;
}

.btn-save {
  background-color: #0c8687;
  color: white;
}

.btn-save:hover {
  background-color: #0a7273;
}

.btn-cancel {
  background-color: #6c757d;
  color: white;
}

.btn-cancel:hover {
  background-color: #5a6268;
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

/* Row Status Classes */
.medication-row.active-row,
.medication-row.active-row .tabs-available,
.medication-row.active-row .select-time-dosage { 
  background-color: #d4edda; 
}

.medication-row.discontinue-row,
.medication-row.discontinue-row .tabs-available,
.medication-row.discontinue-row .select-time-dosage { 
  background-color: #f8d7da; 
}

.medication-row.hold-row,
.medication-row.hold-row .tabs-available,
.medication-row.hold-row .select-time-dosage { 
  background-color: #fff3cd !important; 
}

.medication-row.new-row,
.medication-row.new-row .tabs-available,
.medication-row.new-row .select-time-dosage { 
  background-color: #869ccd; 
}

.medication-row.pending-row,
.medication-row.pending-row .tabs-available,
.medication-row.pending-row .select-time-dosage { 
  background-color: #bf86cd; 
}

.medication-row.change-row,
.medication-row.change-row .tabs-available,
.medication-row.change-row .select-time-dosage { 
  background-color: #bf8d05; 
}

.medication-row.completed-row,
.medication-row.completed-row .tabs-available,
.medication-row.completed-row .select-time-dosage { 
  background-color: #00f445; 
}

.medication-row.partial-row,
.medication-row.partial-row .tabs-available,
.medication-row.partial-row .select-time-dosage { 
  background-color: #ff69b4; 
}

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

.prn-indicator {
  font-style: italic;
  color: #666;
}

.form-select {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 1rem;
  background-color: white;
}

.form-select:focus {
  border-color: #0c8687;
  outline: none;
  box-shadow: 0 0 0 2px rgba(12, 134, 135, 0.1);
}
</style>