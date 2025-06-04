import { BrowserMultiFormatReader } from '@zxing/library'

export async function scanBarcodeFromFile(file: File): Promise<string | null> {
  const reader = new BrowserMultiFormatReader()
  const img = document.createElement('img')
  return new Promise((resolve) => {
    img.onload = async () => {
      try {
        const result = await reader.decodeFromImageElement(img)
        resolve(result.getText())
      } catch {
        resolve(null)
      }
    }
    img.src = URL.createObjectURL(file)
  })
}