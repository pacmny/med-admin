<script setup lang="ts">
import { ref, onMounted } from 'vue';
import 'flatpickr/dist/flatpickr.css';
import flatpickr from 'flatpickr';
import ExpandableDetails from './ExpandableDetails.vue';

interface Medication {
  name: string;
  times: string[];
}

const medications = ref<Medication[]>([
  {
    name: "Tylenol 50mg PO DAILY Give 3 TAB 50 mg every six hours",
    times: ["09:00", "15:30", "21:45"]
  },
  {
    name: "Paxil 100 mg PO DAILY Give 1 TAB 100 mg daily",
    times: ["09:00", "15:30", "21:45"]
  },
  {
    name: "Furosemide 80 mg PO DAILY Give 2 TAB furosemide 40 mg daily",
    times: ["09:00", "15:30", "21:45"]
  },
  {
    name: "Aspirin 80 mg PO DAILY Give 2 TAB furosemide 40 mg daily",
    times: ["09:00", "15:30", "21:45"]
  }
]);

const dateList = ref<Date[]>([]);
const selectedTimeElement = ref<HTMLElement | null>(null);
const selectedTime = ref<string>('');
const selectedAction = ref<string>('');
const medicationStatus = ref<Record<string, any>>({});
const showPopupDialog = ref(false);

onMounted(() => {
  initializeDateRangePicker();
  populateMedicationTable()
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
};

const handleTimeClick = (time: string, element: HTMLElement) => {
  selectedTime.value = time;
  selectedTimeElement.value = element;
  showPopupDialog.value = true;
};

const selectOption = (action: string) => {
  selectedAction.value = action;
  if (action === 'taken') {
    applyAction();
  }
};

const applyAction = () => {
  if (!selectedTimeElement.value || !selectedTime.value || !selectedAction.value) return;

  const currentDate = formatDateToYYYYMMDD(new Date());
  if (medicationStatus.value[currentDate] && 
      medicationStatus.value[currentDate][selectedTime.value]) {
    const medIndex = selectedTimeElement.value.getAttribute('data-med-index');
    if (medIndex !== null) {
      medicationStatus.value[currentDate][selectedTime.value][medIndex] = selectedAction.value;
    }
  }

  showPopupDialog.value = false;
  selectedTimeElement.value = null;
  selectedTime.value = '';
  selectedAction.value = '';
};

const closePopup = () => {
  showPopupDialog.value = false;
  selectedTimeElement.value = null;
  selectedTime.value = '';
  selectedAction.value = '';
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
            <th>Medication, Route, Frequency</th>
            <th>Status</th>
            <th>Administration Times</th>
            <th v-for="date in dateList" :key="formatDateToYYYYMMDD(date)">
              {{ date.toLocaleDateString('default', { weekday: 'short', month: 'short', day: 'numeric' }) }}
            </th>
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
                          <span class="detail-value">Cream</span>
                        </div>
                        <div class="detail-row">
                          <span class="detail-label">Route:</span>
                          <span class="detail-value">Topical</span>
                        </div>
                        <div class="detail-row">
                          <span class="detail-label">Number of Tablets/Quanity:</span>
                          <span class="detail-value">60 units</span>
                        </div>
                      </div>
                      <div class="edit-link">
                        <a href="#" class="edit">Edit</a>
                      </div>
                    </div>
                  </div>
                </template>
              </ExpandableDetails> 
            </td>
            <td>
              <select class="status-dropdown" @change="(e) => handleStatusChange(e, medIndex)">
                <option value="active" selected>Active</option>
                <option value="hold">Hold</option>
                <option value="new">New</option>
                <option value="change">Change</option>
                <option value="discontinue">Discontinue</option>
                <option value="pending">Pending</option>
              </select>
            </td>
            <td>
              <template v-for="(time, timeIndex) in med.times" :key="time">
                <div 
                  class="time-entry"
                  :data-time="time"
                  :data-med-index="medIndex"
                  @click="handleTimeClick(time, $event.target as HTMLElement)"
                >
                  {{ time }}
                </div>
                <hr v-if="timeIndex < med.times.length - 1">
              </template>
            </td>
            <td v-for="date in dateList" :key="formatDateToYYYYMMDD(date)" class="date-cell">
              <template v-for="time in med.times" :key="time">
                <div 
                  class="time-slot"
                  :class="{
                    'active-cell': medicationStatus[formatDateToYYYYMMDD(date)]?.[time]?.[medIndex] === 'taken',
                    'hold-cell': medicationStatus[formatDateToYYYYMMDD(date)]?.[time]?.[medIndex] === 'take-later',
                    'discontinued-cell': medicationStatus[formatDateToYYYYMMDD(date)]?.[time]?.[medIndex] === 'refused'
                  }"
                  :data-time="time"
                  :data-med-index="medIndex"
                  @click="handleTimeClick(time, $event.target as HTMLElement)"
                ></div>
              </template>
            </td>
          </tr>
        </tbody>
      </table>
     
    </div>

    <Teleport to="body">
      <div v-if="showPopupDialog" class="popup">
        <div class="popup-content">
          <h3>Choose Action for {{ selectedTime }}</h3>
          <p>Date: {{ new Date().toDateString() }}</p>
          <div class="button-row">
            <button type="button" class="taken" @click="selectOption('taken')">Taken</button>
            <button type="button" class="take-later" @click="selectOption('take-later')">Take Later</button>
            <button type="button" class="refused" @click="selectOption('refused')">Refused</button>
          </div>
          <div v-if="selectedAction === 'take-later' || selectedAction === 'refused'" style="margin-top: 10px;">
            <label for="reason-input">Reason:</label>
            <textarea id="reason-input" rows="3" style="width: 100%;"></textarea>
            <button type="button" @click="applyAction">Submit</button>
          </div>
          <button type="button" class="cancel-button" @click="closePopup" style="margin-top: 10px;">Cancel</button>
        </div>
      </div>
    </Teleport>

    <div class="footer-buttons">
      <button type="button" @click="$router.push('/')">Back to Medications</button>
      <button type="button">Data</button>
      <button type="button">Reports</button>
    </div>
  </div>
</template>

<style>
.medication-details {
  font-size: 14px;
}

.detail-row {
  display: flex;
  margin-bottom: 4px;
}

.detail-label {
  font-weight: bold;
  width: 120px;
}

.detail-value {
  flex: 1;
}

.details-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1rem;
  padding: 1rem;
}

