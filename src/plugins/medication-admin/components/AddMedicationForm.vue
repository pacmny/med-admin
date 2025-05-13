<template>
  <transition name="fade">
    <div v-if="show" class="modal-overlay">
      <div class="modal-content">
        <!-- Heading -->
        <h2 class="modal-title">
          {{ isEditMode ? 'Edit Medication' : 'Add New Medication' }}
        </h2>

        <!-- Tabs -->
        <div class="tabs">
          <button
            v-for="tab in tabs"
            :key="tab.value"
            class="tab-button"
            :class="{ active: activeTab === tab.value }"
            @click="activeTab = tab.value"
          >
            {{ tab.label }}
          </button>
        </div>

        <!-- TAB 1: Medication Information -->
        <div v-if="activeTab === 'medInfo'" class="tab-panel">
          <h3 class="section-title">Medication Information</h3>

          <div class="form-group">
            <label>Medication Name</label>
            <input
              type="text"
              v-model="formData.medicationName"
              placeholder="Medication Name"
            />
          </div>
          <div class="form-group">
            <label>NDC Number</label>
            <input
              type="text"
              v-model="formData.ndcNumber"
              placeholder="NDC Number"
            />
          </div>
          <div class="form-group">
            <label>RX Norm</label>
            <input
              type="text"
              v-model="formData.rxNorm"
              placeholder="RX Norm"
            />
          </div>
          <div class="form-group">
            <label>Diagnosis</label>
            <input
              type="text"
              v-model="formData.diagnosis"
              placeholder="Diagnosis"
            />
          </div>

          <!-- Route & Fluid Type row -->
          <div class="form-row">
            <div class="form-group">
              <label>Route</label>
              <select v-model="formData.route">
                <option value="Oral/Sublingual">Oral/Sublingual</option>
                <option value="IVI Intravaginal">IVI Intravaginal</option>
                <option value="SQ">SQ (Subcutaneous)</option>
                <option value="IM">IM (Intramuscular)</option>
                <option value="IV">IV (Intravenous)</option>
                <option value="ID">ID (Intradermal)</option>
                <option value="TOP Topical">TOP Topical</option>
              </select>
            </div>
            <!-- Show fluidType only if route === IV -->
            <div class="form-group" v-if="formData.route === 'IV'">
              <label>Fluid Type</label>
              <select v-model="formData.fluidType">
                <option value="">Select fluid type</option>
                <option>0.9% Normal Saline</option>
                <option>D5W (5% Dextrose in Water)</option>
                <option>Lactated Ringers (LR)</option>
                <option>Half Normal Saline (0.45% NaCl)</option>
              </select>
            </div>
          </div>

          <!-- Dosage + Frequency row -->
          <div class="form-row">
            <div class="form-group dosage-group">
              <label>Dosage</label>
              <div class="dosage-row">
                <input
                  type="text"
                  v-model="formData.dosage"
                  placeholder="Dosage"
                  class="dosage-input"
                />
                <select v-model="formData.unitType" class="dosage-select">
                  <option :value="''">Select Dosage Form</option>
                  <option
                    v-for="option in dosageOptions"
                    :key="option"
                    :value="option"
                  >
                    {{ option }}
                  </option>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label>Frequency</label>
              <select v-model="formData.frequency">
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
<option>monday, wednesday, friday, sunday</option>
<option>tuesday, thursday, saturday</option>
                <!-- etc. -->
              </select>
            </div>
          </div>

          <!-- Oral route only -->
          <div v-if="formData.route === 'Oral/Sublingual'" class="form-row">
            <div class="form-group">
              <label>Number of Tablets/Quantity</label>
              <input
                type="number"
                min="0"
                v-model.number="formData.quantity"
                placeholder="0"
              />
            </div>
            <div class="form-group checkbox-group">
              <input
                type="checkbox"
                id="prnCheck"
                v-model="formData.prn"
              />
              <label for="prnCheck">PRN (As Needed)</label>
            </div>
          </div>

          <!-- IV Administration (if route === IV) -->
          <div v-if="formData.route === 'IV'">
            <h4>IV Administration</h4>
            <div class="form-row volume-rate-row">
              <!-- Volume -->
              <div class="form-group volume-group">
                <label>Total Volume</label>
                <div class="volume-row">
                  <input
                    list="volumeOptions"
                    v-model="formData.totalVolume"
                    class="volume-dropdown"
                    placeholder="e.g. 100"
                  />
                  <datalist id="volumeOptions">
                    <option value="10"></option>
                    <option value="100"></option>
                    <option value="250"></option>
                    <option value="500"></option>
                    <option value="1000"></option>
                  </datalist>
                  <select
                    class="volume-dropdown"
                    v-model="formData.totalVolumeUnit"
                  >
                    <option value="ml">ml</option>
                    <option value="liter">liter</option>
                  </select>
                </div>
              </div>
              <!-- Rate -->
              <div class="form-group">
                <label>Rate</label>
                <input
                  type="text"
                  v-model="formData.rate"
                  placeholder="50 (ml/hr)"
                />
              </div>
              <!-- How Long (Computed) -->
              <div class="form-group">
                <label>How Long (hrs)</label>
                <input
                  type="text"
                  :value="formData.howLong"
                  disabled
                  placeholder="Computed"
                />
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label>Start Time</label>
                <input
                  type="time"
                  v-model="formData.startTime"
                  placeholder="HH:MM"
                />
              </div>
              <div class="form-group">
                <label>End Time</label>
                <input
                  type="time"
                  :value="formData.endTime"
                  disabled
                />
              </div>
            </div>

            <div class="form-group">
              <label>VIA</label>
              <select v-model="formData.via">
                <option value="">Select an option</option>
                <option>Peripheral IV - Left Arm</option>
                <option>Peripheral IV - Right Arm</option>
                <option>PICC Line - Left</option>
                <option>PICC Line - Right</option>
                <option>Mid Line - Left</option>
                <option>Mid Line - Right</option>
                <option>Central Line - Left</option>
                <option>Central Line - Right</option>
              </select>
            </div>
          </div>
        </div>

        <!-- TAB 2: Prescription Info -->
        <div v-if="activeTab === 'prescriptionInfo'" class="tab-panel">
          <h3 class="section-title">Prescription Information</h3>
          <!-- same as before... -->
          <div class="form-group">
            <label>RX Number</label>
            <input
              type="text"
              v-model="formData.rxNumber"
              placeholder="RX Number"
            />
          </div>
          <div class="form-group">
            <label>Date the script was filled</label>
            <input
              type="date"
              v-model="formData.filledDate"
              placeholder="mm/dd/yyyy"
            />
          </div>
          <div class="form-group">
            <label>Number of Refills</label>
            <input
              type="number"
              min="0"
              v-model.number="formData.refills"
              placeholder="0"
            />
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Start Date</label>
              <input
                type="date"
                v-model="formData.startDate"
                placeholder="mm/dd/yyyy"
              />
            </div>
            <div class="form-group">
              <label>End Date</label>
              <input
                type="date"
                v-model="formData.endDate"
                placeholder="mm/dd/yyyy"
              />
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Refill Reminder Date</label>
              <input
                type="date"
                v-model="formData.refillReminderDate"
                placeholder="mm/dd/yyyy"
              />
            </div>
            <div class="form-group">
              <label>Expiration/Refills until</label>
              <input
                type="date"
                v-model="formData.expirationDate"
                placeholder="mm/dd/yyyy"
              />
            </div>
          </div>
        </div>

        <!-- TAB 3: Provider Info -->
        <div v-if="activeTab === 'providerInfo'" class="tab-panel">
          <h3 class="section-title">Provider Information</h3>
          <div class="form-group">
            <label>Provider Name</label>
            <input
              type="text"
              v-model="formData.providerName"
              placeholder="Provider Name"
            />
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>DEA Number</label>
              <input
                type="text"
                v-model="formData.providerDea"
                placeholder="DEA Number"
              />
            </div>
            <div class="form-group">
              <label>NPI Number</label>
              <input
                type="text"
                v-model="formData.providerNpi"
                placeholder="NPI Number"
              />
            </div>
          </div>
          <div class="form-group">
            <label>License Number</label>
            <input
              type="text"
              v-model="formData.licenseNumber"
              placeholder="License Number"
            />
          </div>
          <div class="form-group">
            <label>Address</label>
            <input
              type="text"
              v-model="formData.providerAddress"
              placeholder="Address"
            />
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Office Number</label>
              <input
                type="text"
                v-model="formData.providerOffice"
                placeholder="Office Number"
              />
            </div>
            <div class="form-group">
              <label>Cell Phone</label>
              <input
                type="text"
                v-model="formData.providerCell"
                placeholder="Cell Phone"
              />
            </div>
          </div>
          <div class="form-group">
            <label>Email</label>
            <input
              type="email"
              v-model="formData.providerEmail"
              placeholder="Email"
            />
          </div>
        </div>

        <!-- TAB 4: Pharmacy Info -->
        <div v-if="activeTab === 'pharmacyInfo'" class="tab-panel">
          <h3 class="section-title">Pharmacy Information</h3>
          <div class="form-group">
            <label>Pharmacy Name</label>
            <input
              type="text"
              v-model="formData.pharmacyName"
              placeholder="Pharmacy Name"
            />
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>DEA Number</label>
              <input
                type="text"
                v-model="formData.pharmacyDea"
                placeholder="DEA Number"
              />
            </div>
            <div class="form-group">
              <label>NPI Number</label>
              <input
                type="text"
                v-model="formData.pharmacyNpi"
                placeholder="NPI Number"
              />
            </div>
          </div>
          <div class="form-group">
            <label>Address</label>
            <input
              type="text"
              v-model="formData.pharmacyAddress"
              placeholder="Address"
            />
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Office Number</label>
              <input
                type="text"
                v-model="formData.pharmacyOffice"
                placeholder="Office Number"
              />
            </div>
            <div class="form-group">
              <label>Cell Phone</label>
              <input
                type="text"
                v-model="formData.pharmacyCell"
                placeholder="Cell Phone"
              />
            </div>
          </div>
          <div class="form-group">
            <label>Email</label>
            <input
              type="email"
              v-model="formData.pharmacyEmail"
              placeholder="Email"
            />
          </div>
        </div>

        <!-- Save/Cancel Buttons -->
        <div class="form-actions">
          <button class="btn-cancel" @click="$emit('close')">
            Cancel
          </button>
          <button class="btn-save" @click="handleSave">
            Save
          </button>
        </div>
      </div>
    </div>
  </transition>
