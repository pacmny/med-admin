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
      <slot name="preview"></slot>
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
              <span class="detail-label">NDC Number:</span>
              <span class="detail-value">70700010917</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Dosage:</span>
              <span class="detail-value">Cream</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Route:</span>
              <span class="detail-value">Topical</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Number of Tablets/Quanity:</span>
              <span class="detail-value">60 units</span>
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
              <span class="detail-label">RX Number:</span>
              <span class="detail-value">N/A</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Date the script was filled:</span>
              <span class="detail-value">N/A</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Number of Refills:</span>
              <span class="detail-value">N/A</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Expiration/Refills until:</span>
              <span class="detail-value">N/A</span>
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
              <span class="detail-label">Pharmacy:</span>
              <span class="detail-value">N/A</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">NPI Number:</span>
              <span class="detail-value">N/A</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Address:</span>
              <span class="detail-value">N/A</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Phone Number:</span>
              <span class="detail-value">N/A</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">DEA BL Number:</span>
              <span class="detail-value">N/A</span>
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
              <span class="detail-label">Prescriber:</span>
              <span class="detail-value">N/A</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">DEA Number or NPI Number:</span>
              <span class="detail-value">N/A</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Auth Number:</span>
              <span class="detail-value">N/A</span>
            </div>
          </div>
          <div class="edit-link">
            <a href="#" class="edit">Edit</a>
          </div>
        </div>
      </div>
    </div>

    <button 
      class="more-button"
      @click="toggleExpanded"
      :aria-expanded="expanded"
      :aria-controls="detailsId">
      {{ expanded ? 'LESS' : 'MORE' }}
    </button>
  </div>
</template>

<style scoped>
.expandable-container {
  position: relative;
  width: 100%;
}

.content-preview {
  margin-bottom: 8px;
}

.expanded-details {
  background-color: #fff;
  padding: 20px;
  margin: 8px 0;
  border: 1px solid #ddd;
}

.details-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 20px;
}

.details-box {
  background: #fff;
  border: 1px solid #ddd;
  border-radius: 4px;
  padding: 15px;
  display: flex;
  flex-direction: column;
}

.details-box h3 {
  margin: 0;
  padding: 10px;
  background: #f8f9fa;
  border-bottom: 1px solid #ddd;
  font-size: 16px;
  text-align: center;
}

.details-content {
  flex-grow: 1;
  padding: 15px;
}

.detail-row {
  display: flex;
  flex-direction: column;
  margin-bottom: 10px;
}

.detail-label {
  font-weight: bold;
  color: #666;
  font-size: 14px;
}

.detail-value {
  margin-top: 4px;
  font-size: 14px;
}

.edit-link {
  text-align: center;
  padding: 10px;
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
  padding: 2px 8px;
  font-size: 12px;
  cursor: pointer;
  position: absolute;
  right: 8px;
  top: 50%;
  transform: translateY(-50%);
}

.more-button:hover {
  background-color: #0a6f70;
}
</style>