<template>
  <transition name="fade">
    <div v-if="show" class="modal-overlay">
      <div class="modal-content">
        <h2 v-if="isAddNewMed==true" class="modal-title">Add New Medication</h2>
        <h2 v-if="isEditMedication==true" class="modal-title">Edit Medication</h2>
        <!-- Tabs Row -->
        <div class="tabs">
          <button
            v-for="tab in tabs"
            :key="tab.value"
            class="tab-button"
            :class="{ active: activeTab === tab.value }"
            @click="handleTabClick(tab.value)"
          >
            {{ tab.label }}
          </button>
        </div>

        <!-- TAB 1: Medication Information -->
        <div v-if="activeTab === 'medInfo'" class="tab-panel">
          <h3 class="section-title">Medication Information</h3>
          <div class="form-group">
            <label>Medication Name</label>
            <input
              type="text"
              v-model="formData.medicationName"
              placeholder="Medication Name"
              @keyup.prevent="fetchDrugs"
              
            />
           <!--- <input v-if="isEditMedication"
              type="text"
              v-model="editFormdata.medname"
              placeholder="Medication Name"
              @keyup.prevent="fetchDrugs"
             
            /> -->
           <!-- <input
              type="text"
              v-model="formData.medicationName"
              placeholder="Medication Name"
              @keyup.prevent="fetchDrugs"
             
            /> -->
            <div v-if="drugs.length > 0" class="drug-list">     
               <div v-for="(drug, index) in drugs" :key="index" class="drug-item" @click="getDrugSynonym(drug[2])">        
                  {{ drug[0] }} - {{ drug[1] }}     
               </div>   
            </div>
           
          </div>
          <div class="form-group">
            <label>NDC Number</label>
            <input
              type="text"
              v-model="formData.ndcnumber"
              placeholder="NDC Number"
            />
          </div>
          <div class="form-group">
            <label>RX Norm</label>
            <input
              type="text"
              v-model="formData.rxnorns"
              placeholder="RX Norm"
            />
          </div>
          <div class="form-group">
            <label>Diagnosis</label>
            <input
              type="text"
              v-model="formData.diagnosis"
              placeholder="Diagnosis"
              @keyup.prevent="fetchDiagnosis"
            />
            <!---<input v-if="isEditMedication"
            type="text"
            v-model="editFormdata.diagnosis"
            placeholder="Diagnosis"
            @keyup.prevent="fetchDiagnosis"
            /> -->
            <div v-if="newDiagloaded" id="npinamesearch" :class="'selectdisplay-'+newDiagloaded">
                <div
                  v-for="(dicode, index) in newDiagcodes"
                  class="additionalnameli"
                  :key="index"
                  @click="selectDiagcode(index)"
                >
                  {{ dicode.code }} - {{ dicode.description}}
                </div>
              </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label>Dosage</label>
              <input v-if=" !isEditMedication==true"
                type="text"
                v-model="formData.dosage"
                placeholder="Dosage"
              />
              <input v-if="isEditMedication==true" 
              type="number" 
              v-model="formData.dosage" 
              placeholder="(0)"
               @change="detectDosageChange(formData.dosage)"/>
            </div>
            <div class="form-group">
              <label>Frequency</label>
              <select v-model="formData.frequency"
              @change="isEditMedication==true? detectFreqChange(formData.frequency): formData.frequency"
              >
                <option value="">Select frequency</option>
                <option>1 times daily</option>
                <option>2 times daily</option>
                <option>every 4 hours</option>
                <option>every 6 hours</option>
                <!-- Add as many as needed -->
              </select>
            </div>
          </div>
          <div class="form-row admin-times">
            <div class="form-group">
              <div v-if="timeInputs.length > 0" class="form-group">
                <label>Administration Times:</label>
                <div
                  v-for="(_, index) in timeInputs"
                  :key="index"
                  class="time-input-row"
                >
                  <input
                    type="time"
                    v-model="timeInputs[index]"
                    class="time-input"
                    required
                  />
                </div>
             </div>
              
            </div>
            <div class="form-group">
              <div v-if="ifStatusIsChange==true" >
                <label>Enter Reason For Change:</label>
                <div>
                  <input class="change-reason" type="text" placeholder="Enter Reason for Change" v-model="Reasaon4change"/>
                </div>
              </div>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Route</label>
              <select v-model="formData.route"
              @change="checkRouteSelection(formData.route)"
              >
                <option>Oral/Sublingual</option>
                <option>IVI Intravaginal</option>
                <option>IV (Intravenous)</option>
                <option>SQ/IM/IV/ID</option>
                <option>TOP Topical</option>
                <!-- etc. -->
              </select>
            </div>
            <div class="form-group checkbox-group">
              <input
                type="checkbox"
                id="prnCheck"
                v-model="formData.prn"
              />
              <label for="prnCheck">PRN (As Needed)</label>
            </div>
          </div>

          <div class="form-group">
            <label>Number of Tablets/Quantity</label>
            <input
              type="number"
              min="0"
              v-model.number="formData.quantity"
              placeholder="0"
            />
          </div>
        </div>
        <!-- IV Administration -->
        <div v-if="showIvform==true">
            <h4>IV Administration</h4>

            <!-- Fluid Type & VIA row -->
            <div class="form-row">
              <div class="form-group">
                <label>Fluid Type</label>
                <select v-model="formData.fluidType">
                  <option value="">Select fluid type</option>
                  <option>0.9% Normal Saline</option>
                  <option>D5W (5% Dextrose in Water)</option>
                  <option>Lactated Ringers (LR)</option>
                  <option>Half Normal Saline (0.45% NaCl)</option>
                </select>
              </div>
              <div class="form-group">
                <label>VIA</label>
                <select v-model="formData.via">
                  <option value="">Select an option</option>
                  <option>Peripheral IV - Left Arm</option>
                  <option>Peripheral IV - Right Arm</option>
                  <option>PICC Line - Left</option>
                  <option>PICC Line - Right</option>
                  <option>Mid Line - Left</option>
                  <option>Mid Line - Right</option>
                  <option>Central Line - Left</option>
                  <option>Central Line - Right</option>
                </select>
              </div>
            </div>

            <!-- Volume + Rate row -->
            <div class="form-row volume-rate-row">
              <div class="form-group volume-group">
                <label>Total Volume</label>
                <div class="volume-row">
                  <input
                    list="volumeOptions"
                    v-model="formData.totalVolume"
                    class="volume-dropdown"
                    placeholder="e.g. 100"
                  />
                  <datalist id="volumeOptions">
                    <option value="10"></option>
                    <option value="100"></option>
                    <option value="250"></option>
                    <option value="500"></option>
                    <option value="1000"></option>
                  </datalist>
                  <select
                    class="volume-dropdown"
                    v-model="formData.totalVolumeUnit"
                  >
                    <option value="ml">ml</option>
                    <option value="liter">liter</option>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label>Rate</label>
                <input
                  type="text"
                  v-model="formData.rate"
                  placeholder="50 (ml/hr)"
                />
              </div>
              <div class="form-group">
                <label>How Long (hrs)</label>
                <input
                  type="text"
                  :value="formData.howLong"
                  disabled
                  placeholder="Computed"
                />
              </div>
            </div>

            <!-- Start/End Time row -->
            <div class="form-row">
              <div class="form-group">
                <label>Start Time</label>
                <input
                  type="time"
                  v-model="formData.startTime"
                  placeholder="HH:MM"
                />
              </div>
              <div class="form-group">
                <label>End Time</label>
                <input
                  type="time"
                  :value="formData.endTime"
                  disabled
                />
              </div>
            </div>
            </div>

        <!-- TAB 2: Prescription Information -->
        <div v-if="activeTab === 'prescriptionInfo'" class="tab-panel">
          <h3 class="section-title">Prescription Information</h3>
          <div class="form-group">
            <label>RX Number</label>
            <input
              type="text"
              v-model="formData.rxNumber"
              placeholder="RX Number"
            />
          </div>
          <div class="form-group">
            <label>Date the script was filled</label>
            <!-- If you prefer a date picker, replace with your component -->
            <input
              type="date"
              v-model="formData.filledDate"
              placeholder="mm/dd/yyyy"
            />
          </div>
          <div class="form-group">
            <label>Number of Refills</label>
            <input
              type="number"
              min="0"
              v-model.number="formData.refills"
              placeholder="0"
            />
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Start Date</label>
              <input
                type="date"
                v-model="formData.startDate"
                placeholder="mm/dd/yyyy"
              />
            </div>
            <div class="form-group">
              <label>End Date</label>
              <input
                type="date"
                v-model="formData.endDate"
                placeholder="mm/dd/yyyy"
              />
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label>Refill Reminder Date</label>
              <input
                type="date"
                v-model="formData.refillReminderDate"
                placeholder="mm/dd/yyyy"
              />
            </div>
            <div class="form-group">
              <label>Expiration/Refills until</label>
              <input
                type="date"
                v-model="formData.expirationDate"
                placeholder="mm/dd/yyyy"
              />
            </div>
          </div>
        </div>

        <!-- TAB 3: Provider Information -->
        <div v-if="activeTab === 'providerInfo'" class="tab-panel">
          <h3 class="section-title">Provider Information</h3>
          <div class="form-group">
            <label>Provider Name</label>
            <input
              type="text"
              v-model="formData.providerName"
              placeholder="Provider Name"
              @keyup.prevent="showNpiResult"
            />
            <select v-if="pastProvloaded" v-model="loadedProvider" @change="fillProviderInfo" :class="'pastProvdisplay-'+pastProvloaded">
                <option v-for="(prov,index) in pastProvar" :key="index" >{{ prov.firstname }} {{ prov.lastname }}</option>
            </select>
            <div v-if="newProvloaded" id="npinamesearch" :class="'selectdisplay-'+newProvloaded">
                <div
                  v-for="(npi, index) in newProvider"
                  class="additionalnameli"
                  :key="index + 'npi' + npi.npinumber"
                  @click="selectProvider(index)"
                >
                  {{ npi.name }} - {{ npi.npinumber }}
                </div>
              </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>DEA Number</label>
              <input
                type="text"
                v-model="formData.providerDea"
                placeholder="DEA Number"
              />
            </div>
            <div class="form-group">
              <label>NPI Number</label>
              <input
                type="text"
                v-model="formData.providerNpi"
                placeholder="NPI Number"
              />
            </div>
          </div>
          <div class="form-group">
            <label>License Number</label>
            <input
              type="text"
              v-model="formData.licenseNumber"
              placeholder="License Number"
            />
          </div>
          <div class="form-group">
            <label>Address</label>
            <input
              type="text"
              v-model="formData.providerAddress"
              placeholder="Address"
            />
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Office Number</label>
              <input
                type="text"
                v-model="formData.providerOffice"
                placeholder="Office Number"
              />
            </div>
            <div class="form-group">
              <label>Cell Phone</label>
              <input
                type="text"
                v-model="formData.providerCell"
                placeholder="Cell Phone"
              />
            </div>
          </div>
          <div class="form-group">
            <label>Email</label>
            <input
              type="email"
              v-model="formData.providerEmail"
              placeholder="Email"
            />
          </div>
        </div>

        <!-- TAB 4: Pharmacy Information -->
        <div v-if="activeTab === 'pharmacyInfo'" class="tab-panel">
          <h3 class="section-title">Pharmacy Information</h3>
          <div class="form-group">
            <label>Pharmacy Name</label>
            <input
              type="text"
              v-model="formData.pharmacyName"
              placeholder="Pharmacy Name"
              @keyup.prevent="showPharmacyResult"
            />
            <div v-if="newpastPatPharcy" id="pharmselect" :class="'selectdisplay2-'+newpastPatPharcy">
                <select class="pharmselectfield" v-model="loadedpatientNPI" @change="selectpastPharm">
               <option v-for="(ppharm, index) in  pastpatPharmacy" :key ="index" :value="ppharm.npinumber">
                {{ ppharm.pharmacyname }} - {{ ppharm.npinumber }}
               </option>
                </select>
            </div>
            <div v-if="newPharmloaded" id="npinamesearch" :class="'selectdisplay-'+newPharmloaded">
                <div
                  v-for="(pharm, index) in newPharmacy"
                  class="additionalnameli"
                  :key="index"
                  @click="selectPharm(index)"
                >
                  {{pharm.npinumber}} - {{pharm.name}} - {{ pharm.addresses[index].address }} 
                </div>
              </div>
          
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>DEA Number</label>
              <input
                type="text"
                v-model="formData.pharmacyDea"
                placeholder="DEA Number"
              />
            </div>
            <div class="form-group">
              <label>NPI Number</label>
              <input
                type="text"
                v-model="formData.pharmacyNpi"
                placeholder="NPI Number"
              />
            </div>
          </div>
          <div class="form-group">
            <label>Address</label>
            <input
              type="text"
              v-model="formData.pharmacyAddress"
              placeholder="Address"
            />
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Office Number</label>
              <input
                type="text"
                v-model="formData.pharmacyOffice"
                placeholder="Office Number"
              />
            </div>
            <div class="form-group">
              <label>Cell Phone</label>
              <input
                type="text"
                v-model="formData.pharmacyCell"
                placeholder="Cell Phone"
              />
            </div>
          </div>
          <div class="form-group">
            <label>Email</label>
            <input
              type="email"
              v-model="formData.pharmacyEmail"
              placeholder="Email"
            />
          </div>
        </div>

        <!-- Action Buttons (Bottom) -->
        <div class="form-actions">
          <button class="btn-cancel" @click="$emit('close')">
            Cancel
          </button>
          <button class="btn-save" @click="handleSave">
            Save
          </button>
        </div>
      </div>
    </div>
  </transition>
