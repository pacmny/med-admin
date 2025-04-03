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

/*
  --- REACTIVE DATA, PROPS, AND EMITS ---
*/

// Core router
const router = useRouter()
const medications = ref<Medication[]>([])

// Sorting
const sortBy = ref<string>('')

// The default pinned date
const currentDate = ref(new Date())

// UI toggles
const showSelectDropdown = ref(false)
const showAddForm = ref(false)
const selectedMedication = ref<Medication | null>(null)
const selectedFrequency = ref('')
const selectedDosage = ref('1')
const selectedTimes = ref<string[]>([])
const dateList = ref<Date[]>([])

const selectedTimeElement = ref<HTMLElement | null>(null)
const selectedTime = ref<string>('')
const selectedAction = ref<string>('')

// For storing statuses keyed by date/time
const medicationStatus = ref<Record<string, any>>({})

// Status filter
const selectedStatus = ref<string | null>(null)

// “Hold/New/Discontinue” popup
const showHoldSelector = ref(false)
const selectedMedicationForHold = ref<Medication | null>(null)
/**
 * This can be 'hold', 'new', 'discontinue', or 'change'
 * so we can reuse the same popup for these statuses.
 */
const selectedStatusOption = ref<'hold' | 'new' | 'discontinue' | 'change'>('hold')

// Time & Dosage modal
const showTimeModal = ref(false)
const selectedMedicationForTime = ref<Medication | null>(null)
const timeInputs = ref<string[]>([])

// “Taken/Later/Refused” popup
const showTimeActionPopup = ref(false)
const selectedDateAndTime = ref<{ dateObj: Date; timeObj: any } | null>(null)

// Early/Late Confirmation Popup
const showTimeConfirmationPopup = ref(false)
const confirmationMessage = ref("")
const pendingDateAndTime = ref<{ dateObj: Date; timeObj: any } | null>(null)
const isEarly = ref(false)
const showEarlyReasonInput = ref(false)
const earlyReason = ref("")

// Error Modal for validations
const showErrorModal = ref(false)
const errorMessage = ref("")

/*
  FREQUENCY OPTIONS
*/
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

/*
  Helper: normalize date => midnight
*/
function normalizeToMidnight(d: Date): Date {
  return new Date(d.getFullYear(), d.getMonth(), d.getDate())
}

/*
  Format date => "Thu, Mar 27, 2025"
*/
function formatDate(date: Date) {
  return date.toLocaleDateString('en-US', {
    weekday: 'short',
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  })
}

/*
  Format time => 12-hour format (e.g., "9:27 AM")
*/
function formatTime12Hour(date: Date): string {
  let hours = date.getHours()
  const minutes = date.getMinutes()
  const ampm = hours >= 12 ? 'PM' : 'AM'
  hours = hours % 12
  hours = hours ? hours : 12
  const minutesStr = minutes < 10 ? '0' + minutes : minutes
  return hours + ":" + minutesStr + " " + ampm
}

/*
  Return scheduled DateTime by combining date + time string
*/
function getScheduledDateTime(dateObj: Date, timeString: string): Date {
  let scheduledTime = timeString
  if (scheduledTime.includes("(")) {
    scheduledTime = scheduledTime.split("(")[0].trim()
  }
  const parts = scheduledTime.split(":")
  const hours = parseInt(parts[0])
  const mins = parseInt(parts[1])
  return new Date(dateObj.getFullYear(), dateObj.getMonth(), dateObj.getDate(), hours, mins)
}

/*
  Status filter options
*/
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

/*
  Route categories
*/
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

/*
  groupMedicationsByDiagnosis
*/
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

