<template>
  <div class="popup-backdrop">
    <div class="popup-content">
      <h2>Choose Action</h2>
      <div class="action-buttons">
        <button class="taken-btn" @click="chooseAction('taken')">
          <span class="icon-check">✔</span> Taken
        </button>
        <button class="later-btn" @click="chooseAction('later')">
          <span class="icon-later">⏳</span> Take Later
        </button>
        <button class="refused-btn" @click="chooseAction('refused')">
          <span class="icon-x">✘</span> Refused
        </button>
      </div>
      <div class="close-row">
        <button @click="emitClose">Close</button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { defineProps, defineEmits } from 'vue'

const props = defineProps<{
  timeObj: any
}>()

const emit = defineEmits(['close', 'action-selected'])

function chooseAction(action: 'taken' | 'later' | 'refused') {
  emit('action-selected', { action })
  emit('close')
}

function emitClose() {
  emit('close')
}
</script>

<style scoped>
.popup-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0,0,0,0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 2000;
}
.popup-content {
  background: #fff;
  border-radius: 8px;
  padding: 1.5rem;
  width: 300px;
  text-align: center;
}
.action-buttons {
  display: flex;
  justify-content: space-between;
  margin-top: 1rem;
}
.taken-btn {
  background-color: #28a745; /* Green */
  color: #fff;
  border: none;
  padding: 0.75rem 1rem;
  border-radius: 4px;
  cursor: pointer;
}
.later-btn {
  background-color: #ffe600; /* Yellow */
  color: #000;
  border: none;
  padding: 0.75rem 1rem;
  border-radius: 4px;
  cursor: pointer;
}
.refused-btn {
  background-color: #dc3545; /* Red */
  color: #fff;
  border: none;
  padding: 0.75rem 1rem;
  border-radius: 4px;
  cursor: pointer;
}
.icon-check, .icon-later, .icon-x {
  margin-right: 0.3rem;
}
.close-row {
  margin-top: 1rem;
}
</style>