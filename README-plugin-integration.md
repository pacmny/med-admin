# Medication Administration Plugin Integration Guide

## Method 1: Copy the Plugin Files

1. Copy the entire `src/plugins/medication-admin` directory to your target application
2. Install the required dependencies in your target application:

```bash
npm install flatpickr@^4.6.13
```

3. Register the plugin in your main.ts file:

```typescript
import { createApp } from 'vue'
import App from './App.vue'
import MedicationAdminPlugin from './plugins/medication-admin'

const app = createApp(App)
app.use(MedicationAdminPlugin)
app.mount('#app')
```

4. Use the components in your Vue files:

```vue
<script setup lang="ts">
import { MedicationAdministration } from './plugins/medication-admin';
import type { Medication } from './plugins/medication-admin';

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

<template>
  <MedicationAdministration 
    :medications="medications"
    @statusChange="handleStatusChange"
    @medicationTaken="handleMedicationTaken"
    @signatureSubmit="handleSignatureSubmit"
  />
</template>
```

## Method 2: Create an NPM Package

If you plan to reuse this plugin across multiple projects, consider creating an NPM package:

1. Create a new directory for your package:
```bash
mkdir vue-medication-admin
cd vue-medication-admin
npm init -y
```

2. Set up the package structure:
```
vue-medication-admin/
├── dist/
├── src/
│   ├── components/
│   │   ├── ExpandableDetails.vue
│   │   └── MedicationAdministration.vue
│   ├── types.ts
│   ├── styles.css
│   └── index.ts
├── package.json
├── tsconfig.json
└── README.md
```

3. Copy the plugin files from `src/plugins/medication-admin` to the `src` directory

4. Update package.json with build scripts and dependencies:
```json
{
  "name": "vue-medication-admin",
  "version": "1.0.0",
  "description": "Medication administration plugin for Vue 3",
  "main": "dist/index.js",
  "types": "dist/index.d.ts",
  "scripts": {
    "build": "vue-tsc -b && vite build"
  },
  "peerDependencies": {
    "vue": "^3.3.0",
    "flatpickr": "^4.6.13"
  },
  "devDependencies": {
    "@vitejs/plugin-vue": "^4.2.3",
    "typescript": "^5.0.2",
    "vite": "^4.4.5",
    "vue-tsc": "^1.8.5"
  }
}
```

5. Build and publish the package:
```bash
npm run build
npm publish
```

6. Install in your target application:
```bash
npm install vue-medication-admin
```

7. Use in your application:
```typescript
import { createApp } from 'vue'
import App from './App.vue'
import MedicationAdminPlugin from 'vue-medication-admin'

const app = createApp(App)
app.use(MedicationAdminPlugin)
app.mount('#app')
```

## Method 3: Git Submodule

If you're using Git for version control, you can use Git submodules:

1. Add the plugin as a submodule in your target project:
```bash
git submodule add https://your-repo-url.git src/plugins/medication-admin
```

2. Update and initialize the submodule:
```bash
git submodule update --init --recursive
```

3. Follow the same integration steps as Method 1 for using the plugin.