/*
  1) GROUPED MEDICATIONS
*/
const groupedMedications = computed(() => {
  let sortedMeds = [...medications.value]

  // Filter by selected status
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
            allTimes.add(obj.time)
          })
        })
      }
    })

    const sortedTimes = Array.from(allTimes).sort((a, b) => {
      const timeA = new Date(`1970/01/01 ${a}`)
      const timeB = new Date(`1970/01/01 ${b}`)
      return timeA.getTime() - timeB.getTime()
    })

    sortedTimes.forEach(time => {
      groups[time] = []
      sortedMeds.forEach(med => {
        if (!med.prn && med.dates) {
          Object.values(med.dates).forEach((arr: any) => {
            arr.forEach((tObj: any) => {
              if (tObj.time === time) {
                groups[time].push(med)
              }
            })
          })
        }
      })
    })
  }
  else if (sortBy.value === 'prn') {
    const prnMeds = sortedMeds.filter(med => med.prn)
    if (prnMeds.length > 0) {
      groups['PRN Medications'] = prnMeds
    }
  }
  else if (sortBy.value === 'route') {
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
  }
  else {
    // Default sorts: medication or diagnosis
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

/*
  2) allColumns merges default date + dateList
*/
const allColumns = computed(() => {
  const combined = new Set<Date>()
  const defaultMidnight = normalizeToMidnight(currentDate.value)
  combined.add(defaultMidnight)
  dateList.value.forEach(d => combined.add(normalizeToMidnight(d)))

  let columnsArray = Array.from(combined)
  columnsArray.sort((a, b) => a.getTime() - b.getTime())

  const idx = columnsArray.findIndex(d => d.getTime() === defaultMidnight.getTime())
  if (idx !== -1) {
    columnsArray.splice(idx, 1)
    columnsArray.unshift(defaultMidnight)
  }
  return columnsArray
})

/*
  3) LOCAL STORAGE LOAD/SAVE
*/
function loadMedications() {
  const savedMedications = localStorage.getItem('medications')
  if (savedMedications) {
    medications.value = JSON.parse(savedMedications)
  }
}
watch(medications, (newVal) => {
  localStorage.setItem('medications', JSON.stringify(newVal))
}, { deep: true })

// defineProps & watch
const props = withDefaults(defineProps<{
  medications?: Medication[];
}>(), {
  medications: () => []
})
watch(() => props.medications, (newMeds) => {
  if (!localStorage.getItem('medications')) {
    medications.value = [...newMeds]
  }
}, { immediate: true })

// defineEmits
const emit = defineEmits<{
  (e: 'statusChange', medication: Medication, status: string): void
  (e: 'medicationTaken', medication: Medication, time: string, action: string): void
  (e: 'signatureSubmit', signature: string, medications: Medication[], time: string): void
  (e: 'tabsChange', medication: Medication, tabs: number): void
}>()

/*
  4) FREQUENCY & watchers
*/
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
    case 'monday, wednesday, friday, sunday':
      return 4
    case 'tuesday, thursday, saturday':
      return 3
    default:
      return 1
  }
}
watch(selectedFrequency, (newFreq, oldFreq) => {
  if (!newFreq || (selectedMedicationForTime.value && selectedMedicationForTime.value.prn)) {
    timeInputs.value = []
    return
  }
  if (oldFreq && oldFreq !== newFreq) {
    const timesCount = getTimesCountFromFrequency(newFreq)
    timeInputs.value = Array(timesCount).fill('')
  }
})

/*
  5) TIME & DOSAGE MODAL
*/

/**
 * Attempt to open the "Select Time and Dosage" popup:
 * 1) If tabsAvailable == 0 => show error popup.
 * 2) Else => load existing times & show the popup.
 */
function toggleSelectDropdown(medication: Medication) {
  if (medication.tabsAvailable <= 0) {
    errorMessage.value = "Please add tabs available"
    showErrorModal.value = true
    return
  }
  selectedMedicationForTime.value = medication
  selectedFrequency.value = medication.frequency || ''
  selectedDosage.value = medication.dosage || '1'
  if (
    medication.administrationTimes &&
    medication.administrationTimes !== 'As needed'
  ) {
    const splitted = medication.administrationTimes.split(',')
    timeInputs.value = splitted.map(t => t.trim())
  } else {
    timeInputs.value = []
  }
  showTimeModal.value = true
}

