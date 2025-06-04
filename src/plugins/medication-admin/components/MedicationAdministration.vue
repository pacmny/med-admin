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

      <!-- Sorting Controls + Signature Button + Expand/Collapse -->
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

        <!-- Signature Button (was "Sign Off") -->
        <button
          class="sort-button sign-off-button"
          @click="showSignOffPopup = true"
        >
          Signature
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
                  <th v-if="!collapsed" class="sticky-header-3">Amount Available</th>
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
                  <!-- Medication Info: clickable name => edit 
                       PLUS the new tooltip icon showing the nurse/time from Add New Medication -->
                  <td class="sticky-column-1">
                    <span
                      class="medication-link"
                      @click="openMedicationForm(med)"
                    >
                      {{ med.name }}
                    </span>
                    <!-- Tooltip icon for nurse/time from Add New Medication -->
                    <span
                      v-if="getAddMedicationTooltip(med)"
                      class="tooltip-icon"
                      :title="getAddMedicationTooltip(med)"
                      style="margin-left: 4px;"
                    >
                      ℹ️
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

                  <!-- Amount Available: numeric input + dosage-type dropdown -->
                  <td v-if="!collapsed" class="tabs-available sticky-column-3">
                    <div class="tabs-counter">
                      <!-- Numeric input for quantity -->
                      <input
                        type="number"
                        v-model="med.tabsAvailable"
                        @change="handleTabsChange(med, $event.target.value)"
                        class="tabs-input"
                      />
                    </div>
                    <!-- Dosage type dropdown (unitType) -->
                    <div class="unit-dropdown">
                      <select v-model="med.unitType" class="unit-select">
                        <option value="" disabled>Select Dosage Type</option>
                        <option
                          v-for="option in unitOptions"
                          :key="option"
                          :value="option"
                        >
                          {{ option }}
                        </option>
                      </select>
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
                        <!-- Only show "As needed" if no PRN times exist for that date -->
                        <div
                          class="prn-indicator"
                          v-if="!hasPrnTimesForDate(med, dateObj)"
                          @click="stampPRNTime(med)"
                        >
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
                            @click="!timeObj.locked && handleTimeClick(dateObj, timeObj, med)"
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

                            <BarcodeScanner
    v-if="scannerContext && scannerContext.timeObj === timeObj"
    @scanned="onBarcodeScanned"
    @close="scannerContext = null"
    :active="true"
  />
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
          <label>Dosage ({{selectedMedicationForTime.unitType || 'unit'}} per admin time):</label>
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

    <!-- Signature Popup (was Sign-Off Popup) -->
    <div v-if="showSignOffPopup" class="modal-overlay">
      <div class="modal-content">
        <h3>Signature Pending Transactions</h3>
        <div v-if="pendingTransactions.length === 0">
          <p>No medications pending signature.</p>
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

          <!-- Nurse Signature -->
          <div class="form-group">
            <label for="signOffNurseSignature">Nurse Signature:</label>
            <input
              type="text"
              id="signOffNurseSignature"
              v-model="signOffNurseSignature"
              placeholder="Enter your name or initials"
            />
          </div>

          <div class="button-row">
            <button @click="finalSignOff" class="save-button" :disabled="!signOffNurseSignature">
              Signature
            </button>
            <button @click="showSignOffPopup = false" class="cancel-button">Cancel</button>
          </div>
        </div>
      </div>
    </div>

    <!-- PRN Signature Popup (was PRN Sign-Off Popup) -->
    <div v-if="showPrnSignOffPopup" class="modal-overlay">
      <div class="modal-content">
        <h3>PRN Signature</h3>
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
            Signature
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
import { ref, computed, watch, onMounted, defineProps, withDefaults, defineEmits, nextTick } from 'vue'
import { useRouter } from 'vue-router'
import 'flatpickr/dist/flatpickr.css'
import flatpickr from 'flatpickr'
import AddMedicationForm from './AddMedicationForm.vue'
import HoldTimeSelector from './HoldTimeSelector.vue'
import BarcodeScanner from './barcode-scanner/BarcodeScanner.vue'

