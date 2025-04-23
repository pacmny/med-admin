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

      <!-- Sorting Controls + Sign Off Button -->
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
        <div v-if="medsInGroup.length > 0" class="category-section">
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

                <!-- Status Dropdown -->
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
                      :selected="med.status === option.value"
                    >
                      {{ option.label }}
                    </option>
                  </select>
                </td>

                <!-- Tabs Available -->
                <td class="tabs-available">
                  <div class="tabs-counter">
                    <input
                      type="number"
                      v-model="med.tabsAvailable"
                      @change="handleTabsChange(med, $event.target.value)"
                      class="tabs-input"
                    />
                  </div>
                </td>

                <!-- Frequency & Dosage -->
                <td>{{ med.frequency || 'Not set' }}</td>
                <td>{{ med.dosage || 'Not set' }}</td>

                <!-- "Select Time and Dosage" Button -->
                <td class="select-time-dosage">
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

                    <!-- Scheduled Meds -->
                    <template v-else>
                      <template v-for="timeObj in getTimesForDate(med, dateObj)">
                        <!-- Only show this timeObj if its base time matches the grouping category (when sortBy = 'time'). -->
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
      </template>
    </div>

    <!-- Add Medication Form -->
    <AddMedicationForm
      :show="showAddForm"
      @close="showAddForm = false"
      @save="handleNewMedication"
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
import ExpandableDetails from './ExpandableDetails.vue'
import AddMedicationForm from './AddMedicationForm.vue'
import HoldTimeSelector from './HoldTimeSelector.vue'
import type { Medication } from '../types'

/* ----------------------------------------------------------
   REACTIVE STATE
---------------------------------------------------------- */
const router = useRouter()
const medications = ref<Medication[]>([])

// Sorting
const sortBy = ref<string>('')

// Default pinned date
const currentDate = ref(new Date())

// UI Toggles & Selected Data
const showAddForm = ref(false)
const selectedMedicationForTime = ref<Medication | null>(null)
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

// Pending transactions (for sign-off)
const showSignOffPopup = ref(false)
const pendingTransactions = ref<any[]>([])

// PRN Sign-Off
const showPrnSignOffPopup = ref(false)
const prnSignOffMedication = ref<Medication | null>(null)
const prnSignOffTimeObj = ref<any>(null)
const prnNurseSignature = ref('')

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

/* ----------------------------------------------------------
   HELPER FUNCTIONS
---------------------------------------------------------- */

/**
 * Returns just the “base” time, stripping off any parenthetical text
 * e.g. "08:00 (taken at 8:01 AM)" => "08:00".
 */
function extractBaseTime(rawTime: string): string {
  return rawTime.split('(')[0].trim()
}