</template>

<script setup lang="ts">
import { ref, toRefs,defineProps, defineEmits, watch} from 'vue'
import { PastProvarItem } from '../types';
import axios from 'axios';
//import EditDetailsForm from './EditDetailsForm.vue';



/** Define the structure of all form fields. */
interface MedicationFormData {
  medicationName: string;
  ndcnumber: string;
  rxnorns: string;
  diagnosis: string;
  diagdescription:string;
  dosage: string;
  frequency: string;
  route: string;
  administrationTimes?: string; //new to the form it hold the med times
  coreason:string, //new to the form and it holds the reason for the change
  ordernumber:string, //new paramater that needs to be present when the parent component passis formdata items to axios for processing 
  prn: boolean;
  quantity: number;
  rate:'';
  unitType:'';
  duration: '';
  fluidType: '';
  totalVolume: '';
  totalVolumeUnit: 'ml';
  howLong: '';
  startTime: '';
  endTime: '';
  via: '';
  tabletnumber:'';
  sqInjectionSite: '';
  idInjectionSite: '';
  imInjectionSite: '';
  rxNumber: string;
  filledDate: string;
  refills: number;
  startDate: string;
  endDate: string;
  refillReminderDate: string;
  expirationDate: string;

  providerName: string;
  providerDea: string;
  providerNpi: string;
  licenseNumber: string;
  providerAddress: string;
  providerOffice: string;
  providerCell: string;
  providerEmail: string;

