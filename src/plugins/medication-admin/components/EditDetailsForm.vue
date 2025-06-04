<script setup lang="ts">
import { ref, onMounted } from 'vue';
import type { Medication } from '../types';

const props = defineProps<{
  show: boolean;
  section: string;
  medication: Medication;
}>();

const emit = defineEmits<{
  (e: 'close'): void;
  (e: 'save', data: Partial<Medication>): void;
}>();

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

const frequencyOptions = [
'1 time daily',  
'2 times daily',  
'2 times daily, as needed (PRN)',  
'3 times a day',  
'3 times a day, as needed for headache (PRN)',  
'3 times daily',  
'3 times daily, as needed (PRN)',  
'4 times a day', 
'4 times daily',  
'4 times daily, as needed (PRN)',
'as directed',  
'as needed',  
'as one dose on the first day then take one tablet daily thereafter',  
'at bedtime',  
'at bedtime, as needed (PRN)',  
'at bedtime as needed for sleep (PRN)',  
'before every meal',  
'bi-weekly',  
'constant infusion',  
'daily',  
'daily, as needed (PRN)',  
'daily as directed',  
'every day',  
'every month',  
'every other day',  
'every morning',  
'every evening',  
'every hour',  
'every hour, as needed (PRN)',  
'every 2 hours',  
'every 2 hours, as needed (PRN)',  
'every 3 hours',  
'every 3 hours, as needed (PRN)',  
'every 4 hours',  
'every 4 hours, as needed (PRN)',  
'every 4 to 6 hours, as needed for pain (PRN)',  
'every 4 to 6 minutes',  
'every 4 to 8 hours',  
'every 6 hours',  
'every 6 hours, as needed for pain (PRN)',  
'every 6 hours, as needed for cough (PRN)',  
'every 8 hours',  
'every 8 hours, as needed (PRN)',  
'every 12 hours',  
'every 12 hours, as needed (PRN)',  
'every 24 hours',  
'every 24 hours, as needed (PRN)',  
'every Monday, Wednesday, Friday, Sunday',  
'every Tuesday, Thursday, Saturday', 
'before breakfast, lunch, dinner',
'after breakfast, lunch, dinner',
'Monday',  
'Tuesday',  
'Wednesday',  
'Thursday',  
'Friday',  
'Saturday',  
'Sunday',  
'once a week',  
'one time dose',  
'three times a week',  
'twice daily',  
'twice daily, as needed for nausea (PRN)',  
'two times a week',  
'use as directed per instructions in pack',  
'weekly',
];

const form = ref({
  // Medication Information
  ndcNumber: props.medication.ndc || '',
  dosage: props.medication.dosage || '',
  frequency: props.medication.frequency || '',
  route: props.medication.route || 'Oral/Sublingual',
  prn: props.medication.prn || false,
  tabsAvailable: props.medication.tabsAvailable || 0,
  deaNumber: props.medication.pharmacyDea || '',
  diagnosis: props.medication.diagnosis || '',

  // Prescription Information
  rxNumber: props.medication.rxNumber || '',
  scriptFillDate: props.medication.scriptFillDate || '',
  refills: props.medication.refills || 0,
  startDate: props.medication.startDate ? new Date(props.medication.startDate).toISOString().split('T')[0] : '',
  endDate: props.medication.endDate ? new Date(props.medication.endDate).toISOString().split('T')[0] : '',
  refillReminderDate: props.medication.refillReminderDate ? new Date(props.medication.refillReminderDate).toISOString().split('T')[0] : '',
  expirationDate: props.medication.expirationDate || '',

  // Pharmacy Information
  pharmacy: props.medication.pharmacy || '',
  pharmacyNpi: props.medication.pharmacyNpi || '',
  pharmacyAddress: props.medication.pharmacyAddress || '',
  pharmacyPhone: props.medication.pharmacyPhone || '',

  // Provider Information
  providerName: props.medication.prescriberInfo || '',
  providerDeaNpi: props.medication.prescriberDeaNpi || ''
});

const handleSubmit = () => {
  const updatedData: Partial<Medication> = {};

  switch (props.section) {
    case 'medication':
      updatedData.ndc = form.value.ndcNumber;
      updatedData.dosage = form.value.dosage;
      updatedData.frequency = form.value.frequency;
      updatedData.route = form.value.route;
      updatedData.prn = form.value.prn;
      updatedData.tabsAvailable = form.value.tabsAvailable;
      updatedData.pharmacyDea = form.value.deaNumber;
      updatedData.diagnosis = form.value.diagnosis;
      break;

    case 'prescription':
      updatedData.rxNumber = form.value.rxNumber;
      updatedData.scriptFillDate = form.value.scriptFillDate;
      updatedData.refills = form.value.refills;
      updatedData.startDate = form.value.startDate ? new Date(form.value.startDate) : undefined;
      updatedData.endDate = form.value.endDate ? new Date(form.value.endDate) : undefined;
      updatedData.refillReminderDate = form.value.refillReminderDate ? new Date(form.value.refillReminderDate) : undefined;
      updatedData.expirationDate = form.value.expirationDate;
      break;

    case 'pharmacy':
      updatedData.pharmacy = form.value.pharmacy;
      updatedData.pharmacyNpi = form.value.pharmacyNpi;
      updatedData.pharmacyAddress = form.value.pharmacyAddress;
      updatedData.pharmacyPhone = form.value.pharmacyPhone;
      break;

    case 'provider':
      updatedData.prescriberInfo = form.value.providerName;
      updatedData.prescriberDeaNpi = form.value.providerDeaNpi;
      break;
  }

  emit('save', updatedData);
};