function normalizeToMidnight(d: Date): Date {
  return new Date(d.getFullYear(), d.getMonth(), d.getDate())
}
function formatDate(date: Date) {
  return date.toLocaleDateString('en-US', {
    weekday: 'short',
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

/* ----------------------------------------------------------
   STATUS OPTIONS
---------------------------------------------------------- */
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

/* ----------------------------------------------------------
   ROUTE CATEGORIES & GROUPING
---------------------------------------------------------- */
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

/* ----------------------------------------------------------
   GROUPED MEDICATIONS
---------------------------------------------------------- */
const groupedMedications = computed(() => {
  let sortedMeds = [...medications.value]
  if (selectedStatus.value) {
    sortedMeds = sortedMeds.filter(med => med.status === selectedStatus.value)
  }
  const groups: Record<string, Medication[]> = {}

  if (sortBy.value === 'time') {
    // Collect all *base* times (strip off parentheticals)
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
    // Sort times
    const sortedTimes = Array.from(allTimes).sort((a, b) => {
      const timeA = new Date('1970/01/01 ' + a)
      const timeB = new Date('1970/01/01 ' + b)
      return timeA.getTime() - timeB.getTime()
    })
    // Group by that base time
    sortedTimes.forEach(time => {
      groups[time] = []
      const uniqueMedSet = new Set()
      sortedMeds.forEach(med => {
        if (!med.prn && med.dates) {
          Object.values(med.dates).forEach((arr: any) => {
            arr.forEach((tObj: any) => {
              if (
                extractBaseTime(tObj.time) === time &&
                !uniqueMedSet.has(med.name)
              ) {
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
    const uncategorizedMeds = sortedMeds.filter(med => !med.route || !routeCategories.includes(med.route))
    if (uncategorizedMeds.length > 0) {
      groups['Uncategorized'] = uncategorizedMeds
    }
  } else {
    // Sort by medication name or diagnosis, etc.
    if (sortBy.value === 'diagnosis') {
      const groupedByDiagnosis = groupMedicationsByDiagnosis(sortedMeds)
      Object.assign(groups, groupedByDiagnosis)
    } else {
      if (sortBy.value === 'medication') {
        sortedMeds.sort((a, b) => a.name.localeCompare(b.name))
      }
      groups['All Medications'] = sortedMeds
    }
  }
  // Remove empty groups
  Object.keys(groups).forEach(key => {
    if (groups[key].length === 0) {
      delete groups[key]
    }
  })
  return groups
})

/* ----------------------------------------------------------
   allColumns => merges default date + dateList
---------------------------------------------------------- */
const allColumns = computed(() => {
  if (dateList.value.length > 0) {
    const columnsArray = dateList.value.map(d => normalizeToMidnight(d))
    columnsArray.sort((a, b) => a.getTime() - b.getTime())
    return columnsArray
  } else {
    return [normalizeToMidnight(currentDate.value)]
  }
})

/* ----------------------------------------------------------
   LOCAL STORAGE
---------------------------------------------------------- */
function loadMedications() {
  const savedMedications = localStorage.getItem('medications')
  if (savedMedications) {
    medications.value = JSON.parse(savedMedications)
  }
}
watch(medications, (newVal) => {
  localStorage.setItem('medications', JSON.stringify(newVal))
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

/* ----------------------------------------------------------
   FREQUENCY WATCHER
---------------------------------------------------------- */
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

/* ----------------------------------------------------------
   TIME & DOSAGE MODAL
---------------------------------------------------------- */
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
  selectedMedicationForTime.value.frequency = selectedFrequency.value
  selectedMedicationForTime.value.dosage = selectedDosage.value

  if (selectedMedicationForTime.value.prn) {
    selectedMedicationForTime.value.dates = {}
    selectedMedicationForTime.value.administrationTimes = 'As needed'
  } else {
    if (!selectedMedicationForTime.value.dates) {
      selectedMedicationForTime.value.dates = {}
    }
    const dosageNum = parseInt(selectedDosage.value, 10) || 1
    const newTimeArray = timeInputs.value.filter(t => t).map(t => ({
      time: t,
      status: 'pending',
      dosage: dosageNum
    }))
    selectedMedicationForTime.value.administrationTimes = timeInputs.value.join(', ')
    // We do not populate only the current day; we fill *all* date columns
    allColumns.value.forEach(dateObj => {
      const dateStr = formatDateToYYYYMMDD(dateObj)
      selectedMedicationForTime.value.dates![dateStr] = newTimeArray.map(x => ({ ...x }))
    })
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

/* ----------------------------------------------------------
   INITIALIZE
---------------------------------------------------------- */
onMounted(() => {
  loadMedications()
  initializeDateRangePicker()
  populateMedicationTable()
})

/* ----------------------------------------------------------
   DATE RANGE
---------------------------------------------------------- */
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

/* ----------------------------------------------------------
   POPULATE & STATUS
---------------------------------------------------------- */
function populateMedicationTable() {
  // For each medication, ensure we have date entries for every date in dateList
  // if not PRN
  medications.value.forEach(med => {
    if (med.prn) return
    if (!med.administrationTimes) return
    if (!med.dates) {
      med.dates = {}
    }

    const splitted = med.administrationTimes.split(',').map(t => t.trim())
    const dosageNum = parseInt(med.dosage || '1', 10)

    // For each date in range, if there's no entry, create it as "pending"
    dateList.value.forEach(d => {
      const dStr = formatDateToYYYYMMDD(d)
      if (!med.dates[dStr]) {
        med.dates[dStr] = splitted.map(time => ({
          time,
          status: 'pending',
          dosage: dosageNum
        }))
      }
    })
  })

  // (Optional) medicationStatus is used for tracking, left as-is
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
            medicationStatus.value[dateStr][timeObj.time][idx] = 'pending'
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

  if (status === 'hold' || status === 'new' || status === 'discontinue' || status === 'change') {
    selectedMedicationForHold.value = medications.value[medIndex]
    selectedStatusOption.value = status as 'hold' | 'new' | 'discontinue' | 'change'
    showHoldSelector.value = true
    return
  }
  emit('statusChange', medications.value[medIndex], status)
}

/* Gather times for selectedMedicationForHold */
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
  const dateStr = formatDateToYYYYMMDD(data.dateRange[0])
  if (data.holdType === 'all') {
    if (medication.dates && medication.dates[dateStr]) {
      medication.dates[dateStr].forEach((timeObj: any) => {
        if (data.statusOption === 'discontinue' || data.statusOption === 'change') {
          timeObj.status = 'discontinue'
        } else if (data.statusOption === 'new') {
          timeObj.status = 'new'
        } else {
          timeObj.status = 'hold'
        }
      })
    }
  } else if (data.holdType === 'specific' && data.times && medication.dates && medication.dates[dateStr]) {
    data.times.forEach(tStr => {
      const timeObj = medication.dates[dateStr].find((obj: any) => obj.time === tStr)
      if (timeObj) {
        if (data.statusOption === 'discontinue' || data.statusOption === 'change') {
          timeObj.status = 'discontinue'
        } else if (data.statusOption === 'new') {
          timeObj.status = 'new'
        } else {
          timeObj.status = 'hold'
        }
      }
    })
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

/* ----------------------------------------------------------
   CLOSE ERROR MODAL
---------------------------------------------------------- */
function closeErrorModal() {
  showErrorModal.value = false
}

/* ----------------------------------------------------------
   TABS CHANGE
---------------------------------------------------------- */
function handleTabsChange(medication: Medication, newValue: number) {
  medication.tabsAvailable = newValue
  emit('tabsChange', medication, newValue)
}

/* ----------------------------------------------------------
   ROW STYLING
---------------------------------------------------------- */
function getRowStatusClass(_medication: Medication) {
  // If you want row-based coloring, define logic here. 
  // We’ll just return 'active-row' for demonstration:
  return 'active-row'
}

/* ----------------------------------------------------------
   TIME SLOTS
---------------------------------------------------------- */
function getTimesForDate(med: Medication, dateObj: Date) {
  const dateStr = formatDateToYYYYMMDD(dateObj)
  if (!med.dates || !med.dates[dateStr]) {
    return []
  }
  return med.dates[dateStr]
}

/* ----------------------------------------------------------
   ACTION POPUP
---------------------------------------------------------- */
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
  const currentTime = new Date()
  const diffMinutes = (currentTime.getTime() - scheduledDateTime.getTime()) / 60000

  // Within 1 hour => directly open the action popup
  if (diffMinutes >= -60 && diffMinutes <= 60) {
    selectedDateAndTime.value = { dateObj, timeObj, medication }
    showTimeActionPopup.value = true
  } else {
    // Early or late => show confirmation
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

/* ----------------------------------------------------------
   HANDLE TIME ACTION SELECTED
---------------------------------------------------------- */
function handleTimeActionSelected({ action }: { action: string }) {
  if (selectedDateAndTime.value) {
    const { dateObj, timeObj, medication } = selectedDateAndTime.value
    timeObj.temporaryStatus = action

    // If "taken" or "refused," add to pending
    if (action === 'taken' || action === 'refused') {
      const existingIndex = pendingTransactions.value.findIndex(
        item => item.timeObj === timeObj && item.medication === medication
      )
      if (existingIndex === -1) {
        pendingTransactions.value.push({ medication, timeObj, dateObj })
      }
    } 
    // If "later," remove from pending if it was there
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

/* ----------------------------------------------------------
   EARLY/LATE CONFIRMATION
---------------------------------------------------------- */
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
      // If PRN, handle differently
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
      // Normal
      selectedDateAndTime.value.timeObj.earlyReason = earlyReason.value.trim()
      selectedDateAndTime.value.timeObj.temporaryStatus = "taken"

      // Add to pending
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

/* ----------------------------------------------------------
   NEW MED
---------------------------------------------------------- */
function handleNewMedication(medication: Partial<Medication>) {
  const newMedication: Medication = {
    name: medication.medicationDetails || '',
    dates: {},
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
  }
  medications.value.push(newMedication)
  showAddForm.value = false
  if (!newMedication.prn) {
    selectedMedicationForTime.value = newMedication
    selectedFrequency.value = newMedication.frequency
    selectedDosage.value = '1'
    showTimeModal.value = true
  }
  populateMedicationTable()
}
function handleMedicationUpdate(updatedMedication: Medication) {
  const i = medications.value.findIndex(m => m.name === updatedMedication.name)
  if (i !== -1) {
    medications.value[i] = updatedMedication
  }
}

/* ----------------------------------------------------------
   SIGN OFF PENDING
---------------------------------------------------------- */
function finalSignOff() {
  // We confirm each item in pendingTransactions
  const now = new Date()
  const nurseSignature = prompt("Please enter your signature/initials:")

  if (!nurseSignature) {
    return
  }
  // Lock each item, update status, subtract tabs if "taken"
  pendingTransactions.value.forEach(item => {
    const { medication, timeObj } = item
    const pendingAction = timeObj.temporaryStatus
    if (!pendingAction) return
    timeObj.locked = true
    timeObj.status = pendingAction
    timeObj.signedOff = {
      nurse: nurseSignature,
      date: now
    }
    if (pendingAction === 'taken') {
      const dose = (typeof timeObj.dosage === 'number')
        ? timeObj.dosage
        : parseInt(medication.dosage || '1', 10)
      medication.tabsAvailable = Math.max(0, medication.tabsAvailable - dose)
      const formatted = formatTime12Hour(now)
      // We preserve the "taken at ..." so you can see it,
      // but for sorting we now strip it out with extractBaseTime.
      timeObj.time = timeObj.time + " (taken at " + formatted + ")"
    }
  })
  // Clear pending
  pendingTransactions.value = []
  showSignOffPopup.value = false
}

/* ----------------------------------------------------------
   PRN SIGN-OFF
---------------------------------------------------------- */
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
  // Subtract tabs from medication
  const dose = (typeof prnSignOffTimeObj.value.dosage === 'number')
    ? prnSignOffTimeObj.value.dosage
    : parseInt(prnSignOffMedication.value.dosage || '1', 10)
  prnSignOffMedication.value.tabsAvailable = Math.max(0, prnSignOffMedication.value.tabsAvailable - dose)
  closePrnSignOffPopup()
}

/* ----------------------------------------------------------
   STAMP PRN TIME => open PRN Sign-Off
---------------------------------------------------------- */
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
  // If frequency = 2 times daily, check 11 hours gap, etc. => example only
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
  // Normal PRN action
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

/* ----------------------------------------------------------
   TOOLTIP SUPPORT
---------------------------------------------------------- */
function getTooltipText(timeObj: any) {
  if (!timeObj.signedOff) {
    return ''
  }
  const nurseStr = timeObj.signedOff.nurse || 'Unknown Nurse'
  const reasonStr = timeObj.reason || '(none)'
  const doseStr = timeObj.dosage || '(not specified)'
  const signoffTime = timeObj.signedOff.date ? new Date(timeObj.signedOff.date).toLocaleString() : ''
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

/* Schedule Table */
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

/* Select Time and Dosage Button */
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

/* Category Section */
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

/* time-entry styles */
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
/* Show discontinued times in red/pink */
.time-entry.discontinue {
  background-color: #f8d7da !important;
}

/* medication-row styling */
.medication-row.active-row {
  background-color: #d4edda;
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
</style>
