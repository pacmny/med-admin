import ndcDatabase from './ndc-database.json'

export function useDrugDatabase() {
  function getNdcFromBarcode(barcode: string): string {
    // You can adjust this logic based on your NDC database structure
    return ndcDatabase[barcode]?.ndc || ''
  }
  return { getNdcFromBarcode }
}