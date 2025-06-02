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
        <input type="text" id="date-range-picker" placeholder="Select date range" />
        <button class="add-manually-btn" @click="showAddForm = true">
          Add Manually
        </button>
      </div>

      <!-- Sorting Controls + Sign Off Button + Expand/Collapse -->
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

        <!-- Expand/Collapse Columns -->
        <button
          class="sort-button"
          @click="toggleCollapse"
        >
          {{ collapsed ? 'Expand' : 'Collapse' }}
        </button>

        <!-- Sign Off Button -->
        <button
          class="sort-button sign-off-button"
          @click="showSignOffPopup = true"
        >
          Sign Off
        </button>
      </div>

      <!-- Grouped Medications -->
      <template v-for="(medsInGroup, category) in groupedMedications" :key="category">
        <div v-if="medsInGroup.length > 0">
          <!-- Sticky category header -->
          <h3 class="category-header">{{ category }}</h3>
          <div class="category-section">
            <table class="schedule-table">
              <thead>
                <tr>
                  <th class="sticky-header-1">Medication Details</th>
                  <!-- Hide these columns if collapsed -->
                  <th v-if="!collapsed" class="sticky-header-2">Status</th>
                  <th v-if="!collapsed" class="sticky-header-3">Tabs Available</th>
                  <th v-if="!collapsed" class="sticky-header-4">Frequency</th>
                  <th v-if="!collapsed" class="sticky-header-5">Dosage</th>
                  <th v-if="!collapsed" class="sticky-header-6">Select Time and Dosage</th>
                  <!-- Date Columns -->
                  <th
                    v-for="dateObj in allColumns"
                    :key="dateObj.getTime()"
                  >
                    Administration Times ({{ formatDate(dateObj) }})
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="(med, medIndex) in medsInGroup"
                  :key="medIndex"
                  class="medication-row"
                  :class="getRowStatusClass(med)"
                  :data-med-index="medIndex"
                >
                  <!-- Medication Info -->
                  <td class="sticky-column-1">
                    <ExpandableDetails
                      :medication="med"
                      @update="handleMedicationUpdate"
                    >
                      <template #preview>
                        {{ med.medname }}
                      </template>
                    </ExpandableDetails>
                  </td>

                  <!-- Status (hidden if collapsed) -->
                  <td v-if="!collapsed" class="sticky-column-2">
                    <select
                      class="status-dropdown"
                      @change="(e) => handleStatusChange(e, medIndex)"
                    >
                      <option
                        v-for="option in statusOptions"
                        :key="option.value"
                        :value="option.value"
                        :style="{ backgroundColor: option.color }"
                        :selected="med.status === option.value"
                      >
                      {{ option.label }} 
                      </option>
                    </select>
                  </td>

                  <!-- Tabs Available (hidden if collapsed) -->
                  <td v-if="!collapsed" class="tabs-available sticky-column-3">
                    <div class="tabs-counter">
                      <input
                        type="number"
                        v-model="med.available"
                        @change="handleTabsChange(med, $event.target.value)"
                        class="tabs-input"
                      />
                    </div>
                  </td>

                  <!-- Frequency & Dosage (hidden if collapsed) -->
                  <td v-if="!collapsed" class="sticky-column-4">{{ med.med_frequency || 'Not set' }}</td>
                  <td v-if="!collapsed" class="sticky-column-5">{{ med.med_amount || 'Not set' }}</td>

                  <!-- "Select Time and Dosage" Button (hidden if collapsed) -->
                  <td v-if="!collapsed" class="select-time-dosage sticky-column-6">
                    <button class="select-button" @click="toggleSelectDropdown(med)">
                      Select
                    </button>
                  </td>

                  <!-- Times by Date -->
                  <td
                    v-for="dateObj in allColumns"
                    :key="dateObj.getTime()"
                  >
                    <div class="administration-times">
                      <!-- PRN Meds -->
                      <template v-if="med.prn">
                        <div class="prn-indicator" @click="stampPRNTime(med)">
                          As needed
                        </div>
                        <div
                          v-if="med.times && med.times.length"
                          class="prn-times-list"
                        >
                          <div
                            v-for="timeObj in med.times.filter(entry => entry.date === formatDateToYYYYMMDD(dateObj))"
                            :key="timeObj.time + timeObj.status"
                            class="time-entry"
                            :class="[timeObj.status, { discontinued: timeObj.status === 'discontinue' }]"
                            @mouseover="showTooltip(timeObj)"
                            @mouseout="hideTooltip"
                            :style="{
                              backgroundColor:
                                timeObj.locked && timeObj.status === 'taken'
                                  ? '#b3f0b3'
                                  : timeObj.locked && timeObj.status === 'refused'
                                  ? '#f9b3b3'
                                  : 'transparent'
                            }"
                          >
                            {{ timeObj.time }}
                            <span v-if="timeObj.earlyReason">
                              ({{ timeObj.earlyReason }})
                            </span>
                            <!-- Tooltip Icon -->
                            <span
                              v-if="getTooltipText(timeObj)"
                              class="tooltip-icon"
                              :title="getTooltipText(timeObj)"
                            >
                              ℹ️
                            </span>
                          </div>
                        </div>
                      </template>

                      <!-- Scheduled Meds (non-PRN) -->
                      <template v-else>
                        <template v-for="timeObj in getTimesForDate(med, dateObj)">
                          <!-- Only show if base time matches category if we're sorting by time -->
                          <div
                            v-if="sortBy !== 'time' || extractBaseTime(timeObj.time) === category"
                            :key="timeObj.time"
                            class="time-entry"
                            :class="[timeObj.status, timeObj.temporaryStatus, { discontinued: timeObj.status === 'discontinue' }]"
                            @click="!timeObj.locked && openActionPopup(dateObj, timeObj, med)"
                            :style="{
                              backgroundColor:
                                timeObj.locked && timeObj.status === 'taken'
                                  ? '#b3f0b3'
                                  : timeObj.locked && timeObj.status === 'refused'
                                  ? '#f9b3b3'
                                  : 'transparent'
                            }"
                          >
                            {{ timeObj.time }}
                            <span v-if="timeObj.earlyReason">
                              ({{ timeObj.earlyReason }})
                            </span>

                            <!-- Immediate Icons -->
                            <template v-if="timeObj.temporaryStatus === 'taken'">
                              <span
                                class="icon-immediate"
                                style="color: #28a745; margin-left: 0.3rem;"
                                title="Taken"
                              >
                                ✔
                              </span>
                            </template>
                            <template v-else-if="timeObj.temporaryStatus === 'refused'">
                              <span
                                class="icon-immediate"
                                style="color: #dc3545; margin-left: 0.3rem;"
                                title="Refused"
                              >
                                ✘
                              </span>
                            </template>
                            <template v-else-if="timeObj.temporaryStatus === 'later'">
                              <span
                                class="icon-immediate"
                                style="color: #ffe600; margin-left: 0.3rem;"
                                title="Take Later"
                              >
                                ⏳
                              </span>
                            </template>

                            <!-- Tooltip if signed off -->
                            <span
                              v-if="getTooltipText(timeObj)"
                              class="tooltip-icon"
                              :title="getTooltipText(timeObj)"
                            >
                              ℹ️
                            </span>
                          </div>
                        </template>
                      </template>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </template>
    </div>

    <!-- Add Medication Form -->
    <AddMedicationForm
      :show="showAddForm"
      :pastProvloaded="pastProvloaded"
      :pastProvar="pastProvar"
      @close="showAddForm = false"
      @updtPastProvbool="updatepastProvBoolean"
      @save="handleNewMedication"
      @loadprov="loadpastProv"
    />

    <!-- Hold/New/Discontinue Time Selector Modal -->
    <div v-if="showHoldSelector" class="modal-overlay">
      <div class="modal-content">
        <HoldTimeSelector
          v-if="selectedMedicationForHold"
          :status-option="selectedStatusOption"
          :medication-times="holdTimes"
          @submit="handleHoldSubmit"
          @cancel="showHoldSelector = false"
        />
      </div>
    </div>

    <!-- Time and Dosage Modal -->
    <div v-if="showTimeModal" class="modal-overlay">
      <div class="modal-content">
        <h3>Select Time and Dosage</h3>
        <h4 v-if="selectedMedicationForTime">{{ selectedMedicationForTime.name }}</h4>
        <div class="form-group">
          <label>Frequency:</label>
          <select v-model="selectedFrequency" class="form-select">
            <option value="">Select frequency</option>
            <option
              v-for="option in frequencyOptions"
              :key="option"
              :value="option"
            >
              {{ option }}
            </option>
          </select>
        </div>
        <div class="form-group">
          <label>Dosage (tabs per admin time):</label>
          <input
            type="number"
            v-model="selectedDosage"
            min="1"
            step="1"
          />
        </div>
        <div v-if="timeInputs.length > 0" class="form-group">
          <label>Administration Times:</label>
          <div
            v-for="(_, index) in timeInputs"
            :key="index"
            class="time-input-row"
          >
            <input
              type="time"
              v-model="timeInputs[index]"
              class="time-input"
              required
            />
          </div>
        </div>
        <div class="form-actions">
          <button @click="handleSave" class="btn-save">Save</button>
          <button @click="handleCancel" class="btn-cancel">Cancel</button>
        </div>
      </div>
    </div>

    <!-- "Taken / Refused / Take Later" Popup -->
    <MedicationActionPopup
      v-if="showTimeActionPopup"
      :timeObj="selectedDateAndTime?.timeObj"
      @close="closeTimeActionPopup"
      @action-selected="handleTimeActionSelected"
    />

    <!-- Early/Late Confirmation Popup -->
    <div v-if="showTimeConfirmationPopup" class="modal-overlay">
      <div class="modal-content">
        <h3>{{ confirmationMessage }}</h3>
        <div v-if="isEarly">
          <div v-if="!showEarlyReasonInput" class="button-row">
            <button @click="triggerEarlyYes">Yes</button>
            <button @click="cancelTimeActionConfirmation">No</button>
          </div>
          <div v-else>
            <input
              type="text"
              v-model="earlyReason"
              placeholder="Enter reason"
            />
            <button class="btn-green" @click="confirmEarlyWithReason">Select</button>
          </div>
        </div>
        <div v-else>
          <div class="button-row">
            <button @click="confirmTimeAction">Yes</button>
            <button @click="cancelTimeActionConfirmation">No</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Error Modal -->
    <div v-if="showErrorModal" class="modal-overlay">
      <div class="modal-content">
        <h3>{{ errorMessage }}</h3>
        <div class="button-row">
          <button @click="closeErrorModal">OK</button>
        </div>
      </div>
    </div>

    <!-- Sign-Off Popup -->
    <div v-if="showSignOffPopup" class="modal-overlay">
      <div class="modal-content">
        <h3>Sign Off Pending Transactions</h3>
        <div v-if="pendingTransactions.length === 0">
          <p>No medications pending sign-off.</p>
          <div class="button-row">
            <button @click="showSignOffPopup = false">Close</button>
          </div>
        </div>
        <div v-else>
          <p>Please confirm the following medication administrations:</p>
          <ul>
            <li
              v-for="(item, index) in pendingTransactions"
              :key="index"
            >
              <strong>{{ item.medication.name }}</strong>
              <span v-if="item.timeObj.temporaryStatus === 'taken'" style="color: #28a745;">✔ Taken</span>
              <span v-else-if="item.timeObj.temporaryStatus === 'refused'" style="color: #dc3545;">✘ Refused</span>
              <span>
                — Time:
                <em>
                  {{
                    item.timeObj.time.includes("(")
                      ? item.timeObj.time.split("(")[0].trim()
                      : item.timeObj.time
                  }}
                </em>
              </span>
            </li>
          </ul>
          <div class="button-row">
            <button @click="finalSignOff" class="save-button">Sign Off</button>
            <button @click="showSignOffPopup = false" class="cancel-button">Cancel</button>
          </div>
        </div>
      </div>
    </div>

    <!-- PRN Sign-Off Popup -->
    <div v-if="showPrnSignOffPopup" class="modal-overlay">
      <div class="modal-content">
        <h3>PRN Sign-Off</h3>
        <p>
          <strong>Medication:</strong>
          {{ prnSignOffMedication?.name }}
        </p>
        <p>
          <strong>Time:</strong>
          <span v-if="prnSignOffTimeObj?.time">
            {{ prnSignOffTimeObj.time }}
          </span>
        </p>
        <!-- Dosage -->
        <div class="form-group">
          <label>Dosage (tabs):</label>
          <input
            type="number"
            v-model.number="prnSignOffTimeObj.dosage"
            min="1"
            step="1"
          />
        </div>
        <!-- PRN Reason -->
        <div class="form-group">
          <label>Reason for PRN:</label>
          <input
            type="text"
            v-model="prnSignOffTimeObj.reason"
            placeholder="Enter PRN reason"
          />
        </div>
        <!-- Nurse Signature -->
        <div class="form-group">
          <label for="prn-nurse-signature">Nurse Signature:</label>
          <input
            type="text"
            id="prn-nurse-signature"
            v-model="prnNurseSignature"
            placeholder="Enter your name or initials"
          />
        </div>
        <div class="button-row">
          <button
            class="save-button"
            :disabled="!prnNurseSignature"
            @click="handlePrnSignOff"
          >
            Sign Off
          </button>
          <button
            class="cancel-button"
            @click="closePrnSignOffPopup"
          >
            Cancel
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import MedicationActionPopup from './MedicationActionPopup.vue'
import { ref, computed, watch, onMounted, defineProps, withDefaults, defineEmits } from 'vue'
import { useRouter } from 'vue-router'
import 'flatpickr/dist/flatpickr.css'
import flatpickr from 'flatpickr'
import axios from 'axios'
import ExpandableDetails from './ExpandableDetails.vue'
import AddMedicationForm from './AddMedicationForm.vue'
import HoldTimeSelector from './HoldTimeSelector.vue'