/** How many future days to populate scheduled times. */
const FUTURE_DAYS_TO_POPULATE = 365

/** Medication interface */
export interface Medication {
  name: string
  ndcNumber?: string
  rxNorm?: string

  /** Scheduled times, keyed by date: */
  dates?: Record<string, {
    time: string
    status?: string
    locked?: boolean
    signedOff?: any
    temporaryStatus?: string
    dosage?: number|string
    earlyReason?: string
    reason?: string
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

  /**
   * If user discontinues only certain times after a certain date,
   * we store them here so those times won't appear for *future* days
   * (although day-of is shown in red).
   */
  discontinuedTimes?: Record<string, string[]>

  /** Link dosage type to the dropdown in "Amount Available" */
  unitType?: string

  /** For "Add New Medication" nurse + time */
  addedByNurse?: string
  addedTimestamp?: string

  /** For hold reasons, etc. */
  holdInfo?: any

  /** For PRN meds, we store usage times in a separate array. */
  times?: {
    time: string
    status: string
    date: string
    locked?: boolean
    signedOff?: any
    dosage?: number|string
    earlyReason?: string
    reason?: string
  }[]
}

const router = useRouter()
const medications = ref<Medication[]>([])

/** We reuse the same unit options for both the "AddMedicationForm" and the dropdown here. */
const unitOptions = [
  "Actuation", "Ampule", "Application", "Applicator", "Auto-Injector", "Bar",
  "Capful", "Caplet", "Capsule", "Cartridge", "Centimeter", "Disk", "Dropperful",
  "Each", "Film", "Fluid Ounce", "Gallon", "Gram", "Gum", "Implant", "Inch",
  "Inhalation", "Injection", "Insert", "Liter", "Lollipop", "Lozenge", "Metric Drop",
  "Microgram", "Milliequivalent", "Milligram", "Milliliter", "Nebule", "Ounce",
  "Package", "Packet", "Pad", "Patch", "Pellet", "Pill", "Pint", "Pre-filled Pen Syringe",
  "Puff", "Pump", "Ring", "Sachet", "Scoopful", "Sponge", "Spray", "Stick", "Strip",
  "Suppository", "Swab", "Syringe", "Tablet", "Troche", "Unit", "Vial", "Wafer"
]

// Sorting
const sortBy = ref<string>('')
const currentDate = ref(new Date())

// Collapse toggle
const collapsed = ref(true)
function toggleCollapse() {
  collapsed.value = !collapsed.value
}

const showBarcodeScanner = ref(false)
const scannerContext = ref<{ med: Medication, timeObj: any, dateObj: Date } | null>(null)

// Add/edit med
const showAddForm = ref(false)
const editingMedication = ref<any | null>(null)

function onAddMedication() {
  editingMedication.value = null
  showAddForm.value = true
}

function openMedicationForm(med: Medication) {
  editingMedication.value = {
    ...med,
    originalName: med.name,

    medicationName: med.name,
    quantity: med.tabsAvailable,
    ndcNumber: med.ndcNumber || '',
    rxNorm: med.rxNorm || '',

    rxNumber: med.rxNumber || '',
    refills: med.refills ?? 0,
    pharmacy: med.pharmacy || '',
    pharmacyNpi: med.pharmacyNpi || '',
    pharmacyAddress: med.pharmacyAddress || '',
    pharmacyPhone: med.pharmacyPhone || '',
    pharmacyDea: med.pharmacyDea || '',
    prescriberInfo: med.prescriberInfo || '',
    prescriberDeaNpi: med.prescriberDeaNpi || '',

    unitType: med.unitType || '',
    nurseSignature: med.addedByNurse || ''
  }
  showAddForm.value = true
}

function handleMedicationFormSave(payload: any) {
  const isEdit = payload.isEdit
  const originalName = payload.originalName
  const nurseSignature = payload.nurseSignature || ''

  if (!isEdit) {
    const newMedication: Medication = {
      name: payload.medicationName,
      ndcNumber: payload.ndcNumber || '',
      rxNorm: payload.rxNorm || '',
      tabsAvailable: payload.quantity || 0,
      frequency: payload.frequency,
      dosage: payload.dosage,
      route: payload.route,
      prn: payload.prn,
      diagnosis: payload.diagnosis || '',
      unitType: payload.unitType || '',
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
      dates: {},
      addedByNurse: nurseSignature,
      addedTimestamp: new Date().toISOString()
    }
    medications.value.push(newMedication)
    populateMedicationTable()
  } else {
    const idx = medications.value.findIndex(m => m.name === originalName)
    if (idx !== -1) {
      medications.value[idx].name = payload.medicationName
      medications.value[idx].ndcNumber = payload.ndcNumber
      medications.value[idx].rxNorm = payload.rxNorm
      medications.value[idx].frequency = payload.frequency
      medications.value[idx].dosage = payload.dosage
      medications.value[idx].route = payload.route
      medications.value[idx].tabsAvailable = payload.quantity
      medications.value[idx].prn = payload.prn
      medications.value[idx].diagnosis = payload.diagnosis
      medications.value[idx].unitType = payload.unitType
      medications.value[idx].rxNumber = payload.rxNumber
      medications.value[idx].refills = payload.refills
      medications.value[idx].pharmacy = payload.pharmacy
      medications.value[idx].pharmacyNpi = payload.pharmacyNpi
      medications.value[idx].pharmacyAddress = payload.pharmacyAddress
      medications.value[idx].pharmacyPhone = payload.pharmacyPhone
      medications.value[idx].pharmacyDea = payload.pharmacyDea
      medications.value[idx].prescriberInfo = payload.prescriberInfo
      medications.value[idx].prescriberDeaNpi = payload.prescriberDeaNpi
      medications.value[idx].addedByNurse = nurseSignature
      medications.value[idx].addedTimestamp = new Date().toISOString()

      populateMedicationTable()
    }
  }
  showAddForm.value = false
}

// Status filter
const selectedStatus = ref<string | null>(null)
function handleStatusFilter(status: string | null) {
  selectedStatus.value = selectedStatus.value === status ? null : status
}

const showHoldSelector = ref(false)
const selectedMedicationForHold = ref<Medication | null>(null)
const selectedStatusOption = ref<'hold' | 'new' | 'discontinue' | 'change'>('hold')

// Time & Dosage Modal
const showTimeModal = ref(false)
const selectedMedicationForTime = ref<Medication | null>(null)
const selectedFrequency = ref('')
const selectedDosage = ref('1')
const timeInputs = ref<string[]>([])

// "Taken / Refused / Later" Popup
const showTimeActionPopup = ref(false)
const selectedDateAndTime = ref<{ dateObj: Date; timeObj: any; medication: Medication } | null>(null)

// Early/Late check
const showTimeConfirmationPopup = ref(false)
const confirmationMessage = ref("")
const pendingDateAndTime = ref<{ dateObj: Date; timeObj: any; medication: Medication } | null>(null)
const isEarly = ref(false)
const showEarlyReasonInput = ref(false)
const earlyReason = ref("")

// Error modal
const showErrorModal = ref(false)
const errorMessage = ref("")

// Signature popup
const showSignOffPopup = ref(false)
const signOffNurseSignature = ref('')
const pendingTransactions = ref<any[]>([])

// PRN signature popup
const showPrnSignOffPopup = ref(false)
const prnSignOffMedication = ref<Medication | null>(null)
const prnSignOffTimeObj = ref<any>(null)
const prnNurseSignature = ref('')

const frequencyOptions = [
  '1 time daily','2 times daily',
  '3 times daily','4 times daily',
  'as directed','as needed','as one dose',
  'at bedtime',
  'before every meal','bi-weekly','constant infusion','daily',
  'daily as directed','every day','every month','every other day','every morning','every evening',
  'every hour','every 2 hours',
  'every 3 hours','every 4 hours',
  'every 4 to 6 hours, as needed','every 4 to 6 minutes','every 4 to 8 hours',
  'every 6 hours',
  'every 8 hours','every 12 hours',
  'every 24 hours',
  'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday',
  'every Monday, Wednesday, Friday, Sunday',
  'every Tuesday, Thursday, Saturday','before breakfast, lunch, dinner','after breakfast, lunch, dinner',
  'once a week','one time dose',
  'three times a week','twice daily',
  'two times a week','use as directed per instructions in pack','weekly',
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
function formatDateToYYYYMMDD(d: Date): string {
  return (
    d.getFullYear() +
    '-' +
    String(d.getMonth() + 1).padStart(2, '0') +
    '-' +
    String(d.getDate()).padStart(2, '0')
  )
}

/** Tooltip for which nurse added med + timestamp */
function getAddMedicationTooltip(med: Medication) {
  if (!med.addedByNurse || !med.addedTimestamp) return ''
  const addedTime = new Date(med.addedTimestamp).toLocaleString()
  return `Nurse: ${med.addedByNurse}\nAdded On: ${addedTime}`
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
  'PRN','Neb/INH','Oral/Sublingual','IVI Intravaginal','SQ (Subcutaneous)',
  'IM (Intramuscular)','IV (Intravenous)','ID (Intradermal)',
  'NAS Intranasal','TD Transdermal','TOP Topical','Urethral','Rectally',
  'Optic','Otic'
]

function isMedicationVisible(med: Medication): boolean {
  // If no discontinuedDate => show
  if (!med.discontinuedDate) return true

  // If no date range => show
  if (dateList.value.length === 0) return true

  // If earliest day is after med.discontinuedDate => hide the med entirely
  const earliestDay = normalizeToMidnight(dateList.value[0])
  if (earliestDay.getTime() > normalizeToMidnight(med.discontinuedDate).getTime()) {
    return false
  }
  return true
}

const groupedMedications = computed(() => {
  let sortedMeds = [...medications.value].filter(med => isMedicationVisible(med))

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
    case 'every other day':
      return 1
    case 'monday, wednesday, friday, sunday': return 4
    case 'tuesday, thursday, saturday': return 3
    default: return 1
  }
}

/** For PRN meds, we check if any times exist that day to hide "As needed" */
function hasPrnTimesForDate(med: Medication, dateObj: Date): boolean {
  if (!med.times) return false
  const dateStr = formatDateToYYYYMMDD(dateObj)
  return med.times.some(entry => entry.date === dateStr)
}

function toggleSelectDropdown(med: Medication) {
  if (med.tabsAvailable <= 0) {
    errorMessage.value = "Please add tabs available"
    showErrorModal.value = true
    return
  }
  selectedMedicationForTime.value = med

  let splitted: string[] = []
  if (med.administrationTimes && med.administrationTimes !== 'As needed') {
    splitted = med.administrationTimes.split(',').map(t => t.trim())
  }

  selectedFrequency.value = ''
  timeInputs.value = []

  nextTick(() => {
    selectedFrequency.value = med.frequency || ''
    selectedDosage.value = med.dosage || '1'
    if (!med.prn && splitted.length > 0) {
      timeInputs.value = splitted
    }
    showTimeModal.value = true
  })
}

watch(selectedFrequency, (newFreq) => {
  if (selectedMedicationForTime.value?.prn || !newFreq) {
    timeInputs.value = []
    return
  }
  const count = getTimesCountFromFrequency(newFreq)
  const existingTimes = [...timeInputs.value]
  timeInputs.value = Array.from({ length: count }, (_, i) =>
    i < existingTimes.length ? existingTimes[i] : ''
  )
})

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
    const newTimeArray = timeInputs.value
      .filter(t => t)
      .map(t => ({
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
      if (med.discontinuedDate &&
          normalizeToMidnight(futureDate).getTime() > normalizeToMidnight(med.discontinuedDate).getTime()) {
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
            if (
              !med.dates[ds][idx].locked &&
              med.dates[ds][idx].status !== 'discontinue' &&
              med.dates[ds][idx].status !== 'hold'
            ) {
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

/** Populate the table for each medication's daily times. */
function populateMedicationTable() {
  medications.value.forEach(med => {
    if (med.prn) return
    if (!med.administrationTimes || med.administrationTimes === 'As needed') return

    if (!med.dates) {
      med.dates = {}
    }

    dateList.value.forEach(d => {
      const dStr = formatDateToYYYYMMDD(d)
      if (
        med.discontinuedDate &&
        normalizeToMidnight(d).getTime() > normalizeToMidnight(med.discontinuedDate).getTime()
      ) {
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
  localEmit('statusChange', medications.value[medIndex], status)
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
function parseDateKey(key: string): Date {
  const [y, m, d] = key.split('-').map(Number)
  return new Date(y, m - 1, d)
}

/**
 * When user picks dateRange + times + reason + holdType + status
 */
function handleHoldSubmit(data: {
  dateRange: [Date, Date];
  times: string[] | null;
  reason: string;
  holdType: 'all' | 'specific';
  statusOption?: 'hold' | 'new' | 'discontinue' | 'change';
}) {
  if (!selectedMedicationForHold.value) return
  const medication = selectedMedicationForHold.value
  medication.holdInfo = data

  // Shared references
  const startDate = normalizeToMidnight(data.dateRange[0])
  const startDateStr = formatDateToYYYYMMDD(startDate)

  // --- 1) DISCONTINUE LOGIC (unchanged, as you said it's perfect) ---
  if (data.statusOption === 'discontinue') {
    if (data.holdType === 'all') {
      // Discontinue entire medication
      medication.discontinuedDate = startDate
      if (medication.dates) {
        // On the discontinuation date => set all to red (status='discontinue')
        if (medication.dates[startDateStr]) {
          medication.dates[startDateStr].forEach(slot => {
            slot.status = 'discontinue'
          })
        }
        // Remove future dates entirely
        for (const dKey of Object.keys(medication.dates)) {
          const dObj = normalizeToMidnight(parseDateKey(dKey))
          if (dObj.getTime() > startDate.getTime()) {
            delete medication.dates[dKey]
          }
        }
      }
    } else if (data.holdType === 'specific') {
      // Discontinue only selected time(s)
      if (!data.times || data.times.length === 0) {
        showHoldSelector.value = false
        selectedMedicationForHold.value = null
        return
      }
      if (!medication.discontinuedTimes) {
        medication.discontinuedTimes = {}
      }
      // On the exact start date => mark those times 'discontinue'
      if (medication.dates && medication.dates[startDateStr]) {
        medication.dates[startDateStr].forEach(slot => {
          if (data.times!.includes(slot.time)) {
            slot.status = 'discontinue'
          }
        })
      }
      // Remove those times from all future dates
      const endDate = new Date(startDate)
      endDate.setDate(endDate.getDate() + FUTURE_DAYS_TO_POPULATE)
      let day = new Date(startDate)
      day.setDate(day.getDate() + 1)

      while (day <= endDate) {
        const ds = formatDateToYYYYMMDD(day)
        if (!medication.discontinuedTimes[ds]) {
          medication.discontinuedTimes[ds] = []
        }
        data.times.forEach(tStr => {
          if (!medication.discontinuedTimes[ds].includes(tStr)) {
            medication.discontinuedTimes[ds].push(tStr)
          }
        })
        if (medication.dates[ds]) {
          medication.dates[ds] = medication.dates[ds].filter(
            slot => !data.times!.includes(slot.time)
          )
        }
        day.setDate(day.getDate() + 1)
      }
    }
  }

  // --- 2) HOLD LOGIC (newly added to match "discontinue" behavior) ---
  else if (data.statusOption === 'hold') {
    if (data.holdType === 'all') {
      // Hold entire medication
      //  a) On the hold date => set status='hold' (yellow)
      if (medication.dates && medication.dates[startDateStr]) {
        medication.dates[startDateStr].forEach(slot => {
          slot.status = 'hold'
        })
      }
      //  b) Remove all future days from medication.dates
      if (medication.dates) {
        for (const dKey of Object.keys(medication.dates)) {
          const dObj = normalizeToMidnight(parseDateKey(dKey))
          if (dObj.getTime() > startDate.getTime()) {
            delete medication.dates[dKey]
          }
        }
      }
    } else if (data.holdType === 'specific') {
      // Hold only selected time(s)
      if (!data.times || data.times.length === 0) {
        showHoldSelector.value = false
        selectedMedicationForHold.value = null
        return
      }
      // a) On the hold date => set those slot(s) status='hold' (yellow)
      if (medication.dates && medication.dates[startDateStr]) {
        medication.dates[startDateStr].forEach(slot => {
          if (data.times.includes(slot.time)) {
            slot.status = 'hold'
          }
        })
      }
      // b) Remove those times on all subsequent days
      const endDate = new Date(startDate)
      endDate.setDate(endDate.getDate() + FUTURE_DAYS_TO_POPULATE)
      let day = new Date(startDate)
      day.setDate(day.getDate() + 1)
      while (day <= endDate) {
        const ds = formatDateToYYYYMMDD(day)
        if (medication.dates && medication.dates[ds]) {
          medication.dates[ds] = medication.dates[ds].filter(
            slot => !data.times!.includes(slot.time)
          )
        }
        day.setDate(day.getDate() + 1)
      }
    }
  }

  // "new"/"change" logic (if any) ...
  showHoldSelector.value = false
  selectedMedicationForHold.value = null
}



function closeErrorModal() {
  showErrorModal.value = false
}
function handleTabsChange(medication: Medication, newValue: number) {
  medication.tabsAvailable = newValue
  localEmit('tabsChange', medication, newValue)
}
function getRowStatusClass(_medication: Medication) {
  return 'active-row'
}

/**
 * Return the time slots for a given dateObj,
 * skipping those in discontinuedTimes, except if it's the EXACT date of discontinuation
 * (that day will show the slot in red).
 */
function getTimesForDate(med: Medication, dateObj: Date) {
  const dateStr = formatDateToYYYYMMDD(dateObj)
  if (!med.dates || !med.dates[dateStr]) {
    return []
  }
  let slots = [...med.dates[dateStr]]

  // If medication is fully discontinued after a certain date => no times after that
  if (med.discontinuedDate) {
    const discDay = normalizeToMidnight(med.discontinuedDate)
    const thisDay = normalizeToMidnight(dateObj)
    if (thisDay.getTime() > discDay.getTime()) {
      return []
    }
  }

  // For "partial" specific-time discontinuation:
  if (med.discontinuedTimes && med.discontinuedTimes[dateStr]) {
    // Let’s skip those times, *unless* the date is the same as when they were first discontinued
    // But we do not actually know which "first day" might apply, so we rely on the slot's status.
    // By default we set them to 'discontinue' on that day so they appear in red.
    const timesToSkip = med.discontinuedTimes[dateStr]
    // Skip any slot that does *not* have status=discontinue
    // but is in timesToSkip. Because if we actually changed it to 'discontinue',
    // it should remain in the array (so user sees it in red).
    slots = slots.filter(s => {
      if (timesToSkip.includes(s.time)) {
        // If we *did not* manually set s.status='discontinue', we skip it.
        // If s.status === 'discontinue', keep it so it shows red.
        return s.status === 'discontinue'
      }
      return true
    })
  }

  return slots
}

function onBarcodeScanned(barcode: string) {
  if (scannerContext.value) {
    scannerContext.value.timeObj.temporaryStatus = 'taken'
    alert('Scanned barcode: ' + barcode)
  }
  scannerContext.value = null
}

function handleTimeClick(dateObj: Date, timeObj: any, med: Medication) {
  const now = new Date()
  const isToday =
    dateObj.getFullYear() === now.getFullYear() &&
    dateObj.getMonth() === now.getMonth() &&
    dateObj.getDate() === now.getDate()
  if (isToday) {
    scannerContext.value = { med, timeObj, dateObj }
    // showBarcodeScanner.value = true
  } else {
    openActionPopup(dateObj, timeObj, med)
  }
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

// Early/late
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

// Signature grouping
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
  if (!signOffNurseSignature.value) {
    alert("Please enter your nurse signature before signing off.")
    return
  }
  const now = new Date()

  pendingTransactions.value.forEach(item => {
    const { medication, timeObj } = item
    const pendingAction = timeObj.temporaryStatus
    if (!pendingAction) return
    timeObj.locked = true
    timeObj.status = pendingAction
    timeObj.signedOff = { nurse: signOffNurseSignature.value, date: now }

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
  signOffNurseSignature.value = ''
  showSignOffPopup.value = false
}

// PRN sign-off
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
  if (!prnNurseSignature.value) {
    alert("Please enter your nurse signature before signing off.")
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

/** "As needed" => user stamps a new time record now */
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
.add-manually-btn {
  background-color: #0c8687;
  border: none;
  border-radius: 4px;
  color: #fff;
  padding: 0.5rem 1rem;
  cursor: pointer;
}
.add-manually-btn:hover {
  background-color: #0a7273;
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
  overflow-x: auto;
  max-height: 400px; 
  overflow-y: auto;
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
.schedule-table thead th {
  position: sticky;
  top: 0;
  background-color: #f8f9fa;
  z-index: 2;
}

.schedule-table th,
.schedule-table td {
  border: 1px solid #ddd;
  padding: 12px;
  text-align: center;
}
/* Sticky columns */
.schedule-table .sticky-header-1,
.schedule-table .sticky-header-2,
.schedule-table .sticky-header-3,
.schedule-table .sticky-header-4,
.schedule-table .sticky-header-5,
.schedule-table .sticky-header-6 {
  position: sticky;
  top: 0;
  background-color: #f8f9fa;
  z-index: 10;
}
.schedule-table .sticky-column-1,
.schedule-table .sticky-column-2,
.schedule-table .sticky-column-3,
.schedule-table .sticky-column-4,
.schedule-table .sticky-column-5,
.schedule-table .sticky-column-6 {
  position: sticky;
  background-color: #d4edda;
  z-index: 3;
}
.schedule-table .sticky-column-1, .sticky-header-1 {
  left: 0;
  min-width: 250px;
}
.schedule-table .sticky-column-2,.sticky-header-2 {
  left: 262px;
  min-width: 120px;
}
.schedule-table .sticky-column-3, .sticky-header-3 {
  left: 394px;
  min-width: 180px;
}
.schedule-table .sticky-column-4, .sticky-header-4 {
  left: 651px;
  min-width: 100px;
}
.schedule-table .sticky-column-5, .sticky-header-5 {
  left: 765px;
  min-width: 100px;
}
.schedule-table .sticky-column-6, .sticky-header-6 {
  left: 875px;
  min-width: 200px;
}

/* Tabs Available + Unit dropdown */
.tabs-available {
  padding: 8px;
  display: flex;
  gap: 6px;
  align-items: center;
  justify-content: center;
}
.tabs-counter {
  display: flex;
  align-items: center;
}
.tabs-input {
  width: 60px;
  padding: 4px;
  text-align: center;
  border: 1px solid #ddd;
  border-radius: 4px;
}
.unit-dropdown {
  display: inline-block;
}
.unit-select {
  padding: 4px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 0.9rem;
}

/* Select Time and Dosage */
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

/* Active Row */
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
.time-entry.hold {
  background-color: #fff3cd !important;
  color: #000;
}

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

/* Signature styling (was Sign-Off) */
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
