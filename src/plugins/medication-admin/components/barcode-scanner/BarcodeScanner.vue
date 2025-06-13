<!-- src/components/barcode-scanner/BarcodeScanner.vue -->
<template>
  <div v-if="active" class="scanner-container">
    <video
      ref="videoEl"
      class="scanner-video"
      autoplay
      playsinline
      muted
    ></video>

    <div class="scanner-overlay">
      <template v-if="scanRegion">
        <div
          class="region-border"
          :style="{
            left: `${scanRegion.x}px`,
            top: `${scanRegion.y}px`,
            width: `${scanRegion.width}px`,
            height: `${scanRegion.height}px`,
            borderColor: rapidScanMode ? '#F59E0B' : '#10B981'
          }"
        >
          <div
            class="region-label"
            :style="{ color: rapidScanMode ? '#F59E0B' : '#10B981' }"
          >
            {{ rapidScanMode ? 'Rapid scan ready' : 'Barcode detected' }}
          </div>
        </div>
      </template>
      <template v-else>
        <div class="default-box">
          <div class="default-border"></div>
          <div class="default-label">Position barcode here</div>
        </div>
      </template>
    </div>

    <button
      class="close-btn"
      @click="handleClose"
      aria-label="Close scanner"
    >✕</button>
  </div>
</template>

<script setup lang="ts">
import {
  ref,
  watch,
  onUnmounted,
  nextTick,
  defineProps,
  defineEmits
} from 'vue'

import { BrowserMultiFormatReader, Result } from '@zxing/library'

/* ---------- props ---------- */
const props = defineProps<{
  active: boolean
  scanRegion?: { x: number; y: number; width: number; height: number } | null
  rapidScanMode?: boolean
}>()

const codeReader = new BrowserMultiFormatReader()

/* ---------- emits ---------- */
const emit = defineEmits<{
  /** Fired when a barcode library supplies a decoded string */
  scanned: (code: string) => void
  /** Fired when the user presses the ✕ button */
  close: () => void
}>()

/* ---------- refs ---------- */
const videoEl = ref<HTMLVideoElement | null>(null)
let stream: MediaStream | null = null

/* ---------- camera helpers ---------- */
async function startCamera() {
console.log("🚀 startCamera()", props.active, videoEl.value);
  if (!videoEl.value) return                           // safety

  try {
    stream = await navigator.mediaDevices.getUserMedia({
      video: {
        facingMode: { ideal: 'environment' },          // prefer rear cam
        width:  { ideal: 1280 },
        height: { ideal: 720 }
      },
      audio: false
    })

    videoEl.value.srcObject = stream
    await videoEl.value.play()                         // required on Chrome

    // ← begin continuous decode
codeReader.decodeFromVideoElementContinuously(
videoEl.value,
(result: Result | null, err: Error | null) => {
if (result) {
const text = result.getText()
console.log('📦 scanned barcode:', text)     // your console.log
emit('scanned', text)
// stop so we don’t fire repeatedly
stopCamera()
codeReader.reset()
}
}
)

  } catch (err) {
    console.error('Camera start failed:', err)
    stopCamera()
  }
}

function stopCamera() {
  if (stream) {
    stream.getTracks().forEach(t => t.stop())
    stream = null
  }

  codeReader.reset()
  
  if (videoEl.value) videoEl.value.srcObject = null
}

/* ---------- react to prop changes ---------- */
watch(
  () => props.active,
  async active => {
    if (active) {
      await nextTick()      // wait until <video> is rendered
      await startCamera()
    } else {
      stopCamera()
    }
  },
  { immediate: true }
)

/* ---------- lifecycle ---------- */
onUnmounted(stopCamera)

/* ---------- UI events ---------- */
function handleClose() {
  stopCamera()
  emit('close')
}
</script>

<style scoped>
/* ADD instead: */
.scanner-container {
  position: absolute;
  inset: 0;
  background: transparent;
}
.scanner-video {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.scanner-overlay {
  position: absolute;
  inset: 0;
  pointer-events: none;
}

.scanner-video     { width:100%; height:100%; object-fit:cover; }
.scanner-overlay   { position:absolute; inset:0; pointer-events:none; }
.default-box       { position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); width:12rem; height:8rem; }
.default-border    { position:absolute; inset:0; border:4px solid #ef4444; border-radius:0.5rem; opacity:0.8; }
.default-label     { position:absolute; top:-1.5rem; left:50%; transform:translateX(-50%); color:#ef4444; font-size:0.85rem; font-weight:500; }
.region-border     { position:absolute; border:4px solid; border-radius:0.5rem; transition:all 0.2s ease-in-out; }
.region-label      { position:absolute; top:-1.5rem; left:0; font-size:0.85rem; font-weight:500; }
.close-btn         {
  position:absolute; top:0.5rem; right:0.5rem;
  width:2rem; height:2rem;
  display:flex; align-items:center; justify-content:center;
  border:none; border-radius:50%;
  background:rgba(255,255,255,0.8);
  font-size:1.2rem; cursor:pointer;
}
.close-btn:hover   { background:#fff; }
</style>
