<script setup lang="ts">
import { ref } from 'vue';

const expanded = ref(false);
const detailsId = ref(`details-${Math.random().toString(36).substr(2, 9)}`);

function toggleExpanded() {
  expanded.value = !expanded.value;
}
</script>

<template>
  <div class="expandable-container">
    <div class="content-preview">
      <div class="medication-name">
        <slot name="preview"></slot>
      </div>
      <button 
        class="more-button"
        @click="toggleExpanded"
        :aria-expanded="expanded"
        :aria-controls="detailsId">
        {{ expanded ? 'LESS' : 'MORE' }}
      </button>
    </div>

    <div 
      v-if="expanded"
      class="expanded-details"
      :class="{ 'fade-enter-active': expanded }">
      <div class="details-grid">
        <div class="details-box">
          <h3>Medication Information</h3>
          <div class="details-content">
            <div class="detail-row">
              <div class="detail-label">NDC Number:</div>
              <div class="detail-value">70700010917</div>
            </div>
            <div class="detail-row">
              <div class="detail-label">Dosage:</div>
              <div class="detail-value">Cream</div>
            </div>
            <div class="detail-row">
              <div class="detail-label">Route:</div>
              <div class="detail-value">Topical</div>
            </div>
            <div class="detail-row">
              <div class="detail-label">Number of Tablets/Quanity:</div>
              <div class="detail-value">60 units</div>
            </div>
          </div>
          <div class="edit-link">
            <a href="#" class="edit">Edit</a>
          </div>
        </div>

        <div class="details-box">
          <h3>Prescription Information</h3>
          <div class="details-content">
            <div class="detail-row">
              <div class="detail-label">RX Number:</div>
              <div class="detail-value">N/A</div>
            </div>
            <div class="detail-row">
              <div class="detail-label">Date the script was filled:</div>
              <div class="detail-value">N/A</div>
            </div>
            <div class="detail-row">
              <div class="detail-label">Number of Refills:</div>
              <div class="detail-value">N/A</div>
            </div>
            <div class="detail-row">
              <div class="detail-label">Expiration/Refills until:</div>
              <div class="detail-value">N/A</div>
            </div>
          </div>
          <div class="edit-link">
            <a href="#" class="edit">Edit</a>
          </div>
        </div>

        <div class="details-box">
          <h3>Pharmacy Information</h3>
          <div class="details-content">
            <div class="detail-row">
              <div class="detail-label">Pharmacy:</div>
              <div class="detail-value">N/A</div>
            </div>
            <div class="detail-row">
              <div class="detail-label">NPI Number:</div>
              <div class="detail-value">N/A</div>
            </div>
            <div class="detail-row">
              <div class="detail-label">Address:</div>
              <div class="detail-value">N/A</div>
            </div>
            <div class="detail-row">
              <div class="detail-label">Phone Number:</div>
              <div class="detail-value">N/A</div>
            </div>
            <div class="detail-row">
              <div class="detail-label">DEA BL Number:</div>
              <div class="detail-value">N/A</div>
            </div>
          </div>
          <div class="edit-link">
            <a href="#" class="edit">Edit</a>
          </div>
        </div>

        <div class="details-box">
          <h3>Prescribers Information</h3>
          <div class="details-content">
            <div class="detail-row">
              <div class="detail-label">Prescriber:</div>
              <div class="detail-value">N/A</div>
            </div>
            <div class="detail-row">
              <div class="detail-label">DEA Number or NPI Number:</div>
              <div class="detail-value">N/A</div>
            </div>
            <div class="detail-row">
              <div class="detail-label">Auth Number:</div>
              <div class="detail-value">N/A</div>
            </div>
          </div>
          <div class="edit-link">
            <a href="#" class="edit">Edit</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.expandable-container {
  position: relative;
  width: 100%;
}

.content-preview {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: 8px;
  margin-bottom: 8px;
}

.medication-name {
  margin-bottom: 4px;
}

.expanded-details {
  background-color: #fff;
  padding: 20px;
  margin: 8px 0;
  border: 1px solid #ddd;
  border-radius: 4px;
  width: 100%;
}

.details-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
}

.details-box {
  background: #fff;
  border: 1px solid #ddd;
  border-radius: 4px;
  display: flex;
  flex-direction: column;
}

.details-box h3 {
  margin: 0;
  padding: 12px;
  background: #f8f9fa;
  border-bottom: 1px solid #ddd;
  font-size: 16px;
  text-align: center;
  font-weight: 500;
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
  color: #666;
  font-size: 14px;
  margin-bottom: 4px;
}

.detail-value {
  font-size: 14px;
  color: #333;
}

.edit-link {
  padding: 12px;
  text-align: center;
  border-top: 1px solid #ddd;
}

.edit {
  color: #0c8687;
  text-decoration: none;
  font-size: 14px;
}

.edit:hover {
  text-decoration: underline;
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
  align-self: flex-start;
}

.more-button:hover {
  background-color: #0a6f70;
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
</style>