<template>
  <transition name="fade">
    <div v-if="show" class="modal-overlay">
      <div class="modal-content">
        <h2 class="modal-title">Add New Medication</h2>

        <!-- Tabs Row -->
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

          <div class="form-row">
            <div class="form-group">
              <label>Dosage</label>
              <input
                type="text"
                v-model="formData.dosage"
                placeholder="Dosage"
              />
            </div>
            <div class="form-group">
              <label>Frequency</label>
              <select v-model="formData.frequency">
                <option value="">Select frequency</option>
                <option>1 times daily</option>
                <option>2 times daily</option>
                <option>every 4 hours</option>
                <option>every 6 hours</option>
                <!-- Add as many as needed -->
              </select>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label>Route</label>
              <select v-model="formData.route">
                <option>Oral/Sublingual</option>
                <option>IVI Intravaginal</option>
                <option>SQ/IM/IV/ID</option>
                <option>TOP Topical</option>
                <!-- etc. -->
              </select>
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

          <div class="form-group">
            <label>Number of Tablets/Quantity</label>
            <input
              type="number"
              min="0"
              v-model.number="formData.quantity"
              placeholder="0"
            />
          </div>
        </div>

        <!-- TAB 2: Prescription Information -->
        <div v-if="activeTab === 'prescriptionInfo'" class="tab-panel">
          <h3 class="section-title">Prescription Information</h3>
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
            <!-- If you prefer a date picker, replace with your component -->
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

        <!-- TAB 3: Provider Information -->
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

        <!-- TAB 4: Pharmacy Information -->
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

        <!-- Action Buttons (Bottom) -->
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
import { ref, defineProps, defineEmits } from 'vue'

/** Define the structure of all form fields. */
interface MedicationFormData {
  medicationName: string;
  ndcNumber: string;
  rxNorm: string;
  diagnosis: string;
  dosage: string;
  frequency: string;
  route: string;
  prn: boolean;
  quantity: number;

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

/**
 * Props:
 *  show: controls visibility of the modal
 */
const props = defineProps<{
  show: boolean;
}>()

/**
 * Emits:
 *  close  -> for closing/canceling the modal
 *  save   -> sends the entire formData object
 */
const emit = defineEmits<{
  (e: 'close'): void;
  (e: 'save', payload: MedicationFormData): void;
}>()

/** Reactive object storing all form fields. */
const formData = ref<MedicationFormData>({
  medicationName: '',
  ndcNumber: '',
  rxNorm: '',
  diagnosis: '',
  dosage: '',
  frequency: '',
  route: 'Oral/Sublingual',
  prn: false,
  quantity: 0,

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

/** The four tabs: */
const tabs = [
  { value: 'medInfo',         label: 'Medication Information' },
  { value: 'prescriptionInfo',label: 'Prescription Information'},
  { value: 'providerInfo',    label: 'Provider Information'    },
  { value: 'pharmacyInfo',    label: 'Pharmacy Information'    }
]

/** Track which tab is active. */
const activeTab = ref('medInfo')

/** Handler for the Save button. */
function handleSave() {
  // You can do validation or other logic here
  emit('save', formData.value)
}
</script>

<style scoped>
/* Basic fade for the modal appear/disappear */
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.2s;
}
.fade-enter, .fade-leave-to {
  opacity: 0;
}

/* Modal Overlay & Container */
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
  background-color: #fff;
  width: 80%;
  max-width: 800px;
  border-radius: 8px;
  padding: 2rem;
  box-shadow: 0 10px 25px rgba(0,0,0,0.3);
}
.modal-title {
  margin-top: 0;
  text-align: center;
  margin-bottom: 1rem;
}

/* Tabs (buttons) */
.tabs {
  display: flex;
  margin-bottom: 1rem;
  border-bottom: 1px solid #ccc;
}
.tab-button {
  flex: 1;
  text-align: center;
  background: none;
  border: none;
  padding: 0.75rem;
  cursor: pointer;
  font-weight: 500;
  transition: background-color 0.2s;
}
.tab-button:hover {
  background-color: #f2f2f2;
}
.tab-button.active {
  background-color: #e6f4f4;
  border-bottom: 3px solid #0c8687;
}

/* Tab panel content */
.tab-panel {
  margin: 1rem 0;
}
.section-title {
  margin: 1rem 0 1rem;
  font-size: 1.1rem;
  border-bottom: 2px solid #0c8687;
  color: #333;
  padding-bottom: 0.5rem;
}

/* Form layout */
.form-group {
  margin-bottom: 1rem;
  display: flex;
  flex-direction: column;
}
.form-group label {
  font-weight: 500;
  margin-bottom: 0.3rem;
}
.form-group input,
.form-group select {
  padding: 0.4rem 0.6rem;
  border: 1px solid #ccc;
  border-radius: 4px;
}
.checkbox-group {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}
/* Horizontal row of fields */
.form-row {
  display: flex;
  gap: 1rem;
}
.form-row .form-group {
  flex: 1;
}

/* Bottom action buttons */
.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 1rem;
}
.btn-cancel,
.btn-save {
  padding: 0.6rem 1.2rem;
  border-radius: 4px;
  border: none;
  cursor: pointer;
  font-weight: 500;
}
.btn-cancel {
  background-color: #6c757d;
  color: #fff;
}
.btn-cancel:hover {
  background-color: #5a6268;
}
.btn-save {
  background-color: #0c8687;
  color: #fff;
}
.btn-save:hover {
  background-color: #0a7273;
}
</style>
