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
        <button class="add-manually-btn" @click="onAddMedication">
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
                  <!-- Medication Info: clickable name => edit -->
                  <td class="sticky-column-1">
                    <span
                      class="medication-link"
                      @click="openMedicationForm(med)"
                    >
                      {{ med.name }}
                    </span>
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
                        v-model="med.tabsAvailable"
                        @change="handleTabsChange(med, $event.target.value)"
                        class="tabs-input"
                      />
                    </div>
                  </td>

                  <!-- Frequency & Dosage (hidden if collapsed) -->
                  <td v-if="!collapsed" class="sticky-column-4">
                    {{ med.frequency || 'Not set' }}
                  </td>
                  <td v-if="!collapsed" class="sticky-column-5">
                    {{ med.dosage || 'Not set' }}
                  </td>

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
                          <!-- Only show if base time matches category if sorting by time -->
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

    <!-- AddMedicationForm (Add + Edit) -->
    <AddMedicationForm
      :show="showAddForm"
      :existingMedication="editingMedication"
      @close="showAddForm = false"
      @save="handleMedicationFormSave"
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

    <!-- "Taken / Refused / Later" Popup -->
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

          <!-- TAKEN grouping -->
          <div
            v-for="(group, idx) in takenGrouped"
            :key="'taken-'+idx"
          >
            <h4 class="status-time-header taken-time">
              Taken Time: {{ group.time }}
            </h4>
            <div
              v-for="(item, iIdx) in group.items"
              :key="iIdx"
              class="sign-off-item"
            >
              <span class="taken-icon">✔</span>
              <span class="sign-off-med">{{ item.medication.name }}</span>
            </div>
          </div>

          <!-- REFUSED grouping -->
          <div
            v-for="(group, idx) in refusedGrouped"
            :key="'refused-'+idx"
          >
            <h4 class="status-time-header refused-time">
              Refused Time: {{ group.time }}
            </h4>
            <div
              v-for="(item, iIdx) in group.items"
              :key="iIdx"
              class="sign-off-item"
            >
              <span class="refused-icon">✘</span>
              <span class="sign-off-med">{{ item.medication.name }}</span>
            </div>
          </div>

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
import AddMedicationForm from './AddMedicationForm.vue'
import HoldTimeSelector from './HoldTimeSelector.vue'

const FUTURE_DAYS_TO_POPULATE = 365