/**
 * Validate time inputs. If user selected "3 times daily" => we expect 3 times. If any are empty => show error popup
 */
function handleSave() {
  if (!selectedMedicationForTime.value) {
    showTimeModal.value = false
    return
  }

  // If not PRN, ensure all time inputs are filled
  if (!selectedMedicationForTime.value.prn && timeInputs.value.length > 0) {
    // If any are empty => show error
    if (timeInputs.value.some(t => !t)) {
      errorMessage.value = "Please select all required times."
      showErrorModal.value = true
      return
    }
  }

  // Proceed to save frequency/dosage/times
  selectedMedicationForTime.value.frequency = selectedFrequency.value
  selectedMedicationForTime.value.dosage = selectedDosage.value

  if (selectedMedicationForTime.value.prn) {
    selectedMedicationForTime.value.dates = {}
    selectedMedicationForTime.value.administrationTimes = 'As needed'
  } else {
    if (!selectedMedicationForTime.value.dates) {
      selectedMedicationForTime.value.dates = {}
    }
    const newTimeArray = timeInputs.value
      .filter(t => t)
      .map(t => ({ time: t, status: 'pending' }))
    selectedMedicationForTime.value.administrationTimes = timeInputs.value.join(', ')

    allColumns.value.forEach(dateObj => {
      const dateStr = formatDateToYYYYMMDD(dateObj)
      selectedMedicationForTime.value.dates![dateStr] = newTimeArray.map(x => ({ ...x }))
    })
  }
  // Close the modal
  showTimeModal.value = false
  selectedMedicationForTime.value = null
  timeInputs.value = []
}

function handleCancel() {
  showTimeModal.value = false
  selectedMedicationForTime.value = null
  timeInputs.value = []
}

/*
  6) "Taken/Later/Refused" Popup
*/
function openActionPopup(dateObj: Date, timeObj: any) {
  const scheduledTimeStr = timeObj.time.includes("(")
    ? timeObj.time.split("(")[0].trim()
    : timeObj.time
  const [hours, mins] = scheduledTimeStr.split(":").map(Number)
  const scheduledDateTime = new Date(dateObj.getFullYear(), dateObj.getMonth(), dateObj.getDate(), hours, mins)
  const currentTime = new Date()
  const diffMinutes = (currentTime.getTime() - scheduledDateTime.getTime()) / 60000

  if (diffMinutes >= -60 && diffMinutes <= 60) {
    selectedDateAndTime.value = { dateObj, timeObj }
    showTimeActionPopup.value = true
  } else {
    pendingDateAndTime.value = { dateObj, timeObj }
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
    if (action === 'taken') {
      const originalTime = selectedDateAndTime.value.timeObj.time
      const now = new Date()
      const formatted = formatTime12Hour(now)
      selectedDateAndTime.value.timeObj.time = originalTime + " (taken at " + formatted + ")"
    }
    selectedDateAndTime.value.timeObj.status = action
  }
  showTimeActionPopup.value = false
}

/*
  Early/Late Confirmation
*/
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
    selectedDateAndTime.value.timeObj.earlyReason = earlyReason.value.trim()
    const originalTime = selectedDateAndTime.value.timeObj.time
    const now = new Date()
    const formatted = formatTime12Hour(now)
    selectedDateAndTime.value.timeObj.time = originalTime + " (taken at " + formatted + ")"
    selectedDateAndTime.value.timeObj.status = "taken"
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

/*
  7) NEW MED
*/
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

/*
  8) DATE RANGE
*/
function formatDateToYYYYMMDD(d: Date): string {
  return d.getFullYear() + '-' +
    String(d.getMonth() + 1).padStart(2, '0') + '-' +
    String(d.getDate()).padStart(2, '0')
}
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