</template>

<script setup lang="ts">
import { ref, computed, watch, defineProps, defineEmits } from 'vue'

/** The interface for all fields. */
interface MedicationFormData {
  medicationName: string;
  ndcNumber: string;
  rxNorm: string;
  diagnosis: string;
  dosage: string;
  unitType: string;
  frequency: string;
  route: string;
  fluidType: string; // Only used for IV
  prn: boolean;
  quantity: number;

  totalVolume: string;
  totalVolumeUnit: string;
  rate: string;
  howLong: string;
  startTime: string;
  endTime: string;
  via: string;

  sqInjectionSite: string;
  idInjectionSite: string;
  imInjectionSite: string;

  rxNumber: string;
  filledDate: string;
  refills: number;
  startDate: string;
  endDate: string;
  refillReminderDate: string;
  expirationDate: string;

  providerName: string;
  providerDea: string;
  providerNpi: string;
  licenseNumber: string;
  providerAddress: string;
  providerOffice: string;
  providerCell: string;
  providerEmail: string;

  pharmacyName: string;
  pharmacyDea: string;
  pharmacyNpi: string;
  pharmacyAddress: string;
  pharmacyOffice: string;
  pharmacyCell: string;
  pharmacyEmail: string;
}

const props = defineProps<{
  show: boolean;
  existingMedication?: Partial<MedicationFormData> | null;
}>()