  pharmacyName: string;
  pharmacyDea: string;
  pharmacyNpi: string;
  pharmacyAddress: string;
  pharmacyOffice: string;
  pharmacyCell: string;
  pharmacyEmail: string;
}
//const pastProvar = ref<string[]>([]);
/**
 * Props:
 *  show: controls visibility of the modal
 */
const props = defineProps<{
  show: boolean;
  pastProvloaded:boolean;
  isEditMedication:boolean;
  isAddNewMed:boolean;
  ifStatusIsChange:boolean;
  pastProvar: PastProvarItem[];
  editFormdata: object;
}>()
const selectedDosage = ref<string>('');
const selectedFrequency = ref<string>('');
const timeInputs = ref<string[]>([]);
// Keyon Added variables for Time Change - Status change
const ifStatusIsChange = ref<boolean>(false);
const Reasaon4change = ref<string>('');
/**
 * Emits:
 *  close  -> for closing/canceling the modal
 *  save   -> sends the entire formData object
 */
const emit = defineEmits<{
  (e: 'close'): void;
  (e: 'save', payload: MedicationFormData): void;
  (e: 'freqchange', payload: MedicationFormData,selectedFrequency:string): void;
  (e: 'dosagechange', payload:MedicationFormData,selectedDosage:string): void;
  (e: 'loadprov'): void;
  (e: 'updtPastProvbool'):void;
}>()
/** Reacctive oject for exetracing and prepopulating form from past Med data */
const { editFormdata } = toRefs(props); 
/** Reactive object storing all form fields. */
const formData = ref<MedicationFormData>({
  medicationName: '',
  ndcnumber: '',
  rxnorns: '',
  diagnosis: '',
  diagdescription:'',
  dosage: '',
  frequency: '',
  administrationTimes:'', //added because of the Update so this is new
  coreason:'', //add because of the update so this is new
  ordernumber:'',//added because the update - this is new 
  route: 'Oral/Sublingual',
  prn: false,
  quantity: 0,
  rate:'',
  unitType:'',
  duration: '',
  fluidType: '',
  totalVolume: '',
  totalVolumeUnit: 'ml',
  rate: '',
  howLong: '',
  startTime: '',
  endTime: '',
  via: '',
  tabletnumber:'',
  sqInjectionSite: '',
  idInjectionSite: '',
  imInjectionSite: '',
  rxNumber: '',
  filledDate: '',
  refills: 0,
  startDate: '',
  endDate: '',
  refillReminderDate: '',
  expirationDate: '',

  providerName: '',
  providerDea: '',
  providerNpi: '',
  licenseNumber: '',
  providerAddress: '',
  providerOffice: '',
  providerCell: '',
  providerEmail: '',

  pharmacyName: '',
  pharmacyDea: '',
  pharmacyNpi: '',
  pharmacyAddress: '',
  pharmacyOffice: '',
  pharmacyCell: '',
  pharmacyEmail: ''
})

