export interface TimeStatus {
  time: string;
  completed: boolean;
}
<<<<<<< HEAD
export interface PastProvarItem {
  [key:number]: string | number;
  /*npinumber:string;
  firstname:string;
  lastname:string;
  email:string; */
}

export interface Medication {
  medname: string;
  times: TimeStatus[];
  med_amount?: number;
  total?:number;
  quantity?:number;
  available?:number;
  med_frequency?: string;
=======

export interface Medication {
  name: string;
  times: TimeStatus[];
  tabsAvailable?: number;
  frequency?: string;
>>>>>>> may20
  dosage?: string;
  administrationTimes?: string;
  route?: string;
  dosageForm?: string;
<<<<<<< HEAD
  diagnose_code?: string;
  med_startdate?: Date;
  med_enddate?: Date;
  ndcnumber?: string;
=======
  diagnosis?: string;
  startDate?: Date;
  endDate?: Date;
  ndc?: string;
>>>>>>> may20
  pharmacy?: string;
  pharmacyNpi?: string;
  pharmacyAddress?: string;
  pharmacyPhone?: string;
  pharmacyDea?: string;
  prescriberInfo?: string;
  prescriberDeaNpi?: string;
<<<<<<< HEAD
  rxnorns?: string;
=======
  rxNumber?: string;
>>>>>>> may20
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