const emit = defineEmits<{
  (e: 'close'): void;
  (e: 'save', payload: MedicationFormData & { isEdit: boolean }): void;
}>()

/** The dosage form dropdown list. */
const dosageOptions = [
  "Actuation","Ampule","Application","Applicator","Auto-Injector","Bar","Capful","Caplet","Capsule",
  "Cartridge","Centimeter","Disk","Dropperful","Each","Film","Fluid Ounce","Gallon","Gram","Gum","Implant",
  "Inch","Inhalation","Injection","Insert","Liter","Lollipop","Lozenge","Metric Drop","Microgram",
  "Milliequivalent","Milligram","Milliliter","Nebule","Ounce","Package","Packet","Pad","Patch","Pellet",
  "Pill","Pint","Pre-filled Pen Syringe","Puff","Pump","Ring","Sachet","Scoopful","Sponge","Spray","Stick",
  "Strip","Suppository","Swab","Syringe","Tablet","Troche","Unit","Vial","Wafer"
]

/** The main reactive form data. */
const formData = ref<MedicationFormData>({
  medicationName: '',
  ndcNumber: '',
  rxNorm: '',
  diagnosis: '',
  dosage: '',
  unitType: '',
  frequency: '',
  route: 'Oral/Sublingual',
  fluidType: '',
  prn: false,
  quantity: 0,

  totalVolume: '',
  totalVolumeUnit: 'ml',
  rate: '',
  howLong: '',
  startTime: '',
  endTime: '',
  via: '',

  sqInjectionSite: '',
  idInjectionSite: '',
  imInjectionSite: '',

  rxNumber: '',
  filledDate: '',
  refills: 0,
  startDate: '',
  endDate: '',
  refillReminderDate: '',
  expirationDate: '',

  providerName: '',
  providerDea: '',
  providerNpi: '',
  licenseNumber: '',
  providerAddress: '',
  providerOffice: '',
  providerCell: '',
  providerEmail: '',

  pharmacyName: '',
  pharmacyDea: '',
  pharmacyNpi: '',
  pharmacyAddress: '',
  pharmacyOffice: '',
  pharmacyCell: '',
  pharmacyEmail: ''
})

