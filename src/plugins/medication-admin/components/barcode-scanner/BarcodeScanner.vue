<template>
  <div class="scanner" @touchstart.passive="onTouchStart" @touchmove.prevent="onTouchMove" @touchend="onTouchEnd">
    <div class="video-wrapper">
      <video
        ref="video"
        autoplay
        playsinline
        muted
        class="live-video"
        :style="{ transform: `scale(${zoom.toFixed(2)})` }"
      ></video>
      <button class="close-btn" @click="handleClose">?</button>
    </div>

    <button class="scan-btn" @click="startScan" :disabled="scanning">
      {{ scanning ? 'Scanning?' : 'Start Scan' }}
    </button>

    <div v-if="scanning" class="zoom-indicator">
      Zoom: {{ zoom.toFixed(2) }}�
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, defineEmits } from 'vue'

const emit = defineEmits(['close','scanned'])

const video = ref(null)
const scanning = ref(false)
let scanInterval = null

const zoom = ref(1)
let initialZoom = 1
let pinchStart = null

let readBarcodes = null
let videoTrack = null
let trackCapabilities = null

onMounted(() => {
  if (
    !window.ZXingWASM ||
    typeof window.ZXingWASM.readBarcodesFromImageData !== 'function'
  ) {
    throw new Error(
      'ZXingWASM.readBarcodesFromImageData not found. Ensure IIFE loader in index.html.'
    )
  }
  readBarcodes = window.ZXingWASM.readBarcodesFromImageData
})

function onTouchStart(e) {
  if (e.touches.length === 2) {
    const [t0,t1] = e.touches
    pinchStart = Math.hypot(
      t1.clientX - t0.clientX,
      t1.clientY - t0.clientY
    )
    initialZoom = zoom.value
  }
}

function onTouchMove(e) {
  if (e.touches.length === 2 && pinchStart) {
    const [t0,t1] = e.touches
    const dist = Math.hypot(
      t1.clientX - t0.clientX,
      t1.clientY - t0.clientY
    )
    zoom.value = Math.min(Math.max(initialZoom * (dist / pinchStart),1),5)
    if (videoTrack && trackCapabilities.zoom) {
      const z = Math.min(Math.max(zoom.value,trackCapabilities.zoom.min),trackCapabilities.zoom.max)
      videoTrack.applyConstraints({ advanced:[{ zoom: z }] }).catch(()=>{})
    }
    if (videoTrack && trackCapabilities.focusMode?.includes('continuous')) {
      videoTrack.applyConstraints({ advanced:[{ focusMode:'continuous' }] }).catch(()=>{})
    }
  }
}

function onTouchEnd(e) {
  if (e.touches.length < 2) pinchStart = null
}

async function startScan() {
  if (!readBarcodes) {
    return alert('Decoder not ready')
  }
  scanning.value = true

  const stream = await navigator.mediaDevices.getUserMedia({
    video:{ facingMode:'environment' }
  })
  video.value.srcObject = stream
  videoTrack = stream.getVideoTracks()[0]

  if (videoTrack?.getCapabilities) {
    trackCapabilities = videoTrack.getCapabilities()
    if (trackCapabilities.zoom) {
      const mid = (trackCapabilities.zoom.max + trackCapabilities.zoom.min)/2
      videoTrack.applyConstraints({ advanced:[{ zoom: mid }] }).catch(()=>{})
    }
    if (trackCapabilities.focusMode?.includes('continuous')) {
      videoTrack.applyConstraints({ advanced:[{ focusMode:'continuous' }] }).catch(()=>{})
    }
  }

  scanInterval = setInterval(async () => {
    const vid = video.value
    if (!vid || vid.readyState !== 4) return

    const w = vid.videoWidth
    const h = vid.videoHeight
    const cropW = w / zoom.value
    const cropH = h / zoom.value
    const offsetX = (w - cropW)/2
    const offsetY = (h - cropH)/2

    const canvas = document.createElement('canvas')
    canvas.width  = w
    canvas.height = h
    const ctx = canvas.getContext('2d')
    ctx.drawImage(vid, offsetX, offsetY, cropW, cropH, 0, 0, w, h)

    const imgData = ctx.getImageData(0, 0, w, h)
    let results = []
    try {
      results = await readBarcodes(imgData, {
        tryHarder: true,
        autoRotate: true,
        tryInverted: true
      })
    } catch (err) {
      console.error('readBarcodes error:', err)
    }

    if (results.length && results[0].isValid && results[0].text) {
      clearInterval(scanInterval)
      stream.getTracks().forEach(t => t.stop())
      scanning.value = false
      emit('scanned', results[0].text)
    }
  }, 200)
}

function stopAll() {
  clearInterval(scanInterval)
  const s = video.value?.srcObject
  if (s) s.getTracks().forEach(t => t.stop())
}

function handleClose() {
  stopAll()
  emit('close')
}

onBeforeUnmount(stopAll)
</script>

<style scoped>
.scanner { text-align:center; padding:1rem; background:#000; }
.video-wrapper { position:relative; width:100%; max-width:400px; margin:0 auto; }
.live-video {
  width:100%;
  height:200px;
  object-fit:cover;
  background:#111;
  border-radius:8px 8px 0 0;
}
.close-btn {
  position:absolute;
  top:8px;
  right:8px;
  z-index:10;
  background:rgba(255,255,255,0.6);
  border:none;
  font-size:1.3rem;
  width:2rem;
  height:2rem;
  border-radius:50%;
  cursor:pointer;
}
.scan-btn {
  margin-top:0.75rem;
  padding:0.6rem 1.2rem;
  font-size:1rem;
}
.zoom-indicator {
  position:absolute;
  top:10px;
  right:10px;
  background:rgba(0,0,0,0.5);
  color:#fff;
  padding:0.25rem 0.5rem;
  border-radius:4px;
  font-size:0.9rem;
}
</style>