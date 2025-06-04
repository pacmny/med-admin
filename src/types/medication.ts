export const frequencyOptions = [
  '1 time daily','2 times daily',
  '3 times daily','4 times daily',
  'as directed','as needed','as one dose',
  'at bedtime',
  'before every meal','bi-weekly','constant infusion','daily',
  'daily as directed','every day','every month','every other day','every morning','every evening',
  'every hour','every 2 hours',
  'every 3 hours','every 4 hours',
  'every 4 to 6 hours, as needed','every 4 to 6 minutes','every 4 to 8 hours',
  'every 6 hours',
  'every 8 hours','every 12 hours',
  'every 24 hours',
  'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday',
  'every Monday, Wednesday, Friday, Sunday',
  'every Tuesday, Thursday, Saturday','before breakfast, lunch, dinner','after breakfast, lunch, dinner',
  'once a week','one time dose',
  'three times a week','twice daily',
  'two times a week','use as directed per instructions in pack','weekly',
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