/** Medication interface with fields for Rx/Provider/Pharmacy info as well */
export interface Medication {
  name: string
  ndcNumber?: string
  rxNorm?: string
  dates?: Record<string, {
    time: string
    status?: string
    locked?: boolean
    signedOff?: any
    temporaryStatus?: string
    dosage?: number|string
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

  /** Pharmacy info */
  pharmacy?: string
  pharmacyNpi?: string
  pharmacyAddress?: string
  pharmacyPhone?: string
  pharmacyDea?: string

  /** Provider/prescriber info */
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

  times?: Array<{
    time: string
    status?: string
    locked?: boolean
    dosage?: number|string
    earlyReason?: string
    reason?: string
    date: string
  }>
}

const router = useRouter()
const medications = ref<Medication[]>([])

// Sort
const sortBy = ref<string>('')
const currentDate = ref(new Date())

// Collapse toggle
const collapsed = ref(true)
function toggleCollapse() {
  collapsed.value = !collapsed.value
}

// For add/edit
const showAddForm = ref(false)
/** If null => new; else => editing */
const editingMedication = ref<any | null>(null)

/** "Add Manually" => new med */
function onAddMedication() {
  editingMedication.value = null
  showAddForm.value = true
}

/**
 * "Click medication name" => edit
 * Copy both "Medication Information" fields and also the Prescription/Provider/Pharmacy fields
 * so that the child form can prefill them.
 */
function openMedicationForm(med: Medication) {
  editingMedication.value = {
    ...med,
    originalName: med.name,

    // For the "Medication Information" tab:
    medicationName: med.name,
    quantity: med.tabsAvailable,
    ndcNumber: med.ndcNumber || '',
    rxNorm: med.rxNorm || '',

    // For the other tabs:
    rxNumber: med.rxNumber || '',
    refills: med.refills ?? 0,
    pharmacy: med.pharmacy || '',
    pharmacyNpi: med.pharmacyNpi || '',
    pharmacyAddress: med.pharmacyAddress || '',
    pharmacyPhone: med.pharmacyPhone || '',
    pharmacyDea: med.pharmacyDea || '',
    prescriberInfo: med.prescriberInfo || '',
    prescriberDeaNpi: med.prescriberDeaNpi || ''
  }
  showAddForm.value = true
}

/** Handle form saving */
function handleMedicationFormSave(payload: any) {
  const isEdit = payload.isEdit
  const originalName = payload.originalName

  if (!isEdit) {
    // Creating new medication
    const newMedication: Medication = {
      // Medication Info tab
      name: payload.medicationName,
      ndcNumber: payload.ndcNumber || '',
      rxNorm: payload.rxNorm || '',
      tabsAvailable: payload.quantity || 0,
      frequency: payload.frequency,
      dosage: payload.dosage,
      route: payload.route,
      prn: payload.prn,
      diagnosis: payload.diagnosis || '',

      // Prescription/Provider/Pharmacy tabs
      rxNumber: payload.rxNumber || '',
      refills: payload.refills || 0,
      pharmacy: payload.pharmacy || '',
      pharmacyNpi: payload.pharmacyNpi || '',
      pharmacyAddress: payload.pharmacyAddress || '',
      pharmacyPhone: payload.pharmacyPhone || '',
      pharmacyDea: payload.pharmacyDea || '',
      prescriberInfo: payload.prescriberInfo || '',
      prescriberDeaNpi: payload.prescriberDeaNpi || '',

      administrationTimes: '',
      dates: {}
    }
    medications.value.push(newMedication)
    populateMedicationTable()
  } else {
    // Updating existing
    const idx = medications.value.findIndex(m => m.name === originalName)
    if (idx !== -1) {
      // Medication Info tab
      medications.value[idx].name = payload.medicationName
      medications.value[idx].ndcNumber = payload.ndcNumber
      medications.value[idx].rxNorm = payload.rxNorm
      medications.value[idx].frequency = payload.frequency
      medications.value[idx].dosage = payload.dosage
      medications.value[idx].route = payload.route
      medications.value[idx].tabsAvailable = payload.quantity
      medications.value[idx].prn = payload.prn
      medications.value[idx].diagnosis = payload.diagnosis

      // Prescription/Provider/Pharmacy tabs
      medications.value[idx].rxNumber = payload.rxNumber
      medications.value[idx].refills = payload.refills
      medications.value[idx].pharmacy = payload.pharmacy
      medications.value[idx].pharmacyNpi = payload.pharmacyNpi
      medications.value[idx].pharmacyAddress = payload.pharmacyAddress
      medications.value[idx].pharmacyPhone = payload.pharmacyPhone
      medications.value[idx].pharmacyDea = payload.pharmacyDea
      medications.value[idx].prescriberInfo = payload.prescriberInfo
      medications.value[idx].prescriberDeaNpi = payload.prescriberDeaNpi

      populateMedicationTable()
    }
  }
  showAddForm.value = false
}

/** The rest is your existing watchers, date-range, hold logic, time logic, etc. */

const selectedStatus = ref<string | null>(null)
function handleStatusFilter(status: string | null) {
  selectedStatus.value = selectedStatus.value === status ? null : status
}

const showHoldSelector = ref(false)
const selectedMedicationForHold = ref<Medication | null>(null)
const selectedStatusOption = ref<'hold' | 'new' | 'discontinue' | 'change'>('hold')

const showTimeModal = ref(false)
const selectedMedicationForTime = ref<Medication | null>(null)
const selectedFrequency = ref('')
const selectedDosage = ref('1')
const timeInputs = ref<string[]>([])

const showTimeActionPopup = ref(false)
const selectedDateAndTime = ref<{ dateObj: Date; timeObj: any; medication: Medication } | null>(null)

const showTimeConfirmationPopup = ref(false)
const confirmationMessage = ref("")
const pendingDateAndTime = ref<{ dateObj: Date; timeObj: any; medication: Medication } | null>(null)
const isEarly = ref(false)
const showEarlyReasonInput = ref(false)
const earlyReason = ref("")

const showErrorModal = ref(false)
const errorMessage = ref("")

const showSignOffPopup = ref(false)
const pendingTransactions = ref<any[]>([])

const showPrnSignOffPopup = ref(false)
const prnSignOffMedication = ref<Medication | null>(null)
const prnSignOffTimeObj = ref<any>(null)
const prnNurseSignature = ref('')

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
    const prnMeds = sortedMeds.filter(m => m.prn)
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

const dateList = ref<Date[]>([])
const allColumns = computed(() => {
  if (dateList.value.length > 0) {
    const cols = dateList.value.map(d => normalizeToMidnight(d))
    cols.sort((a, b) => a.getTime() - b.getTime())
    return cols
  } else {
    return [normalizeToMidnight(currentDate.value)]
  }
})

onMounted(() => {
  loadMedications()
  initializeDateRangePicker()
  populateMedicationTable()
})

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

function loadMedications() {
  const savedMedications = localStorage.getItem('medications')
  if (savedMedications) {
    medications.value = JSON.parse(savedMedications)
  }
}
watch(medications, (newVal) => {
  localStorage.setItem('medications', JSON.stringify(newVal))
}, { deep: true })

const localProps = withDefaults(defineProps<{ medications?: Medication[] }>(), {
  medications: () => []
})
watch(() => localProps.medications, (newMeds) => {
  if (!localStorage.getItem('medications')) {
    medications.value = [...newMeds]
  }
}, { immediate: true })

const localEmit = defineEmits<{
  (e: 'statusChange', medication: Medication, status: string): void;
  (e: 'medicationTaken', medication: Medication, time: string, action: string): void;
  (e: 'signatureSubmit', signature: string, medications: Medication[], time: string): void;
  (e: 'tabsChange', medication: Medication, tabs: number): void;
}>()

watch(selectedFrequency, (newFreq, oldFreq) => {
  if (newFreq === oldFreq) return  // or if (!newFreq || newFreq === oldFreq)...

  // Now do the usual reset
  if (!newFreq || selectedMedicationForTime.value?.prn) {
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
function toggleSelectDropdown(medication: Medication) {
  if (medication.tabsAvailable <= 0) {
    errorMessage.value = "Please add tabs available"
    showErrorModal.value = true
    return
  }
  selectedMedicationForTime.value = medication
  selectedFrequency.value = medication.frequency || ''
  selectedDosage.value = medication.dosage || '1'
  if (medication.administrationTimes && medication.administrationTimes !== 'As needed') {
    const splitted = medication.administrationTimes.split(',')
    timeInputs.value = splitted.map(t => t.trim())
  } else {
    timeInputs.value = []
  }
  showTimeModal.value = true
}
function handleSave() {
  if (!selectedMedicationForTime.value) {
    showTimeModal.value = false
    return
  }
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

    const todayMidnight = normalizeToMidnight(new Date())
    if (med.dates) {
      for (const dStr of Object.keys(med.dates)) {
        const d = new Date(dStr)
        console.log(normalizeToMidnight(d).getTime(), todayMidnight.getTime())
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
      })
    }
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

function populateMedicationTable() {
  medications.value.forEach(med => {
    if (med.prn) return
    if (!med.administrationTimes || med.administrationTimes === 'As needed') return

    if (!med.dates) {
      med.dates = {}
    }

    dateList.value.forEach(d => {
      const dStr = formatDateToYYYYMMDD(d)
      if (med.discontinuedDate && normalizeToMidnight(d).getTime() > normalizeToMidnight(med.discontinuedDate).getTime()) {
        return
      }
      if (!med.dates![dStr]) {
        const splitted = med.administrationTimes.split(',').map(t => t.trim())
        const dosageNum = parseInt(med.dosage || '1', 10)
        med.dates![dStr] = splitted.map(t => ({
          time: t,
          status: 'pending',
          dosage: dosageNum
        }))
      }
    })
  })
}

function handleStatusChange(event: Event, medIndex: number) {
  const select = event.target as HTMLSelectElement
  const status = select.value
  medications.value[medIndex].status = status

  if (status === 'hold' || status === 'new' || status === 'discontinue' || status === 'change') {
    selectedMedicationForHold.value = medications.value[medIndex]
    selectedStatusOption.value = status as 'hold' | 'new' | 'discontinue' | 'change'
    showHoldSelector.value = true
    return
  }
  emit('statusChange', medications.value[medIndex], status)
}

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
    dateRange: data.dateRange,
    times: data.times,
    reason: data.reason,
    type: data.holdType
  }
  showHoldSelector.value = false
  selectedMedicationForHold.value = null
}

function closeErrorModal() {
  showErrorModal.value = false
}

function handleTabsChange(medication: Medication, newValue: number) {
  medication.tabsAvailable = newValue
  emit('tabsChange', medication, newValue)
}

function getRowStatusClass(_medication: Medication) {
  return 'active-row'
}

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
    if (action === 'later') {
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

// Early / Late
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

// Sign Off
function groupTransactionsByTime(transactions: any[]) {
  const map: Record<string, any[]> = {}
  transactions.forEach(item => {
    const rawTime = item.timeObj.time.includes("(")
      ? item.timeObj.time.split("(")[0].trim()
      : item.timeObj.time
    if (!map[rawTime]) {
      map[rawTime] = []
    }
    map[rawTime].push(item)
  })
  return Object.entries(map).map(([time, items]) => ({ time, items }))
}
const takenGrouped = computed(() => {
  const takenItems = pendingTransactions.value.filter(pt => pt.timeObj.temporaryStatus === 'taken')
  return groupTransactionsByTime(takenItems)
})
const refusedGrouped = computed(() => {
  const refusedItems = pendingTransactions.value.filter(pt => pt.timeObj.temporaryStatus === 'refused')
  return groupTransactionsByTime(refusedItems)
})
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
      medication.tabsAvailable = Math.max(0, medication.tabsAvailable - dose)
      const formatted = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
      timeObj.time = timeObj.time + " (taken at " + formatted + ")"
    }
  })
  pendingTransactions.value = []
  showSignOffPopup.value = false
}