watch(
  [() => formData.value.totalVolume, () => formData.value.rate, () => formData.value.totalVolumeUnit],
  () => {
    const vol = parseFloat(formData.value.totalVolume) || 0
    let numericRate = parseFloat(formData.value.rate) || 0
    if (!numericRate) {
      const match = formData.value.rate.match(/(\d+(\.\d+)?)/)
      if (match) numericRate = parseFloat(match[1])
    }
    const finalVolumeInMl =
      formData.value.totalVolumeUnit === 'liter' ? vol * 1000 : vol
    let hours = numericRate > 0 ? finalVolumeInMl / numericRate : 0
    formData.value.howLong = hours > 0 ? hours.toFixed(2) : ''
  }
)

watch(
  [() => formData.value.howLong, () => formData.value.startTime],
  () => {
    if (!formData.value.howLong || !formData.value.startTime) {
      formData.value.endTime = ''
      return
    }
    const [startH, startM] = formData.value.startTime.split(':').map(Number)
    const hoursFloat = parseFloat(formData.value.howLong)
    if (isNaN(hoursFloat) || isNaN(startH)) {
      formData.value.endTime = ''
      return
    }
    const totalMinutes = Math.round(hoursFloat * 60)
    let newH = startH
    let newM = startM + totalMinutes
    newH += Math.floor(newM / 60)
    newM = newM % 60
    const hh = String(newH % 24).padStart(2, '0')
    const mm = String(newM).padStart(2, '0')
    formData.value.endTime = `${hh}:${mm}`
  }
)
watch(      
  editFormdata,     
   (newValue, oldValue) => {       
     if (newValue && !oldValue) {         
       // Copy properties from editFormdata to formData          
       //Object.assign(formData.value, props.editFormdata); 
      
       if(props.isEditMedication==true)
       {
         formData.value.medicationName = props.editFormdata.medname;
          formData.value.diagnosis = props.editFormdata.diagnosis;  
          formData.value.rxnorns = props.editFormdata.rxnorns;
          formData.value.dosage = props.editFormdata.med_amount;
          formData.value.frequency = props.editFormdata.med_frequency;
          formData.value.ndcnumber = props.editFormdata.ndcnumber;
          formData.value.quantity = props.editFormdata.total;
          formData.value.prn = props.editFormdata.prn;
          formData.value.ordernumber = props.editFormdata.order_number;
          formData.value.route = props.editFormdata.route;
          /*
          This is accounting for Intravanous Form Fields if the showIvForm and via param has a value 
          */ 
         if(props.editFormdata.via_med==1)
         {
          showIvform.value = true;
           formData.value.via = props.editFormdata.viatype;
           formData.value.fluidType = props.editFormdata.fluidtype;
           formData.value.totalVolume = props.editFormdata.totalVolumn;
           formData.value.totalVolumeUnit = props.editFormdata.totalVolumnUnit;
           formData.value.rate = props.editFormdata.rate;
           formData.value.howLong = props.editFormdata.ivhowLong;
           formData.value.startTime = props.editFormdata.ivstarttime;
           formData.value.endTime = props.editFormdata.ivendtime;

         }
          //TIme Input Prefill 
          /* Medication AdminisrtrationTimes propertis (1st choice) || yearmedTimes (jSON - backup if needed) has the Administration times 
          * Thats needed in order to prefill the time slots if Administration time already exist 
          */
          if(props.editFormdata.administrationTimes && props.editFormdata.administrationTimes !="" || props.editFormdata.administrationTimes !=null)
          {
            //extract times from the yearmedtime (no need to loop since we have a single object of the Medication instance)
            const splitted = props.editFormdata.administrationTimes.split(',');
            timeInputs.value = splitted.map(t => t.trim());
            formData.value.administrationTimes = timeInputs.value;
          }
         else {
             // timeInputs.value = []
            }
          
       }
      
       }     
      }    
    );
