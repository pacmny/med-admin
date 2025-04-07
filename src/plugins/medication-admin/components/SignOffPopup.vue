<template>
    <div class="modal-overlay">
      <div class="modal-content">
        <h2>Sign Off for {{ timeString }} on {{ formatDate(dateObj) }}</h2>
        <p>Please review the following medications that were marked <strong>taken or refused</strong>.</p>
        <ul>
          <li v-for="(item, idx) in medItems" :key="idx">
            <!-- Medication name & status -->
            <strong>{{ item.medication.name }}</strong>
            <span> (Status: {{ item.timeObj.status }})</span>
            <span v-if="item.timeObj.dosage !== undefined">
              — Dosage: {{ item.timeObj.dosage }}
            </span>
          </li>
        </ul>
  
        <div class="form-group">
          <label for="signatureInput">Nurse Signature:</label>
          <input
            id="signatureInput"
            type="text"
            v-model="signature"
            placeholder="Enter your name or initials"
          />
        </div>
  
        <div class="button-row">
          <button @click="emitSignOff" :disabled="!signature">Sign Off</button>
          <button @click="emitClose">Cancel</button>
        </div>
      </div>
    </div>
  </template>
  
  <script setup lang="ts">
  import { ref, defineProps, defineEmits } from 'vue'
  
  const props = defineProps<{
    medItems: { medication: any; timeObj: any }[],
    timeString: string,
    dateObj: Date | null
  }>()
  
  const emit = defineEmits<{
    (e: 'close'): void
    (e: 'sign-off', signature: string): void
  }>()
  
  const signature = ref('')
  
  function emitClose() {
    emit('close')
  }
  
  function emitSignOff() {
    emit('sign-off', signature.value.trim())
  }
  
  function formatDate(dateObj: Date | null) {
    if (!dateObj) return ''
    return dateObj.toLocaleDateString('en-US', {
      weekday: 'short',
      month: 'short',
      day: 'numeric',
      year: 'numeric'
    })
  }
  </script>
  
  <style scoped>
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
    z-index: 2000;
  }
  .modal-content {
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    width: 90%;
    max-width: 400px;
    padding: 1.5rem;
  }
  h2 {
    margin-top: 0;
  }
  ul {
    margin: 1rem 0;
    padding-left: 1.2rem;
  }
  .form-group {
    margin-bottom: 1rem;
  }
  #signatureInput {
    width: 100%;
    padding: 0.5rem;
    margin-top: 0.5rem;
    box-sizing: border-box;
  }
  .button-row {
    display: flex;
    justify-content: space-between;
    margin-top: 1rem;
  }
  button {
    padding: 0.5rem 1rem;
    border-radius: 4px;
    border: none;
    cursor: pointer;
  }
  button[disabled] {
    background-color: #ccc;
    cursor: not-allowed;
  }
  </style>
  