const FUTURE_DAYS_TO_POPULATE = 365
export interface Provider {
  providerid:string;
  name:string;
}
export interface Medication {
  name: string
  dates?: Record<string, {
    time: string
    status?: string
    locked?: boolean
    signedOff?: any
    temporaryStatus?: string
    med_amount?: number|string
    earlyReason?: string
  }[]>
  tabsAvailable: number
  frequency?: string
  dosage?: string
  administrationTimes?: string
  route?: string
  dosageForm?: string
  diagnosis?: string
  prn?: boolean
  startDate?: Date
  endDate?: Date
  pharmacy?: string
  pharmacyNpi?: string
  pharmacyAddress?: string
  pharmacyPhone?: string
  pharmacyDea?: string
  prescriberInfo?: string
  prescriberDeaNpi?: string
  rxNumber?: string
  refills?: number
  refillReminderDate?: Date
  expirationDate?: string
  instructions?: string
  status?: string

  discontinuedDate?: Date
  discontinuedTimes?: Record<string, string[]>
}
const CURR_API="/keyon/";
const router = useRouter()
const medications = ref<Medication[]>([]);
const pastProvar = ref<Provider[]>([]);
//const pastProvar = ref<string[]>([]);
const pastProvloaded = ref<boolean>(false);
// Sorting
const sortBy = ref<string>('')

// Default pinned date
const currentDate = ref(new Date())

// Collapse/Expand columns toggle
const collapsed = ref(true)
function toggleCollapse() {
  collapsed.value = !collapsed.value
}

// UI Toggles & Selected Data
const showAddForm = ref(false)
const selectedMedicationForTime = ref<Medication | null>(null)
const selectedMedStatusForTime = ref<string>('');
const selectedFrequency = ref('')
const selectedDosage = ref('1')
const timeInputs = ref<string[]>([])
const dateList = ref<Date[]>([])

// For storing statuses
const medicationStatus = ref<Record<string, any>>({})

// Status Filter
const selectedStatus = ref<string | null>(null)

// "Hold/New/Discontinue" popup
const showHoldSelector = ref(false)
const selectedMedicationForHold = ref<Medication | null>(null)
const selectedStatusOption = ref<'hold' | 'new' | 'discontinue' | 'change'>('hold')

// Time & Dosage modal
const showTimeModal = ref(false)