//------ifStatusIsChange Watch----------//
watch( [() =>props.ifStatusIsChange],
  () => {
  if(props.ifStatusIsChange==false)
  {
    alert("False");
     ifStatusIsChange.value=props.ifStatusIsChange;
  }
  else{
    alert("True");
    ifStatusIsChange.value=props.ifStatusIsChange;
  }
})
// ---------- FREQUENCY WATCH ----------//
watch(selectedFrequency, (newFreq) => {
  if (!newFreq || (editFormdata.value && editFormdata.prn)) {
    timeInputs.value = []
    return
  }
  const timesCount = getTimesCountFromFrequency(newFreq)
  timeInputs.value = Array(timesCount).fill('')
},{ deep: true })

function getTimesCountFromFrequency(frequency: string): number {
  if (!frequency) return 0
  const dailyMatch = frequency.match(/(\d+)\s*times?\s*daily/)
  if (dailyMatch) {
    return parseInt(dailyMatch[1], 10)
  }
  const hoursMatch = frequency.match(/every\s*(\d+)\s*hours?/)
  if (hoursMatch) {
    const hours = parseInt(hoursMatch[1], 10)
    return Math.floor(24 / hours)
  }
  switch (frequency) {
    case 'every hour': return 24
    case 'daily': return 1
    case 'at bedtime': return 1
    case 'every 24 hours': return 1
    case 'every other day': return 4
    case 'monday, wednesday, friday, sunday': return 4
    case 'tuesday, thursday, saturday': return 3
    default: return 1
  }
}
/** The four tabs: */
const tabs = [
  { value: 'medInfo',         label: 'Medication Information' },
  { value: 'prescriptionInfo',label: 'Prescription Information'},
  { value: 'providerInfo',    label: 'Provider Information'    },
  { value: 'pharmacyInfo',    label: 'Pharmacy Information'    }
]
const CURR_API = ref<string>('https://medadministration:8890/keyon');
const loadedProvider = ref<string>('');
const loadedpatientNPI = ref<string>('');
const newProvider = ref<PastProvarItem[]>([]);
  const newPharmacy = ref<PastProvarItem[]>([]);
  const pastpatPharmacy = ref<PastProvarItem[]>([]);
    const drugs = ref<PastProvarItem[]>([]);
