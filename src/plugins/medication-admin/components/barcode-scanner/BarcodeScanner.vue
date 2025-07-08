<template>
  <div
    class="scanner"
    @touchstart.passive="onTouchStart"
    @touchmove.prevent="onTouchMove"
    @touchend="onTouchEnd"
  >
    <video
      ref="video"
      autoplay
      playsinline
      muted
      class="live-video"
      :style="{ transform: `scale(${zoom.toFixed(2)})` }"
    ></video>

    <div v-if="scanning" class="zoom-indicator">
      Zoom: {{ zoom.toFixed(2) }}×
    </div>

    <button @click="startScan" :disabled="scanning">
      {{ scanning ? 'Scanning…' : 'Start Scan' }}
    </button>

    <div v-if="lastResult" class="result">
      ✅ Scanned: {{ lastResult }}
    </div>

    <div class="debug-area" v-if="scanning">
      <h4>Debug Frame:</h4>
      <canvas ref="debugCanvas"></canvas>
      <pre>Last Results: {{ debugJson }}</pre>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'

const video       = ref(null)
const debugCanvas = ref(null)
const scanning    = ref(false)
const lastResult  = ref(null)
const debugJson   = ref('')
let scanInterval  = null

// zoom state
const zoom       = ref(1)
let initialZoom  = 1
let pinchStart   = null

// ZXing‑wasm API
let readBarcodes      = null
let videoTrack        = null
let trackCapabilities = null

onMounted(() => {
  if (
    !window.ZXingWASM ||
    typeof window.ZXingWASM.readBarcodesFromImageData !== 'function'
  ) {
    throw new Error(
      'ZXingWASM.readBarcodesFromImageData not found. Ensure <script src="/decoder.js"> is in index.html.'
    )
  }
  readBarcodes = window.ZXingWASM.readBarcodesFromImageData
})

function onTouchStart(e) {
  if (e.touches.length === 2) {
    const [t0, t1] = e.touches
    pinchStart = Math.hypot(
      t1.clientX - t0.clientX,
      t1.clientY - t0.clientY
    )
    initialZoom = zoom.value
  }
}

function onTouchMove(e) {
  if (e.touches.length === 2 && pinchStart) {
    const [t0, t1] = e.touches
    const dist = Math.hypot(
      t1.clientX - t0.clientX,
      t1.clientY - t0.clientY
    )
    zoom.value = Math.min(Math.max(initialZoom * (dist / pinchStart), 1), 5)

    // hardware zoom
    if (videoTrack && trackCapabilities.zoom) {
      const z = Math.min(
        Math.max(zoom.value, trackCapabilities.zoom.min),
        trackCapabilities.zoom.max
      )
      videoTrack.applyConstraints({ advanced: [{ zoom: z }] }).catch(() => {})
    }
    // continuous focus
    if (videoTrack && trackCapabilities.focusMode?.includes('continuous')) {
      videoTrack.applyConstraints({ advanced: [{ focusMode: 'continuous' }] }).catch(() => {})
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
  lastResult.value = null
  debugJson.value  = ''
  scanning.value   = true

  const stream = await navigator.mediaDevices.getUserMedia({
    video: { facingMode: 'environment' }
  })
  video.value.srcObject = stream

  videoTrack = stream.getVideoTracks()[0]
  if (videoTrack?.getCapabilities) {
    trackCapabilities = videoTrack.getCapabilities()
    if (trackCapabilities.zoom) {
      const mid = (trackCapabilities.zoom.max + trackCapabilities.zoom.min) / 2
      videoTrack.applyConstraints({ advanced: [{ zoom: mid }] }).catch(() => {})
    }
    if (trackCapabilities.focusMode?.includes('continuous')) {
      videoTrack.applyConstraints({ advanced: [{ focusMode: 'continuous' }] }).catch(() => {})
    }
  }

  scanInterval = setInterval(async () => {
    const vid = video.value
    if (!vid || vid.readyState !== 4) return

    const w = vid.videoWidth
    const h = vid.videoHeight
    const cropW = w / zoom.value
    const cropH = h / zoom.value
    const offsetX = (w - cropW) / 2
    const offsetY = (h - cropH) / 2

    const canvas = document.createElement('canvas')
    canvas.width  = w
    canvas.height = h
    const ctx = canvas.getContext('2d')
    ctx.drawImage(vid, offsetX, offsetY, cropW, cropH, 0, 0, w, h)

    const dbg = debugCanvas.value
    dbg.width  = w / 4
    dbg.height = h / 4
    dbg.getContext('2d').drawImage(canvas, 0, 0, w / 4, h / 4)

    const imgData = ctx.getImageData(0, 0, w, h)

    let results = []
    try {
      results = await readBarcodes(imgData, {
        tryHarder:   true,
        autoRotate:  true,
        tryInverted: true
      })
    } catch (err) {
      console.error('readBarcodes error:', err)
    }

    debugJson.value = JSON.stringify(results, null, 2)

    if (results.length && results[0].isValid && results[0].text) {
      lastResult.value = results[0].text
      clearInterval(scanInterval)
      stream.getTracks().forEach(t => t.stop())
      scanning.value = false
      alert(`Scanned: ${results[0].text}`)
    }
  }, 200)
}

onBeforeUnmount(() => {
  clearInterval(scanInterval)
  const s = video.value?.srcObject
  if (s) s.getTracks().forEach(t => t.stop())
})
</script>

<style scoped>
.scanner {
  text-align: center;
  padding: 1rem;
}
.live-video {
  width: 100%;
  max-width: 400px;
  border: 1px solid #444;
  transform-origin: center center;
}
.zoom-indicator {
  position: absolute;
  top: 10px;
  right: 10px;
  background: rgba(0,0,0,0.5);
  color: white;
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  font-size: 0.9rem;
}
button {
  margin-top: 1rem;
  padding: 0.5rem 1rem;
  font-size: 1rem;
}
button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}
.result {
  margin-top: 1rem;
  font-weight: bold;
  color: green;
}
.debug-area {
  margin-top: 1rem;
  display: inline-block;
  text-align: left;
}
.debug-area canvas {
  border: 1px solid #999;
  display: block;
  margin-bottom: 0.5rem;
}
.debug-area pre {
  max-height: 150px;
  overflow: auto;
  background: #f5f5f5;
  padding: 0.5rem;
  white-space: pre-wrap;
}
</style>