// "Taken/Refused/Later" popup
const showTimeActionPopup = ref(false)
const selectedDateAndTime = ref<{ dateObj: Date; timeObj: any; medication: Medication } | null>(null)

// Early/Late confirmation
const showTimeConfirmationPopup = ref(false)
const confirmationMessage = ref("")
const pendingDateAndTime = ref<{ dateObj: Date; timeObj: any; medication: Medication } | null>(null)
const isEarly = ref(false)
const showEarlyReasonInput = ref(false)
const earlyReason = ref("")

// Error Modal
const showErrorModal = ref(false)
const errorMessage = ref("")

// Sign-Off Popup
const showSignOffPopup = ref(false)
const pendingTransactions = ref<any[]>([])

// PRN Sign-Off
const showPrnSignOffPopup = ref(false)
const prnSignOffMedication = ref<Medication | null>(null)
const prnSignOffTimeObj = ref<any>(null)
const prnNurseSignature = ref('')

let medval= {};
// FREQUENCY OPTIONS
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
]
function updatepastProvBoolean()
{
  pastProvloaded.value=false;
}
//loadpastProv axios call here 
async function loadpastProv()
{
  
  try{
    let content = {
      Provider:{
      API_Meth: "GetAllProviders",
      patientid: "709081242", //"70894333",
      }
    };
    axios.post('https://medadministration:8890/keyon/tswebhook.php', content)
         .then(response => {        
          console.log('Data posted successfully:', response.data);  
         pastProvar.value = response.data.provider;
          console.log(pastProvar);
          pastProvloaded.value=true;
          //errorMessage = ""; // Clear any previous error messages     
         }) 
         .catch(error => {       
           if (axios.isAxiosError(error)) {  
             console.error('Error posting data:', error.response?.data || error.message);     
           // errorMessage = error.response?.data?.message || 'An error occurred while posting data.';  
           } 
            else {          
              console.log('Unexpected error:', error);         
             // errorMessage = 'An unexpected error occurred. Please try again.'; 
            }    
            });   
  }
  catch(error)
  {
    console.error("Error Posting to Past Provider enpoint:",error);
  }
}
//axios goes here 
async function loadMedications2() {
     try{
      let content = {
        API_Meth: "GetPatientMeds",
        pid: "70894333",
        accountId: "9048544",
      };
      const res = await axios.post('https://api.example.com/submit', content);    
       response = res.data; // Handle the response     
       } catch (error) {  
      console.error('Error posting data:', error);     
     }
    }
function extractBaseTime(rawTime: string): string {
  return rawTime.split('(')[0].trim()
}
function normalizeToMidnight(d: Date): Date {
  return new Date(d.getFullYear(), d.getMonth(), d.getDate())
}
function formatDate(date: Date) {
  return date.toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  })
}
function formatTime12Hour(date: Date): string {
  let hours = date.getHours()
  const minutes = date.getMinutes()
  const ampm = hours >= 12 ? 'PM' : 'AM'
  hours = hours % 12
  hours = hours ? hours : 12
  const minutesStr = minutes < 10 ? '0' + minutes : minutes
  return hours + ":" + minutesStr + " " + ampm
}
function formatDateToYYYYMMDD(d: Date): string {
  return (
    d.getFullYear() +
    '-' +
    String(d.getMonth() + 1).padStart(2, '0') +
    '-' +
    String(d.getDate()).padStart(2, '0')
  )
}

// ---------- STATUS & SORT ----------
const statusOptions = [
  { value: 'active', label: 'Active', color: '#d4edda' },
  { value: 'discontinue', label: 'Discontinue', color: '#f8d7da' },
  { value: 'hold', label: 'Hold', color: '#fff3cd' },
  { value: 'new', label: 'New', color: '#869ccd' },
  { value: 'pending', label: 'Pending', color: '#bf86cd' },
  { value: 'change', label: 'Change', color: '#bf8d05' },
  { value: 'completed', label: 'Completed', color: '#00f445' },
  { value: 'partial', label: 'Partial', color: '#ff69b4' }
]
function handleSort(type: string) {
  sortBy.value = type
}
function handleStatusFilter(status: string | null) {
  selectedStatus.value = selectedStatus.value === status ? null : status
}

// ---------- GROUPING ----------
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
]
function groupMedicationsByDiagnosis(meds: Medication[]): Record<string, Medication[]> {
  const grouped: Record<string, Medication[]> = {}
  meds.forEach(med => {
    const diag = med.diagnosis || 'Unspecified Diagnosis'
    if (!grouped[diag]) {
      grouped[diag] = []
    }
    grouped[diag].push(med)
  })
  return grouped
}
const groupedMedications = computed(() => {
  let sortedMeds = [...medications.value]
  if (selectedStatus.value) {
    sortedMeds = sortedMeds.filter(med => med.status === selectedStatus.value)
  }
  const groups: Record<string, Medication[]> = {}

  if (sortBy.value === 'time') {
    const allTimes = new Set<string>()
    sortedMeds.forEach(med => {
      if (!med.prn && med.dates) {
        Object.values(med.dates).forEach((timeArray: any) => {
          timeArray.forEach((obj: any) => {
            allTimes.add(extractBaseTime(obj.time))
          })
        })
      }
    })
    const sortedTimes = Array.from(allTimes).sort((a, b) => {
      const timeA = new Date('1970/01/01 ' + a)
      const timeB = new Date('1970/01/01 ' + b)
      return timeA.getTime() - timeB.getTime()
    })
    sortedTimes.forEach(time => {
      groups[time] = []
      const uniqueMedSet = new Set()
      sortedMeds.forEach(med => {
        if (!med.prn && med.dates) {
          Object.values(med.dates).forEach((arr: any) => {
            arr.forEach((tObj: any) => {
              if (extractBaseTime(tObj.time) === time && !uniqueMedSet.has(med.name)) {
                groups[time].push(med)
                uniqueMedSet.add(med.name)
              }
            })
          })
        }
      })
    })
  } else if (sortBy.value === 'prn') {
    const prnMeds = sortedMeds.filter(med => med.prn)
    if (prnMeds.length > 0) {
      groups['PRN Medications'] = prnMeds
    }
  } else if (sortBy.value === 'route') {
    routeCategories.forEach(route => {
      const medsForRoute = sortedMeds.filter(med => med.route === route)
      if (medsForRoute.length > 0) {
        groups[route] = medsForRoute
      }
    })
    const uncategorized = sortedMeds.filter(med => !med.route || !routeCategories.includes(med.route))
    if (uncategorized.length > 0) {
      groups['Uncategorized'] = uncategorized
    }
  } else if (sortBy.value === 'diagnosis') {
    const groupedByDiagnosis = groupMedicationsByDiagnosis(sortedMeds)
    Object.assign(groups, groupedByDiagnosis)
  } else if (sortBy.value === 'medication') {
    sortedMeds.sort((a, b) => a.name.localeCompare(b.name))
    groups['All Medications'] = sortedMeds
  } else {
    groups['All Medications'] = sortedMeds
  }

  Object.keys(groups).forEach(key => {
    if (groups[key].length === 0) {
      delete groups[key]
    }
  })
  return groups
})

// ---------- ALL COLUMNS ----------
const allColumns = computed(() => {
  if (dateList.value.length > 0) {
    const cols = dateList.value.map(d => normalizeToMidnight(d))
    cols.sort((a, b) => a.getTime() - b.getTime())
    return cols
  } else {
    return [normalizeToMidnight(currentDate.value)]
  }
})