/*
  9) MEDICATION STATUS
*/
function populateMedicationTable() {
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

/*
  10) STATUS CHANGES (Dropdown)
*/
function handleStatusChange(event: Event, medIndex: number) {
  const select = event.target as HTMLSelectElement
  const status = select.value
  if (status === 'hold' || status === 'new' || status === 'discontinue' || status === 'change') {
    selectedMedicationForHold.value = medications.value[medIndex]
    selectedStatusOption.value = status as 'hold' | 'new' | 'discontinue' | 'change'
    showHoldSelector.value = true
    return
  }
  const row = select.closest('tr')
  if (row) {
    row.classList.remove(
      'active-row', 'discontinued-row', 'hold-row',
      'new-row', 'pending-row', 'change-row',
      'completed-row', 'partial-row'
    )
    row.classList.add(`${status}-row`)
  }
  emit('statusChange', medications.value[medIndex], status)
}

/*
  Gather times for selectedMedicationForHold
*/
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

/**
 * Closes the error modal
 */
function closeErrorModal() {
  showErrorModal.value = false
}
 
/**
 * If user changes "Tabs Available"
 */
function handleTabsChange(medication: Medication, newValue: number) {
  medication.tabsAvailable = newValue
  emit('tabsChange', medication, newValue)
}

/**
 * Row styling
 */
function getRowStatusClass(medication: Medication) {
  return 'active-row'
}

/**
 * onMounted
 */
onMounted(() => {
  loadMedications()
  initializeDateRangePicker()
  populateMedicationTable()
})

/**
 * Return times for a specific date
 */
function getTimesForDate(med: Medication, dateObj: Date) {
  const dateStr = formatDateToYYYYMMDD(dateObj)
  if (!med.dates || !med.dates[dateStr]) {
    return []
  }
  return med.dates[dateStr]
}
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
        <input type="text" id="date-range-picker" placeholder="Select date range" />
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
                <!-- Columns for each date in allColumns -->
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
                <!-- Basic medication info -->
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

                <!-- Status dropdown -->
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

                <!-- Frequency, Dosage -->
                <td>{{ med.frequency || 'Not set' }}</td>
                <td>{{ med.dosage || 'Not set' }}</td>

                <!-- "Select Time and Dosage" button -->
                <td class="select-time-dosage">
                  <button class="select-button" @click="toggleSelectDropdown(med)">
                    Select
                  </button>
                </td>

                <!-- Show each date's times -->
                <td
                  v-for="dateObj in allColumns"
                  :key="dateObj.getTime()"
                >
                  <div class="administration-times">
                    <div v-if="med.prn" class="prn-indicator">As needed</div>
                    <template v-else>
                      <div
                        v-for="timeObj in getTimesForDate(med, dateObj)"
                        :key="timeObj.time"
                        class="time-entry"
                        :class="timeObj.status"
                        @click="openActionPopup(dateObj, timeObj)"
                      >
                        {{ timeObj.time }}
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
          <label>Dosage:</label>
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

    <!-- "Taken / Later / Refused" Popup -->
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

    <!-- Error Modal for validations -->
    <div v-if="showErrorModal" class="modal-overlay">
      <div class="modal-content">
        <h3>{{ errorMessage }}</h3>
        <div class="button-row">
          <button @click="closeErrorModal">OK</button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/*
  Minimal changes from the previous code. We added a second check in handleSave() 
  to ensure all required times are selected, plus an error modal for it.
*/

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

/* Modal Overlays */
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

/* Row / Time Entry Statuses */
.medication-row.active-row {
  background-color: #d4edda;
}
.time-entry.hold {
  background-color: #fff3cd;
  color: #000;
}
.time-entry.new {
  background-color: #c7d0f9;
  color: #000;
}
.time-entry.discontinue {
  background-color: #f8d7da;
  color: #000;
}
.time-entry.taken {
  background-color: #00ff1e;
  color: #fff;
}
.time-entry.later {
  background-color: #ff9907;
  color: #000;
}
.time-entry.refused {
  background-color: #dc3545;
  color: #fff;
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
.prn-indicator {
  font-style: italic;
  color: #666;
}

/* Select & Focus */
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
