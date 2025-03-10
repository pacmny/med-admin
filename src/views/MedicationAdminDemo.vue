<script setup lang="ts">
import { MedicationAdministration } from '../plugins/medication-admin';
import type { Medication } from '../plugins/medication-admin';

// Example of custom medications data
const medications: Medication[] = [
  {
    name: "Tylenol 50mg PO DAILY Give 3 TAB 50 mg every six hours",
    times: []
  },
  {
    name: "Paxil 100 mg PO DAILY Give 1 TAB 100 mg daily",
    times: []
  },
  {
    name: "Furosemide 80 mg PO DAILY Give 2 TAB furosemide 40 mg daily",
    times: []
  },
  {
    name: "Aspirin 80 mg PO DAILY Give 2 TAB furosemide 40 mg daily",
    times: []
  }
];

// Example event handlers
const handleStatusChange = (medication: Medication, status: string) => {
  console.log(`Medication ${medication.name} status changed to ${status}`);
};

const handleMedicationTaken = (medication: Medication, time: string, action: string) => {
  console.log(`Medication ${medication.name} at ${time} action: ${action}`);
};

const handleSignatureSubmit = (signature: string, medications: Medication[], time: string) => {
  console.log(`Signature: ${signature} for medications at ${time}`);
  console.log('Medications:', medications);
};
</script>

<template>
  <div>
    <h1>Medication Administration Record</h1>
    <MedicationAdministration 
      :medications="medications"
      @statusChange="handleStatusChange"
      @medicationTaken="handleMedicationTaken"
      @signatureSubmit="handleSignatureSubmit"
    />
  </div>
</template>