// ---------- LOAD & WATCH ----------
async function loadMedications() {
 /* const savedMedications = localStorage.getItem('medications')
  let errorMessage=""
  if (savedMedications) {
    //medications.value = JSON.parse(savedMedications)
  }*/
  
      let content = {
        MedicationAdmin:{
        API_Meth: "GetPatientMeds",
        pid: "709081242",
        accountId: "904575107",
        providerid:"123456789"
        }
      };
    
        axios.post('https://medadministration:8890/keyon/tswebhook.php', content)
         .then(response => {        
          console.log('Data posted successfully:', response.data);  
          medications.value = response.data.records;
          let cur = new Date();
     /*let tstdt = new Date(med.administrated_at);
     console.log("Test medDt:"+" "+ med.administrated_at);
     *If you use Dates from the database it will set the Date column to that date and you can't administered past med | So this means that you can only view today 
     * of each day to administer the meds. Keep this in mind for adjustments 
     * 
     */
     dateList.value.push(normalizeToMidnight(cur)); //setting this to today's date so that populateMedicationTable can load the times into the slots
          populateMedicationTable();
          //let try and set the localStorage Engine medication array/JSON value to allow the app to sort later on | Try but can remove later 
          localStorage.setItem('medications', JSON.stringify(medications.value));
          errorMessage.value = ""; // Clear any previous error messages     
         }) 
         .catch(error => {       
           if (axios.isAxiosError(error)) {  
             console.error('Error posting data:', error.response?.data || error.message);     
            errorMessage.value = error.response?.data?.message || 'An error occurred while posting data.';  
           } 
            else {          
              console.log('Unexpected error:', error);         
              errorMessage.value = 'An unexpected error occurred. Please try again.'; 
            }    
            });   
          
     
}
watch(medications, (newVal) => {
  localStorage.setItem('medications', JSON.stringify(newVal));
  if(newVal.length >0)
  {
    medications.value = newVal;
    //populateMedicationTable()
  }
  //medications.value = newVal;
  
}, { deep: true })

const props = withDefaults(defineProps<{ medications?: Medication[] }>(), {
  medications: () => []
})
watch(() => props.medications, (newMeds) => {
  if (!localStorage.getItem('medications')) {
    medications.value = [...newMeds]
  }
}, { immediate: true })

const emit = defineEmits<{
  (e: 'statusChange', medication: Medication, status: string): void;
  (e: 'medicationTaken', medication: Medication, time: string, action: string): void;
  (e: 'signatureSubmit', signature: string, medications: Medication[], time: string): void;
  (e: 'tabsChange', medication: Medication, tabs: number): void;
}>()

// ---------- FREQUENCY WATCH ----------
watch(selectedFrequency, (newFreq) => {
  if (!newFreq || (selectedMedicationForTime.value && selectedMedicationForTime.value.prn)) {
    timeInputs.value = []
    return
  }
  const timesCount = getTimesCountFromFrequency(newFreq)
  timeInputs.value = Array(timesCount).fill('')
})
function getTimesCountFromFrequency(frequency: string): number {
  if (!frequency) return 0
  const dailyMatch = frequency.match(/(\d+)\s*times?\s*daily/)
  if (dailyMatch) {
    return parseInt(dailyMatch[1], 10)
  }
  const hoursMatch = frequency.match(/every\s*(\d+)\s*hours?/)
  if (hoursMatch) {
    const hours = parseInt(hoursMatch[1], 10)
    return Math.floor(24 / hours)
  }
  switch (frequency) {
    case 'every hour': return 24
    case 'daily':
    case 'at bedtime':
    case 'every 24 hours':
    case 'every other day': return 1
    case 'monday, wednesday, friday, sunday': return 4
    case 'tuesday, thursday, saturday': return 3
    default: return 1
  }
}

// ---------- TIME & DOSAGE MODAL ----------
function toggleSelectDropdown(medication: Medication) {
  if (medication.tabsAvailable <= 0) {
    errorMessage.value = "Please add tabs available"
    showErrorModal.value = true
    return
  }
  selectedMedicationForTime.value = medication;
  selectedFrequency.value = medication.med_frequency || ''
  selectedDosage.value = medication.med_amount || '1'
  if(!selectedMedStatusForTime.value ) //if this is empty lets set the status to Active 
  {
    selectedMedStatusForTime.value="pending";
  }
  if (medication.administrationTimes && medication.administrationTimes !== 'As needed') {
    //need to see whats here 
    console.log("curious");
    console.log(medication.administrationTimes);
    const splitted = medication.administrationTimes.split(',')
    timeInputs.value = splitted.map(t => t.trim())
  } else {
    timeInputs.value = []
  }
  showTimeModal.value = true
}

async function handleSave() {
  if (!selectedMedicationForTime.value) {
    showTimeModal.value = false
    return
  }
  console.log(selectedMedicationForTime.value);
  if (!selectedMedicationForTime.value.prn && timeInputs.value.length > 0) {
    if (timeInputs.value.some(t => !t)) {
      errorMessage.value = "Please select all required times."
      showErrorModal.value = true
      return
    }
  }

  const med = selectedMedicationForTime.value
  med.frequency = selectedFrequency.value
  med.dosage = selectedDosage.value
  const medname =med.medname;//setting this so that I can grab the actual MedId that's needed to lo
  med.status="Active";
  const medstatus = med.status;
  console.log(medname);
  console.log(medstatus);
  if (med.prn) {
    med.administrationTimes = 'As needed'
    med.dates = {}
  } else {
    const newTimeArray = timeInputs.value.filter(t => t).map(t => ({
      time: t,
      status: 'pending',
      dosage: parseInt(selectedDosage.value, 10) || 1  
      
    }))
    med.administrationTimes = timeInputs.value.join(', ')

    if (!med.startDate) {
      med.startDate = new Date()
    }
    //checking to see date times is greater than todayMidnight and if so lable status as discountinued
    const todayMidnight = normalizeToMidnight(new Date())
    if (med.dates) {
      for (const dStr of Object.keys(med.dates)) {
        const d = new Date(dStr)
        if (normalizeToMidnight(d).getTime() >= todayMidnight.getTime()) {
          med.dates[dStr] = med.dates[dStr].filter(slot =>
            slot.locked === true || slot.status === 'discontinue'
          )
        }
      }
    }

    for (let i = 0; i < FUTURE_DAYS_TO_POPULATE; i++) {
      const futureDate = new Date(todayMidnight)
      futureDate.setDate(futureDate.getDate() + i)
      if (med.discontinuedDate && normalizeToMidnight(futureDate).getTime() > normalizeToMidnight(med.discontinuedDate).getTime()) {
        break
      }
      const ds = formatDateToYYYYMMDD(futureDate)
      if (!med.dates) {
        med.dates = {}
      }
      if (!med.dates[ds]) {
        med.dates[ds] = []
      }
      newTimeArray.forEach(t => {
        const existing = med.dates[ds].find(
          slot => slot.time === t.time && (slot.locked || slot.status === 'discontinue')
        )
        if (!existing) {
          const idx = med.dates[ds].findIndex(slot => slot.time === t.time)
          if (idx === -1) {
            med.dates[ds].push({ ...t })
          } else {
            if (!med.dates[ds][idx].locked && med.dates[ds][idx].status !== 'discontinue') {
              med.dates[ds][idx] = { ...t }
            }
          }
        }
      });
      //I think I need to try and make an axios call right her to log
     
      
      
      //console.log(med.dates);
    }
     console.log("Log newTimeArray - to send to axios");
     console.log(newTimeArray);
     console.log("Med Dates");
     console.log(med.dates);
     let todaydt = formatDate(new Date());
     console.log(todaydt);
     let content = {
        MedicationAdmin:{
        API_Meth:"InsertUpdateMedLogTimes",
        pid: "709081242",
        accountId: "904575107",
        providerid:"123456789",
        slotedtimes:newTimeArray,
        adminDate:todaydt,
        medname:medname,
        ordernumber:'36', // Order number is hard coded for now but should or could be set when the admin app is loaded || or when loaded it could pass the order information as param
        status:medstatus,
        }
      };
    
        axios.post('https://medadministration:8890/keyon/tswebhook.php', content)
         .then(response => {        
          console.log('Data posted successfully:', response.data);  
          if(response.data && response.data.results=="Insert")
          {
            alert("Medication Times Added Successfully");
          }
          if(response.data && response.data.results=="Updated")
          {
            alert("Medication Times Updated Successfully.");
          }
             
         }) 
         .catch(error => {       
           if (axios.isAxiosError(error)) {  
             console.error('Error posting data:', error.response?.data || error.message);     
           // errorMessage.value = error.response?.data?.message || 'An error occurred while posting data.';  
           } 
            else {          
              console.log('Unexpected error:', error);         
              
            }    
            });   
  }

  showTimeModal.value = false
  selectedMedicationForTime.value = null
  timeInputs.value = []
}

function handleCancel() {
  showTimeModal.value = false
  selectedMedicationForTime.value = null
  timeInputs.value = []
}

