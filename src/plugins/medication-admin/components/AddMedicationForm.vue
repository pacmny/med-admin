<script setup lang="ts">
import { ref, onMounted, watch } from 'vue';
import 'flatpickr/dist/flatpickr.css';
import flatpickr from 'flatpickr';
import type { Medication } from '../types';

const props = defineProps<{
  show: boolean;
  medication?: Medication;
}>();

const emit = defineEmits<{
  (e: 'close'): void;
  (e: 'save', medication: Partial<Medication>): void;
}>();

const form = ref({
  // Medication Information
  medicationName: '',
  ndcNumber: '',
  rxNorm: '',
  dosage: '',
  frequency: '',
  dosageForm: 'Tablet',
  route: 'Oral/Sublingual',
  prn: false,
  tabsAvailable: 0,
  diagnosis: '',

  // Prescription Information
  rxNumber: '',
  scriptFillDate: '',
  refills: 0,
  startDate: new Date().toISOString().split('T')[0],
  endDate: '',
  refillReminderDate: '',
  expirationDate: '',

  // Provider Information
  providerName: '',
  providerDeaNumber: '',
  providerNpiNumber: '',
  providerLicenseNumber: '',
  providerAddress: '',
  providerOfficeNumber: '',
  providerCellPhone: '',
  providerEmail: '',

  // Pharmacy Information
  pharmacyName: '',
  pharmacyDeaNumber: '',
  pharmacyNpiNumber: '',
  pharmacyAddress: '',
  pharmacyOfficeNumber: '',
  pharmacyCellPhone: '',
  pharmacyEmail: '',

  // Additional fields
  currentMedication: false,
  duration: 30,
  instructions: ''
});

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