.details-box {
  border: 1px solid #ddd;
  border-radius: 8px;
  padding: 1rem;
}

.details-box h3 {
  margin-top: 0;
  margin-bottom: 1rem;
  color: #333;
  font-size: 1.1rem;
}

.details-content {
  margin-bottom: 1rem;
}

.edit-link {
  text-align: right;
}

.edit {
  color: #0066cc;
  text-decoration: none;
}

.edit:hover {
  text-decoration: underline;
}

.table-container {
  margin: 20px auto;
  width: 90%;
  overflow-x: auto;
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

.schedule-table th {
  background-color: #f8f9fa;
  font-weight: normal;
  position: sticky;
  top: 0;
  z-index: 1;
}

.schedule-table td:first-child {
  text-align: left;
  width: 300px;
  white-space: pre-line;
}

.schedule-table td:nth-child(2) {
  width: 150px;
}

.schedule-table td:nth-child(3) {
  width: 100px;
}

.schedule-table td[data-date] {
  width: 80px;
}

.popup {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.popup-content {
  background-color: white;
  padding: 20px;
  border-radius: 5px;
  width: 300px;
  text-align: center;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
}

.popup-content h3 {
  margin-top: 0;
}

.popup-content button {
  margin: 0 10px;
  padding: 10px 20px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  font-size: 16px;
  flex: 1;
}

.popup-content .taken {
  background-color: #28a745;
  color: white;
}

.popup-content .take-later {
  background-color: #ffc107;
  color: black;
}

.popup-content .refused {
  background-color: #dc3545;
  color: white;
}

.button-row {
  display: flex;
  justify-content: space-between;
  margin-top: 20px;
}

.time-entry {
  cursor: pointer;
  padding: 5px;
}

hr {
  border: none;
  border-top: 1px solid #ddd;
  margin: 5px 0;
}

.time-slot {
  min-height: 30px;
  width: 100%;
  cursor: pointer;
}

.date-cell {
  padding: 0 !important;
}

.active-cell {
  background-color: #d4edda;
}

.discontinued-cell {
  background-color: #f8d7da;
}

.hold-cell {
  background-color: #fff3cd;
}

.new-cell {
  background-color: #869ccd;
}

.pending-cell {
  background-color: #bf86cd;
}

.active-row {
  background-color: #d4edda;
}

.medication-row.discontinued-row {
  background-color: #f8d7da;
}

.medication-row.new-row {
  background-color: #869ccd;
}

.medication-row.hold-row {
  background-color: #fff3cd;
}

.medication-row.pending-row {
  background-color: #bf86cd;
}

.medication-row.active-row {
  background-color: #d4edda;
}

.cancel-button {
  background-color: #6c757d;
  color: white;
  width: 100%;
  margin: 0 !important;
}
</style>