const newProvloaded =ref<boolean>(false);
const newpastPatPharcy = ref<boolean>(false);
const  newPharmloaded = ref<boolean>(false);
const showIvform = ref<boolean>(false);
const searchTerm = ref<string>('');
const newDiagloaded = ref<boolean>(false);
const newDiagcodes = ref<PastProvarItem[]>([]);
/** Track which tab is active. */
const activeTab = ref('medInfo')

/*Handle Tab Function */
const handleTabClick = (tabValue: string) => {
  activeTab.value = tabValue; //set the active tab
  //Run or emit event when the providerInfo tab is active 
  if(tabValue === 'providerInfo') {
    loadPastProviders();
    showIvform.value = false;
  }
  if(tabValue==='pharmacyInfo')
  {
    loadPatientPharmacy();
    showIvform.value = false;
  }
  if(tabValue==='prescriptionInfo')
  {
    showIvform.value=false;
  }
  if(tabValue==='medInfo')
  {
    if(formData.value.route=="IV (Intravenous)")
    {
      showIvform.value=true;
    }
    else{
      showIvform.value=false;
    }
  }
};
/** Checking Route Option */
function checkRouteSelection(fdata)
{
 
  if(fdata=="IV (Intravenous)")
  {
    showIvform.value=true;
  }
  else{
    showIvform.value=false;
  }
}
function detectFreqChange(frequency:string){
 
  selectedFrequency.value = frequency;
  emit('freqchange', formData.value, frequency)
  if(props.ifStatusIsChange==true)
  {
   console.log("Status True");
    ifStatusIsChange.value =true;
  }
  else{
    ifStatusIsChange.value=false;
   console.log("Status False");
  }
}
function detectDosageChange(dosageamount:string){
  
  selectedDosage.value = dosageamount;
  emit('dosagechange', formData.value, dosageamount)
}
/** Handler for the Save button. */
function handleSave() {
  // You can do validation or other logic here
  if(props.isEditMedication)
  {
    //lets make sure the time in puts are in the formData before we send it over
    alert("Its definitely an edit");
    if(timeInputs.value.length > 0 )
    {
     
      formData.value.administrationTimes = timeInputs.value.join(',') ;
      formData.value.coreason = Reasaon4change.value;
      emit('save',formData.value,timeInputs.value,selectedFrequency.value);
    }
   // emit('save',editFormdata.value)
  }
  if(props.isAddNewMed)
  {
    emit('save',formData.value)
  }
 // emit('save', formData.value)
}
/*Handles the Patient Pharmacy Select Box Change and parsing*/
function selectpastPharm()
{
  
  for(var i=0; i < pastpatPharmacy.value.length; i++)
  {
    
    if(pastpatPharmacy.value[i].npinumber==loadedpatientNPI.value)
    {
      formData.value.pharmacyName = pastpatPharmacy.value[i].pharmacyname;
      formData.value.pharmacyNpi = pastpatPharmacy.value[i].npinumber;
      formData.value.pharmacyAddress = pastpatPharmacy.value[i].pharmaddress;
     // formData.value.pharmacyOffice = pastpatPharmacy.value[i].addresses[index].tel;
     // formData.value.pharmacyCell = pastpatPharmacy.value[i].addresses[index].tel;
     newpastPatPharcy.value=false;
    }
  }
}
/*Handle Selecting the Diagnosis code and description*/
function selectDiagcode(index: number)
{
  formData.value.diagnosis = newDiagcodes.value[index].code;
  formData.value.diagdescription = newDiagcodes.value[index].description;
  //now close list container 
  newDiagloaded.value=false;
}
/*Handle SElecting a Pharmacy*/
function selectPharm(index: number)
{
      formData.value.pharmacyName = newPharmacy.value[index].name;
      formData.value.pharmacyNpi = newPharmacy.value[index].npinumber;
      formData.value.pharmacyAddress = newPharmacy.value[index].addresses[index].address;
      formData.value.pharmacyOffice = newPharmacy.value[index].addresses[index].tel;
      formData.value.pharmacyCell = newPharmacy.value[index].addresses[index].tel;
       newPharmloaded.value=false;
      
}
/*Handle NewProvider Function*/
function selectProvider(index: number)
{
      formData.value.providerName = newProvider.value[index].name;
      formData.value.providerNpi = newProvider.value[index].npinumber;
      formData.value.providerAddress = newProvider.value[index].addresses[0].address;
      formData.value.providerOffice = newProvider.value[index].addresses[0].tel;
      formData.value.providerCell = newProvider.value[index].addresses[0].tel;
      //formData.value.providerEmail = newProvider[index].email;
      newProvloaded.value=false;
     
}
function getDrugSynonym(synonym: string)
{
  console.log(`Fetching synonyms: ${synonym}`);
  formData.value.medicationName = synonym
  drugs.value =[];
}
async function fetchDiagnosis()
{
   searchTerm.value =formData.value.diagnosis;
   let content = {
    action:"CodeLookUp",
    searchTerm:searchTerm.value
   };
   if(formData.value.diagnosis=="")
   {
    newDiagcodes.value = [];  //resetting the newDiagcodes array if the input field is blank
    newDiagloaded.value=false; //Should make the code display box disappear based on the boolean
    return;
   }
   try{

    await axios
        .post( "https://medadministration:8890/keyon/icd_calls.php", content, {
          headers: { "Content-Type": "application/json;" },
        })
        .then((res) => {
          console.log(res.data); //see what the response looks like from a data structure stanpoint
          if (res.data && res.data.result <=0) {
            newDiagcodes.value = [];  //resetting the newDiagcodes array if the input field is blank
            newDiagloaded.value=false; //Should make the code display box disappear based on the boolean
            formData.value.diagnosis="No Results Found";
          } else{ 
            
            
            newDiagcodes.value = res.data.actualResults;
            newDiagloaded.value=true;
           
           
          }
        });
   }
   catch(error){
    console.error('Error fetching Diagnosis Code:', error);
   }
}
async function fetchDrugs()
{
  if (formData.value.medicationName.trim() === '') { 
      drugs.value = [];       
       return; 
      }
      try {        
        const response = await axios.get(`https://rxnav.nlm.nih.gov/REST/drugs.json?name=${encodeURIComponent(formData.value.medicationName)}`); 
        const data = response.data;                
        if (data && data.drugGroup && data.drugGroup.conceptGroup) {  
            const conceptProperties = data.drugGroup.conceptGroup            
            .flatMap(group => group.conceptProperties || []);                   
             // Assigning to drugs array with PastProvItem type          
             drugs.value = conceptProperties.map(drug => ({            
              0: drug.rxcui,          // RXCUI           
              1: drug.name,           // Drug Name           
              2: drug.synonym         // Synonym          
              })) as PastProvarItem[];       
        } else {          
          drugs.value = [];        

        }      
      } catch (error) { 
          console.error('Error fetching drugs:', error);     
      }
}
async function showPharmacyResult()
{
  let content = {
        methname:"GetAllInfo",
        primphysician:"",
        organization: formData.value.pharmacyName, 
        postalCode:"46260",
        enumeration:"NPI-2"
      };
      // this.showPreloader()
      await axios
        .post( "https://medadministration:8890/keyon/NPILookup.php", content, {
          headers: { "Content-Type": "application/json;" },
        })
        .then((res) => {
          if (res.data && res.data.length < 1) {
            console.log(res.data);
            newPharmacy.value = [];
          } else{ 
            
            console.log(res.data);
            newPharmacy.value = res.data;
            console.log(newPharmacy);
            newPharmloaded.value=true;
            newpastPatPharcy.value=false;
           
          }
        });
      // this.stopPreloader()
}
/*Patient assigned Pharmacy Lookup Axios Call*/
async function loadPatientPharmacy()
{
  //newPharmloaded.value=false;
   let content = {
     MedicationAdmin: {
      API_Meth:"GetAssignedPatPharmacy",
      patientid:"709081242",
      accountnumber:"904575107"
     }
   };
   await axios
        .post( "https://medadministration:8890/keyon/tswebhook.php", content, {
          headers: { "Content-Type": "application/json;" },
        })
        .then((res) => {
 
          if (res.data.results && res.data.results =="") {
            pastpatPharmacy.value = [];
            newpastPatPharcy.value=false;
          } else{ 
            pastpatPharmacy.value = res.data.results;
           newpastPatPharcy.value=true;
           newPharmloaded.value=false;
            console.log(res.data);
           
            console.log(pastpatPharmacy);
          
           // newPharmloaded.value=false;
           
          }
        });
}
/* Npi Search Result from the NPIRegistery on click event*/
async function showNpiResult()
{
  let content = {
        methname: "GetAllInfo",
        primphysician: formData.value.providerName, 
      };
      // this.showPreloader()
      await axios
        .post( "https://medadministration:8890/keyon/NPILookup.php", content, {
          headers: { "Content-Type": "application/json;" },
        })
        .then((res) => {
          if (res.data.results && res.data.results == "No Results Found") {
            newProvider.value = [];
          } else{ 
            emit('updtPastProvbool');
            console.log(res.data);
            newProvider.value = res.data;
            console.log(newProvider);
            newProvloaded.value=true;
           
          }
        });
      // this.stopPreloader()
}
/*function to handle the on change event on the provider select box - Fill PRovider information into the Provider form */
function fillProviderInfo()
{
  for(var i=0; i < props.pastProvar.length;i++)
  {
    let name = props.pastProvar[i]["firstname"] + ' ' + props.pastProvar[i]["lastname"];
    if(loadedProvider.value == name)
    {
      
      console.log(props.pastProvar[i]);
      console.log(props.pastProvar[i].npinumber);
      formData.value.providerName = name;
      formData.value.providerNpi = props.pastProvar[i].npinumber;
      formData.value.providerAddress = props.pastProvar[i].addr1;
      formData.value.providerOffice = props.pastProvar[i].tel;
      formData.value.providerCell = props.pastProvar[i].tel;
      formData.value.providerEmail = props.pastProvar[i].email;
      emit('updtPastProvbool');
    }
  }
}
/*Function to go and lookup past providers */
function loadPastProviders()
{
  
  emit('loadprov');
}
</script>

