<template>
  <div class="w-full max-w-md mx-auto p-4">
    <div class="flex flex-col items-center">
      <video ref="video" autoplay playsinline class="w-full aspect-video rounded-lg border mb-4"></video>
      <button
        class="px-4 py-2 bg-blue-600 text-white rounded mb-2"
        @click="startCameraScan"
        :disabled="scanning"
      >
        {{ scanning ? 'Scanning...' : 'Start Scan' }}
      </button>
      <input type="file" accept="image/*" @change="onFileChange" class="mb-2" />
      <div v-if="barcode" class="mt-2 text-lg font-mono text-blue-700">
        Barcode: {{ barcode }}
      </div>
      <div v-if="ndc" class="mt-2 text-green-700">
        NDC: {{ ndc }}
      </div>
      <div v-if="error" class="mt-2 text-red-600">
        {{ error }}
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useScanner } from '../barcode-scanner/useScanner'
import { useDrugDatabase } from '../barcode-scanner/useDrugDatabase'
import { scanBarcodeFromFile } from './useFileUpload'

const video = ref<HTMLVideoElement | null>(null)
const barcode = ref('')
const ndc = ref('')
const error = ref('')
const scanning = ref(false)

const { startScan, stopScan } = useScanner()
const { getNdcFromBarcode } = useDrugDatabase()

function startCameraScan() {
  barcode.value = ''
  ndc.value = ''
  error.value = ''
  scanning.value = true
  if (!video.value) {
    error.value = 'Camera not ready'
    scanning.value = false
    return
  }
  startScan(video.value, (result: string) => {
    barcode.value = result
    ndc.value = getNdcFromBarcode(result)
    scanning.value = false
  }, (err: string) => {
    error.value = err
    scanning.value = false
  })
}

async function onFileChange(e: Event) {
  barcode.value = ''
  ndc.value = ''
  error.value = ''
  const files = (e.target as HTMLInputElement).files
  if (files && files[0]) {
    const result = await scanBarcodeFromFile(files[0])
    if (result) {
      barcode.value = result
      ndc.value = getNdcFromBarcode(result)
    } else {
      error.value = 'No barcode detected in image.'
    }
  }
}
</script>