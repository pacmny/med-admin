# Medication Administration Plugin for Vue 3

This plugin provides a comprehensive medication administration system for Vue 3 applications. It includes components for managing medication schedules, administration tracking, and signature collection.

## Features

- Expandable medication details
- Date range selection
- Medication status tracking
- Administration recording (Taken, Take Later, Refused)
- Signature collection for completed administrations
- Fully customizable styling

## Installation

```bash
# If you're using npm
npm install vue-medication-admin

# If you're using yarn
yarn add vue-medication-admin
```

## Usage

### Register the plugin globally

```js
// main.ts or main.js
import { createApp } from 'vue'
import App from './App.vue'
import MedicationAdminPlugin from './plugins/medication-admin'

const app = createApp(App)
app.use(MedicationAdminPlugin)
app.mount('#app')
```

### Use components individually

```js
// YourComponent.vue
import { MedicationAdministration, ExpandableDetails } from './plugins/medication-admin'
```

### Basic Example

```vue
<template>
  <MedicationAdministration 
    :medications="medications"
    @statusChange="handleStatusChange"
    @medicationTaken="handleMedicationTaken"
    @signatureSubmit="handleSignatureSubmit"
  />
</template>

<script setup>
import { MedicationAdministration } from './plugins/medication-admin';

const medications = [
  {
    name: "Tylenol 50mg PO DAILY",
    times: ["09:00", "15:30", "21:45"]
  },
  // More medications...
];

const handleStatusChange = (medication, status) => {
  console.log(`Medication ${medication.name} status changed to ${status}`);
};

const handleMedicationTaken = (medication, time, action) => {
  console.log(`Medication ${medication.name} at ${time} action: ${action}`);
};

const handleSignatureSubmit = (signature, medications, time) => {
  console.log(`Signature: ${signature} for medications at ${time}`);
};
</script>
```

## Components

### MedicationAdministration

The main component for medication administration tracking.

#### Props

- `medications`: Array of medication objects with name and times
- `onStatusChange`: Callback for medication status changes
- `onMedicationTaken`: Callback for medication administration actions
- `onSignatureSubmit`: Callback for signature submission

#### Events

- `statusChange`: Emitted when medication status changes
- `medicationTaken`: Emitted when medication is taken, refused, or postponed
- `signatureSubmit`: Emitted when a signature is submitted

### ExpandableDetails

A reusable component for expandable/collapsible content sections.

#### Props

- `initialState`: Boolean to set initial expanded state (default: false)
- `transitionDuration`: Animation duration in milliseconds (default: 300)

#### Events

- `expand`: Emitted when content is expanded
- `collapse`: Emitted when content is collapsed

## Customization

You can customize the appearance by importing and modifying the styles:

```js
// Import the styles in your main.js or a component
import './plugins/medication-admin/styles.css'
```

## License

MIT