<style scoped>
/* Basic fade for the modal appear/disappear */
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.2s;
}
.fade-enter, .fade-leave-to {
  opacity: 0;
}
.selectdisplay2-true{
  display:flex;
  width:220px;
}
/*select field dynamci css*/
.selectdisplay-true{
  width: 100%;
    height: 220px;
    overflow-y:scroll;
    color: black;
    display: flex;
    flex-direction: column;
    border-style:solid;
    border-width:1px;
    border-color:#6c757d
}
.selectdisplay-false{
  display:none;
}
.pastProvdisplay-true{
  display:block;
}
.pastProvdisplay-false{
  display:none;
}
/* Modal Overlay & Container */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0,0,0,0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}
.modal-content {
  background-color: #fff;
  width: 80%;
  max-width: 800px;
  border-radius: 8px;
  padding: 2rem;
  box-shadow: 0 10px 25px rgba(0,0,0,0.3);
  height:800px;
  overflow-y:scroll;
}
.modal-title {
  margin-top: 0;
  text-align: center;
  margin-bottom: 1rem;
}

/* Tabs (buttons) */
.tabs {
  display: flex;
  margin-bottom: 1rem;
  border-bottom: 1px solid #ccc;
}
.tab-button {
  flex: 1;
  text-align: center;
  background: none;
  border: none;
  padding: 0.75rem;
  cursor: pointer;
  font-weight: 500;
  transition: background-color 0.2s;
}
.form-row .admin-times{
  border-style: solid;
    border-width: 1px;
    border-color: #d8e1e1;
    margin-top: -15px;

}
.tab-button:hover {
  background-color: #f2f2f2;
}
.tab-button.active {
  background-color: #e6f4f4;
  border-bottom: 3px solid #0c8687;
}

