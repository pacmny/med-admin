import { ref } from 'vue'
import { BrowserMultiFormatReader, BarcodeFormat, DecodeHintType } from '@zxing/library'

export function useScanner() {
  const reader = new BrowserMultiFormatReader()
  let controls: any = null

  const hints = new Map()
  hints.set(DecodeHintType.POSSIBLE_FORMATS, [
    BarcodeFormat.UPC_A,
    BarcodeFormat.UPC_E,
    BarcodeFormat.EAN_13,
    BarcodeFormat.EAN_8,
    BarcodeFormat.CODE_128
  ])

  function startScan(
    videoElement: HTMLVideoElement,
    onResult: (result: string) => void,
    onError?: (err: string) => void
  ) {
    try {
      controls = reader.decodeFromVideoDevice(
        null,
        videoElement,
        (result, err) => {
          if (result) {
            onResult(result.getText())
            stopScan()
          }
          if (err && onError) {
            onError(err.message)
          }
        },
        hints
      )
    } catch (e: any) {
      if (onError) onError(e.message || 'Camera error')
    }
  }

  function stopScan() {
    if (controls) controls.stop()
    reader.reset()
  }

  return { startScan, stopScan }
}