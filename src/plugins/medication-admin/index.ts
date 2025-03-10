import type { App } from 'vue';
import MedicationAdministration from './components/MedicationAdministration.vue';
import ExpandableDetails from './components/ExpandableDetails.vue';
import type { Medication, MedicationStatus, MedicationAdminProps } from './types';

export { MedicationAdministration, ExpandableDetails };
export type { Medication, MedicationStatus, MedicationAdminProps };

export default {
  install: (app: App) => {
    app.component('MedicationAdministration', MedicationAdministration);
    app.component('ExpandableDetails', ExpandableDetails);
  }
};