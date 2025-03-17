export interface TimeStatus {
  time: string;
  completed: boolean;
}

export interface Medication {
  name: string;
  times: TimeStatus[];
  tabsAvailable?: number;
  frequency?: string;
  dosage?: string;
  administrationTimes?: string;
  route?: string;
  dosageForm?: string;
  diagnosis?: string;
  startDate?: Date;
  endDate?: Date;
  ndc?: string;
  pharmacy?: string;
  pharmacyNpi?: string;
  pharmacyAddress?: string;
  pharmacyPhone?: string;
  pharmacyDea?: string;
  prescriberInfo?: string;
  prescriberDeaNpi?: string;
  rxNumber?: string;
  scriptFillDate?: string;
  refills?: number;
  refillReminderDate?: Date;
  expirationDate?: string;
  instructions?: string;
  prn?: boolean;
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