<template>
    <!-- Dimmed overlay -->
    <div class="popup-overlay" @click.self="emitClose">
      <div class="popup-content">
        <h3>Choose Action</h3>
        <p>You clicked time: <strong>{{ timeObj?.time }}</strong></p>
        <div class="button-group">
          <button class="taken-btn" @click="selectAction('taken')">Taken</button>
          <button class="later-btn" @click="selectAction('later')">Take Later</button>
          <button class="refused-btn" @click="selectAction('refused')">Refused</button>
        </div>
      </div>
    </div>
</template>
  <script setup>
  import { defineProps, defineEmits } from 'vue'
  const props = defineProps({
    // The specific time object that was clicked
    timeObj: {
      type: Object,
      required: true
    }
  })
  const emits = defineEmits([
    // Fired when user clicks outside or wants to close
    'close',
    // Fired when user chooses an action
    'action-selected'
  ])
  function emitClose() {
    emits('close')
  }
  function selectAction(action) {
    emits('action-selected', { action })
  }
  </script>
  <style scoped>
  /* Full-screen overlay */
  .popup-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
  }
  /* Inner popup box */
  .popup-content {
    background: #fff;
    padding: 1.5rem 2rem;
    border-radius: 8px;
    max-width: 400px;
    width: 100%;
    text-align: center;
    font-family: Arial, sans-serif;
  }
  .button-group {
    display: flex;
    gap: 1rem;
    margin-top: 1rem;
    justify-content: center;
  }
  button {
    padding: 0.5rem 1rem;
    font-size: 0.95rem;
    border: none;
    border-radius: 4px;
    cursor: pointer;
  }
  .taken-btn {
    background-color: #4CAF50;
    color: white;
  }
  .later-btn {
    background-color: #FFEB3B;
    color: #000;
  }
  .refused-btn {
    background-color: #F44336;
    color: white;
  }
  </style>