const routeOptions = [
  'Administration',
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

const dosageFormOptions = [
  'Applicator',
  'Blister',
  'Caplet',
  'Capsule',
  'Each',
  'Film',
  'Gram',
  'Gum',
  'Implant',
  'Insert',
  'Kit',
  'Lancet',
  'Lozenge',
  'Milliliter',
  'Packet',
  'Pad',
  'Patch',
  'Pen Needle',
  'Ring',
  'Sponge',
  'Stick',
  'Strip',
  'Suppository',
  'Swab',
  'Tablet',
  'Troche',
  'Unspecified',
  'Wafer'
];

// Watch for medication prop changes to populate form
watch(() => props.medication, (newMedication) => {
  if (newMedication) {
    form.value = {
      medicationName: newMedication.name || '',
      ndcNumber: newMedication.ndc || '',
      rxNorm: newMedication.rxNorm || '',
      dosage: newMedication.dosage || '',
      frequency: newMedication.frequency || '',
      dosageForm: newMedication.dosageForm || 'Tablet',
      route: newMedication.route || 'Oral/Sublingual',
      prn: newMedication.prn || false,
      tabsAvailable: newMedication.tabsAvailable || 0,
      diagnosis: newMedication.diagnosis || '',
      rxNumber: newMedication.rxNumber || '',
      scriptFillDate: newMedication.scriptFillDate || '',
      refills: newMedication.refills || 0,
      startDate: newMedication.startDate ? new Date(newMedication.startDate).toISOString().split('T')[0] : new Date().toISOString().split('T')[0],
      endDate: newMedication.endDate ? new Date(newMedication.endDate).toISOString().split('T')[0] : '',
      refillReminderDate: newMedication.refillReminderDate ? new Date(newMedication.refillReminderDate).toISOString().split('T')[0] : '',
      expirationDate: newMedication.expirationDate || '',
      providerName: newMedication.prescriberInfo || '',
      providerDeaNumber: newMedication.prescriberDeaNpi || '',
      providerNpiNumber: newMedication.prescriberNpi || '',
      providerLicenseNumber: newMedication.prescriberLicense || '',
      providerAddress: newMedication.prescriberAddress || '',
      providerOfficeNumber: newMedication.prescriberOffice || '',
      providerCellPhone: newMedication.prescriberCell || '',
      providerEmail: newMedication.prescriberEmail || '',
      pharmacyName: newMedication.pharmacy || '',
      pharmacyDeaNumber: newMedication.pharmacyDea || '',
      pharmacyNpiNumber: newMedication.pharmacyNpi || '',
      pharmacyAddress: newMedication.pharmacyAddress || '',
      pharmacyOfficeNumber: newMedication.pharmacyOffice || '',
      pharmacyCellPhone: newMedication.pharmacyCell || '',
      pharmacyEmail: newMedication.pharmacyEmail || '',
      currentMedication: true,
      duration: 30,
      instructions: newMedication.instructions || ''
    };
  }
}, { immediate: true });

onMounted(() => {
  const datePickers = document.querySelectorAll('.date-picker');
  datePickers.forEach(picker => {
    flatpickr(picker, {
      dateFormat: "Y-m-d",
      allowInput: true
    });
  });
});

const handleSubmit = () => {
  const medication: Partial<Medication> = {
    medicationDetails: form.value.medicationName,
    ndc: form.value.ndcNumber,
    rxNorm: form.value.rxNorm,
    dosage: form.value.dosage,
    frequency: form.value.frequency,
    dosageForm: form.value.dosageForm,
    route: form.value.route,
    prn: form.value.prn,
    tabsAvailable: form.value.tabsAvailable,
    diagnosis: form.value.diagnosis,
    rxNumber: form.value.rxNumber,
    scriptFillDate: form.value.scriptFillDate,
    refills: form.value.refills,
    startDate: form.value.startDate ? new Date(form.value.startDate) : undefined,
    endDate: form.value.endDate ? new Date(form.value.endDate) : undefined,
    refillReminderDate: form.value.refillReminderDate ? new Date(form.value.refillReminderDate) : undefined,
    expirationDate: form.value.expirationDate,
    prescriberInfo: form.value.providerName,
    prescriberDeaNpi: form.value.providerDeaNumber,
    prescriberNpi: form.value.providerNpiNumber,
    prescriberLicense: form.value.providerLicenseNumber,
    prescriberAddress: form.value.providerAddress,
    prescriberOffice: form.value.providerOfficeNumber,
    prescriberCell: form.value.providerCellPhone,
    prescriberEmail: form.value.providerEmail,
    pharmacy: form.value.pharmacyName,
    pharmacyDea: form.value.pharmacyDeaNumber,
    pharmacyNpi: form.value.pharmacyNpiNumber,
    pharmacyAddress: form.value.pharmacyAddress,
    pharmacyOffice: form.value.pharmacyOfficeNumber,
    pharmacyCell: form.value.pharmacyCellPhone,
    pharmacyEmail: form.value.pharmacyEmail,
    instructions: form.value.instructions
  };

  emit('save', medication);
};
</script>

<template>
  <div v-if="show" class="form-overlay" @click.self="$emit('close')">
    <div class="form-container">
      <h2>{{ medication ? 'Edit Medication' : 'Add New Medication' }}</h2>
      
      <form @submit.prevent="handleSubmit">
        <!-- Medication Information -->
        <div class="form-section">
          <h3><strong>Medication Information</strong></h3>
          <div class="form-group">
            <label for="medicationName">Medication Name</label>
            <input type="text" id="medicationName" v-model="form.medicationName" required>
          </div>
          <div class="form-group">
            <label for="ndcNumber">NDC Number</label>
            <input type="text" id="ndcNumber" v-model="form.ndcNumber">
          </div>
          <div class="form-group">
            <label for="rxNorm">RX Norm</label>
            <input type="text" id="rxNorm" v-model="form.rxNorm">
          </div>
          <div class="form-group">
            <label for="diagnosis">Diagnosis</label>
            <input type="text" id="diagnosis" v-model="form.diagnosis">
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="dosage">Dosage</label>
              <input type="text" id="dosage" v-model="form.dosage" required>
            </div>
            <div class="form-group">
              <label for="frequency">Frequency</label>
              <select id="frequency" v-model="form.frequency" required>
                <option value="">Select frequency</option>
                <option v-for="option in frequencyOptions" :key="option" :value="option">
                  {{ option }}
                </option>
              </select>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="route">Route</label>
              <select id="route" v-model="form.route">
                <option v-for="option in routeOptions" :key="option" :value="option">
                  {{ option }}
                </option>
              </select>
            </div>
            <div class="form-group prn-checkbox">
              <label class="checkbox-label">
                <input type="checkbox" v-model="form.prn">
                PRN (As Needed)
              </label>
            </div>
          </div>
          <div class="form-group">
            <label for="tabsAvailable">Number of Tablets/Quantity</label>
            <input type="number" id="tabsAvailable" v-model="form.tabsAvailable" required>
          </div>
        </div>

        <!-- Prescription Information -->
        <div class="form-section">
          <h3><strong>Prescription Information</strong></h3>
          <div class="form-group">
            <label for="rxNumber">RX Number</label>
            <input type="text" id="rxNumber" v-model="form.rxNumber">
          </div>
          <div class="form-group">
            <label for="scriptFillDate">Date the script was filled</label>
            <input type="date" id="scriptFillDate" v-model="form.scriptFillDate" class="date-picker">
          </div>
          <div class="form-group">
            <label for="refills">Number of Refills</label>
            <input type="number" id="refills" v-model="form.refills" min="0">
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="startDate">Start Date</label>
              <input type="date" id="startDate" v-model="form.startDate" class="date-picker" required>
            </div>
            <div class="form-group">
              <label for="endDate">End Date</label>
              <input type="date" id="endDate" v-model="form.endDate" class="date-picker">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="refillReminderDate">Refill Reminder Date</label>
              <input type="date" id="refillReminderDate" v-model="form.refillReminderDate" class="date-picker">
            </div>
            <div class="form-group">
              <label for="expirationDate">Expiration/Refills until</label>
              <input type="date" id="expirationDate" v-model="form.expirationDate" class="date-picker">
            </div>
          </div>
        </div>

        <!-- Provider Information -->
        <div class="form-section">
          <h3><strong>Provider Information</strong></h3>
          <div class="form-group">
            <label for="providerName">Provider Name</label>
            <input type="text" id="providerName" v-model="form.providerName">
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="providerDeaNumber">DEA Number</label>
              <input type="text" id="providerDeaNumber" v-model="form.providerDeaNumber">
            </div>
            <div class="form-group">
              <label for="providerNpiNumber">NPI Number</label>
              <input type="text" id="providerNpiNumber" v-model="form.providerNpiNumber">
            </div>
          </div>
          <div class="form-group">
            <label for="providerLicenseNumber">License Number</label>
            <input type="text" id="providerLicenseNumber" v-model="form.providerLicenseNumber">
          </div>
          <div class="form-group">
            <label for="providerAddress">Address</label>
            <input type="text" id="providerAddress" v-model="form.providerAddress">
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="providerOfficeNumber">Office Number</label>
              <input type="tel" id="providerOfficeNumber" v-model="form.providerOfficeNumber">
            </div>
            <div class="form-group">
              <label for="providerCellPhone">Cell Phone</label>
              <input type="tel" id="providerCellPhone" v-model="form.providerCellPhone">
            </div>
          </div>
          <div class="form-group">
            <label for="providerEmail">Email</label>
            <input type="email" id="providerEmail" v-model="form.providerEmail">
          </div>
        </div>

        <!-- Pharmacy Information -->
        <div class="form-section">
          <h3><strong>Pharmacy Information</strong></h3>
          <div class="form-group">
            <label for="pharmacyName">Pharmacy Name</label>
            <input type="text" id="pharmacyName" v-model="form.pharmacyName">
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="pharmacyDeaNumber">DEA Number</label>
              <input type="text" id="pharmacyDeaNumber" v-model="form.pharmacyDeaNumber">
            </div>
            <div class="form-group">
              <label for="pharmacyNpiNumber">NPI Number</label>
              <input type="text" id="pharmacyNpiNumber" v-model="form.pharmacyNpiNumber">
            </div>
          </div>
          <div class="form-group">
            <label for="pharmacyAddress">Address</label>
            <input type="text" id="pharmacyAddress" v-model="form.pharmacyAddress">
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="pharmacyOfficeNumber">Office Number</label>
              <input type="tel" id="pharmacyOfficeNumber" v-model="form.pharmacyOfficeNumber">
            </div>
            <div class="form-group">
              <label for="pharmacyCellPhone">Cell Phone</label>
              <input type="tel" id="pharmacyCellPhone" v-model="form.pharmacyCellPhone">
            </div>
          </div>
          <div class="form-group">
            <label for="pharmacyEmail">Email</label>
            <input type="email" id="pharmacyEmail" v-model="form.pharmacyEmail">
          </div>
        </div>

        <div class="form-actions">
          <button type="button" class="btn-cancel" @click="$emit('close')">Cancel</button>
          <button type="submit" class="btn-save">Save</button>
        </div>
      </form>
    </div>
  </div>
</template>

<style scoped>
.form-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.form-container {
  background-color: white;
  padding: 2rem;
  border-radius: 8px;
  width: 90%;
  max-width: 1200px;
  max-height: 90vh;
  overflow-y: auto;
}

.form-section {
  margin-bottom: 2rem;
  padding: 1.5rem;
  border: 1px solid #eee;
  border-radius: 4px;
  background-color: #fff;
}

.form-section h3 {
  margin: 0 0 1.5rem 0;
  color: #333;
  font-size: 1.2rem;
  border-bottom: 2px solid #0c8687;
  padding-bottom: 0.5rem;
}

.form-group {
  margin-bottom: 1rem;
}

.form-row {
  display: flex;
  gap: 1rem;
  margin-bottom: 1rem;
}

.form-row .form-group {
  flex: 1;
  margin-bottom: 0;
}

label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
  color: #333;
}

input[type="text"],
input[type="number"],
input[type="tel"],
input[type="email"],
input[type="date"],
select,
textarea {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 1rem;
  transition: border-color 0.2s;
}

input:focus,
select:focus,
textarea:focus {
  border-color: #0c8687;
  outline: none;
  box-shadow: 0 0 0 2px rgba(12, 134, 135, 0.1);
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 2rem;
  padding-top: 1rem;
  border-top: 1px solid #eee;
}

.btn-save,
.btn-cancel {
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 1rem;
  font-weight: 500;
  transition: background-color 0.2s;
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

h2 {
  margin: 0 0 2rem 0;
  color: #333;
  font-size: 1.5rem;
  text-align: center;
}

.prn-checkbox {
  display: flex;
  align-items: center;
  margin-left: 1rem;
}

.checkbox-label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
}

.checkbox-label input[type="checkbox"] {
  width: auto;
  margin: 0;
}

@media (max-width: 768px) {
  .form-row {
    flex-direction: column;
    gap: 0;
  }
  
  .form-container {
    padding: 1rem;
    width: 95%;
  }
}
</style>