// ---------- ON MOUNT ----------
onMounted(() => {
  loadMedications()
  initializeDateRangePicker()
  //populateMedicationTable()
})

// ---------- DATE RANGE PICKER ----------
function initializeDateRangePicker() {
  const dateRangePicker = document.getElementById('date-range-picker')
  if (dateRangePicker) {
    flatpickr(dateRangePicker, {
      mode: "range",
      dateFormat: "l, M d, Y",
      maxDate: new Date().fp_incr(365),
      onChange: (selDates) => {
        if (selDates.length === 2) {
          updateDateRange(selDates[0], selDates[1])
        }
      }
    })
  }
}
function updateDateRange(startDate: Date, endDate: Date) {
  /*Function sets up the dateList array with start and end date from calendar*/
  if (!startDate || !endDate) {
    alert("Please select both a start and an end date.")
    return
  }
  dateList.value = []
  let cur = new Date(startDate)
  while (cur <= endDate) {
    dateList.value.push(normalizeToMidnight(cur))
    cur.setDate(cur.getDate() + 1)
  }
  populateMedicationTable()
}

// ---------- POPULATE & STATUS ----------
function populateMedicationTable() {
  console.log("keyon figure it out");
  console.log(medications);
const length = ref<number>(0);
  medications.value.forEach(med => {
      const yrmedtimeArray = JSON.parse(med.yearmedtime);
    console.log(yrmedtimeArray);
    if(Array.isArray(yrmedtimeArray) && yrmedtimeArray !=null){
     
     //lets try and put todays date in the dateList 
    // let cur = new Date();
     /*let tstdt = new Date(med.administrated_at);
     console.log("Test medDt:"+" "+ med.administrated_at);
     *If you use Dates from the database it will set the Date column to that date and you can't administered past med | So this means that you can only view today 
     * of each day to administer the meds. Keep this in mind for adjustments 
     * 
     */
    // dateList.value.push(normalizeToMidnight(cur));
    /*
    * I commented out the dateList value push here because the Date Rande Update could conflict with date that was loaded on page load. This is done when 
    * Patients meds are loaded 
    * */
      length.value = yrmedtimeArray.length;
      console.log("const length:" +" "+ length.value)
      console.log(yrmedtimeArray.length);
     let newadmintimes =[''];
     for(var i = 0; i < length.value; i++)
      {
        console.log("Times Looped:"+" "+i);
       
        newadmintimes.push(yrmedtimeArray[i].time)
      };
      //lets assign the new time array to the administrationTimes variable (medication)
      //console.log(newadmintimes);
     newadmintimes = newadmintimes.filter(item => item);//filter our falsy values 
      med.administrationTimes = newadmintimes.join(',');
      console.log("med.AdministrationTimes value");
      console.log(med.administrationTimes);
    }
    
    
    if (med.prn) return
    if (!med.administrationTimes || med.administrationTimes === 'As needed')
    {
     console.log("returned no admin time dates");
       return;
    } 
    //alert("WHjat");
    console.log("Check TImes");
    console.log(med.administrationTimes);
    if (!med.dates) {
      med.dates = {}
    }
  
   //lets see whats in the datelist 
   console.log("Lets see whats in the dateList.value variable");
   console.log(dateList.value); //Its not running because dateList is empty 
   console.log("I want to see whats in the med.date before we start assigning new values");
   console.log(med.dates);
   let acttakentimes = [];
   if(med.takentimes !="" && med.takentimes !=null)
   {
    acttakentimes =med.takentimes.split(',');
   } 
   else{
    acttakentimes=[];
   }
  
   console.log(acttakentimes);
   let medtakenstats;
    dateList.value.forEach(d => {
     
      const dStr = formatDateToYYYYMMDD(d)
      if (med.discontinuedDate && normalizeToMidnight(d).getTime() > normalizeToMidnight(med.discontinuedDate).getTime()) {
        return
      }
      if (!med.dates![dStr]) {
        const splitted =med.administrationTimes.split(',').map(t => t.trim()); //med.administrationTimes
        if(med.temporaryStatus !="" && med.temporaryStatus !=null)
        {
            medtakenstats = med.temporaryStatus.split(',');
        }
        else{
          medtakenstats =[];
        }
       
       // alert(splitted);
        let lockedstatus =false;
        medtakenstats.value = medtakenstats[0];
       console.log("Taken:"+" "+medtakenstats.value);
        if(medtakenstats.value=="taken")
        {
          lockedstatus =true;
        }
        const dosageNum = parseInt(med.med_amount || '1', 10);
        med.dates![dStr] = splitted.map(t => ({
          time:t +" (taken at"+" "+acttakentimes[0]+")",
          status: medtakenstats.value,//med.temporaryStatus,
          dosage: dosageNum,
          earlyReason: med.earlyReason,
          locked:lockedstatus,
          temporaryStatus:medtakenstats.value
        }));
        console.log("Supposed to be here");
        console.log(med.dates[dStr]);
      } 
      else{
        alert(med.dates![dStr]);
      }
      
    })
  })

  dateList.value.forEach(date => {
    const dateStr = formatDateToYYYYMMDD(date)
    if (!medicationStatus.value[dateStr]) {
      medicationStatus.value[dateStr] = {}
      medications.value.forEach((med, idx) => {
        if (!med.prn && med.dates) {
          const dayTimes = med.dates[dateStr] || []
          dayTimes.forEach((timeObj: any) => {
            if (!medicationStatus.value[dateStr][timeObj.time]) {
              medicationStatus.value[dateStr][timeObj.time] = {}
            }
           // alert( medicationStatus.value[dateStr][timeObj.time][idx]);
           if(med.earlyReason !="")
           {
            medicationStatus.value[dateStr][timeObj.time][idx] = med.earlyReason;
           }
           else{
               medicationStatus.value[dateStr][timeObj.time][idx] = 'pending'
           }
            //medicationStatus.value[dateStr][timeObj.time][idx] = 'pending'
           
          })
        }
      })
    }
  })
}
function handleStatusChange(event: Event, medIndex: number) {
  const select = event.target as HTMLSelectElement
  const status = select.value
  medications.value[medIndex].status = status
//alert("Its here");
  if (status === 'hold' || status === 'new' || status === 'discontinue' || status === 'change') {
    selectedMedicationForHold.value = medications.value[medIndex]
    selectedStatusOption.value = status as 'hold' | 'new' | 'discontinue' | 'change'
    showHoldSelector.value = true
    return
  }
  emit('statusChange', medications.value[medIndex], status)
}

// ---------- HOLD SUBMIT ----------
const holdTimes = computed(() => {
  if (!selectedMedicationForHold.value) return []
  const timesSet = new Set<string>()
  const med = selectedMedicationForHold.value
  if (med.dates) {
    for (const dateKey in med.dates) {
      med.dates[dateKey].forEach((tObj: any) => {
        timesSet.add(tObj.time)
      })
    }
  }
  return Array.from(timesSet)
})
function handleHoldSubmit(data: {
  dateRange: [Date, Date];
  times: string[] | null;
  reason: string;
  holdType: 'all' | 'specific';
  statusOption?: 'hold' | 'new' | 'discontinue' | 'change';
}) {
  if (!selectedMedicationForHold.value) return
  const medication = selectedMedicationForHold.value
  console.log("HandleHoldSubmit Med");
  console.log(medication);
  const discDate = normalizeToMidnight(data.dateRange[0])

  if (data.statusOption === 'discontinue') {
    if (data.holdType === 'all') {
      medication.discontinuedDate = discDate
      const dateStr = formatDateToYYYYMMDD(discDate)
      if (!medication.dates) medication.dates = {}
      if (medication.dates[dateStr]) {
        medication.dates[dateStr].forEach((slot: any) => {
          slot.status = 'discontinue'
          slot.locked = true
        })
      }
    } else if (data.holdType === 'specific' && data.times) {
      if (!medication.discontinuedTimes) {
        medication.discontinuedTimes = {}
      }
      const dateStr = formatDateToYYYYMMDD(discDate)
      if (!medication.discontinuedTimes[dateStr]) {
        medication.discontinuedTimes[dateStr] = []
      }
      if (medication.dates && medication.dates[dateStr]) {
        data.times.forEach(t => {
          const found = medication.dates[dateStr].find((obj: any) => obj.time === t)
          if (found) {
            found.status = 'discontinue'
            found.locked = true
          }
          if (!medication.discontinuedTimes[dateStr].includes(t)) {
            medication.discontinuedTimes[dateStr].push(t)
          }
        })
      }
    }
  }

  medication.holdInfo = {
    medname: medication.medname,
    dateRange: data.dateRange,
    times: data.times,
    reason: data.reason,
    type: data.holdType
  }
  let ask = confirm("Are you sure you want to hold this medication?");
  if(ask==true)
  {
    alert("Lets process");
    holdMedication(medication.holdInfo);
    //send to backend 
    //we are going to assume everything went well and are going to go ahead and close the showHolder Selector and the SelectedMedicationfor Hold comp
  }
  else{
    //do nothing but lets go ahead and still close the modalss 
    showHoldSelector.value = false
    selectedMedicationForHold.value = null
  }
  console.log("Med Hold Status that is to be sent over for process");
  console.log(medication.holdInfo);
  showHoldSelector.value = false
  selectedMedicationForHold.value = null
}

