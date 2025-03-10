export interface Medication {
  name: string;
  times: string[];
  tabsAvailable?: number;
  frequency?: string;
  dosage?: string;
  administrationTimes?: string;
  complete?: boolean;
}

export interface MedicationStatus {
  [date: string]: {
    [time: string]: {
      [key: string]: string;
    };
  };
}

export interface MedicationAdminProps {
  medications?: Medication[];
  onStatusChange?: (medication: Medication, status: string) => void;
  onMedicationTaken?: (medication: Medication, time: string, action: string) => void;
  onSignatureSubmit?: (signature: string, medications: Medication[], time: string) => void;
  onTabsChange?: (medication: Medication, tabs: number) => void;
}