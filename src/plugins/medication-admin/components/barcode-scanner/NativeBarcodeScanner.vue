<!-- src/components/NativeBarcodeScanner.vue -->
<template>
  <div v-if="scanning" class="scanner-wrapper">
    <!-- Native scanner UI appears behind the transparent WebView -->
    <div class="overlay">
      <button class="close-btn" @click="stopScan">✕</button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch, defineProps, defineEmits } from 'vue'
import { BarcodeScanner } from '@capacitor-community/barcode-scanner'

const props = defineProps<{ active: boolean }>()
const emit = defineEmits<{
  scanned: (code: string) => void
  close: () => void
}>()

const scanning = ref(false)

async function startScan() {
  // 1) request camera permission
  const perm = await BarcodeScanner.checkPermission({ force: true })
  if (!perm.granted) {
    console.warn('Camera permission denied')
    emit('close')
    return
  }

  // 2) hide the webview background so native view shows
  await BarcodeScanner.hideBackground()
  scanning.value = true

  // 3) start the scan (this shows the native UI)
  const result = await BarcodeScanner.startScan()

  // 4) restore the webview and emit
  await BarcodeScanner.showBackground()
  scanning.value = false

  if (result.hasContent) {
    emit('scanned', result.content)
  } else {
    // user cancelled or no code
    emit('close')
  }
}

function stopScan() {
  // forcibly stop if still running
  BarcodeScanner.stopScan()
  BarcodeScanner.showBackground()
  scanning.value = false
  emit('close')
}

// watch for the `active` prop
watch(
  () => props.active,
  (active) => {
    if (active) startScan()
    else stopScan()
  },
  { immediate: true }
)
</script>

<style scoped>
.scanner-wrapper {
  position: fixed;
  inset: 0;
  z-index: 9999;
}
.overlay {
  position: absolute;
  inset: 0;
}
.close-btn {
  position: absolute;
  top: 1rem;
  right: 1rem;
  background: rgba(255, 255, 255, 0.8);
  border: none;
  padding: 0.5rem;
  border-radius: 50%;
  font-size: 1.2rem;
  cursor: pointer;
}
.close-btn:hover {
  background: #ffffff;
}
</style>
