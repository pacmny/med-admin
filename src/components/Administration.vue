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
          const med = medications.value[parseInt(key)];
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
                    <div class="details-box">
                      <h3>Prescription Information</h3>
                      <div class="details-content">
                        <div class="detail-row">
                          <span class="detail-label">RX Number:</span>
                          <span class="detail-value">N/A</span>
                        </div>
                        <div class="detail-row">
                          <span class="detail-label">Date the script was filled:</span>
                          <span class="detail-value">N/A</span>
                        </div>
                        <div class="detail-row">
                          <span class="detail-label">Number of Refills:</span>
                          <span class="detail-value">N/A</span>
                        </div>
                        <div class="detail-row">
                          <span class="detail-label">Expiration/Refills until:</span>
                          <span class="detail-value">N/A</span>
                        </div>
                      </div>
                      <div class="edit-link">
                        <a href="#" class="edit">Edit</a>
                      </div>
                    </div>
                    <div class="details-box">
                      <h3>Pharmacy Information</h3>
                      <div class="details-content">
                        <div class="detail-row">
                          <span class="detail-label">Pharmacy:</span>
                          <span class="detail-value">N/A</span>
                        </div>
                        <div class="detail-row">
                          <span class="detail-label">NPI Number:</span>
                          <span class="detail-value">N/A</span>
                        </div>
                        <div class="detail-row">
                          <span class="detail-label">Address:</span>
                          <span class="detail-value">N/A</span>
                        </div>
                        <div class="detail-row">
                          <span class="detail-label">Phone Number:</span>
                          <span class="detail-value">N/A</span>
                        </div>
                        <div class="detail-row">
                          <span class="detail-label">DEA BL Number:</span>
                          <span class="detail-value">N/A</span>
                        </div>
                      </div>
                      <div class="edit-link">
                        <a href="#" class="edit">Edit</a>
                      </div>
                    </div>
                    <div class="details-box">
                      <h3>Prescribers Information</h3>
                      <div class="details-content">
                        <div class="detail-row">
                          <span class="detail-label">Prescriber:</span>
                          <span class="detail-value">N/A</span>
                        </div>
                        <div class="detail-row">
                          <span class="detail-label">DEA Number or NPI Number:</span>
                          <span class="detail-value">N/A</span>
                        </div>
                        <div class="detail-row">
                          <span class="detail-label">Auth Number:</span>
                          <span class="detail-value">N/A</span>
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
                  @click="showPopup(time, $event.target as HTMLElement)"
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
                  @click="showPopup(time, $event.target as HTMLElement)"
                ></div>
              </template>
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

    <div class="footer-buttons">
      <button type="button" @click="$router.push('/')">Back to Medications</button>
      <button type="button" @click="handleData">Data</button>
      <button type="button" @click="handleReports">Reports</button>
    </div>
  </div>
</template>

<style>
/* Add these new styles to your existing styles */
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

/* Existing styles from style.css remain unchanged */
</style>