// ---------- CLOSE ERROR MODAL ----------
function closeErrorModal() {
  showErrorModal.value = false
}

// ---------- TABS CHANGE ----------
function handleTabsChange(medication: Medication, newValue: number) {
  medication.tabsAvailable = newValue
  emit('tabsChange', medication, newValue)
}

// ---------- ROW STYLING ----------
function getRowStatusClass(_medication: Medication) {
  return 'active-row'
}

// ---------- GET TIMES FOR DATE ----------
function getTimesForDate(med: Medication, dateObj: Date) {
  const dateStr = formatDateToYYYYMMDD(dateObj)
  if (!med.dates || !med.dates[dateStr]) {
    return []
  }
  let slots = [...med.dates[dateStr]]

  if (med.discontinuedDate) {
    const discDay = normalizeToMidnight(med.discontinuedDate)
    const thisDay = normalizeToMidnight(dateObj)
    if (thisDay.getTime() > discDay.getTime()) {
      return []
    }
  }
  if (med.discontinuedTimes) {
    for (const discDateStr of Object.keys(med.discontinuedTimes)) {
      const discDayParsed = normalizeToMidnight(new Date(discDateStr))
      const thisDay = normalizeToMidnight(dateObj)
      if (thisDay.getTime() > discDayParsed.getTime()) {
        const timesToRemove = med.discontinuedTimes[discDateStr]
        slots = slots.filter(s => !timesToRemove.includes(s.time))
      }
    }
  }
  return slots
}

// ---------- ACTION POPUP ----------
function openActionPopup(dateObj: Date, timeObj: any, medication: Medication) {
  if (timeObj.locked) return
  const now = new Date()
  const isToday =
    dateObj.getFullYear() === now.getFullYear() &&
    dateObj.getMonth() === now.getMonth() &&
    dateObj.getDate() === now.getDate()
  if (!isToday) {
    errorMessage.value = "Medications can only be given on the current date."
    showErrorModal.value = true
    return
  }

  const scheduledTimeStr = timeObj.time.includes("(")
    ? timeObj.time.split("(")[0].trim()
    : timeObj.time
  const [hours, mins] = scheduledTimeStr.split(":").map(Number)
  const scheduledDateTime = new Date(
    dateObj.getFullYear(),
    dateObj.getMonth(),
    dateObj.getDate(),
    hours,
    mins
  )
  const diffMinutes = (now.getTime() - scheduledDateTime.getTime()) / 60000

  if (diffMinutes >= -60 && diffMinutes <= 60) {
    selectedDateAndTime.value = { dateObj, timeObj, medication }
    showTimeActionPopup.value = true
  } else {
    pendingDateAndTime.value = { dateObj, timeObj, medication }
    if (diffMinutes < -60) {
      isEarly.value = true
      confirmationMessage.value = "This medication is early, do you still want to give it?"
    } else {
      isEarly.value = false
      confirmationMessage.value = "This medication is late, do you still want to give it?"
    }
    showTimeConfirmationPopup.value = true
  }
}
function closeTimeActionPopup() {
  showTimeActionPopup.value = false
}
function handleTimeActionSelected({ action }: { action: string }) {
  if (selectedDateAndTime.value) {
    const { dateObj, timeObj, medication } = selectedDateAndTime.value
    timeObj.temporaryStatus = action
    if (action === 'taken' || action === 'refused') {
      const existingIndex = pendingTransactions.value.findIndex(
        item => item.timeObj === timeObj && item.medication === medication
      )
      if (existingIndex === -1) {
        pendingTransactions.value.push({ medication, timeObj, dateObj })
      }
    }
    if (action === 'later') { //removing from pending transaction array
      const existingIndex = pendingTransactions.value.findIndex(
        item => item.timeObj === timeObj && item.medication === medication
      )
      if (existingIndex !== -1) {
        pendingTransactions.value.splice(existingIndex, 1)
      }
    }
  }
  showTimeActionPopup.value = false
}

// ---------- EARLY / LATE CONFIRMATION ----------
function confirmTimeAction() {
  showTimeConfirmationPopup.value = false
  if (!isEarly.value && pendingDateAndTime.value) {
    selectedDateAndTime.value = pendingDateAndTime.value
    showTimeActionPopup.value = true
    pendingDateAndTime.value = null
  }
}
function triggerEarlyYes() {
  showEarlyReasonInput.value = true
}
function confirmEarlyWithReason() {
  if (pendingDateAndTime.value && earlyReason.value.trim() !== "") {
    selectedDateAndTime.value = pendingDateAndTime.value
    if (selectedDateAndTime.value.timeObj.med?.prn) {
      const med = selectedDateAndTime.value.timeObj.med
      const now = new Date()
      const todayStr = formatDateToYYYYMMDD(now)
      const timeStr = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
      if (!med.times) {
        med.times = []
      }
      const newTimeObj = {
        time: timeStr,
        status: 'PRN',
        date: todayStr,
        earlyReason: earlyReason.value.trim(),
        dosage: med.dosage || '1',
        reason: ''
      }
      med.times.push(newTimeObj)
      openPrnSignOffPopup(med, newTimeObj)
    } else {
      selectedDateAndTime.value.timeObj.earlyReason = earlyReason.value.trim()
      selectedDateAndTime.value.timeObj.temporaryStatus = "taken"
      const { timeObj, medication, dateObj } = selectedDateAndTime.value
      const existingIndex = pendingTransactions.value.findIndex(
        item => item.timeObj === timeObj && item.medication === medication
      )
      if (existingIndex === -1) {
        pendingTransactions.value.push({ medication, timeObj, dateObj })
      }
    }
    showTimeConfirmationPopup.value = false
    showEarlyReasonInput.value = false
    earlyReason.value = ""
    pendingDateAndTime.value = null
  }
}
function cancelTimeActionConfirmation() {
  showTimeConfirmationPopup.value = false
  pendingDateAndTime.value = null
  showEarlyReasonInput.value = false
  earlyReason.value = ""
}