/* Tab panel content */
.tab-panel {
  margin: 1rem 0;
}
.section-title {
  margin: 1rem 0 1rem;
  font-size: 1.1rem;
  border-bottom: 2px solid #0c8687;
  color: #333;
  padding-bottom: 0.5rem;
}

/* Form layout */
.form-group {
  margin-bottom: 1rem;
  display: flex;
  flex-direction: column;
}
.form-group label {
  font-weight: 500;
  margin-bottom: 0.3rem;
}
.form-group input,
.form-group select {
  padding: 0.4rem 0.6rem;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 1rem;
}

.time-input-row {
  margin-bottom: 0.5rem;
}
.time-input {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 1rem;
}
.change-reason{
  width:100%;
  padding:0.5rem;
  border: 1px solid #ddd;
  border-radius:4px;
  font-size:1rem;
}
.checkbox-group {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}
/* Horizontal row of fields */
.form-row {
  display: flex;
  gap: 1rem;
}
.form-row .form-group {
  flex: 1;
}

/* Bottom action buttons */
.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 1rem;
}
.btn-cancel,
.btn-save {
  padding: 0.6rem 1.2rem;
  border-radius: 4px;
  border: none;
  cursor: pointer;
  font-weight: 500;
}
.btn-cancel {
  background-color: #6c757d;
  color: #fff;
}
.btn-cancel:hover {
  background-color: #5a6268;
}
.btn-save {
  background-color: #0c8687;
  color: #fff;
}
.btn-save:hover {
  background-color: #0a7273;
}
</style>
