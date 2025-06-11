<template>
  <div class="scanner-container">
    <video ref="videoEl" autoplay playsinline muted class="scanner-video" />

    <div class="scanner-overlay">
      <template v-if="scanRegion">
        <!-- existing adaptive‐region logic… -->
      </template>
      <template v-else>
        <div class="default-box">
          <div class="default-border"></div>
          <div class="default-label">Position barcode here</div>
        </div>
      </template>
    </div>

    <!-- 🔴 simple text “X” close button -->
    <button class="close-btn" @click="$emit('close')" aria-label="Close scanner">
      ✕
    </button>
  </div>
</template>

<script setup lang="ts">
// no lucide imports needed
import { ref, onMounted, defineEmits, defineProps } from 'vue'

const props = defineProps<{
  scanRegion: { x: number; y: number; width: number; height: number } | null
  rapidScanMode?: boolean
}>()

const emit = defineEmits(['close'])

const videoEl = ref<HTMLVideoElement|null>(null)
onMounted(() => {
  // your existing camera setup…
})
</script>

<style scoped>
.scanner-container {
  position: relative;
  width: 100%; height: 100%;
  background: black;
}
.scanner-video {
  width: 100%; height: 100%;
  object-fit: cover;
}

.scanner-overlay { position: absolute; inset: 0; pointer-events: none; }

.default-box {
  position: absolute;
  top: 50%; left: 50%;
  transform: translate(-50%, -50%);
  width: 12rem; height: 8rem;
}
.default-border {
  position: absolute; inset: 0;
  border: 4px solid #ef4444;  /* red */
  border-radius: 0.5rem;
  opacity: 0.8;
}
.default-label {
  position: absolute;
  top: -1.5rem; left: 50%;
  transform: translateX(-50%);
  color: #ef4444;
  font-size: 0.85rem;
  font-weight: 500;
  white-space: nowrap;
}

/* 🔴 Close button styling */
.close-btn {
  position: absolute;
  top: 0.5rem; right: 0.5rem;
  background: rgba(255,255,255,0.8);
  border: none;
  border-radius: 50%;
  width: 2rem; height: 2rem;
  display: flex; align-items: center; justify-content: center;
  font-size: 1.2rem;
  line-height: 1;
  cursor: pointer;
}
.close-btn:hover {
  background: rgba(255,255,255,1);
}
</style>