// PRN sign off
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

// Just a stub so it compiles; in real code you’d implement your “stampPRNTime”.
function stampPRNTime(_med: Medication) {}
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
/* EXACT same styling from your code, nothing removed. */

/* Make the medication name clickable */
.medication-link {
  color: #007bff;
  text-decoration: underline;
  cursor: pointer;
}
.medication-link:hover {
  color: #0056b3;
}

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
  overflow-x: auto; 
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
  position: sticky;
  top: 0;
  z-index: 10;
  display: block;
}

/* Schedule Table */
.schedule-table {
  width: 100%;
  border-collapse: collapse;
  background-color: white;
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}
/* Sticky table headers */
.schedule-table .sticky-header-1,
.schedule-table .sticky-header-2,
.schedule-table .sticky-header-3,
.schedule-table .sticky-header-4,
.schedule-table .sticky-header-5,
.schedule-table .sticky-header-6 {
  position: sticky;
  top: 0;
  background-color: #f8f9fa;
  z-index: 2;
}
.schedule-table .sticky-column-1,
.schedule-table .sticky-column-2,
.schedule-table .sticky-column-3,
.schedule-table .sticky-column-4,
.schedule-table .sticky-column-5,
.schedule-table .sticky-column-6 {
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
.schedule-table th,
.schedule-table td {
  border: 1px solid #ddd;
  padding: 12px;
  text-align: center;
}
/* Tabs Available */
.tabs-available {
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

/* Sign-Off styling */
.status-time-header {
  font-weight: bold;
  margin: 1rem 0 0.5rem;
  font-size: 1rem;
}
.taken-time {
  color: #28a745;
}
.refused-time {
  color: #dc3545;
}
.sign-off-item {
  display: flex;
  align-items: center;
  margin-left: 1.5rem;
  margin-bottom: 8px;
}
.taken-icon {
  color: #28a745;
  margin-right: 0.4rem;
  font-size: 1.1rem;
}
.refused-icon {
  color: #dc3545;
  margin-right: 0.4rem;
  font-size: 1.1rem;
}
.sign-off-med {
  font-weight: 500;
}
</style>