// ---------- NEW MED ----------
/* Keyon To make this function async to post the API endpoint */
async function handleNewMedication(medication: Partial<Medication>) {
  const newMedication: Medication = {
    name: medication.medicationName || '',
    dates: {},
    tabsAvailable: medication.quantity || 0,
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
    instructions: medication.instructions || '',
    status: 'active',
    discontinuedDate: undefined,
    discontinuedTimes: {}
  }
  let medsetting="";
  let content = {
    MedicationAdmin: {
    API_Meth:"InsertAdminMecationInfo", 
    accountnumber:"904575107", 
    npinumber:"123456789",
    proflicensenumber:"609382", //insurance test
    patientid:"709081242",
    ordernumber:'36',
    name: medication.medicationName || '',
    dates: {},
    tabsAvailable: medication.tabsAvailable || 0,
    totalTabs:medication.quantity,
    frequency: medication.frequency || '',
    dosage: medication.dosage || '',
    administrationTimes: '',
    route: medication.route || '',
    dosageForm: medication.dosageForm || '',
    diagnosis: medication.diagnosis || '',
    prn: medication.prn || false,
    datescriptFilled: medication.filledDate,
    startDate: medication.startDate,
    endDate: medication.endDate,
    pharmacy: medication.pharmacyName || '',
    pharmacyNpi: medication.pharmacyNpi || '',
    pharmacyAddress: medication.pharmacyAddress || '',
    pharmacyPhone: medication.pharmacyOffice || '',
    pharmacyCell: medication.pharmacyCell || '',
    pharmacyEmail: medication.pharmacyEmail || '',
    pharmacyDea: medication.pharmacyDea || '',
    prescriberInfo: medication.prescriberInfo || '',
    prescriberDeaNpi: medication.prescriberDeaNpi || '',
    rxNumber: medication.rxnorns || '',
    ndcnumber:medication.ndcnumber,
    refills: medication.refills || 0,
    refillReminderDate: medication.refillReminderDate,
    expirationDate: medication.expirationDate || '',
    instructions: medication.instructions || '',
    status: 'active',
    medsetting:medsetting,
    discontinuedDate: undefined,
    discontinuedTimes: {}
    
    }
    }
  
  let errorMessage="";
  axios.post('https://medadministration:8890/keyon/tswebhook.php', content)
         .then(response => {        
          console.log('Data posted successfully:', response.data);  
          if(response.data && response.data.count >=1)
         {
          /*let the user know that the medication is already in the table and active. Then ask with a confirm dialog if they want to add the Mec to the 
          *Administration setting, so that it can be added from the Medication table 
          */
         console.log("Here now lets ask for the dialog");
         let ask =confirm(medication.medicationName+" is already listed in the Patients Medication List. Do you want to add it to the Administration setting to be included?");
          if(ask==true)
         {
          //now lets update the information and call axios again
          content.MedicationAdmin.medsetting="update";
          let updatemed = updateMedAdminSetting(content);
          //alert(updatemed);
         }
         else{
          // do nothing because 
         }

         }
         else if(response.data.message=="Medication Added Successfully" && response.data.status=="200 Successfull")
         {
           alert("New Medication Added Successfully");
           showAddForm.value = false
         }
         else{
           //now lets add the information to the Appropriate tables 
           alert("now sure why its running");
         }
          medications.value = response.data.records
          errorMessage = ""; // Clear any previous error messages     
         }) 
         .catch(error => {       
           if (axios.isAxiosError(error)) {  
             console.error('Error posting data:', error.response?.data || error.message);     
            errorMessage = error.response?.data?.message || 'An error occurred while posting data.';  
           } 
            else {          
              console.log('Unexpected error:', error);         
              errorMessage = 'An unexpected error occurred. Please try again.'; 
            }    
            });   
  //medications.value.push(newMedication)
  //showAddForm.value = false
  /*if (!newMedication.prn) {
    selectedMedicationForTime.value = newMedication
    selectedFrequency.value = newMedication.frequency
    selectedDosage.value = '1'
    showTimeModal.value = true
  }
  populateMedicationTable()
  */
}
async function updateMedAdminSetting(payload)
{
  let errorMessage="";
  axios.post('https://medadministration:8890/keyon/tswebhook.php', payload)
         .then(response => {        
          console.log('Past Update parmaaters successfully:', response.data);  
          if(response.data && response.data.message =="Updated Successfully")
         {
           let returnmsg="";
           returnmsg="Medication Updated Successfully";
           alert(returnmsg);
           showAddForm.value = false;
           loadMedications();
           return ;
         // alert("Meciation Updated Successfully");
         

         }
         else{
           //now lets add the information to the Appropriate tables 
           let returnmsg="";
           returnmsg="Update Note Succcessful";

           return returnmsg;
           //alert("Meciation Settings Not Updated Successfully, Please try again later");
         }
              
         }) 
         .catch(error => {       
           if (axios.isAxiosError(error)) {  
             console.error('Error posting data:', error.response?.data || error.message);     
            errorMessage = error.response?.data?.message || 'An error occurred while posting data.';  
           } 
            else {          
              console.log('Unexpected error:', error);         
              errorMessage = 'An unexpected error occurred. Please try again.'; 
            }    
            });   
}
function handleMedicationUpdate(updatedMedication: Medication) {
  const i = medications.value.findIndex(m => m.name === updatedMedication.name)
  if (i !== -1) {
    medications.value[i] = updatedMedication
  }
}

// ---------- SIGN OFF PENDING ----------
function finalSignOff() {
  const now = new Date()
  const nurseSignature = prompt("Please enter your signature/initials:")
  if (!nurseSignature) return
  pendingTransactions.value.forEach(item => {
    const { medication, timeObj } = item
    const pendingAction = timeObj.temporaryStatus
    if (!pendingAction) return
    timeObj.locked = true
    timeObj.status = pendingAction
    timeObj.signedOff = { nurse: nurseSignature, date: now }
    if (pendingAction === 'taken') {
      const dose = (typeof timeObj.dosage === 'number')
        ? timeObj.dosage
        : parseInt(medication.dosage || '1', 10)
      medication.tabsAvailable = Math.max(0, medication.total - dose);//medication.tabsAvailable
      const formatted = formatTime12Hour(now)
      timeObj.time = timeObj.time + " (taken at " + formatted + ")"
    }
  })
  console.log("We need to Send Information to process signofff. Figure it out here");
  console.log(pendingTransactions.value);
  medFinalSignOff(pendingTransactions.value);
  pendingTransactions.value = []
  showSignOffPopup.value = false
}
//-------Hold Medication Axios Call --------//
async function holdMedication(medholddata:object)
{
   let content = {
    MedicationAdmin:{
      API_Meth:"HoldMedication",
      accountnumber:"904575107",
      npinumber:"123456789",
      patientid:"709081242",
      ordernumber:"36",
      holdobjec:medholddata
    }
   }
   axios.post('https://medadministration:8890/keyon/tswebhook.php',content)
   .then(response => {
    console.log(response.data);
    if(response.data && response.data.message =="Updated")
         {
           let returnmsg="";
           returnmsg="Medication Held Successfully";
           alert(returnmsg);
           //showAddForm.value = false;
           loadMedications();
           return ;

         }
         else{
           //now lets add the information to the Appropriate tables 
           let returnmsg="";
           returnmsg="Medications Sign Off was unsuccessfull";

           return returnmsg;
           //alert("Meciation Settings Not Updated Successfully, Please try again later");
         }
   })
   .catch(error => {
    if (axios.isAxiosError(error)) {  
             console.error('Error posting data:', error.response?.data || error.message);     
          //  errorMessage = error.response?.data?.message || 'An error occurred while posting data.';  
           } 
            else {          
              console.log('Unexpected error:', error);         
            //  errorMessage = 'An unexpected error occurred. Please try again.'; 
            }    
   });
   
}
//--------FinalSignOff Async Axios Call ------//
async function medFinalSignOff(pendingTrans:object)
{
  let content = {
    MedicationAdmin: {
      API_Meth:"MedSignOff",
      accountnumber:"904575107", 
      npinumber:"123456789",
      proflicensenumber:"609382", //insurance test
      patientid:"709081242",
      ordernumber:'36', 
      signoffObj:pendingTrans
    }
  }
  axios.post('https://medadministration:8890/keyon/tswebhook.php', content)
         .then(response => {        
          console.log('Meds signed off successfully:', response.data);  
          if(response.data && response.data.message =="Updated")
         {
           let returnmsg="";
           returnmsg="Medication Signed off Successfully";
           alert(returnmsg);
           showAddForm.value = false;
           loadMedications();
           return ;

         }
         else{
           //now lets add the information to the Appropriate tables 
           let returnmsg="";
           returnmsg="Medications Sign Off was unsuccessfull";

           return returnmsg;
           //alert("Meciation Settings Not Updated Successfully, Please try again later");
         }
              
         }) 
         .catch(error => {       
           if (axios.isAxiosError(error)) {  
             console.error('Error posting data:', error.response?.data || error.message);     
          //  errorMessage = error.response?.data?.message || 'An error occurred while posting data.';  
           } 
            else {          
              console.log('Unexpected error:', error);         
            //  errorMessage = 'An unexpected error occurred. Please try again.'; 
            }    
            });   
}
// ---------- PRN SIGN-OFF ----------
function openPrnSignOffPopup(med: Medication, timeObj: any) {
  if (timeObj.dosage == null || timeObj.dosage === '') {
    timeObj.dosage = med.dosage || '1'
  }
  if (!timeObj.reason) {
    timeObj.reason = ''
  }
  prnSignOffMedication.value = med
  prnSignOffTimeObj.value = timeObj
  prnNurseSignature.value = ''
  showPrnSignOffPopup.value = true
}
function closePrnSignOffPopup() {
  showPrnSignOffPopup.value = false
  prnSignOffMedication.value = null
  prnSignOffTimeObj.value = null
  prnNurseSignature.value = ''
}
function handlePrnSignOff() {
  if (!prnSignOffMedication.value || !prnSignOffTimeObj.value) {
    showPrnSignOffPopup.value = false
    return
  }
  prnSignOffTimeObj.value.signedOff = {
    nurse: prnNurseSignature.value,
    date: new Date()
  }
  prnSignOffTimeObj.value.locked = true
  prnSignOffTimeObj.value.status = 'taken'
  const dose = (typeof prnSignOffTimeObj.value.dosage === 'number')
    ? prnSignOffTimeObj.value.dosage
    : parseInt(prnSignOffMedication.value.dosage || '1', 10)
  prnSignOffMedication.value.tabsAvailable =
    Math.max(0, prnSignOffMedication.value.tabsAvailable - dose)
  closePrnSignOffPopup()
}

