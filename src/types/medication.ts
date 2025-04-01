export const frequencyOptions = [
  'daily',
  '2 times daily',
  '3 times daily',
  '4 times daily',
  'every other day',
  'at bedtime',
  'every hour',
  'every 2 hours',
  'every 3 hours',
  'every 4 hours',
  'every 6 hours',
  'every 8 hours',
  'every 12 hours',
  'every 24 hours',
  'before every meal',
  'after every meal',
  '1 hour after every meal',
  'every morning',
  'once a week',
  'as directed'
];
export interface Medication {
  medicationDetails: string;
  status: string;
  counter: number;
  pillsPerAdministration: number;
  frequency: string;
  route: string;
  startDate: Date;
  endDate: Date | null;
  times: any[];
  isSelected: boolean;
  completedCount: number;
  prepour: boolean;
  holdRanges: any[];
  prePouredDates: Set<any>;
  pharmacy?: string;
  pharmacyNpi?: string;
  pharmacyAddress?: string;
  pharmacyPhone?: string;
  pharmacyDea?: string;
  prescriberInfo?: string;
  prescriberDeaNpi?: string;
  newDate: Date;
  newReason: string;
  refills: number;
  refillsRemaining: number;
  administrations: Map<any, any>;
  totalDailyPills: number;
  discontinuationDate: Date | null;
  changeDate: Date | null;
  discontinuationReason: string;
  holdReason: string;
  changeReason: string;
  dosageForm: string;
  prn: boolean;
}