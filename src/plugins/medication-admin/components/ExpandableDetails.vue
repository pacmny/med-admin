<template>
  <div class="expandable-container">
    <div class="content-preview">
      <div class="medication-name">
        {{ medication.name }}
      </div>
      <button 
        class="more-button"
        @click="toggleExpanded"
        :aria-expanded="expanded"
        :aria-controls="detailsId">
        {{ expanded ? 'LESS' : 'MORE' }}
      </button>
    </div>

    <!-- Backdrop and details container -->
    <div 
      v-if="expanded"
      class="details-backdrop"
      @click="closeDetails"
    >
      <div 
        class="expanded-details"
        :class="{ 'fade-enter-active': expanded }"
        @click.stop
      >
        <div class="details-grid">
          <div class="details-box">
            <h3>Medication Information</h3>
            <div class="details-content">
              <div class="detail-row">
                <span class="detail-label">NDC Number:</span>
                <span class="detail-value">{{ medication.ndc || '70700010917' }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Diagnosis:</span>
                <span class="detail-value">{{ medication.diagnosis || 'N/A' }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Dosage:</span>
                <span class="detail-value">{{ medication.dosage || 'N/A' }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Frequency:</span>
                <span class="detail-value">{{ medication.frequency || 'N/A' }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Route:</span>
                <span class="detail-value">{{ medication.route || 'N/A' }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">PRN:</span>
                <span class="detail-value">{{ medication.prn ? 'Yes' : 'No' }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Number of Tablets/Quantity:</span>
                <span class="detail-value">{{ medication.tabsAvailable || 0 }} units</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">DEA Number:</span>
                <span class="detail-value">{{ medication.pharmacyDea || 'N/A' }}</span>
              </div>
            </div>
            <div class="edit-link">
              <a href="#" class="edit" @click.prevent="handleEdit('medication')">Edit</a>
            </div>
          </div>

          <div class="details-box">
            <h3>Prescription Information</h3>
            <div class="details-content">
              <div class="detail-row">
                <span class="detail-label">RX Number:</span>
                <span class="detail-value">{{ medication.rxNumber || 'N/A' }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Date the script was filled:</span>
                <span class="detail-value">{{ medication.scriptFillDate || 'N/A' }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Number of Refills:</span>
                <span class="detail-value">{{ medication.refills || '0' }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Start Date:</span>
                <span class="detail-value">{{ formatDate(medication.startDate) }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">End Date:</span>
                <span class="detail-value">{{ formatDate(medication.endDate) }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Refill Reminder Date:</span>
                <span class="detail-value">{{ formatDate(medication.refillReminderDate) }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Expiration/Refills until:</span>
                <span class="detail-value">{{ medication.expirationDate || formatDate(medication.endDate) || 'N/A' }}</span>
              </div>
            </div>
            <div class="edit-link">
              <a href="#" class="edit" @click.prevent="handleEdit('prescription')">Edit</a>
            </div>
          </div>

          <div class="details-box">
            <h3>Pharmacy Information</h3>
            <div class="details-content">
              <div class="detail-row">
                <span class="detail-label">Pharmacy:</span>
                <span class="detail-value">{{ medication.pharmacy || 'N/A' }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">NPI Number:</span>
                <span class="detail-value">{{ medication.pharmacyNpi || 'N/A' }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Address:</span>
                <span class="detail-value">{{ medication.pharmacyAddress || 'N/A' }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Phone Number:</span>
                <span class="detail-value">{{ medication.pharmacyPhone || 'N/A' }}</span>
              </div>
            </div>
            <div class="edit-link">
              <a href="#" class="edit" @click.prevent="handleEdit('pharmacy')">Edit</a>
            </div>
          </div>

          <div class="details-box">
            <h3>Provider Information</h3>
            <div class="details-content">
              <div class="detail-row">
                <span class="detail-label">Provider Name:</span>
                <span class="detail-value">{{ medication.prescriberInfo || 'N/A' }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">DEA/NPI Number:</span>
                <span class="detail-value">{{ medication.prescriberDeaNpi || 'N/A' }}</span>
              </div>
            </div>
            <div class="edit-link">
              <a href="#" class="edit" @click.prevent="handleEdit('provider')">Edit</a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <EditDetailsForm
      v-if="showEditForm"
      :show="showEditForm"
      :section="editingSection"
      :medication="medication"
      @close="showEditForm = false"
      @save="handleSave"
    />
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import type { Medication } from '../types';
import EditDetailsForm from './EditDetailsForm.vue';

const props = defineProps<{
  medication: Medication;
}>();

const emit = defineEmits<{
  (e: 'update', medication: Medication): void;
}>();

const expanded = ref(false);
const detailsId = ref(`details-${Math.random().toString(36).substr(2, 9)}`);
const showEditForm = ref(false);
const editingSection = ref('');

function toggleExpanded() {
  expanded.value = !expanded.value;
}

function formatDate(date: Date | string | undefined | null): string {
  if (!date) return 'N/A';
  return new Date(date).toLocaleDateString();
}

function handleEdit(section: string) {
  editingSection.value = section;
  showEditForm.value = true;
}

function handleSave(updatedData: Partial<Medication>) {
  const mergedMedication = {
    ...props.medication,
    ...updatedData
  };
  
  emit('update', mergedMedication);
  showEditForm.value = false;
}

function closeDetails(event: MouseEvent) {
  if (event.target === event.currentTarget) {
    expanded.value = false;
  }
}
</script>

<style scoped>
.expandable-container {
  position: relative;
  width: 100%;
}

.content-preview {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 8px;
}

.medication-name {
  flex: 1;
  font-size: 14px;
}

.more-button {
  background-color: #0c8687;
  color: white;
  border: none;
  border-radius: 4px;
  padding: 4px 12px;
  font-size: 12px;
  cursor: pointer;
  transition: background-color 0.2s;
}

.more-button:hover {
  background-color: #0a6f70;
}

.details-backdrop {
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

.expanded-details {
  background-color: #f8f9fa;
  padding: 32px;
  border-radius: 8px;
  width: 90vw;
  max-width: 1600px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.details-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 24px;
}

.details-box {
  background: #fff;
  border: 1px solid #dee2e6;
  border-radius: 8px;
  display: flex;
  flex-direction: column;
  height: 100%;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.details-box h3 {
  margin: 0;
  padding: 16px;
  background: #f8f9fa;
  border-bottom: 1px solid #dee2e6;
  font-size: 16px;
  font-weight: 600;
  text-align: center;
  color: #212529;
  border-radius: 8px 8px 0 0;
}

.details-content {
  flex-grow: 1;
  padding: 16px;
}

.detail-row {
  margin-bottom: 12px;
}

.detail-row:last-child {
  margin-bottom: 0;
}

.detail-label {
  display: block;
  color: #495057;
  font-size: 12px;
  font-weight: 600;
  margin-bottom: 4px;
}

.detail-value {
  font-size: 14px;
  color: #212529;
}

.edit-link {
  text-align: center;
  padding: 12px;
  border-top: 1px solid #dee2e6;
  margin-top: auto;
}

.edit {
  color: #0c8687;
  text-decoration: none;
  font-size: 14px;
  font-weight: 500;
}

.edit:hover {
  text-decoration: underline;
}

/* Accessibility styles */
.more-button:focus {
  outline: 2px solid #0c8687;
  outline-offset: 2px;
}

/* High contrast mode support */
@media (forced-colors: active) {
  .more-button {
    border: 1px solid ButtonText;
  }
}

/* Responsive adjustments */
@media (max-width: 1400px) {
  .details-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 768px) {
  .details-grid {
    grid-template-columns: 1fr;
  }
  
  .expanded-details {
    padding: 16px;
    width: 95vw;
  }
}
</style>