onMounted(() => {
  const datePickers = document.querySelectorAll('.date-picker');
  datePickers.forEach(picker => {
    flatpickr(picker, {
      dateFormat: "Y-m-d",
      allowInput: true
    });
  });
});
</script>

<template>
  <div v-if="show" class="edit-overlay" @click.self="$emit('close')">
    <div class="edit-container">
      <h3>Edit {{ section.charAt(0).toUpperCase() + section.slice(1) }} Information</h3>
      
      <form @submit.prevent="handleSubmit">
        <!-- Medication Information -->
        <div v-if="section === 'medication'" class="form-section">
          <div class="form-group">
            <label for="ndcNumber">NDC Number</label>
            <input type="text" id="ndcNumber" v-model="form.ndcNumber">
          </div>
          <div class="form-group">
            <label for="diagnosis">Diagnosis</label>
            <input type="text" id="diagnosis" v-model="form.diagnosis">
          </div>
          <div class="form-group">
            <label for="dosage">Dosage</label>
            <input type="text" id="dosage" v-model="form.dosage">
          </div>
          <div class="form-group">
            <label for="frequency">Frequency</label>
            <select id="frequency" v-model="form.frequency" class="form-select">
              <option value="">Select frequency</option>
              <option v-for="option in frequencyOptions" :key="option" :value="option">
                {{ option }}
              </option>
            </select>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="route">Route</label>
              <select id="route" v-model="form.route" class="form-select">
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
            <input type="number" id="tabsAvailable" v-model="form.tabsAvailable">
          </div>
          <div class="form-group">
            <label for="deaNumber">DEA Number</label>
            <input type="text" id="deaNumber" v-model="form.deaNumber">
          </div>
        </div>

        <!-- Prescription Information -->
        <div v-if="section === 'prescription'" class="form-section">
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
            <input type="number" id="refills" v-model="form.refills">
          </div>
          <div class="form-group">
            <label for="startDate">Start Date</label>
            <input type="date" id="startDate" v-model="form.startDate" class="date-picker">
          </div>
          <div class="form-group">
            <label for="endDate">End Date</label>
            <input type="date" id="endDate" v-model="form.endDate" class="date-picker">
          </div>
          <div class="form-group">
            <label for="refillReminderDate">Refill Reminder Date</label>
            <input type="date" id="refillReminderDate" v-model="form.refillReminderDate" class="date-picker">
          </div>
          <div class="form-group">
            <label for="expirationDate">Expiration/Refills until</label>
            <input type="date" id="expirationDate" v-model="form.expirationDate" class="date-picker">
          </div>
        </div>

        <!-- Pharmacy Information -->
        <div v-if="section === 'pharmacy'" class="form-section">
          <div class="form-group">
            <label for="pharmacy">Pharmacy</label>
            <input type="text" id="pharmacy" v-model="form.pharmacy">
          </div>
          <div class="form-group">
            <label for="pharmacyNpi">NPI Number</label>
            <input type="text" id="pharmacyNpi" v-model="form.pharmacyNpi">
          </div>
          <div class="form-group">
            <label for="pharmacyAddress">Address</label>
            <input type="text" id="pharmacyAddress" v-model="form.pharmacyAddress">
          </div>
          <div class="form-group">
            <label for="pharmacyPhone">Phone Number</label>
            <input type="tel" id="pharmacyPhone" v-model="form.pharmacyPhone">
          </div>
        </div>

        <!-- Provider Information -->
        <div v-if="section === 'provider'" class="form-section">
          <div class="form-group">
            <label for="providerName">Provider Name</label>
            <input type="text" id="providerName" v-model="form.providerName">
          </div>
          <div class="form-group">
            <label for="providerDeaNpi">DEA/NPI Number</label>
            <input type="text" id="providerDeaNpi" v-model="form.providerDeaNpi">
          </div>
        </div>

        <div class="form-actions">
          <button type="button" class="btn-cancel" @click="$emit('close')">Cancel</button>
          <button type="submit" class="btn-save">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</template>

<style scoped>
.edit-overlay {
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

.edit-container {
  background-color: white;
  padding: 2rem;
  border-radius: 8px;
  width: 90%;
  max-width: 600px;
  max-height: 90vh;
  overflow-y: auto;
}

.form-section {
  margin-bottom: 1.5rem;
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

input,
.form-select {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 1rem;
}

input:focus,
.form-select:focus {
  border-color: #0c8687;
  outline: none;
  box-shadow: 0 0 0 2px rgba(12, 134, 135, 0.1);
}

.form-select {
  background-color: white;
  cursor: pointer;
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
}

.btn-save {
  background-color: #0c8687;
  color: white;
}

.btn-cancel {
  background-color: #6c757d;
  color: white;
}

h3 {
  margin: 0 0 1.5rem 0;
  color: #333;
  font-size: 1.25rem;
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
</style>