/** The four tab sections. */
const tabs = [
  { value: 'medInfo',          label: 'Medication Information' },
  { value: 'prescriptionInfo', label: 'Prescription Information' },
  { value: 'providerInfo',     label: 'Provider Information' },
  { value: 'pharmacyInfo',     label: 'Pharmacy Information' }
]
const activeTab = ref('medInfo')

/** Are we editing an existing medication? */
const isEditMode = computed(() => !!props.existingMedication)

/** If there's an existingMedication, merge it in; else reset. */
watch(() => props.existingMedication, (newVal) => {
  if (newVal) {
    formData.value = { ...formData.value, ...newVal }
  } else {
    resetForm()
  }
}, { immediate: true })

/** Reset form data to defaults. */
function resetForm() {
  formData.value = {
    medicationName: '',
    ndcNumber: '',
    rxNorm: '',
    diagnosis: '',
    dosage: '',
    unitType: '',
    frequency: '',
    route: 'Oral/Sublingual',
    fluidType: '',
    prn: false,
    quantity: 0,

    totalVolume: '',
    totalVolumeUnit: 'ml',
    rate: '',
    howLong: '',
    startTime: '',
    endTime: '',
    via: '',

    sqInjectionSite: '',
    idInjectionSite: '',
    imInjectionSite: '',

    rxNumber: '',
    filledDate: '',
    refills: 0,
    startDate: '',
    endDate: '',
    refillReminderDate: '',
    expirationDate: '',

    providerName: '',
    providerDea: '',
    providerNpi: '',
    licenseNumber: '',
    providerAddress: '',
    providerOffice: '',
    providerCell: '',
    providerEmail: '',

    pharmacyName: '',
    pharmacyDea: '',
    pharmacyNpi: '',
    pharmacyAddress: '',
    pharmacyOffice: '',
    pharmacyCell: '',
    pharmacyEmail: ''
  }
}

/** Save => emit the entire form + isEdit. */
function handleSave() {
  emit('save', {
    ...formData.value,
    isEdit: isEditMode.value
  })
}

/** Watchers for howLong & endTime. (Optional, if you want to keep them) */
watch(
  [() => formData.value.totalVolume, () => formData.value.rate, () => formData.value.totalVolumeUnit],
  () => {
    const vol = parseFloat(formData.value.totalVolume) || 0
    let numericRate = parseFloat(formData.value.rate) || 0
    // parse out "50 ml/hr"
    if (!numericRate) {
      const match = formData.value.rate.match(/(\d+(\.\d+)?)/)
      if (match) {
        numericRate = parseFloat(match[1])
      }
    }
    const finalVolumeInMl =
      formData.value.totalVolumeUnit === 'liter' ? vol * 1000 : vol
    let hours = 0
    if (numericRate > 0) {
      hours = finalVolumeInMl / numericRate
    }
    formData.value.howLong = hours > 0 ? hours.toFixed(2) : ''
  }
)

