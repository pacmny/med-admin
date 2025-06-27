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
import { ref, watch, onUnmounted, nextTick, defineProps, defineEmits } from 'vue'
import {
  BrowserMultiFormatReader,
  DecodeHintType,
  BarcodeFormat,
  NotFoundException,
  Result
} from '@zxing/library'

const props = defineProps<{
  active: boolean
  scanRegion?: { x: number; y: number; width: number; height: number } | null
  rapidScanMode?: boolean
}>()
const emit = defineEmits<{
  scanned: (code: string) => void
  close: () => void
}>()

const videoEl = ref<HTMLVideoElement | null>(null)
let codeReader: BrowserMultiFormatReader | null = null

async function startCamera() {
  if (!videoEl.value) return

  // Configure ZXing for higher accuracy on small barcodes
  const hints = new Map()
  hints.set(DecodeHintType.TRY_HARDER, true)
  hints.set(DecodeHintType.POSSIBLE_FORMATS, [
    BarcodeFormat.CODE_128,
    BarcodeFormat.EAN_13,
    BarcodeFormat.EAN_8,
    BarcodeFormat.UPC_A,
    BarcodeFormat.UPC_E
  ])
  codeReader = new BrowserMultiFormatReader(hints)

  // Pick the back-facing camera if available
  const devices = await navigator.mediaDevices.enumerateDevices()
  const videoDevices = devices.filter(d => d.kind === 'videoinput')
  if (!videoDevices.length) {
    console.error('No video inputs found')
    return
  }
  const chosen =
    videoDevices.find(d => /back|rear|environment/i.test(d.label)) ||
    videoDevices[0]

  // Request a high-resolution stream with continuous focus/exposure
  const stream = await navigator.mediaDevices.getUserMedia({
    video: {
      deviceId: chosen.deviceId,
      facingMode: 'environment',
      width: { ideal: 1280, max: 1920 },
      height: { ideal: 720, max: 1080 },
      focusMode: 'continuous',
      exposureMode: 'continuous'
    }
  })
  videoEl.value.srcObject = stream

  // If supported, push zoom and continuous modes to the camera
  const [track] = stream.getVideoTracks()
  if (track.getCapabilities) {
    const caps = track.getCapabilities()
    const adv: any[] = []
    if (caps.zoom) adv.push({ zoom: caps.zoom.max })
    if (caps.focusMode) adv.push({ focusMode: 'continuous' })
    if (caps.exposureMode) adv.push({ exposureMode: 'continuous' })
    if (adv.length) await track.applyConstraints({ advanced: adv })
  }

  // Start decoding frames
  codeReader.decodeFromVideoDevice(
    chosen.deviceId,
    videoEl.value,
    (result: Result | null, err: Error | null) => {
      if (result) {
        const text = result.getText()
        console.log('📦 scanned barcode:', text)
        emit('scanned', text)
        stopCamera()
      }
      if (err && !(err instanceof NotFoundException)) {
        console.warn(err)
      }
    }
  )
}

function stopCamera() {
  if (codeReader) {
    codeReader.reset()
    codeReader = null
  }
  if (videoEl.value?.srcObject) {
    ;(videoEl.value.srcObject as MediaStream).getTracks().forEach(t => t.stop())
    videoEl.value.srcObject = null
  }
}

watch(
  () => props.active,
  async active => {
    if (active) {
      await nextTick()
      startCamera()
    } else {
      stopCamera()
    }
  },
  { immediate: true }
)

onUnmounted(stopCamera)

function handleClose() {
  stopCamera()
  emit('close')
}
</script>


<style scoped>
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

.default-box       { position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); width:12rem; height:8rem; }
.default-border    { position:absolute; inset:0; border:4px solid #ef4444; border-radius:0.5rem; opacity:0.8; }
.default-label     { position:absolute; top:-1.5rem; left:50%; transform:translateX(-50%); color:#ef4444; font-size:0.85rem; font-weight:500; }
.region-border     { position:absolute; border:4px solid; border-radius:0.5rem; transition:all 0.2s ease-in-out; }
.region-label      { position:absolute; top:-1.5rem; left:0; font-size:0.85rem; font-weight:500; }

.close-btn {
  position:absolute;
  top:0.5rem; right:0.5rem;
  width:2rem; height:2rem;
  display:flex; align-items:center; justify-content:center;
  border:none; border-radius:50%;
  background:rgba(255,255,255,0.8);
  font-size:1.2rem; cursor:pointer;
}
.close-btn:hover {
  background:#fff;
}
</style>