// ---------- STAMP PRN TIME ----------
function stampPRNTime(med: Medication) {
  const limit = getTimesCountFromFrequency(med.frequency || '')
  if (!med.times) {
    med.times = []
  }
  const todayStr = formatDateToYYYYMMDD(new Date())
  const todaysPRNs = med.times.filter(entry => entry.date === todayStr)
  if (todaysPRNs.length >= limit) {
    alert(`You can only add up to ${limit} PRN timestamp(s) per day.`)
    return
  }
  if (med.frequency === '2 times daily' && med.times.length > 0) {
    const lastDose = med.times[med.times.length - 1]
    const lastDoseDateTime = new Date(lastDose.date + ' ' + lastDose.time)
    const now = new Date()
    const diffHours = (now.getTime() - lastDoseDateTime.getTime()) / (1000 * 60 * 60)
    if (diffHours < 11) {
      confirmationMessage.value = "This medication is early (less than 11 hours from the initial dose). Do you still want to give it?"
      isEarly.value = true
      showTimeConfirmationPopup.value = true
      pendingDateAndTime.value = { dateObj: new Date(), timeObj: { med }, medication: med }
      return
    }
  }
  const now = new Date()
  const timeStr = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
  const newTimeObj = {
    time: timeStr,
    status: 'PRN',
    date: todayStr,
    dosage: med.dosage || '1',
    reason: ''
  }
  med.times.push(newTimeObj)
  openPrnSignOffPopup(med, newTimeObj)
}

// ---------- TOOLTIP ----------
function getTooltipText(timeObj: any) {
  if (!timeObj.signedOff) {
    return ''
  }
  const nurseStr = timeObj.signedOff.nurse || 'Unknown Nurse'
  const reasonStr = timeObj.reason || '(none)'
  const doseStr = timeObj.dosage || '(not specified)'
  const signoffTime = timeObj.signedOff.date
    ? new Date(timeObj.signedOff.date).toLocaleString()
    : ''
  return `Nurse: ${nurseStr}\nReason: ${reasonStr}\nDosage: ${doseStr}\nSigned Off: ${signoffTime}`
}
function showTooltip(_timeObj: any) {}
function hideTooltip() {}
</script>

<style scoped>
/* Status Filter & Buttons */
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

/* Table Container */
.table-container {
  margin: 20px auto;
  width: 90%;
  overflow-x: auto; /* horizontal scroll if needed */
}
.date-range-selector {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 1rem;
}
.sign-off-button {
  margin-left: auto;
  background-color: white;
  color: #0c8687;
  border: 1px solid #dee2e6;
}
.sign-off-button:hover {
  background-color: #e9ecef;
}

/* Sorting Controls */
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

/* Category Section + Sticky Category Header */
.category-section {
  overflow-x: scroll;
  margin-bottom: 2rem;
}
.category-header {
  background-color: #0c8687;
  color: white;
  padding: 0.75rem 1rem;
  margin: 0;
  font-size: 1.2rem;
  border-radius: 4px 4px 0 0;
  /* Make it sticky */
  position: sticky;
  top: 0;
  z-index: 10;
  /* Ensures it spans the table width if desired */
  display: block;
}

/* Schedule Table */
.schedule-table {
  width: 100%;
  border-collapse: collapse;
  background-color: white;
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

/* Sticky table header row */
/* .schedule-table thead th {
  position: sticky;
  top: 0;
  background-color: #f8f9fa;
  z-index: 2;
} */
.schedule-table .sticky-header-1,  .sticky-header-2, .sticky-header-3, .sticky-header-4, .sticky-header-5, .sticky-header-6{
  position: sticky;
  top: 0;
  background-color: #f8f9fa;
  z-index: 2;
}

.schedule-table .sticky-column-1, .sticky-column-2, .sticky-column-3, .sticky-column-4, .sticky-column-5, .sticky-column-6 {
  position: sticky;
  background-color: #ffffff;
  z-index: 3;
}

.schedule-table .sticky-column-1, .sticky-header-1 {
  left: 0;
  min-width: 220px;
}

.schedule-table .sticky-column-2, .sticky-header-2 {
  left: 220px;
  min-width: 120px;
}

.schedule-table .sticky-column-3, .sticky-header-3 {
  left: 340px;
  min-width: 140px;
}

.schedule-table .sticky-column-4, .sticky-header-4 {
  left: 480px;
  min-width: 100px;
}

.schedule-table .sticky-column-5, .sticky-header-5 {
  left: 580px;
  min-width: 100px;
}

.schedule-table .sticky-column-6, .sticky-header-6 {
  left: 680px;
  min-width: 200px;
}

/* STICKY COLUMNS */

/* (Columns 7+ scroll normally.) */

.schedule-table th,
.schedule-table td {
  border: 1px solid #ddd;
  padding: 12px;
  text-align: center;
}
.tabs-available {
  /* background-color: #d4edda; */
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

/* Select Time and Dosage Button */
.select-time-dosage {
  /* background-color: #d4edda; */
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

/* Table "Active Row" styling */
.medication-row.active-row {
  background-color: #d4edda;
}

/* PRN indicator */
.prn-indicator {
  font-style: italic;
  color: #666;
  cursor: pointer;
  background-color: white;
  border: 1px solid #ddd;
  border-radius: 4px;
  padding: 8px 24px;
  font-size: 14px;
  text-decoration: none;
  transition: background-color 0.2s;
}
.prn-indicator:hover {
  background-color: #f8f9fa;
  color: #007bff;
}
.prn-times-list {
  margin-top: 0.5rem;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

/* Time-entry styles */
.time-entry {
  cursor: pointer;
  margin: 4px 0;
  border-radius: 4px;
  padding: 2px 6px;
  transition: background-color 0.2s;
}
.time-entry:hover {
  background-color: #fafafa;
}
.time-entry.discontinue {
  background-color: #f8d7da !important;
}

/* Icons for immediate statuses */
.icon-immediate {
  font-weight: bold;
  font-size: 1rem;
  vertical-align: middle;
}

/* Tooltip Icon */
.tooltip-icon {
  margin-left: 4px;
  cursor: help;
  font-size: 0.9rem;
  vertical-align: middle;
}

/* Modal Overlays */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0,0,0,0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}
.modal-content {
  background: white;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  width: 90%;
  max-width: 400px;
  padding: 2rem;
  margin-top:0;
  overflow-y:scroll;
  height:420px;
}

/* Button Rows */
.button-row {
  display: flex;
  justify-content: space-around;
  margin-top: 1rem;
}
.button-row button {
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

/* Input Styles */
.modal-content input[type="text"] {
  width: 100%;
  padding: 0.5rem;
  margin-bottom: 1rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 1rem;
}
.btn-green {
  background-color: #28a745;
  color: #fff;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 4px;
  cursor: pointer;
}

/* Form Groups */
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
</style>