watch(
  [() => formData.value.howLong, () => formData.value.startTime],
  () => {
    if (!formData.value.howLong || !formData.value.startTime) {
      formData.value.endTime = ''
      return
    }
    const [startH, startM] = formData.value.startTime.split(':').map(Number)
    const hoursFloat = parseFloat(formData.value.howLong)
    if (isNaN(hoursFloat) || isNaN(startH)) {
      formData.value.endTime = ''
      return
    }
    const totalMinutes = Math.round(hoursFloat * 60)
    let newH = startH
    let newM = startM + totalMinutes
    newH += Math.floor(newM / 60)
    newM = newM % 60
    const hh = String(newH % 24).padStart(2, '0')
    const mm = String(newM).padStart(2, '0')
    formData.value.endTime = `${hh}:${mm}`
  }
)
</script>

<style scoped>
/* Basic fade for the modal appear/disappear */
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.2s;
}
.fade-enter, .fade-leave-to {
  opacity: 0;
}

/* Modal overlay & container */
.modal-overlay {
  position: fixed; top: 0; left: 0; right: 0; bottom: 0;
  background-color: rgba(0,0,0,0.5);
  display: flex; justify-content: center; align-items: center;
  z-index: 1000;
}
.modal-content {
  background-color: #fff;
  width: 80%; max-width: 800px;
  border-radius: 8px;
  padding: 2rem;
  box-shadow: 0 10px 25px rgba(0,0,0,0.3);
  max-height: 80%;
  overflow: scroll;
}
.modal-title {
  margin-top: 0; text-align: center; margin-bottom: 1rem;
}

/* Tabs */
.tabs {
  display: flex; margin-bottom: 1rem; border-bottom: 1px solid #ccc;
}
.tab-button {
  flex: 1; text-align: center; background: none; border: none;
  padding: 0.75rem; cursor: pointer; font-weight: 500; transition: background-color 0.2s;
}
.tab-button:hover { background-color: #f2f2f2; }
.tab-button.active {
  background-color: #e6f4f4;
  border-bottom: 3px solid #0c8687;
}

/* Tab panel content */
.tab-panel { margin: 1rem 0; }
.section-title {
  margin: 1rem 0; font-size: 1.1rem;
  border-bottom: 2px solid #0c8687;
  color: #333; padding-bottom: 0.5rem;
}

/* Form layout */
.form-group {
  margin-bottom: 1rem; display: flex; flex-direction: column;
}
.form-group label { font-weight: 500; margin-bottom: 0.3rem; }
.form-group input, .form-group select {
  padding: 0.4rem 0.6rem; border: 1px solid #ccc; border-radius: 4px;
}
.checkbox-group {
  display: flex; align-items: center; gap: 0.5rem;
}
.form-row { display: flex; gap: 1rem; }
.form-row .form-group { flex: 1; }

/* Dosage row */
.dosage-group { width: 100%; display: flex; flex-direction: column; }
.dosage-row {
  display: flex; gap: 0.5rem; align-items: center;
}
.dosage-input { flex: 0 0 80px; }
.dosage-select { flex: 1; }

/* Volume & Rate row */
.volume-rate-row { display: flex; gap: 1rem; }
.volume-group { display: flex; flex-direction: column; }
.volume-row {
  display: flex; gap: 0.5rem; align-items: center;
}
.volume-dropdown { width: 70px; }

/* Bottom actions */
.form-actions {
  display: flex; justify-content: flex-end; gap: 1rem; margin-top: 1rem;
}
.btn-cancel, .btn-save {
  padding: 0.6rem 1.2rem; border-radius: 4px; border: none; cursor: pointer; font-weight: 500;
}
.btn-cancel {
  background-color: #6c757d; color: #fff;
}
.btn-cancel:hover {
  background-color: #5a6268;
}
.btn-save {
  background-color: #0c8687; color: #fff;
}
.btn-save:hover {
  background-color: #0a7273;
}
</style>
